<?php
// core/Auth.php

class Auth {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    public static function getUser() {
        return $_SESSION; // Tüm oturum bilgilerini döndür
    }

    public static function hasPrivilege($requiredPrivilege) {
        return isset($_SESSION['privilege']) && $_SESSION['privilege'] >= $requiredPrivilege;
    }

    public static function redirectIfNotLoggedIn($location = '/login.php') {
        if (!self::isLoggedIn()) {
            header('Location: ' . $location);
            exit;
        }
    }

    public static function redirectIfUnauthorized($requiredPrivilege, $location = '/unauthorized.php') {
        if (!self::hasPrivilege($requiredPrivilege)) {
            header('Location: ' . $location);
            exit;
        }
    }

    public static function login($user_data) {
        session_regenerate_id(true); // Yeni oturum ID'si oluştur

        $_SESSION['user_id'] = $user_data['user_id'];
        $_SESSION['user_name'] = $user_data['name'] . ' ' . $user_data['surname'];
        $_SESSION['privilege'] = $user_data['privilege'];
        $_SESSION['card_number'] = $user_data['card_number'];
        $_SESSION['department'] = $user_data['department'];
        $_SESSION['position'] = $user_data['position'];
        $_SESSION['photo_path'] = $user_data['photo_path'] ?? 'uploads/default-user.png';
        
        return true;
    }

    public static function logout() {
        $_SESSION = array(); // Tüm oturum değişkenlerini temizle
        session_destroy(); // Oturumu yok et
        header('Location: /login.php');
        exit;
    }
}
?>