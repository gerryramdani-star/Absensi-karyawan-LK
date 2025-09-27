<?php
$page_title = "Riwayat Pengajuan";
include_once '../includes/header.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM leave_requests WHERE user_id = '$user_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $sql);
?>
<div class="card">
    <div class="table-header">
        <h4>Riwayat Pengajuan Cuti, Izin, & Sakit</h4>
        <a href="tambah.php" class="btn btn-tambah">Buat Pengajuan Baru</a>
    </div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Keterangan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><span class="badge badge-<?php echo strtolower($row['tipe_pengajuan']); ?>"><?php echo ucfirst($row['tipe_pengajuan']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($row['tanggal_selesai'])); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><span class="badge badge-<?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Anda belum memiliki riwayat pengajuan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php include_once '../includes/footer.php'; ?>