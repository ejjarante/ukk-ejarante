<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";
require "../upload.php";

wajib_login('admin');

$sql = "SELECT p.id_pengaduan, s.nis, s.nama_siswa, s.kelas,
               sr.nama_sarana, sr.lokasi, sr.kondisi,
               p.tanggal_pengaduan, p.isi_pengaduan, p.foto, p.status
        FROM pengaduan p
        JOIN siswa s   ON p.id_siswa  = s.id_siswa
        JOIN sarana sr ON p.id_sarana = sr.id_sarana
        ORDER BY p.id_pengaduan DESC";

$query = mysqli_query($koneksi, $sql);
if (!$query) {
    die("Query error: " . mysqli_error($koneksi));
}

layout_awal('admin', 'Data Pengaduan', 'pengaduan');
?>

<div class="box">
    <a href="tambah_pengaduan.php" class="btn btn-primary">+ Tambah Pengaduan</a>
    <a href="tambah_siswa.php" class="btn btn-primary">+ Tambah Siswa</a>
    <a href="tambah_sarana.php" class="btn btn-primary">+ Tambah Sarana</a>
</div>

<div class="box">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>No</th><th>NIS</th><th>Nama Siswa</th><th>Kelas</th><th>Sarana</th>
                    <th>Lokasi</th><th>Kondisi</th><th>Tanggal</th><th>Isi Pengaduan</th><th>Bukti</th>
                    <th>Status</th><th>Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($row['nis']) ?></td>
                        <td><?= e($row['nama_siswa']) ?></td>
                        <td><?= e($row['kelas']) ?></td>
                        <td><?= e($row['nama_sarana']) ?></td>
                        <td><?= e($row['lokasi']) ?></td>
                        <td><?= e($row['kondisi']) ?></td>
                        <td><?= e($row['tanggal_pengaduan']) ?></td>
                        <td><?= e($row['isi_pengaduan']) ?></td>
                        <td><?= tampil_bukti($row['foto']) ?></td>
                        <td><?= badge_status($row['status']) ?></td>
                        <td>
                            <a href="edit_pengaduan.php?id=<?= (int)$row['id_pengaduan'] ?>" class="btn btn-edit">Edit</a>
                            <a href="hapus_pengaduan.php?id=<?= (int)$row['id_pengaduan'] ?>" class="btn btn-hapus"
                               onclick="return confirm('Yakin ingin menghapus data pengaduan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="12" class="kosong">Belum ada data pengaduan.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php layout_akhir(); ?>
