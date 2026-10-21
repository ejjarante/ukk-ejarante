-- Jalankan SEKALI di phpMyAdmin (tab SQL) pada database db_mreza_xiirpl1_paket3
-- Tujuan: menambah kolom untuk menyimpan nama file bukti foto pengaduan.
-- (Hanya perlu jika kamu TIDAK import ulang db_mreza_xiirpl1_paket3.sql versi terbaru.)

ALTER TABLE `pengaduan`
  ADD COLUMN `foto` varchar(255) DEFAULT NULL;
