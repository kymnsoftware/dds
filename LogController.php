<?php
// controllers/LogController.php

require_once APP_ROOT . '/models/CardLogModel.php';
require_once APP_ROOT . '/core/Auth.php';

class LogController {
    private $cardLogModel;

    public function __construct() {
        $this->cardLogModel = new CardLogModel();
    }

    // Kart Logları sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // Minimum yetki: Kayıt Yetkilisi

        $logs = $this->cardLogModel->getCardLogs(); // İlk yüklemede tüm logları çek

        $this->renderView('logs/index', [
            'logs' => $logs
        ]);
    }

    // AJAX: Kart loglarını filtreleyerek getir
    public function getCardLogsAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $search = $_GET['search'] ?? '';
        $date = $_GET['date'] ?? '';

        echo $this->cardLogModel->getCardLogs($search, $date); // getCardLogs zaten HTML döndürüyor
    }

    // HTML görünümünü render eden yardımcı fonksiyon
    private function renderView($viewName, $data = []) {
        extract($data);

        include APP_ROOT . '/views/shared/header.php';
        include APP_ROOT . '/views/' . $viewName . '.php';
        include APP_ROOT . '/views/shared/footer.php';
    }
}
?>