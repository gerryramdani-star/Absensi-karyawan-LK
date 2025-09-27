<?php
$page_title = "Ajukan Lembur";
include_once '../includes/header.php';

$user_id = $_SESSION['user_id'];
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_lembur = $_POST['tanggal_lembur'];
    $jam_mulai = $_POST['jam_mulai'];
    $jam_selesai = $_POST['jam_selesai'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);

    // Validasi: Cek apakah ada catatan kehadiran yang valid pada tanggal tersebut
    $sql_check_absensi = "SELECT id FROM absences WHERE user_id = '$user_id' AND DATE(check_in) = '$tanggal_lembur' AND check_out IS NOT NULL";
    $result_check = mysqli_query($conn, $sql_check_absensi);

    if (mysqli_num_rows($result_check) > 0) {
        $absence = mysqli_fetch_assoc($result_check);
        $absence_id = $absence['id'];

        // Jika valid, masukkan data ke overtime_requests
        $sql_insert = "INSERT INTO overtime_requests (user_id, absence_id, tanggal_lembur, jam_mulai_pengajuan, jam_selesai_pengajuan, catatan_pengajuan) 
                       VALUES ('$user_id', '$absence_id', '$tanggal_lembur', '$jam_mulai', '$jam_selesai', '$catatan')";
        
        if (mysqli_query($conn, $sql_insert)) {
            header("Location: index.php");
            exit();
        } else {
            $error_msg = "Error: Gagal menyimpan pengajuan.";
        }
    } else {
        $error_msg = "Pengajuan gagal: Tidak ditemukan catatan absensi (clock in & out) yang lengkap pada tanggal yang Anda pilih.";
    }
}
?>

<div class="card">
    <h4>Form Pengajuan Lembur</h4>
    <?php if ($error_msg): ?>
        <div class="message error"><?php echo $error_msg; ?></div>
    <?php endif; ?>
    <form action="tambah.php" method="post">
        <div class="form-group">
            <label for="tanggal_lembur">Tanggal Lembur</label>
            <input type="date" id="tanggal_lembur" name="tanggal_lembur" required>
        </div>
        <div class="form-group">
            <label for="jam_mulai">Jam Mulai Lembur</label>
            <input type="time" id="jam_mulai" name="jam_mulai" required>
        </div>
        <div class="form-group">
            <label for="jam_selesai">Jam Selesai Lembur</label>
            <input type="time" id="jam_selesai" name="jam_selesai" required>
        </div>
        <div class="form-group">
            <label for="catatan">Catatan/Pekerjaan yang Dilakukan</label>
            <textarea id="catatan" name="catatan" rows="4" required></textarea>
        </div>
        <button type="submit" class="btn">Ajukan</button>
    </form>
</div>

<?php
include_once '../includes/footer.php';
?>