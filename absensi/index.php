<?php
$page_title = "Data Absensi";
include_once '../includes/header.php';

if (!$is_admin) {
    echo "<div class='card' style='text-align:center; color: red;'>Maaf, Anda tidak memiliki hak akses untuk halaman ini.</div>";
    include_once '../includes/footer.php';
    exit();
}

$total_catatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM absences"))['total'];
$bulan_ini = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM absences WHERE MONTH(check_in) = MONTH(CURDATE()) AND YEAR(check_in) = YEAR(CURDATE())"))['total'];
$karyawan_aktif_query = mysqli_query($conn, "SELECT u.name, COUNT(a.id) AS total FROM absences a JOIN users u ON a.user_id = u.id GROUP BY a.user_id ORDER BY total DESC LIMIT 1");
$karyawan_aktif = mysqli_fetch_assoc($karyawan_aktif_query);

$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

$sql = "SELECT a.id, u.name AS user_name, a.type, a.check_in, a.check_out, a.remarks 
        FROM absences a 
        JOIN users u ON a.user_id = u.id
        ORDER BY a.check_in DESC
        LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

$total_data_query = mysqli_query($conn, "SELECT COUNT(id) FROM absences");
$total_data = mysqli_fetch_row($total_data_query)[0];
$total_pages = ceil($total_data / $limit);
?>

<div class="report-stats">
    <div class="stat-card"><p>Total Catatan Absensi</p><span><?php echo $total_catatan; ?></span></div>
    <div class="stat-card"><p>Absensi Bulan Ini</p><span><?php echo $bulan_ini; ?></span></div>
    <div class="stat-card"><p>Karyawan Paling Aktif</p><span><?php echo $karyawan_aktif ? htmlspecialchars($karyawan_aktif['name']) : '-'; ?></span></div>
</div>

<div class="card">
    <div class="table-header">
        <h4>Riwayat Absensi Seluruh Karyawan</h4>
        <div class="search-bar">
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari berdasarkan nama...">
        </div>
    </div>
    
    <table class="data-table" id="absensiTable">
        <thead>
            <tr>
                <th>Nama Karyawan</th>
                <th>Tipe</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Status Masuk</th>
                <th>Jam Pulang</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><span class="badge badge-<?php echo strtolower($row['type']); ?>"><?php echo strtoupper($row['type']); ?></span></td>
                        <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                        
                        <?php if(in_array($row['type'], ['kantor', 'wfa'])): ?>
                            <td><?php echo date('H:i:s', strtotime($row['check_in'])); ?></td>
                            <td>
                                <?php 
                                $jam_masuk = date('H:i:s', strtotime($row['check_in']));
                                if ($jam_masuk > '09:00:59') {
                                    echo '<span class="badge badge-telat">Telat</span>';
                                } else {
                                    echo '<span class="badge badge-tepat-waktu">Tepat Waktu</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo $row['check_out'] ? date('H:i:s', strtotime($row['check_out'])) : '<span class="belum-absen">Belum Clock Out</span>'; ?></td>
                            <td><?php echo htmlspecialchars($row['remarks'] ?? '-'); ?></td>
                        <?php else: ?>
                            <td colspan="4" style="text-align: center; font-style: italic; color: #64748b;">
                                Tidak ada data jam untuk status <?php echo strtoupper($row['type']); ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7">Belum ada data absensi.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<script>
function searchTable() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("absensiTable");
    tr = table.getElementsByTagName("tr");
    for (i = 1; i < tr.length; i++) { 
        td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            txtValue = td.textContent || td.innerText;
            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

<?php
include_once '../includes/footer.php';
?>