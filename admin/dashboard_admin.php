<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";

wajib_login('admin');

$totalSiswa     = hitung($koneksi, "SELECT COUNT(*) FROM siswa");
$totalPengaduan = hitung($koneksi, "SELECT COUNT(*) FROM pengaduan");
$totalSarana    = hitung($koneksi, "SELECT COUNT(*) FROM sarana");
$totalMenunggu  = hitung($koneksi, "SELECT COUNT(*) FROM pengaduan WHERE status = 'Menunggu'");

// 5 pengaduan terbaru
$terbaru = mysqli_query($koneksi,
    "SELECT p.id_pengaduan, s.nama_siswa, s.kelas, sr.nama_sarana, p.tanggal_pengaduan, p.status
     FROM pengaduan p
     JOIN siswa s   ON p.id_siswa  = s.id_siswa
     JOIN sarana sr ON p.id_sarana = sr.id_sarana
     ORDER BY p.id_pengaduan DESC
     LIMIT 5");

layout_awal('admin', 'Dashboard Admin', 'dashboard');
?>

<div class="welcome">
    <h2>Selamat Datang, <?= e($_SESSION['nama']) ?>! 👋</h2>
    <p>Kelola data siswa, sarana, dan tanggapi pengaduan dari siswa melalui menu yang tersedia.</p>
</div>

<div class="cards">
    <div class="card"><div class="card-icon">👨‍🎓</div><p>Total Siswa</p><h2><?= $totalSiswa ?></h2></div>
    <div class="card"><div class="card-icon">📢</div><p>Total Pengaduan</p><h2><?= $totalPengaduan ?></h2></div>
    <div class="card"><div class="card-icon">⏳</div><p>Menunggu Ditangani</p><h2><?= $totalMenunggu ?></h2></div>
    <div class="card"><div class="card-icon">🏢</div><p>Total Sarana</p><h2><?= $totalSarana ?></h2></div>
</div>

<div class="box">
    <h3>Pengaduan Terbaru</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Siswa</th><th>Kelas</th><th>Sarana</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php if ($terbaru && mysqli_num_rows($terbaru) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($terbaru)): ?>
                    <tr>
                        <td><?= e($r['tanggal_pengaduan']) ?></td>
                        <td><?= e($r['nama_siswa']) ?></td>
                        <td><?= e($r['kelas']) ?></td>
                        <td><?= e($r['nama_sarana']) ?></td>
                        <td><?= badge_status($r['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="kosong">Belum ada pengaduan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="box">
    <h3>Menu Cepat</h3>
    <a href="tambah_siswa.php" class="btn btn-primary">➕ Tambah Siswa</a>
    <a href="tampil_siswa.php" class="btn btn-primary">👨‍🎓 Kelola Siswa</a>
    <a href="tampil.php" class="btn btn-primary">📢 Kelola Pengaduan</a>
    <a href="tambah_sarana.php" class="btn btn-primary">🏢 Tambah Sarana</a>
</div>

<?php layout_akhir(); ?>
