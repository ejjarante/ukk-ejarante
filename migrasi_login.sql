-- Jalankan SEKALI di phpMyAdmin (tab SQL) pada database db_mreza_xiirpl1_paket3
-- Tujuan: menyiapkan login siswa & merapikan data lama.

-- 1. Kolom password untuk siswa (NULL = password awal sama dengan NIS,
--    otomatis di-hash saat siswa pertama kali login)
ALTER TABLE `siswa`
  ADD COLUMN `password` varchar(255) DEFAULT NULL AFTER `jurusan`;

-- 2. Samakan penulisan status pengaduan ('Diproses' -> 'Proses')
UPDATE `pengaduan` SET `status` = 'Proses' WHERE `status` = 'Diproses';

-- 3. NIS harus unik karena dipakai sebagai username login
--    (jika error "Duplicate entry", ada NIS ganda: perbaiki dulu datanya)
ALTER TABLE `siswa` ADD UNIQUE KEY `nis` (`nis`);
