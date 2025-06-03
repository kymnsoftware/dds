<?php
// controllers/LeaveController.php

require_once APP_ROOT . '/models/LeaveModel.php';
require_once APP_ROOT . '/models/CardModel.php'; // Kullanıcıları ve departmanları çekmek için
require_once APP_ROOT . '/core/Auth.php';

class LeaveController {
    private $leaveModel;
    private $cardModel;

    public function __construct() {
        $this->leaveModel = new LeaveModel();
        $this->cardModel = new CardModel();
    }

    // İzin Yönetimi sayfasını render et (Yönetici Paneli)
    public function index() {
        Auth::redirectIfUnauthorized(1); // En az Kayıt Yetkilisi

        $leaveTypes = $this->leaveModel->getLeaveTypes();
        $users = $this->cardModel->getUsersList();
        $departments = $this->cardModel->getDepartments();
        $pendingRequests = $this->leaveModel->getPendingLeaveRequests(); // İlk yükleme için
        $allBalances = $this->leaveModel->getLeaveBalances(); // Tüm bakiyeler
        $departmentManagers = $this->leaveModel->getDepartmentManagers();

        $this->renderView('leave/index', [
            'leaveTypes' => $leaveTypes,
            'users' => $users,
            'departments' => $departments,
            'pendingRequests' => $pendingRequests,
            'allBalances' => $allBalances,
            'departmentManagers' => $departmentManagers
        ]);
    }
    
    // İzin Talep sayfası (Normal Kullanıcı)
    public function requestPage() {
        Auth::redirectIfNotLoggedIn(); // Kullanıcının giriş yapmış olması yeterli

        $leaveTypes = $this->leaveModel->getLeaveTypes(true); // Aktif olanları getir
        $userRequests = $this->leaveModel->getUserLeaveRequests(Auth::getUser()['user_id']);
        $userBalances = $this->leaveModel->getLeaveBalances(Auth::getUser()['user_id'], date('Y'));

        $this->renderView('leave/request', [
            'leaveTypes' => $leaveTypes,
            'userRequests' => $userRequests,
            'userBalances' => $userBalances
        ]);
    }

    // AJAX: Bekleyen izin taleplerini getir
    public function getPendingLeavesAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $pendingRequests = $this->leaveModel->getPendingLeaveRequests(5); // Son 5 taneyi getir
        
        $html = '';
        if (count($pendingRequests) > 0) {
            foreach ($pendingRequests as $request) {
                $html .= '<div class="leave-item mb-3 p-3" style="border-left: 4px solid '.sanitize_html($request['color']).'; background-color: #f8f9fa; border-radius: 4px;">';
                $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
                $html .= '<h6 class="mb-0">'.sanitize_html($request['name']).' '.sanitize_html($request['surname']).'</h6>';
                $html .= '<span class="badge" style="background-color: '.sanitize_html($request['color']).'; color: white;">'.sanitize_html($request['leave_type_name']).'</span>';
                $html .= '</div>';
                $html .= '<div class="text-muted small">'.sanitize_html($request['department']).' - '.sanitize_html($request['position']).'</div>';
                $html .= '<div class="mt-2">';
                $html .= '<strong>Tarih:</strong> '.date('d.m.Y', strtotime($request['start_date'])).' - '.date('d.m.Y', strtotime($request['end_date'])).' ('.sanitize_html($request['total_days']).' gün)';
                $html .= '</div>';
                $html .= '<div class="mt-2 d-flex justify-content-end">';
                $html .= '<a href="/leave_management.php#pending" class="btn btn-sm btn-primary">İşlem Yap</a>';
                $html .= '</div>';
                $html .= '</div>';
            }
            if (count($pendingRequests) >= 5) {
                $html .= '<div class="text-center mt-3">';
                $html .= '<a href="/leave_management.php#pending" class="btn btn-outline-primary btn-sm">Tümünü Görüntüle</a>';
                $html .= '</div>';
            }
        } else {
            $html .= '<div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i> Bekleyen izin talebi bulunmamaktadır.</div>';
        }
        echo json_encode(['success' => true, 'html' => $html, 'count' => count($pendingRequests)]);
    }

    // AJAX: İşlenmiş izin taleplerini getir
    public function getProcessedLeavesAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $filters = [
            'name' => $_GET['name'] ?? '',
            'department' => $_GET['department'] ?? '',
            'leave_type' => $_GET['leave_type'] ?? '',
            'status' => $_GET['status'] ?? '',
            'start_date' => $_GET['start_date'] ?? '',
            'end_date' => $_GET['end_date'] ?? ''
        ];

        $requests = $this->leaveModel->getProcessedLeaveRequests($filters);
        
        ob_start();
        include APP_ROOT . '/views/leave/processed_partial.php'; // Kısmi HTML
        $html = ob_get_clean();

        echo json_encode(['success' => true, 'html' => $html, 'count' => count($requests)]);
    }

    // AJAX: İzin bakiyelerini getir
    public function getLeaveBalancesAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $currentYear = date('Y');
        $balances = $this->leaveModel->getLeaveBalances(null, $currentYear, 10); // Son 10 taneyi getir
        
        $html = '';
        if (count($balances) > 0) {
            $html .= '<div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>Personel</th><th>İzin Türü</th><th>Toplam</th><th>Kullanılan</th><th>Kalan</th></tr></thead><tbody>';
            foreach ($balances as $balance) {
                $html .= '<tr><td>'.sanitize_html($balance['name']).' '.sanitize_html($balance['surname']).'</td>';
                $html .= '<td>'.sanitize_html($balance['leave_type_name']).'</td>';
                $html .= '<td>'.sanitize_html($balance['total_days']).'</td>';
                $html .= '<td>'.sanitize_html($balance['used_days']).'</td>';
                $html .= '<td><strong>'.sanitize_html($balance['remaining_days']).'</strong></td></tr>';
            }
            $html .= '</tbody></table></div>';
            
            if (count($balances) >= 10) {
                $html .= '<div class="text-center mt-3"><a href="/leave_management.php#balances" class="btn btn-outline-success btn-sm">Tümünü Görüntüle</a></div>';
            }
        } else {
            $html .= '<div class="alert alert-info"><i class="fas fa-info-circle mr-1"></i> Tanımlanmış izin bakiyesi bulunmamaktadır.</div>';
        }
        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: İzin detayını getir
    public function getLeaveDetailAjax() {
        Auth::redirectIfNotLoggedIn();
        $leaveId = $_GET['id'] ?? null;
        
        if (!$leaveId) {
            echo json_encode(['success' => false, 'message' => 'İzin ID eksik!']);
            return;
        }

        $leave = $this->leaveModel->getLeaveDetail($leaveId);
        if (!$leave) {
            echo json_encode(['success' => false, 'message' => 'İzin bulunamadı!']);
            return;
        }

        ob_start();
        include APP_ROOT . '/views/leave/detail_partial.php'; // Kısmi HTML
        $html = ob_get_clean();

        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: İzin talebi onayla/reddet
    public function updateLeaveRequestStatusAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1); // En az kayıt yetkilisi

        $requestId = $_POST['request_id'] ?? null;
        $status = $_POST['status'] ?? null;
        $comment = $_POST['comment'] ?? '';
        $approvedBy = Auth::getUser()['user_id'];

        if (!$requestId || !in_array($status, ['approved', 'rejected'])) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz istek!']);
            return;
        }
        if ($status == 'rejected' && empty($comment)) {
            echo json_encode(['success' => false, 'message' => 'Reddetme nedeni belirtilmelidir!']);
            return;
        }

        try {
            $updated = $this->leaveModel->updateLeaveRequestStatus($requestId, $status, $comment, $approvedBy);
            if ($updated) {
                echo json_encode(['success' => true, 'message' => 'İzin talebi başarıyla güncellendi.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'İzin talebi güncellenemedi veya bulunamadı.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'İşlem sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: İzin bakiyesi tanımla/güncelle
    public function updateLeaveBalanceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $data = [
            'user_id' => $_POST['balance_user_id'] ?? '',
            'leave_type_id' => $_POST['balance_leave_type_id'] ?? '',
            'year' => $_POST['balance_year'] ?? date('Y'),
            'total_days' => $_POST['balance_total_days'] ?? 0
        ];

        if (empty($data['user_id']) || empty($data['leave_type_id']) || empty($data['year']) || !is_numeric($data['total_days'])) {
            echo json_encode(['success' => false, 'message' => 'Lütfen tüm gerekli alanları doldurun!']);
            return;
        }

        try {
            $this->leaveModel->updateLeaveBalance($data);
            echo json_encode(['success' => true, 'message' => 'İzin bakiyesi başarıyla güncellendi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'İşlem sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: İzin takvimi etkinliklerini getir
    public function getCalendarEventsAjax() {
        Auth::redirectIfNotLoggedIn();

        $start = $_GET['start'] ?? '';
        $end = $_GET['end'] ?? '';
        $department = $_GET['department'] ?? '';
        $leaveTypes = isset($_GET['leave_types']) ? explode(',', $_GET['leave_types']) : [];
        $status = $_GET['status'] ?? 'approved';

        $sql = "
            SELECT lr.id, lr.user_id, lr.start_date, lr.end_date, lr.status, 
                   c.name, c.surname, c.department, 
                   lt.name as leave_type_name, lt.color
            FROM leave_requests lr
            JOIN cards c ON lr.user_id = c.user_id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($start) && !empty($end)) {
            $sql .= " AND (
                (lr.start_date BETWEEN :start AND :end)
                OR (lr.end_date BETWEEN :start AND :end)
                OR (lr.start_date <= :start AND lr.end_date >= :end)
            )";
            $params[':start'] = $start;
            $params[':end'] = $end;
        }
        
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        
        if (!empty($leaveTypes)) {
            $placeholders = implode(',', array_fill(0, count($leaveTypes), '?'));
            $sql .= " AND lr.leave_type_id IN ({$placeholders})";
            $params = array_merge($params, $leaveTypes);
        }
        
        if (!empty($status)) {
            $sql .= " AND lr.status = :status_val";
            $params[':status_val'] = $status;
        }
        
        $leaves = Database::fetchAll($sql, $params);
        
        $events = [];
        foreach ($leaves as $leave) {
            $startDate = new DateTime($leave['start_date']);
            $endDate = new DateTime($leave['end_date']);
            $endDate->modify('+1 day');
            
            $statusClass = '';
            switch ($leave['status']) {
                case 'pending': $statusClass = 'pending-leave'; break;
                case 'approved': $statusClass = 'approved-leave'; break;
                case 'rejected': $statusClass = 'rejected-leave'; break;
            }
            
            $events[] = [
                'id' => sanitize_html($leave['id']),
                'title' => sanitize_html($leave['name']) . ' ' . sanitize_html($leave['surname']) . ' - ' . sanitize_html($leave['leave_type_name']),
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
                'backgroundColor' => sanitize_html($leave['color']),
                'borderColor' => sanitize_html($leave['color']),
                'classNames' => $statusClass,
                'extendedProps' => [
                    'userId' => sanitize_html($leave['user_id']),
                    'department' => sanitize_html($leave['department']),
                    'leaveType' => sanitize_html($leave['leave_type_name']),
                    'status' => sanitize_html($leave['status'])
                ]
            ];
        }
        echo json_encode(['success' => true, 'events' => $events]);
    }
    
    // AJAX: Yeni izin talebi oluştur (personel sayfası)
    public function submitLeaveRequestAjax() {
        Auth::redirectIfNotLoggedIn();

        $data = [
            'user_id' => Auth::getUser()['user_id'],
            'leave_type_id' => $_POST['leave_type_id'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => $_POST['reason'] ?? ''
        ];

        if (empty($data['leave_type_id']) || empty($data['start_date']) || empty($data['end_date'])) {
            echo json_encode(['success' => false, 'message' => 'Lütfen tüm gerekli alanları doldurun!']);
            return;
        }

        try {
            $this->leaveModel->submitLeaveRequest($data);
            echo json_encode(['success' => true, 'message' => 'İzin talebiniz başarıyla oluşturuldu.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Talep oluşturulurken hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Departman yöneticisi ekle
    public function addDepartmentManagerAjax() {
        Auth::redirectIfUnauthorized(1); // Yetki kontrolü (manager add yetkisi)

        $department = $_POST['manager_department'] ?? '';
        $managerId = $_POST['manager_id'] ?? '';

        if (empty($department) || empty($managerId)) {
            echo json_encode(['success' => false, 'message' => 'Lütfen tüm gerekli alanları doldurun!']);
            return;
        }

        $result = $this->leaveModel->addDepartmentManager($department, $managerId);
        echo json_encode($result);
    }

    // AJAX: Departman yöneticisi sil
    public function deleteDepartmentManagerAjax() {
        Auth::redirectIfUnauthorized(1);

        $managerId = $_POST['manager_id_delete'] ?? null;
        if (!$managerId) {
            echo json_encode(['success' => false, 'message' => 'Yönetici ID eksik!']);
            return;
        }

        $result = $this->leaveModel->deleteDepartmentManager($managerId);
        echo json_encode($result);
    }

    // AJAX: İzin onaylama yetkisini değiştir
    public function toggleApprovalPermissionAjax() {
        Auth::redirectIfUnauthorized(1);

        $managerId = $_POST['manager_id_toggle'] ?? null;
        $canApprove = $_POST['can_approve'] ?? '0';

        if (!$managerId) {
            echo json_encode(['success' => false, 'message' => 'Yönetici ID eksik!']);
            return;
        }

        $result = $this->leaveModel->toggleApprovalPermission($managerId, $canApprove);
        echo json_encode($result);
    }

    // HTML görünümünü render eden yardımcı fonksiyon
    private function renderView($viewName, $data = []) {
        extract($data); // $data dizisindeki elemanları değişken olarak kullanılabilir yap

        // Header'ı dahil et
        include APP_ROOT . '/views/shared/header.php';

        // İlgili view dosyasını dahil et
        include APP_ROOT . '/views/' . $viewName . '.php';

        // Footer'ı dahil et
        include APP_ROOT . '/views/shared/footer.php';
    }
}
?>