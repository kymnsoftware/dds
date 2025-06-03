<?php
// ajax/api.php (En son güncellenmiş hali)

require_once __DIR__ . '/../config/app.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' && $_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek metodu!']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

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
            $controller = new DashboardController();
            $controller->getDashboardDataAjax();
            exit;

        // Kart Yönetimi İstekleri (CardController'dan)
        case 'getCards':
            $controller = new CardController();
            $controller->getCardsAjax();
            exit;
        case 'getUserDetailsHtml':
            $controller = new CardController();
            $controller->getUserDetailsHtmlAjax();
            exit;
        case 'getUserData':
            $controller = new CardController();
            $controller->getUserDataAjax();
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
        case 'getLastCard':
             $cardLogModel = new CardLogModel();
             $lastCard = $cardLogModel->getLastScannedCard();
             if ($lastCard) {
                 $response = ['success' => true, 'card_number' => $lastCard['card_number']];
             } else {
                 $response = ['success' => false, 'message' => 'Kart bulunamadı'];
             }
             break;

        // Giriş-Çıkış Kayıtları İstekleri (AttendanceController'dan)
        case 'getAttendanceLogs':
            $controller = new AttendanceController();
            $controller->getAttendanceLogsAjax();
            exit;
        case 'getAttendanceStats':
            $controller = new AttendanceController();
            $controller->getAttendanceStatsAjax();
            exit;
        case 'exportAttendance':
            $controller = new AttendanceController();
            $controller->exportAttendanceAjax();
            exit;

        // Devamsızlık İstekleri (AbsenceController'dan)
        case 'getTodayAbsences':
            $controller = new AbsenceController();
            $controller->getTodayAbsencesAjax();
            exit;
        case 'getAbsenceStats':
            $controller = new AbsenceController();
            $controller->getAbsenceStatsAjax();
            exit;
        case 'addAbsence':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new AbsenceController();
                $controller->addAbsenceAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'autoScanAbsence':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new AbsenceController();
                $controller->autoScanAbsenceAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'getAbsenceHistory':
            $controller = new AbsenceController();
            $controller->getAbsenceHistoryAjax();
            exit;
        case 'getAbsenceDetail':
            $controller = new AbsenceController();
            $controller->getAbsenceDetailAjax();
            exit;
        case 'updateAbsence':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new AbsenceController();
                $controller->updateAbsenceAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteAbsence':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new AbsenceController();
                $controller->deleteAbsenceAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        
        // İzin Yönetimi İstekleri (LeaveController'dan)
        case 'getPendingLeaves':
            $controller = new LeaveController();
            $controller->getPendingLeavesAjax();
            exit;
        case 'getProcessedLeaves':
            $controller = new LeaveController();
            $controller->getProcessedLeavesAjax();
            exit;
        case 'getLeaveBalances':
            $controller = new LeaveController();
            $controller->getLeaveBalancesAjax();
            exit;
        case 'getLeaveDetail':
            $controller = new LeaveController();
            $controller->getLeaveDetailAjax();
            exit;
        case 'updateLeaveRequestStatus':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->updateLeaveRequestStatusAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'updateLeaveBalance':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->updateLeaveBalanceAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'getCalendarEvents':
            $controller = new LeaveController();
            $controller->getCalendarEventsAjax();
            exit;
        case 'submitLeaveRequest':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->submitLeaveRequestAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'addDepartmentManager':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->addDepartmentManagerAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteDepartmentManager':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->deleteDepartmentManagerAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'toggleApprovalPermission':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new LeaveController();
                $controller->toggleApprovalPermissionAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'addLeaveType':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once APP_ROOT . '/models/LeaveTypeModel.php';
                $leaveTypeModel = new LeaveTypeModel();
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'max_days' => $_POST['max_days'] ?? null,
                    'color' => $_POST['color'] ?? '#3498db',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                $id = $leaveTypeModel->create($data);
                if ($id) {
                    $response = ['success' => true, 'message' => 'İzin türü başarıyla eklendi.'];
                } else {
                    $response = ['success' => false, 'message' => 'İzin türü eklenirken hata oluştu.'];
                }
                echo json_encode($response);
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'updateLeaveType':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once APP_ROOT . '/models/LeaveTypeModel.php';
                $leaveTypeModel = new LeaveTypeModel();
                $id = $_POST['id'] ?? null;
                $data = [
                    'name' => $_POST['name'] ?? '',
                    'description' => $_POST['description'] ?? '',
                    'max_days' => $_POST['max_days'] ?? null,
                    'color' => $_POST['color'] ?? '#3498db',
                    'is_active' => isset($_POST['is_active']) ? 1 : 0
                ];
                if ($id && $leaveTypeModel->update($id, $data)) {
                    $response = ['success' => true, 'message' => 'İzin türü başarıyla güncellendi.'];
                } else {
                    $response = ['success' => false, 'message' => 'İzin türü güncellenirken hata oluştu.'];
                }
                echo json_encode($response);
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteLeaveType':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require_once APP_ROOT . '/models/LeaveTypeModel.php';
                $leaveTypeModel = new LeaveTypeModel();
                $id = $_POST['id'] ?? null;
                if ($id && $leaveTypeModel->delete($id)) {
                    $response = ['success' => true, 'message' => 'İzin türü başarıyla silindi.'];
                } else {
                    $response = ['success' => false, 'message' => 'İzin türü silinirken hata oluştu veya bulunamadı.'];
                }
                echo json_encode($response);
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;

        // Maaş Yönetimi İstekleri (SalaryController'dan)
        case 'calculateSalary':
            $controller = new SalaryController();
            $controller->calculateSalaryAjax();
            exit;
        case 'getSalaryStats':
            $controller = new SalaryController();
            $controller->getSalaryStatsAjax();
            exit;
        case 'getSalarySettingsSummary':
            $controller = new SalaryController();
            $controller->getSalarySettingsSummaryAjax();
            exit;
        case 'getSalaryOverview':
            $controller = new SalaryController();
            $controller->getSalaryOverviewAjax();
            exit;
        case 'updateEmployeeSalary':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new SalaryController();
                $controller->updateEmployeeSalaryAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'exportSalaryPDF':
            $controller = new SalaryController();
            $controller->exportSalaryPdfAjax();
            exit;
        case 'exportSalaryExcel':
            $controller = new SalaryController();
            $controller->exportSalaryExcelAjax();
            exit;
        case 'bulkSalaryCalculation':
            $controller = new SalaryController();
            $controller->bulkSalaryCalculationAjax();
            exit;
        case 'exportBulkSalaryExcel':
            $controller = new SalaryController();
            $controller->exportBulkSalaryExcelAjax();
            exit;
        case 'exportBulkSalaryPDF':
            $controller = new SalaryController();
            $controller->exportBulkSalaryPdfAjax();
            exit;

        // Resmi Tatil Yönetimi İstekleri (HolidayController'dan)
        case 'addHoliday':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new HolidayController();
                $controller->addHolidayAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'deleteHoliday':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new HolidayController();
                $controller->deleteHolidayAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;
        case 'addCommonHolidays':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new HolidayController();
                $controller->addCommonHolidaysAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;

        // Kart Logları İstekleri (LogController'dan)
        case 'getCardLogs':
            $controller = new LogController();
            $controller->getCardLogsAjax();
            exit;

        // Sistem Ayarları İstekleri (SystemController'dan)
        case 'backupDatabase':
            $controller = new SystemController();
            $controller->backupDatabaseAjax();
            exit;
        case 'triggerSync':
            $controller = new SystemController();
            $controller->triggerSyncAjax();
            exit;
        case 'getSystemSettings':
            $controller = new SystemController();
            $controller->getSystemSettingsAjax();
            exit;
        case 'saveSystemSettings':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller = new SystemController();
                $controller->saveSystemSettingsAjax();
                exit;
            } else {
                $response = ['success' => false, 'message' => 'Geçersiz istek metodu. POST bekleniyor.'];
            }
            break;

        // Raporlar İstekleri (ReportController'dan)
        case 'generateReport':
            $controller = new ReportController();
            $controller->generateReportAjax();
            exit;
        // Exportlar ReportController içindeki generateReportAjax tarafından yönetiliyor
        // case 'exportLeaveReport':
        // case 'exportAttendance': (AttendanceController'dan taşındı)
        // ... diğer rapor exportları


        default:
            http_response_code(400);
            $response = ['success' => false, 'message' => 'Geçersiz işlem: ' . $action];
            break;
    }

    echo json_encode($response);

} catch (Exception $e) {
    http_response_code(500);
    error_log("API hatası: " . $e->getMessage() . " - Action: " . $action);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası: ' . ($e->getMessage() . (DEBUG_MODE ? " Trace: " . $e->getTraceAsString() : ''))]);
}