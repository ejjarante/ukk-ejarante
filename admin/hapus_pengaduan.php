<?php
require "../auth.php";
require "../koneksi.php";
require "../upload.php";

wajib_login('admin');

$id = (int)($_GET['id'] ?? 0);

if ($id > 0) {
    // Ambil nama foto dulu supaya file ikut terhapus
    $cek = mysqli_prepare($koneksi, "SELECT foto FROM pengaduan WHERE id_pengaduan = ?");
    mysqli_stmt_bind_param($cek, "i", $id);
    mysqli_stmt_execute($cek);
    $baris = mysqli_fetch_assoc(mysqli_stmt_get_result($cek));
    mysqli_stmt_close($cek);

    $stmt = mysqli_prepare($koneksi, "DELETE FROM pengaduan WHERE id_pengaduan = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        hapus_bukti($baris['foto'] ?? null);
        flash_set("Data pengaduan berhasil dihapus.");
    } else {
        flash_set("Gagal menghapus data: " . mysqli_error($koneksi), "error");
    }
    mysqli_stmt_close($stmt);
}

header("Location: tampil.php");
exit;
