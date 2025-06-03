<?php
// models/LeaveModel.php

require_once APP_ROOT . '/models/BaseModel.php';
require_once APP_ROOT . '/models/CardModel.php'; // Kullanıcı bilgilerini çekmek için
require_once APP_ROOT . '/core/Mailer.php';

class LeaveModel extends BaseModel {
    public function __construct() {
        parent::__construct('leave_requests');
    }

    // İzin türlerini getir
    public function getLeaveTypes($isActive = null) {
        $sql = "SELECT * FROM leave_types";
        $params = [];
        if ($isActive !== null) {
            $sql .= " WHERE is_active = :is_active";
            $params[':is_active'] = $isActive ? 1 : 0;
        }
        $sql .= " ORDER BY name";
        return Database::fetchAll($sql, $params);
    }

    // İzin bakiyelerini getir
    public function getLeaveBalances($userId = null, $year = null, $limit = null) {
        $sql = "SELECT lb.*, lt.name as leave_type_name, c.name, c.surname
                FROM leave_balances lb
                JOIN leave_types lt ON lb.leave_type_id = lt.id
                JOIN cards c ON lb.user_id = c.user_id
                WHERE 1=1";
        $params = [];

        if ($userId !== null) {
            $sql .= " AND lb.user_id = :user_id";
            $params[':user_id'] = $userId;
        }
        if ($year !== null) {
            $sql .= " AND lb.year = :year_val";
            $params[':year_val'] = $year;
        }
        $sql .= " ORDER BY c.name, c.surname, lt.name";
        if ($limit !== null) {
            $sql .= " LIMIT :limit_val";
            $params[':limit_val'] = $limit;
        }
        return Database::fetchAll($sql, $params);
    }

    // İzin bakiyesi tanımla/güncelle
    public function updateLeaveBalance($data) {
        $userId = $data['user_id'];
        $leaveTypeId = $data['leave_type_id'];
        $year = $data['year'];
        $totalDays = $data['total_days'];

        $existingBalance = Database::fetch("SELECT * FROM leave_balances WHERE user_id = :user_id AND leave_type_id = :leave_type_id AND year = :year", [
            ':user_id' => $userId,
            ':leave_type_id' => $leaveTypeId,
            ':year' => $year
        ]);

        if ($existingBalance) {
            // Bakiye varsa güncelle
            return Database::execute("UPDATE leave_balances SET total_days = :total_days WHERE user_id = :user_id AND leave_type_id = :leave_type_id AND year = :year", [
                ':total_days' => $totalDays,
                ':user_id' => $userId,
                ':leave_type_id' => $leaveTypeId,
                ':year' => $year
            ])->rowCount();
        } else {
            // Bakiye yoksa oluştur
            return Database::create("leave_balances", [
                'user_id' => $userId,
                'leave_type_id' => $leaveTypeId,
                'year' => $year,
                'total_days' => $totalDays,
                'used_days' => 0 // Yeni kayıt ise kullanılan gün 0
            ]);
        }
    }

    // İzin talebi oluştur
    public function submitLeaveRequest($data) {
        $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        $data['status'] = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $insertId = $this->create($data);

        // Yöneticiye e-posta gönderimi (Mailer sınıfını kullanacak)
        $cardModel = new CardModel();
        $mailer = new Mailer();

        $leaveType = Database::fetch("SELECT name FROM leave_types WHERE id = :id", [':id' => $data['leave_type_id']]);
        
        $managerEmails = Database::fetchAll("SELECT email FROM cards WHERE privilege >= 2 AND email IS NOT NULL AND email != ''", []);
        $managerEmails = array_column($managerEmails, 'email'); // Sadece e-posta adreslerini al

        $userData = $cardModel->find($data['user_id']); // Talep eden kullanıcının bilgilerini al

        if (!empty($managerEmails) && $userData) {
            $leaveDataForEmail = [
                'id' => $insertId,
                'leave_type_name' => $leaveType['name'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_days' => $data['total_days'],
                'reason' => $data['reason']
            ];
            $mailer->sendNewLeaveRequestEmail($managerEmails, $userData, $leaveDataForEmail);
        }
        
        return $insertId;
    }

    // İzin talebini güncelle (onayla/reddet)
    public function updateLeaveRequestStatus($requestId, $status, $comment, $approvedBy) {
        $request = $this->find($requestId);
        if (!$request) {
            return false;
        }

        $data = [
            'status' => $status,
            'comment' => $comment,
            'approved_by' => $approvedBy,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $updated = $this->update($requestId, $data);

        if ($updated && $status === 'approved') {
            // Kullanıcının izin bakiyesini güncelle
            $userId = $request['user_id'];
            $leaveTypeId = $request['leave_type_id'];
            $totalDays = $request['total_days'];
            $year = date('Y', strtotime($request['start_date'])); // İzin başlangıç yılını al

            $balance = Database::fetch("SELECT * FROM leave_balances WHERE user_id = :user_id AND leave_type_id = :leave_type_id AND year = :year", [
                ':user_id' => $userId,
                ':leave_type_id' => $leaveTypeId,
                ':year' => $year
            ]);

            if ($balance) {
                Database::execute("UPDATE leave_balances SET used_days = used_days + :total_days WHERE id = :id", [
                    ':total_days' => $totalDays,
                    ':id' => $balance['id']
                ]);
            } else {
                // Eğer bakiye yoksa, varsayılan bir değerle oluşturulur (önceden tanımlanmış türler için)
                $defaultDays = 0; // Varsayılan
                $leaveTypeInfo = Database::fetch("SELECT max_days FROM leave_types WHERE id = :id", [':id' => $leaveTypeId]);
                if ($leaveTypeInfo && $leaveTypeInfo['max_days']) {
                    $defaultDays = $leaveTypeInfo['max_days'];
                }

                Database::create("leave_balances", [
                    'user_id' => $userId,
                    'leave_type_id' => $leaveTypeId,
                    'year' => $year,
                    'total_days' => $defaultDays,
                    'used_days' => $totalDays
                ]);
            }
        }

        // E-posta gönderimi (Mailer sınıfını kullanacak)
        $cardModel = new CardModel();
        $mailer = new Mailer();

        $leaveType = Database::fetch("SELECT name FROM leave_types WHERE id = :id", [':id' => $request['leave_type_id']]);
        $userData = $cardModel->find($request['user_id']);

        if ($userData && !empty($userData['email'])) {
            $leaveDataForEmail = [
                'status' => $status,
                'start_date' => $request['start_date'],
                'end_date' => $request['end_date'],
                'total_days' => $request['total_days'],
                'leave_type_name' => $leaveType['name'],
                'comment' => $comment
            ];
            $mailer->sendLeaveStatusEmail($userData, $leaveDataForEmail);
        }
        return $updated;
    }

    // Belirli bir tarih için izinli personeli getirir (AbsenceModel için)
    public function getOnLeaveEmployeesForDate($date) {
        $sql = "
            SELECT DISTINCT lr.user_id, c.name, c.surname, c.department, c.position,
                   lt.name as leave_type_name, lt.color
            FROM leave_requests lr
            JOIN cards c ON lr.user_id = c.user_id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = 'approved'
            AND :date BETWEEN lr.start_date AND lr.end_date
            AND c.enabled = 'true'
        ";
        return Database::fetchAll($sql, [':date' => $date]);
    }

    // Kullanıcının izin taleplerini getir
    public function getUserLeaveRequests($userId) {
        $sql = "
            SELECT lr.*, lt.name as leave_type_name, lt.color
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.user_id = :user_id
            ORDER BY lr.created_at DESC
        ";
        return Database::fetchAll($sql, [':user_id' => $userId]);
    }

    // Bekleyen izin taleplerini getir
    public function getPendingLeaveRequests($limit = null) {
        $sql = "
            SELECT lr.*, lt.name as leave_type_name, lt.color, c.name, c.surname, c.department, c.position
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN cards c ON lr.user_id = c.user_id
            WHERE lr.status = 'pending'
            ORDER BY lr.created_at DESC
        ";
        if ($limit !== null) {
            $sql .= " LIMIT :limit_val";
            return Database::fetchAll($sql, [':limit_val' => $limit]);
        }
        return Database::fetchAll($sql);
    }

    // İşlenmiş izin taleplerini filtreli getir
    public function getProcessedLeaveRequests($filters = []) {
        $sql = "
            SELECT lr.*, lt.name as leave_type_name, lt.color, c.name, c.surname, c.department, c.position
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN cards c ON lr.user_id = c.user_id
            WHERE lr.status != 'pending'
        ";
        $params = [];

        if (!empty($filters['name'])) {
            $sql .= " AND (c.name LIKE :search_name OR c.surname LIKE :search_name)";
            $params[':search_name'] = "%{$filters['name']}%";
        }
        if (!empty($filters['department'])) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $filters['department'];
        }
        if (!empty($filters['leave_type'])) {
            $sql .= " AND lr.leave_type_id = :leave_type";
            $params[':leave_type'] = $filters['leave_type'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND lr.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $sql .= " AND lr.start_date >= :start_date";
            $params[':start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $sql .= " AND lr.end_date <= :end_date";
            $params[':end_date'] = $filters['end_date'];
        }

        $sql .= " ORDER BY lr.updated_at DESC LIMIT 100"; // Sayfalama eklenebilir

        return Database::fetchAll($sql, $params);
    }

    // İzin Detayını Getir
    public function getLeaveDetail($leaveId) {
        $sql = "
            SELECT lr.*, lt.name as leave_type_name, lt.color, c.name, c.surname, c.department, c.position, c.photo_path
            FROM leave_requests lr
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            JOIN cards c ON lr.user_id = c.user_id
            WHERE lr.id = :id
        ";
        return Database::fetch($sql, [':id' => $leaveId]);
    }

    // Departman yöneticilerini getir
    public function getDepartmentManagers() {
        $sql = "
            SELECT dm.*, c.name, c.surname
            FROM department_managers dm
            JOIN cards c ON dm.manager_id = c.user_id
            ORDER BY dm.department, c.name
        ";
        return Database::fetchAll($sql);
    }

    // Departman yöneticisi ekle
    public function addDepartmentManager($department, $managerId) {
        $existing = Database::fetch("SELECT id FROM department_managers WHERE department = :department AND manager_id = :manager_id", [
            ':department' => $department,
            ':manager_id' => $managerId
        ]);
        if ($existing) {
            return ['success' => false, 'message' => 'Bu departman için zaten bu yönetici atanmış!'];
        }
        Database::create("department_managers", ['department' => $department, 'manager_id' => $managerId, 'can_approve_leave' => 1]);
        return ['success' => true, 'message' => 'Departman yöneticisi başarıyla eklendi.'];
    }

    // Departman yöneticisi sil
    public function deleteDepartmentManager($managerId) {
        Database::delete($managerId, 'id', 'department_managers'); // BaseModel'de 3. parametre ile tablo adı belirtme eklemeli
        return ['success' => true, 'message' => 'Departman yöneticisi başarıyla silindi.'];
    }

    // İzin onaylama yetkisini değiştir
    public function toggleApprovalPermission($managerId, $canApprove) {
        Database::execute("UPDATE department_managers SET can_approve_leave = :can_approve WHERE id = :id", [
            ':can_approve' => $canApprove,
            ':id' => $managerId
        ]);
        return ['success' => true, 'message' => 'İzin onaylama yetkisi başarıyla güncellendi.'];
    }

    private function calculateTotalDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');
        $interval = $start->diff($end);
        return $interval->days;
    }
}
?>