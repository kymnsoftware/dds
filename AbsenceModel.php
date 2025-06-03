<?php
// models/AbsenceModel.php

require_once APP_ROOT . '/models/BaseModel.php';
require_once APP_ROOT . '/models/CardModel.php'; // Personel bilgilerini çekmek için
require_once APP_ROOT . '/models/LeaveModel.php'; // İzinli günleri kontrol etmek için

class AbsenceModel extends BaseModel {
    public function __construct() {
        parent::__construct('absences');
    }

    // Devamsızlık türlerini getir
    public function getAbsenceTypes($isActive = null) {
        $sql = "SELECT * FROM absence_types";
        $params = [];
        if ($isActive !== null) {
            $sql .= " WHERE is_active = :is_active";
            $params[':is_active'] = $isActive ? 1 : 0;
        }
        $sql .= " ORDER BY name";
        return Database::fetchAll($sql, $params);
    }

    // Yeni devamsızlık kaydı ekle
    public function addAbsence($data) {
        $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->create($data);
    }

    // Devamsızlık kaydını güncelle
    public function updateAbsence($absenceId, $data) {
        $data['total_days'] = $this->calculateTotalDays($data['start_date'], $data['end_date']);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->update($absenceId, $data);
    }

    // Devamsızlık kaydını sil
    public function deleteAbsence($absenceId) {
        return $this->delete($absenceId);
    }

    // Toplam gün sayısını hesapla
    private function calculateTotalDays($startDate, $endDate) {
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $end->modify('+1 day');
        $interval = $start->diff($end);
        return $interval->days;
    }

    // Detaylı devamsızlık analizi (Bugünkü Durum Analizi için)
    public function getDetailedAbsenceAnalysis($date) {
        $cardModel = new CardModel();
        $leaveModel = new LeaveModel(); // LeaveModel oluşturulmuş olmalı

        // 1. Tüm aktif personelleri al
        $allEmployees = $cardModel->getCards('', ''); // Tüm aktif kartları çeker
        
        // 2. Bugün giriş yapan personelleri al
        $presentEmployees = Database::fetchAll("
            SELECT DISTINCT c.user_id
            FROM attendance_logs al
            JOIN cards c ON al.card_number = c.card_number
            WHERE DATE(al.event_time) = :date AND al.event_type = 'ENTRY' AND c.enabled = 'true'
        ", [':date' => $date]);
        $presentEmployeeIds = array_column($presentEmployees, 'user_id');
        
        // 3. Bugün izinli olan personelleri al (onaylanmış izinler)
        $onLeaveEmployees = $leaveModel->getOnLeaveEmployeesForDate($date); // Yeni metod
        $onLeaveUserIds = array_column($onLeaveEmployees, 'user_id');
        
        // 4. Bugün için kayıtlı devamsızlığı olan personelleri al
        $recordedAbsences = Database::fetchAll("
            SELECT a.user_id, a.is_justified, at.name as absence_type_name, at.color
            FROM absences a
            JOIN absence_types at ON a.absence_type_id = at.id
            WHERE :date BETWEEN a.start_date AND a.end_date
        ", [':date' => $date]);
        $recordedAbsentUserIds = array_column($recordedAbsences, 'user_id');
        
        // 5. Analiz sonuçlarını kategorize et
        $analysis = [
            'present' => [],
            'on_leave' => $onLeaveEmployees,
            'absent_unrecorded' => [],
            'absent_recorded' => []
        ];
        
        foreach ($allEmployees as $employee) {
            $userId = $employee['user_id'];
            
            if (in_array($userId, $presentEmployeeIds)) {
                $analysis['present'][] = $employee;
            } elseif (in_array($userId, $onLeaveUserIds)) {
                // İzinli (zaten $analysis['on_leave'] içinde var)
                continue;
            } elseif (in_array($userId, $recordedAbsentUserIds)) {
                $absenceInfo = array_filter($recordedAbsences, function($a) use ($userId) { return $a['user_id'] == $userId; });
                $employee['absence_info'] = reset($absenceInfo);
                $analysis['absent_recorded'][] = $employee;
            } else {
                $analysis['absent_unrecorded'][] = $employee;
            }
        }
        
        return $analysis;
    }

    // Otomatik devamsızlık taraması ve kaydı
    public function performAutoAbsenceScan($date, $createdBy) {
        $analysis = $this->getDetailedAbsenceAnalysis($date);
        $processedCount = 0;
        
        // "Kayıtsız Devamsızlık" türünü bul veya oluştur
        $absenceType = Database::fetch("SELECT id FROM absence_types WHERE name LIKE '%Kayıtsız Devamsızlık%' OR name LIKE '%Mazeretsiz%' LIMIT 1");
        $absenceTypeId = $absenceType['id'] ?? null;

        if (!$absenceTypeId) {
            $absenceTypeId = Database::create("absence_types", ['name' => 'Kayıtsız Devamsızlık', 'description' => 'Sisteme kaydı olmayan devamsızlık', 'color' => '#dc3545']);
        }
        
        foreach ($analysis['absent_unrecorded'] as $employee) {
            $data = [
                'user_id' => $employee['user_id'],
                'absence_type_id' => $absenceTypeId,
                'start_date' => $date,
                'end_date' => $date,
                'total_days' => 1,
                'reason' => 'Otomatik tespit: Giriş kaydı bulunamadı',
                'is_justified' => 0,
                'created_by' => $createdBy,
                'auto_generated' => 1
            ];
            $this->addAbsence($data);
            $processedCount++;
        }
        
        return ['success' => true, 'message' => "$processedCount adet otomatik devamsızlık kaydı oluşturuldu."];
    }

    // Devamsızlık istatistiklerini getir
    public function getAbsenceStats() {
        $currentMonth = date('Y-m');
        $monthStart = $currentMonth . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        
        $sql = "
            SELECT COUNT(*) as total_absences,
                   SUM(total_days) as total_days,
                   SUM(CASE WHEN is_justified = 1 THEN total_days ELSE 0 END) as justified_days,
                   SUM(CASE WHEN is_justified = 0 THEN total_days ELSE 0 END) as unjustified_days
            FROM absences
            WHERE start_date >= :month_start AND end_date <= :month_end
        ";
        $stats = Database::fetch($sql, [':month_start' => $monthStart, ':month_end' => $monthEnd]);
        
        // En çok devamsızlık yapan departman
        $sql = "
            SELECT c.department, COUNT(a.id) as absence_count
            FROM absences a
            JOIN cards c ON a.user_id = c.user_id
            WHERE a.start_date >= :month_start AND a.end_date <= :month_end
            GROUP BY c.department
            ORDER BY absence_count DESC
            LIMIT 1
        ";
        $topDept = Database::fetch($sql, [':month_start' => $monthStart, ':month_end' => $monthEnd]);
        
        return ['stats' => $stats, 'topDept' => $topDept];
    }

    // Devamsızlık geçmişini filtreleyerek getir
    public function getAbsenceHistory($filters = []) {
        $sql = "
            SELECT a.*, at.name as absence_type_name, at.color, 
                   c.name, c.surname, c.department, c.position,
                   creator.name as created_by_name
            FROM absences a
            JOIN absence_types at ON a.absence_type_id = at.id
            JOIN cards c ON a.user_id = c.user_id
            LEFT JOIN cards creator ON a.created_by = creator.user_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.name LIKE :search OR c.surname LIKE :search)";
            $params[':search'] = "%{$filters['search']}%";
        }
        
        if (!empty($filters['department'])) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $filters['department'];
        }
        
        if (!empty($filters['absence_type'])) {
            $sql .= " AND a.absence_type_id = :absence_type";
            $params[':absence_type'] = $filters['absence_type'];
        }
        
        if (!empty($filters['date_filter'])) {
            $sql .= " AND (a.start_date <= :date_filter AND a.end_date >= :date_filter)";
            $params[':date_filter'] = $filters['date_filter'];
        }
        
        if (!empty($filters['status_filter'])) {
            if ($filters['status_filter'] == 'justified') {
                $sql .= " AND a.is_justified = 1";
            } elseif ($filters['status_filter'] == 'unjustified') {
                $sql .= " AND a.is_justified = 0";
            }
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT 100";
        
        return Database::fetchAll($sql, $params);
    }
}
?>