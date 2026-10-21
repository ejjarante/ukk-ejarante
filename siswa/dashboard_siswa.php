<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";
require "../upload.php";

wajib_login('siswa');

$id_siswa = (int)$_SESSION['id_siswa'];

// Ringkasan pengaduan milik siswa yang sedang login
$stmt = mysqli_prepare($koneksi,
    "SELECT COUNT(*) AS total,
            COALESCE(SUM(status = 'Menunggu'), 0) AS menunggu,
            COALESCE(SUM(status IN ('Proses', 'Diproses')), 0) AS proses,
            COALESCE(SUM(status = 'Selesai'), 0) AS selesai
     FROM pengaduan WHERE id_siswa = ?");
mysqli_stmt_bind_param($stmt, "i", $id_siswa);
mysqli_stmt_execute($stmt);
$ringkas = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

// Riwayat pengaduan milik siswa
$stmt = mysqli_prepare($koneksi,
    "SELECT p.tanggal_pengaduan, sr.nama_sarana, sr.lokasi, p.isi_pengaduan, p.foto, p.status
     FROM pengaduan p
     JOIN sarana sr ON p.id_sarana = sr.id_sarana
     WHERE p.id_siswa = ?
     ORDER BY p.id_pengaduan DESC");
mysqli_stmt_bind_param($stmt, "i", $id_siswa);
mysqli_stmt_execute($stmt);
$riwayat = mysqli_stmt_get_result($stmt);

layout_awal('siswa', 'Dashboard Siswa', 'dashboard');
?>

<div class="welcome">
    <h2>Halo, <?= e($_SESSION['nama']) ?>! 👋</h2>
    <p>NIS: <?= e($_SESSION['nis']) ?> &nbsp;|&nbsp; Kelas: <?= e($_SESSION['kelas']) ?></p>
    <p style="margin-top:8px">Laporkan sarana sekolah yang rusak dan pantau perkembangannya di sini.</p>
</div>

<div class="cards">
    <div class="card"><div class="card-icon">📢</div><p>Total Pengaduan</p><h2><?= (int)$ringkas['total'] ?></h2></div>
    <div class="card"><div class="card-icon">⏳</div><p>Menunggu</p><h2><?= (int)$ringkas['menunggu'] ?></h2></div>
    <div class="card"><div class="card-icon">🔧</div><p>Diproses</p><h2><?= (int)$ringkas['proses'] ?></h2></div>
    <div class="card"><div class="card-icon">✅</div><p>Selesai</p><h2><?= (int)$ringkas['selesai'] ?></h2></div>
</div>

<div class="box">
    <a href="buat_pengaduan.php" class="btn btn-primary">📝 Buat Pengaduan Baru</a>
</div>

<div class="box">
    <h3>Riwayat Pengaduan Saya</h3>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No</th><th>Tanggal</th><th>Sarana</th><th>Lokasi</th><th>Isi Pengaduan</th><th>Bukti</th><th>Status</th></tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php if (mysqli_num_rows($riwayat) > 0): ?>
                <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($r['tanggal_pengaduan']) ?></td>
                        <td><?= e($r['nama_sarana']) ?></td>
                        <td><?= e($r['lokasi']) ?></td>
                        <td><?= e($r['isi_pengaduan']) ?></td>
                        <td><?= tampil_bukti($r['foto']) ?></td>
                        <td><?= badge_status($r['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="kosong">Kamu belum pernah membuat pengaduan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php layout_akhir(); ?>
