<?php
$page_title = "Laporan Absensi";
include_once '../includes/header.php';

// Cek hak akses admin
if (!$is_admin) {
    echo "<div class='card' style='text-align:center; color: red;'>Maaf, Anda tidak memiliki hak akses untuk halaman ini.</div>";
    include_once '../includes/footer.php';
    exit();
}

// --- LOGIKA UNTUK LAPORAN AKUMULASI BULANAN ---
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

$sql_akumulasi = "SELECT 
                    u.name AS user_name,
                    SUM(TIMESTAMPDIFF(SECOND, a.check_in, a.check_out)) AS total_detik
                  FROM absences a
                  JOIN users u ON a.user_id = u.id
                  WHERE 
                    MONTH(a.check_in) = '$bulan' AND 
                    YEAR(a.check_in) = '$tahun' AND
                    a.check_out IS NOT NULL
                  GROUP BY u.id
                  ORDER BY total_detik DESC";
$result_akumulasi = mysqli_query($conn, $sql_akumulasi);
$data_akumulasi = [];
while($row = mysqli_fetch_assoc($result_akumulasi)){
    $data_akumulasi[] = $row;
}

// --- LOGIKA UNTUK DETAIL RIWAYAT HARIAN ---
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

$sql_detail = "SELECT a.id, u.name AS user_name, a.type, a.check_in, a.check_out 
               FROM absences a JOIN users u ON a.user_id = u.id
               WHERE DATE(a.check_in) BETWEEN '$start_date' AND '$end_date'
               ORDER BY a.check_in DESC";
$result_detail = mysqli_query($conn, $sql_detail);
?>

<div class="card">
    <h4>Laporan Akumulasi Bulanan</h4>
    <form action="index.php" method="get" class="filter-form">
        <div class="form-group">
            <label>Pilih Bulan</label>
            <select name="bulan">
                <?php for ($m=1; $m<=12; $m++): 
                    $month = date('F', mktime(0,0,0,$m, 1, date('Y')));
                ?>
                    <option value="<?php echo $m; ?>" <?php echo ($m == $bulan) ? 'selected' : ''; ?>><?php echo $month; ?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Pilih Tahun</label>
            <input type="number" name="tahun" value="<?php echo $tahun; ?>" min="2020" max="<?php echo date('Y'); ?>">
        </div>
        <button type="submit" class="btn">Tampilkan</button>
    </form>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Total Jam Kerja (Bulan Ini)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data_akumulasi)): ?>
                <?php foreach($data_akumulasi as $row): 
                    $jam = floor($row['total_detik'] / 3600);
                    $menit = floor(($row['total_detik'] % 3600) / 60);
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><?php echo "$jam jam $menit menit"; ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="2">Tidak ada data absensi yang selesai (clock out) untuk periode ini.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<div class="card">
    <h4>Detail Riwayat Absensi</h4>
    <div class="report-controls">
        <form action="index.php" method="get" class="filter-form">
            <div class="form-group">
                <label for="start_date">Dari Tanggal</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
            </div>
            <div class="form-group">
                <label for="end_date">Sampai Tanggal</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
            </div>
            <button type="submit" class="btn">Filter Detail</button>
        </form>
        <a href="export.php?start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-export">Export Detail ke CSV</a>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Jam Pulang</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result_detail) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result_detail)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                    <td><span class="badge badge-<?php echo strtolower($row['type']); ?>"><?php echo strtoupper($row['type']); ?></span></td>
                    <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                    <td><?php echo date('H:i:s', strtotime($row['check_in'])); ?></td>
                    <td>
                        <?php 
                        $jam_masuk = date('H:i:s', strtotime($row['check_in']));
                        if ($jam_masuk > '10:00:00') {
                            echo '<span class="badge badge-telat">Telat</span>';
                        } else {
                            echo '<span class="badge badge-tepat-waktu">Tepat Waktu</span>';
                        }
                        ?>
                    </td>
                    <td><?php echo $row['check_out'] ? date('H:i:s', strtotime($row['check_out'])) : '<span class="belum-absen">Belum Clock Out</span>'; ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6">Tidak ada data untuk periode yang dipilih.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include_once '../includes/footer.php';
?>