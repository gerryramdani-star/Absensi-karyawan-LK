<?php
// Atur zona waktu ke Asia/Jakarta
date_default_timezone_set('Asia/Jakarta');

// Detail koneksi database
$host = 'localhost';    // Biasanya 'localhost'
$user = 'root';         // User default XAMPP
$pass = '';             // Password default XAMPP kosong
$db_name = 'db_absensi'; // Nama database yang kita buat

// Membuat koneksi ke database
$conn = mysqli_connect($host, $user, $pass, $db_name);

// Cek koneksi
// Jika gagal, tampilkan pesan error dan hentikan script
if (!$conn) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}

// Memulai session
// Session digunakan untuk menyimpan status login pengguna
session_start();