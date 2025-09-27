<?php
// karyawan/hapus.php
include '../config.php';

// Cek session dan hak akses admin
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo "Akses ditolak.";
    exit();
}

// Cek parameter ID
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_karyawan = intval($_GET['id']);

// Hapus data
$sql = "DELETE FROM users WHERE id = $id_karyawan";

if (mysqli_query($conn, $sql)) {
    header("Location: index.php");
    exit();
} else {
    echo "Error: Gagal menghapus data. " . mysqli_error($conn);
}
?>