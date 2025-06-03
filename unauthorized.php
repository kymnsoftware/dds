<?php
// views/auth/unauthorized.php

require_once APP_ROOT . '/config/app.php'; // Uygulama ayarlarını içerir
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetkisiz Erişim</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 40px;
        }
        .icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 20px;
        }
        h1 {
            color: #343a40;
            margin-bottom: 15px;
        }
        p {
            color: #6c757d;
            margin-bottom: 30px;
        }
        .btn-home {
            background-color: #3498db;
            border-color: #3498db;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
        }
        .btn-home:hover {
            background-color: #2980b9;
            border-color: #2980b9;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="icon">
            <i class="fas fa-exclamation-circle"></i>
        </div>
        <h1>Yetkisiz Erişim!</h1>
        <p>Bu sayfayı görüntülemek için yeterli yetkiye sahip değilsiniz.</p>
        <a href="/public/index.php" class="btn btn-home">
            <i class="fas fa-home mr-2"></i> Ana Sayfaya Dön
        </a>
        <a href="/public/logout.php" class="btn btn-outline-secondary ml-3">
            <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
        </a>
    </div>
</body>
</html>