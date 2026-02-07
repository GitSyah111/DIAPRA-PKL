-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 20, 2026 at 08:20 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `si_surat`
--

-- --------------------------------------------------------

--
-- Table structure for table `kadis`
--

CREATE TABLE `kadis` (
  `no` int NOT NULL,
  `nama` varchar(255) NOT NULL,
  `pangkat` varchar(255) NOT NULL,
  `NIP` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kadis`
--

INSERT INTO `kadis` (`no`, `nama`, `pangkat`, `NIP`) VALUES
(1, 'Drs. M. HELFIANNOOR, M.Si', 'Pembina Utama Muda', '19730719 199302 1 002');

-- --------------------------------------------------------

--
-- Table structure for table `spj_umpeg`
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

--
-- Dumping data for table `spj_umpeg`
--

INSERT INTO `spj_umpeg` (`id`, `nomor_urut`, `nomor_spj`, `tanggal`, `nama_kegiatan`, `dibuat_oleh`, `file_spj`) VALUES
(1, 1, 'SPJ/001/UMPEG/2025', '2025-11-24', 'Permohonan Persetujuan Pelaksanaan Pembayaran Belanja Surat Kabar', 'Admin', 'spj_1764033421_6925038da800e.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `surat cuti`
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
-- Table structure for table `surat_keluar`
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

--
-- Dumping data for table `surat_keluar`
--

INSERT INTO `surat_keluar` (`id`, `nomor_urut`, `nomor_surat`, `tujuan_surat`, `tanggal_surat`, `perihal`, `dibuat_oleh`, `file_surat`, `created_at`) VALUES
(1, 1, ' 400.13/1799/KS/DPPKBPM-BJM/XI/2025', 'Terlampir', '2025-11-12', 'Rapat Koordinasi dalam Rangka Persiapan Expose Hasil Survei Indeks Pembangunan Keluarga (Ibangga), Tahun Anggaran 2025 ', 'Admin', '69266a6e0d4be_1764125294.pdf', '2025-11-26 02:48:14'),
(2, 2, '900.1.3.5/128-Sekr/DPPKBPM-BJM//2025', 'Kepala DPPKBPM', '2025-11-25', 'Permohonan Persetujuan Pelaksanaan Pembayaran Jasa Pelayanan Umum Kantor', 'Admin', '69279b77a199c_1764203383.pdf', '2025-11-27 00:29:43');

-- --------------------------------------------------------

--
-- Table structure for table `surat_masuk`
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

--
-- Dumping data for table `surat_masuk`
--

INSERT INTO `surat_masuk` (`id`, `nomor_agenda`, `tanggal_terima`, `alamat_pengirim`, `tanggal_surat`, `nomor_surat`, `perihal`, `file_surat`, `sifat_surat`, `tujuan_disposisi`, `instruksi_disposisi`, `catatan_disposisi`, `status_disposisi`, `dilihat_oleh`, `created_at`) VALUES
(5, '1', '2025-11-24', 'Sekretariat Daerah Kota Banjarmasin', '2025-11-05', '000.8.2./ 357 /ORG', 'Rapat Koordinasi Pembahasan Usulan Peta Jabatan dan Penyesuaian Informasi Jabatan', 'surat_1763967031_6924003718c3e.pdf', NULL, NULL, NULL, NULL, 'Belum diproses', '', '2025-11-24 06:50:31');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `no` bigint NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(10) NOT NULL,
  `role` enum('admin','user') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`no`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Admin', 'Admin', 'admin123', 'admin'),
(2, 'Bidang Dalduk', 'bidangdalduk', 'dalduk123', 'user'),
(3, 'Bidang Keluarga Berencana', 'bidangKB', 'kb123', 'user'),
(4, 'Bidang Keluarga Sejahtera', 'bidangKS', 'ks123', 'user'),
(5, 'Bidang Pemberdayaan Masyarakat', 'bidangPM', 'pm123', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kadis`
--
ALTER TABLE `kadis`
  ADD PRIMARY KEY (`no`);

--
-- Indexes for table `spj_umpeg`
--
ALTER TABLE `spj_umpeg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat cuti`
--
ALTER TABLE `surat cuti`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kadis`
--
ALTER TABLE `kadis`
  MODIFY `no` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `spj_umpeg`
--
ALTER TABLE `spj_umpeg`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `surat cuti`
--
ALTER TABLE `surat cuti`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `surat_keluar`
--
ALTER TABLE `surat_keluar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `surat_masuk`
--
ALTER TABLE `surat_masuk`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `no` bigint NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
