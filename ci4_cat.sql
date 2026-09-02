-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 02, 2026 at 04:48 PM
-- Server version: 8.0.42
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ci4_cat`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `required_count` int NOT NULL DEFAULT '20'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `event_id`, `name`, `required_count`) VALUES
(10, 3, 'Bahasa Indonesia', 20),
(11, 3, 'Matematika', 20),
(13, NULL, 'Kalkulus', 200);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `organizer` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `location` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `panitia_user_id` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `organizer`, `location`, `panitia_user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(3, 'P3D Desa Binangun 2025', 'Panitia P3D Binangun', 'Kecamatan Banyumas', 10, 1, '2025-10-16 18:47:05', '2025-10-20 22:32:14');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_finished` tinyint(1) NOT NULL DEFAULT '0',
  `score_cat` decimal(5,2) NOT NULL DEFAULT '0.00',
  `score_skill` decimal(5,2) NOT NULL DEFAULT '0.00',
  `score_total` decimal(5,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` int UNSIGNED NOT NULL,
  `exam_id` int UNSIGNED NOT NULL,
  `question_id` int UNSIGNED NOT NULL,
  `user_answer` char(1) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT '0',
  `question_order` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `exam_type_id` int UNSIGNED NOT NULL,
  `score` decimal(5,2) DEFAULT '0.00',
  `status` enum('lulus','tidak_lulus','belum') DEFAULT 'belum',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_sessions`
--

CREATE TABLE `exam_sessions` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `exam_setting_id` int NOT NULL,
  `question_order` json NOT NULL,
  `answers` json DEFAULT NULL,
  `current_index` int DEFAULT '0',
  `status` enum('started','paused','finished') DEFAULT 'started',
  `score` decimal(10,0) DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `paused_at` datetime DEFAULT NULL,
  `finished_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_sessions`
--

INSERT INTO `exam_sessions` (`id`, `user_id`, `exam_setting_id`, `question_order`, `answers`, `current_index`, `status`, `score`, `started_at`, `paused_at`, `finished_at`, `created_at`, `updated_at`) VALUES
(1, 16, 1, '[\"14\", \"7\", \"18\", \"15\", \"20\", \"22\", \"21\", \"5\", \"6\", \"4\", \"19\", \"10\", \"9\", \"8\", \"13\", \"3\", \"11\", \"12\", \"16\", \"17\"]', '{\"3\": \"B\", \"4\": \"D\", \"5\": \"C\", \"6\": \"D\", \"7\": \"B\", \"8\": \"B\", \"9\": \"D\", \"10\": \"D\", \"11\": \"B\", \"12\": \"A\", \"13\": \"C\", \"14\": \"A\", \"15\": \"A\", \"16\": \"C\", \"17\": \"C\", \"18\": \"C\", \"19\": \"C\", \"20\": \"A\", \"21\": \"A\", \"22\": \"A\"}', 19, 'finished', '25', '2025-10-19 22:25:42', NULL, NULL, '2025-10-19 15:25:42', '2025-10-20 15:21:39'),
(2, 16, 7, '[\"TITLE_10\", \"30\", \"31\", \"29\", \"36\", \"28\", \"3\", \"34\", \"24\", \"25\", \"35\", \"33\", \"23\", \"26\", \"39\", \"40\", \"41\", \"38\", \"37\", \"27\", \"32\", \"TITLE_11\", \"18\", \"12\", \"14\", \"11\", \"19\", \"17\", \"15\", \"10\", \"9\", \"13\", \"5\", \"16\", \"6\", \"4\", \"7\", \"21\", \"8\", \"22\", \"20\"]', '{\"3\": \"E\", \"4\": \"A\", \"5\": \"C\", \"6\": \"A\", \"7\": \"A\", \"8\": \"A\", \"9\": \"B\", \"10\": \"A\", \"11\": \"A\", \"12\": \"A\", \"13\": \"A\", \"14\": \"B\", \"15\": \"A\", \"16\": \"A\", \"17\": \"A\", \"18\": \"A\", \"19\": \"A\", \"20\": \"B\", \"21\": \"A\", \"22\": \"B\", \"23\": \"C\", \"24\": \"A\", \"25\": \"B\", \"26\": \"A\", \"27\": \"A\", \"28\": \"B\", \"29\": \"A\", \"30\": \"B\", \"31\": \"C\", \"32\": \"A\", \"33\": \"A\", \"34\": \"B\", \"35\": \"A\", \"36\": \"A\", \"37\": \"A\", \"38\": \"A\", \"39\": \"A\", \"40\": \"A\", \"41\": \"A\"}', 40, 'finished', '28', '2025-10-20 21:15:25', NULL, NULL, '2025-10-20 14:15:25', '2025-10-20 15:23:35'),
(3, 16, 2, '[\"TITLE_10\", \"35\", \"23\", \"3\", \"33\", \"31\", \"32\", \"38\", \"30\", \"28\", \"29\", \"34\", \"40\", \"36\", \"25\", \"26\", \"24\", \"39\", \"41\", \"27\", \"37\", \"TITLE_11\", \"8\", \"13\", \"21\", \"7\", \"14\", \"15\", \"10\", \"4\", \"22\", \"18\", \"9\", \"19\", \"16\", \"5\", \"17\", \"12\", \"11\", \"6\", \"20\"]', '{\"3\": \"B\", \"4\": \"A\", \"5\": \"C\", \"6\": \"D\", \"7\": \"A\", \"8\": \"B\", \"9\": \"B\", \"10\": \"A\", \"11\": \"D\", \"12\": \"D\", \"13\": \"B\", \"14\": \"B\", \"15\": \"A\", \"16\": \"C\", \"17\": \"C\", \"18\": \"C\", \"19\": \"B\", \"20\": \"C\", \"21\": \"B\", \"22\": \"D\", \"23\": \"D\", \"24\": \"A\", \"25\": \"C\", \"26\": \"D\", \"27\": \"B\", \"28\": \"B\", \"29\": \"A\", \"30\": \"D\", \"31\": \"C\", \"32\": \"A\", \"33\": \"B\", \"34\": \"C\", \"35\": \"C\", \"36\": \"A\", \"37\": \"D\", \"38\": \"B\", \"39\": \"C\", \"40\": \"B\", \"41\": \"A\"}', 40, 'started', NULL, '2025-10-29 19:25:45', NULL, NULL, '2025-10-29 12:25:45', '2025-10-29 12:30:29');

-- --------------------------------------------------------

--
-- Table structure for table `exam_settings`
--

CREATE TABLE `exam_settings` (
  `id` int UNSIGNED NOT NULL,
  `exam_type_id` int UNSIGNED NOT NULL,
  `duration` int NOT NULL DEFAULT '60',
  `passing_grade` int NOT NULL DEFAULT '70',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `is_paused` tinyint(1) DEFAULT '0',
  `last_paused_at` datetime DEFAULT NULL,
  `paused_seconds` int DEFAULT NULL,
  `status` enum('scheduled','started','paused','finished') DEFAULT 'scheduled',
  `paused_at` datetime DEFAULT NULL,
  `mode` enum('online','offline') DEFAULT 'online',
  `randomize_questions` tinyint(1) DEFAULT '1',
  `show_result` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_settings`
--

INSERT INTO `exam_settings` (`id`, `exam_type_id`, `duration`, `passing_grade`, `start_time`, `end_time`, `is_paused`, `last_paused_at`, `paused_seconds`, `status`, `paused_at`, `mode`, `randomize_questions`, `show_result`, `created_at`, `updated_at`) VALUES
(1, 1, 120, 80, '2025-10-19 02:02:00', '2025-10-20 00:08:00', 0, NULL, 3699, 'finished', NULL, 'online', 1, 0, '2025-10-17 16:44:14', '2025-10-29 19:24:56'),
(2, 3, 60, 70, '2025-10-29 19:21:00', '2025-10-29 20:27:59', 0, NULL, 3231, 'started', NULL, 'online', 1, 0, '2025-10-17 16:45:07', '2025-10-29 19:34:08'),
(7, 5, 60, 70, '2025-10-20 21:10:23', '2025-10-20 22:10:23', 0, NULL, NULL, 'finished', NULL, 'online', 1, 0, '2025-10-20 21:09:33', '2025-10-20 22:22:40');

-- --------------------------------------------------------

--
-- Table structure for table `exam_setting_categories`
--

CREATE TABLE `exam_setting_categories` (
  `id` int UNSIGNED NOT NULL,
  `exam_setting_id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `question_count` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_setting_categories`
--

INSERT INTO `exam_setting_categories` (`id`, `exam_setting_id`, `category_id`, `question_count`) VALUES
(12, 7, 10, 0),
(13, 7, 11, 0),
(14, 1, 10, 0),
(15, 1, 11, 0),
(16, 2, 10, 0),
(17, 2, 11, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_types`
--

CREATE TABLE `exam_types` (
  `id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `exam_types`
--

INSERT INTO `exam_types` (`id`, `event_id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 'Ujian CAT 100 Soal', 'deskripsinya y', 1, '2025-10-17 09:19:04', '2025-10-17 09:24:25'),
(3, 3, 'Ujian Office', 'word,excel', 1, '2025-10-17 09:44:43', '2025-10-17 09:44:43'),
(5, 3, 'tes', 'sdasds', 1, '2025-10-18 10:42:32', '2025-10-18 10:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `final_results`
--

CREATE TABLE `final_results` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED NOT NULL,
  `final_score` decimal(5,2) DEFAULT '0.00',
  `status` enum('lulus','tidak_lulus') DEFAULT 'tidak_lulus'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-10-16-175545', 'App\\Database\\Migrations\\CreateRolesTable', 'default', 'App', 1760637486, 1),
(2, '2025-10-16-175608', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1760637487, 1),
(3, '2025-10-16-175633', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1760637487, 1),
(4, '2025-10-16-274445', 'App\\Database\\Migrations\\CreateQuestionsTable', 'default', 'App', 1760637487, 1),
(5, '2025-10-16-374638', 'App\\Database\\Migrations\\CreateExamsTable', 'default', 'App', 1760637487, 1),
(6, '2025-10-16-474712', 'App\\Database\\Migrations\\CreateExamQuestionsTable', 'default', 'App', 1760637487, 1),
(7, '2025-10-16-183529', 'App\\Database\\Migrations\\CreateEventsTable', 'default', 'App', 1760639814, 2),
(8, '2025-10-16-183617', 'App\\Database\\Migrations\\AddEventIdColumns', 'default', 'App', 1760639814, 2),
(9, '2025-10-17-153631', 'App\\Database\\Migrations\\CreateExamSettingsTable', 'default', 'App', 1760715539, 3);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int UNSIGNED NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED DEFAULT NULL,
  `question_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `option_a` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_b` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_c` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_d` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `option_e` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correct_answer` char(1) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `category_id`, `event_id`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `correct_answer`, `created_at`, `updated_at`) VALUES
(3, 10, 3, 'Soalnya ya', 'A', 'B', 'C', 'D', 'E', 'B', '2025-10-17 11:09:53', '2025-10-17 11:10:06'),
(4, 11, 3, 'Hasil dari 15 + 7 x 3 adalah?', '36', '46', '66', '54', '', 'A', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(5, 11, 3, 'Bentuk sederhana dari x2+3x−10 jika difaktorkan adalah?', '(x+5)(x-2)', '(x-5)(x+2)', '(x+10)(x-1)', '(x-10)(x+1)', '', 'A', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(6, 11, 3, 'Berapa keliling persegi jika panjang sisinya 12 cm?', '48 cm', '144 cm', '24 cm', '36 cm', '', 'A', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(7, 11, 3, 'Jika 2x−8=12, berapa nilai x?', '8', '10', '12', '16', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(8, 11, 3, 'Pecahan desimal dari 3/4 adalah?', '0.34', '0.75', '0.47', '0.5', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(9, 11, 3, 'Jarak antara kota A dan B adalah 180 km. Jika ditempuh dalam 3 jam, berapa kecepatan rata-ratanya?', '50 km/jam', '60 km/jam', '70 km/jam', '90 km/jam', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(10, 11, 3, 'Sudut siku-siku memiliki besar...', '45 derajat', '90 derajat', '180 derajat', '360 derajat', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(11, 11, 3, 'Jika 3(y−2)=15, berapa nilai y?', '5', '7', '8', '10', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(12, 11, 3, 'Berapakah hasil dari 2/5+1/10?', '45780', '45689', '45935', '42064', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(13, 11, 3, 'Volume balok dengan panjang 5, lebar 3, dan tinggi 4 adalah?', '12 cm³', '60 cm³', '30 cm³', '45 cm³', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(14, 11, 3, 'Barisan bilangan 2,4,8,16,… adalah barisan...', 'Aritmatika', 'Geometri', 'Fibonacci', 'Kuadrat', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(15, 11, 3, 'Tentukan nilai x dari persamaan 3x+4=x+10.', 'x=2', 'x=3', 'x=4', 'x=6', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(16, 11, NULL, 'Jika harga 5 pensil adalah Rp. 12.500, berapa harga 1 pensil?', '2000', '2500', '3000', '5000', '6000', 'B', '2025-10-17 15:27:30', '2025-10-17 17:22:00'),
(17, 11, 3, 'Himpunan bilangan prima antara 10 dan 20 adalah?', '{11, 13, 17, 19}', '{13, 15, 17, 19}', '{11, 13, 15, 17}', '{11, 19}', '', 'A', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(18, 11, 3, '$15% $ dari 200 adalah?', '20', '30', '45', '50', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(19, 11, 3, 'Median dari data 5, 8, 6, 9, 7 adalah?', '6', '7', '8', '9', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(20, 11, 3, 'Luas segitiga dengan alas 8 cm dan tinggi 5 cm adalah?', '20 cm²', '40 cm²', '13 cm²', '10 cm²', '', 'A', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(21, 11, 3, 'Bilangan bulat terbesar yang lebih kecil dari -5 adalah?', '-4', '-6', '-5', '0', '', 'B', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(22, 11, 3, 'Jika a=3 dan b=2, maka nilai 2a+3b adalah?', '9', '10', '12', '15', '', 'C', '2025-10-17 15:27:30', '2025-10-17 15:27:30'),
(23, 10, 3, 'Hasil dari 15 + 7 x 3 adalah?', '36', '46', '66', '54', '', 'A', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(24, 10, 3, 'Bentuk sederhana dari x2+3x−10 jika difaktorkan adalah?', '(x+5)(x-2)', '(x-5)(x+2)', '(x+10)(x-1)', '(x-10)(x+1)', '', 'A', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(25, 10, 3, 'Berapa keliling persegi jika panjang sisinya 12 cm?', '48 cm', '144 cm', '24 cm', '36 cm', '', 'A', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(26, 10, 3, 'Jika 2x−8=12, berapa nilai x?', '8', '10', '12', '16', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(27, 10, 3, 'Pecahan desimal dari 3/4 adalah?', '0.34', '0.75', '0.47', '0.5', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(28, 10, 3, 'Jarak antara kota A dan B adalah 180 km. Jika ditempuh dalam 3 jam, berapa kecepatan rata-ratanya?', '50 km/jam', '60 km/jam', '70 km/jam', '90 km/jam', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(29, 10, 3, 'Sudut siku-siku memiliki besar...', '45 derajat', '90 derajat', '180 derajat', '360 derajat', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(30, 10, 3, 'Jika 3(y−2)=15, berapa nilai y?', '5', '7', '8', '10', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(31, 10, 3, 'Berapakah hasil dari 2/5+1/10?', '45780', '45689', '45935', '42064', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(32, 10, 3, 'Volume balok dengan panjang 5, lebar 3, dan tinggi 4 adalah?', '12 cm³', '60 cm³', '30 cm³', '45 cm³', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(33, 10, 3, 'Barisan bilangan 2,4,8,16,… adalah barisan...', 'Aritmatika', 'Geometri', 'Fibonacci', 'Kuadrat', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(34, 10, 3, 'Tentukan nilai x dari persamaan 3x+4=x+10.', 'x=2', 'x=3', 'x=4', 'x=6', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(35, 10, 3, 'Jika harga 5 pensil adalah Rp. 12.500, berapa harga 1 pensil?', '2000', '2500', '3000', '5000', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(36, 10, 3, 'Himpunan bilangan prima antara 10 dan 20 adalah?', '{11, 13, 17, 19}', '{13, 15, 17, 19}', '{11, 13, 15, 17}', '{11, 19}', '', 'A', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(37, 10, 3, '$15% $ dari 200 adalah?', '20', '30', '45', '50', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(38, 10, 3, 'Median dari data 5, 8, 6, 9, 7 adalah?', '6', '7', '8', '9', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(39, 10, 3, 'Luas segitiga dengan alas 8 cm dan tinggi 5 cm adalah?', '20 cm²', '40 cm²', '13 cm²', '10 cm²', '', 'A', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(40, 10, 3, 'Bilangan bulat terbesar yang lebih kecil dari -5 adalah?', '-4', '-6', '-5', '0', '', 'B', '2025-10-20 20:59:55', '2025-10-20 20:59:55'),
(41, 10, 3, 'Jika a=3 dan b=2, maka nilai 2a+3b adalah?', '9', '10', '12', '15', '', 'C', '2025-10-20 20:59:55', '2025-10-20 20:59:55');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(3, 'Korektor'),
(2, 'Panitia'),
(4, 'Peserta'),
(1, 'Super Admin');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL,
  `event_id` int UNSIGNED DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `full_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `participant_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pin` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('waiting','login','finished') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'waiting',
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `event_id`, `username`, `password`, `full_name`, `participant_number`, `pin`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(9, 1, NULL, 'superadmin', '$2y$10$60ZeeFbyXtVBYXX2cJFEwuvp3yOSxFW2iy..HSNhZDwkKJ5LEmZjS', 'Admin Utama Sistem CAT', NULL, NULL, 'waiting', NULL, '2025-10-16 18:47:05', '2025-10-16 18:47:05'),
(10, 2, 3, 'panitia', '$2y$10$60ZeeFbyXtVBYXX2cJFEwuvp3yOSxFW2iy..HSNhZDwkKJ5LEmZjS', 'Bambang Edi Sunarto (Panitia Binangun)', NULL, NULL, 'waiting', NULL, '2025-10-16 18:47:05', '2025-10-16 18:47:05'),
(11, 4, 3, 'peserta01', '$2y$10$60ZeeFbyXtVBYXX2cJFEwuvp3yOSxFW2iy..HSNhZDwkKJ5LEmZjS', 'Calon Perangkat Desa 001', NULL, NULL, 'waiting', NULL, '2025-10-16 18:47:05', '2025-10-16 18:47:05'),
(14, 4, NULL, 'akun01', '$2y$10$jkE.ch4jKl61dCbNrdQI6.Wxdb9ria6fh/0EJQ6JYFahLe9UrfaEq', 'Aji Ppdsfdsf', NULL, NULL, 'waiting', NULL, '2025-10-17 09:18:27', '2025-10-17 09:18:27'),
(16, 4, 3, 'aji01', '$2y$10$53H9c85zNeYzGpzTj/RqNeyFtlv4.MJl3Rcl0KEkGOMUZXcBg3p0O', 'Aji', NULL, NULL, 'waiting', NULL, '2025-10-19 20:12:05', '2025-10-19 20:12:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_user_id_foreign` (`user_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_questions_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_questions_question_id_foreign` (`question_id`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_type_id` (`exam_type_id`);

--
-- Indexes for table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exam_settings`
--
ALTER TABLE `exam_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_exam_settings_exam_type` (`exam_type_id`);

--
-- Indexes for table `exam_setting_categories`
--
ALTER TABLE `exam_setting_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_setting_id` (`exam_setting_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `final_results`
--
ALTER TABLE `final_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_category_id_foreign` (`category_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `uk_participant_number` (`participant_number`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_sessions`
--
ALTER TABLE `exam_sessions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `exam_settings`
--
ALTER TABLE `exam_settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `exam_setting_categories`
--
ALTER TABLE `exam_setting_categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `exam_types`
--
ALTER TABLE `exam_types`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `final_results`
--
ALTER TABLE `final_results`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `exam_questions_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD CONSTRAINT `exam_results_ibfk_1` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_settings`
--
ALTER TABLE `exam_settings`
  ADD CONSTRAINT `fk_exam_settings_exam_type` FOREIGN KEY (`exam_type_id`) REFERENCES `exam_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_setting_categories`
--
ALTER TABLE `exam_setting_categories`
  ADD CONSTRAINT `exam_setting_categories_ibfk_1` FOREIGN KEY (`exam_setting_id`) REFERENCES `exam_settings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_setting_categories_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_types`
--
ALTER TABLE `exam_types`
  ADD CONSTRAINT `exam_types_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `final_results`
--
ALTER TABLE `final_results`
  ADD CONSTRAINT `final_results_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
