<?php
// config/app.php

// Oturum ayarları
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 0); // HTTPS kullanıyorsanız 1 yapın
ini_set('session.use_strict_mode', 1);
session_set_cookie_params([
    'lifetime' => 0, // Tarayıcı kapanana kadar
    'path' => '/',
    'domain' => '',
    'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
    'httponly' => true,
    'samesite' => 'Lax'
]);

session_start();
session_regenerate_id(true); // Oturum sabitlemeyi önler

// Uygulama dizini
define('APP_ROOT', dirname(__DIR__));
require_once APP_ROOT . '/core/Autoloader.php';
define('PUBLIC_PATH', APP_ROOT . '/public');
define('UPLOAD_DIR', PUBLIC_PATH . '/uploads/');
define('BACKUP_DIR', APP_ROOT . '/backups/');

// Varsayılan zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama (Geliştirme ortamında true, üretimde false olmalı)
define('DEBUG_MODE', true);

if (DEBUG_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(0);
}

// Güvenlik: XSS koruması için genel bir fonksiyon
function sanitize_html($data) {
    return htmlspecialchars($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

// Uygulama başlığı
define('APP_TITLE', 'PDKS Yönetim Sistemi');
define('COMPANY_NAME', 'Şirket Adınız'); // Ayarlardan çekilebilir hale getirilecek
?>