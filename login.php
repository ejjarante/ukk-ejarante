<?php
require "auth.php";
require "koneksi.php";

// Sudah login -> langsung ke dashboard masing-masing
if (!empty($_SESSION['login'])) {
    header("Location: " . url_dashboard($_SESSION['role'] ?? '', ''));
    exit;
}

$error = "";
$role  = $_POST['role'] ?? 'siswa';
if (!in_array($role, ['admin', 'siswa'], true)) {
    $role = 'siswa';
}
$username = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === "" || $password === "") {
        $error = "Username/NIS dan password wajib diisi!";
    } else {

        $berhasil = false;

        if ($role === 'admin') {

            $stmt = mysqli_prepare($koneksi, "SELECT id, username, password FROM admin WHERE username = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($data && cek_password($password, $data['password'], $perlu_hash)) {
                if ($perlu_hash) {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $up = mysqli_prepare($koneksi, "UPDATE admin SET password = ? WHERE id = ?");
                    mysqli_stmt_bind_param($up, "si", $hash, $data['id']);
                    mysqli_stmt_execute($up);
                    mysqli_stmt_close($up);
                }

                session_regenerate_id(true);
                $_SESSION['login']    = true;
                $_SESSION['role']     = 'admin';
                $_SESSION['id_admin'] = $data['id'];
                $_SESSION['nama']     = $data['username'];
                $berhasil = true;
            }

        } else {

            $stmt = mysqli_prepare($koneksi, "SELECT id_siswa, nis, nama_siswa, kelas, password FROM siswa WHERE nis = ? LIMIT 1");
            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            $data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);

            if ($data) {
                // Password awal siswa = NIS (jika belum pernah diatur)
                $tersimpan = ($data['password'] === null || $data['password'] === '')
                    ? $data['nis']
                    : $data['password'];

                if (cek_password($password, $tersimpan, $perlu_hash)) {
                    if ($perlu_hash) {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $up = mysqli_prepare($koneksi, "UPDATE siswa SET password = ? WHERE id_siswa = ?");
                        mysqli_stmt_bind_param($up, "si", $hash, $data['id_siswa']);
                        mysqli_stmt_execute($up);
                        mysqli_stmt_close($up);
                    }

                    session_regenerate_id(true);
                    $_SESSION['login']    = true;
                    $_SESSION['role']     = 'siswa';
                    $_SESSION['id_siswa'] = $data['id_siswa'];
                    $_SESSION['nis']      = $data['nis'];
                    $_SESSION['nama']     = $data['nama_siswa'];
                    $_SESSION['kelas']    = $data['kelas'];
                    $berhasil = true;
                }
            }
        }

        if ($berhasil) {
            header("Location: " . url_dashboard($role, ''));
            exit;
        }

        // Pesan sengaja dibuat umum agar tidak membocorkan username yang valid
        $error = "Username/NIS atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="login-page">

<div class="login-box">

    <div class="logo">🏫</div>
    <h2>Pengaduan Sarana Sekolah</h2>
    <p class="subtitle">Silakan login untuk melanjutkan</p>

    <?php $flash = flash_ambil(); ?>
    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['tipe']) ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>

    <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">

        <div class="role-switch">
            <label>
                <input type="radio" name="role" value="siswa" <?= $role === 'siswa' ? 'checked' : '' ?>>
                <span>👨‍🎓 Siswa</span>
            </label>
            <label>
                <input type="radio" name="role" value="admin" <?= $role === 'admin' ? 'checked' : '' ?>>
                <span>🛠️ Admin</span>
            </label>
        </div>

        <label id="label-user" for="username">Username / NIS</label>
        <input type="text" id="username" name="username"
               value="<?= e($username) ?>" placeholder="Masukkan NIS (siswa) / username (admin)" required autofocus>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Masukkan password" required>

        <button type="submit" name="login" class="btn btn-primary btn-block">LOGIN</button>

    </form>

    <p class="hint">Siswa belum punya akun? <a href="register.php">Daftar di sini</a></p>
    <p class="hint">Siswa yang sudah didaftarkan admin: password awal = NIS.</p>

</div>

<script>
    // Ubah label & placeholder sesuai role yang dipilih
    var radios = document.querySelectorAll('input[name="role"]');
    var label  = document.getElementById('label-user');
    var input  = document.getElementById('username');

    function ubahRole() {
        var admin = document.querySelector('input[name="role"]:checked').value === 'admin';
        label.textContent = admin ? 'Username' : 'NIS';
        input.placeholder = admin ? 'Masukkan username admin' : 'Masukkan NIS';
    }
    radios.forEach(function (r) { r.addEventListener('change', ubahRole); });
    ubahRole();
</script>

</body>
</html>
