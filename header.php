<?php
// views/shared/header.php

// $_SESSION verisinin mevcut olduğundan emin olun
$user_id = $_SESSION['user_id'] ?? '';
$user_name = $_SESSION['user_name'] ?? 'Misafir';
$department = $_SESSION['department'] ?? 'N/A';
$position = $_SESSION['position'] ?? 'N/A';
$photo_path = $_SESSION['photo_path'] ?? 'uploads/default-user.png';
$privilege = $_SESSION['privilege'] ?? 0;

// APP_TITLE ve COMPANY_NAME tanımlı olduğundan emin olun
if (!defined('APP_TITLE')) define('APP_TITLE', 'PDKS Yönetim Sistemi');
if (!defined('COMPANY_NAME')) define('COMPANY_NAME', 'Şirket Adınız');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_TITLE; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/style.css"> <?php if (isset($is_calendar_page) && $is_calendar_page): ?>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.10.1/main.min.css" rel="stylesheet">
    <?php endif; ?>
    <style>
        /* Modern Kurumsal Tema CSS (public/css/style.css'e taşınacak) */
        :root {
            --primary-blue: #2563eb;
            --primary-dark: #1e40af;
            --secondary-blue: #60a5fa;
            --accent-orange: #f59e0b;
            --success-green: #10b981;
            --danger-red: #ef4444;
            --warning-amber: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --sidebar-width: 280px;
            --header-height: 70px;
            --border-radius: 12px;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            margin: 0;
            padding: 0;
            color: var(--gray-800);
            line-height: 1.6;
        }

        /* Header */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: var(--header-height);
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            color: white;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
        }

        .header-brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .header-brand i {
            background: rgba(255, 255, 255, 0.2);
            padding: 0.5rem;
            border-radius: 8px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            backdrop-filter: blur(10px);
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: var(--header-height);
            left: 0;
            width: var(--sidebar-width);
            height: calc(100vh - var(--header-height));
            background: white;
            box-shadow: var(--shadow-md);
            z-index: 900;
            overflow-y: auto;
            transition: var(--transition);
        }

        .sidebar-menu {
            padding: 1.5rem 0;
        }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.875rem 1.5rem;
            color: var(--gray-600);
            text-decoration: none;
            transition: var(--transition);
            border: none;
            background: none;
            width: 100%;
            cursor: pointer;
            font-size: 0.925rem;
            font-weight: 500;
        }

        .menu-item:hover {
            background: var(--gray-50);
            color: var(--primary-blue);
            text-decoration: none;
        }

        .menu-item.active {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            color: white;
            border-right: 4px solid var(--accent-orange);
        }

        .menu-item i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .menu-badge {
            margin-left: auto;
            background: var(--danger-red);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 50px;
            font-weight: 600;
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 2rem;
            min-height: calc(100vh - var(--header-height));
        }

        /* Cards */
        .card {
            background: white;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            margin-bottom: 1.5rem;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--gray-50);
            border-bottom: 1px solid var(--gray-200);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            color: var(--gray-800);
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Dashboard Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border-left: 4px solid var(--primary-blue);
        }

        .stat-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
        }

        .stat-card.success {
            border-left-color: var(--success-green);
        }

        .stat-card.warning {
            border-left-color: var(--warning-amber);
        }

        .stat-card.danger {
            border-left-color: var(--danger-red);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 3rem;
            height: 3rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: white;
        }

        .stat-icon.primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
        }

        .stat-icon.success {
            background: linear-gradient(135deg, var(--success-green) 0%, #059669 100%);
        }

        .stat-icon.warning {
            background: linear-gradient(135deg, var(--warning-amber) 0%, #d97706 100%);
        }

        .stat-icon.danger {
            background: linear-gradient(135deg, var(--danger-red) 0%, #dc2626 100%);
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--gray-900);
            line-height: 1;
        }

        /* Buttons */
        .btn {
            font-weight: 500;
            border-radius: 8px;
            padding: 0.625rem 1.25rem;
            transition: var(--transition);
            border: none;
            font-size: 0.875rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e3a8a 100%);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success-green) 0%, #059669 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--warning-amber) 0%, #d97706 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger-red) 0%, #dc2626 100%);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-blue);
            color: var(--primary-blue);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary-blue);
            color: white;
        }

        /* Tables */
        .table {
            background: white;
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table thead th {
            background: var(--gray-50);
            border: none;
            font-weight: 600;
            color: var(--gray-700);
            padding: 1rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody td {
            padding: 1rem;
            border-top: 1px solid var(--gray-200);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--gray-50);
        }

        /* Forms */
        .form-control {
            border: 2px solid var(--gray-200);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        /* Badges */
        .badge {
            font-weight: 600;
            padding: 0.375rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
        }

        .badge-success {
            background: var(--success-green);
            color: white;
        }

        .badge-danger {
            background: var(--danger-red);
            color: white;
        }

        .badge-warning {
            background: var(--warning-amber);
            color: white;
        }

        .badge-info {
            background: var(--primary-blue);
            color: white;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--border-radius);
            padding: 1rem 1.25rem;
            margin-bottom: 1rem;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left-color: var(--success-green);
        }

        .alert-danger {
            background: #fef2f2;
            color: #dc2626;
            border-left-color: var(--danger-red);
        }

        .alert-warning {
            background: #fffbeb;
            color: #d97706;
            border-left-color: var(--warning-amber);
        }

        .alert-info {
            background: #eff6ff;
            color: #1d4ed8;
            border-left-color: var(--primary-blue);
        }

        /* Modals */
        .modal-content {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
        }

        .modal-header {
            border-bottom: 1px solid var(--gray-200);
            padding: 1.5rem;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            border-top: 1px solid var(--gray-200);
            padding: 1.5rem;
        }

        /* User Photo */
        .user-photo {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid var(--gray-200);
            box-shadow: var(--shadow-md);
        }

        .user-photo-small {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gray-200);
        }

        /* Page Transitions */
        .content-section {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.mobile-open {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-brand {
                font-size: 1.25rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gray-400);
        }

        /* Loading Spinner */
        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .quick-action-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            cursor: pointer;
            border: 2px solid transparent;
        }

        .quick-action-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--primary-blue);
        }

        .quick-action-icon {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.5rem;
            color: white;
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
        }

        /* Page Header */
        .page-header {
            background: white;
            border-radius: var(--border-radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .page-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1.125rem;
        }

        /* Tab Navigation */
        .nav-tabs {
            border: none;
            background: white;
            border-radius: var(--border-radius);
            padding: 0.5rem;
            box-shadow: var(--shadow-sm);
            margin-bottom: 2rem;
        }

        .nav-tabs .nav-link {
            border: none;
            border-radius: 8px;
            color: var(--gray-600);
            font-weight: 500;
            padding: 0.75rem 1.25rem;
            transition: var(--transition);
        }

        .nav-tabs .nav-link:hover {
            background: var(--gray-50);
            color: var(--primary-blue);
        }

        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            color: white;
        }

        /* Utility Classes */
        .text-gradient {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
        }

        .shadow-soft {
            box-shadow: var(--shadow-sm);
        }

        .shadow-medium {
            box-shadow: var(--shadow-md);
        }

        .shadow-large {
            box-shadow: var(--shadow-lg);
        }

        /* Dropdown Enhancements */
        .dropdown-menu {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.625rem 1rem;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: var(--gray-50);
            color: var(--primary-blue);
        }
        /* Devamsızlık Takibi Özel Stiller */
        .absence-status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-justified {
            background-color: #2ecc71;
            color: white;
        }
        .status-unjustified {
            background-color: #e74c3c;
            color: white;
        }
        .employee-item {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .present-item {
            background-color: #d4edda;
            border-left-color: #28a745;
        }
        .on-leave-item {
            background-color: #d1ecf1;
            border-left-color: #17a2b8;
        }
        .absent-unrecorded-item {
            background-color: #f8d7da;
            border-left-color: #dc3545;
        }
        .absent-recorded-item {
            background-color: #fff3cd;
            border-left-color: #ffc107;
        }
        .stats-card-small {
            text-align: center;
            padding: 15px;
            border-radius: 8px;
            background-color: #f8f9fa;
        }
        .stats-number-small {
            font-size: 1.5rem;
            font-weight: bold;
        }

        /* İzin Yönetimi Özel Stiller */
        .leave-status-small {
            font-weight: bold;
            padding: 4px 8px;
            border-radius: 15px;
            font-size: 11px;
        }
        .status-pending-small {
            background-color: #f39c12;
            color: white;
        }
        .status-approved-small {
            background-color: #2ecc71;
            color: white;
        }
        .status-rejected-small {
            background-color: #e74c3c;
            color: white;
        }
        .request-card {
            border-left: 5px solid #3498db;
            border-radius: 5px;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .action-buttons .btn {
            margin-right: 5px;
        }
        .balance-card {
            text-align: center;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        .balance-header {
            padding: 10px;
            color: white;
            font-weight: bold;
        }
        .balance-body {
            padding: 15px 10px;
        }
        .balance-value {
            font-size: 24px;
            font-weight: bold;
        }
        .leave-type {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        
    </style>
</head>
<body>
    <header class="main-header">
        <div class="header-brand">
            <i class="fas fa-building"></i>
            <span><?php echo COMPANY_NAME; ?></span>
        </div>
        <div class="header-actions">
            <div class="dropdown">
                <button class="btn btn-outline-light dropdown-toggle" type="button" id="systemDropdown" data-toggle="dropdown">
                    <i class="fas fa-cog mr-2"></i> Sistem
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="#" id="trigger-sync-btn">
                        <i class="fas fa-sync mr-2"></i> Senkronizasyon
                    </a>
                    <a class="dropdown-item" href="#" id="backup-btn">
                        <i class="fas fa-database mr-2"></i> Veritabanı Yedekle
                    </a>
                    <a class="dropdown-item" href="/leave_management.php">
                        <i class="fas fa-calendar-alt mr-2"></i> İzin Yönetimi
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="/public/logout.php">
                        <i class="fas fa-sign-out-alt mr-2"></i> Çıkış Yap
                    </a>
                </div>
            </div>
            <div class="user-info">
                <img src="<?php echo sanitize_html($photo_path); ?>" class="user-photo-small mr-2" alt="Profil">
                <span><?php echo sanitize_html($user_name); ?></span>
            </div>
        </div>
    </header>

    <nav class="sidebar">
        <div class="sidebar-menu">
            <button class="menu-item active" data-target="dashboard">
                <i class="fas fa-tachometer-alt"></i>
                <span>Gösterge Paneli</span>
            </button>
            <?php if ($privilege >= 1): // Kayıt yetkilisi ve üzeri ?>
            <button class="menu-item" data-target="cards">
                <i class="fas fa-users"></i>
                <span>Personel Kartları</span>
            </button>
            <button class="menu-item" data-target="attendance">
                <i class="fas fa-clock"></i>
                <span>Giriş-Çıkış Kayıtları</span>
            </button>
            <button class="menu-item" data-target="absence">
                <i class="fas fa-user-times"></i>
                <span>Devamsızlık Takibi</span>
                <span class="menu-badge" id="absence-count" style="display: none;">0</span>
            </button>
            <button class="menu-item" data-target="leave">
                <i class="fas fa-calendar-alt"></i>
                <span>İzin Yönetimi</span>
                <span class="menu-badge" id="leave-count" style="display: none;">0</span>
            </button>
            <button class="menu-item" data-target="salary">
                <i class="fas fa-money-bill-wave"></i>
                <span>Maaş Yönetimi</span>
            </button>
            <button class="menu-item" data-target="logs">
                <i class="fas fa-list-alt"></i>
                <span>Kart Logları</span>
            </button>
            <button class="menu-item" data-target="reports">
                <i class="fas fa-chart-bar"></i>
                <span>Raporlar</span>
            </button>
            <?php endif; ?>
            <?php if ($privilege >= 2): // Yönetici ve üzeri ?>
            <button class="menu-item" data-target="settings">
                <i class="fas fa-cogs"></i>
                <span>Sistem Ayarları</span>
            </button>
            <?php endif; ?>
        </div>
    </nav>

    <main class="main-content">
    <?php
    // Her bir bölüm için ayrı PHP dosyalarını buraya include edeceğiz
    // Örneğin:
    // include APP_ROOT . '/views/dashboard/index.php';
    // include APP_ROOT . '/views/cards/index.php';
    // Bu, ana index.php dosyasını çok uzun olmaktan kurtarır.
    ?>