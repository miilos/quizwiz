-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Mar 22, 2026 at 12:07 PM
-- Server version: 9.3.0
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quizwiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `chat_history`
--

CREATE TABLE `chat_history` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `response` longtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_history`
--

INSERT INTO `chat_history` (`id`, `user_id`, `response`) VALUES
(1, 1, 'What is the name of the island where Edmond Dantès finds the treasure?'),
(2, 1, 'What profession does Edmond Dantès have at the beginning of the story?'),
(3, 1, 'Which character serves as a false friend to Edmond Dantès and ultimately leads to his imprisonment?'),
(4, 1, 'Which two characters are central to the plot of revenge that Edmond Dantès orchestrates against his enemies?'),
(5, 1, 'How does Edmond Dantès gain access to the island of Monte Cristo?'),
(6, 1, 'What is the name of the ship that Edmond Dantès sails on before his imprisonment?'),
(7, 1, 'What is the name of the force that drives the cyclical nature of time in \'The Wheel of Time\' series?'),
(8, 1, 'Who is known for the creation of the character of the Count of Monte Cristo?'),
(9, 1, 'What is the significance of Lews Therin Telamon in the \'Wheel of Time\' series?'),
(10, 1, 'What is the primary motivation behind Edmond Dantès\' desire for revenge?'),
(12, 13, 'Which 1970s rock band was known for the hit song \'Hotel California\'?'),
(13, 13, 'What city is considered the birthplace of grunge music?'),
(14, 1, 'What unique musical instrument does Kvothe learn to play in \'The Name of the Wind\'?');

-- --------------------------------------------------------

--
-- Table structure for table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8mb3_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20251108170658', '2025-12-11 20:36:32', 69),
('DoctrineMigrations\\Version20251108172526', '2025-12-11 20:36:32', 110),
('DoctrineMigrations\\Version20251109143638', '2025-12-11 20:36:32', 2831),
('DoctrineMigrations\\Version20251113202258', '2025-12-11 20:36:35', 234),
('DoctrineMigrations\\Version20251115135213', '2025-12-11 20:36:35', 42),
('DoctrineMigrations\\Version20251130123713', '2025-12-11 20:36:35', 13),
('DoctrineMigrations\\Version20251206192902', '2025-12-11 20:36:35', 88),
('DoctrineMigrations\\Version20251209224536', '2025-12-11 20:36:35', 37),
('DoctrineMigrations\\Version20260102234117', '2026-01-02 23:44:55', 168),
('DoctrineMigrations\\Version20260105160934', '2026-01-05 16:11:08', 335),
('DoctrineMigrations\\Version20260113214515', '2026-01-13 21:47:41', 398),
('DoctrineMigrations\\Version20260114215033', '2026-01-14 21:51:45', 109),
('DoctrineMigrations\\Version20260225184046', '2026-02-25 18:41:49', 308),
('DoctrineMigrations\\Version20260314091303', '2026-03-14 09:16:21', 161);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `text` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` json NOT NULL COMMENT '(DC2Type:json)',
  `correct_answer` json NOT NULL COMMENT '(DC2Type:json)',
  `position` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `explanation` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`id`, `quiz_id`, `text`, `options`, `correct_answer`, `position`, `type`, `explanation`) VALUES
(1, 1, 'What was the name of Edmond\'s boss in the beginning of the book?', '[\"M. Morrel\", \"Danglars\", \"Peppino\", \"Maximilien\"]', '[0]', 1, 'one', NULL),
(2, 1, 'The Paris cast of characters first encounters the Count in Rome.', '[\"True\", \"False\"]', '[0]', 2, 'one', NULL),
(38, 18, 'What does the __serialize() method do?', '[\"Turns the given object into an array\", \"Writes the object out as text\", \"Saves the object in memory\", \"Clones the object\"]', '[0]', 1, 'one', 'The __serialize() method is used to turn an object into an array. '),
(39, 18, 'Which of the following are magic methods?', '[\"__clone()\", \"__sleep()\", \"__duplicate()\", \"__construct()\"]', '[0, 1, 3]', 2, 'multiple', NULL),
(50, 1, 'What pseudonims does Edmond use throughout the book?', '[\"Lord Wilmore\", \"Luigi Vampa\", \"Abbe Bussoni\", \"Sinbad the sailor\"]', '[0, 2, 3]', 3, 'multiple', NULL),
(51, 1, 'Who\'s father did Noirtier murder in a duel?', '[\"Albert de Morcerf\", \"Franz D\'Epinay\", \"Cadeousse\", \"Maximillien Morrel\"]', '[1]', 4, 'one', NULL),
(52, 20, 'What\'s the debut album of the Jimi Hendrix Experience called?', '[\"Electric Ladyland\", \"Are You Experienced?\", \"Axis: Bold as Love\", \"Live at Filmore East\"]', '[1]', 1, 'one', NULL),
(53, 20, 'This 4-piece from Liverpool\'s debut album is called \'Please please me\'', '[\"The Rolling Stones\", \"Led Zeppelin\", \"The Beatles\", \"The Byrds\"]', '[2]', 2, 'one', NULL),
(54, 20, 'Select all albums by Pearl Jam', '[\"Ten\", \"Nevermind\", \"Badmotorfinger\", \"Vs.\"]', '[0, 3]', 3, 'multiple', NULL),
(55, 20, 'Which 1970s rock band was known for the hit song \'Hotel California\'?', '[\"The Eagles\", \"Fleetwood Mac\", \"Led Zeppelin\", \"Pink Floyd\"]', '[0]', 4, 'one', NULL),
(57, 20, 'What city is considered the birthplace of grunge?', '[\"Seattle\", \"Los Angeles\", \"New York\", \"London\"]', '[0]', 5, 'one', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz`
--

CREATE TABLE `quiz` (
  `id` int NOT NULL,
  `author_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `further_reading` longtext COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz`
--

INSERT INTO `quiz` (`id`, `author_id`, `title`, `description`, `created_at`, `further_reading`) VALUES
(1, 1, 'The Count of Monte Cristo quiz', 'Test your knowledge od the Count of Monte Cristo by Alexandre Dumas :)', '2025-12-11 20:44:27', 'Get a copy of the book for yourself and read further :)'),
(18, 1, 'PHP quiz', 'test your knowledge of php', '2026-03-03 18:21:09', 'https://www.php.net/manual/en/index.php'),
(20, 13, 'Music quiz', 'Test your music knowledge!', '2026-03-14 17:46:23', '');

-- --------------------------------------------------------

--
-- Table structure for table `quiz_attempt`
--

CREATE TABLE `quiz_attempt` (
  `id` int NOT NULL,
  `quiz_id` int NOT NULL,
  `user_id` int NOT NULL,
  `answers` json NOT NULL COMMENT '(DC2Type:json)',
  `attempted_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)',
  `correct_answer_count` int NOT NULL,
  `incorrect_answer_count` int NOT NULL,
  `percentage_score` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `quiz_attempt`
--

INSERT INTO `quiz_attempt` (`id`, `quiz_id`, `user_id`, `answers`, `attempted_at`, `correct_answer_count`, `incorrect_answer_count`, `percentage_score`) VALUES
(4, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-01-17 18:57:05', 3, 1, 75),
(5, 1, 1, '[{\"status\": \"incorrect\", \"answers\": [0, 1], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-01-17 18:58:15', 2, 2, 50),
(6, 1, 1, '[{\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-01-17 18:58:36', 2, 1, 50),
(7, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-01-20 11:46:01', 3, 1, 75),
(8, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [1, 0, 2], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 17:59:41', 3, 1, 75),
(9, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [1, 0, 2], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 21:31:53', 3, 1, 75),
(10, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 21:32:55', 3, 1, 75),
(11, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [1, 0, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 21:34:15', 3, 1, 75),
(12, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [2, 1, 0], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 21:35:09', 3, 1, 75),
(13, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [1, 0, 2], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-24 21:44:09', 2, 2, 50),
(14, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 2], \"questionId\": 32}, {\"status\": \"incorrect\", \"answers\": [0], \"questionId\": 33}]', '2026-02-24 22:04:28', 2, 2, 50),
(16, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 2], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-25 17:54:27', 3, 1, 75),
(17, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-26 17:27:26', 3, 1, 75),
(18, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-26 17:28:13', 3, 1, 75),
(19, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-02-26 18:12:23', 3, 1, 75),
(20, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 2, 1], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-03-01 12:30:16', 3, 1, 75),
(21, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [2, 1, 0], \"questionId\": 32}, {\"status\": \"incorrect\", \"answers\": [0], \"questionId\": 33}]', '2026-03-01 12:42:23', 2, 2, 50),
(22, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 2], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-03-01 18:51:40', 3, 1, 75),
(23, 18, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 38}, {\"status\": \"correct\", \"answers\": [0, 1, 3], \"questionId\": 39}]', '2026-03-03 18:21:34', 2, 0, 100),
(24, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-03-05 18:27:35', 3, 1, 75),
(25, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"incorrect\", \"answers\": [1], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-03-05 18:31:43', 3, 1, 75),
(26, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 3, 1], \"questionId\": 32}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 33}]', '2026-03-05 18:56:09', 3, 1, 75),
(27, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 46}, {\"status\": \"incorrect\", \"answers\": [2], \"questionId\": 47}]', '2026-03-10 18:53:29', 3, 1, 75),
(28, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 3, 1], \"questionId\": 46}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 47}]', '2026-03-10 20:49:35', 3, 1, 75),
(30, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 3], \"questionId\": 50}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 51}]', '2026-03-14 09:56:02', 3, 1, 75),
(31, 20, 13, '[{\"status\": \"correct\", \"answers\": [1], \"questionId\": 52}, {\"status\": \"correct\", \"answers\": [2], \"questionId\": 53}, {\"status\": \"correct\", \"answers\": [0, 3], \"questionId\": 54}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 55}]', '2026-03-14 17:46:43', 4, 0, 100),
(32, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 50}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 51}]', '2026-03-14 22:31:42', 4, 0, 100),
(33, 1, 1, '[{\"status\": \"correct\", \"answers\": [0], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"correct\", \"answers\": [0, 2, 3], \"questionId\": 50}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 51}]', '2026-03-14 22:34:43', 4, 0, 100),
(34, 1, 1, '[{\"status\": \"incorrect\", \"answers\": [3], \"questionId\": 1}, {\"status\": \"correct\", \"answers\": [0], \"questionId\": 2}, {\"status\": \"incorrect\", \"answers\": [0, 1, 2], \"questionId\": 50}, {\"status\": \"correct\", \"answers\": [1], \"questionId\": 51}]', '2026-03-15 16:00:15', 2, 2, 50);

-- --------------------------------------------------------

--
-- Table structure for table `tag`
--

CREATE TABLE `tag` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tag`
--

INSERT INTO `tag` (`id`, `name`, `display_name`) VALUES
(1, 'wheel-of-time', 'Wheel-of-timeheel-of-time'),
(2, 'books', 'Books'),
(3, 'test', 'Test'),
(4, 'test-tag-test', 'Test Tag Test'),
(5, 'test-tag', 'Test Tag'),
(6, 'tag-edit-test', 'Tag Edit Test'),
(7, 'test-test-test', 'Test Test Test'),
(8, 'new-tag', 'New Tag'),
(9, 'special-word-test', 'Special Word Test'),
(10, 'programming', 'Programming'),
(11, 'php', 'Php'),
(12, 'music', 'Music'),
(13, 'rock', 'Rock'),
(14, 'rock-music', 'Rock Music');

-- --------------------------------------------------------

--
-- Table structure for table `tag_quiz`
--

CREATE TABLE `tag_quiz` (
  `tag_id` int NOT NULL,
  `quiz_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tag_quiz`
--

INSERT INTO `tag_quiz` (`tag_id`, `quiz_id`) VALUES
(2, 1),
(10, 18),
(11, 18),
(12, 20),
(14, 20);

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `token` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` datetime NOT NULL COMMENT '(DC2Type:datetime_immutable)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `token`
--

INSERT INTO `token` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 1, 'a7372a0a36ec20e59432f733f6605ac948e7f2dcb1974db57232cbd5cb3c6373', '2026-03-01 21:43:31'),
(2, 1, 'f57c95dab06497fdd5bf1610b41e21b648d50095350acc52b96d60a52d915abb', '2026-03-04 21:44:38'),
(3, 1, '8d22a5b5cbbbe93ad3e9d2e662fe8501335ab078e02830563315e3d49fa13be9', '2026-03-05 18:25:27'),
(4, 1, 'e98d8d702e3576a9e1fd554b5117a43bfa53179a82fd3c4a8fde0b20f522e8c4', '2026-03-07 10:13:22'),
(5, 1, 'aa69bba89133d32c0d2307e692a6f5fef4cc54a382e4e58ebc7f308763a41d43', '2026-03-07 10:20:16'),
(6, 1, 'd42349c944322cba3ba72e8b718ae857e6321c1d410d3d22f62855ba1ea0716d', '2026-03-07 10:47:52'),
(7, 1, '84de9cf4327b40de45a4a8ca9ecae521f03d25c155492b84a298b1559e968175', '2026-03-07 10:55:26'),
(8, 1, 'e2e588fbef0de30c1d0316fbcf4ed76a288bffb694d27aae843be9f150cb7bca', '2026-03-07 11:06:23'),
(9, 1, '3c94ae3804fb8740c0dd53b84cd804de41099a0570db423d2757114595569738', '2026-03-07 11:06:27'),
(10, 1, '6767195f30eea416700c3f30fee880c8508b9216845213bc9faf4217eeee545f', '2026-03-07 11:07:22'),
(11, 1, '0247bfc4ba3542a42e5428d736db41a404acffd61202f0d2e82eef022d335981', '2026-03-07 11:08:12'),
(12, 1, '58eb7e621ae098f0f74ff45b698da9076e0455963863639381bef3337f88c10b', '2026-03-07 21:17:09'),
(13, 1, 'f68e32a9af72e2fce3af9f2b37d26415bc79f979ff70c268c4378fa8881ee1b7', '2026-03-07 21:17:22'),
(14, 1, '54b367af8571098d6fb9a082e390c6915ff77655fa39854ee47356ae1dfe1a84', '2026-03-07 21:23:11'),
(15, 1, '31cc467957ffea471a28e76a6810d145c655fd8d00b360fcbc6303f205c72c64', '2026-03-07 23:26:02'),
(16, 1, '066036c4da5476dbce4be191613f623ecdb7ba7bf65034d0d1b4c88bcf4868f9', '2026-03-07 23:26:33'),
(17, 1, '362d33e219b6a1df00e140777d33676336f913947562cd72afa631bca97933b6', '2026-03-07 23:27:40'),
(18, 1, 'fc313833e3c18d60de320a530bc4e384b0826719333ebe6d2ab14e9879926ff9', '2026-03-08 10:27:15'),
(19, 1, 'a0e72393d8a273af220598cdcb2bc4b3aa7e63cfd061291543f11c23c5677f82', '2026-03-08 18:51:28'),
(22, 8, 'c7bcd0c3ccd50e22ab8c071a3fc4b9ad29ce2cadb54acb54a946b40c02f2aa31', '2026-03-09 21:55:08'),
(23, 1, '996fd2c6d6d8a0faeab3a82651bcd309ed80b5fbfc6a2d168d36364e962cb91f', '2026-03-09 21:59:48'),
(24, 1, 'db9929e3adb18c36fb35db077448a9e0e2df48e4585bc099bdcf1fcf51683a6c', '2026-03-12 19:08:09'),
(25, 1, 'fec31323fc68b444fe13a2ff8ed6bab43b378957c90bd120849b0a4bd411a912', '2026-03-12 20:16:33'),
(26, 1, '5c33130385b27d2affd6257e3c814dba0686eaf195aac8b0a29b08074ed20b76', '2026-03-12 20:34:23'),
(27, 1, 'b499eaa88ace9d1a8c5e5e138b0a0e860137507ba74179d96cb203092fefe501', '2026-03-12 20:42:57'),
(28, 1, '9b78215cb69b938fde003ebee9be5c5e957a687dd1c864e5c9664b6e7efaf309', '2026-03-12 20:44:30'),
(29, 1, '0018fa6c7cda0e6044df323d721b96a40a50bdc4b8fa3280d906a193d8dfcb21', '2026-03-12 20:46:36'),
(30, 1, '11efabfc5fbd2e81ef5a2057085b0a34805b90d3052a887ade1a33f21ea6244a', '2026-03-12 21:05:07'),
(31, 1, '4aeb26519f0d6a50da24f090427d17477b66617183a8442c6174d72401eb305a', '2026-03-15 12:32:45'),
(32, 1, 'a38163d1ec3ad58a260eef64a7368e1861b02825401075878d1c0b039a03605a', '2026-03-15 19:06:00'),
(33, 1, '66852e522c864bcd0d123245379de7e2027ef43a4f54d1d1f4dbb11e59f7132b', '2026-03-15 19:06:43'),
(34, 1, '55436e3b85f1df145528a190fb22acff8731e38979dfe6f983063233bbe8b138', '2026-03-16 21:48:24'),
(35, 1, '5860d25a0cb43cbc88c1276b8e6427c11850d161af5efeb799dc02a373d7bef6', '2026-03-17 17:55:04'),
(36, 1, '3244d461c0a5a345b512a1ba977fbbfe7947b65ff51d84926999cbe4d8e5186d', '2026-03-17 18:05:00'),
(37, 1, '3f81785d85fd7ce011c244131519c8c8037217a65e9509512bac2ad669ebdbda', '2026-03-17 18:59:36'),
(38, 1, '61a3332b17d9728cac250260090c3835825816a23c1063370dccfe6caf7e1d41', '2026-03-18 20:46:43'),
(42, 1, 'f3a758dc792723ac72a5012c72d40bdffc3f720def69f2b522b339e3f5685c47', '2026-03-18 21:23:32'),
(44, 13, '4d4cdc883b52165569f1672c8735c1c780f5961debacb9298746c666d39df7d7', '2026-03-21 16:46:55'),
(45, 13, '78700c23f8624e98f3ec4575ab3d0e98fd2cc82bc0ae976391f28e2c792ef72b', '2026-03-21 17:58:31'),
(46, 1, '3384304c6ec83aa6c47abc8fa8352f966610d9660163012f5c48287bc0dca5f1', '2026-03-21 17:59:38'),
(47, 1, '53d7c14c21d68d8c0d95c25d82988392167af4c79133dd63c1f9cfae8712fa90', '2026-03-21 22:31:13'),
(48, 1, '0cd07b1b58c3841bb7546087f026ce20a9656e4ee43157badd2c042c1446bc10', '2026-03-22 16:33:10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `roles` json NOT NULL COMMENT '(DC2Type:json)',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_activated` tinyint(1) NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_reset_expires` datetime DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_activation_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `roles`, `password`, `is_activated`, `password_reset_token`, `password_reset_expires`, `username`, `account_activation_token`) VALUES
(1, 'milos@gmail.com', '[\"ROLE_USER\", \"ROLE_ADMIN\"]', '$2y$13$c6WW2BgmJmWdWZ2UtSBjxO3EXvGQnmIEAbyDdcNOaYZJN/AqCdEXS', 1, NULL, NULL, 'milos', NULL),
(8, 'test@gmail.com', '[\"ROLE_USER\"]', '$2y$13$2iGknenigiLFbFghmG/4ce/7LiasIlJzBW39bGlzHr1lkpmVO4aey', 0, NULL, NULL, 'test', 'e741435aef69056feae84ef41764e83a'),
(13, 'user@gmail.com', '[\"ROLE_USER\"]', '$2y$13$N8/7g5aE9nAIo3Meq/fJPeXQFNm3XLpJyHKBlj9CeBrW9kXlXzvpW', 0, NULL, NULL, 'user', 'fae25049debccdbdc4b14a0f34d4db2f');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6BB4BC22A76ED395` (`user_id`);

--
-- Indexes for table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494E853CD175` (`quiz_id`);

--
-- Indexes for table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_A412FA92F675F31B` (`author_id`);

--
-- Indexes for table `quiz_attempt`
--
ALTER TABLE `quiz_attempt`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AB6AFC6853CD175` (`quiz_id`),
  ADD KEY `IDX_AB6AFC6A76ED395` (`user_id`);

--
-- Indexes for table `tag`
--
ALTER TABLE `tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tag_quiz`
--
ALTER TABLE `tag_quiz`
  ADD PRIMARY KEY (`tag_id`,`quiz_id`),
  ADD KEY `IDX_4A1D4524BAD26311` (`tag_id`),
  ADD KEY `IDX_4A1D4524853CD175` (`quiz_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5F37A13BA76ED395` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_IDENTIFIER_EMAIL` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `chat_history`
--
ALTER TABLE `chat_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `quiz_attempt`
--
ALTER TABLE `quiz_attempt`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `tag`
--
ALTER TABLE `tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `token`
--
ALTER TABLE `token`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chat_history`
--
ALTER TABLE `chat_history`
  ADD CONSTRAINT `FK_6BB4BC22A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494E853CD175` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`);

--
-- Constraints for table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `FK_A412FA92F675F31B` FOREIGN KEY (`author_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `quiz_attempt`
--
ALTER TABLE `quiz_attempt`
  ADD CONSTRAINT `FK_AB6AFC6853CD175` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`),
  ADD CONSTRAINT `FK_AB6AFC6A76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `tag_quiz`
--
ALTER TABLE `tag_quiz`
  ADD CONSTRAINT `FK_4A1D4524853CD175` FOREIGN KEY (`quiz_id`) REFERENCES `quiz` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_4A1D4524BAD26311` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `FK_5F37A13BA76ED395` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
