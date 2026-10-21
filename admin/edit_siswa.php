<?php
require "../auth.php";
require "../koneksi.php";
require "../layout.php";

wajib_login('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: tampil_siswa.php");
    exit;
}

$stmt = mysqli_prepare($koneksi, "SELECT id_siswa, nis, nama_siswa, kelas FROM siswa WHERE id_siswa = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$data) {
    flash_set("Data siswa tidak ditemukan.", "error");
    header("Location: tampil_siswa.php");
    exit;
}

$error = "";

if (isset($_POST['update'])) {
    $nis      = trim($_POST['nis'] ?? '');
    $nama     = trim($_POST['nama_siswa'] ?? '');
    $kelas    = trim($_POST['kelas'] ?? '');
    $password = $_POST['password'] ?? '';

    $data['nis'] = $nis;
    $data['nama_siswa'] = $nama;
    $data['kelas'] = $kelas;

    if ($nis === '' || $nama === '' || $kelas === '') {
        $error = "NIS, nama, dan kelas wajib diisi.";
    } else {
        // NIS tidak boleh sama dengan siswa lain
        $cek = mysqli_prepare($koneksi, "SELECT id_siswa FROM siswa WHERE nis = ? AND id_siswa <> ? LIMIT 1");
        mysqli_stmt_bind_param($cek, "si", $nis, $id);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        $bentrok = mysqli_stmt_num_rows($cek) > 0;
        mysqli_stmt_close($cek);

        if ($bentrok) {
            $error = "NIS $nis sudah dipakai siswa lain.";
        } else {
            if ($password !== '') {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $up = mysqli_prepare($koneksi, "UPDATE siswa SET nis = ?, nama_siswa = ?, kelas = ?, password = ? WHERE id_siswa = ?");
                mysqli_stmt_bind_param($up, "ssssi", $nis, $nama, $kelas, $hash, $id);
            } else {
                $up = mysqli_prepare($koneksi, "UPDATE siswa SET nis = ?, nama_siswa = ?, kelas = ? WHERE id_siswa = ?");
                mysqli_stmt_bind_param($up, "sssi", $nis, $nama, $kelas, $id);
            }

            if (mysqli_stmt_execute($up)) {
                flash_set("Data siswa berhasil diperbarui.");
                header("Location: tampil_siswa.php");
                exit;
            }
            $error = "Gagal mengupdate data: " . mysqli_error($koneksi);
        }
    }
}

layout_awal('admin', 'Edit Siswa', 'siswa');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>NIS</label>
        <input type="text" name="nis" value="<?= e($data['nis']) ?>" required>

        <label>Nama Siswa</label>
        <input type="text" name="nama_siswa" value="<?= e($data['nama_siswa']) ?>" required>

        <label>Kelas</label>
        <input type="text" name="kelas" value="<?= e($data['kelas']) ?>" required>

        <label>Reset Password (opsional)</label>
        <input type="text" name="password" placeholder="Kosongkan jika tidak diubah">

        <div class="form-aksi">
            <button type="submit" name="update" class="btn btn-edit">Update</button>
            <a href="tampil_siswa.php" class="btn btn-abu">Kembali</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
