<?php
$page_title = "Profil Saya";
include_once '../includes/header.php';

// ... Logika update profil tetap sama ...
$user_id = $_SESSION['user_id'];
$error_msg = '';
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $sql_update_name = "UPDATE users SET name = '$name' WHERE id = $user_id";
        if (mysqli_query($conn, $sql_update_name)) { $_SESSION['user_name'] = $name; $success_msg = "Nama berhasil diperbarui!"; } else { $error_msg = "Gagal memperbarui nama."; }
    }
    elseif (isset($_POST['update_password'])) {
        $password_lama = $_POST['password_lama']; $password_baru = $_POST['password_baru']; $konfirmasi_password = $_POST['konfirmasi_password'];
        $result = mysqli_query($conn, "SELECT password FROM users WHERE id = $user_id"); $user = mysqli_fetch_assoc($result); $password_saat_ini = $user['password'];
        if ($password_lama != $password_saat_ini) { $error_msg = "Password lama tidak sesuai."; } elseif ($password_baru != $konfirmasi_password) { $error_msg = "Konfirmasi password baru tidak cocok."; } elseif (strlen($password_baru) < 3) { $error_msg = "Password baru minimal harus 3 karakter."; } else {
            $password_baru_escaped = mysqli_real_escape_string($conn, $password_baru);
            $sql_update_pass = "UPDATE users SET password = '$password_baru_escaped' WHERE id = $user_id";
            if (mysqli_query($conn, $sql_update_pass)) { $success_msg = "Password berhasil diperbarui!"; } else { $error_msg = "Terjadi kesalahan saat memperbarui password."; }
        }
    }
}

// ... Query data user tetap sama ...
$sql_user = "SELECT u.name, u.username, d.name AS division_name, p.name AS position_name FROM users u LEFT JOIN divisions d ON u.division_id = d.id LEFT JOIN positions p ON u.position_id = p.id WHERE u.id = '$user_id'";
$result_user = mysqli_query($conn, $sql_user);
$user_data = mysqli_fetch_assoc($result_user);
$user_initial = strtoupper(substr($user_data['name'], 0, 1));
$sql_history = "SELECT *, type FROM absences WHERE user_id = $user_id ORDER BY check_in DESC";
$history_result = mysqli_query($conn, $sql_history);
?>

<div class="card profile-card">
    <div class="profile-sidebar">
        <div class="profile-avatar"><?php echo $user_initial; ?></div>
        <h3><?php echo htmlspecialchars($user_data['name']); ?></h3>
        <p><?php echo htmlspecialchars($user_data['position_name'] ?? 'N/A'); ?></p>
    </div>
    <div class="profile-content">
        <div class="profile-tabs">
            <button class="tab-link active" onclick="openTab(event, 'detail')">Detail Profil</button>
            <button class="tab-link" onclick="openTab(event, 'password')">Ubah Password</button>
            <button class="tab-link" onclick="openTab(event, 'riwayat')">Riwayat Absensi</button>
        </div>
        <?php if ($error_msg): ?><div class="message error"><?php echo $error_msg; ?></div><?php endif; ?>
        <?php if ($success_msg): ?><div class="message success"><?php echo $success_msg; ?></div><?php endif; ?>
        <div id="detail" class="tab-content active">
            <form action="index.php" method="post"><div class="form-group"><label>Nama Lengkap</label><input type="text" name="name" value="<?php echo htmlspecialchars($user_data['name']); ?>" required></div><div class="form-group"><label>Username</label><input type="text" value="<?php echo htmlspecialchars($user_data['username']); ?>" readonly></div><div class="form-group"><label>Divisi</label><input type="text" value="<?php echo htmlspecialchars($user_data['division_name'] ?? 'N/A'); ?>" readonly></div><button type="submit" name="update_profile" class="btn">Simpan Perubahan</button></form>
        </div>
        <div id="password" class="tab-content">
            <form action="index.php" method="post"><div class="form-group"><label for="password_lama">Password Lama</label><input type="password" id="password_lama" name="password_lama" required></div><div class="form-group"><label for="password_baru">Password Baru</label><input type="password" id="password_baru" name="password_baru" required></div><div class="form-group"><label for="konfirmasi_password">Konfirmasi Password Baru</label><input type="password" id="konfirmasi_password" name="konfirmasi_password" required></div><button type="submit" name="update_password" class="btn">Perbarui Password</button></form>
        </div>
        <div id="riwayat" class="tab-content">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tipe</th>
                        <th>Jam Masuk</th>
                        <th>Status Masuk</th> <th>Jam Pulang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_result) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?php echo date('d M Y', strtotime($row['check_in'])); ?></td>
                            <td><span class="badge badge-<?php echo strtolower($row['type']); ?>"><?php echo strtoupper($row['type']); ?></span></td>
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
                        <tr><td colspan="5">Anda belum memiliki riwayat absensi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) { tabcontent[i].style.display = "none"; }
    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) { tablinks[i].className = tablinks[i].className.replace(" active", ""); }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}
document.addEventListener("DOMContentLoaded", function() { document.getElementsByClassName("tab-link")[0].click(); });
</script>

<?php
include_once '../includes/footer.php';
?>