<?php
$page_title = "Buat Pengajuan";
include_once '../includes/header.php';

$user_id = $_SESSION['user_id'];
$error_msg = '';
$success_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tipe_pengajuan = $_POST['tipe_pengajuan'];
    $tanggal_mulai = $_POST['tanggal_mulai'];
    $tanggal_selesai = $_POST['tanggal_selesai'];
    $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    // Logika upload file (jika ada)
    $file_pendukung = NULL;
    if (isset($_FILES['file_pendukung']) && $_FILES['file_pendukung']['error'] == 0) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0755, true); }
        $file_name = time() . '_' . basename($_FILES["file_pendukung"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["file_pendukung"]["tmp_name"], $target_file)) {
            $file_pendukung = $file_name;
        }
    }

    $sql = "INSERT INTO leave_requests (user_id, tipe_pengajuan, tanggal_mulai, tanggal_selesai, keterangan, file_pendukung) VALUES ('$user_id', '$tipe_pengajuan', '$tanggal_mulai', '$tanggal_selesai', '$keterangan', '$file_pendukung')";

    if (mysqli_query($conn, $sql)) {
        $success_msg = "Pengajuan berhasil dikirim dan sedang menunggu persetujuan.";
    } else {
        $error_msg = "Terjadi kesalahan, pengajuan gagal disimpan.";
    }
}
?>
<div class="card">
    <h4>Form Pengajuan Cuti, Izin, atau Sakit</h4>
    <?php if ($error_msg): ?><div class="message error"><?php echo $error_msg; ?></div><?php endif; ?>
    <?php if ($success_msg): ?><div class="message success"><?php echo $success_msg; ?></div><?php endif; ?>

    <form action="tambah.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="tipe_pengajuan">Tipe Pengajuan</label>
            <select id="tipe_pengajuan" name="tipe_pengajuan" required>
                <option value="cuti">Cuti</option>
                <option value="izin">Izin</option>
                <option value="sakit">Sakit</option>
            </select>
        </div>
        <div class="form-group">
            <label for="tanggal_mulai">Dari Tanggal</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai" required>
        </div>
        <div class="form-group">
            <label for="tanggal_selesai">Sampai Tanggal</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai" required>
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="file_pendukung">File Pendukung (Opsional)</label>
            <input type="file" id="file_pendukung" name="file_pendukung">
            <small>Contoh: Surat dokter untuk pengajuan sakit.</small>
        </div>
        <button type="submit" class="btn">Kirim Pengajuan</button>
    </form>
</div>
<?php include_once '../includes/footer.php'; ?>