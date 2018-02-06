-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 07, 2018 at 12:02 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `api_bedil`
--

-- --------------------------------------------------------

--
-- Table structure for table `berita`
--

CREATE TABLE `berita` (
  `id` int(3) NOT NULL,
  `lpw_id` int(3) NOT NULL,
  `judul_berita` varchar(50) NOT NULL,
  `isi_berita` text NOT NULL,
  `tanggal` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `laporan_pemberdayaan_wakaf`
--

CREATE TABLE `laporan_pemberdayaan_wakaf` (
  `id` int(3) NOT NULL,
  `pegawai_id` int(3) NOT NULL,
  `nomor_laporan` varchar(25) NOT NULL,
  `jenis_laporan` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `level`
--

CREATE TABLE `level` (
  `id` int(3) NOT NULL,
  `level` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `level`
--

INSERT INTO `level` (`id`, `level`) VALUES
(1, 'admin'),
(2, 'bagian_keuangan'),
(3, 'muwakif');

-- --------------------------------------------------------

--
-- Table structure for table `muwakif`
--

CREATE TABLE `muwakif` (
  `id` int(3) NOT NULL,
  `user_id` int(3) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `email` varchar(50) NOT NULL,
  `no_hp` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `muwakif`
--

INSERT INTO `muwakif` (`id`, `user_id`, `nama`, `alamat`, `tanggal_lahir`, `email`, `no_hp`) VALUES
(1, 1, 'Ahmad Djuanedi', 'Bekasi', '2018-02-14', 'ahmaddjunaedi92@gmail.com', '089693401875');

-- --------------------------------------------------------

--
-- Table structure for table `total_wakaf`
--

CREATE TABLE `total_wakaf` (
  `id` int(3) NOT NULL,
  `total_wakaf` int(3) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `total_wakaf`
--

INSERT INTO `total_wakaf` (`id`, `total_wakaf`, `created_at`) VALUES
(1, 100000, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(3) NOT NULL,
  `muwakif_id` int(3) NOT NULL,
  `jumlah_transaksi` bigint(15) NOT NULL,
  `tanggal_transaksi` datetime NOT NULL,
  `jenis_transaksi` int(1) NOT NULL DEFAULT '0' COMMENT '0. sekali 1. rutin',
  `bukti_transaksi` varchar(50) DEFAULT NULL,
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '0. pending, 1.proses, 2. gagal, 3. berhasil',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `muwakif_id`, `jumlah_transaksi`, `tanggal_transaksi`, `jenis_transaksi`, `bukti_transaksi`, `status`, `created_at`) VALUES
(1, 1, 100000, '2018-02-06 00:00:00', 0, '398277-walpaper.jpg', 3, '2018-02-06 09:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_berhasil`
--

CREATE TABLE `transaksi_berhasil` (
  `id` int(3) NOT NULL,
  `transaksi_id` int(3) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;

--
-- Dumping data for table `transaksi_berhasil`
--

INSERT INTO `transaksi_berhasil` (`id`, `transaksi_id`, `created_at`) VALUES
(1, 1, '0000-00-00 00:00:00'),
(2, 1, '0000-00-00 00:00:00'),
(3, 1, '0000-00-00 00:00:00'),
(4, 1, '0000-00-00 00:00:00'),
(5, 1, '0000-00-00 00:00:00'),
(6, 1, '0000-00-00 00:00:00'),
(7, 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_gagal`
--

CREATE TABLE `transaksi_gagal` (
  `id` int(3) NOT NULL,
  `transaksi_id` int(3) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(3) NOT NULL,
  `level_id` int(1) NOT NULL,
  `username` varchar(25) NOT NULL,
  `password` varchar(60) NOT NULL,
  `kode_aktivasi` varchar(5) NOT NULL,
  `status` enum('0','1') NOT NULL DEFAULT '0' COMMENT '0. not active 1. active',
  `_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `level_id`, `username`, `password`, `kode_aktivasi`, `status`, `_token`) VALUES
(1, 3, 'djuned92', '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', '5TY8P', '1', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `berita`
--
ALTER TABLE `berita`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laporan_pemberdayaan_wakaf`
--
ALTER TABLE `laporan_pemberdayaan_wakaf`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `level`
--
ALTER TABLE `level`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `muwakif`
--
ALTER TABLE `muwakif`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `total_wakaf`
--
ALTER TABLE `total_wakaf`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_berhasil`
--
ALTER TABLE `transaksi_berhasil`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaksi_gagal`
--
ALTER TABLE `transaksi_gagal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `berita`
--
ALTER TABLE `berita`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `laporan_pemberdayaan_wakaf`
--
ALTER TABLE `laporan_pemberdayaan_wakaf`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `level`
--
ALTER TABLE `level`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `muwakif`
--
ALTER TABLE `muwakif`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `total_wakaf`
--
ALTER TABLE `total_wakaf`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transaksi_berhasil`
--
ALTER TABLE `transaksi_berhasil`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `transaksi_gagal`
--
ALTER TABLE `transaksi_gagal`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(3) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
