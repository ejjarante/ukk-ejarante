-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Sep 2026 pada 05.37
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_mreza_xiirpl1_paket3`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '12345');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengaduan`
--

CREATE TABLE `pengaduan` (
  `id_pengaduan` int(11) NOT NULL,
  `id_siswa` int(11) NOT NULL,
  `id_sarana` int(11) NOT NULL,
  `tanggal_pengaduan` date NOT NULL,
  `isi_pengaduan` text NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'Menunggu',
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengaduan`
--

INSERT INTO `pengaduan` (`id_pengaduan`, `id_siswa`, `id_sarana`, `tanggal_pengaduan`, `isi_pengaduan`, `status`) VALUES
(1, 2, 3, '2026-09-02', 'meja rusak', 'Selesai'),
(2, 2, 2, '2026-09-01', 'Proyektor tidak dapat menyala saat digunakan untuk pembelajaran.', 'Proses'),
(3, 3, 3, '2026-09-02', 'Ac butut', 'Proses'),
(4, 4, 4, '2026-09-02', 'Toilet lantai 1 kurang bersih dan air sering tidak tersedia.', 'Selesai'),
(5, 3, 5, '2026-09-02', 'komputer rusak\r\n', 'Proses'),
(6, 6, 5, '2026-09-01', 'komputer rusak', 'Proses'),
(7, 7, 4, '2026-09-22', 'tpilet jelek', 'Proses'),
(8, 9, 6, '2026-09-16', 'Ac rusak', 'Menunggu'),
(9, 10, 1, '2026-09-09', 'kursi rusak', 'Menunggu');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sarana`
--

CREATE TABLE `sarana` (
  `id_sarana` int(11) NOT NULL,
  `nama_sarana` varchar(100) NOT NULL,
  `lokasi` varchar(100) NOT NULL,
  `kondisi` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `sarana`
--

INSERT INTO `sarana` (`id_sarana`, `nama_sarana`, `lokasi`, `kondisi`) VALUES
(1, 'Kursi Siswa', 'Ruang X RPL 1', 'Rusak'),
(2, 'Proyektor', 'Ruang XI TKJ 1', 'Rusak'),
(3, 'Meja', 'Ruang XII RPL 1', 'Tidak Berfungsi'),
(4, 'Toilet', 'Gedung Utama Lantai 1', 'Kurang Bersih'),
(5, 'Komputer', 'Laboratorium Komputer', 'Rusak'),
(6, 'Ac', 'rpl1', 'Rusak Ringan'),
(7, 'Meja', 'Ruang XII RPL 1', 'Baik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `id_siswa` int(11) NOT NULL,
  `nis` varchar(20) NOT NULL,
  `nama_siswa` varchar(100) NOT NULL,
  `kelas` varchar(20) NOT NULL,
  `jurusan` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`id_siswa`, `nis`, `nama_siswa`, `kelas`, `jurusan`) VALUES
(1, '2026001', 'Andi Pratama', 'X RPL 1', 'Rekayasa Perangkat Lunak'),
(2, '2026002', 'Roma irama', 'XI TKJ 1', 'Teknik Komputer Jaringan'),
(3, '2026003', 'Reza gaming', 'XII RPL 2', 'Rekayasa Perangkat Lunak'),
(4, '2026004', 'Reza Rante', 'XI AKL 1', 'Akuntansi dan Keuangan Lembaga'),
(5, '2026005', 'Dimas Saputra', 'X MPLB 1', 'Manajemen Perkantoran'),
(6, '12346', 'anjay mabar', 'XII RPL 1', NULL),
(7, '5555', 'ejja', 'XII PM 1', NULL),
(8, '145371473', 'prabu', 'XII AKL 1', NULL),
(9, '189637189', 'Andre', 'XII MP 1', NULL),
(10, '263489', 'Anjay', 'XII AKL 1', NULL),
(11, '872782403', 'ihsan', 'XII RPL 1', NULL),
(12, '216515', 'Robi', 'XII RPL 1', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `nama_lengkap`) VALUES
(1, 'admin', 'admin123', 'Administrator');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD PRIMARY KEY (`id_pengaduan`),
  ADD KEY `id_siswa` (`id_siswa`),
  ADD KEY `id_sarana` (`id_sarana`);

--
-- Indeks untuk tabel `sarana`
--
ALTER TABLE `sarana`
  ADD PRIMARY KEY (`id_sarana`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`id_siswa`),
  ADD UNIQUE KEY `nis` (`nis`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  MODIFY `id_pengaduan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `sarana`
--
ALTER TABLE `sarana`
  MODIFY `id_sarana` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `siswa`
--
ALTER TABLE `siswa`
  MODIFY `id_siswa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengaduan`
--
ALTER TABLE `pengaduan`
  ADD CONSTRAINT `pengaduan_ibfk_1` FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`),
  ADD CONSTRAINT `pengaduan_ibfk_2` FOREIGN KEY (`id_sarana`) REFERENCES `sarana` (`id_sarana`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
