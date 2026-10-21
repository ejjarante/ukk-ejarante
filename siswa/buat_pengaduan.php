<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";
require "../upload.php";

wajib_login('siswa');

$id_siswa = (int)$_SESSION['id_siswa'];
$error = "";

$saranaList = mysqli_query($koneksi, "SELECT id_sarana, nama_sarana, lokasi FROM sarana ORDER BY nama_sarana ASC");

if (isset($_POST['simpan'])) {
    $id_sarana = (int)($_POST['id_sarana'] ?? 0);
    $tanggal   = $_POST['tanggal_pengaduan'] ?? '';
    $isi       = trim($_POST['isi_pengaduan'] ?? '');

    if ($id_sarana <= 0 || $tanggal === '' || $isi === '') {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Upload foto bukti (opsional)
        $foto = simpan_bukti($_FILES['foto'] ?? [], $error);

        if ($error === "") {
            // Identitas siswa diambil dari sesi login, status awal selalu "Menunggu"
            $status = 'Menunggu';
            $stmt = mysqli_prepare($koneksi,
                "INSERT INTO pengaduan (id_siswa, id_sarana, tanggal_pengaduan, isi_pengaduan, foto, status) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iissss", $id_siswa, $id_sarana, $tanggal, $isi, $foto, $status);

            if (mysqli_stmt_execute($stmt)) {
                flash_set("Pengaduan berhasil dikirim. Silakan pantau statusnya di dashboard.");
                header("Location: dashboard_siswa.php");
                exit;
            }
            hapus_bukti($foto); // batalkan foto jika data gagal tersimpan
            $error = "Gagal mengirim pengaduan: " . mysqli_error($koneksi);
        }
    }
}

layout_awal('siswa', 'Buat Pengaduan', 'buat');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>Pelapor</label>
        <input type="text" value="<?= e($_SESSION['nama'] . ' (' . $_SESSION['kelas'] . ')') ?>" readonly>

        <label>Sarana / Fasilitas</label>
        <select name="id_sarana" required>
            <option value="">-- Pilih Sarana --</option>
            <?php while ($sr = mysqli_fetch_assoc($saranaList)): ?>
                <option value="<?= (int)$sr['id_sarana'] ?>">
                    <?= e($sr['nama_sarana'] . ' (Lokasi: ' . $sr['lokasi'] . ')') ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Tanggal Pengaduan</label>
        <input type="date" name="tanggal_pengaduan" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" required>

        <label>Isi Pengaduan</label>
        <textarea name="isi_pengaduan" rows="4" placeholder="Jelaskan kerusakan atau keluhan..." required></textarea>

        <label>Bukti Foto (opsional)</label>
        <input type="file" name="foto" accept="image/jpeg,image/png,image/webp">
        <small class="bantuan">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</small>

        <div class="form-aksi">
            <button type="submit" name="simpan" class="btn btn-primary">Kirim Pengaduan</button>
            <a href="dashboard_siswa.php" class="btn btn-abu">Batal</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
