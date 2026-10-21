<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";

wajib_login('admin');

$daftarKondisi = ['Baik', 'Rusak Ringan', 'Rusak Berat'];
$error = "";

if (isset($_POST['simpan_sarana'])) {
    $nama    = trim($_POST['nama_sarana'] ?? '');
    $lokasi  = trim($_POST['lokasi'] ?? '');
    $kondisi = $_POST['kondisi'] ?? '';

    if ($nama === '' || $lokasi === '' || !in_array($kondisi, $daftarKondisi, true)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        $stmt = mysqli_prepare($koneksi, "INSERT INTO sarana (nama_sarana, lokasi, kondisi) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $nama, $lokasi, $kondisi);

        if (mysqli_stmt_execute($stmt)) {
            flash_set("Sarana $nama berhasil ditambahkan.");
            header("Location: dashboard_admin.php");
            exit;
        }
        $error = "Gagal menambahkan sarana: " . mysqli_error($koneksi);
    }
}

layout_awal('admin', 'Tambah Sarana', 'sarana');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Nama Sarana</label>
        <input type="text" name="nama_sarana" placeholder="Contoh: Proyektor, AC" required>

        <label>Lokasi Sarana</label>
        <input type="text" name="lokasi" placeholder="Contoh: Lab Komputer 1" required>

        <label>Kondisi</label>
        <select name="kondisi" required>
            <?php foreach ($daftarKondisi as $k): ?>
                <option value="<?= e($k) ?>"><?= e($k) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="form-aksi">
            <button type="submit" name="simpan_sarana" class="btn btn-primary">Simpan Sarana</button>
            <a href="dashboard_admin.php" class="btn btn-abu">Kembali</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
