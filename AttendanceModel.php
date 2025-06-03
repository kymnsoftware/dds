<?php
// models/AttendanceModel.php

require_once APP_ROOT . '/models/BaseModel.php';

class AttendanceModel extends BaseModel {
    public function __construct() {
        parent::__construct('attendance_logs');
    }

    // Dashboard ve Attendance sayfası için istatistikler
    public function getTodayEntriesCount() {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(DISTINCT al.card_number) FROM attendance_logs al JOIN cards c ON al.card_number = c.card_number WHERE DATE(al.event_time) = :today AND al.event_type = 'ENTRY' AND c.enabled = 'true'";
        return Database::fetchColumn($sql, [':today' => $today]);
    }

    public function getTodayExitsCount() {
        $today = date('Y-m-d');
        $sql = "SELECT COUNT(DISTINCT al.card_number) FROM attendance_logs al JOIN cards c ON al.card_number = c.card_number WHERE DATE(al.event_time) = :today AND al.event_type = 'EXIT' AND c.enabled = 'true'";
        return Database::fetchColumn($sql, [':today' => $today]);
    }

    public function getCurrentlyInsideCount() {
        // En son hareketi giriş olan ve enabled=true olan kartların sayısı
        $sql = "
            SELECT COUNT(DISTINCT al1.card_number)
            FROM attendance_logs al1
            JOIN cards c ON al1.card_number = c.card_number
            LEFT JOIN attendance_logs al2 ON al1.card_number = al2.card_number
                AND al1.event_time < al2.event_time
            WHERE al2.id IS NULL -- Sadece en son kayıtları al
            AND al1.event_type = 'ENTRY' -- Ve en son kayıt giriş olmalı
            AND c.enabled = 'true'
        ";
        return Database::fetchColumn($sql);
    }

    // Son giriş-çıkış aktiviteleri için HTML çıktısı (Dashboard için)
    public function getRecentActivitiesHtml($limit = 10) {
        $sql = "
            SELECT al.*, c.name, c.surname, c.photo_path
            FROM attendance_logs al
            JOIN cards c ON al.card_number = c.card_number
            WHERE c.enabled = 'true'
            ORDER BY al.event_time DESC LIMIT :limit_val
        ";
        $activities = Database::fetchAll($sql, [':limit_val' => $limit]);

        $html = "";
        foreach($activities as $activity) {
            $photoPath = !empty($activity['photo_path']) ? sanitize_html($activity['photo_path']) : '/public/uploads/default-user.png';
            $fullName = !empty($activity['name']) ? sanitize_html($activity['name']) . ' ' . sanitize_html($activity['surname']) : 'Bilinmeyen Kullanıcı';
            $eventTypeText = ($activity['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
            $eventTypeClass = ($activity['event_type'] == 'ENTRY') ? 'success' : 'danger';

            $html .= "<tr>";
            $html .= "<td>";
            $html .= "<div class='d-flex align-items-center'>";
            $html .= "<img src='{$photoPath}' class='user-photo-small mr-3' alt='Profil'>";
            $html .= "<span>{$fullName}</span>";
            $html .= "</div>";
            $html .= "</td>";
            $html .= "<td><span class='badge badge-{$eventTypeClass}'>{$eventTypeText}</span></td>";
            $html .= "<td>".date('d.m.Y H:i', strtotime($activity['event_time']))."</td>";
            $html .= "<td><span class='badge badge-info'>Cihaz #".sanitize_html($activity['device_id'])."</span></td>";
            $html .= "</tr>";
        }
        return $html;
    }

    // Giriş-Çıkış kayıtlarını filtreli çekme (Attendance sayfası için)
    public function getAttendanceLogs($search = '', $date = '', $type = '') {
        $sql = "
            SELECT al.*, c.name, c.surname, c.photo_path
            FROM attendance_logs al
            LEFT JOIN cards c ON al.card_number = c.card_number
            WHERE 1=1
            AND (c.enabled = 'true' OR c.enabled IS NULL)
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (c.name LIKE :search_name OR c.surname LIKE :search_surname OR al.card_number LIKE :search_card)";
            $params[':search_name'] = "%{$search}%";
            $params[':search_surname'] = "%{$search}%";
            $params[':search_card'] = "%{$search}%";
        }
        if (!empty($date)) {
            $sql .= " AND DATE(al.event_time) = :date";
            $params[':date'] = $date;
        }
        if (!empty($type)) {
            $sql .= " AND al.event_type = :type";
            $params[':type'] = $type;
        }
        $sql .= " ORDER BY al.event_time DESC LIMIT 500"; // Sayfalama eklenmeli

        $logs = Database::fetchAll($sql, $params);

        $html = "";
        if (count($logs) > 0) {
            foreach($logs as $log) {
                $photoPath = !empty($log['photo_path']) ? sanitize_html($log['photo_path']) : '/public/uploads/default-user.png';
                $fullName = !empty($log['name']) ? sanitize_html($log['name']) . ' ' . sanitize_html($log['surname']) : 'Bilinmeyen Kullanıcı';
                $eventTypeText = ($log['event_type'] == 'ENTRY') ? 'Giriş' : 'Çıkış';
                $eventTypeClass = ($log['event_type'] == 'ENTRY') ? 'success' : 'danger';

                $html .= "<tr>";
                $html .= "<td>".sanitize_html($log['id'])."</td>";
                $html .= "<td><div class='d-flex align-items-center'><img src='{$photoPath}' class='user-photo-small mr-3' alt='Profil'></div></td>";
                $html .= "<td>{$fullName}</td>";
                $html .= "<td><span class='badge badge-primary'>".sanitize_html($log['card_number'])."</span></td>";
                $html .= "<td><span class='badge badge-{$eventTypeClass}'><i class='fas fa-".($log['event_type'] == 'ENTRY' ? 'sign-in-alt' : 'sign-out-alt')." mr-1'></i>{$eventTypeText}</span></td>";
                $html .= "<td>".date('d.m.Y H:i:s', strtotime($log['event_time']))."</td>";
                $html .= "<td><span class='badge badge-info'>Cihaz #".sanitize_html($log['device_id'])."</span></td>";
                $html .= "</tr>";
            }
        } else {
            $html .= "<tr><td colspan='7' class='text-center'>Kayıt bulunamadı</td></tr>";
        }
        return $html;
    }

    // Raporlama için veri çekme
    public function getAttendanceDataForExport($search = '', $date = '', $type = '') {
        $sql = "
            SELECT al.id, al.card_number, c.name, c.surname, al.event_type, al.event_time, al.device_id
            FROM attendance_logs al
            LEFT JOIN cards c ON al.card_number = c.card_number
            WHERE 1=1
            AND (c.enabled = 'true' OR c.enabled IS NULL)
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (c.name LIKE :search_name OR c.surname LIKE :search_surname OR al.card_number LIKE :search_card)";
            $params[':search_name'] = "%{$search}%";
            $params[':search_surname'] = "%{$search}%";
            $params[':search_card'] = "%{$search}%";
        }
        if (!empty($date)) {
            $sql .= " AND DATE(al.event_time) = :date";
            $params[':date'] = $date;
        }
        if (!empty($type)) {
            $sql .= " AND al.event_type = :type";
            $params[':type'] = $type;
        }
        $sql .= " ORDER BY al.event_time DESC"; // Export olduğu için limit yok

        return Database::fetchAll($sql, $params);
    }
}
?>