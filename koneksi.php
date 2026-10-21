<?php
// Konfigurasi koneksi database (satu file untuk seluruh aplikasi)
$host    = "localhost";
$user    = "root";
$pass    = "";
$db_name = "db_mreza_xiirpl1_paket3";

$koneksi = mysqli_connect($host, $user, $pass, $db_name);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
