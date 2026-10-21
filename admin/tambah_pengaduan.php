<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";
require "../upload.php";

wajib_login('admin');

$daftarStatus = ['Menunggu', 'Proses', 'Selesai'];
$error = "";

$siswaList  = mysqli_query($koneksi, "SELECT id_siswa, nis, nama_siswa, kelas FROM siswa ORDER BY nama_siswa ASC");
$saranaList = mysqli_query($koneksi, "SELECT id_sarana, nama_sarana, lokasi FROM sarana ORDER BY nama_sarana ASC");

if (isset($_POST['simpan'])) {
    $id_siswa  = (int)($_POST['id_siswa'] ?? 0);
    $id_sarana = (int)($_POST['id_sarana'] ?? 0);
    $tanggal   = $_POST['tanggal_pengaduan'] ?? '';
    $isi       = trim($_POST['isi_pengaduan'] ?? '');
    $status    = $_POST['status'] ?? 'Menunggu';

    if (!in_array($status, $daftarStatus, true)) {
        $status = 'Menunggu';
    }

    if ($id_siswa <= 0 || $id_sarana <= 0 || $tanggal === '' || $isi === '') {
        $error = "Semua kolom wajib diisi.";
    } else {
        $foto = simpan_bukti($_FILES['foto'] ?? [], $error);

        if ($error === "") {
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO pengaduan (id_siswa, id_sarana, tanggal_pengaduan, isi_pengaduan, foto, status) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iissss", $id_siswa, $id_sarana, $tanggal, $isi, $foto, $status);

            if (mysqli_stmt_execute($stmt)) {
                flash_set("Pengaduan berhasil ditambahkan.");
                header("Location: tampil.php");
                exit;
            }
            hapus_bukti($foto);
            $error = "Gagal menambahkan pengaduan: " . mysqli_error($koneksi);
        }
    }
}

layout_awal('admin', 'Tambah Pengaduan', 'pengaduan');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Siswa</label>
        <select name="id_siswa" required>
            <option value="">-- Pilih Siswa --</option>
            <?php while ($s = mysqli_fetch_assoc($siswaList)): ?>
                <option value="<?= (int)$s['id_siswa'] ?>">
                    <?= e($s['nama_siswa'] . ' - ' . $s['nis'] . ' (' . $s['kelas'] . ')') ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Sarana / Fasilitas</label>
        <select name="id_sarana" required>
            <option value="">-- Pilih Sarana --</option>
            <?php while ($sr = mysqli_fetch_assoc($saranaList)): ?>
                <option value="<?= (int)$sr['id_sarana'] ?>">
                    <?= e($sr['nama_sarana'] . ' (Lokasi: ' . $sr['lokasi'] . ')') ?>
                </option>
            <?php endwhile; ?>
        </select>
        <small class="bantuan"><a href="tambah_sarana.php">+ Tambah sarana baru</a></small>

        <label>Tanggal Pengaduan</label>
        <input type="date" name="tanggal_pengaduan" value="<?= date('Y-m-d') ?>" required>

        <label>Isi Pengaduan</label>
        <textarea name="isi_pengaduan" rows="4" placeholder="Tuliskan keluhan..." required></textarea>

        <label>Bukti Foto (opsional)</label>
        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
        <small class="bantuan">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>

        <label>Status</label>
        <select name="status">
            <?php foreach ($daftarStatus as $st): ?>
                <option value="<?= e($st) ?>"><?= e($st) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-aksi">
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Pengaduan</button>
            <a href="tampil.php" class="btn btn-abu">Batal</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
