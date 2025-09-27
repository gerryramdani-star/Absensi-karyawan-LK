<?php
include_once(__DIR__ . '/../config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: /proyek_absensi/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$is_admin = $_SESSION['is_admin'];
$user_initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'Aplikasi Absensi'; ?> - Aplikasi Absensi</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/proyek_absensi/assets/css/style.css">
</head>
<body>
    <div class="dashboard-page">
        <div class="sidebar">
            <div class="sidebar-header">
                <img src="/proyek_absensi/assets/img/logo.png" alt="Logo Perusahaan" class="sidebar-logo">
            </div>
            <ul class="sidebar-menu">
                <li><a href="/proyek_absensi/dashboard.php">📊 <span>Dashboard</span></a></li>
                
                <?php if ($is_admin): ?>
                    <li><a href="/proyek_absensi/karyawan/">👥 <span>Data Karyawan</span></a></li>
                    <li><a href="/proyek_absensi/absensi/">📅 <span>Data Absensi</span></a></li>
                    <li><a href="/proyek_absensi/laporan/">📄 <span>Laporan</span></a></li>
                <?php endif; ?>

                <li><a href="/proyek_absensi/profil/">👤 <span>Profil Saya</span></a></li>
                <li>
                    <a href="https://docs.google.com/spreadsheets/d/14rBQ3oT1mxXEhD_K9DplsJJmsUu2XjoJ/edit?gid=1587184166#gid=1587184166" target="_blank">
                        ✅ <span>To-do List</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="main-content">
            <div class="header">
                <div class="header-title">
                    <h3><?php echo $page_title ?? 'Dashboard'; ?></h3>
                    <p>Selamat datang kembali, <?php echo htmlspecialchars($user_name); ?>!</p>
                </div>
                <div class="header-right">
                    <div class="user-avatar" title="<?php echo htmlspecialchars($user_name); ?>">
                        <?php echo $user_initial; ?>
                    </div>
                    <a href="/proyek_absensi/logout.php" class="logout-icon" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                    </a>
                </div>
            </div>
            <div class="content-wrapper">