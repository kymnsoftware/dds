<?php
// views/cards/user_details_partial.php
// $user, $attendance, $privilegeText değişkenleri CardController tarafından extract edilmiştir.
?>

<div class="row" style="max-height: 70vh; overflow-y: auto;">
    <div class="col-md-4 text-center">
        <img src="<?php echo sanitize_html($photoPath); ?>" class="user-photo mb-3" alt="Profil Fotoğrafı">
        <h4><?php echo sanitize_html($user['name']) . ' ' . sanitize_html($user['surname']); ?></h4>
        <p class="text-muted"><?php echo sanitize_html($user['department']) . ' - ' . sanitize_html($user['position']); ?></p>
        <?php if ($user['enabled'] == 'true'): ?>
            <span class="badge badge-success p-2 mb-3">Aktif</span>
        <?php else: ?>
            <span class="badge badge-danger p-2 mb-3">Pasif</span>
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <ul class="nav nav-tabs" id="userDetailTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab">Kişisel Bilgiler</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="card-tab" data-toggle="tab" href="#card" role="tab">Kart Bilgileri</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="attendance-tab" data-toggle="tab" href="#attendance" role="tab">Giriş-Çıkış Kayıtları</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="salary-tab" data-toggle="tab" href="#salary" role="tab">Maaş Bilgileri</a>
            </li>
        </ul>
        <div class="tab-content" id="userDetailTabsContent">
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <div class="mt-3">
                    <table class="table table-hover">
                        <tr><th width="30%">Telefon:</th><td><?php echo !empty($user['phone']) ? sanitize_html($user['phone']) : '-'; ?></td></tr>
                        <tr><th>E-posta:</th><td><?php echo !empty($user['email']) ? sanitize_html($user['email']) : '-'; ?></td></tr>
                        <tr><th>İşe Giriş Tarihi:</th><td><?php echo !empty($user['hire_date']) && $user['hire_date'] != '0000-00-00' ? date('d.m.Y', strtotime($user['hire_date'])) : '-'; ?></td></tr>
                        <tr><th>Doğum Tarihi:</th><td><?php echo !empty($user['birth_date']) && $user['birth_date'] != '0000-00-00' ? date('d.m.Y', strtotime($user['birth_date'])) : '-'; ?></td></tr>
                        <tr><th>Adres:</th><td><?php echo !empty($user['address']) ? nl2br(sanitize_html($user['address'])) : '-'; ?></td></tr>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="card" role="tabpanel">
                <div class="mt-3">
                    <table class="table table-hover">
                        <tr><th width="30%">Kullanıcı ID:</th><td><?php echo sanitize_html($user['user_id']); ?></td></tr>
                        <tr><th>Kart Numarası:</th><td><span class="badge badge-info p-2"><?php echo sanitize_html($user['card_number']); ?></span></td></tr>
                        <tr><th>Yetki Seviyesi:</th><td><?php echo sanitize_html($privilegeText); ?></td></tr>
                        <tr><th>Kayıt Tarihi:</th><td><?php echo date('d.m.Y H:i:s', strtotime($user['created_at'])); ?></td></tr>
                        <tr><th>Cihaz Senkronizasyonu:</th><td><?php echo $user['synced_to_device'] == 1 ? '<span class="badge badge-success">Senkronize</span>' : '<span class="badge badge-warning">Senkronize Değil</span>'; ?></td></tr>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade" id="attendance" role="tabpanel">
                <div class="mt-3">
                    <?php if (count($attendance) > 0): ?>
                        <table class="table table-striped table-sm">
                            <thead><tr><th>Tarih/Saat</th><th>İşlem</th><th>Cihaz</th></tr></thead>
                            <tbody>
                                <?php foreach ($attendance as $record): ?>
                                    <?php
                                    $eventTypeText = ($record['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
                                    $eventTypeClass = ($record['event_type'] == 'ENTRY') ? 'success' : 'danger';
                                    ?>
                                    <tr>
                                        <td><?php echo date('d.m.Y H:i:s', strtotime($record['event_time'])); ?></td>
                                        <td><span class="badge badge-<?php echo $eventTypeClass; ?>"><?php echo $eventTypeText; ?></span></td>
                                        <td>Cihaz #<?php echo sanitize_html($record['device_id']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="alert alert-info">Bu kullanıcıya ait giriş-çıkış kaydı bulunamadı.</div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="tab-pane fade" id="salary" role="tabpanel">
                <div class="mt-3">
                    <table class="table table-hover">
                        <tr><th>Maaş Türü:</th><td>
                            <?php
                            $salaryType = $user['salary_type'] ?? 'fixed';
                            echo $salaryType === 'fixed' ? '<span class="badge badge-primary">Sabit Maaş</span>' : '<span class="badge badge-warning">Saatlik Ücret</span>';
                            ?>
                        </td></tr>
                        <?php if ($salaryType === 'fixed'): ?>
                        <tr><th>Aylık Sabit Maaş:</th><td><?php echo number_format($user['fixed_salary'], 2, ',', '.') . ' ₺'; ?></td></tr>
                        <?php else: ?>
                        <tr><th>Saatlik Ücret:</th><td><?php echo number_format($user['hourly_rate'], 2, ',', '.') . ' ₺/saat'; ?></td></tr>
                        <?php endif; ?>
                        <tr><th>Günlük Çalışma Saati:</th><td><?php echo $user['daily_work_hours']; ?> saat</td></tr>
                        <tr><th>Fazla Mesai Çarpanı:</th><td><?php echo $user['overtime_rate']; ?>x</td></tr>
                        <tr><th>Aylık Çalışma Günü:</th><td><?php echo $user['monthly_work_days']; ?> gün</td></tr>
                    </table>
                </div>
            </div>
        </div></div></div>```

#### 7. `ajax/api.php` (Tüm AJAX istekleri için merkezi giriş noktası)

Bu dosya, gelen `action` parametresine göre ilgili kontrolcüyü ve metodunu çağıracak.

```php
<?php
// ajax/api.php

require_once __DIR__ . '/../config/app.php'; // Uygulama ayarlarını ve oturumu başlat
require_once APP_ROOT . '/core/Auth.php';    // Auth sınıfını dahil et

// Gerekli tüm kontrolcüleri ve modelleri dahil et
require_once APP_ROOT . '/models/AttendanceModel.php'; // İhtiyaç duyulanlar eklenecek
require_once APP_ROOT . '/models/CardLogModel.php';
require_once APP_ROOT . '/models/HolidayModel.php';
require_once APP_ROOT . '/models/LeaveModel.php';
require_once APP_ROOT . '/models/SalaryModel.php';
require_once APP_ROOT . '/models/SettingModel.php';

require_once APP_ROOT . '/controllers/CardController.php';
require_once APP_ROOT . '/controllers/DashboardController.php';
// Diğer kontrolcüler de buraya eklenecek:
// require_once APP_ROOT . '/controllers/AttendanceController.php';
// require_once APP_ROOT . '/controllers/AbsenceController.php';
// require_once APP_ROOT . '/controllers/LeaveController.php';
// require_once APP_ROOT . '/controllers/SalaryController.php';
// require_once APP_ROOT . '/controllers/SystemController.php';
// require_once APP_ROOT . '/controllers/ReportController.php';
// require_once APP_ROOT . '/controllers/HolidayController.php';


header('Content-Type: application/json'); // Varsayılan olarak JSON çıktı ver

// Sadece POST veya GET isteklerini kabul et
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu!']);
    exit;
}

$action = $_REQUEST['action'] ?? ''; // Hem GET hem POST'tan action'ı al

if (empty($action)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'İşlem belirtilmedi!']);
    exit;
}

try {
    $response = ['success' => false, 'message' => 'Geçersiz işlem veya yetki yok.'];

    switch ($action) {
        // Dashboard İstekleri
        case 'getDashboardData':
            // DashboardController'ı çağırmak için DashboardController'ı oluşturmanız gerekecek.
            // Şimdilik CardModel'deki bazı verileri direkt çekebiliriz.
            $cardModel = new CardModel();
            $totalPersonnel = $cardModel->getTotalPersonnelCount();
            
            // Mevcut index.php'deki AttendanceModel ve CardLogModel kullanımlarını simüle et
            // Bu modeller oluşturulduğunda burası düzenlenecek
            $attendanceModel = new AttendanceModel();
            $todayEntries = $attendanceModel->getTodayEntriesCount();
            $todayExits = $attendanceModel->getTodayExitsCount();
            $currentlyInside = $attendanceModel->getCurrentlyInsideCount();
            $recentActivities = $attendanceModel->getRecentActivitiesHtml(10);

            $cardLogModel = new CardLogModel();
            $recentScans = $cardLogModel->getRecentScansHtml(10);
            
            $response = [
                'success' => true,
                'total_personnel' => $totalPersonnel,
                'today_entries' => $todayEntries,
                'today_exits' => $todayExits,
                'currently_inside' => $currentlyInside,
                'recent_activities' => $recentActivities,
                'recent_scans' => $recentScans
            ];
            break;

        // Kart Yönetimi İstekleri (CardController'dan)
        case 'getCards':
            $controller = new CardController();
            $controller->getCardsAjax(); // Bu metod direkt HTML çıktısı veriyor
            exit; // HTML çıktısı verdiği için JSON encode etmeyiz
        case 'getUserDetailsHtml':
            $controller = new CardController();
            $controller->getUserDetailsHtmlAjax(); // Bu metod direkt HTML çıktısı veriyor
            exit;
        case 'getUserData':
            $controller = new CardController();
            $controller->getUserDataAjax(); // Bu metod JSON çıktısı veriyor
            exit;
        case 'getNextUserId':
            $controller = new CardController();
            $controller->getNextUserIdAjax();
            exit;
        case 'saveCard':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new CardController();
                $controller->saveCardAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'updateUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new CardController();
                $controller->updateUserAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteUser':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new CardController();
                $controller->deleteUserAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteDeviceOnly':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new CardController();
                $controller->deleteDeviceOnlyAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteAllUsers':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new CardController();
                $controller->deleteAllUsersAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'getUsersList':
             $cardModel = new CardModel();
             $users = $cardModel->getUsersList();
             $response = ['success' => true, 'users' => $users];
             break;
        case 'getLastCard': // get_last_card.php'nin yerini alacak
             $cardLogModel = new CardLogModel(); // CardLogModel oluşturulacak
             $lastCard = $cardLogModel->getLastScannedCard();
             if ($lastCard) {
                 $response = ['success' => true, 'card_number' => $lastCard['card_number']];
             } else {
                 $response = ['success' => false, 'message' => 'Kart bulunamadı'];
             }
             break;

        // Diğer AJAX istekleri buraya eklenecek
        // case 'getAttendanceLogs': (AttendanceController)
        // case 'getAttendanceStats': (AttendanceController)
        // ... vb.
        
        default:
            http_response_code(400); // Bad Request
            $response = ['success' => false, 'message' => 'Geçersiz işlem: ' . $action];
            break;
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    error_log("API hatası: " . $e->getMessage() . " - Action: " . $action);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası: ' . ($e->getMessage() . (DEBUG_MODE ? " Trace: " . $e->getTraceAsString() : ''))]);
}
?>