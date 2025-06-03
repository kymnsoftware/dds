<?php
// controllers/CardController.php

require_once APP_ROOT . '/models/CardModel.php';
require_once APP_ROOT . '/models/AttendanceModel.php'; // Gerekli olabilir
require_once APP_ROOT . '/models/CardLogModel.php'; // Gerekli olabilir
require_once APP_ROOT . '/core/Auth.php';

class CardController {
    private $cardModel;
    private $attendanceModel;
    private $cardLogModel;

    public function __construct() {
        $this->cardModel = new CardModel();
        $this->attendanceModel = new AttendanceModel(); // Henüz oluşturulmadı, sonra eklenecek
        $this->cardLogModel = new CardLogModel(); // Henüz oluşturulmadı, sonra eklenecek
    }

    // Kartlar sayfasını render et (eski index.php'deki cards section'ın yerini alacak)
    public function index() {
        Auth::redirectIfUnauthorized(1); // En az Kayıt Yetkilisi

        // Dropdown'lar için departmanları ve kullanıcıları çek
        $departments = $this->cardModel->getDepartments();
        $users = $this->cardModel->getUsersList(); // Tüm kullanıcıları al

        // view dosyasını include et (bu kısım daha sonra views/cards/index.php olacak)
        // Şimdilik, sadece ilgili HTML kısmını burada gösterebiliriz.
        // Amaç, tüm HTML'i view katmanına taşımak.
        $this->renderView('cards/index', [
            'departments' => $departments,
            'users' => $users // Eğer modal içinde kullanıcı listesi gerekiyorsa
        ]);
    }

    // AJAX: Kart listesini getir
    public function getCardsAjax() {
        Auth::redirectIfNotLoggedIn(); // Oturum kontrolü
        Auth::redirectIfUnauthorized(1); // Yetki kontrolü

        $search = $_GET['search'] ?? '';
        $department = $_GET['department'] ?? '';
        
        $cards = $this->cardModel->getCards($search, $department);

        $html = "";
        if (count($cards) > 0) {
            foreach($cards as $card) {
                $photoPath = !empty($card['photo_path']) ? $card['photo_path'] : '/public/uploads/default-user.png';
                $fullName = sanitize_html($card['name']) . ' ' . sanitize_html($card['surname']);
                
                // Yetki seviyesi metni ve sınıfı
                $privilegeText = '';
                $privilegeClass = '';
                switch($card['privilege']) {
                    case '0': $privilegeText = 'Normal'; $privilegeClass = 'secondary'; break;
                    case '1': $privilegeText = 'Kayıt Yetkilisi'; $privilegeClass = 'info'; break;
                    case '2': $privilegeText = 'Yönetici'; $privilegeClass = 'warning'; break;
                    case '3': $privilegeText = 'Süper Admin'; $privilegeClass = 'danger'; break;
                    default: $privilegeText = 'Bilinmiyor'; $privilegeClass = 'secondary';
                }

                $html .= "<tr>";
                $html .= "<td><span class='badge badge-info'>".sanitize_html($card['user_id'])."</span></td>";
                $html .= "<td><img src='".sanitize_html($photoPath)."' class='user-photo-small' alt='Profil'></td>";
                $html .= "<td><strong>{$fullName}</strong></td>";
                $html .= "<td>".(!empty($card['department']) ? sanitize_html($card['department']) : '<span class="text-muted">-</span>')."</td>";
                $html .= "<td><span class='badge badge-primary'>".sanitize_html($card['card_number'])."</span></td>";
                $html .= "<td><span class='badge badge-".$privilegeClass."'>".$privilegeText."</span></td>";
                $html .= "<td>".($card['enabled'] == 'true' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Pasif</span>')."</td>";
                $html .= "<td>
                    <div class='btn-group btn-group-sm'>
                        <button class='btn btn-outline-info view-details' data-user-id='".sanitize_html($card['user_id'])."' title='Detay'><i class='fas fa-eye'></i></button>
                        <button class='btn btn-outline-primary edit-user' data-user-id='".sanitize_html($card['user_id'])."' title='Düzenle'><i class='fas fa-edit'></i></button>
                        <button class='btn btn-outline-danger delete-user' data-user-id='".sanitize_html($card['user_id'])."' title='Sil'><i class='fas fa-trash'></i></button>
                    </div>
                  </td>";
                $html .= "</tr>";
            }
        } else {
            $html .= "<tr><td colspan='8' class='text-center'>Kayıt bulunamadı</td></tr>";
        }
        echo $html;
    }

    // AJAX: Kullanıcı detaylarını getir (modal için)
    public function getUserDetailsHtmlAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            echo "<div class='alert alert-warning'>Kullanıcı ID eksik!</div>";
            return;
        }

        $user = $this->cardModel->find($userId);
        if (!$user) {
            echo "<div class='alert alert-warning'>Kullanıcı bulunamadı!</div>";
            return;
        }

        $photoPath = !empty($user['photo_path']) ? sanitize_html($user['photo_path']) : '/public/uploads/default-user.png';

        // Son 10 giriş-çıkış kaydını al (AttendanceModel kullanılacak)
        // Şimdilik yer tutucu, AttendanceModel oluşturulunca eklenecek
        $attendance = []; // $this->attendanceModel->getRecentUserAttendance($user['card_number'], 10);

        // Yetki seviyesi metni
        $privilegeText = '';
        switch($user['privilege']) {
            case '0': $privilegeText = 'Normal Kullanıcı'; break;
            case '1': $privilegeText = 'Kayıt Yetkilisi'; break;
            case '2': $privilegeText = 'Yönetici'; break;
            case '3': $privilegeText = 'Süper Admin'; break;
            default: $privilegeText = 'Bilinmiyor';
        }

        ob_start(); // Çıktıyı tamponlamaya başla
        include APP_ROOT . '/views/cards/user_details_partial.php'; // Kısmi görünüm
        $html = ob_get_clean(); // Tamponlanan çıktıyı al
        echo $html;
    }

    // AJAX: Kullanıcı verilerini getir (düzenleme modalı için JSON olarak)
    public function getUserDataAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $userId = $_GET['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Kullanıcı ID eksik!']);
            return;
        }

        $user = $this->cardModel->find($userId);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Kullanıcı bulunamadı!']);
            return;
        }

        // Varsayılan değerleri ayarla (eğer veritabanında NULL ise)
        $user['salary_type'] = $user['salary_type'] ?: 'fixed';
        if (empty($user['salary_type'])) { // Backward compatibility
            $user['salary_type'] = ($user['fixed_salary'] > 0) ? 'fixed' : 'hourly';
        }
        $user['fixed_salary'] = $user['fixed_salary'] ?? 35000;
        $user['hourly_rate'] = $user['hourly_rate'] ?? 0;
        $user['overtime_rate'] = $user['overtime_rate'] ?? 1.5;
        $user['daily_work_hours'] = $user['daily_work_hours'] ?? 8.0;
        $user['monthly_work_days'] = $user['monthly_work_days'] ?? 22;

        echo json_encode($user);
    }

    // AJAX: Yeni kullanıcı ID'sini getir
    public function getNextUserIdAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);
        echo json_encode(['success' => true, 'next_id' => $this->cardModel->getNextUserId()]);
    }

    // AJAX: Yeni kart kaydet
    public function saveCardAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        // Fotoğraf dosyası
        $photoFile = $_FILES['photo'] ?? null;

        $result = $this->cardModel->saveUser($_POST, $photoFile);
        echo json_encode($result);
    }

    // AJAX: Kullanıcı güncelle
    public function updateUserAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        // Fotoğraf dosyası
        $photoFile = $_FILES['photo'] ?? null;

        // Kullanıcı ID'sini POST verisine ekle
        $_POST['is_update'] = true;
        
        $result = $this->cardModel->saveUser($_POST, $photoFile);
        echo json_encode($result);
    }

    // AJAX: Kullanıcı sil
    public function deleteUserAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(2); // Silme işlemi için daha yüksek yetki gerekebilir

        $userId = $_POST['user_id'] ?? null;
        $deleteType = $_POST['delete_type'] ?? 'db_only';

        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Kullanıcı ID eksik!']);
            return;
        }

        $result = $this->cardModel->deleteUser($userId, $deleteType);
        echo json_encode($result);
    }

    // AJAX: Sadece cihazdan sil
    public function deleteDeviceOnlyAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(2);

        $userId = $_POST['user_id'] ?? null;
        if (!$userId) {
            echo json_encode(['success' => false, 'message' => 'Kullanıcı ID eksik!']);
            return;
        }

        $result = $this->cardModel->deleteUserFromDeviceOnly($userId);
        echo json_encode($result);
    }

    // AJAX: Tüm kullanıcıları sil
    public function deleteAllUsersAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(3); // Süper Admin yetkisi

        $confirmText = $_POST['confirm_text'] ?? '';
        $deleteType = $_POST['delete_all_type'] ?? 'db_only';

        $result = $this->cardModel->deleteAllUsers($confirmText, $deleteType);
        echo json_encode($result);
    }

    // Ortak view render fonksiyonu
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