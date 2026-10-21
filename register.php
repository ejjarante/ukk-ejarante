<?php
require "auth.php";
require "koneksi.php";

// Sudah login -> tidak perlu daftar lagi
if (!empty($_SESSION['login'])) {
    header("Location: " . url_dashboard($_SESSION['role'] ?? '', ''));
    exit;
}

$error = "";
$nis = $nama = $kelas = "";

if (isset($_POST['register'])) {
    $nis      = trim($_POST['nis'] ?? '');
    $nama     = trim($_POST['nama_siswa'] ?? '');
    $kelas    = trim($_POST['kelas'] ?? '');
    $password = $_POST['password'] ?? '';
    $ulangi   = $_POST['password_ulangi'] ?? '';

    if ($nis === '' || $nama === '' || $kelas === '' || $password === '') {
        $error = "Semua kolom wajib diisi.";
    } elseif (!preg_match('/^[0-9]{4,20}$/', $nis)) {
        $error = "NIS harus berupa angka (4-20 digit).";
    } elseif (mb_strlen($nama) > 100 || mb_strlen($kelas) > 20) {
        $error = "Nama maksimal 100 karakter dan kelas maksimal 20 karakter.";
    } elseif (strlen($password) < 5) {
        $error = "Password minimal 5 karakter.";
    } elseif ($password !== $ulangi) {
        $error = "Konfirmasi password tidak sama.";
    } else {
        // NIS dipakai sebagai username, jadi tidak boleh ganda
        $cek = mysqli_prepare($koneksi, "SELECT id_siswa FROM siswa WHERE nis = ? LIMIT 1");
        mysqli_stmt_bind_param($cek, "s", $nis);
        mysqli_stmt_execute($cek);
        mysqli_stmt_store_result($cek);
        $sudahAda = mysqli_stmt_num_rows($cek) > 0;
        mysqli_stmt_close($cek);

        if ($sudahAda) {
            $error = "NIS $nis sudah terdaftar. Silakan login, atau hubungi admin jika lupa password.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);

            try {
                $stmt = mysqli_prepare($koneksi, "INSERT INTO siswa (nis, nama_siswa, kelas, password) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "ssss", $nis, $nama, $kelas, $hash);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                flash_set("Pendaftaran berhasil! Silakan login memakai NIS dan password yang baru dibuat.");
                header("Location: login.php");
                exit;
            } catch (mysqli_sql_exception $ex) {
                $error = ($ex->getCode() == 1062)
                    ? "NIS $nis sudah terdaftar."
                    : "Pendaftaran gagal: " . $ex->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Siswa - Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">

<div class="login-box">

    <div class="logo">📝</div>
    <h2>Daftar Akun Siswa</h2>
    <p class="subtitle">Buat akun untuk mengirim pengaduan sarana sekolah</p>

    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <label for="nis">NIS</label>
        <input type="text" id="nis" name="nis" value="<?= e($nis) ?>" inputmode="numeric" placeholder="Contoh: 2026010" required autofocus>

        <label for="nama_siswa">Nama Lengkap</label>
        <input type="text" id="nama_siswa" name="nama_siswa" value="<?= e($nama) ?>" maxlength="100" required>

        <label for="kelas">Kelas</label>
        <input type="text" id="kelas" name="kelas" value="<?= e($kelas) ?>" maxlength="20" placeholder="Contoh: XII RPL 1" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Minimal 5 karakter" required>

        <label for="password_ulangi">Ulangi Password</label>
        <input type="password" id="password_ulangi" name="password_ulangi" required>

        <button type="submit" name="register" class="btn btn-primary btn-block">DAFTAR</button>

    </form>

    <p class="hint">Sudah punya akun? <a href="login.php">Login di sini</a></p>

</div>

</body>
</html>
