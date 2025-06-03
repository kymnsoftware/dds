<?php
// public/login.php

require_once __DIR__ . '/../config/app.php'; // Uygulama ayarlarını ve oturumu başlat
require_once APP_ROOT . '/core/Auth.php';    // Auth sınıfını dahil et
require_once APP_ROOT . '/models/CardModel.php'; // CardModel'i dahil et

// Zaten giriş yapmışsa yönlendir
if (Auth::isLoggedIn()) {
    if (Auth::hasPrivilege(1)) {
        header('Location: /index.php');
    } else {
        header('Location: /leave_request.php');
    }
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login_name = $_POST['login_name'] ?? '';
    $password_input = $_POST['password'] ?? ''; // password olarak adlandırılmış kart numarası

    $cardModel = new CardModel();
    $user = $cardModel->authenticateUser($login_name, $password_input);

    if ($user) {
        Auth::login($user);
        if (Auth::hasPrivilege(1)) {
            header('Location: /index.php');
        } else {
            header('Location: /leave_request.php');
        }
        exit;
    } else {
        $error_message = 'Geçersiz kullanıcı adı veya şifre!';
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDKS - Giriş</title>
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
        }
        .login-container {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-logo i {
            font-size: 60px;
            color: #3498db;
        }
        .login-title {
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-control {
            height: 50px;
            border-radius: 5px;
        }
        .btn-login {
            height: 50px;
            border-radius: 5px;
            font-weight: bold;
            background-color: #3498db;
            border-color: #3498db;
        }
        .btn-login:hover {
            background-color: #2980b9;
            border-color: #2980b9;
        }
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-logo">
            <i class="fas fa-id-card"></i>
        </div>
        <div class="login-title">
            <h2>PDKS</h2>
            <p class="text-muted">Personel Devam Kontrol Sistemi</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo sanitize_html($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form method="post" action="">
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" class="form-control" name="login_name" placeholder="Kullanıcı Adı" required>
                </div>
            </div>
            <div class="form-group">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    </div>
                    <input type="password" class="form-control" name="password" placeholder="Şifre (Kart Numarası)" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-login">Giriş Yap</button>
        </form>
        
        <div class="login-footer">
            <p class="text-muted">&copy; <?php echo date('Y'); ?> PDKS - Tüm Hakları Saklıdır</p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>