<?php
// controllers/ReportController.php

require_once APP_ROOT . '/models/AttendanceModel.php';
require_once APP_ROOT . '/models/CardModel.php';
require_once APP_ROOT . '/models/AbsenceModel.php';
require_once APP_ROOT . '/models/LeaveModel.php';
require_once APP_ROOT . '/models/SalaryModel.php';
require_once APP_ROOT . '/core/Auth.php';

class ReportController {
    private $attendanceModel;
    private $cardModel;
    private $absenceModel;
    private $leaveModel;
    private $salaryModel;

    public function __construct() {
        $this->attendanceModel = new AttendanceModel();
        $this->cardModel = new CardModel();
        $this->absenceModel = new AbsenceModel();
        $this->leaveModel = new LeaveModel();
        $this->salaryModel = new SalaryModel();
    }

    // Raporlar sayfasını render et
    public function index() {
        Auth::redirectIfUnauthorized(1); // Minimum yetki: Kayıt Yetkilisi

        $departments = $this->cardModel->getDepartments();
        $users = $this->cardModel->getUsersList();
        $absenceTypes = $this->absenceModel->getAbsenceTypes();
        $leaveTypes = $this->leaveModel->getLeaveTypes();

        $this->renderView('reports/index', [
            'departments' => $departments,
            'users' => $users,
            'absenceTypes' => $absenceTypes,
            'leaveTypes' => $leaveTypes
        ]);
    }

    // AJAX: Rapor verilerini oluştur ve HTML önizlemesi veya dosya çıktısı ver
    public function generateReportAjax() {
        Auth::redirectIfNotLoggedIn();
        Auth::redirectIfUnauthorized(1);

        $reportType = $_GET['type'] ?? '';
        $format = $_GET['format'] ?? 'html'; // html (preview), excel, pdf, csv
        
        $data = [];
        $title = '';
        $headers = [];
        $template = '';

        switch ($reportType) {
            case 'daily_attendance': // Günlük Giriş-Çıkış Raporu
                $date = $_GET['date'] ?? date('Y-m-d');
                $department = $_GET['department'] ?? '';
                $data = $this->getDailyAttendanceReportData($date, $department);
                $title = 'Günlük Giriş-Çıkış Raporu - ' . date('d.m.Y', strtotime($date));
                $headers = ['ID', 'Ad Soyad', 'Departman', 'Pozisyon', 'İlk Giriş', 'Son Çıkış', 'Toplam Süre'];
                $template = 'reports/daily_attendance_template';
                break;

            case 'monthly_attendance': // Aylık Mesai Raporu
                $month = $_GET['month'] ?? date('Y-m');
                $department = $_GET['department'] ?? '';
                $data = $this->getMonthlyAttendanceReportData($month, $department);
                $title = 'Aylık Mesai Raporu - ' . date('F Y', strtotime($month . '-01'));
                $headers = ['ID', 'Ad Soyad', 'Departman', 'Pozisyon', 'Çalışma Günü', 'Toplam Süre'];
                $template = 'reports/monthly_attendance_template';
                break;

            case 'user_attendance': // Personel Bazlı Giriş-Çıkış Raporu
                $userId = $_GET['user_id'] ?? null;
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-t');
                $data = $this->getUserAttendanceReportData($userId, $startDate, $endDate);
                $userName = $this->cardModel->find($userId)['name'] . ' ' . $this->cardModel->find($userId)['surname'] ?? 'Bilinmeyen Personel';
                $title = 'Personel Giriş-Çıkış Raporu - ' . sanitize_html($userName);
                $headers = ['Tarih', 'İlk Giriş', 'Son Çıkış', 'Çalışma Süresi'];
                $template = 'reports/user_attendance_template';
                break;

            case 'department_attendance': // Departman Bazlı Mesai Raporu
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-t');
                $department = $_GET['department'] ?? ''; // Opsiyonel filtre
                $data = $this->getDepartmentAttendanceReportData($startDate, $endDate, $department);
                $title = 'Departman Mesai Raporu';
                if (!empty($department)) $title .= ' - ' . sanitize_html($department);
                $headers = ['Departman', 'Personel Sayısı', 'Toplam Çalışma Günü', 'Toplam Çalışma Süresi'];
                $template = 'reports/department_attendance_template';
                break;

            case 'daily_absence': // Günlük Devamsızlık Raporu
                $date = $_GET['date'] ?? date('Y-m-d');
                $department = $_GET['department'] ?? '';
                $status = $_GET['status'] ?? '';
                $data = $this->getDailyAbsenceReportData($date, $department, $status);
                $title = 'Günlük Devamsızlık Raporu - ' . date('d.m.Y', strtotime($date));
                $headers = ['Personel', 'Departman', 'Pozisyon', 'Devamsızlık Türü', 'Başlangıç', 'Bitiş', 'Gün', 'Durum', 'Sebep', 'Kaydeden'];
                $template = 'reports/daily_absence_template';
                break;

            case 'monthly_absence': // Aylık Devamsızlık Raporu
                $month = $_GET['month'] ?? date('Y-m');
                $department = $_GET['department'] ?? '';
                $absenceType = $_GET['absence_type'] ?? '';
                $data = $this->getMonthlyAbsenceReportData($month, $department, $absenceType);
                $title = 'Aylık Devamsızlık Raporu - ' . date('F Y', strtotime($month . '-01'));
                $headers = ['Personel', 'Departman', 'Devamsızlık Sayısı', 'Toplam Gün', 'Mazeretli Gün', 'Mazeretsiz Gün', 'Devamsızlık Türleri'];
                $template = 'reports/monthly_absence_template';
                break;

            case 'employee_absence': // Personel Bazlı Devamsızlık Raporu
                $userId = $_GET['user_id'] ?? null;
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-t');
                $data = $this->getEmployeeAbsenceReportData($userId, $startDate, $endDate);
                $userName = $this->cardModel->find($userId)['name'] . ' ' . $this->cardModel->find($userId)['surname'] ?? 'Bilinmeyen Personel';
                $title = 'Personel Devamsızlık Raporu - ' . sanitize_html($userName);
                $headers = ['Başlangıç Tarihi', 'Bitiş Tarihi', 'Gün Sayısı', 'Devamsızlık Türü', 'Durum', 'Sebep', 'Kaydeden'];
                $template = 'reports/employee_absence_template';
                break;

            case 'department_absence': // Departman Bazlı Devamsızlık Raporu
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-t');
                $department = $_GET['department'] ?? '';
                $data = $this->getDepartmentAbsenceReportData($startDate, $endDate, $department);
                $title = 'Departman Bazlı Devamsızlık Raporu';
                if (!empty($department)) $title .= ' - ' . sanitize_html($department);
                $headers = ['Departman', 'Personel Sayısı', 'Toplam Devamsızlık', 'Toplam Gün', 'Mazeretli Gün', 'Mazeretsiz Gün', 'Ortalama Süre'];
                $template = 'reports/department_absence_template';
                break;

            case 'summary_absence': // Özet Devamsızlık Raporu
                $startDate = $_GET['start_date'] ?? date('Y-m-01');
                $endDate = $_GET['end_date'] ?? date('Y-m-t');
                $groupBy = $_GET['group_by'] ?? 'department';
                $data = $this->getSummaryAbsenceReportData($startDate, $endDate, $groupBy);
                $title = 'Özet Devamsızlık Raporu - ' . ucwords($groupBy) . ' Bazlı';
                $headers = ['Grup', 'Devamsızlık Sayısı', 'Toplam Gün', 'Mazeretli', 'Mazeretsiz'];
                $template = 'reports/summary_absence_template';
                break;

            case 'leave_department': // Departman Bazlı İzin Raporu
                $startDate = $_GET['start_date'] ?? date('Y-01-01');
                $endDate = $_GET['end_date'] ?? date('Y-12-31');
                $department = $_GET['department'] ?? '';
                $data = $this->getLeaveDepartmentReportData($startDate, $endDate, $department);
                $title = 'Departman Bazlı İzin Raporu';
                if (!empty($department)) $title .= ' - ' . sanitize_html($department);
                $template = 'reports/leave_department_template'; // Özel şablon gerekecek
                break;

            case 'leave_user': // Personel Bazlı İzin Raporu
                $userId = $_GET['user_id'] ?? null;
                $startDate = $_GET['start_date'] ?? date('Y-01-01');
                $endDate = $_GET['end_date'] ?? date('Y-12-31');
                $data = $this->getLeaveUserReportData($userId, $startDate, $endDate);
                $userName = $this->cardModel->find($userId)['name'] . ' ' . $this->cardModel->find($userId)['surname'] ?? 'Bilinmeyen Personel';
                $title = 'Personel Bazlı İzin Raporu - ' . sanitize_html($userName);
                $template = 'reports/leave_user_template'; // Özel şablon gerekecek
                break;
            
            case 'leave_type': // İzin Türü Bazlı Rapor
                $startDate = $_GET['start_date'] ?? date('Y-01-01');
                $endDate = $_GET['end_date'] ?? date('Y-12-31');
                $department = $_GET['department'] ?? ''; // Opsiyonel filtre
                $data = $this->getLeaveTypeReportData($startDate, $endDate, $department);
                $title = 'İzin Türü Bazlı Rapor';
                if (!empty($department)) $title .= ' - ' . sanitize_html($department);
                $template = 'reports/leave_type_template'; // Özel şablon gerekecek
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Geçersiz rapor türü.']);
                return;
        }

        // Çıktı formatına göre işlem yap
        if ($format === 'html') {
            ob_start();
            include APP_ROOT . '/views/' . $template . '.php'; // HTML şablonunu kullan
            $htmlContent = ob_get_clean();
            echo json_encode(['success' => true, 'html' => $htmlContent]);
        } else {
            // Dosya çıktısı için doğrudan header basıyoruz, bu yüzden exit;
            $this->outputFile($data, $title, $headers, $format, $template, $reportType);
        }
    }

    // --- Rapor Verisi Çekme Metodları ---
    private function getDailyAttendanceReportData($date, $department) {
        $sql = "
            SELECT c.user_id, c.name, c.surname, c.department, c.position,
                   MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END) as first_entry,
                   MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END) as last_exit,
                   TIMEDIFF(
                       MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END),
                       MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END)
                   ) as total_time
            FROM cards c
            LEFT JOIN attendance_logs al ON c.card_number = al.card_number
              AND DATE(al.event_time) = :date
            WHERE c.enabled = 'true'
        ";
        $params = [':date' => $date];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY c.user_id, c.name, c.surname, c.department, c.position
                  HAVING first_entry IS NOT NULL OR last_exit IS NOT NULL
                  ORDER BY c.department, c.name";
        return Database::fetchAll($sql, $params);
    }

    private function getMonthlyAttendanceReportData($month, $department) {
        $monthStart = $month . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $sql = "
            SELECT c.user_id, c.name, c.surname, c.department, c.position,
                   COUNT(DISTINCT DATE(al.event_time)) as work_days,
                   SEC_TO_TIME(SUM(
                       TIMESTAMPDIFF(SECOND,
                           MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END),
                           MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END)
                       )
                   )) as total_time
            FROM cards c
            LEFT JOIN attendance_logs al ON c.card_number = al.card_number
              AND DATE(al.event_time) BETWEEN :month_start AND :month_end
            WHERE c.enabled = 'true'
        ";
        $params = [':month_start' => $monthStart, ':month_end' => $monthEnd];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY c.user_id, c.name, c.surname, c.department, c.position
                  HAVING work_days > 0
                  ORDER BY c.department, c.name";
        return Database::fetchAll($sql, $params);
    }

    private function getUserAttendanceReportData($userId, $startDate, $endDate) {
        $sql = "
            SELECT DATE(al.event_time) as work_date,
                   MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END) as first_entry,
                   MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END) as last_exit,
                   TIMEDIFF(
                       MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END),
                       MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END)
                   ) as daily_time
            FROM cards c
            JOIN attendance_logs al ON c.card_number = al.card_number
            WHERE c.user_id = :user_id
              AND DATE(al.event_time) BETWEEN :start_date AND :end_date
            GROUP BY DATE(al.event_time)
            ORDER BY DATE(al.event_time) DESC
        ";
        return Database::fetchAll($sql, [':user_id' => $userId, ':start_date' => $startDate, ':end_date' => $endDate]);
    }

    private function getDepartmentAttendanceReportData($startDate, $endDate, $department) {
        $sql = "
            SELECT c.department,
                   COUNT(DISTINCT c.user_id) as employee_count,
                   COUNT(DISTINCT DATE(al.event_time)) as work_days,
                   SEC_TO_TIME(SUM(
                       TIMESTAMPDIFF(SECOND,
                           MIN(CASE WHEN al.event_type = 'ENTRY' THEN al.event_time ELSE NULL END),
                           MAX(CASE WHEN al.event_type = 'EXIT' THEN al.event_time ELSE NULL END)
                       )
                   )) as total_time
            FROM cards c
            LEFT JOIN attendance_logs al ON c.card_number = al.card_number
              AND DATE(al.event_time) BETWEEN :start_date AND :end_date
            WHERE c.department IS NOT NULL AND c.department != ''
              AND c.enabled = 'true'
        ";
        $params = [':start_date' => $startDate, ':end_date' => $endDate];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY c.department ORDER BY c.department";
        return Database::fetchAll($sql, $params);
    }
    
    private function getDailyAbsenceReportData($date, $department, $status) {
        $sql = "
            SELECT c.name, c.surname, c.department, c.position,
                   at.name as absence_type_name, a.start_date, a.end_date, a.total_days, a.reason,
                   a.is_justified, creator.name as created_by_name
            FROM absences a
            JOIN cards c ON a.user_id = c.user_id
            JOIN absence_types at ON a.absence_type_id = at.id
            LEFT JOIN cards creator ON a.created_by = creator.user_id
            WHERE :date BETWEEN a.start_date AND a.end_date
        ";
        $params = [':date' => $date];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        if (!empty($status)) {
            $sql .= ($status == 'justified') ? " AND a.is_justified = 1" : " AND a.is_justified = 0";
        }
        $sql .= " ORDER BY c.department, c.name, c.surname";
        return Database::fetchAll($sql, $params);
    }

    private function getMonthlyAbsenceReportData($month, $department, $absenceType) {
        $monthStart = $month . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        $sql = "
            SELECT c.name, c.surname, c.department,
                   COUNT(a.id) as absence_count,
                   SUM(a.total_days) as total_days,
                   SUM(CASE WHEN a.is_justified = 1 THEN a.total_days ELSE 0 END) as justified_days,
                   SUM(CASE WHEN a.is_justified = 0 THEN a.total_days ELSE 0 END) as unjustified_days,
                   GROUP_CONCAT(DISTINCT at.name SEPARATOR ', ') as absence_types_list
            FROM absences a
            JOIN cards c ON a.user_id = c.user_id
            JOIN absence_types at ON a.absence_type_id = at.id
            WHERE a.start_date >= :month_start AND a.end_date <= :month_end
        ";
        $params = [':month_start' => $monthStart, ':month_end' => $monthEnd];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        if (!empty($absenceType)) {
            $sql .= " AND a.absence_type_id = :absence_type";
            $params[':absence_type'] = $absenceType;
        }
        $sql .= " GROUP BY c.user_id ORDER BY c.department, total_days DESC";
        return Database::fetchAll($sql, $params);
    }

    private function getEmployeeAbsenceReportData($userId, $startDate, $endDate) {
        $sql = "
            SELECT a.start_date, a.end_date, a.total_days, a.reason, a.is_justified,
                   at.name as absence_type_name, creator.name as created_by_name
            FROM absences a
            JOIN absence_types at ON a.absence_type_id = at.id
            LEFT JOIN cards creator ON a.created_by = creator.user_id
            WHERE a.user_id = :user_id
            AND a.start_date >= :start_date AND a.end_date <= :end_date
            ORDER BY a.start_date DESC
        ";
        return Database::fetchAll($sql, [':user_id' => $userId, ':start_date' => $startDate, ':end_date' => $endDate]);
    }

    private function getDepartmentAbsenceReportData($startDate, $endDate, $department) {
        $sql = "
            SELECT c.department,
                   COUNT(DISTINCT c.user_id) as employee_count,
                   COUNT(a.id) as total_absences,
                   SUM(a.total_days) as total_absence_days,
                   SUM(CASE WHEN a.is_justified = 1 THEN a.total_days ELSE 0 END) as justified_days,
                   SUM(CASE WHEN a.is_justified = 0 THEN a.total_days ELSE 0 END) as unjustified_days,
                   ROUND(AVG(a.total_days), 2) as avg_absence_duration
            FROM cards c
            LEFT JOIN absences a ON c.user_id = a.user_id 
                AND a.start_date >= :start_date 
                AND a.end_date <= :end_date
            WHERE c.enabled = 'true' AND c.department IS NOT NULL AND c.department != ''
        ";
        $params = [':start_date' => $startDate, ':end_date' => $endDate];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY c.department ORDER BY total_absence_days DESC";
        return Database::fetchAll($sql, $params);
    }

    private function getSummaryAbsenceReportData($startDate, $endDate, $groupBy) {
        $sql = '';
        $titlePrefix = '';
        if ($groupBy == 'department') {
            $sql = "
                SELECT c.department as group_name,
                       COUNT(a.id) as absence_count,
                       SUM(a.total_days) as total_days,
                       SUM(CASE WHEN a.is_justified = 1 THEN 1 ELSE 0 END) as justified_count,
                       SUM(CASE WHEN a.is_justified = 0 THEN 1 ELSE 0 END) as unjustified_count
                FROM absences a
                JOIN cards c ON a.user_id = c.user_id
                WHERE a.start_date >= :start_date AND a.end_date <= :end_date
                GROUP BY c.department
                ORDER BY total_days DESC
            ";
        } elseif ($groupBy == 'absence_type') {
            $sql = "
                SELECT at.name as group_name,
                       COUNT(a.id) as absence_count,
                       SUM(a.total_days) as total_days,
                       SUM(CASE WHEN a.is_justified = 1 THEN 1 ELSE 0 END) as justified_count,
                       SUM(CASE WHEN a.is_justified = 0 THEN 1 ELSE 0 END) as unjustified_count
                FROM absences a
                JOIN absence_types at ON a.absence_type_id = at.id
                WHERE a.start_date >= :start_date AND a.end_date <= :end_date
                GROUP BY at.id
                ORDER BY total_days DESC
            ";
        } else { // monthly
            $sql = "
                SELECT DATE_FORMAT(a.start_date, '%Y-%m') as group_name,
                       COUNT(a.id) as absence_count,
                       SUM(a.total_days) as total_days,
                       SUM(CASE WHEN a.is_justified = 1 THEN 1 ELSE 0 END) as justified_count,
                       SUM(CASE WHEN a.is_justified = 0 THEN 1 ELSE 0 END) as unjustified_count
                FROM absences a
                WHERE a.start_date >= :start_date AND a.end_date <= :end_date
                GROUP BY DATE_FORMAT(a.start_date, '%Y-%m')
                ORDER BY group_name DESC
            ";
        }
        return Database::fetchAll($sql, [':start_date' => $startDate, ':end_date' => $endDate]);
    }

    private function getLeaveDepartmentReportData($startDate, $endDate, $department) {
        // İzin türlerini de çekmeliyiz ki dinamik kolonlar oluşturabilelim
        $leaveTypes = $this->leaveModel->getLeaveTypes();

        $sql = "
            SELECT c.department, lt.id as leave_type_id, lt.name as leave_type_name, lt.color,
                   COUNT(DISTINCT lr.id) as leave_count,
                   SUM(lr.total_days) as total_days
            FROM leave_requests lr
            JOIN cards c ON lr.user_id = c.user_id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = 'approved'
            AND (lr.start_date >= :start_date AND lr.end_date <= :end_date)
        ";
        $params = [':start_date' => $startDate, ':end_date' => $endDate];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY c.department, lt.id
                  ORDER BY c.department, lt.name";
        
        $rawResult = Database::fetchAll($sql, $params);

        // Sonuçları departmanlara göre düzenle
        $departmentsData = [];
        $totalsByType = [];
        $grandTotal = 0;
        
        foreach ($rawResult as $row) {
            if (!isset($departmentsData[$row['department']])) {
                $departmentsData[$row['department']] = [];
            }
            $departmentsData[$row['department']][$row['leave_type_id']] = [
                'name' => $row['leave_type_name'], 'color' => $row['color'],
                'count' => $row['leave_count'], 'days' => $row['total_days']
            ];
            if (!isset($totalsByType[$row['leave_type_id']])) {
                $totalsByType[$row['leave_type_id']] = ['name' => $row['leave_type_name'], 'color' => $row['color'], 'count' => 0, 'days' => 0];
            }
            $totalsByType[$row['leave_type_id']]['count'] += $row['leave_count'];
            $totalsByType[$row['leave_type_id']]['days'] += $row['total_days'];
            $grandTotal += $row['total_days'];
        }

        return [
            'data' => $departmentsData,
            'leave_types' => $leaveTypes, // Tüm izin türleri de template için lazım
            'totals_by_type' => $totalsByType,
            'grand_total' => $grandTotal
        ];
    }

    private function getLeaveUserReportData($userId, $startDate, $endDate) {
        $leaveTypes = $this->leaveModel->getLeaveTypes(); // Tüm izin türleri de lazım

        $sql = "
            SELECT c.user_id, c.name, c.surname, c.department, c.position,
                   lt.id as leave_type_id, lt.name as leave_type_name, lt.color,
                   COUNT(lr.id) as leave_count,
                   SUM(lr.total_days) as total_days
            FROM leave_requests lr
            JOIN cards c ON lr.user_id = c.user_id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = 'approved'
            AND (lr.start_date >= :start_date AND lr.end_date <= :end_date)
        ";
        $params = [':start_date' => $startDate, ':end_date' => $endDate];
        if (!empty($userId)) {
            $sql .= " AND c.user_id = :user_id";
            $params[':user_id'] = $userId;
        }
        $sql .= " GROUP BY c.user_id, lt.id
                  ORDER BY c.name, c.surname, lt.name";
        
        $rawResult = Database::fetchAll($sql, $params);

        $usersData = [];
        $totalsByType = [];
        $grandTotal = 0;
        
        foreach ($rawResult as $row) {
            $currentUserId = $row['user_id'];
            if (!isset($usersData[$currentUserId])) {
                $usersData[$currentUserId] = [
                    'name' => $row['name'] . ' ' . $row['surname'],
                    'department' => $row['department'],
                    'position' => $row['position'],
                    'leaves' => []
                ];
            }
            $usersData[$currentUserId]['leaves'][$row['leave_type_id']] = [
                'name' => $row['leave_type_name'], 'color' => $row['color'],
                'count' => $row['leave_count'], 'days' => $row['total_days']
            ];
            if (!isset($totalsByType[$row['leave_type_id']])) {
                $totalsByType[$row['leave_type_id']] = ['name' => $row['leave_type_name'], 'color' => $row['color'], 'count' => 0, 'days' => 0];
            }
            $totalsByType[$row['leave_type_id']]['count'] += $row['leave_count'];
            $totalsByType[$row['leave_type_id']]['days'] += $row['total_days'];
            $grandTotal += $row['total_days'];
        }

        return [
            'data' => $usersData,
            'leave_types' => $leaveTypes,
            'totals_by_type' => $totalsByType,
            'grand_total' => $grandTotal
        ];
    }

    private function getLeaveTypeReportData($startDate, $endDate, $department) {
        $leaveTypes = $this->leaveModel->getLeaveTypes(); // Tüm izin türleri de lazım
        $allDepartments = $this->cardModel->getDepartments(); // Tüm departmanlar da lazım

        $sql = "
            SELECT lt.id as leave_type_id, lt.name as leave_type_name, lt.color,
                   COUNT(lr.id) as leave_count,
                   SUM(lr.total_days) as total_days,
                   c.department
            FROM leave_requests lr
            JOIN cards c ON lr.user_id = c.user_id
            JOIN leave_types lt ON lr.leave_type_id = lt.id
            WHERE lr.status = 'approved'
            AND (lr.start_date >= :start_date AND lr.end_date <= :end_date)
        ";
        $params = [':start_date' => $startDate, ':end_date' => $endDate];
        if (!empty($department)) {
            $sql .= " AND c.department = :department";
            $params[':department'] = $department;
        }
        $sql .= " GROUP BY lt.id, c.department
                  ORDER BY lt.name, c.department";
        
        $rawResult = Database::fetchAll($sql, $params);

        $leaveTypesData = [];
        $totalsByDept = [];
        $grandTotal = 0;
        $distinctDepartments = []; // Rapor tablosu için departman başlıkları

        foreach ($rawResult as $row) {
            if (!isset($leaveTypesData[$row['leave_type_id']])) {
                $leaveTypesData[$row['leave_type_id']] = [
                    'name' => $row['leave_type_name'],
                    'color' => $row['color'],
                    'departments' => []
                ];
            }
            $leaveTypesData[$row['leave_type_id']]['departments'][$row['department']] = [
                'count' => $row['leave_count'], 'days' => $row['total_days']
            ];
            if (!isset($totalsByDept[$row['department']])) {
                $totalsByDept[$row['department']] = 0;
            }
            $totalsByDept[$row['department']] += $row['total_days'];
            $grandTotal += $row['total_days'];
            if (!in_array($row['department'], $distinctDepartments)) {
                $distinctDepartments[] = $row['department'];
            }
        }
        sort($distinctDepartments); // Alfabetik sırala

        return [
            'data' => $leaveTypesData,
            'departments' => $distinctDepartments, // Dinamik başlıklar için
            'all_departments' => $allDepartments, // Filtre dropdown için (gereksiz olabilir)
            'leave_types' => $leaveTypes, // Filtre dropdown için (gereksiz olabilir)
            'totals_by_dept' => $totalsByDept,
            'grand_total' => $grandTotal
        ];
    }

    // --- Dosya Çıktı Metodları ---
    private function outputFile($data, $title, $headers, $format, $template, $reportType) {
        if ($format === 'excel') {
            header('Content-Type: application/vnd.ms-excel; charset=utf-8');
            header('Content-Disposition: attachment; filename="rapor_' . date('Y-m-d_H-i-s') . '.xls"');
            header('Cache-Control: max-age=0');
            ob_start();
            include APP_ROOT . '/views/reports/excel_template.php'; // Genel Excel şablonu
            echo ob_get_clean();
        } elseif ($format === 'pdf') {
            header('Content-Type: text/html; charset=utf-8');
            header('Content-Disposition: attachment; filename="rapor_' . date('Y-m-d_H-i-s') . '.html"'); // PDF için HTML çıktısı
            ob_start();
            include APP_ROOT . '/views/reports/pdf_template.php'; // Genel PDF şablonu
            echo ob_get_clean();
        } elseif ($format === 'csv') {
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="rapor_' . date('Y-m-d_H-i-s') . '.csv"');
            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
            fputcsv($output, $headers); // Başlıkları yaz
            foreach ($data as $row) {
                // Sadece değerleri al (key'ler olmadan)
                $csvRow = [];
                foreach($row as $key => $value) {
                    // Tarihleri veya diğer özel formatları düzenle
                    if (strpos($key, '_date') !== false && $value) {
                        $csvRow[] = date('d.m.Y', strtotime($value));
                    } elseif ($key == 'is_justified') {
                        $csvRow[] = $value ? 'Mazeretli' : 'Mazeretsiz';
                    } else {
                        $csvRow[] = $value;
                    }
                }
                fputcsv($output, $csvRow);
            }
            fclose($output);
        }
        exit;
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