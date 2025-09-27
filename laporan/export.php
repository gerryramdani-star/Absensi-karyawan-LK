<?php
include '../config.php';

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    echo "Akses ditolak";
    exit();
}

$filename = "laporan_absensi_" . date('Y-m-d') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

$output = fopen('php://output', 'w');

// Tambah kolom 'Status Masuk'
fputcsv($output, ['Nama Karyawan', 'Tipe Kehadiran', 'Tanggal', 'Jam Masuk', 'Status Masuk', 'Jam Pulang', 'Total Jam Kerja']);

$sql = "SELECT u.name AS user_name, a.type, a.check_in, a.check_out 
        FROM absences a 
        JOIN users u ON a.user_id = u.id";

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " WHERE DATE(a.check_in) BETWEEN '$start_date' AND '$end_date'";
}
$sql .= " ORDER BY a.check_in DESC";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $jam_masuk = date('H:i:s', strtotime($row['check_in']));
        // Tentukan status masuk
        $status_masuk = ($jam_masuk > '10:00:00') ? 'Telat' : 'Tepat Waktu';
        
        $check_out_time = $row['check_out'] ? date('H:i:s', strtotime($row['check_out'])) : '-';
        $tanggal = date('d M Y', strtotime($row['check_in']));
        $total_jam = '-';

        if ($row['check_out']) {
            $check_in_dt = new DateTime($row['check_in']);
            $check_out_dt = new DateTime($row['check_out']);
            $interval = $check_in_dt->diff($check_out_dt);
            $total_jam = $interval->format('%h jam %i menit');
        }

        fputcsv($output, [$row['user_name'], strtoupper($row['type']), $tanggal, $jam_masuk, $status_masuk, $check_out_time, $total_jam]);
    }
}

fclose($output);
exit();
?>