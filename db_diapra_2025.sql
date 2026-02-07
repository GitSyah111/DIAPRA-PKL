-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 07 Feb 2026 pada 07.32
-- Versi server: 8.0.30
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Basis data: `db_diapra_2025`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `kadis`
--

CREATE TABLE `kadis` (
  `no` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `pangkat` varchar(255) NOT NULL,
  `NIP` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kadis`
--

INSERT INTO `kadis` (`no`, `nama`, `pangkat`, `NIP`) VALUES
(1, 'Drs. M. HELFIANNOOR, M.Si', 'Pembina Utama Muda', '19730719 199302 1 002');

-- --------------------------------------------------------

--
-- Struktur dari tabel `spj_umpeg`
--

CREATE TABLE `spj_umpeg` (
  `id` int NOT NULL,
  `nomor_urut` int NOT NULL,
  `nomor_spj` varchar(50) NOT NULL,
  `tanggal` date NOT NULL,
  `nama_kegiatan` varchar(255) NOT NULL,
  `dibuat_oleh` varchar(100) NOT NULL,
  `file_spj` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat cuti`
--

CREATE TABLE `surat cuti` (
  `id` int NOT NULL,
  `Nama/NIP` varchar(100) NOT NULL,
  `Pangkat/GOL RUANG` varchar(25) NOT NULL,
  `Jabatan` varchar(30) NOT NULL,
  `Jenis Cuti` varchar(20) NOT NULL,
  `Lamanya` varchar(11) NOT NULL,
  `Dilaksanakan DI` varchar(20) NOT NULL,
  `Mulai Cuti` int NOT NULL,
  `Sampai Dengan` int NOT NULL,
  `Sisa Cuti` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_keluar`
--

CREATE TABLE `surat_keluar` (
  `id` int NOT NULL,
  `nomor_urut` int NOT NULL,
  `nomor_surat` varchar(255) NOT NULL,
  `tujuan_surat` varchar(255) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `perihal` text NOT NULL,
  `dibuat_oleh` varchar(100) NOT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `surat_masuk`
--

CREATE TABLE `surat_masuk` (
  `id` int NOT NULL,
  `nomor_agenda` varchar(100) NOT NULL,
  `tanggal_terima` date NOT NULL,
  `alamat_pengirim` varchar(255) NOT NULL,
  `tanggal_surat` date NOT NULL,
  `nomor_surat` varchar(255) NOT NULL,
  `perihal` text NOT NULL,
  `file_surat` varchar(255) DEFAULT NULL,
  `sifat_surat` varchar(100) DEFAULT NULL,
  `tujuan_disposisi` text,
  `instruksi_disposisi` text,
  `catatan_disposisi` text,
  `status_disposisi` varchar(50) NOT NULL DEFAULT 'Belum diproses',
  `dilihat_oleh` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `no` bigint NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(10) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`no`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Admin', 'Admin', 'admin123', 'admin'),
(2, 'Bidang Dalduk', 'bidangdalduk', 'dalduk123', 'user'),
(3, 'Bidang Keluarga Berencana', 'bidangKB', 'kb123', 'user'),
(4, 'Bidang Keluarga Sejahtera', 'bidangKS', 'ks123', 'user'),
(5, 'Bidang Pemberdayaan Masyarakat', 'bidangPM', 'pm123', 'user');

--
-- Indeks untuk tabel yang dibuang
--

--
-- Indeks untuk tabel `kadis`
--
ALTER TABLE `kadis`
  ADD PRIMARY KEY (`no`);

--
-- Indeks untuk tabel `spj_umpeg`
--
ALTER TABLE `spj_umpeg`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `surat cuti`
--
ALTER TABLE `surat cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`no`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `kadis`
--
ALTER TABLE `kadis`
  MODIFY `no` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `spj_umpeg`
--
ALTER TABLE `spj_umpeg`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `surat cuti`
--
ALTER TABLE `surat cuti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `no` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
