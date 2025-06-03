<?php
// controllers/AttendanceController.php

require_once APP_ROOT . '/models/AttendanceModel.php';
require_once APP_ROOT . '/models/CardModel.php'; // Departman listesi için
require_once APP_ROOT . '/core/Auth.php';

class AttendanceController {
    private $attendanceModel;
    private $cardModel;

    public function __construct() {
        $this->attendanceModel = new AttendanceModel();
        $this->cardModel = new CardModel();
    }

    // Giriş-Çıkış Kayıtları sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // En az Kayıt Yetkilisi

        $departments = $this->cardModel->getDepartments();
        $logs = $this->attendanceModel->getAttendanceLogs(); // İlk yüklemede tüm logları çek

        $this->renderView('attendance/index', [
            'departments' => $departments, // Eğer filtreler için departmanlara ihtiyaç olursa
            'logs' => $logs // İlk tablo verisi
        ]);
    }

    // AJAX: Giriş-Çıkış loglarını filtreleyerek getir
    public function getAttendanceLogsAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? '';
        $type = $_GET['type'] ?? '';

        echo $this->attendanceModel->getAttendanceLogs($search, $date, $type);
    }

    // AJAX: Giriş-Çıkış istatistiklerini getir
    public function getAttendanceStatsAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $entries = $this->attendanceModel->getTodayEntriesCount();
        $exits = $this->attendanceModel->getTodayExitsCount();
        $inside = $this->attendanceModel->getCurrentlyInsideCount();

        echo json_encode([
            'entries' => $entries,
            'exits' => $exits,
            'inside' => $inside
        ]);
    }

    // AJAX: Giriş-Çıkış raporunu dışa aktar (Excel, PDF, CSV)
    public function exportAttendanceAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? '';
        $type = $_GET['type'] ?? '';
        $format = $_GET['format'] ?? 'excel';

        $data = $this->attendanceModel->getAttendanceDataForExport($search, $date, $type);

        $dateText = !empty($date) ? date('d.m.Y', strtotime($date)) : 'Tüm Tarihler';
        $typeText = '';
        if (!empty($type)) {
            $typeText = $type == 'ENTRY' ? 'Giriş Kayıtları' : 'Çıkış Kayıtları';
        } else {
            $typeText = 'Giriş-Çıkış Kayıtları';
        }
        $reportTitle = "PDKS {$typeText} - {$dateText}";

        switch ($format) {
            case 'pdf':
                $this->generatePdf($data, $reportTitle);
                break;
            case 'csv':
                $this->generateCsv($data);
                break;
            case 'excel':
            default:
                $this->generateExcel($data, $reportTitle);
                break;
        }
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

    // Yardımcı export fonksiyonları (şimdilik basit HTML tabanlı, daha sonra kütüphanelerle geliştirilebilir)
    private function generateExcel($data, $title) {
        $filename = 'giris_cikis_raporu_' . date('Y-m-d_H-i-s') . '.xls';
        header('Content-Type: application/vnd.ms-excel; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title>';
        echo '<style>table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #000; padding: 5px; } th { background-color: #4CAF50; color: white; } .entry { color: green; } .exit { color: red; }</style></head><body>';
        echo '<h1>' . $title . '</h1>';
        echo '<table><thead><tr><th>ID</th><th>Kart Numarası</th><th>Ad</th><th>Soyad</th><th>İşlem Tipi</th><th>Tarih/Saat</th><th>Cihaz</th></tr></thead><tbody>';
        foreach ($data as $row) {
            $eventTypeText = ($row['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
            $colorClass = ($row['event_type'] == 'ENTRY') ? 'entry' : 'exit';
            echo '<tr><td>' . sanitize_html($row['id']) . '</td><td>' . sanitize_html($row['card_number']) . '</td><td>' . sanitize_html($row['name']) . '</td><td>' . sanitize_html($row['surname']) . '</td><td class="' . $colorClass . '">' . $eventTypeText . '</td><td>' . date('d.m.Y H:i:s', strtotime($row['event_time'])) . '</td><td>Cihaz #' . sanitize_html($row['device_id']) . '</td></tr>';
        }
        echo '</tbody></table><div style="text-align: center; margin-top: 20px; font-size: 12px;">PDKS Raporu - Oluşturulma Tarihi: ' . date('d.m.Y H:i:s') . '</div></body></html>';
    }

    private function generateCsv($data) {
        $filename = 'giris_cikis_raporu_' . date('Y-m-d_H-i-s') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        fputcsv($output, ['ID', 'Kart Numarası', 'Ad', 'Soyad', 'İşlem Tipi', 'Tarih/Saat', 'Cihaz']);
        foreach ($data as $row) {
            $eventType = ($row['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
            fputcsv($output, [
                sanitize_html($row['id']),
                sanitize_html($row['card_number']),
                sanitize_html($row['name']),
                sanitize_html($row['surname']),
                $eventType,
                date('d.m.Y H:i:s', strtotime($row['event_time'])),
                'Cihaz #' . sanitize_html($row['device_id'])
            ]);
        }
        fclose($output);
    }

    private function generatePdf($data, $title) {
        $filename = 'giris_cikis_raporu_' . date('Y-m-d_H-i-s') . '.html'; // HTML olarak indirilebilir PDF (Tarayıcıdan yazdırılabilir)
        header('Content-Type: text/html; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>' . $title . '</title>';
        echo '<style>body { font-family: Arial, sans-serif; } table { border-collapse: collapse; width: 100%; margin-top: 20px; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #4CAF50; color: white; } tr:nth-child(even) { background-color: #f2f2f2; } .entry { color: green; font-weight: bold; } .exit { color: red; font-weight: bold; } h1 { text-align: center; color: #333; } @media print { .no-print { display: none; } }</style></head><body>';
        echo '<div class="no-print" style="text-align: center; margin: 20px;"><button onclick="window.print()">Yazdır</button> <button onclick="window.close()">Kapat</button></div>';
        echo '<h1>' . $title . '</h1>';
        echo '<table><thead><tr><th>ID</th><th>Kart Numarası</th><th>Ad</th><th>Soyad</th><th>İşlem Tipi</th><th>Tarih/Saat</th><th>Cihaz</th></tr></thead><tbody>';
        foreach ($data as $row) {
            $eventTypeText = ($row['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
            $colorClass = ($row['event_type'] == 'ENTRY') ? 'entry' : 'exit';
            echo '<tr><td>' . sanitize_html($row['id']) . '</td><td>' . sanitize_html($row['card_number']) . '</td><td>' . sanitize_html($row['name']) . '</td><td>' . sanitize_html($row['surname']) . '</td><td class="' . $colorClass . '">' . $eventTypeText . '</td><td>' . date('d.m.Y H:i:s', strtotime($row['event_time'])) . '</td><td>Cihaz #' . sanitize_html($row['device_id']) . '</td></tr>';
        }
        echo '</tbody></table><div style="text-align: center; margin-top: 20px; font-size: 12px;">PDKS Raporu - Oluşturulma Tarihi: ' . date('d.m.Y H:i:s') . '</div></body></html>';
    }
}
?>