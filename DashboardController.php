<?php
// controllers/DashboardController.php

require_once APP_ROOT . '/models/CardModel.php';
require_once APP_ROOT . '/models/AttendanceModel.php';
require_once APP_ROOT . '/models/CardLogModel.php';
require_once APP_ROOT . '/core/Auth.php';

class DashboardController {
    private $cardModel;
    private $attendanceModel;
    private $cardLogModel;

    public function __construct() {
        $this->cardModel = new CardModel();
        $this->attendanceModel = new AttendanceModel();
        $this->cardLogModel = new CardLogModel();
    }

    // Dashboard sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // En az Kayıt Yetkilisi

        // İlk yüklemede istatistikleri ve aktiviteyi çek
        $totalPersonnel = $this->cardModel->getTotalPersonnelCount();
        $todayEntries = $this->attendanceModel->getTodayEntriesCount();
        $todayExits = $this->attendanceModel->getTodayExitsCount();
        $currentlyInside = $this->attendanceModel->getCurrentlyInsideCount();
        $recentActivities = $this->attendanceModel->getRecentActivitiesHtml(10);
        $recentScans = $this->cardLogModel->getRecentScansHtml(10);

        $this->renderView('dashboard/index', [
            'totalPersonnel' => $totalPersonnel,
            'todayEntries' => $todayEntries,
            'todayExits' => $todayExits,
            'currentlyInside' => $currentlyInside,
            'recentActivities' => $recentActivities,
            'recentScans' => $recentScans
        ]);
    }

    // AJAX: Dashboard verilerini getir
    public function getDashboardDataAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $totalPersonnel = $this->cardModel->getTotalPersonnelCount();
        $todayEntries = $this->attendanceModel->getTodayEntriesCount();
        $todayExits = $this->attendanceModel->getTodayExitsCount();
        $currentlyInside = $this->attendanceModel->getCurrentlyInsideCount();
        $recentActivities = $this->attendanceModel->getRecentActivitiesHtml(10);
        $recentScans = $this->cardLogModel->getRecentScansHtml(10);

        echo json_encode([
            'success' => true,
            'total_personnel' => $totalPersonnel,
            'today_entries' => $todayEntries,
            'today_exits' => $todayExits,
            'currently_inside' => $currentlyInside,
            'recent_activities' => $recentActivities,
            'recent_scans' => $recentScans
        ]);
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