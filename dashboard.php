<?php
$page_title = "Dashboard";
include_once 'includes/header.php';

// --- KUMPULAN QUERY UNTUK DASHBOARD ---
$user_id = $_SESSION['user_id'];
$today = date("Y-m-d");
$sql_absensi_user = "SELECT * FROM absences WHERE user_id = '$user_id' AND DATE(check_in) = '$today'";
$result_absensi_user = mysqli_query($conn, $sql_absensi_user);
$absensi_hari_ini = mysqli_fetch_assoc($result_absensi_user);
$sudah_absen_hari_ini = $absensi_hari_ini ? true : false;
$tipe_absen_hari_ini = $absensi_hari_ini['type'] ?? '';

// Cek apakah absen hari ini adalah absen kerja (bukan cuti/sakit/izin)
$is_working_today = in_array($tipe_absen_hari_ini, ['kantor', 'wfa']);

$sudah_clock_in = $sudah_absen_hari_ini && $is_working_today;
$sudah_clock_out = $sudah_clock_in && $absensi_hari_ini['check_out'];

$jam_sekarang = (int)date('H');
$bisa_absen = $jam_sekarang >= 9;

// Query lainnya...
$sql_akumulasi = "SELECT SUM(TIMESTAMPDIFF(SECOND, check_in, check_out)) AS total_detik FROM absences WHERE user_id = '$user_id' AND MONTH(check_in) = MONTH(CURDATE()) AND YEAR(check_in) = YEAR(CURDATE()) AND check_out IS NOT NULL";
$result_akumulasi = mysqli_query($conn, $sql_akumulasi);
$total_detik_bulan_ini = mysqli_fetch_assoc($result_akumulasi)['total_detik'] ?? 0;
$jam_akumulasi = floor($total_detik_bulan_ini / 3600);
$menit_akumulasi = floor(($total_detik_bulan_ini % 3600) / 60);

$total_karyawan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(id) AS total FROM users"))['total'];
$sql_whos_in = "SELECT u.name FROM absences a JOIN users u ON a.user_id = u.id WHERE DATE(a.check_in) = '$today' AND a.type IN ('kantor', 'wfa') ORDER BY a.check_in ASC";
$whos_in_result = mysqli_query($conn, $sql_whos_in);
?>

<div class="dashboard-container-new">
    <div class="main-column">
        <div class="card card-main-action">
            <div class="main-action-content">
                <h4>Halo, <?php echo htmlspecialchars($user_name); ?>!</h4>
                <p>Bagaimana kabarmu hari ini? Jangan lupa untuk mencatat kehadiranmu.</p>
                
                <form action="/proyek_absensi/proses_absen.php" method="post" class="main-action-form" id="absensiForm">
                    <input type="hidden" name="clock_in_type" id="clockInType" value="">
                    <input type="hidden" name="wfa_remarks" id="wfaRemarks" value="">

                    <?php if (!$sudah_absen_hari_ini): ?>
                        <div class="button-group">
                            <button type="submit" name="clock_in" value="kantor" class="btn btn-masuk" <?php if (!$bisa_absen) echo 'disabled'; ?>>🏢 Masuk Kantor</button>
                            <button type="button" id="wfaBtn" class="btn btn-wfa" <?php if (!$bisa_absen) echo 'disabled'; ?>>🌐 Work From Anywhere</button>
                        </div>
                        <div class="button-group" style="margin-top: 10px;">
                            <button type="submit" name="status_update" value="izin" class="btn btn-izin">✉️ Izin</button>
                            <button type="submit" name="status_update" value="sakit" class="btn btn-sakit">🤒 Sakit</button>
                            <button type="submit" name="status_update" value="cuti" class="btn btn-cuti">🏖️ Cuti</button>
                        </div>
                        <?php if (!$bisa_absen): ?>
                            <p class="info-absen-tunggu">Absensi baru bisa dilakukan mulai pukul 09:00.</p>
                        <?php endif; ?>
                    <?php elseif ($sudah_clock_in && !$sudah_clock_out): ?>
                        <div class="info-absen">Anda sudah Clock In hari ini (<?php echo strtoupper($tipe_absen_hari_ini); ?>).</div>
                        <button type="submit" name="clock_out" class="btn btn-pulang">🕒 Clock Out Sekarang</button>
                    <?php else: ?>
                        <div class="info-absen-selesai">
                            ✅ Kehadiran hari ini telah tercatat sebagai: <strong><?php echo strtoupper($tipe_absen_hari_ini); ?></strong>.
                        </div>
                    <?php endif; ?>
                </form>
            </div>
            <div class="main-action-clock">
                <div id="jam" class="jam"></div>
                <div class="tanggal"><?php echo date("l, d F Y"); ?></div>
            </div>
        </div>
        
        <div class="card">
            <h4>Karyawan Hadir Hari Ini (<?php echo mysqli_num_rows($whos_in_result); ?>)</h4>
            <div class="whos-in-list">
                <?php if (mysqli_num_rows($whos_in_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($whos_in_result)): 
                        $initial = strtoupper(substr($row['name'], 0, 1));
                    ?>
                        <div class="whos-in-item" title="<?php echo htmlspecialchars($row['name']); ?>">
                            <div class="whos-in-avatar"><?php echo $initial; ?></div>
                            <span class="whos-in-name"><?php echo htmlspecialchars($row['name']); ?></span>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="empty-state">Belum ada karyawan yang absen kerja hari ini.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="side-column">
        <div class="card">
             <h4>Akumulasi Jam Kerja Bulan Ini</h4>
             <div class="akumulasi-jam">
                 <span><?php echo $jam_akumulasi; ?></span> jam <span><?php echo $menit_akumulasi; ?></span> menit
             </div>
        </div>
        <div class="card">
             <h4>Aktivitas Anda Hari Ini</h4>
             <div class="your-activity">
                 <div class="activity-item"><span>Clock In</span><strong class="<?php echo $sudah_clock_in ? 'text-success' : 'text-muted'; ?>"><?php echo $sudah_clock_in ? date("H:i", strtotime($absensi_hari_ini['check_in'])) : '-'; ?></strong></div>
                 <div class="activity-item"><span>Clock Out</span><strong class="<?php echo $sudah_clock_out ? 'text-success' : 'text-muted'; ?>"><?php echo $sudah_clock_out ? date("H:i", strtotime($absensi_hari_ini['check_out'])) : '-'; ?></strong></div>
             </div>
        </div>
         <div class="card card-stat">
             <h4>Total Karyawan</h4>
             <p class="stat-number"><?php echo $total_karyawan; ?></p>
             <a href="/proyek_absensi/karyawan/" class="stat-link">Lihat Semua Karyawan</a>
         </div>
    </div>
</div>

<div id="wfaModal" class="modal">
    <div class="modal-content">
        <span class="close-button">&times;</span>
        <h4>Keterangan Work From Anywhere</h4>
        <p>Jelaskan di mana lokasi Anda bekerja atau apa aktivitas yang sedang Anda lakukan hari ini.</p>
        <div class="form-group">
            <label for="remarksText">Contoh: Meeting di Client ABC, Bekerja dari Bandung.</label>
            <textarea id="remarksText" rows="4" class="form-group input" placeholder="Tulis keterangan Anda di sini..."></textarea>
        </div>
        <button id="submitWfa" class="btn btn-tambah">Kirim Absensi WFA</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('absensiForm');
    
    // Logika untuk Modal WFA
    const wfaModal = document.getElementById('wfaModal');
    const wfaBtn = document.getElementById('wfaBtn');
    const closeBtn = document.querySelector('.close-button');
    const submitWfaBtn = document.getElementById('submitWfa');

    if (wfaBtn) {
        wfaBtn.addEventListener('click', function(e) {
            e.preventDefault(); // Mencegah form submit
            wfaModal.style.display = 'block';
        });
    }
    
    if (closeBtn) {
        closeBtn.onclick = function() {
            wfaModal.style.display = 'none';
        }
    }

    window.onclick = function(event) {
        if (event.target == wfaModal) {
            wfaModal.style.display = 'none';
        }
    }
    
    if (submitWfaBtn) {
        submitWfaBtn.onclick = function() {
            const remarks = document.getElementById('remarksText').value;
            if (remarks.trim() === "") {
                alert('Keterangan WFA tidak boleh kosong.');
                return;
            }
            // Buat input hidden baru untuk menandakan ini adalah clock_in
            const clockInInput = document.createElement('input');
            clockInInput.type = 'hidden';
            clockInInput.name = 'clock_in';
            clockInInput.value = 'wfa';
            form.appendChild(clockInInput);

            // Isi value untuk remarks
            document.getElementById('wfaRemarks').value = remarks;
            form.submit();
        }
    }
});
</script>

<?php
include_once 'includes/footer.php';
?>