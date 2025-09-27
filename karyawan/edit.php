<?php
// karyawan/edit.php
$page_title = "Edit Karyawan";
include_once '../includes/header.php';
// Cek hak akses admin
if (!$is_admin) {
    // Jika bukan admin, tampilkan pesan dan hentikan script
    echo "<div class='card' style='text-align:center; color: red;'>Maaf, Anda tidak memiliki hak akses untuk halaman ini.</div>";
    include_once '../includes/footer.php';
    exit();
}
// Cek apakah ada parameter ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id_karyawan = intval($_GET['id']);

// Proses update data saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $division_id = $_POST['division_id'];
    $position_id = $_POST['position_id'];

    // Buat query update
    $sql = "UPDATE users SET 
                name = '$name', 
                username = '$username', 
                division_id = '$division_id', 
                position_id = '$position_id' 
            WHERE id = $id_karyawan";
    
    // Jalankan query dan redirect jika berhasil
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data karyawan yang akan diedit
$sql_user = "SELECT * FROM users WHERE id = $id_karyawan";
$result_user = mysqli_query($conn, $sql_user);
$user = mysqli_fetch_assoc($result_user);

// Jika user tidak ditemukan, kembali ke halaman utama
if (!$user) {
    header("Location: index.php");
    exit();
}

// Ambil data divisi dan posisi untuk dropdown
$divisions = mysqli_query($conn, "SELECT * FROM divisions");
$positions = mysqli_query($conn, "SELECT * FROM positions");
?>

<div class="card">
    <h4>Form Edit Karyawan</h4>
    <form action="edit.php?id=<?php echo $id_karyawan; ?>" method="post">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label>Divisi</label>
            <select name="division_id" required>
                <?php while($d = mysqli_fetch_assoc($divisions)): ?>
                    <option value="<?php echo $d['id']; ?>" <?php echo ($d['id'] == $user['division_id']) ? 'selected' : ''; ?>>
                        <?php echo $d['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jabatan</label>
            <select name="position_id" required>
                <?php while($p = mysqli_fetch_assoc($positions)): ?>
                    <option value="<?php echo $p['id']; ?>" <?php echo ($p['id'] == $user['position_id']) ? 'selected' : ''; ?>>
                        <?php echo $p['name']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <p style="font-size: 14px; color: #757575;">* Perubahan password dilakukan di halaman profil masing-masing.</p>
        <button type="submit" class="btn">Update Data</button>
    </form>
</div>

<?php
include_once '../includes/footer.php';
?>