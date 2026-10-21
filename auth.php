<?php
// Helper autentikasi & flash message. Di-include oleh semua halaman.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Escape output HTML
function e($teks): string
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, 'UTF-8');
}

// Dashboard sesuai role
function url_dashboard(string $role, string $root = ''): string
{
    return $root . ($role === 'admin'
        ? 'admin/dashboard_admin.php'
        : 'siswa/dashboard_siswa.php');
}

/**
 * Wajib login.
 * $role = 'admin' / 'siswa' -> hanya role itu yang boleh masuk.
 * $role = null              -> role apa pun boleh (asal sudah login).
 * $root = prefix menuju folder utama ('../' dari admin/ atau siswa/).
 */
function wajib_login(?string $role = null, string $root = '../'): void
{
    if (empty($_SESSION['login'])) {
        header("Location: {$root}login.php");
        exit;
    }
    if ($role !== null && ($_SESSION['role'] ?? '') !== $role) {
        // Sudah login tapi salah role -> kembalikan ke dashboard miliknya
        header("Location: " . url_dashboard($_SESSION['role'] ?? '', $root));
        exit;
    }
}

/**
 * Cek password. Mendukung hash (password_hash) dan password lama yang
 * masih teks biasa. $perlu_hash = true kalau password sebaiknya disimpan ulang
 * dalam bentuk hash.
 */
function cek_password(string $input, ?string $tersimpan, ?bool &$perlu_hash = null): bool
{
    $perlu_hash = false;

    if ($tersimpan === null || $tersimpan === '') {
        return false;
    }

    $info = password_get_info($tersimpan);
    if (!empty($info['algo'])) {
        if (!password_verify($input, $tersimpan)) {
            return false;
        }
        $perlu_hash = password_needs_rehash($tersimpan, PASSWORD_DEFAULT);
        return true;
    }

    // Password lama (belum di-hash)
    if (hash_equals($tersimpan, $input)) {
        $perlu_hash = true;
        return true;
    }
    return false;
}

// Flash message (pesan sekali tampil)
function flash_set(string $pesan, string $tipe = 'sukses'): void
{
    $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function flash_ambil(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $f = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $f;
}
