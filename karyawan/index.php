<?php
$page_title = "Data Karyawan";
include_once '../includes/header.php';

// Cek hak akses admin
if (!$is_admin) {
    echo "<div class='card' style='text-align:center; color: red;'>Maaf, Anda tidak memiliki hak akses untuk halaman ini.</div>";
    include_once '../includes/footer.php';
    exit();
}

// --- STATISTIK ---
$total_karyawan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM users"))['total'];
$total_divisi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM divisions"))['total'];
$total_jabatan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM positions"))['total'];

// --- LOGIKA PAGINASI ---
$limit = 10; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $limit) - $limit : 0;

// --- Query utama dengan paginasi ---
$sql = "SELECT u.id, u.name, u.username, d.name AS division, p.name AS position 
        FROM users u 
        LEFT JOIN divisions d ON u.division_id = d.id 
        LEFT JOIN positions p ON u.position_id = p.id
        ORDER BY u.name ASC
        LIMIT $start, $limit";
$result = mysqli_query($conn, $sql);

// Query untuk total data (untuk menghitung jumlah halaman)
$total_data = $total_karyawan;
$total_pages = ceil($total_data / $limit);
?>

<div class="report-stats">
    <div class="stat-card">
        <p>Total Karyawan</p>
        <span><?php echo $total_karyawan; ?></span>
    </div>
    <div class="stat-card">
        <p>Total Divisi</p>
        <span><?php echo $total_divisi; ?></span>
    </div>
    <div class="stat-card">
        <p>Total Jabatan</p>
        <span><?php echo $total_jabatan; ?></span>
    </div>
</div>

<div class="card">
    <div class="table-header">
        <h4>Daftar Karyawan</h4>
        <div class="table-controls">
            <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Cari nama karyawan..." class="search-bar">
            <a href="tambah.php" class="btn btn-tambah">Tambah Karyawan</a>
        </div>
    </div>
    
    <table class="data-table" id="karyawanTable">
        <thead>
            <tr>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Divisi</th>
                <th>Jabatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['division']); ?></td>
                    <td><?php echo htmlspecialchars($row['position']); ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-edit">Edit</a>
                        <a href="hapus.php?id=<?php echo $row['id']; ?>" class="btn-aksi btn-hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5">Tidak ada data karyawan.</td></tr>
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
    table = document.getElementById("karyawanTable");
    tr = table.getElementsByTagName("tr");

    for (i = 1; i < tr.length; i++) { // Mulai dari 1 untuk skip header
        td = tr[i].getElementsByTagName("td")[0]; // Kolom pertama (Nama Lengkap)
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