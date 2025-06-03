<?php
// models/CardLogModel.php

require_once APP_ROOT . '/models/BaseModel.php';

class CardLogModel extends BaseModel {
    public function __construct() {
        parent::__construct('card_logs');
    }

    // En son okutulan kartı al
    public function getLastScannedCard() {
        // En son 5 saniye içinde okutulan kartı al
        $sql = "
            SELECT card_number
            FROM card_logs
            WHERE scan_time >= DATE_SUB(NOW(), INTERVAL 5 SECOND)
            ORDER BY scan_time DESC
            LIMIT 1
        ";
        return Database::fetch($sql);
    }

    // Son kart okutmaları için HTML çıktısı (Dashboard için)
    public function getRecentScansHtml($limit = 10) {
        $sql = "
            SELECT cl.*, c.enabled
            FROM card_logs cl
            LEFT JOIN cards c ON cl.card_number = c.card_number
            WHERE c.enabled = 'true' OR c.enabled IS NULL
            ORDER BY cl.scan_time DESC LIMIT :limit_val
        ";
        $scans = Database::fetchAll($sql, [':limit_val' => $limit]);

        $html = "";
        foreach($scans as $scan) {
            $html .= "<tr>";
            $html .= "<td><span class='badge badge-info'>".sanitize_html($scan['card_number'])."</span></td>";
            $html .= "<td>".date('H:i:s', strtotime($scan['scan_time']))."</td>";
            $html .= "</tr>";
        }
        return $html;
    }

    // Kart loglarını filtreli çekme (Logs sayfası için)
    public function getCardLogs($search = '', $date = '') {
        $sql = "
            SELECT cl.*, c.name, c.surname, c.photo_path, c.user_id
            FROM card_logs cl
            LEFT JOIN cards c ON cl.card_number = c.card_number
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (cl.card_number LIKE :search_card OR c.name LIKE :search_name OR c.user_id LIKE :search_user_id)";
            $params[':search_card'] = "%{$search}%";
            $params[':search_name'] = "%{$search}%";
            $params[':search_user_id'] = "%{$search}%";
        }
        if (!empty($date)) {
            $sql .= " AND DATE(cl.scan_time) = :date";
            $params[':date'] = $date;
        }
        $sql .= " ORDER BY cl.scan_time DESC LIMIT 500"; // Sayfalama eklenmeli

        $logs = Database::fetchAll($sql, $params);

        $html = "";
        if (count($logs) > 0) {
            foreach($logs as $log) {
                $fullName = !empty($log['name']) ? sanitize_html($log['name']) . ' ' . sanitize_html($log['surname']) : 'Bilinmeyen';
                $html .= "<tr>";
                $html .= "<td>".sanitize_html($log['id'])."</td>";
                $html .= "<td><span class='badge badge-primary'>".sanitize_html($log['card_number'])."</span></td>";
                $html .= "<td>".date('d.m.Y H:i:s', strtotime($log['scan_time']))."</td>";
                $html .= "<td>".date('d.m.Y H:i:s', strtotime($log['created_at']))."</td>";
                if (!empty($log['name'])) {
                    $html .= "<td><strong>{$fullName}</strong> <span class='text-muted'>(ID: ".sanitize_html($log['user_id']).")</span></td>";
                } else {
                    $html .= "<td><span class='text-muted'>Kullanıcı bulunamadı</span></td>";
                }
                $html .= "</tr>";
            }
        } else {
            $html .= "<tr><td colspan='5' class='text-center'>Kayıt bulunamadı</td></tr>";
        }
        return $html;
    }
}
?>