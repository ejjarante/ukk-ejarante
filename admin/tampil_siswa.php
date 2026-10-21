<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";

wajib_login('admin');

$query = mysqli_query($koneksi, "SELECT id_siswa, nis, nama_siswa, kelas FROM siswa ORDER BY nama_siswa ASC");
if (!$query) {
    die("Query gagal: " . mysqli_error($koneksi));
}

layout_awal('admin', 'Data Siswa', 'siswa');
?>

<div class="box">
    <a href="tambah_siswa.php" class="btn btn-primary">+ Tambah Siswa</a>
</div>

<div class="box">
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>No</th><th>NIS</th><th>Nama</th><th>Kelas</th><th>Aksi</th></tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php if (mysqli_num_rows($query) > 0): ?>
                <?php while ($d = mysqli_fetch_assoc($query)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= e($d['nis']) ?></td>
                        <td><?= e($d['nama_siswa']) ?></td>
                        <td><?= e($d['kelas']) ?></td>
                        <td>
                            <a href="edit_siswa.php?id=<?= (int)$d['id_siswa'] ?>" class="btn btn-edit">Edit</a>
                            <a href="hapus_siswa.php?id=<?= (int)$d['id_siswa'] ?>" class="btn btn-hapus"
                               onclick="return confirm('Yakin ingin menghapus siswa ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="5" class="kosong">Belum ada data siswa.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php layout_akhir(); ?>
