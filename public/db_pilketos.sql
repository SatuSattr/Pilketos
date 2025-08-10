-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 10, 2025 at 10:47 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.10

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
  `misi` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_kelas` int NOT NULL,
  `url_foto` varchar(521) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `calon_ketua`
--

INSERT INTO `calon_ketua` (`id`, `nama`, `nomor`, `visi`, `misi`, `id_kelas`, `url_foto`) VALUES
(50, 'Faiz Nabil Akram', 1, 'membuat osis menjadi organisasi yang aktif dan terbuka,sehingga dapat menciptakan siswa yang inspiratif, partisipatif, dan kaya akan kegiatan positif', '1. Menyelenggarakan kegiatan yang kreatif dan edukatif untuk mengembangkan potensi siswa.\r\n2. Mendorong partisipasi aktif seluruh siswa dalam kegiatan sekolah dan organisasi.\r\n3. Membangun komunikasi yang terbuka dan transparan antara OSIS, siswa, dan pihak sekolah.\r\n4. Menjadi wadah aspirasi siswa yang menjunjung nilai-nilai kedisiplinan, tanggung jawab, dan kerja sama.\r\n5. Menggali serta mengembangkan bakat siswa melalui program kerja yang berkelanjutan dan inovatif.', 5, 'foto_calon/6898a5f29345b.png'),
(51, 'Ibnu Ghali Aulia', 2, 'Menjadikan OSIS sebagai wadah yang aktif, inovatif, dan inklusif, dalam mengembangkan, potensi siswa, serta membangun lingkungan sekolah yang berkarakter, berprestasi, dan peduli sesama', '1. Mendorong partisipasi siswa/i dalam kegiatan OSIS melalui program program yang menarik, bermandaat, dan sesuai minat bakat siswa/i\r\n2. ⁠Mengadakan kegiatan yang bertujuan untuk mempererat kebersamaan antar siswa/i\r\n3. ⁠Menyediakan sarana komunikasi yang efektif agar seluruh siswa dapat menyampaikan aspirasi, saran dan ide demi kemajuan sekolah\r\n4. ⁠membangun lingkungan sekolah yang nyaman untuk berkembang bersama baik akademik, maupun non - akademik\r\n5. Menghadirkan osis yang lebih dekat dan terbuka untuk semua ide, saran, maupun keresahan siswa', 5, 'foto_calon/6898a652ba94e.png');

-- --------------------------------------------------------

--
-- Table structure for table `hak_suara`
--

CREATE TABLE `hak_suara` (
  `id` int NOT NULL,
  `nisn` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hak_suara`
--

INSERT INTO `hak_suara` (`id`, `nisn`) VALUES
(111, 'Muhammad Faisal'),
(112, 'Bimasena Yusuf'),
(113, 'Rafay Arvino');

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
(1, 'admin@gmail.com', '98439e2b9e4d0184a2e41a1a00da603031d645cb915fd321b6a2dc590935e6f1', 'Admin ganteng'),
(3, 'kyaaeyri@gmail.com', 'ab7492c9b11fb3710f436fa276fe6e8a1b71652fa10f2e01034ea98bc82a93f0', 'Sattar');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `hak_suara`
--
ALTER TABLE `hak_suara`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=331;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
