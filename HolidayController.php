<?php
// controllers/HolidayController.php

require_once APP_ROOT . '/models/HolidayModel.php';
require_once APP_ROOT . '/core/Auth.php';

class HolidayController {
    private $holidayModel;

    public function __construct() {
        $this->holidayModel = new HolidayModel();
    }

    // Resmi Tatil Yönetimi sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // Minimum yetki: Kayıt Yetkilisi

        $holidays = Database::fetchAll("SELECT * FROM holidays ORDER BY holiday_date DESC"); // Tüm tatilleri çek

        $this->renderView('holidays/index', [
            'holidays' => $holidays
        ]);
    }

    // AJAX: Yeni tatil ekle
    public function addHolidayAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $holidayDate = $_POST['holiday_date'] ?? '';
        $name = $_POST['name'] ?? '';

        if (empty($holidayDate) || empty($name)) {
            echo json_encode(['success' => false, 'message' => 'Tarih ve tatil adı gereklidir!']);
            return;
        }

        try {
            $existing = $this->holidayModel->find($holidayDate, 'holiday_date');
            if ($existing) {
                echo json_encode(['success' => false, 'message' => 'Bu tarihte zaten bir tatil mevcut!']);
                return;
            }

            $this->holidayModel->create(['holiday_date' => $holidayDate, 'name' => $name, 'is_active' => 1]);
            echo json_encode(['success' => true, 'message' => 'Resmi tatil başarıyla eklendi.']);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Ekleme sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Tatil sil
    public function deleteHolidayAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $holidayId = $_POST['holiday_id'] ?? null;
        if (!$holidayId) {
            echo json_encode(['success' => false, 'message' => 'Tatil ID eksik!']);
            return;
        }

        try {
            $deleted = $this->holidayModel->delete($holidayId);
            if ($deleted) {
                echo json_encode(['success' => true, 'message' => 'Resmi tatil başarıyla silindi.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Silinecek tatil bulunamadı!']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Silme sırasında hata: ' . $e->getMessage()]);
        }
    }

    // AJAX: Yaygın tatilleri ekle
    public function addCommonHolidaysAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        try {
            $result = $this->holidayModel->addCommonHolidays();
            echo json_encode($result);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Yaygın tatiller eklenirken hata: ' . $e->getMessage()]);
        }
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