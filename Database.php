<?php
// core/Database.php

class Database {
    private static $pdo;

    public static function connect() {
        if (self::$pdo === null) {
            require_once APP_ROOT . '/config/database.php'; // $pdo değişkenini içerir
            self::$pdo = $pdo;
        }
        return self::$pdo;
    }

    // Parametreli sorgu çalıştırmak için yardımcı metod
    public static function execute($sql, $params = []) {
        $stmt = self::connect()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll($sql, $params = []) {
        return self::execute($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function fetch($sql, $params = []) {
        return self::execute($sql, $params)->fetch(PDO::FETCH_ASSOC);
    }

    public static function fetchColumn($sql, $params = []) {
        return self::execute($sql, $params)->fetchColumn();
    }

    public static function lastInsertId() {
        return self::connect()->lastInsertId();
    }
}
?>