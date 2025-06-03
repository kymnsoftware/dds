<?php
// controllers/AbsenceController.php

require_once APP_ROOT . '/models/AbsenceModel.php';
require_once APP_ROOT . '/models/CardModel.php';
require_once APP_ROOT . '/models/LeaveModel.php'; // getDetailedAbsenceAnalysis için
require_once APP_ROOT . '/core/Auth.php';

class AbsenceController {
    private $absenceModel;
    private $cardModel;
    private $leaveModel; // getDetailedAbsenceAnalysis için

    public function __construct() {
        $this->absenceModel = new AbsenceModel();
        $this->cardModel = new CardModel();
        $this->leaveModel = new LeaveModel();
    }

    // Devamsızlık Takibi sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // En az Kayıt Yetkilisi

        $today = date('Y-m-d');
        $todayAnalysis = $this->absenceModel->getDetailedAbsenceAnalysis($today);
        $absenceTypes = $this->absenceModel->getAbsenceTypes(true); // Aktif olanları getir
        $users = $this->cardModel->getUsersList();
        $departments = $this->cardModel->getDepartments();


        $this->renderView('absence/index', [
            'todayAnalysis' => $todayAnalysis,
            'absenceTypes' => $absenceTypes,
            'users' => $users,
            'departments' => $departments, // Rapor filtreleri için
            'current_date' => $today
        ]);
    }

    // AJAX: Bugünkü devamsızlık analizini getir
    public function getTodayAbsencesAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $today = date('Y-m-d');
        $analysis = $this->absenceModel->getDetailedAbsenceAnalysis($today);
        
        $html = '';
        
        // Özet bilgiler
        $html .= '<div class="row mb-3">';
        $html .= '<div class="col-6"><div class="alert alert-success mb-2">Giriş Yapan: <strong>'.count($analysis['present']).'</strong></div></div>';
        $html .= '<div class="col-6"><div class="alert alert-info mb-2">İzinli: <strong>'.count($analysis['on_leave']).'</strong></div></div>';
        $html .= '<div class="col-6"><div class="alert alert-warning mb-2">Kayıtlı Devamsız: <strong>'.count($analysis['absent_recorded']).'</strong></div></div>';
        $html .= '<div class="col-6"><div class="alert alert-danger mb-2">Kayıtsız Devamsız: <strong>'.count($analysis['absent_unrecorded']).'</strong></div></div>';
        $html .= '</div>';
        
        // Kayıtsız devamsızlar
        if (count($analysis['absent_unrecorded']) > 0) {
            $html .= '<h6 class="text-danger"><i class="fas fa-exclamation-triangle mr-1"></i> Kayıtsız Devamsızlar:</h6>';
            $displayCount = 0;
            foreach ($analysis['absent_unrecorded'] as $employee) {
                if ($displayCount >= 5) break;
                $html .= '<div class="mb-2 p-2" style="background-color: #f8d7da; border-left: 4px solid #dc3545; border-radius: 3px;">';
                $html .= '<strong>'.sanitize_html($employee['name']).' '.sanitize_html($employee['surname']).'</strong><br>';
                $html .= '<small class="text-muted">'.sanitize_html($employee['department']).' - '.sanitize_html($employee['position']).'</small>';
                $html .= '<div class="mt-2">
                                <button type="button" class="btn btn-sm btn-success add-justified-absence"
                                        data-user-id="'.sanitize_html($employee['user_id']).'"
                                        data-name="'.sanitize_html($employee['name']).' '.sanitize_html($employee['surname']).'">
                                    <i class="fas fa-check mr-1"></i> Mazeretli
                                </button>
                                <button type="button" class="btn btn-sm btn-danger add-unjustified-absence"
                                        data-user-id="'.sanitize_html($employee['user_id']).'"
                                        data-name="'.sanitize_html($employee['name']).' '.sanitize_html($employee['surname']).'">
                                    <i class="fas fa-times mr-1"></i> Mazeretsiz
                                </button>
                            </div>';
                $html .= '</div>';
                $displayCount++;
            }
            
            if (count($analysis['absent_unrecorded']) > 5) {
                $html .= '<div class="text-center mt-2">';
                $html .= '<small class="text-muted">+'.(count($analysis['absent_unrecorded']) - 5).' kişi daha...</small>';
                $html .= '</div>';
            }
        }
        
        // İzinli olanlar
        if (count($analysis['on_leave']) > 0) {
            $html .= '<h6 class="text-info mt-3"><i class="fas fa-calendar-alt mr-1"></i> İzinli Personel:</h6>';
            $displayCount = 0;
            foreach ($analysis['on_leave'] as $employee) {
                if ($displayCount >= 3) break;
                $html .= '<div class="mb-2 p-2" style="background-color: #d1ecf1; border-left: 4px solid #17a2b8; border-radius: 3px;">';
                $html .= '<strong>'.sanitize_html($employee['name']).' '.sanitize_html($employee['surname']).'</strong><br>';
                $html .= '<small class="text-muted">'.sanitize_html($employee['department']).'</small><br>';
                $html .= '<span class="badge mt-1" style="background-color: '.sanitize_html($employee['color']).'; color: white; font-size: 10px;">';
                $html .= sanitize_html($employee['leave_type_name']);
                $html .= '</span>';
                $html .= '</div>';
                $displayCount++;
            }
            
            if (count($analysis['on_leave']) > 3) {
                $html .= '<div class="text-center mt-2">';
                $html .= '<small class="text-muted">+'.(count($analysis['on_leave']) - 3).' kişi daha...</small>';
                $html .= '</div>';
            }
        }
        
        // Detay sayfası linki
        $html .= '<div class="text-center mt-3">';
        $html .= '<a href="/attendance_tracking.php" class="btn btn-outline-primary btn-sm">Detaylı Görünüm</a>';
        $html .= '</div>';
        
        $totalAbsent = count($analysis['absent_unrecorded']) + count($analysis['absent_recorded']);
        
        echo json_encode([
            'success' => true, 
            'html' => $html, 
            'count' => $totalAbsent,
            'details' => [
                'present' => count($analysis['present']),
                'on_leave' => count($analysis['on_leave']),
                'absent_unrecorded' => count($analysis['absent_unrecorded']),
                'absent_recorded' => count($analysis['absent_recorded'])
            ]
        ]);
    }

    // AJAX: Devamsızlık istatistiklerini getir
    public function getAbsenceStatsAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $data = $this->absenceModel->getAbsenceStats();
        $stats = $data['stats'];
        $topDept = $data['topDept'];
        
        $html = '<div class="row">';
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-danger">'.($stats['total_absences'] ?: 0).'</h4>';
        $html .= '<small>Bu Ayki Toplam Devamsızlık</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-warning">'.($stats['total_days'] ?: 0).'</h4>';
        $html .= '<small>Toplam Devamsızlık Günü</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-success">'.($stats['justified_days'] ?: 0).'</h4>';
        $html .= '<small>Mazeretli Gün</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-danger">'.($stats['unjustified_days'] ?: 0).'</h4>';
        $html .= '<small>Mazeretsiz Gün</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        if ($topDept) {
            $html .= '<div class="col-md-12">';
            $html .= '<div class="alert alert-info">';
            $html .= '<strong>En Çok Devamsızlık:</strong> '.sanitize_html($topDept['department']).' ('.sanitize_html($topDept['absence_count']).' kayıt)';
            $html .= '</div>';
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: Manuel devamsızlık ekle
    public function addAbsenceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $data = [
            'user_id' => $_POST['user_id'] ?? '',
            'absence_type_id' => $_POST['absence_type_id'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => $_POST['reason'] ?? '',
            'is_justified' => isset($_POST['is_justified']) ? 1 : 0,
            'created_by' => Auth::getUser()['user_id']
        ];
        
        if (empty($data['user_id']) || empty($data['absence_type_id']) || empty($data['start_date']) || empty($data['end_date'])) {
            echo json_encode(['success' => false, 'message' => 'Lütfen tüm gerekli alanları doldurun!']);
            return;
        }

        try {
            $this->absenceModel->addAbsence($data);
            echo json_encode(['success' => true, 'message' => 'Devamsızlık kaydı başarıyla eklendi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Devamsızlık eklenirken hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Otomatik devamsızlık taraması başlat
    public function autoScanAbsenceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $scanDate = $_POST['scan_date'] ?? date('Y-m-d');
        
        try {
            $result = $this->absenceModel->performAutoAbsenceScan($scanDate, Auth::getUser()['user_id']);
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Otomatik tarama sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Devamsızlık geçmişini getir
    public function getAbsenceHistoryAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $filters = [
            'search' => $_GET['search'] ?? '',
            'department' => $_GET['department'] ?? '',
            'absence_type' => $_GET['absence_type'] ?? '',
            'date_filter' => $_GET['date_filter'] ?? '',
            'status_filter' => $_GET['status_filter'] ?? ''
        ];

        $absences = $this->absenceModel->getAbsenceHistory($filters);
        $allAbsenceTypes = $this->absenceModel->getAbsenceTypes();
        $allDepartments = $this->cardModel->getDepartments();

        ob_start();
        include APP_ROOT . '/views/absence/history_partial.php';
        $html = ob_get_clean();
        echo $html;
    }

    // AJAX: Devamsızlık detayını getir
    public function getAbsenceDetailAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $absenceId = $_GET['id'] ?? null;
        if (!$absenceId) {
            echo json_encode(['success' => false, 'message' => 'Devamsızlık ID eksik!']);
            return;
        }

        $absence = $this->absenceModel->find($absenceId);
        if (!$absence) {
            echo json_encode(['success' => false, 'message' => 'Devamsızlık kaydı bulunamadı!']);
            return;
        }
        
        // Ek bilgileri çek
        $cardInfo = $this->cardModel->find($absence['user_id']);
        $absenceTypeInfo = Database::fetch("SELECT name, color FROM absence_types WHERE id = :id", [':id' => $absence['absence_type_id']]);
        $creatorInfo = $this->cardModel->find($absence['created_by']);

        $absence['user_name'] = $cardInfo['name'] ?? '';
        $absence['user_surname'] = $cardInfo['surname'] ?? '';
        $absence['department'] = $cardInfo['department'] ?? '';
        $absence['position'] = $cardInfo['position'] ?? '';
        $absence['photo_path'] = $cardInfo['photo_path'] ?? '';
        $absence['absence_type_name'] = $absenceTypeInfo['name'] ?? 'Bilinmiyor';
        $absence['color'] = $absenceTypeInfo['color'] ?? '#000000';
        $absence['created_by_name'] = $creatorInfo['name'] ?? 'Sistem';

        ob_start();
        include APP_ROOT . '/views/absence/detail_partial.php';
        $html = ob_get_clean();

        echo json_encode(['success' => true, 'html' => $html, 'data' => $absence]);
    }

    // AJAX: Devamsızlık kaydını güncelle
    public function updateAbsenceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $data = [
            'id' => $_POST['absence_id'] ?? '',
            'absence_type_id' => $_POST['absence_type_id'] ?? '',
            'start_date' => $_POST['start_date'] ?? '',
            'end_date' => $_POST['end_date'] ?? '',
            'reason' => $_POST['reason'] ?? '',
            'admin_note' => $_POST['admin_note'] ?? '',
            'is_justified' => isset($_POST['is_justified']) ? 1 : 0
        ];

        if (empty($data['id']) || empty($data['absence_type_id']) || empty($data['start_date']) || empty($data['end_date'])) {
            echo json_encode(['success' => false, 'message' => 'Gerekli alanlar eksik!']);
            return;
        }

        try {
            $this->absenceModel->updateAbsence($data['id'], $data);
            echo json_encode(['success' => true, 'message' => 'Devamsızlık kaydı başarıyla güncellendi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Devamsızlık kaydını sil
    public function deleteAbsenceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $absenceId = $_POST['absence_id'] ?? null;
        if (!$absenceId) {
            echo json_encode(['success' => false, 'message' => 'Devamsızlık ID eksik!']);
            return;
        }

        try {
            $this->absenceModel->deleteAbsence($absenceId);
            echo json_encode(['success' => true, 'message' => 'Devamsızlık kaydı başarıyla silindi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Silme sırasında hata: ' . $e->getMessage()]);
        }
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