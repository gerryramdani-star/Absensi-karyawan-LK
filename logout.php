<?php
// Sertakan file config untuk memulai session
include 'config.php';

// Hapus semua variabel session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header("Location: index.php");
exit();
?>