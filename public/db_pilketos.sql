-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 04, 2025 at 06:21 AM
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
(1, 'Glenn Marcel', 1, 'Membentuk generasi muda SMK yang cerdas, berakhlak mulia, dan peduli terhadap\r\nlingkungan serta masyarakat.', 'Misi kami adalah mengadakan kegiatan edukatif dan sosial yang menumbuhkan kecerdasan, akhlak mulia, serta kepedulian siswa terhadap lingkungan dan masyarakat.', 5, 'foto_calon/683fb1177ef19.png'),
(2, 'Faiz Nabil Akram', 2, 'Menjadikan OSIS sebagai wadah aspirasi siswa yang berdaya guna dan membentuk\r\nkarakter pelajar yang unggul serta berwawasan luas.', 'Misi kami adalah menampung aspirasi siswa melalui kegiatan yang membentuk karakter unggul dan memperluas wawasan pelajar secara nyata dan berkelanjutan.', 4, 'foto_calon/683fb0f9c0479.png'),
(3, 'Muhammad Faisal', 3, 'Mewujudkan OSIS yang aktif, kreatif, dan berintegritas dalam membangun lingkungan\r\nsekolah yang inspiratif dan berprestasi.', 'Misi kami adalah menyelenggarakan program kerja yang mendorong keaktifan, kreativitas, dan integritas siswa untuk menciptakan lingkungan sekolah yang inspiratif dan berprestasi.', 6, 'foto_calon/683fb0d6cf062.png');

-- --------------------------------------------------------

--
-- Table structure for table `hak_suara`
--

CREATE TABLE `hak_suara` (
  `id` int NOT NULL,
  `nisn` bigint NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hak_suara`
--

INSERT INTO `hak_suara` (`id`, `nisn`) VALUES
(3, 10000001),
(4, 10000002),
(5, 10000003),
(6, 10050004),
(7, 10000005),
(8, 10000006),
(9, 10000007),
(10, 10000008),
(11, 10000009),
(12, 10000010),
(13, 10000011),
(14, 10000012),
(15, 10000013),
(16, 10000014),
(17, 10000015),
(18, 10000016),
(19, 10000017),
(20, 10000018),
(21, 10000019),
(22, 10000020),
(23, 10000021),
(24, 10000022),
(25, 10000023),
(26, 10000024),
(27, 10000025),
(28, 10000026),
(29, 10000027),
(30, 10000028),
(31, 10000029),
(32, 10000030),
(33, 10000031),
(34, 10000032),
(35, 10000033),
(36, 10000034),
(37, 10000035),
(38, 10000036),
(39, 10000037),
(40, 10000038),
(41, 10000039),
(42, 10000040),
(104, 10000041),
(105, 10000042),
(106, 10000044),
(107, 10000045);

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
(4, 'aylashabira17@gmail.com', '2c73e5f51f0a10d24e61b4435d2ba61d75c711d152b82d67f5c7845e18b8b734', 'Shabira'),
(5, 'faiz@gmail.com', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5', 'Faizx');

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE `vote` (
  `id` int NOT NULL,
  `id_calon` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_nisn` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `vote`
--

INSERT INTO `vote` (`id`, `id_calon`, `created_at`, `id_nisn`) VALUES
(181, 1, '2025-05-30 01:00:00', 3),
(182, 2, '2025-05-30 01:00:35', 4),
(184, 1, '2025-05-30 01:01:50', 5),
(185, 2, '2025-05-30 01:02:25', 6),
(189, 3, '2025-05-30 01:04:25', 7),
(190, 1, '2025-05-30 01:04:50', 8),
(191, 2, '2025-05-30 01:05:15', 9),
(192, 3, '2025-05-30 01:05:50', 10),
(193, 1, '2025-05-30 01:06:25', 11),
(194, 2, '2025-05-30 01:06:55', 12),
(195, 3, '2025-05-30 01:07:30', 13),
(196, 1, '2025-05-30 01:08:05', 14),
(197, 2, '2025-05-30 01:08:35', 15),
(198, 3, '2025-05-30 01:09:00', 16),
(199, 1, '2025-05-30 01:09:30', 17),
(200, 2, '2025-05-30 01:09:55', 18),
(201, 3, '2025-05-30 01:10:20', 19),
(202, 1, '2025-05-30 01:10:55', 20),
(203, 2, '2025-05-30 01:11:20', 21),
(208, 1, '2025-05-30 01:13:45', 22),
(209, 2, '2025-05-30 01:14:10', 23),
(210, 3, '2025-05-30 01:14:40', 24),
(211, 1, '2025-05-30 01:15:05', 25),
(212, 2, '2025-05-30 01:15:35', 26),
(213, 3, '2025-05-30 01:16:05', 27),
(214, 1, '2025-05-30 01:16:35', 28),
(215, 2, '2025-05-30 01:17:00', 29),
(216, 3, '2025-05-30 01:17:30', 30),
(313, 3, '2025-06-04 06:14:20', 31),
(314, 2, '2025-06-04 06:14:32', 32),
(315, 2, '2025-06-04 06:14:37', 33),
(316, 1, '2025-06-04 06:15:03', 34),
(317, 1, '2025-06-04 06:15:08', 35),
(318, 3, '2025-06-04 06:15:17', 36),
(319, 2, '2025-06-04 06:15:21', 37),
(320, 1, '2025-06-04 06:15:25', 38),
(321, 3, '2025-06-04 06:15:29', 39),
(322, 2, '2025-06-04 06:15:33', 40),
(323, 2, '2025-06-04 06:15:36', 41),
(324, 2, '2025-06-04 06:15:40', 42);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `calon_ketua`
--
ALTER TABLE `calon_ketua`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hak_suara`
--
ALTER TABLE `hak_suara`
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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `hak_suara`
--
ALTER TABLE `hak_suara`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `kelas`
--
ALTER TABLE `kelas`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vote`
--
ALTER TABLE `vote`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
