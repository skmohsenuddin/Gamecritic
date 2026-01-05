-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 12, 2025 at 02:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gamecritic`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`) VALUES
(1, '', 'admin@gamecritic.com', 'admin123');

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `genre` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `release_year` int(11) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `review` text DEFAULT NULL,
  `pos_count` int(11) DEFAULT 0,
  `neg_count` int(11) DEFAULT 0,
  `overall_score` decimal(4,1) DEFAULT 0.0,
  `comments` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`id`, `title`, `genre`, `platform`, `release_year`, `cover_image`, `description`, `review`, `pos_count`, `neg_count`, `overall_score`, `comments`) VALUES
(4, 'Battlefield 6', 'War, fps, ', 'PC, Xbox, Playstation', 2025, '/images/693bf85f5f999.webp', 'Join an elite squad of Marine Raiders fighting relentlessly to save a world on the edge of collapse. From classic modes like Conquest and Breakthrough to new fast-paced, close-quarters experiences like Escalation, Battlefield 6 is bringing you more ways to battle than ever before.', NULL, 1, 1, 5.0, '[2025-12-12 14:57] dim: 10 e 10 try it\n'),
(6, 'God of War: Ragnarök', 'Action', 'PlayStation', 2022, '/images/693bf88e3a6f7.jpg', 'Epic Norse adventure continues', NULL, 0, 0, 0.0, NULL),
(7, 'Elden Ring', 'RPG', 'Multi-platform', 2022, '/images/693bf966cb8da.webp', 'FromSoftware masterpiece of exploration', NULL, 0, 0, 0.0, NULL),
(8, 'Zelda: Tears of the Kingdom', 'Adventure', 'Nintendo Switch', 2023, '/images/693bf8dbb9cbb.jpg', 'A magical return to Hyrule', NULL, 0, 0, 0.0, NULL),
(9, 'Spider-Man 2', 'Action', 'PlayStation', 2023, '/images/68bddaa5bf96f.jpg', 'Swing into action with Peter & Miles', NULL, 1, 1, 5.0, '[2025-09-07 20:16] Mrinu_4789: THis needs a remaster\n[2025-12-12 11:55] dim: shera part\n[2025-12-12 11:55] dim: agun game\n[2025-12-12 11:55] dim: try it\n'),
(13, 'ami mosha', 'rpg', 'ps16', 1999, '/images/693bf89f06064.jpg', 'mosha maro talle tale nacho taale taale', NULL, 0, 0, 0.0, NULL),
(14, 'tick tak toe', 'strategy', 'pc', 2025, '/images/693bf8c384453.png', 'just a basic games for stategical inputs', NULL, 0, 0, 0.0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `game_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` decimal(3,1) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `vote_score` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `game_id`, `user_id`, `rating`, `comment`, `created_at`, `upvotes`, `downvotes`, `vote_score`) VALUES
(19, 9, 5, 0.0, 'shera part', '2025-12-12 10:55:27', 0, 0, 0),
(20, 9, 5, 0.0, 'agun game', '2025-12-12 10:55:34', 0, 0, 0),
(21, 9, 5, 0.0, 'try it', '2025-12-12 10:55:40', 0, 0, 0),
(22, 4, 5, 0.0, '10 e 10 try it', '2025-12-12 13:57:27', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `phone`, `password`, `profile_picture`, `is_admin`) VALUES
(1, '', 'user@example.com', NULL, '123456', NULL, 0),
(2, '', 'admin@gamecritic.com', NULL, 'admin123', NULL, 1),
(3, 'Mrinu_4789', 'cars@gmail.com', '', '123457', NULL, 0),
(4, 'SOSB', 'SOSB@bracu.ac.bd', '', 'GOAT123', NULL, 0),
(5, 'dim', 'pookie@gmail.com', '', 'pookie123', NULL, 0),
(6, 'mohsen', 'weri@gmail.com', '', 'hiitsme1', NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_id` (`game_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`game_id`) REFERENCES `games` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
