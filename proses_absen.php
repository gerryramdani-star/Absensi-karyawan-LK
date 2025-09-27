<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /proyek_absensi/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");
$now = date("Y-m-d H:i:s");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql_check = "SELECT id FROM absences WHERE user_id = '$user_id' AND DATE(check_in) = '$today'";
    $result_check = mysqli_query($conn, $sql_check);
    $sudah_absen = mysqli_num_rows($result_check) > 0;

    // --- PROSES CLOCK IN (KANTOR & WFA) ---
    if (isset($_POST['clock_in'])) {
        if (!$sudah_absen) {
            $tipe_absen = mysqli_real_escape_string($conn, $_POST['clock_in']);
            $remarks = NULL;

            if ($tipe_absen == 'wfa' && isset($_POST['wfa_remarks'])) {
                $remarks = mysqli_real_escape_string($conn, $_POST['wfa_remarks']);
            }

            $stmt = $conn->prepare("INSERT INTO absences (user_id, type, check_in, remarks) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user_id, $tipe_absen, $now, $remarks);
            $stmt->execute();
            $stmt->close();
        }
    }
    // --- PROSES STATUS (IZIN, SAKIT, CUTI) ---
    elseif (isset($_POST['status_update'])) {
        if (!$sudah_absen) {
            $tipe_status = mysqli_real_escape_string($conn, $_POST['status_update']);
            
            if (in_array($tipe_status, ['izin', 'sakit', 'cuti'])) {
                $stmt = $conn->prepare("INSERT INTO absences (user_id, type, check_in) VALUES (?, ?, ?)");
                $stmt->bind_param("iss", $user_id, $tipe_status, $now);
                $stmt->execute();
                $stmt->close();
            }
        }
    }
    // --- PROSES CLOCK OUT ---
    elseif (isset($_POST['clock_out'])) {
        $sql = "UPDATE absences SET check_out = '$now' WHERE user_id = '$user_id' AND DATE(check_in) = '$today'";
        mysqli_query($conn, $sql);
    }
}

header("Location: /proyek_absensi/dashboard.php");
exit();
?>