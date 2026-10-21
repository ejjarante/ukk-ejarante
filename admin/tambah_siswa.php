<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";

wajib_login('admin');

$error = "";
$nis = $nama = $kelas = "";

if (isset($_POST['simpan'])) {
    $nis      = trim($_POST['nis'] ?? '');
    $nama     = trim($_POST['nama_siswa'] ?? '');
    $kelas    = trim($_POST['kelas'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($nis === '' || $nama === '' || $kelas === '') {
        $error = "NIS, nama, dan kelas wajib diisi.";
    } else {
        // NIS harus unik karena dipakai sebagai username login
        $cek = mysqli_prepare($koneksi, "SELECT id_siswa FROM siswa WHERE nis = ? LIMIT 1");
        mysqli_stmt_bind_param($cek, "s", $nis);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        $sudahAda = mysqli_stmt_num_rows($cek) > 0;
        mysqli_stmt_close($cek);

        if ($sudahAda) {
            $error = "NIS $nis sudah terdaftar.";
        } else {
            // Password kosong -> password awal = NIS
            $hash = password_hash($password !== '' ? $password : $nis, PASSWORD_DEFAULT);

            $stmt = mysqli_prepare($koneksi, "INSERT INTO siswa (nis, nama_siswa, kelas, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $nis, $nama, $kelas, $hash);

            if (mysqli_stmt_execute($stmt)) {
                flash_set("Siswa $nama berhasil ditambahkan.");
                header("Location: tampil_siswa.php");
                exit;
            }
            $error = "Gagal menambahkan data: " . mysqli_error($koneksi);
        }
    }
}

layout_awal('admin', 'Tambah Siswa', 'siswa');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>NIS</label>
        <input type="text" name="nis" value="<?= e($nis) ?>" required>

        <label>Nama Siswa</label>
        <input type="text" name="nama_siswa" value="<?= e($nama) ?>" required>

        <label>Kelas</label>
        <input type="text" name="kelas" value="<?= e($kelas) ?>" placeholder="Contoh: XII RPL 1" required>

        <label>Password (opsional)</label>
        <input type="text" name="password" placeholder="Kosongkan untuk memakai NIS">
        <small class="bantuan">Jika dikosongkan, password awal siswa sama dengan NIS.</small>

        <div class="form-aksi">
            <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
            <a href="tampil_siswa.php" class="btn btn-abu">Kembali</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
