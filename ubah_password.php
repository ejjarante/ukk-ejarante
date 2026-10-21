<?php
require "auth.php";
require "koneksi.php";
require "layout.php";

wajib_login(null, '');

$role  = $_SESSION['role'];
$error = "";

if (isset($_POST['simpan'])) {
    $lama   = $_POST['password_lama'] ?? '';
    $baru   = $_POST['password_baru'] ?? '';
    $ulangi = $_POST['password_ulangi'] ?? '';

    if ($role === 'admin') {
        $sql = "SELECT password FROM admin WHERE id = ?";
        $idUser = (int)$_SESSION['id_admin'];
    } else {
        $sql = "SELECT password, nis FROM siswa WHERE id_siswa = ?";
        $idUser = (int)$_SESSION['id_siswa'];
    }

    $stmt = mysqli_prepare($koneksi, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idUser);
    mysqli_stmt_execute($stmt);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);

    $tersimpan = $data['password'] ?? null;
    if ($role === 'siswa' && ($tersimpan === null || $tersimpan === '')) {
        $tersimpan = $data['nis'] ?? null; // password awal = NIS
    }

    if (!$data || !cek_password($lama, $tersimpan)) {
        $error = "Password lama salah.";
    } elseif (strlen($baru) < 5) {
        $error = "Password baru minimal 5 karakter.";
    } elseif ($baru !== $ulangi) {
        $error = "Konfirmasi password baru tidak sama.";
    } else {
        $hash = password_hash($baru, PASSWORD_DEFAULT);

        if ($role === 'admin') {
            $up = mysqli_prepare($koneksi, "UPDATE admin SET password = ? WHERE id = ?");
        } else {
            $up = mysqli_prepare($koneksi, "UPDATE siswa SET password = ? WHERE id_siswa = ?");
        }
        mysqli_stmt_bind_param($up, "si", $hash, $idUser);

        if (mysqli_stmt_execute($up)) {
            flash_set("Password berhasil diubah.");
            header("Location: " . url_dashboard($role, ''));
            exit;
        }
        $error = "Gagal mengubah password: " . mysqli_error($koneksi);
    }
}

layout_awal($role, 'Ubah Password', 'password', '');
?>

<div class="box box-form">
    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <label>Password Lama</label>
        <input type="password" name="password_lama" required>

        <label>Password Baru</label>
        <input type="password" name="password_baru" required>

        <label>Ulangi Password Baru</label>
        <input type="password" name="password_ulangi" required>

        <div class="form-aksi">
            <button type="submit" name="simpan" class="btn btn-primary">Simpan Password</button>
            <a href="<?= e(url_dashboard($role, '')) ?>" class="btn btn-abu">Batal</a>
        </div>
    </form>
</div>

<?php layout_akhir(); ?>
