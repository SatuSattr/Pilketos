-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 02, 2025 at 10:37 PM
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
-- Database: `db_pilketos`
--

-- --------------------------------------------------------

--
-- Table structure for table `calon_ketua`
--

CREATE TABLE `calon_ketua` (
  `id` int NOT NULL,
  `nama` varchar(256) NOT NULL,
  `nomor` int NOT NULL,
  `visi` varchar(521) NOT NULL,
  `misi` varchar(521) NOT NULL,
  `id_kelas` int NOT NULL,
  `url_foto` varchar(521) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `calon_ketua`
--

INSERT INTO `calon_ketua` (`id`, `nama`, `nomor`, `visi`, `misi`, `id_kelas`, `url_foto`) VALUES
(1, 'Faqih Abdul Karim', 1, 'Menjadikan OSIS sebagai rumah bersama yang aktif, ramah, dan peduli terhadap kebutuhan seluruh siswa.', 'Misi saya adalah meningkatkan keaktifan kegiatan OSIS yang inklusif, menyuarakan aspirasi siswa secara adil, mengadakan program sosial yang berdampak, memperbaiki fasilitas dan kebersihan sekolah, serta menjaga solidaritas dan kekeluargaan antar siswa.', 3, 'foto_calon/683c0181252a3.png'),
(2, 'Shabira Syahla Alvaliza', 2, 'Mewujudkan OSIS sebagai organisasi yang inovatif, aktif, dan responsif dalam menciptakan lingkungan sekolah yang inspiratif dan mendukung potensi siswa.', 'Misi saya adalah mengadakan program kreatif yang mendukung bakat siswa, memperkuat komunikasi OSIS lewat media digital, menyediakan wadah aspirasi terbuka, menjalin kerja sama dengan pihak luar, serta menumbuhkan semangat kolaborasi dan kepemimpinan di setiap kegiatan sekolah.', 4, 'foto_calon/68384410be197.png'),
(3, 'Ibnu Ghali Aulia', 3, 'Mengembangkan OSIS yang modern, digital, dan adaptif terhadap perkembangan zaman, untuk menciptakan sekolah yang keren dan relevan dengan generasi mudaaaa', 'Misi saya adalah mendigitalisasi sistem informasi OSIS, mengadakan lomba dan kegiatan teknologi, aktif di media sosial untuk promosi dan informasi, mendorong penggunaan teknologi dalam belajar dan OSIS, serta membentuk tim kreatif siswa di bidang konten digital.', 6, 'foto_calon/68384453ae5d4.png');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE `kelas` (
  `id` int NOT NULL,
  `name` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id`, `name`) VALUES
(1, 'X-3'),
(2, 'X-1'),
(3, 'X-2'),
(4, 'XI-1'),
(5, 'XI-2'),
(6, 'XI-3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `email` varchar(256) NOT NULL,
  `psw` varchar(521) NOT NULL,
  `nama_lengkap` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `psw`, `nama_lengkap`) VALUES
(1, 'admin@gmail.com', '8da3930b3345f15fa4947486e893a20e6fa45c39e426148e1754777a486622ec', 'Admin ganteng'),
(3, 'kyaaeyri@gmail.com', 'ab7492c9b11fb3710f436fa276fe6e8a1b71652fa10f2e01034ea98bc82a93f0', 'Sattar'),
(4, 'aylashabira17@gmail.com', '2c73e5f51f0a10d24e61b4435d2ba61d75c711d152b82d67f5c7845e18b8b734', 'Shabira');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE `vote` (
  `id` int NOT NULL,
  `id_calon` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `id_calon`, `created_at`) VALUES
(181, 1, '2025-05-30 01:00:00'),
(182, 2, '2025-05-30 01:00:35'),
(184, 1, '2025-05-30 01:01:50'),
(185, 2, '2025-05-30 01:02:25'),
(189, 3, '2025-05-30 01:04:25'),
(190, 1, '2025-05-30 01:04:50'),
(191, 2, '2025-05-30 01:05:15'),
(192, 3, '2025-05-30 01:05:50'),
(193, 1, '2025-05-30 01:06:25'),
(194, 2, '2025-05-30 01:06:55'),
(195, 3, '2025-05-30 01:07:30'),
(196, 1, '2025-05-30 01:08:05'),
(197, 2, '2025-05-30 01:08:35'),
(198, 3, '2025-05-30 01:09:00'),
(199, 1, '2025-05-30 01:09:30'),
(200, 2, '2025-05-30 01:09:55'),
(201, 3, '2025-05-30 01:10:20'),
(202, 1, '2025-05-30 01:10:55'),
(203, 2, '2025-05-30 01:11:20'),
(208, 1, '2025-05-30 01:13:45'),
(209, 2, '2025-05-30 01:14:10'),
(210, 3, '2025-05-30 01:14:40'),
(211, 1, '2025-05-30 01:15:05'),
(212, 2, '2025-05-30 01:15:35'),
(213, 3, '2025-05-30 01:16:05'),
(214, 1, '2025-05-30 01:16:35'),
(215, 2, '2025-05-30 01:17:00'),
(216, 3, '2025-05-30 01:17:30'),
(217, 1, '2025-05-30 01:18:00'),
(218, 2, '2025-05-30 01:18:30'),
(219, 3, '2025-05-30 01:18:55'),
(220, 1, '2025-05-30 01:19:25'),
(221, 2, '2025-05-30 01:19:50'),
(224, 2, '2025-05-30 01:21:10'),
(225, 3, '2025-05-30 01:21:40'),
(226, 1, '2025-05-30 01:22:10'),
(227, 2, '2025-05-30 01:22:40'),
(228, 3, '2025-05-30 01:23:05'),
(229, 1, '2025-05-30 01:23:35'),
(233, 2, '2025-05-30 01:25:30'),
(234, 3, '2025-05-30 01:26:00'),
(236, 2, '2025-05-30 01:26:55'),
(237, 3, '2025-05-30 01:27:25'),
(238, 1, '2025-05-30 01:27:55'),
(239, 2, '2025-05-30 01:28:20'),
(240, 3, '2025-05-30 01:28:50'),
(241, 1, '2025-05-30 01:29:15'),
(242, 2, '2025-05-30 01:29:45'),
(243, 3, '2025-05-30 01:30:10'),
(244, 1, '2025-05-30 01:30:40'),
(245, 2, '2025-05-30 01:31:05'),
(246, 3, '2025-05-30 01:31:30'),
(247, 1, '2025-05-30 01:32:00'),
(248, 2, '2025-05-30 01:32:30'),
(249, 3, '2025-05-30 01:33:00'),
(250, 1, '2025-05-30 01:33:25'),
(251, 2, '2025-05-30 01:33:55'),
(252, 3, '2025-05-30 01:34:25'),
(253, 1, '2025-05-30 01:34:55'),
(254, 2, '2025-05-30 01:35:20'),
(255, 3, '2025-05-30 01:35:50'),
(256, 1, '2025-05-30 01:36:15'),
(257, 2, '2025-05-30 01:36:45'),
(258, 3, '2025-05-30 01:37:10'),
(263, 2, '2025-05-30 01:39:30'),
(264, 3, '2025-05-30 01:39:55'),
(265, 1, '2025-05-30 01:40:25'),
(267, 3, '2025-05-30 01:41:20'),
(268, 1, '2025-05-30 01:41:45'),
(269, 2, '2025-05-30 01:42:15'),
(271, 1, '2025-05-30 01:43:10'),
(272, 2, '2025-05-30 01:43:35'),
(274, 2, '2025-06-01 06:13:30'),
(275, 3, '2025-06-01 06:13:34'),
(276, 3, '2025-06-01 06:18:17'),
(277, 1, '2025-06-01 06:18:24'),
(278, 1, '2025-06-01 06:19:17'),
(279, 1, '2025-06-01 06:23:48'),
(280, 3, '2025-06-01 06:26:08'),
(281, 3, '2025-06-01 06:32:37'),
(282, 3, '2025-06-01 06:33:29'),
(283, 2, '2025-06-01 06:58:35'),
(284, 1, '2025-06-01 06:58:39'),
(285, 3, '2025-06-01 06:58:45'),
(286, 3, '2025-06-01 06:58:57'),
(287, 3, '2025-06-01 06:59:05'),
(288, 2, '2025-06-01 06:59:26'),
(289, 2, '2025-06-01 06:59:39'),
(290, 2, '2025-06-01 06:59:43'),
(291, 2, '2025-06-01 06:59:48'),
(292, 2, '2025-06-01 07:31:21'),
(293, 2, '2025-06-01 09:00:33');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calon_ketua`
--
ALTER TABLE `calon_ketua`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kelas`
--
ALTER TABLE `kelas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `calon_ketua`
--
ALTER TABLE `calon_ketua`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vote`
--
ALTER TABLE `vote`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=294;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
