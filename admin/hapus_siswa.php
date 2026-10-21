<?php
require "../auth.php";
require "../koneksi.php";

wajib_login('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: tampil_siswa.php");
    exit;
}

// Siswa yang masih punya pengaduan tidak bisa dihapus (foreign key)
$cek = mysqli_prepare($koneksi, "SELECT COUNT(*) FROM pengaduan WHERE id_siswa = ?");
mysqli_stmt_bind_param($cek, "i", $id);
mysqli_stmt_execute($cek);
mysqli_stmt_bind_result($cek, $jumlah);
mysqli_stmt_fetch($cek);
mysqli_stmt_close($cek);

if ($jumlah > 0) {
    flash_set("Siswa tidak bisa dihapus karena masih memiliki $jumlah pengaduan. Hapus pengaduannya terlebih dahulu.", "error");
} else {
    $stmt = mysqli_prepare($koneksi, "DELETE FROM siswa WHERE id_siswa = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    if (mysqli_stmt_execute($stmt)) {
        flash_set("Data siswa berhasil dihapus.");
    } else {
        flash_set("Gagal menghapus data: " . mysqli_error($koneksi), "error");
    }
    mysqli_stmt_close($stmt);
}

header("Location: tampil_siswa.php");
exit;
