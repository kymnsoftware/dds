<?php
// controllers/SalaryController.php

class SalaryController {
    private $salaryModel;
    private $cardModel;
    private $settingModel;

    public function __construct() {
        $this->salaryModel = new SalaryModel();
        $this->cardModel = new CardModel();
        $this->settingModel = new SettingModel();
    }

    // Maaş Yönetimi sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // Minimum yetki: Kayıt Yetkilisi

        $users = $this->cardModel->getUsersList(); // Tüm kullanıcıları al
        $departments = $this->cardModel->getDepartments(); // Tüm departmanları al
        $systemSettings = $this->settingModel->getSalarySettings(); // Maaş sistemi ayarlarını al

        $this->renderView('salary/index', [
            'users' => $users,
            'departments' => $departments,
            'systemSettings' => $systemSettings
        ]);
    }

    // AJAX: Tekil personel için maaş hesapla
    public function calculateSalaryAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_GET['user_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Kullanıcı ID eksik!']);
            return;
        }

        $result = $this->salaryModel->calculateSalary($userId, $startDate, $endDate);
        echo json_encode($result);
    }

    // AJAX: Maaş istatistiklerini getir
    public function getSalaryStatsAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $stats = $this->salaryModel->getSalaryStats();
        
        $html = '<div class="row">';
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-primary">' . sanitize_html($stats['total_employees']) . '</h4>';
        $html .= '<small>Toplam Personel</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-success">' . number_format($stats['avg_salary'], 0, ',', '.') . ' ₺</h4>';
        $html .= '<small>Ortalama Maaş</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-info">' . number_format($stats['max_salary'], 0, ',', '.') . ' ₺</h4>';
        $html .= '<small>En Yüksek Maaş</small>';
        $html .= '</div>';
        $html .= '</div>';
        
        $html .= '<div class="col-md-6 mb-3">';
        $html .= '<div class="text-center p-3" style="background-color: #f8f9fa; border-radius: 5px;">';
        $html .= '<h4 class="text-warning">' . number_format($stats['min_salary'], 0, ',', '.') . ' ₺</h4>';
        $html .= '<small>En Düşük Maaş</small>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</div>';
        
        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: Maaş sistemi ayarları özetini getir
    public function getSalarySettingsSummaryAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $settings = $this->settingModel->getSalarySettings(); // SettingModel'den

        $minimumType = $settings['salary_minimum_type'] ?? 'percentage';
        $minimumValue = $minimumType === 'percentage' ? 
                       ($settings['salary_minimum_work_rate'] ?? 90) . '%' : 
                       ($settings['salary_minimum_work_days'] ?? 20) . ' gün';
        
        $excludeWeekends = ($settings['salary_exclude_weekends'] ?? 'true') === 'true' ? 'Evet' : 'Hayır';
        $excludeHolidays = ($settings['salary_exclude_holidays'] ?? 'true') === 'true' ? 'Evet' : 'Hayır';
        
        $html = '<ul class="list-group list-group-flush">';
        $html .= '<li class="list-group-item d-flex justify-content-between">';
        $html .= '<span>Minimum Çalışma:</span><span class="badge badge-primary">' . sanitize_html($minimumValue) . '</span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item d-flex justify-content-between">';
        $html .= '<span>Hafta Sonu Hariç:</span><span class="badge badge-secondary">' . sanitize_html($excludeWeekends) . '</span>';
        $html .= '</li>';
        $html .= '<li class="list-group-item d-flex justify-content-between">';
        $html .= '<span>Tatil Hariç:</span><span class="badge badge-secondary">' . sanitize_html($excludeHolidays) . '</span>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '<div class="mt-3">';
        $html .= '<a href="/public/index.php?page=salary#settings" class="btn btn-sm btn-outline-primary btn-block">Ayarları Düzenle</a>';
        $html .= '</div>';
        
        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: Personel maaş genel bakışını getir (sadece ilk 10)
    public function getSalaryOverviewAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $month = $_GET['month'] ?? date('Y-m');
        $department = $_GET['department'] ?? '';

        // Kullanıcıları filtreli çek, ancak burada basitçe tümünü çekip PHP'de filtreleyebiliriz
        // veya CardModel'e filtreli getUsers metodu ekleyebiliriz.
        $users = $this->cardModel->getCards('', $department); // Department filtresi ile
        // Sadece ilk 10'u almak için bir limit uygulayalım, veya AJAX'ta sadece 10 gösterilsin
        $displayUsers = array_slice($users, 0, 10);
        
        $html = '';
        if (count($displayUsers) > 0) {
            $html .= '<div class="table-responsive">';
            $html .= '<table class="table table-sm table-striped">';
            $html .= '<thead>';
            $html .= '<tr>';
            $html .= '<th>Personel</th>';
            $html .= '<th>Departman</th>';
            $html .= '<th>Sabit Maaş/Saatlik Ücret</th>'; // Kolon başlığı güncellendi
            $html .= '<th>İşlemler</th>';
            $html .= '</tr>';
            $html .= '</thead>';
            $html .= '<tbody>';
            
            foreach ($displayUsers as $user) {
                $salaryInfo = '';
                if (($user['salary_type'] ?? 'fixed') === 'fixed') {
                    $salaryInfo = '<span class="badge badge-primary">' . number_format($user['fixed_salary'] ?: 35000, 0, ',', '.') . ' ₺</span>';
                } else {
                    $salaryInfo = '<span class="badge badge-warning">' . number_format($user['hourly_rate'] ?: 0, 0, ',', '.') . ' ₺/saat</span>';
                }

                $html .= '<tr>';
                $html .= '<td>' . sanitize_html($user['name']) . ' ' . sanitize_html($user['surname']) . '</td>';
                $html .= '<td>' . (!empty($user['department']) ? sanitize_html($user['department']) : '-') . '</td>';
                $html .= '<td>' . $salaryInfo . '</td>';
                $html .= '<td>';
                $html .= '<a href="/public/index.php?page=salary&tab=calculator&user_id=' . sanitize_html($user['user_id']) . '" class="btn btn-sm btn-outline-primary">'; // Maaş hesaplama sayfasına yönlendir
                $html .= '<i class="fas fa-calculator"></i> Hesapla';
                $html .= '</a>';
                $html .= '</td>';
                $html .= '</tr>';
            }
            
            $html .= '</tbody>';
            $html .= '</table>';
            $html .= '</div>';
            
            if (count($users) > 10) { // Eğer 10'dan fazla personel varsa
                $html .= '<div class="text-center mt-2">';
                $html .= '<small class="text-muted">İlk 10 personel gösteriliyor. Tüm liste için ';
                $html .= '<a href="/public/index.php?page=salary#employee-settings" target="_blank">personel maaş ayarları sayfasını</a> ziyaret edin.</small>';
                $html .= '</div>';
            }
        } else {
            $html = '<div class="alert alert-info">Bu kriterlere uygun personel bulunamadı.</div>';
        }
        
        echo json_encode(['success' => true, 'html' => $html]);
    }

    // AJAX: Personel maaş ayarlarını güncelle
    public function updateEmployeeSalaryAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_POST['user_id'] ?? null;
        $data = [
            'salary_type' => $_POST['salary_type'] ?? 'fixed',
            'fixed_salary' => $_POST['fixed_salary'] ?? 0,
            'hourly_rate' => $_POST['hourly_rate'] ?? 0,
            'overtime_rate' => $_POST['overtime_rate'] ?? 1.5,
            'daily_work_hours' => $_POST['daily_work_hours'] ?? 8.0,
            'monthly_work_days' => $_POST['monthly_work_days'] ?? 22
        ];

        if (!$userId || (!is_numeric($data['fixed_salary']) && !is_numeric($data['hourly_rate']))) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz veri!']);
            return;
        }

        try {
            $this->salaryModel->updateEmployeeSalarySettings($userId, $data);
            echo json_encode(['success' => true, 'message' => 'Maaş ayarları başarıyla güncellendi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Güncelleme sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Toplu maaş hesaplama
    public function bulkSalaryCalculationAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $department = $_GET['department'] ?? '';

        $results = $this->salaryModel->calculateBulkSalaries($startDate, $endDate, $department);

        $summary = [
            'total_employees' => count($results),
            'meeting_requirement' => 0,
            'not_meeting_requirement' => 0,
            'total_gross_salary' => 0,
            'total_net_salary' => 0,
            'total_deductions' => 0
        ];

        $html = '<div class="mt-4">';
        $html .= '<h5>Toplu Hesaplama Sonuçları</h5>';
        $html .= '<div class="table-responsive">';
        $html .= '<table class="table table-striped table-bordered">';
        $html .= '<thead class="thead-dark">';
        $html .= '<tr><th>Personel</th><th>Departman</th><th>Çalışılan</th><th>İzinli</th><th>Toplam</th><th>Gerekli</th><th>Durum</th><th>Net Maaş</th></tr>';
        $html .= '</thead><tbody>';
        
        foreach ($results as $result) {
            $badgeClass = $result['salary']['meets_minimum_requirement'] ? 'badge-success' : 'badge-danger';
            $statusText = $result['salary']['meets_minimum_requirement'] ? 'Tamam' : 'Eksik';
            
            $html .= '<tr>';
            $html .= '<td>' . sanitize_html($result['employee']['name']) . '</td>';
            $html .= '<td>' . sanitize_html($result['employee']['department']) . '</td>';
            $html .= '<td>' . sanitize_html($result['attendance']['worked_days']) . '</td>';
            $html .= '<td>' . sanitize_html($result['attendance']['approved_leave_days']) . '</td>';
            $html .= '<td>' . sanitize_html($result['attendance']['total_attended_days']) . '</td>';
            $html .= '<td>' . sanitize_html($result['period']['required_work_days']) . '</td>';
            $html .= '<td><span class="badge ' . $badgeClass . '">' . $statusText . '</span></td>';
            $html .= '<td><strong>' . number_format($result['salary']['net_salary'], 2, ',', '.') . ' TL</strong></td>';
            $html .= '</tr>';

            // Özet bilgileri güncelle
            if ($result['salary']['meets_minimum_requirement']) {
                $summary['meeting_requirement']++;
            } else {
                $summary['not_meeting_requirement']++;
            }
            $summary['total_gross_salary'] += $result['salary']['fixed_salary']; // Sadece fixed olanlar için doğru olabilir
            $summary['total_net_salary'] += $result['salary']['net_salary'];
            $summary['total_deductions'] += $result['salary']['deduction_amount'];
        }
        
        $html .= '</tbody></table>';
        $html .= '</div>';
        $html .= '</div>';

        // Summary box ekle
        $html .= '<div class="card mt-4">';
        $html .= '<div class="card-header bg-primary text-white">Genel Özet</div>';
        $html .= '<div class="card-body">';
        $html .= '<table class="table table-sm table-borderless">';
        $html .= '<tr><th>Toplam Personel</th><td>' . sanitize_html($summary['total_employees']) . '</td></tr>';
        $html .= '<tr><th>Şartı Karşılayan</th><td>' . sanitize_html($summary['meeting_requirement']) . '</td></tr>';
        $html .= '<tr><th>Şartı Karşılamayan</th><td>' . sanitize_html($summary['not_meeting_requirement']) . '</td></tr>';
        $html .= '<tr><th>Toplam Net Maaş</th><td><strong>' . number_format($summary['total_net_salary'], 2, ',', '.') . ' TL</strong></td></tr>';
        $html .= '</table>';
        $html .= '</div></div>';

        echo json_encode(['success' => true, 'html' => $html]);
    }
    
    // Raporlama Fonksiyonları (Excel/PDF Export)
    private function generateSalaryReport($data, $title, $format) {
        $filename = 'maas_raporu_' . date('Y-m-d_H-i-s');
        if ($format === 'excel') {
            $filename .= '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        } elseif ($format === 'pdf') {
            $filename .= '.html'; // PDF için HTML çıktısı
            header('Content-Type: text/html; charset=utf-8');
        } else {
            return; // Geçersiz format
        }
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // HTML çıktısını oluştur
        ob_start();
        include APP_ROOT . '/views/salary/report_template.php'; // Ortak rapor şablonu
        $htmlContent = ob_get_clean();

        echo $htmlContent;
        exit;
    }

    public function exportSalaryPdfAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_GET['user_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $result = $this->salaryModel->calculateSalary($userId, $startDate, $endDate);
        if (!$result['success']) {
            die($result['message']);
        }
        
        $title = 'Maaş Hesaplama Raporu - ' . sanitize_html($result['employee']['name']);
        $this->generateSalaryReport($result, $title, 'pdf');
    }

    public function exportSalaryExcelAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_GET['user_id'] ?? null;
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        $result = $this->salaryModel->calculateSalary($userId, $startDate, $endDate);
        if (!$result['success']) {
            die($result['message']);
        }
        
        $title = 'Maaş Hesaplama Raporu - ' . sanitize_html($result['employee']['name']);
        $this->generateSalaryReport($result, $title, 'excel');
    }

    public function exportBulkSalaryExcelAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $department = $_GET['department'] ?? '';

        $results = $this->salaryModel->calculateBulkSalaries($startDate, $endDate, $department);

        $this->generateBulkSalaryReport($results, 'Toplu Maaş Hesaplama Raporu', $startDate, $endDate, $department, 'excel');
    }

    public function exportBulkSalaryPdfAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $department = $_GET['department'] ?? '';

        $results = $this->salaryModel->calculateBulkSalaries($startDate, $endDate, $department);

        $this->generateBulkSalaryReport($results, 'Toplu Maaş Hesaplama Raporu', $startDate, $endDate, $department, 'pdf');
    }

    private function generateBulkSalaryReport($data, $title, $startDate, $endDate, $department, $format) {
        $filename = 'toplu_maas_raporu_' . date('Y-m-d');
        if ($format === 'excel') {
            $filename .= '.xls';
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        } elseif ($format === 'pdf') {
            $filename .= '.html'; // PDF için HTML çıktısı
            header('Content-Type: text/html; charset=utf-8');
        } else {
            return;
        }
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        ob_start();
        include APP_ROOT . '/views/salary/bulk_report_template.php'; // Toplu rapor şablonu
        $htmlContent = ob_get_clean();

        echo $htmlContent;
        exit;
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