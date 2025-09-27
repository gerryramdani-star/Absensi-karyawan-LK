<?php
$page_title = "Tambah Karyawan";
include_once '../includes/header.php';

// Cek hak akses admin
if (!$is_admin) {
    echo "<div class='card' style='text-align:center; color: red;'>Maaf, Anda tidak memiliki hak akses untuk halaman ini.</div>";
    include_once '../includes/footer.php';
    exit();
}

// Logika untuk insert data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    
    // --- PERUBAHAN DI SINI ---
    // Fungsi password_hash() dihapus agar password disimpan sebagai teks biasa
    $password = mysqli_real_escape_string($conn, $_POST['password']); 
    
    $division_id = $_POST['division_id'];
    $position_id = $_POST['position_id'];

    $sql = "INSERT INTO users (name, username, password, division_id, position_id) VALUES ('$name', '$username', '$password', '$division_id', '$position_id')";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Ambil data divisi dan posisi untuk dropdown
$divisions = mysqli_query($conn, "SELECT * FROM divisions ORDER BY name ASC");
$positions = mysqli_query($conn, "SELECT * FROM positions ORDER BY name ASC");
?>

<div class="card">
    <h4>Form Tambah Karyawan</h4>
    <form action="tambah.php" method="post">
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div class="form-group">
            <label>Divisi</label>
            <select name="division_id" required>
                <option value="">-- Pilih Divisi --</option>
                <?php while($d = mysqli_fetch_assoc($divisions)): ?>
                    <option value="<?php echo $d['id']; ?>"><?php echo $d['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label>Jabatan</label>
            <select name="position_id" required>
                <option value="">-- Pilih Jabatan --</option>
                <?php while($p = mysqli_fetch_assoc($positions)): ?>
                    <option value="<?php echo $p['id']; ?>"><?php echo $p['name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn">Simpan</button>
    </form>
</div>

<?php
include_once '../includes/footer.php';
?>