<?php
// public/logout.php

require_once __DIR__ . '/../config/app.php'; // Oturum ayarlarını ve session_start'ı içerir
require_once APP_ROOT . '/core/Auth.php';    // Auth sınıfını dahil et

Auth::logout(); // Auth sınıfının logout metodunu çağır
?>