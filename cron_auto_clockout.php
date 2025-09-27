<?php
// cron_auto_clockout.php
include 'config.php';

// --- PENGATURAN ---
// DIUBAH: Durasi kerja standar menjadi 8 jam
$durasi_kerja_jam = 8; 

// ... sisa kode tetap sama ...
$kemarin = date('Y-m-d', strtotime('-1 day'));

$sql = "UPDATE absences 
        SET check_out = DATE_ADD(check_in, INTERVAL $durasi_kerja_jam HOUR) 
        WHERE 
            DATE(check_in) = '$kemarin' AND 
            check_out IS NULL";

if (mysqli_query($conn, $sql)) {
    $jumlah_diupdate = mysqli_affected_rows($conn);
    echo "Proses clock out otomatis untuk tanggal $kemarin berhasil. Diperbarui: $jumlah_diupdate baris.";
} else {
    echo "Proses clock out otomatis gagal.";
}

mysqli_close($conn);
?>