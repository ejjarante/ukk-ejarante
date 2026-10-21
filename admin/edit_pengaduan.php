<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";
require "../upload.php";

wajib_login('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: tampil.php");
    exit;
}

$daftarStatus = ['Menunggu', 'Proses', 'Selesai'];

$stmt = mysqli_prepare($koneksi,
    "SELECT p.*, s.nama_siswa, s.kelas, sr.nama_sarana, sr.lokasi
     FROM pengaduan p
     JOIN siswa s   ON p.id_siswa  = s.id_siswa
     JOIN sarana sr ON p.id_sarana = sr.id_sarana
     WHERE p.id_pengaduan = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data) {
    flash_set("Data pengaduan tidak ditemukan.", "error");
    header("Location: tampil.php");
    exit;
}

// Data lama yang statusnya "Diproses" dianggap "Proses"
if ($data['status'] === 'Diproses') {
    $data['status'] = 'Proses';
}

$error = "";

if (isset($_POST['update'])) {
    $tanggal = $_POST['tanggal_pengaduan'] ?? '';
    $isi     = trim($_POST['isi_pengaduan'] ?? '');
    $status  = $_POST['status'] ?? '';

    if ($tanggal === '' || $isi === '' || !in_array($status, $daftarStatus, true)) {
        $error = "Tanggal, isi pengaduan, dan status wajib diisi dengan benar.";
    } else {
        // Hanya tabel pengaduan yang diubah; data siswa & sarana tidak disentuh
        $up = mysqli_prepare($koneksi,
            "UPDATE pengaduan SET tanggal_pengaduan = ?, isi_pengaduan = ?, status = ? WHERE id_pengaduan = ?");
        mysqli_stmt_bind_param($up, "sssi", $tanggal, $isi, $status, $id);

        if (mysqli_stmt_execute($up)) {
            flash_set("Data pengaduan berhasil diperbarui.");
            header("Location: tampil.php");
            exit;
        }
        $error = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }

    $data['tanggal_pengaduan'] = $tanggal;
    $data['isi_pengaduan']     = $isi;
    $data['status']            = $status;
}

layout_awal('admin', 'Edit Pengaduan', 'pengaduan');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Siswa</label>
        <input type="text" value="<?= e($data['nama_siswa'] . ' (' . $data['kelas'] . ')') ?>" readonly>

        <label>Sarana</label>
        <input type="text" value="<?= e($data['nama_sarana'] . ' - ' . $data['lokasi']) ?>" readonly>

        <?php if (!empty($data['foto'])): ?>
            <label>Bukti Foto</label>
            <?= str_replace('class="thumb"', 'class="thumb-besar"', tampil_bukti($data['foto'])) ?>
        <?php endif; ?>

        <label>Tanggal Pengaduan</label>
        <input type="date" name="tanggal_pengaduan" value="<?= e($data['tanggal_pengaduan']) ?>" required>

        <label>Isi Pengaduan</label>
        <textarea name="isi_pengaduan" rows="4" required><?= e($data['isi_pengaduan']) ?></textarea>

        <label>Status</label>
        <select name="status" required>
            <?php foreach ($daftarStatus as $st): ?>
                <option value="<?= e($st) ?>" <?= $data['status'] === $st ? 'selected' : '' ?>><?= e($st) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-aksi">
            <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
            <a href="tampil.php" class="btn btn-abu">Batal</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
