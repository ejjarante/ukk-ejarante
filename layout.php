<?php
// Kerangka halaman (sidebar + navbar) yang dipakai admin & siswa.
// $root = prefix menuju folder utama ('../' dari admin/ atau siswa/, '' dari root).

function layout_awal(string $role, string $judul, string $aktif = '', string $root = '../'): void
{
    if ($role === 'admin') {
        $brand = '🏫 ADMIN';
        $menu = [
            'dashboard' => ['admin/dashboard_admin.php', '📊', 'Dashboard'],
            'siswa'     => ['admin/tampil_siswa.php',    '👨‍🎓', 'Data Siswa'],
            'pengaduan' => ['admin/tampil.php',          '📢', 'Pengaduan'],
            'sarana'    => ['admin/tambah_sarana.php',   '🏢', 'Sarana'],
            'password'  => ['ubah_password.php',         '🔑', 'Ubah Password'],
        ];
    } else {
        $brand = '🏫 SISWA';
        $menu = [
            'dashboard' => ['siswa/dashboard_siswa.php', '📊', 'Dashboard'],
            'buat'      => ['siswa/buat_pengaduan.php',  '📝', 'Buat Pengaduan'],
            'password'  => ['ubah_password.php',         '🔑', 'Ubah Password'],
        ];
    }

    $nama  = $_SESSION['nama'] ?? '';
    $flash = flash_ambil();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($judul) ?> - Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="<?= e($root) ?>assets/style.css">
</head>
<body>

<div class="sidebar">
    <h2><?= $brand ?></h2>

    <?php foreach ($menu as $kunci => $m): ?>
        <a href="<?= e($root . $m[0]) ?>" class="<?= $kunci === $aktif ? 'active' : '' ?>">
            <?= $m[1] ?> <?= e($m[2]) ?>
        </a>
    <?php endforeach; ?>

    <br>
    <a href="<?= e($root) ?>logout.php" onclick="return confirm('Yakin ingin keluar?');">🚪 Logout</a>
</div>

<div class="main">

    <div class="navbar">
        <h1><?= e($judul) ?></h1>
        <div class="user">👤 <?= e($nama) ?></div>
    </div>

    <div class="content">

    <?php if ($flash): ?>
        <div class="alert alert-<?= e($flash['tipe']) ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>

<?php
}

function layout_akhir(): void
{
    ?>
    </div>
</div>

</body>
</html>
<?php
}

// Label status pengaduan berwarna
function badge_status(string $status): string
{
    $kelas = 'badge-menunggu';
    if (in_array($status, ['Proses', 'Diproses'], true)) {
        $kelas = 'badge-proses';
    } elseif ($status === 'Selesai') {
        $kelas = 'badge-selesai';
    }
    return '<span class="badge ' . $kelas . '">' . e($status) . '</span>';
}

// Ambil satu angka COUNT dari query
function hitung($koneksi, string $sql): int
{
    $q = mysqli_query($koneksi, $sql);
    $r = $q ? mysqli_fetch_row($q) : [0];
    return (int)$r[0];
}
