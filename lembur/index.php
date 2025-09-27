<?php
$page_title = "Lembur Saya";
include_once '../includes/header.php';

$user_id = $_SESSION['user_id'];

// Ambil semua data pengajuan lembur milik user ini
$sql = "SELECT * FROM overtime_requests WHERE user_id = '$user_id' ORDER BY tanggal_lembur DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="card">
    <div class="table-header">
        <h4>Riwayat Pengajuan Lembur Anda</h4>
        <a href="tambah.php" class="btn btn-tambah">Ajukan Lembur Baru</a>
    </div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Tanggal Lembur</th>
                <th>Jam</th>
                <th>Catatan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo date('d M Y', strtotime($row['tanggal_lembur'])); ?></td>
                    <td><?php echo date('H:i', strtotime($row['jam_mulai_pengajuan'])) . ' - ' . date('H:i', strtotime($row['jam_selesai_pengajuan'])); ?></td>
                    <td><?php echo htmlspecialchars($row['catatan_pengajuan']); ?></td>
                    <td>
                        <span class="badge badge-<?php echo strtolower($row['status']); ?>">
                            <?php echo ucfirst($row['status']); ?>
                        </span>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="4">Anda belum memiliki pengajuan lembur.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include_once '../includes/footer.php';
?>