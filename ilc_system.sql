-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 10, 2026 at 07:43 AM
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
-- Database: `ilc_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `user_role` varchar(255) DEFAULT NULL,
  `event_type` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `subject_type` varchar(255) DEFAULT NULL,
  `subject_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `extra` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_name`, `user_role`, `event_type`, `description`, `subject_type`, `subject_id`, `ip_address`, `extra`, `created_at`, `updated_at`) VALUES
(3, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-15 07:08:15', '2026-05-15 07:08:15'),
(6, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-15 22:38:14', '2026-05-15 22:38:14'),
(11, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 01:32:00', '2026-05-16 01:32:00'),
(17, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for tiktok22222@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"tiktok22222@gmail.com\",\"attempts\":1}', '2026-05-16 02:12:40', '2026-05-16 02:12:40'),
(22, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for aeroniloreta7@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"aeroniloreta7@gmail.com\",\"attempts\":1}', '2026-05-16 03:26:18', '2026-05-16 03:26:18'),
(23, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for aeroniloreta7@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"aeroniloreta7@gmail.com\",\"attempts\":2}', '2026-05-16 03:26:35', '2026-05-16 03:26:35'),
(28, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 06:01:56', '2026-05-16 06:01:56'),
(29, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 06:04:08', '2026-05-16 06:04:08'),
(30, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 06:31:26', '2026-05-16 06:31:26'),
(33, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-16 07:38:30', '2026-05-16 07:38:30'),
(34, 81, 'Angela Ramos', 'student', 'login', 'Angela Ramos logged in', 'User', '81', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 08:40:28', '2026-05-16 08:40:28'),
(35, 81, 'Angela Ramos', 'student', 'logout', 'Angela Ramos logged out', 'User', '81', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 08:50:00', '2026-05-16 08:50:00'),
(36, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for temunai010101@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"temunai010101@gmail.com\",\"attempts\":1}', '2026-05-16 08:50:10', '2026-05-16 08:50:10'),
(37, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for temunai010101@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"temunai010101@gmail.com\",\"attempts\":2}', '2026-05-16 08:50:32', '2026-05-16 08:50:32'),
(38, 84, 'Joshua Villanueva', 'student', 'login', 'Joshua Villanueva logged in', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 08:51:01', '2026-05-16 08:51:01'),
(39, 84, 'Joshua Villanueva', 'student', 'logout', 'Joshua Villanueva logged out', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 08:59:44', '2026-05-16 08:59:44'),
(40, 85, 'Sofia Bautista', 'student', 'login', 'Sofia Bautista logged in', 'User', '85', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 08:59:51', '2026-05-16 08:59:51'),
(41, 25, 'Matalo Manalo', 'teacher', 'login', 'Matalo Manalo logged in', 'User', '25', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 09:14:41', '2026-05-16 09:14:41'),
(42, 25, 'Matalo Manalo', 'teacher', 'logout', 'Matalo Manalo logged out', 'User', '25', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 09:15:42', '2026-05-16 09:15:42'),
(43, 19, 'jologs bago', 'teacher', 'login', 'jologs bago logged in', 'User', '19', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 09:15:52', '2026-05-16 09:15:52'),
(44, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 09:28:19', '2026-05-16 09:28:19'),
(45, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 09:28:59', '2026-05-16 09:28:59'),
(46, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 10:00:09', '2026-05-16 10:00:09'),
(47, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 15:41:45', '2026-05-16 15:41:45'),
(48, 84, 'Joshua Villanueva', 'student', 'login', 'Joshua Villanueva logged in', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 15:42:20', '2026-05-16 15:42:20'),
(49, 84, 'Joshua Villanueva', 'student', 'logout', 'Joshua Villanueva logged out', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 15:46:13', '2026-05-16 15:46:13'),
(50, 84, 'Joshua Villanueva', 'student', 'login', 'Joshua Villanueva logged in', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 15:46:24', '2026-05-16 15:46:24'),
(51, 84, 'Joshua Villanueva', 'student', 'login', 'Joshua Villanueva logged in', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 15:49:57', '2026-05-16 15:49:57'),
(52, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-16 15:53:58', '2026-05-16 15:53:58'),
(53, 84, 'Joshua Villanueva', 'student', 'logout', 'Joshua Villanueva logged out', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 15:59:24', '2026-05-16 15:59:24'),
(54, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 15:59:47', '2026-05-16 15:59:47'),
(55, 1, 'Admin', 'admin', 'failed_login', 'Failed login attempt for aeroniloreta7@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"aeroniloreta7@gmail.com\",\"attempts\":1}', '2026-05-16 16:46:18', '2026-05-16 16:46:18'),
(56, 89, 'Aeron Iloreta', 'student', 'login', 'Aeron Iloreta logged in', 'User', '89', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 16:47:02', '2026-05-16 16:47:02'),
(57, 89, 'Aeron Iloreta', 'student', 'login', 'Aeron Iloreta logged in', 'User', '89', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 16:49:34', '2026-05-16 16:49:34'),
(58, 89, 'Aeron Iloreta', 'student', 'logout', 'Aeron Iloreta logged out', 'User', '89', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 16:50:18', '2026-05-16 16:50:18'),
(59, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 16:50:57', '2026-05-16 16:50:57'),
(60, 84, 'Joshua Villanueva', 'student', 'logout', 'Joshua Villanueva logged out', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 16:51:07', '2026-05-16 16:51:07'),
(61, 89, 'Aeron Iloreta', 'student', 'login', 'Aeron Iloreta logged in', 'User', '89', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 16:51:37', '2026-05-16 16:51:37'),
(62, 24, 'batang Reyes', 'teacher', 'logout', 'batang Reyes logged out', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 17:05:56', '2026-05-16 17:05:56'),
(63, 40, 'Daniel Edikk', 'teacher', 'login', 'Daniel Edikk logged in', 'User', '40', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 17:06:55', '2026-05-16 17:06:55'),
(64, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-16 17:09:32', '2026-05-16 17:09:32'),
(65, 81, 'Angela Ramos', 'student', 'login', 'Angela Ramos logged in', 'User', '81', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 17:10:52', '2026-05-16 17:10:52'),
(66, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 17:18:57', '2026-05-16 17:18:57'),
(67, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 17:21:05', '2026-05-16 17:21:05'),
(68, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 17:42:32', '2026-05-16 17:42:32'),
(69, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-16 17:42:55', '2026-05-16 17:42:55'),
(70, 89, 'Aeron Iloreta', 'student', 'logout', 'Aeron Iloreta logged out', 'User', '89', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 17:59:18', '2026-05-16 17:59:18'),
(71, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for aeroniloret8@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"aeroniloret8@gmail.com\",\"attempts\":1}', '2026-05-16 18:00:05', '2026-05-16 18:00:05'),
(72, 91, 'Jude Iloreta', 'student', 'login', 'Jude Iloreta logged in', 'User', '91', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 18:02:23', '2026-05-16 18:02:23'),
(73, 83, 'Christine Mendoza', 'student', 'login', 'Christine Mendoza logged in', 'User', '83', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-16 18:25:48', '2026-05-16 18:25:48'),
(74, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-17 08:57:22', '2026-05-17 08:57:22'),
(75, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-17 10:29:15', '2026-05-17 10:29:15'),
(76, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-18 09:21:35', '2026-05-18 09:21:35'),
(77, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-18 09:21:51', '2026-05-18 09:21:51'),
(78, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for superadmin@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"superadmin@ilc.com\",\"attempts\":1}', '2026-05-18 09:26:51', '2026-05-18 09:26:51'),
(79, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for superadmin@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"superadmin@ilc.com\",\"attempts\":2}', '2026-05-18 09:27:04', '2026-05-18 09:27:04'),
(80, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 09:27:19', '2026-05-18 09:27:19'),
(81, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 09:28:23', '2026-05-18 09:28:23'),
(82, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-18 09:38:07', '2026-05-18 09:38:07'),
(83, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-18 09:47:24', '2026-05-18 09:47:24'),
(84, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 09:47:39', '2026-05-18 09:47:39'),
(85, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 09:47:46', '2026-05-18 09:47:46'),
(86, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 10:15:48', '2026-05-18 10:15:48'),
(87, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-18 10:16:05', '2026-05-18 10:16:05'),
(88, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-18 10:16:20', '2026-05-18 10:16:20'),
(89, 24, 'batang Reyes', 'teacher', 'logout', 'batang Reyes logged out', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-18 10:16:33', '2026-05-18 10:16:33'),
(90, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-18 10:16:42', '2026-05-18 10:16:42'),
(91, 84, 'Joshua Villanueva', 'student', 'login', 'Joshua Villanueva logged in', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-19 04:54:05', '2026-05-19 04:54:05'),
(92, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-19 04:54:43', '2026-05-19 04:54:43'),
(93, 84, 'Joshua Villanueva', 'student', 'logout', 'Joshua Villanueva logged out', 'User', '84', '127.0.0.1', '{\"role\":\"student\"}', '2026-05-19 05:12:11', '2026-05-19 05:12:11'),
(94, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-19 05:12:37', '2026-05-19 05:12:37'),
(95, 24, 'batang Reyes', 'teacher', 'logout', 'batang Reyes logged out', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-05-19 05:26:13', '2026-05-19 05:26:13'),
(96, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-19 05:26:36', '2026-05-19 05:26:36'),
(97, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-19 05:27:04', '2026-05-19 05:27:04'),
(98, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-30 08:30:56', '2026-05-30 08:30:56'),
(99, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-30 09:26:35', '2026-05-30 09:26:35'),
(100, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-30 22:56:52', '2026-05-30 22:56:52'),
(101, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-05-31 00:59:37', '2026-05-31 00:59:37'),
(102, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-31 07:25:14', '2026-05-31 07:25:14'),
(103, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-05-31 07:25:49', '2026-05-31 07:25:49'),
(104, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-06-01 23:34:41', '2026-06-01 23:34:41'),
(105, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-06-01 23:35:57', '2026-06-01 23:35:57'),
(106, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-06-02 02:18:51', '2026-06-02 02:18:51'),
(107, 92, 'Cashier Name', 'cashier', 'failed_login', 'Failed login attempt for teacher7@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher7@ilc.com\",\"attempts\":1}', '2026-06-02 06:40:33', '2026-06-02 06:40:33'),
(108, 24, 'batang Reyes', 'teacher', 'login', 'batang Reyes logged in', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-06-02 06:40:43', '2026-06-02 06:40:43'),
(109, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-06-02 06:46:22', '2026-06-02 06:46:22'),
(110, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-06-02 06:58:22', '2026-06-02 06:58:22'),
(111, 24, 'batang Reyes', 'teacher', 'logout', 'batang Reyes logged out', 'User', '24', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-06-02 07:18:45', '2026-06-02 07:18:45'),
(112, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-06-02 07:18:59', '2026-06-02 07:18:59'),
(113, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-06-02 08:07:18', '2026-06-02 08:07:18'),
(114, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-06-04 07:32:50', '2026-06-04 07:32:50'),
(115, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-06-04 08:16:21', '2026-06-04 08:16:21'),
(116, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-08-16 23:58:35', '2026-08-16 23:58:35'),
(117, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-08-17 00:00:27', '2026-08-17 00:00:27'),
(118, 2, 'Super Admin', 'superadmin', 'failed_login', 'Failed login attempt for teacher@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher@ilc.com\",\"attempts\":1}', '2026-08-17 00:01:00', '2026-08-17 00:01:00'),
(119, 2, 'Super Admin', 'superadmin', 'failed_login', 'Failed login attempt for teacher@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher@ilc.com\",\"attempts\":2}', '2026-08-17 00:01:12', '2026-08-17 00:01:12'),
(120, 2, 'Super Admin', 'superadmin', 'failed_login', 'Failed login attempt for teacher@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher@ilc.com\",\"attempts\":3}', '2026-08-17 00:01:20', '2026-08-17 00:01:20'),
(121, 2, 'Super Admin', 'superadmin', 'failed_login', 'Failed login attempt for teacher@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher@ilc.com\",\"attempts\":4}', '2026-08-17 00:01:30', '2026-08-17 00:01:30'),
(122, 2, 'Super Admin', 'superadmin', 'failed_login', 'Failed login attempt for teacher@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher@ilc.com\",\"attempts\":5}', '2026-08-17 00:01:36', '2026-08-17 00:01:36'),
(123, 19, 'jologs bago', 'teacher', 'login', 'jologs bago logged in', 'User', '19', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-08-17 00:06:42', '2026-08-17 00:06:42'),
(124, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-02 05:55:23', '2026-09-02 05:55:23'),
(125, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-09-02 06:32:02', '2026-09-02 06:32:02'),
(126, 19, 'jologs bago', 'teacher', 'login', 'jologs bago logged in', 'User', '19', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-09-02 06:32:49', '2026-09-02 06:32:49'),
(127, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-02 06:58:38', '2026-09-02 06:58:38'),
(128, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-02 06:58:51', '2026-09-02 06:58:51'),
(129, 93, 'Juan Cruz', 'student', 'logout', 'Juan Cruz logged out', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-02 08:16:03', '2026-09-02 08:16:03'),
(130, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-02 08:16:14', '2026-09-02 08:16:14'),
(131, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-02 23:04:19', '2026-09-02 23:04:19'),
(132, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-03 07:33:37', '2026-09-03 07:33:37'),
(133, 19, 'jologs bago', 'teacher', 'login', 'jologs bago logged in', 'User', '19', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-09-03 07:34:20', '2026-09-03 07:34:20'),
(134, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-03 07:35:04', '2026-09-03 07:35:04'),
(135, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-03 09:45:50', '2026-09-03 09:45:50'),
(136, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 09:21:46', '2026-09-04 09:21:46'),
(137, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 09:24:37', '2026-09-04 09:24:37'),
(138, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for cloud.bgo@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"cloud.bgo@gmail.com\",\"attempts\":1}', '2026-09-04 09:24:57', '2026-09-04 09:24:57'),
(139, 102, 'Cloud Nathaniel Go', 'student', 'login', 'Cloud Nathaniel Go logged in', 'User', '102', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-04 09:25:32', '2026-09-04 09:25:32'),
(140, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 09:32:51', '2026-09-04 09:32:51'),
(141, 102, 'Cloud Nathaniel Go', 'student', 'logout', 'Cloud Nathaniel Go logged out', 'User', '102', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-04 09:58:26', '2026-09-04 09:58:26'),
(142, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 09:58:37', '2026-09-04 09:58:37'),
(143, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 10:08:40', '2026-09-04 10:08:40'),
(144, 102, 'Cloud Nathaniel Go', 'student', 'login', 'Cloud Nathaniel Go logged in', 'User', '102', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-04 10:08:56', '2026-09-04 10:08:56'),
(145, 102, 'Cloud Nathaniel Go', 'student', 'logout', 'Cloud Nathaniel Go logged out', 'User', '102', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-04 10:10:23', '2026-09-04 10:10:23'),
(146, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 10:10:34', '2026-09-04 10:10:34'),
(147, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-04 10:11:50', '2026-09-04 10:11:50'),
(148, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for teacher1@ilc.com', NULL, NULL, '127.0.0.1', '{\"email\":\"teacher1@ilc.com\",\"attempts\":1}', '2026-09-04 10:12:04', '2026-09-04 10:12:04'),
(149, 18, 'john deGuzman', 'teacher', 'login', 'john deGuzman logged in', 'User', '18', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-09-04 10:12:11', '2026-09-04 10:12:11'),
(150, 18, 'john deGuzman', 'teacher', 'login', 'john deGuzman logged in', 'User', '18', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-09-04 10:23:38', '2026-09-04 10:23:38'),
(151, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-07 08:08:10', '2026-09-07 08:08:10'),
(152, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-09-07 08:08:29', '2026-09-07 08:08:29'),
(153, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-07 16:14:12', '2026-09-07 16:14:12'),
(154, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-09-07 16:14:56', '2026-09-07 16:14:56'),
(155, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-07 16:18:14', '2026-09-07 16:18:14'),
(156, 1, 'Admin', 'admin', 'login', 'Admin logged in', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-09 16:17:31', '2026-09-09 16:17:31'),
(157, 1, 'Admin', 'admin', 'logout', 'Admin logged out', 'User', '1', '127.0.0.1', '{\"role\":\"admin\"}', '2026-09-09 16:35:52', '2026-09-09 16:35:52'),
(158, 2, 'Super Admin', 'superadmin', 'login', 'Super Admin logged in', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-09-09 16:36:04', '2026-09-09 16:36:04'),
(159, 2, 'Super Admin', 'superadmin', 'logout', 'Super Admin logged out', 'User', '2', '127.0.0.1', '{\"role\":\"superadmin\"}', '2026-09-09 16:37:52', '2026-09-09 16:37:52'),
(160, NULL, NULL, NULL, 'failed_login', 'Failed login attempt for doteditt@gmail.com', NULL, NULL, '127.0.0.1', '{\"email\":\"doteditt@gmail.com\",\"attempts\":1}', '2026-09-09 16:38:05', '2026-09-09 16:38:05'),
(161, 93, 'Juan Cruz', 'student', 'login', 'Juan Cruz logged in', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-09 16:38:26', '2026-09-09 16:38:26'),
(162, 93, 'Juan Cruz', 'student', 'logout', 'Juan Cruz logged out', 'User', '93', '127.0.0.1', '{\"role\":\"student\"}', '2026-09-09 16:39:03', '2026-09-09 16:39:03'),
(163, 19, 'jologs bago', 'teacher', 'login', 'jologs bago logged in', 'User', '19', '127.0.0.1', '{\"role\":\"teacher\"}', '2026-09-09 16:39:21', '2026-09-09 16:39:21');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `audience` enum('all','section','parents','teachers') NOT NULL DEFAULT 'all',
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `category` enum('academic','reminder','activity','general','enrollment') NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `teacher_id`, `title`, `content`, `audience`, `section_id`, `category`, `is_active`, `image`, `created_at`, `updated_at`) VALUES
(1, 1, 'balitang balita', 'sira ang motor ni maxboro', 'all', NULL, 'general', 1, NULL, '2026-06-02 07:35:23', '2026-06-02 08:10:58');

-- --------------------------------------------------------

--
-- Table structure for table `attendances`
--

CREATE TABLE `attendances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent','late','excused') NOT NULL DEFAULT 'present',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') NOT NULL DEFAULT 'unread',
  `ip_address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `section` varchar(100) DEFAULT NULL,
  `school_year` varchar(255) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `assessment_status` varchar(30) NOT NULL DEFAULT 'pending',
  `assessment_notes` text DEFAULT NULL,
  `assessed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assessed_at` timestamp NULL DEFAULT NULL,
  `teacher_recommendation` varchar(30) DEFAULT NULL,
  `teacher_recommendation_notes` text DEFAULT NULL,
  `teacher_recommended_by` bigint(20) UNSIGNED DEFAULT NULL,
  `teacher_recommended_at` timestamp NULL DEFAULT NULL,
  `student_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`student_data`)),
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `declined_at` timestamp NULL DEFAULT NULL,
  `declined_by` bigint(20) UNSIGNED DEFAULT NULL,
  `decline_reason` text DEFAULT NULL,
  `decline_storage` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) NOT NULL DEFAULT 'pending',
  `payment_type` varchar(20) NOT NULL DEFAULT 'full',
  `payment_option` varchar(255) DEFAULT NULL,
  `downpayment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `monthly_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_breakdown` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_breakdown`)),
  `installment_schedule` int(11) DEFAULT NULL,
  `installment_number` int(11) NOT NULL DEFAULT 1,
  `payment_due_date` date DEFAULT NULL,
  `total_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remaining_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `penalty_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_payment_count` int(11) NOT NULL DEFAULT 0,
  `next_installment_date` date DEFAULT NULL,
  `reminder_sent` tinyint(1) NOT NULL DEFAULT 0,
  `reminder_sent_at` timestamp NULL DEFAULT NULL,
  `account_blocked` tinyint(1) NOT NULL DEFAULT 0,
  `payment_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `payment_method` varchar(255) DEFAULT NULL,
  `payment_reference` varchar(255) DEFAULT NULL,
  `payment_updated_at` timestamp NULL DEFAULT NULL,
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `reference_number`, `user_id`, `grade_level`, `section`, `school_year`, `status`, `assessment_status`, `assessment_notes`, `assessed_by`, `assessed_at`, `teacher_recommendation`, `teacher_recommendation_notes`, `teacher_recommended_by`, `teacher_recommended_at`, `student_data`, `approved_at`, `approved_by`, `declined_at`, `declined_by`, `decline_reason`, `decline_storage`, `payment_status`, `payment_type`, `payment_option`, `downpayment_amount`, `monthly_amount`, `payment_breakdown`, `installment_schedule`, `installment_number`, `payment_due_date`, `total_fee`, `remaining_balance`, `penalty_amount`, `late_payment_count`, `next_installment_date`, `reminder_sent`, `reminder_sent_at`, `account_blocked`, `payment_amount`, `payment_method`, `payment_reference`, `payment_updated_at`, `enrolled_at`, `created_at`, `updated_at`) VALUES
(69, 'ENR-2CRVGFCH', 93, 'nursery', 'Nusery', '2026-2027', 'enrolled', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"first_name\":\"Juan\",\"last_name\":\"Cruz\",\"middle_name\":null,\"suffix\":\"\",\"gender\":\"male\",\"birthdate\":\"2024-10-22\",\"place_of_birth\":\"Papaya\",\"grade_level\":\"nursery\",\"student_type\":\"new\",\"last_school\":\"Central School\",\"mother_name\":\"Mader Cruz\",\"mother_age\":\"34\",\"father_name\":\"Fader Cruz\",\"father_age\":\"34\",\"religious_affiliation\":\"Catholic\",\"region\":\"REGION III (CENTRAL LUZON)\",\"province\":\"NUEVA ECIJA\",\"city\":\"LLANERA\",\"barangay\":\"Mabini\",\"street_address\":\"45\",\"zip_code\":\"5454\",\"guardian_name\":\"Mader Cruz\",\"relationship\":\"mother\",\"guardian_occupation\":\"Ofw\",\"guardian_phone\":\"9123233424\",\"student_email\":\"shopeepa010101@gmail.com\",\"blood_type\":\"O-\",\"allergies\":\"\",\"medical_conditions\":\"\"}', '2026-06-01 23:34:56', 1, NULL, NULL, NULL, NULL, 'paid', 'full', 'A', 0.00, 0.00, '\"{\\\"tuition_fee\\\":7505,\\\"misc_reg_pta\\\":2800,\\\"books\\\":3550,\\\"insurance\\\":150,\\\"electric_bill\\\":2000,\\\"base_total\\\":16005,\\\"discount\\\":1501,\\\"total_due\\\":14504,\\\"payment_type\\\":\\\"full\\\",\\\"downpayment\\\":0,\\\"monthly_amount\\\":0}\"', NULL, 1, NULL, 14504.00, 0.00, 0.00, 0, NULL, 0, NULL, 0, 14504.00, NULL, NULL, NULL, '2026-06-04 08:14:28', '2026-06-01 23:34:26', '2026-09-09 15:16:23'),
(70, 'XENDITTEST-101', 101, 'grade1', NULL, '2025-2026', 'enrolled', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"first_name\":\"Xendit\",\"last_name\":\"Test\"}', NULL, NULL, NULL, NULL, NULL, NULL, 'partial', 'installment', 'B', 7500.00, 1056.10, NULL, NULL, 1, NULL, 10000.00, 8050.00, 0.00, 0, NULL, 0, NULL, 0, 1750.00, NULL, NULL, NULL, NULL, '2026-09-02 07:10:36', '2026-09-10 05:38:54'),
(71, 'ENR-GI3F9BIQ', 102, 'grade3', 'Grade3', '2026-2027', 'enrolled', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '{\"first_name\":\"Cloud Nathaniel\",\"last_name\":\"Go\",\"middle_name\":\"B\",\"suffix\":\"\",\"gender\":\"male\",\"birthdate\":\"2004-12-17\",\"place_of_birth\":\"Niluwal Sa Sawmill\",\"grade_level\":\"grade3\",\"student_type\":\"new\",\"last_school\":\"N\\/a\",\"mother_name\":\"Maria Go\",\"mother_age\":\"30\",\"father_name\":\"Juan Go\",\"father_age\":\"30\",\"religious_affiliation\":\"Inc\",\"region\":\"REGION III (CENTRAL LUZON)\",\"province\":\"NUEVA ECIJA\",\"city\":\"GENERAL TINIO (PAPAYA)\",\"barangay\":\"Sampaguita\",\"street_address\":\"Purok Sawmill\",\"zip_code\":\"3104\",\"guardian_name\":\"Maria Go\",\"relationship\":\"mother\",\"guardian_occupation\":\"N\\/a\",\"guardian_phone\":\"9123457689\",\"student_email\":\"cloud.bgo@gmail.com\",\"blood_type\":\"O-\",\"allergies\":\"Sa Hipon\",\"medical_conditions\":\"Retarded\"}', '2026-09-04 09:22:15', 1, NULL, NULL, NULL, NULL, 'paid', 'full', 'A', 0.00, 0.00, NULL, NULL, 1, NULL, 0.00, 0.00, 0.00, 0, NULL, 0, NULL, 0, 16005.00, NULL, NULL, NULL, '2026-09-04 09:47:25', '2026-09-04 09:21:26', '2026-09-10 05:18:40');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, 'e82bf178-a118-4354-983b-7208d458a12b', 'database', 'default', '{\"uuid\":\"e82bf178-a118-4354-983b-7208d458a12b\",\"displayName\":\"App\\\\Mail\\\\TeacherAccountCreated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\TeacherAccountCreated\\\":4:{s:7:\\\"teacher\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:9;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"password\\\";s:11:\\\"2026-155363\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:27:\\\"testsystemnamin01@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1776699389,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:785\nStack trace:\n#0 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\TeacherAccountCreated->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(97): App\\Mail\\TeacherAccountCreated->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\TeacherAccountCreated->__unserialize(Array)\n#4 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(95): unserialize(\'O:34:\"Illuminat...\')\n#5 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(62): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(358): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(273): Illuminate\\Container\\Container->call(Array)\n#18 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Command\\Command.php(291): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(242): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(1107): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-04-20 07:40:12'),
(2, '10da667d-cc49-421e-a089-73ad7874e8f6', 'database', 'default', '{\"uuid\":\"10da667d-cc49-421e-a089-73ad7874e8f6\",\"displayName\":\"App\\\\Mail\\\\TeacherAccountCreated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\TeacherAccountCreated\\\":4:{s:7:\\\"teacher\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:10;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"password\\\";s:11:\\\"2026-902256\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:18:\\\"doteditt@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1776699457,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\User]. in C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Database\\Eloquent\\Builder.php:785\nStack trace:\n#0 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(112): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesAndRestoresModelIdentifiers.php(63): App\\Mail\\TeacherAccountCreated->restoreModel(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#2 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\SerializesModels.php(97): App\\Mail\\TeacherAccountCreated->getRestoredPropertyValue(Object(Illuminate\\Contracts\\Database\\ModelIdentifier))\n#3 [internal function]: App\\Mail\\TeacherAccountCreated->__unserialize(Array)\n#4 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(95): unserialize(\'O:34:\"Illuminat...\')\n#5 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\CallQueuedHandler.php(62): Illuminate\\Queue\\CallQueuedHandler->getCommand(Array)\n#6 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Jobs\\Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#7 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#9 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Worker.php(358): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#10 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(148): Illuminate\\Queue\\Worker->runNextJob(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#11 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Queue\\Console\\WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#12 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#15 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#16 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Container\\Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#17 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(273): Illuminate\\Container\\Container->call(Array)\n#18 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Command\\Command.php(291): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#19 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Console\\Command.php(242): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#20 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(1107): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#21 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#22 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\symfony\\console\\Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#23 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Console\\Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#24 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\vendor\\laravel\\framework\\src\\Illuminate\\Foundation\\Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#25 C:\\Users\\ron28\\Desktop\\ilc website -  new nanaman\\ilc-website-system\\artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#26 {main}', '2026-04-26 08:31:55');

-- --------------------------------------------------------

--
-- Table structure for table `fee_components`
--

CREATE TABLE `fee_components` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `option` varchar(1) DEFAULT NULL,
  `grade_level` varchar(20) DEFAULT NULL,
  `fee_type` varchar(30) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_components`
--

INSERT INTO `fee_components` (`id`, `option`, `grade_level`, `fee_type`, `amount`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, 'tuition', 7505.00, '2026-09-09 15:27:01', '2026-09-09 16:16:08'),
(2, NULL, NULL, 'misc', 2800.00, '2026-09-09 15:27:01', '2026-09-09 16:16:08'),
(3, NULL, NULL, 'insurance', 150.00, '2026-09-09 15:27:01', '2026-09-09 16:16:08'),
(4, NULL, NULL, 'electric', 2000.00, '2026-09-09 15:27:01', '2026-09-09 16:16:08'),
(5, NULL, 'nursery', 'books', 3550.00, '2026-09-09 15:27:01', '2026-09-09 16:16:08'),
(6, NULL, 'kindergarten', 'books', 3550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(7, NULL, 'grade1', 'books', 4550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(8, NULL, 'grade2', 'books', 4550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(9, NULL, 'grade3', 'books', 5050.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(10, NULL, 'grade4', 'books', 5550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(11, NULL, 'grade5', 'books', 5550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(12, NULL, 'grade6', 'books', 5550.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(13, 'A', NULL, 'discount', 1500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(14, 'B', 'nursery', 'downpayment', 6500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(15, 'B', 'kindergarten', 'downpayment', 6500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(16, 'B', 'grade1', 'downpayment', 7500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(17, 'B', 'grade2', 'downpayment', 7500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(18, 'B', 'grade3', 'downpayment', 8000.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(19, 'B', 'grade4', 'downpayment', 8500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(20, 'B', 'grade5', 'downpayment', 8500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(21, 'B', 'grade6', 'downpayment', 8500.00, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(22, 'B', NULL, 'monthly_tuition', 833.88, '2026-09-09 15:27:02', '2026-09-09 16:16:08'),
(23, 'B', NULL, 'monthly_electric', 222.22, '2026-09-09 15:27:03', '2026-09-09 16:16:08'),
(24, 'C', 'grade1', 'downpayment', 5500.00, '2026-09-09 15:27:03', '2026-09-09 16:16:08'),
(25, 'C', 'grade2', 'downpayment', 5500.00, '2026-09-09 15:27:03', '2026-09-09 16:16:08'),
(26, 'C', 'grade3', 'downpayment', 6000.00, '2026-09-09 15:27:03', '2026-09-09 16:16:08'),
(27, 'C', 'grade4', 'downpayment', 6500.00, '2026-09-09 15:27:04', '2026-09-09 16:16:08'),
(28, 'C', 'grade5', 'downpayment', 6500.00, '2026-09-09 15:27:04', '2026-09-09 16:16:08'),
(29, 'C', 'grade6', 'downpayment', 6500.00, '2026-09-09 15:27:04', '2026-09-09 16:16:08'),
(30, 'C', NULL, 'monthly_tuition', 833.88, '2026-09-09 15:27:05', '2026-09-09 16:16:08'),
(31, 'C', NULL, 'monthly_misc', 222.22, '2026-09-09 15:27:05', '2026-09-09 16:16:08'),
(32, 'C', NULL, 'monthly_electric', 222.22, '2026-09-09 15:27:05', '2026-09-09 16:16:08'),
(33, 'D', 'nursery', 'downpayment', 4505.00, '2026-09-09 15:27:06', '2026-09-09 16:16:08'),
(34, 'D', 'kindergarten', 'downpayment', 4505.00, '2026-09-09 15:27:06', '2026-09-09 16:16:08'),
(35, 'D', NULL, 'monthly_tuition', 833.88, '2026-09-09 15:27:06', '2026-09-09 16:16:08'),
(36, 'D', NULL, 'monthly_misc', 222.22, '2026-09-09 15:27:06', '2026-09-09 16:16:08'),
(37, 'D', NULL, 'monthly_electric', 222.22, '2026-09-09 15:27:07', '2026-09-09 16:16:08');

-- --------------------------------------------------------

--
-- Table structure for table `fee_settings`
--

CREATE TABLE `fee_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tuition` decimal(10,2) NOT NULL DEFAULT 7505.00,
  `misc` decimal(10,2) NOT NULL DEFAULT 2800.00,
  `insurance` decimal(10,2) NOT NULL DEFAULT 150.00,
  `electric` decimal(10,2) NOT NULL DEFAULT 2000.00,
  `books_nursery` decimal(10,2) NOT NULL DEFAULT 3550.00,
  `books_grade1` decimal(10,2) NOT NULL DEFAULT 4550.00,
  `books_grade3` decimal(10,2) NOT NULL DEFAULT 5050.00,
  `books_grade4` decimal(10,2) NOT NULL DEFAULT 5550.00,
  `option_a_discount` decimal(10,2) NOT NULL DEFAULT 1501.00,
  `optb_monthly_tuition` decimal(10,2) NOT NULL DEFAULT 833.89,
  `optb_monthly_electric` decimal(10,2) NOT NULL DEFAULT 222.22,
  `optb_dp_nursery` decimal(10,2) NOT NULL DEFAULT 6500.00,
  `optb_dp_kinder` decimal(10,2) NOT NULL DEFAULT 6500.00,
  `optb_dp_grade1` decimal(10,2) NOT NULL DEFAULT 7500.00,
  `optb_dp_grade3` decimal(10,2) NOT NULL DEFAULT 8000.00,
  `optb_dp_grade4` decimal(10,2) NOT NULL DEFAULT 8500.00,
  `optc_monthly_tuition` decimal(10,2) NOT NULL DEFAULT 833.89,
  `optc_monthly_misc` decimal(10,2) NOT NULL DEFAULT 311.11,
  `optc_monthly_electric` decimal(10,2) NOT NULL DEFAULT 222.22,
  `optc_dp_grade1` decimal(10,2) NOT NULL DEFAULT 5500.00,
  `optc_dp_grade3` decimal(10,2) NOT NULL DEFAULT 6000.00,
  `optc_dp_grade4` decimal(10,2) NOT NULL DEFAULT 6500.00,
  `optd_monthly_tuition` decimal(10,2) NOT NULL DEFAULT 833.89,
  `optd_monthly_misc` decimal(10,2) NOT NULL DEFAULT 311.11,
  `optd_monthly_electric` decimal(10,2) NOT NULL DEFAULT 222.22,
  `optd_dp_nursery` decimal(10,2) NOT NULL DEFAULT 4505.00,
  `optd_dp_kinder` decimal(10,2) NOT NULL DEFAULT 4505.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `optc_dp_nursery` decimal(10,2) NOT NULL DEFAULT 5400.00,
  `optc_dp_kinder` decimal(10,2) NOT NULL DEFAULT 5400.00,
  `optd_dp_grade1` decimal(10,2) NOT NULL DEFAULT 4800.00,
  `optd_dp_grade3` decimal(10,2) NOT NULL DEFAULT 5100.00,
  `optd_dp_grade4` decimal(10,2) NOT NULL DEFAULT 5600.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_settings`
--

INSERT INTO `fee_settings` (`id`, `tuition`, `misc`, `insurance`, `electric`, `books_nursery`, `books_grade1`, `books_grade3`, `books_grade4`, `option_a_discount`, `optb_monthly_tuition`, `optb_monthly_electric`, `optb_dp_nursery`, `optb_dp_kinder`, `optb_dp_grade1`, `optb_dp_grade3`, `optb_dp_grade4`, `optc_monthly_tuition`, `optc_monthly_misc`, `optc_monthly_electric`, `optc_dp_grade1`, `optc_dp_grade3`, `optc_dp_grade4`, `optd_monthly_tuition`, `optd_monthly_misc`, `optd_monthly_electric`, `optd_dp_nursery`, `optd_dp_kinder`, `created_at`, `updated_at`, `optc_dp_nursery`, `optc_dp_kinder`, `optd_dp_grade1`, `optd_dp_grade3`, `optd_dp_grade4`) VALUES
(1, 7505.00, 2800.00, 150.00, 2000.00, 3550.00, 4550.00, 5050.00, 5550.00, 1500.00, 833.88, 222.22, 6500.00, 6500.00, 7500.00, 8000.00, 8500.00, 833.88, 222.22, 222.22, 5500.00, 6000.00, 6500.00, 833.88, 222.22, 222.22, 4505.00, 4505.00, '2026-04-27 21:16:29', '2026-09-09 16:16:08', 0.00, 0.00, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade` decimal(5,2) DEFAULT NULL,
  `descriptive_grade` varchar(10) DEFAULT NULL,
  `school_year` varchar(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'submitted',
  `term` int(11) DEFAULT NULL,
  `remarks` varchar(255) NOT NULL DEFAULT 'Passed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `student_id`, `teacher_id`, `subject_id`, `enrollment_id`, `grade`, `descriptive_grade`, `school_year`, `status`, `term`, `remarks`, `created_at`, `updated_at`) VALUES
(55, 102, 18, 29, 71, 80.00, NULL, '2026-2027', 'submitted', 1, 'Passed', '2026-09-04 10:13:36', '2026-09-04 10:14:13');

-- --------------------------------------------------------

--
-- Table structure for table `guardians`
--

CREATE TABLE `guardians` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(150) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `age` smallint(5) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `guardians`
--

INSERT INTO `guardians` (`id`, `user_id`, `name`, `relationship`, `contact`, `age`, `email`, `occupation`, `created_at`, `updated_at`) VALUES
(41, 93, 'Mader Cruz', 'Mother', '9123233424', NULL, 'shopeepa010101@gmail.com', 'Ofw', '2026-06-01 23:34:26', '2026-06-01 23:34:56'),
(42, 102, 'Maria Go', 'Mother', '9123457689', NULL, 'cloud.bgo@gmail.com', 'N/a', '2026-09-04 09:21:26', '2026-09-04 09:22:16');

-- --------------------------------------------------------

--
-- Table structure for table `guidance_records`
--

CREATE TABLE `guidance_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `counselor_id` bigint(20) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `concern_type` varchar(255) NOT NULL,
  `concern_description` text NOT NULL,
  `action_taken` text DEFAULT NULL,
  `recommendations` text DEFAULT NULL,
  `follow_up_date` date DEFAULT NULL,
  `status` enum('open','in_progress','resolved','closed') NOT NULL DEFAULT 'open',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(3, 'default', '{\"uuid\":\"f3902633-0a71-4bf6-b33a-2defcec191fb\",\"displayName\":\"App\\\\Mail\\\\TeacherAccountCreated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\TeacherAccountCreated\\\":4:{s:7:\\\"teacher\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:36;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"password\\\";s:11:\\\"2026-248187\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:18:\\\"rontechh@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1777220252,\"delay\":null}', 0, NULL, 1777220252, 1777220252),
(4, 'default', '{\"uuid\":\"fb032cf8-4e4b-4b0c-b4a2-5e262f6e1616\",\"displayName\":\"App\\\\Mail\\\\TeacherAccountCreated\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:30:\\\"App\\\\Mail\\\\TeacherAccountCreated\\\":4:{s:7:\\\"teacher\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:37;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"password\\\";s:11:\\\"2026-868010\\\";s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:18:\\\"rontechh@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1777220770,\"delay\":null}', 0, NULL, 1777220770, 1777220770),
(5, 'default', '{\"uuid\":\"9b9acc03-fe04-48e6-b595-17e6d62870fd\",\"displayName\":\"App\\\\Mail\\\\EnrollmentDeclined\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:27:\\\"App\\\\Mail\\\\EnrollmentDeclined\\\":3:{s:10:\\\"enrollment\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:21:\\\"App\\\\Models\\\\Enrollment\\\";s:2:\\\"id\\\";i:24;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:6:\\\"reason\\\";s:4:\\\"pass\\\";s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1778424204,\"delay\":null}', 0, NULL, 1778424204, 1778424204),
(6, 'default', '{\"uuid\":\"b5741c7d-a204-41e4-8b7f-cc622f11e0fb\",\"displayName\":\"App\\\\Mail\\\\EnrollmentDeclined\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"deleteWhenMissingModels\":false,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:27:\\\"App\\\\Mail\\\\EnrollmentDeclined\\\":3:{s:10:\\\"enrollment\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:21:\\\"App\\\\Models\\\\Enrollment\\\";s:2:\\\"id\\\";i:24;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:6:\\\"reason\\\";s:4:\\\"pass\\\";s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:3:\\\"job\\\";N;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;}\",\"batchId\":null},\"createdAt\":1778424205,\"delay\":null}', 0, NULL, 1778424205, 1778424205);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_add_role_and_is_active_to_users_table', 1),
(5, '2024_01_01_000002_create_student_tables', 1),
(6, '2026_03_29_160004_create_otp_codes_table', 1),
(7, '2026_03_29_160456_add_auth_columns_to_users_table', 1),
(8, '2026_04_13_144637_create_personal_access_tokens_table', 1),
(9, '2026_04_13_200000_create_grades_table', 1),
(10, '2026_04_13_200001_create_subjects_table', 1),
(11, '2026_04_17_173800_add_missing_columns_to_enrollments_table', 1),
(12, '2026_04_17_173900_fix_enrollments_table_user_id', 1),
(13, '2026_04_17_174000_add_school_year_to_enrollments_table', 1),
(14, '2026_04_18_220000_fix_enrollments_status_column_length', 2),
(15, '2026_04_19_000000_create_student_documents_table', 3),
(16, '2026_04_19_001000_fix_payment_status_default', 4),
(17, '2026_04_19_160000_add_decline_storage_to_enrollments', 5),
(18, '2026_04_19_200000_create_section_subject_table', 6),
(19, '2026_04_20_000000_add_payment_due_fields_to_enrollments', 7),
(20, '2026_04_20_010000_create_section_student_table', 8),
(21, '2026_04_20_010100_fix_student_profiles_nullable_columns', 8),
(22, '2026_04_20_020000_create_summer_classes_table', 9),
(23, '2026_04_20_140808_change_subjects_grade_level_to_string', 10),
(24, '2026_04_20_141908_fix_nullable_teacher_id_in_sections_and_schedules', 11),
(25, '2026_04_21_000001_add_lrn_to_users_table', 12),
(26, '2026_04_21_000000_add_installment_fields_to_enrollments', 13),
(27, '2026_04_21_132314_add_blocked_to_users_table', 14),
(28, '2026_04_22_000000_add_payment_option_fields_to_enrollments', 15),
(29, '2026_04_23_000000_change_enrollments_grade_level_to_string', 16),
(30, '2026_04_23_000000_add_term_to_schedules_table', 17),
(31, '2026_04_23_000001_create_examination_periods_table', 17),
(32, '2026_04_25_000000_remove_capacity_from_sections_table', 18),
(33, '2026_04_26_072430_create_guidance_records_table', 19),
(34, '2026_04_26_080927_create_settings_table', 20),
(35, '2026_04_26_133700_add_missing_fields_to_profile_tables', 21),
(36, '2026_04_27_000001_create_attendances_table', 22),
(37, '2026_04_27_000002_create_announcements_table', 22),
(38, '2026_04_27_000003_create_parent_teacher_conferences_table', 22),
(39, '2026_04_28_050000_create_fee_settings_table', 23),
(40, '2026_04_28_100000_create_teacher_assignments_table', 24),
(41, '2026_05_01_000000_add_finance_role_to_users', 25),
(42, '2026_05_01_000001_add_optc_optd_fields_to_fee_settings', 26),
(43, '2026_05_01_170000_create_payment_installments_table', 27),
(44, '2026_05_05_000000_make_subject_id_nullable_in_teacher_assignments', 28),
(45, '2026_05_06_142543_add_soft_deletes_to_users_table', 29),
(46, '2026_05_08_000000_add_max_students_to_sections_table', 30),
(47, '2026_05_08_100000_cleanup_stale_section_student_rows', 31),
(48, '2026_05_08_200001_normalize_fk_constraints', 32),
(49, '2026_05_08_200002_complete_otp_codes_table', 32),
(50, '2026_05_08_200003_normalize_examination_period_grade_levels', 33),
(51, '2026_05_08_200004_normalize_guardian_data', 33),
(52, '2026_05_09_000001_make_student_document_file_fields_nullable', 34),
(53, '2026_05_09_200000_add_performance_indexes', 35),
(54, '2026_05_09_194347_create_payment_transactions_table', 36),
(56, '2026_05_11_034600_add_student_status_to_users_table', 38),
(57, '2026_05_11_094500_add_assessment_fields_to_enrollments_table', 39),
(58, '2026_05_12_000000_create_grade_import_drafts_table', 40),
(59, '2026_05_11_000000_create_promotions_table', 41),
(60, '2026_05_12_000001_simplify_grades_table', 41),
(61, '2026_05_13_174946_create_promissory_notes_table', 42),
(62, '2026_05_14_083143_add_photo_to_student_profiles_table', 42),
(63, '2026_05_14_120000_add_profile_photo_to_users_table', 43),
(64, '2026_05_15_000001_add_payment_transaction_id_to_payment_installments', 44),
(65, '2026_05_15_130703_create_activity_logs_table', 45),
(66, '2026_05_15_172430_create_contact_messages_table', 46),
(67, '2026_05_16_065507_add_unique_enrollment_per_user_year', 47),
(68, '2026_05_16_070601_create_otp_verifications_table', 48),
(69, '2026_05_16_141802_add_descriptive_grade_to_grades_table', 49),
(70, '2026_05_16_200000_cleanup_pending_section_student_rows', 50),
(71, '2026_05_18_180717_drop_unused_tables', 51),
(72, '2026_05_31_004753_add_xendit_fields_to_payment_transactions_table', 52),
(73, '2026_05_31_010129_add_cashier_role_to_users_table', 53),
(74, '2026_05_31_140812_create_news_table', 54),
(75, '2026_05_31_151738_add_image_to_announcements_table', 55),
(76, '2026_09_02_144538_add_reject_reason_to_payment_transactions_table', 56),
(77, '2026_04_18_000000_create_sections_table', 57),
(78, '2026_04_18_000001_create_schedules_table', 57),
(79, '2026_09_09_231348_drop_current_enrollment_from_sections_table', 58),
(80, '2026_09_09_232404_create_fee_components_table', 59),
(81, '2026_09_10_003502_backfill_teacher_assignments_from_schedules', 60),
(82, '2026_09_10_114802_backfill_teacher_assignments_for_advisory_only_grades', 61);

-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE `news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `posted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` enum('academic','events','activity','achievement','general') NOT NULL DEFAULT 'general',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `news`
--

INSERT INTO `news` (`id`, `posted_by`, `title`, `body`, `image`, `category`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 'Ej may jowa na', 'sino kaya', 'news/mle60yn9jQUXGNvmOhvTh5n70KCvOSFVJvWihoNI.png', 'general', 1, '2026-06-02 08:21:52', '2026-06-02 08:21:52');

-- --------------------------------------------------------

--
-- Table structure for table `otp_verifications`
--

CREATE TABLE `otp_verifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `attempts` tinyint(4) NOT NULL DEFAULT 0,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `otp_verifications`
--

INSERT INTO `otp_verifications` (`id`, `email`, `code`, `token`, `attempts`, `verified`, `expires_at`, `created_at`, `updated_at`) VALUES
(16, 'sfsf@gmail.com', '$2y$12$ZIJfSFyAhgKePFeP1B1.fedcycUM8MGQyHBya/cJ9g6nr6Af209tC', 'rIivMp68Oo4sz2ca4ZGdsOCEQBEt6foEqACfjBB5PpB5F9500ovSf7KbNrbBVtXj', 0, 0, '2026-05-17 10:04:25', '2026-05-17 09:54:25', '2026-05-17 09:54:25');

-- --------------------------------------------------------

--
-- Table structure for table `parent_teacher_conferences`
--

CREATE TABLE `parent_teacher_conferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `meeting_date` date NOT NULL,
  `meeting_time` time NOT NULL,
  `purpose` varchar(255) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('scheduled','completed','cancelled','rescheduled') NOT NULL DEFAULT 'scheduled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_installments`
--

CREATE TABLE `payment_installments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `month_name` varchar(255) NOT NULL,
  `due_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `amount_paid` decimal(10,2) NOT NULL DEFAULT 0.00,
  `late_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `paid_at` timestamp NULL DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `payment_transaction_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_id` bigint(20) UNSIGNED DEFAULT NULL,
  `weeks_overdue` int(11) NOT NULL DEFAULT 0,
  `warning_sent` tinyint(1) NOT NULL DEFAULT 0,
  `late_fee_applied` tinyint(1) NOT NULL DEFAULT 0,
  `block_notice_sent` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_installments`
--

INSERT INTO `payment_installments` (`id`, `enrollment_id`, `user_id`, `month_name`, `due_date`, `amount`, `amount_paid`, `late_fee`, `status`, `paid_at`, `payment_method`, `reference_number`, `payment_transaction_id`, `document_id`, `weeks_overdue`, `warning_sent`, `late_fee_applied`, `block_notice_sent`, `created_at`, `updated_at`) VALUES
(246, 70, 101, 'July', '2025-07-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(247, 70, 101, 'August', '2025-08-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(248, 70, 101, 'September', '2025-09-30', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(249, 70, 101, 'October', '2025-10-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(250, 70, 101, 'November', '2025-11-30', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(251, 70, 101, 'December', '2025-12-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(252, 70, 101, 'January', '2026-01-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(253, 70, 101, 'February', '2026-02-28', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54'),
(254, 70, 101, 'March', '2026-03-31', 1056.10, 0.00, 0.00, 'pending', NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, '2026-09-10 05:38:54', '2026-09-10 05:38:54');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `payment_type` varchar(255) NOT NULL,
  `payment_method` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `xendit_invoice_id` varchar(255) DEFAULT NULL,
  `xendit_invoice_url` varchar(1000) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'completed',
  `reject_reason` text DEFAULT NULL,
  `installment_month` varchar(255) DEFAULT NULL,
  `installment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_transactions`
--

INSERT INTO `payment_transactions` (`id`, `enrollment_id`, `user_id`, `payment_type`, `payment_method`, `amount`, `reference_number`, `xendit_invoice_id`, `xendit_invoice_url`, `description`, `status`, `reject_reason`, `installment_month`, `installment_id`, `processed_by`, `processed_at`, `created_at`, `updated_at`) VALUES
(79, 69, 93, 'online', 'gcash', 4505.00, 'STU-93-69-1780387486', '6a1e8e9dd14bf94c48e3177f', 'https://checkout-staging.xendit.co/web/6a1e8e9dd14bf94c48e3177f', 'Installment', 'pending', NULL, NULL, NULL, NULL, '2026-06-02 00:04:47', '2026-06-02 00:04:47', '2026-06-02 00:04:47'),
(80, 69, 93, 'online', 'gcash', 4505.00, 'STU-93-69-1780387562', '6a1e8ee9d14bf94c48e31800', 'https://checkout-staging.xendit.co/web/6a1e8ee9d14bf94c48e31800', 'Installment', 'pending', NULL, NULL, NULL, NULL, '2026-06-02 00:06:03', '2026-06-02 00:06:03', '2026-06-02 00:06:03'),
(81, 69, 93, 'online', 'gcash', 4505.00, 'STU-93-69-1780403731', '6a1ece14e1a14a7a1004d7cb', 'https://checkout-staging.xendit.co/web/6a1ece14e1a14a7a1004d7cb', 'Installment', 'pending', NULL, NULL, NULL, NULL, '2026-06-02 04:35:33', '2026-06-02 04:35:33', '2026-06-02 04:35:33'),
(82, 69, 93, 'walkin', 'cash', 14504.00, 'CASH-A18DC2DF', NULL, NULL, 'Full Payment', 'completed', NULL, NULL, NULL, 92, '2026-06-04 08:14:28', '2026-06-04 08:14:28', '2026-06-04 08:14:28'),
(84, 70, 101, 'online', 'gcash', 500.00, 'STU-101-70-1788363005', '6a9840fdd9fcab275e92e773', 'https://checkout-staging.xendit.co/web/6a9840fdd9fcab275e92e773', 'test', 'completed', NULL, NULL, NULL, NULL, '2026-09-02 07:30:06', '2026-09-02 07:30:06', '2026-09-02 07:30:06'),
(85, 70, 101, 'online', 'gcash', 500.00, 'STU-101-70-1788364305', '6a984611d9fcab275e92efc0', 'https://checkout-staging.xendit.co/web/6a984611d9fcab275e92efc0', 'test', 'completed', NULL, NULL, NULL, NULL, '2026-09-02 07:51:46', '2026-09-02 07:51:46', '2026-09-02 07:51:46'),
(86, 70, 101, 'online', 'gcash', 750.00, 'STU-101-70-1788364589', '6a98472dd9fcab275e92f172', 'https://checkout-staging.xendit.co/web/6a98472dd9fcab275e92f172', 'test', 'completed', NULL, NULL, NULL, NULL, '2026-09-02 07:57:50', '2026-09-02 07:56:29', '2026-09-02 07:57:50'),
(91, 71, 102, 'online', 'gcash', 16005.00, 'STU-102-71-1788543339', '6a9b016bd9fcab275e988173', 'https://checkout-staging.xendit.co/web/6a9b016bd9fcab275e988173', 'Full Payment', 'completed', NULL, NULL, NULL, NULL, '2026-09-04 09:47:25', '2026-09-04 09:35:40', '2026-09-04 09:47:25');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `previous_schools`
--

CREATE TABLE `previous_schools` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `school_address` varchar(255) DEFAULT NULL,
  `last_grade_completed` varchar(50) NOT NULL,
  `school_year_graduated` year(4) DEFAULT NULL,
  `general_average` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `previous_schools`
--

INSERT INTO `previous_schools` (`id`, `user_id`, `school_name`, `school_address`, `last_grade_completed`, `school_year_graduated`, `general_average`, `created_at`, `updated_at`) VALUES
(40, 93, 'Central School', NULL, 'nursery', NULL, NULL, '2026-06-01 23:34:26', '2026-06-01 23:34:26'),
(41, 102, 'N/a', NULL, 'grade3', NULL, NULL, '2026-09-04 09:21:26', '2026-09-04 09:21:26');

-- --------------------------------------------------------

--
-- Table structure for table `promissory_notes`
--

CREATE TABLE `promissory_notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_number` varchar(30) NOT NULL,
  `enrollment_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `amount_overdue` decimal(10,2) NOT NULL DEFAULT 0.00,
  `amount_promised` decimal(10,2) NOT NULL,
  `promise_date` date NOT NULL,
  `date_issued` date NOT NULL,
  `parent_guardian` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('pending','fulfilled','broken','extended') NOT NULL DEFAULT 'pending',
  `extended_date` date DEFAULT NULL,
  `fulfilled_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `lrn` varchar(50) DEFAULT NULL,
  `from_grade` varchar(30) NOT NULL,
  `to_grade` varchar(30) NOT NULL,
  `from_school_year` varchar(20) NOT NULL,
  `to_school_year` varchar(20) NOT NULL,
  `from_section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `to_section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promoted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `promoted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(30) NOT NULL DEFAULT 'completed',
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `schedules`
--

CREATE TABLE `schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `room` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `term` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `schedules`
--

INSERT INTO `schedules` (`id`, `section_id`, `subject_id`, `teacher_id`, `day_of_week`, `start_time`, `end_time`, `room`, `is_active`, `term`, `created_at`, `updated_at`) VALUES
(41, 5, 16, 24, 'Monday', '07:00:00', '08:00:00', '', 1, 1, '2026-05-16 08:13:34', '2026-09-02 08:38:34'),
(46, 5, 17, NULL, 'Monday', '08:00:00', '09:00:00', NULL, 1, 1, '2026-05-16 08:14:15', '2026-05-16 08:14:15'),
(47, 5, 12, NULL, 'Monday', '09:20:00', '10:20:00', NULL, 1, 1, '2026-05-16 08:14:22', '2026-05-16 08:14:22'),
(48, 5, 12, 24, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 08:14:27', '2026-05-16 08:14:34'),
(49, 5, 15, NULL, 'Tuesday', '08:00:00', '09:00:00', NULL, 1, 1, '2026-05-16 08:14:41', '2026-05-16 08:14:41'),
(50, 5, 13, 24, 'Tuesday', '09:20:00', '10:20:00', NULL, 1, 1, '2026-05-16 08:14:48', '2026-05-16 08:14:48'),
(51, 5, 14, 24, 'Wednesday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 08:14:57', '2026-05-16 08:14:57'),
(52, 5, 16, 24, 'Wednesday', '08:00:00', '09:00:00', NULL, 1, 1, '2026-05-16 08:15:04', '2026-05-16 08:15:04'),
(53, 5, 17, 24, 'Wednesday', '09:20:00', '10:20:00', NULL, 1, 1, '2026-05-16 08:15:16', '2026-05-16 08:15:16'),
(54, 5, 12, 24, 'Thursday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 08:15:25', '2026-05-16 08:15:25'),
(55, 5, 15, 24, 'Thursday', '08:00:00', '09:00:00', NULL, 1, 1, '2026-05-16 08:15:31', '2026-05-16 08:15:31'),
(56, 5, 13, 24, 'Thursday', '09:20:00', '10:20:00', NULL, 1, 1, '2026-05-16 08:15:37', '2026-05-16 08:15:37'),
(57, 5, 14, 24, 'Friday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 08:15:44', '2026-05-16 08:15:49'),
(58, 5, 16, 24, 'Friday', '08:00:00', '09:00:00', NULL, 1, 1, '2026-05-16 08:15:53', '2026-05-16 08:15:53'),
(59, 5, 17, 24, 'Friday', '09:20:00', '10:20:00', NULL, 1, 1, '2026-05-16 08:16:02', '2026-05-16 08:16:02'),
(60, 15, 27, 40, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:16:33', '2026-05-16 09:16:33'),
(61, 8, 2, 23, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:16:44', '2026-05-16 09:16:44'),
(62, 9, 29, 18, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:16:54', '2026-05-16 09:16:54'),
(63, 16, 39, 19, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:17:02', '2026-05-16 09:17:02'),
(64, 11, 47, 42, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:17:13', '2026-05-16 09:17:13'),
(65, 12, 8, 25, 'Monday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 09:17:21', '2026-05-16 09:17:21'),
(66, 15, 27, 43, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 17:50:14', '2026-05-16 17:50:14'),
(67, 15, 55, 40, 'Wednesday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 18:28:04', '2026-05-16 18:28:04'),
(68, 15, 18, 40, 'Thursday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 18:28:26', '2026-05-16 18:28:26'),
(69, 8, 2, 40, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-05-16 18:28:41', '2026-05-16 18:28:41'),
(70, 15, 27, 40, 'Friday', '07:00:00', '08:00:00', NULL, 1, 1, '2026-06-02 07:16:44', '2026-06-02 07:16:44'),
(123, 5, 16, 24, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(124, 15, 27, 40, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(125, 8, 2, 23, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(126, 9, 29, 18, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(127, 16, 39, 19, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(128, 11, 47, 42, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(129, 12, 8, 25, 'Monday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(130, 5, 17, NULL, 'Monday', '08:00:00', '09:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(131, 5, 12, NULL, 'Monday', '09:20:00', '10:20:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(132, 5, 12, 24, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(133, 15, 27, 43, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(134, 8, 2, 40, 'Tuesday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(135, 5, 15, NULL, 'Tuesday', '08:00:00', '09:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(136, 5, 13, 24, 'Tuesday', '09:20:00', '10:20:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(137, 5, 14, 24, 'Wednesday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(138, 15, 55, 40, 'Wednesday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(139, 5, 16, 24, 'Wednesday', '08:00:00', '09:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(140, 5, 17, 24, 'Wednesday', '09:20:00', '10:20:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(141, 5, 12, 24, 'Thursday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(142, 15, 18, 40, 'Thursday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(143, 5, 15, 24, 'Thursday', '08:00:00', '09:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(144, 5, 13, 24, 'Thursday', '09:20:00', '10:20:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(145, 5, 14, 24, 'Friday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(146, 15, 27, 40, 'Friday', '07:00:00', '08:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(147, 5, 16, 24, 'Friday', '08:00:00', '09:00:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(148, 5, 17, 24, 'Friday', '09:20:00', '10:20:00', NULL, 1, 2, '2026-09-02 08:33:58', '2026-09-02 08:33:58'),
(149, 9, 29, 18, 'Tuesday', '07:00:00', '08:00:00', 'Grade3', 1, 1, '2026-09-04 09:59:42', '2026-09-04 09:59:42'),
(150, 9, 29, 18, 'Wednesday', '07:00:00', '08:00:00', 'Grade3', 1, 1, '2026-09-04 09:59:51', '2026-09-04 09:59:51'),
(151, 9, 29, 18, 'Thursday', '07:00:00', '08:00:00', 'Grade3', 1, 1, '2026-09-04 09:59:57', '2026-09-04 09:59:57'),
(152, 9, 29, 18, 'Friday', '07:00:00', '08:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:03', '2026-09-04 10:00:03'),
(153, 9, 30, 43, 'Monday', '08:00:00', '09:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:12', '2026-09-04 10:00:12'),
(154, 9, 30, 43, 'Tuesday', '08:00:00', '09:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:19', '2026-09-04 10:00:19'),
(155, 9, 30, 43, 'Wednesday', '08:00:00', '09:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:26', '2026-09-04 10:00:26'),
(156, 9, 30, 43, 'Thursday', '08:00:00', '09:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:33', '2026-09-04 10:00:33'),
(157, 9, 30, 43, 'Friday', '08:00:00', '09:00:00', 'Grade3', 1, 1, '2026-09-04 10:00:39', '2026-09-04 10:00:39'),
(158, 9, 34, 24, 'Monday', '09:20:00', '10:20:00', 'Grade3', 1, 1, '2026-09-04 10:00:46', '2026-09-04 10:00:46'),
(159, 9, 33, 40, 'Tuesday', '09:20:00', '10:20:00', 'Grade3', 1, 1, '2026-09-04 10:01:12', '2026-09-04 10:01:12'),
(160, 9, 34, 40, 'Wednesday', '09:20:00', '10:20:00', 'Grade3', 1, 1, '2026-09-04 10:01:26', '2026-09-04 10:01:26'),
(161, 9, 33, 23, 'Thursday', '09:20:00', '10:20:00', 'Grade3', 1, 1, '2026-09-04 10:02:11', '2026-09-04 10:02:11'),
(162, 9, 34, 43, 'Friday', '09:20:00', '10:20:00', 'Grade3', 1, 1, '2026-09-04 10:02:51', '2026-09-04 10:02:51'),
(163, 9, 31, 18, 'Monday', '10:20:00', '11:20:00', 'Grade3', 1, 1, '2026-09-04 10:03:01', '2026-09-04 10:03:01'),
(164, 9, 31, 18, 'Tuesday', '10:20:00', '11:20:00', 'Grade3', 1, 1, '2026-09-04 10:03:07', '2026-09-04 10:03:07'),
(165, 9, 31, 18, 'Wednesday', '10:20:00', '11:20:00', 'Grade3', 1, 1, '2026-09-04 10:03:16', '2026-09-04 10:03:16'),
(166, 9, 31, 18, 'Thursday', '10:20:00', '11:20:00', 'Grade3', 1, 1, '2026-09-04 10:03:25', '2026-09-04 10:03:25'),
(167, 9, 31, 18, 'Friday', '10:20:00', '11:20:00', 'Grade3', 1, 1, '2026-09-04 10:03:32', '2026-09-04 10:03:32'),
(168, 9, 32, 18, 'Monday', '12:00:00', '13:00:00', 'Grade3', 1, 1, '2026-09-04 10:03:41', '2026-09-04 10:03:41'),
(169, 9, 32, 18, 'Tuesday', '12:00:00', '13:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:02', '2026-09-04 10:04:02'),
(170, 9, 32, 18, 'Wednesday', '12:00:00', '13:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:10', '2026-09-04 10:04:10'),
(171, 9, 32, 18, 'Thursday', '12:00:00', '13:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:17', '2026-09-04 10:04:17'),
(172, 9, 32, 18, 'Friday', '12:00:00', '13:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:25', '2026-09-04 10:04:25'),
(173, 9, 29, 18, 'Monday', '13:00:00', '14:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:32', '2026-09-04 10:04:32'),
(174, 9, 30, 43, 'Tuesday', '13:00:00', '14:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:38', '2026-09-04 10:04:38'),
(175, 9, 34, 40, 'Wednesday', '13:00:00', '14:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:46', '2026-09-04 10:04:46'),
(176, 9, 33, 23, 'Thursday', '13:00:00', '14:00:00', 'Grade3', 1, 1, '2026-09-04 10:04:53', '2026-09-04 10:04:53'),
(177, 9, 31, 18, 'Friday', '13:00:00', '14:00:00', 'Grade3', 1, 1, '2026-09-04 10:05:08', '2026-09-04 10:05:08');

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `grade_level` varchar(255) NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `room_number` varchar(255) DEFAULT NULL,
  `max_students` int(11) NOT NULL DEFAULT 30,
  `school_year` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `name`, `grade_level`, `teacher_id`, `room_number`, `max_students`, `school_year`, `is_active`, `created_at`, `updated_at`) VALUES
(5, 'Nusery', 'nursery', 24, 'Nursery Room', 30, '2026-2027', 1, '2026-04-24 09:10:07', '2026-06-04 08:14:28'),
(6, 'Kinder', 'kindergarten', 43, 'Kinder Room', 30, '2026-2027', 1, '2026-04-24 09:10:23', '2026-05-16 08:37:28'),
(8, 'Grade2', 'grade2', 23, 'Grade2', 30, '2026-2027', 1, '2026-04-24 09:10:53', '2026-05-16 17:02:42'),
(9, 'Grade3', 'grade3', 18, 'Grade3', 30, '2026-2027', 1, '2026-04-24 09:11:03', '2026-09-04 09:47:25'),
(11, 'Grade5', 'grade5', 42, 'Grade5', 30, '2026-2027', 1, '2026-04-24 09:11:30', '2026-05-16 08:42:14'),
(12, 'Grade6', 'grade6', 25, 'Grade6', 30, '2026-2027', 1, '2026-04-24 09:11:46', '2026-05-16 17:04:24'),
(15, 'Grade1', 'grade1', 40, 'Grade1', 30, '2026-2027', 1, '2026-04-24 22:06:14', '2026-05-16 18:05:57'),
(16, 'Grade4', 'grade4', 19, 'Grade4', 30, '2026-2027', 1, '2026-04-24 22:06:27', '2026-05-16 09:04:20'),
(19, 'Grade1B', 'grade1', NULL, 'Grade1B', 30, '2026-2027', 1, '2026-05-07 07:27:18', '2026-05-07 07:27:18'),
(20, 'Grade2B', 'grade2', NULL, 'Grade2B', 30, '2026-2027', 1, '2026-05-07 07:27:49', '2026-05-14 03:07:43'),
(21, 'grade 3B', 'grade3', NULL, NULL, 30, '2026-2027', 1, '2026-09-09 16:22:06', '2026-09-09 16:22:06');

-- --------------------------------------------------------

--
-- Table structure for table `section_student`
--

CREATE TABLE `section_student` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_student`
--

INSERT INTO `section_student` (`id`, `section_id`, `user_id`, `created_at`, `updated_at`) VALUES
(64, 5, 93, NULL, NULL),
(66, 9, 102, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `section_subject`
--

CREATE TABLE `section_subject` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `section_subject`
--

INSERT INTO `section_subject` (`id`, `section_id`, `subject_id`, `created_at`, `updated_at`) VALUES
(12, 5, 12, NULL, NULL),
(13, 5, 13, NULL, NULL),
(14, 5, 14, NULL, NULL),
(15, 5, 15, NULL, NULL),
(16, 5, 16, NULL, NULL),
(17, 5, 17, NULL, NULL),
(29, 8, 2, NULL, NULL),
(30, 8, 3, NULL, NULL),
(31, 8, 4, NULL, NULL),
(32, 8, 5, NULL, NULL),
(33, 8, 6, NULL, NULL),
(59, 12, 7, NULL, NULL),
(60, 12, 8, NULL, NULL),
(61, 12, 9, NULL, NULL),
(62, 12, 10, NULL, NULL),
(63, 12, 11, NULL, NULL),
(80, 15, 18, NULL, NULL),
(81, 15, 19, NULL, NULL),
(89, 16, 20, NULL, NULL),
(90, 6, 21, NULL, NULL),
(91, 6, 22, NULL, NULL),
(92, 6, 23, NULL, NULL),
(93, 6, 24, NULL, NULL),
(94, 6, 25, NULL, NULL),
(95, 6, 26, NULL, NULL),
(96, 9, 29, NULL, NULL),
(97, 9, 30, NULL, NULL),
(98, 9, 31, NULL, NULL),
(99, 9, 32, NULL, NULL),
(100, 9, 33, NULL, NULL),
(101, 9, 34, NULL, NULL),
(102, 11, 42, NULL, NULL),
(103, 11, 43, NULL, NULL),
(104, 11, 44, NULL, NULL),
(105, 11, 45, NULL, NULL),
(106, 11, 46, NULL, NULL),
(107, 11, 47, NULL, NULL),
(108, 11, 48, NULL, NULL),
(109, 11, 49, NULL, NULL),
(110, 12, 50, NULL, NULL),
(111, 12, 51, NULL, NULL),
(112, 12, 52, NULL, NULL),
(113, 15, 1, NULL, NULL),
(114, 15, 27, NULL, NULL),
(115, 15, 28, NULL, NULL),
(116, 16, 35, NULL, NULL),
(117, 16, 36, NULL, NULL),
(118, 16, 37, NULL, NULL),
(119, 16, 38, NULL, NULL),
(120, 16, 39, NULL, NULL),
(121, 16, 40, NULL, NULL),
(122, 16, 41, NULL, NULL),
(140, 19, 1, NULL, NULL),
(141, 19, 18, NULL, NULL),
(142, 19, 19, NULL, NULL),
(143, 19, 27, NULL, NULL),
(144, 19, 28, NULL, NULL),
(145, 20, 2, NULL, NULL),
(146, 20, 3, NULL, NULL),
(147, 20, 4, NULL, NULL),
(148, 20, 5, NULL, NULL),
(149, 20, 6, NULL, NULL),
(150, 21, 29, NULL, NULL),
(151, 21, 30, NULL, NULL),
(152, 21, 31, NULL, NULL),
(153, 21, 32, NULL, NULL),
(154, 21, 33, NULL, NULL),
(155, 21, 34, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `label` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `type`, `group`, `label`, `description`, `created_at`, `updated_at`) VALUES
(1, 'school_name', 'IEMELIF Learning Center', 'string', 'school', 'School Name', 'Official school name displayed across the system', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(2, 'school_address', 'General Tinio, Nueva Ecija', 'string', 'school', 'School Address', 'Official school address', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(3, 'school_phone', NULL, 'string', 'school', 'School Phone', 'Contact phone number', '2026-04-26 00:16:19', '2026-05-14 23:53:45'),
(4, 'school_email', NULL, 'string', 'school', 'School Email', 'Contact email address', '2026-04-26 00:16:19', '2026-05-14 23:53:45'),
(5, 'school_logo', '/images/logo.png', 'string', 'school', 'School Logo Path', 'Path to school logo image', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(6, 'principal_name', NULL, 'string', 'school', 'Principal Name', 'Name of school principal/director', '2026-04-26 00:16:19', '2026-05-14 23:53:45'),
(7, 'school_motto', NULL, 'string', 'school', 'School Motto', 'School motto or vision statement', '2026-04-26 00:16:19', '2026-05-14 23:53:45'),
(8, 'current_school_year', '2026-2027', 'string', 'academic', 'Current School Year', 'Active school year (e.g. 2026-2027)', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(9, 'school_year_start', '2026-06-01', 'string', 'academic', 'School Year Start', 'Start date of the school year', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(10, 'school_year_end', '2027-03-31', 'string', 'academic', 'School Year End', 'End date of the school year', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(11, 'passing_grade', '75', 'integer', 'academic', 'Passing Grade', 'Minimum grade to pass a subject', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(12, 'grade_scale_max', '100', 'integer', 'academic', 'Grade Scale Maximum', 'Maximum possible grade', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(13, 'ww_weight', '30', 'integer', 'academic', 'Written Works Weight (%)', 'Percentage weight for written works', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(14, 'pt_weight', '50', 'integer', 'academic', 'Performance Task Weight (%)', 'Percentage weight for performance tasks', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(15, 'qa_weight', '20', 'integer', 'academic', 'Assessment Weight (%)', 'Percentage weight for quarterly assessment', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(16, 'total_terms', '3', 'integer', 'academic', 'Total Quarters', 'Number of quarters in a school year', '2026-04-26 00:16:19', '2026-05-14 23:56:43'),
(17, 'enrollment_open', '1', 'boolean', 'academic', 'Enrollment Open', 'Whether online enrollment is currently accepting applications', '2026-04-26 00:16:19', '2026-05-19 07:12:07'),
(30, 'session_timeout', '120', 'integer', 'security', 'Session Timeout (minutes)', 'Minutes of inactivity before auto logout', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(31, 'max_login_attempts', '5', 'integer', 'security', 'Max Login Attempts', 'Maximum failed login attempts before lockout', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(32, 'lockout_duration', '15', 'integer', 'security', 'Lockout Duration (minutes)', 'Minutes to lock account after max failed attempts', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(33, 'password_min_length', '8', 'integer', 'security', 'Min Password Length', 'Minimum password character length', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(34, 'require_password_uppercase', '1', 'boolean', 'security', 'Require Uppercase in Password', 'Password must contain at least one uppercase letter', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(35, 'require_password_number', '1', 'boolean', 'security', 'Require Number in Password', 'Password must contain at least one number', '2026-04-26 00:16:19', '2026-04-26 00:16:19'),
(79, 'maintenance_mode', '0', 'boolean', 'general', 'Maintenance mode', NULL, '2026-05-12 07:06:04', '2026-09-07 16:14:03'),
(80, 'gcash_number', '09112223333', 'decimal', 'financial', 'Gcash number', NULL, '2026-05-14 23:53:45', '2026-05-14 23:53:45'),
(81, 'gcash_account_name', 'Iemelif Learning Center', 'decimal', 'financial', 'Gcash account name', NULL, '2026-05-14 23:53:45', '2026-05-14 23:53:45'),
(82, 'gcash_qr_path', '/images/GCash-QR-Code.png', 'decimal', 'financial', 'Gcash qr path', NULL, '2026-05-14 23:53:45', '2026-05-14 23:53:45'),
(83, 'visitor_count', '3', 'string', 'general', 'Visitor Count', NULL, '2026-05-15 08:29:51', '2026-09-09 16:17:15'),
(84, 'enrollment_target_year', '2027-2028', 'decimal', 'financial', 'Enrollment target year', NULL, '2026-05-16 02:31:34', '2026-05-16 02:31:34');

-- --------------------------------------------------------

--
-- Table structure for table `student_addresses`
--

CREATE TABLE `student_addresses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `barangay` varchar(150) NOT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `municipality` varchar(150) NOT NULL,
  `city` varchar(150) DEFAULT NULL,
  `province` varchar(150) NOT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_addresses`
--

INSERT INTO `student_addresses` (`id`, `user_id`, `barangay`, `street_address`, `municipality`, `city`, `province`, `zip_code`, `created_at`, `updated_at`) VALUES
(40, 93, 'Mabini', '45', 'LLANERA', 'LLANERA', 'NUEVA ECIJA', '5454', '2026-06-01 23:34:26', '2026-09-10 05:10:43'),
(41, 102, 'Sampaguita', 'Purok Sawmill', 'GENERAL TINIO (PAPAYA)', 'GENERAL TINIO (PAPAYA)', 'NUEVA ECIJA', '3104', '2026-09-04 09:21:26', '2026-09-10 05:10:43');

-- --------------------------------------------------------

--
-- Table structure for table `student_documents`
--

CREATE TABLE `student_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `enrollment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_type` varchar(50) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `reject_reason` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_documents`
--

INSERT INTO `student_documents` (`id`, `user_id`, `enrollment_id`, `document_type`, `file_path`, `original_name`, `mime_type`, `file_size`, `description`, `status`, `reject_reason`, `reviewed_by`, `reviewed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(91, 93, 69, 'payment_screenshot', 'test/fake.jpg', NULL, NULL, NULL, NULL, 'approved', NULL, 50, '2026-09-03 09:20:28', '2026-09-03 09:20:28', '2026-09-03 09:21:34', '2026-09-03 09:21:34'),
(92, 93, 69, 'payment_screenshot', 'test/fake2.jpg', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, '2026-09-03 09:21:11', '2026-09-03 09:21:17', '2026-09-03 09:21:17'),
(93, 102, 71, 'form_137', 'student_documents/71/qgLKyp2n5RSZC4inoKJilK2XwXJhFnUBL5KX82EI.png', 'form 137.png', 'image/png', 480762, NULL, 'approved', NULL, 1, '2026-09-04 09:33:05', '2026-09-04 09:32:25', '2026-09-04 09:33:05', NULL),
(94, 102, 71, 'report_card', 'student_documents/71/oEbnu3DAiFIE2NutNEVqbsKkVcjSCftGlQIhd9xe.jpg', 'Final-Grades-and-General-Average.jpg', 'image/jpeg', 140570, NULL, 'approved', NULL, 1, '2026-09-04 09:33:06', '2026-09-04 09:32:25', '2026-09-04 09:33:06', NULL),
(95, 102, 71, 'two_by_two_picture', 'student_documents/71/209o1gPzjxQKDoP3jDcMZuSANfTSW4jGddMcimkn.jpg', '8a1b9ac5-6f96-4596-8448-916ce9c0d30f.jpg', 'image/jpeg', 29884, NULL, 'approved', NULL, 1, '2026-09-04 09:33:07', '2026-09-04 09:32:25', '2026-09-04 09:33:07', NULL),
(96, 102, 71, 'birth_certificate', 'student_documents/71/DLH9d3TYxyYNQjIChrLFzDNGBs4x1p1jWq9qxwO8.png', 'form 137.png', 'image/png', 480762, NULL, 'approved', NULL, 1, '2026-09-04 09:34:39', '2026-09-04 09:34:27', '2026-09-04 09:34:39', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_profiles`
--

CREATE TABLE `student_profiles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `suffix` varchar(10) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `place_of_birth` varchar(150) DEFAULT NULL,
  `nationality` varchar(100) DEFAULT NULL,
  `religious_affiliation` varchar(100) DEFAULT NULL,
  `blood_type` varchar(10) DEFAULT NULL,
  `allergies` varchar(255) DEFAULT NULL,
  `medical_conditions` varchar(255) DEFAULT NULL,
  `student_email` varchar(255) DEFAULT NULL,
  `student_type` varchar(50) DEFAULT NULL,
  `last_school` varchar(255) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `student_profiles`
--

INSERT INTO `student_profiles` (`id`, `user_id`, `first_name`, `last_name`, `middle_name`, `suffix`, `birthdate`, `place_of_birth`, `nationality`, `religious_affiliation`, `blood_type`, `allergies`, `medical_conditions`, `student_email`, `student_type`, `last_school`, `gender`, `contact`, `photo`, `created_at`, `updated_at`) VALUES
(40, 93, 'Juan', 'Cruz', NULL, NULL, '2024-10-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '9123233424', NULL, '2026-06-01 23:34:26', '2026-09-10 05:24:32'),
(41, 102, 'Cloud Nathaniel', 'Go', 'B', NULL, '2004-12-17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'male', '9123457689', 'student_photos/YudYr5nlzqSqXXktBhkHsNskPmex8B1CoVy5V7KQ.jpg', '2026-09-04 09:21:26', '2026-09-04 09:27:16');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `grade_level` varchar(50) DEFAULT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `description`, `grade_level`, `teacher_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Math', 'MATH1', 'Basic Math', 'grade1', NULL, 1, '2026-04-20 06:14:58', '2026-04-25 07:44:46'),
(2, 'English', 'ENG', NULL, 'grade2', NULL, 1, '2026-04-23 00:53:07', '2026-04-23 00:53:07'),
(3, 'Filipino', 'FIL', NULL, 'grade2', NULL, 1, '2026-04-23 00:53:07', '2026-04-23 00:53:07'),
(4, 'Math', 'MATH', NULL, 'grade2', NULL, 1, '2026-04-23 00:53:07', '2026-04-23 00:53:07'),
(5, 'Makabansa', 'MAK', NULL, 'grade2', NULL, 1, '2026-04-23 00:53:07', '2026-04-23 00:53:07'),
(6, 'GMRC', 'GMRC', NULL, 'grade2', NULL, 1, '2026-04-23 00:53:07', '2026-04-23 00:53:07'),
(7, 'Science', 'SCI', NULL, 'grade6', NULL, 1, '2026-04-24 04:45:54', '2026-04-24 04:45:54'),
(8, 'AP', 'AP', NULL, 'grade6', NULL, 1, '2026-04-24 04:45:54', '2026-04-24 04:45:54'),
(9, 'ESP', 'ESP', NULL, 'grade6', NULL, 1, '2026-04-24 04:45:54', '2026-04-24 04:45:54'),
(10, 'TLE', 'TLE', NULL, 'grade6', NULL, 1, '2026-04-24 04:45:54', '2026-04-24 04:45:54'),
(11, 'Mapeh', 'MAPEH', NULL, 'grade6', NULL, 1, '2026-04-24 04:45:54', '2026-04-24 04:45:54'),
(12, 'Literacy, Language, and Communication', 'LLC', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(13, 'Socio-Emotional Development', 'SED', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(14, 'Values Development', 'VD', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(15, 'Physical Health and Motor Development', 'PHMD', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(16, 'Aesthetic/Creative Development', 'ACD', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(17, 'Cognitive Development', 'CD', NULL, 'nursery', NULL, 1, '2026-04-24 09:10:07', '2026-04-24 09:10:07'),
(18, 'Language', 'LANG', NULL, 'grade1', NULL, 1, '2026-04-24 09:10:41', '2026-04-24 09:10:41'),
(19, 'Reading and Literacy', 'RL', NULL, 'grade1', NULL, 1, '2026-04-24 09:10:41', '2026-04-24 09:10:41'),
(20, 'EPP', 'EPP', NULL, 'grade4', NULL, 1, '2026-04-24 09:11:17', '2026-04-24 09:11:17'),
(21, 'Literacy, Language, and Communication', 'LLC-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(22, 'Socio-Emotional Development', 'SED-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(23, 'Values Development', 'VD-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(24, 'Physical Health and Motor Development', 'PHMD-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(25, 'Aesthetic/Creative Development', 'ACD-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(26, 'Cognitive Development', 'CD-K', NULL, 'kindergarten', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(27, 'GMRC', 'GMRC1', NULL, 'grade1', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(28, 'Makabansa', 'MAK1', NULL, 'grade1', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(29, 'English', 'ENG3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(30, 'Filipino', 'FIL3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(31, 'Math', 'MATH3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(32, 'Science', 'SCI3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(33, 'Makabansa', 'MAK3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(34, 'GMRC', 'GMRC3', NULL, 'grade3', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(35, 'English', 'ENG4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(36, 'Filipino', 'FIL4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(37, 'Math', 'MATH4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(38, 'Science', 'SCI4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(39, 'AP', 'AP4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(40, 'Mapeh', 'MAPEH4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(41, 'GMRC', 'GMRC4', NULL, 'grade4', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(42, 'English', 'ENG5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(43, 'Filipino', 'FIL5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(44, 'Math', 'MATH5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(45, 'Science', 'SCI5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(46, 'EPP', 'EPP5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(47, 'AP', 'AP5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(48, 'Mapeh', 'MAPEH5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(49, 'GMRC', 'GMRC5', NULL, 'grade5', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(50, 'English', 'ENG6', NULL, 'grade6', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(51, 'Filipino', 'FIL6', NULL, 'grade6', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(52, 'Math', 'MATH6', NULL, 'grade6', NULL, 1, '2026-04-25 07:47:02', '2026-04-25 07:47:02'),
(53, 'Mapeh', 'PE', NULL, 'grade1', NULL, 1, '2026-04-25 08:09:18', '2026-04-25 08:09:18'),
(54, 'Wed Development', 'WEB DEV', NULL, 'grade6', NULL, 1, '2026-04-26 20:45:20', '2026-04-26 20:45:20'),
(55, 'Hcii', 'hcii', NULL, 'grade1', NULL, 1, '2026-04-26 23:38:25', '2026-04-26 23:38:25'),
(56, 'WebDevelopment', 'web', NULL, 'grade3', NULL, 1, '2026-04-27 00:53:22', '2026-04-27 00:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `summer_classes`
--

CREATE TABLE `summer_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_year` varchar(20) NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_level` varchar(30) NOT NULL,
  `room` varchar(255) DEFAULT NULL,
  `schedule_description` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('upcoming','ongoing','completed','cancelled') NOT NULL DEFAULT 'upcoming',
  `max_slots` int(11) NOT NULL DEFAULT 40,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `summer_class_enrollments`
--

CREATE TABLE `summer_class_enrollments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `summer_class_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `original_grade` decimal(5,2) DEFAULT NULL,
  `summer_grade` decimal(5,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `status` enum('enrolled','passed','failed','dropped') NOT NULL DEFAULT 'enrolled',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teacher_assignments`
--

CREATE TABLE `teacher_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `teacher_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `is_advisory` tinyint(1) NOT NULL DEFAULT 0,
  `school_year` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `teacher_assignments`
--

INSERT INTO `teacher_assignments` (`id`, `teacher_id`, `subject_id`, `section_id`, `is_advisory`, `school_year`, `created_at`, `updated_at`) VALUES
(19, 24, NULL, 5, 1, '2026-2027', '2026-05-16 07:22:42', '2026-05-16 07:22:42'),
(20, 43, NULL, 6, 1, '2026-2027', '2026-05-16 07:31:19', '2026-05-16 07:31:19'),
(21, 40, NULL, 15, 1, '2026-2027', '2026-05-16 08:41:32', '2026-05-16 08:41:32'),
(22, 23, NULL, 8, 1, '2026-2027', '2026-05-16 08:41:42', '2026-05-16 08:41:42'),
(23, 18, NULL, 9, 1, '2026-2027', '2026-05-16 08:41:52', '2026-05-16 08:41:52'),
(24, 19, NULL, 16, 1, '2026-2027', '2026-05-16 08:41:58', '2026-05-16 08:41:58'),
(25, 42, NULL, 11, 1, '2026-2027', '2026-05-16 08:42:14', '2026-05-16 08:42:14'),
(26, 25, NULL, 12, 1, '2026-2027', '2026-05-16 08:42:19', '2026-05-16 08:42:19'),
(27, 24, 16, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(28, 24, 12, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(29, 24, 13, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(30, 24, 14, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(31, 24, 17, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(32, 24, 15, 5, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(33, 23, 2, 8, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(34, 40, 2, 8, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(35, 18, 29, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(36, 43, 30, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(37, 24, 34, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(38, 40, 33, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(39, 40, 34, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(40, 23, 33, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(41, 43, 34, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(42, 18, 31, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(43, 18, 32, 9, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(44, 42, 47, 11, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(45, 25, 8, 12, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(46, 40, 27, 15, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(47, 43, 27, 15, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(48, 40, 55, 15, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(49, 40, 18, 15, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(50, 19, 39, 16, 0, '2026-2027', '2026-09-09 16:35:45', '2026-09-09 16:35:45'),
(51, 43, 21, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47'),
(52, 43, 22, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47'),
(53, 43, 23, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47'),
(54, 43, 24, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47'),
(55, 43, 25, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47'),
(56, 43, 26, 6, 0, '2026-2027', '2026-09-10 03:48:47', '2026-09-10 03:48:47');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `mfa_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `lrn` varchar(255) DEFAULT NULL,
  `role` enum('superadmin','admin','finance','cashier','teacher','student') DEFAULT 'student',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `student_status` varchar(30) NOT NULL DEFAULT 'active',
  `blocked` tinyint(1) NOT NULL DEFAULT 0,
  `profile_photo` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `avatar`, `mfa_enabled`, `name`, `email`, `lrn`, `role`, `is_active`, `student_status`, `blocked`, `profile_photo`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, NULL, 0, 'Admin', 'admin@ilc.com', NULL, 'admin', 1, 'active', 0, NULL, NULL, '$2y$12$EChNaQPmzqxsf4Cq4f6QTOhuKj6ywS3BvJIwfubFliXmQJYy7WWKq', NULL, NULL, '2026-09-02 09:29:53', NULL),
(2, NULL, NULL, 0, 'Super Admin', 'superadmin@ilc.com', NULL, 'superadmin', 1, 'active', 0, NULL, '2026-05-06 06:53:01', '$2y$12$e86zzW3rB1x3g9dyme7XbuQrZcGgFEM/WXINUjxyNcYYcn3aCRqE2', NULL, NULL, '2026-05-06 06:53:01', NULL),
(18, NULL, NULL, 0, 'john deGuzman', 'teacher1@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:51:23', '$2y$12$wapEIaC.RsAAVM0kqFcXO.b88G.RiGa5mS3RgaiyaCInHNVA6w/si', NULL, '2026-04-25 08:51:23', '2026-04-25 08:51:23', NULL),
(19, NULL, NULL, 0, 'jologs bago', 'teacher2@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:52:08', '$2y$12$6AiZdPW/sZZjSr/jEl3iZuO5Qx.XltoOwO7ZTEyLncpJBVXTreDM.', NULL, '2026-04-25 08:52:08', '2026-04-25 08:52:08', NULL),
(20, NULL, NULL, 0, 'justine bieber', 'teacher3@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:52:46', '$2y$12$pVfGc1oa0EViGehAyUb/JuOF5DnynG.Z7WO9ZAqULKJZ0TAAKRXQa', NULL, '2026-04-25 08:52:46', '2026-04-25 08:52:46', NULL),
(22, NULL, NULL, 0, 'Mikasa hackerman', 'teacher5@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:53:58', '$2y$12$ARPnEHesW32/XWWEV7EFcuUgWJHtE6GgRSAsc1oMPqHZ3dQvDAg3u', NULL, '2026-04-25 08:53:58', '2026-04-25 08:53:58', NULL),
(23, NULL, NULL, 0, 'Eren Bolisay', 'teacher6@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:55:48', '$2y$12$HEzaXHbO8HmGH6khekXw1eLZmeEIRg4VgSy4j7i2xamr8msqNrBH.', NULL, '2026-04-25 08:55:48', '2026-04-25 08:55:48', NULL),
(24, NULL, NULL, 0, 'batang Reyes', 'teacher7@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:56:06', '$2y$12$JqGr5eG0PpMa1e69jmo6heTEXvE6pyRUGj4iB70kZJYIHsYbifRyW', NULL, '2026-04-25 08:56:06', '2026-04-25 08:56:06', NULL),
(25, NULL, NULL, 0, 'Matalo Manalo', 'teacher8@ilc.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-25 08:56:47', '$2y$12$RPqGmVEI9elOI50ClbmN1.0eXjuVcDCuZGmqZS5PI90nIVzppuc7i', NULL, '2026-04-25 08:56:47', '2026-04-25 08:56:47', NULL),
(40, NULL, NULL, 0, 'Daniel Edikk', 'rontechh@gmail.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-26 09:03:43', '$2y$12$1DZXlNt3UlnRKJxQRFdOoekPVlnNZrV.fJ5C1xcyIbUJRR.ZMJPkq', NULL, '2026-04-26 09:03:43', '2026-04-26 09:03:43', NULL),
(42, NULL, NULL, 0, 'Jude Mallare', 'judeanthonymallare2@gmail.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-27 00:49:55', '$2y$12$Ose7UfmW.OTkaUCxrvpv4eHnNzkyxdUicy4cAx1g7YYtpBHwkONM.', NULL, '2026-04-27 00:49:55', '2026-04-27 00:49:55', NULL),
(43, NULL, NULL, 0, 'Aeron Mallare', 'clydelyn01@gmail.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-04-27 00:50:42', '$2y$12$y1pcgJ3r48TaWfbRfqB.A.iSXrtYNeojXNeKRn9QFHPKN9K3VgAqi', NULL, '2026-04-27 00:50:42', '2026-04-27 00:50:42', NULL),
(50, NULL, NULL, 0, 'Finance Administrator', 'finance@iemelif.edu.ph', NULL, 'finance', 1, 'active', 0, NULL, '2026-04-30 23:53:52', '$2y$12$TpaZQ3hYK35XwjEAdvTJmO94Cpl/AJ2tTHlk9DzuTt.Ldf4la408e', NULL, '2026-05-01 07:52:03', '2026-04-30 23:53:52', NULL),
(51, NULL, NULL, 0, 'Finance Administrator', 'finance@ilc.com', NULL, 'finance', 1, 'active', 0, NULL, '2026-05-01 00:51:37', '$2y$12$xoMHX88mjmhpVqK4KmadPeVHSoFDgFkQHR.xN4MyQZ83bavBkO7US', NULL, '2026-05-01 00:51:37', '2026-05-01 00:51:37', NULL),
(87, NULL, NULL, 0, 'Jason delluro', 'doteditt@gmail.com', NULL, 'teacher', 1, 'active', 0, NULL, '2026-05-16 09:08:17', '$2y$12$RmvG1HM1Sa3.kjg3knt.duLJlKkqJTbH0lWsq4js/PkvpnVVnuiNu', NULL, '2026-05-16 09:08:17', '2026-05-16 09:08:17', NULL),
(92, NULL, NULL, 0, 'Cashier Name', 'cashier@ilc.com', NULL, 'cashier', 1, 'active', 0, NULL, '2026-05-30 09:01:49', '$2y$12$hvqkG.AL4o9ioRFkyHnFKOVynmUOITjFJSU.wg5x3UjwA8HBWY212', NULL, '2026-05-30 09:01:49', '2026-05-30 09:04:40', NULL),
(93, NULL, NULL, 0, 'Juan Cruz', 'shopeepa010101@gmail.com', NULL, 'student', 1, 'active', 0, NULL, '2026-06-01 23:34:56', '$2y$12$Y4qINMj83vx3nLujAUbZl.XNuxKwkynF0uhA.5jdIlmgTBcq9rop.', NULL, '2026-06-01 23:34:26', '2026-06-01 23:34:56', NULL),
(94, NULL, NULL, 0, 'Test User', 'test@example.com', NULL, 'superadmin', 1, 'active', 0, NULL, '2026-06-02 06:42:56', '$2y$12$WiEBgnOdRJR3/uGxskVWsOX0Mv.A81j6Q7IJk5cmcWvo17Xl9kyj2', '5YcwNUrnP3', '2026-06-02 06:42:57', '2026-06-02 06:43:28', NULL),
(96, NULL, NULL, 0, 'Admin Registrar', 'admin@ilc.edu.ph', NULL, 'admin', 1, 'active', 0, NULL, '2026-06-02 06:43:28', '$2y$12$oSh0OSB2EDy3Ovb/aL41YuwosOPdgyoQte6jOe1i.MOkYBg7MS55C', NULL, '2026-06-02 06:43:28', '2026-06-02 06:43:28', NULL),
(97, NULL, NULL, 0, 'Sample Teacher', 'teacher@ilc.edu.ph', NULL, 'teacher', 1, 'active', 0, NULL, '2026-06-02 06:43:28', '$2y$12$Jt8TSH.qChd1Knegu0eVcORrXEK6VbYoZ9ASAxSClbcCWeBlY4DO2', NULL, '2026-06-02 06:43:28', '2026-06-02 06:43:28', NULL),
(99, NULL, NULL, 0, 'Finance Officer', 'finance@ilc.edu.ph', NULL, 'finance', 1, 'active', 0, NULL, '2026-06-02 06:43:29', '$2y$12$NagbScRw/cP34Sq2e1NKjOYNyjjj/MFetTLkn4hn24JkntpvFMJhy', NULL, '2026-06-02 06:43:29', '2026-06-02 06:43:29', NULL),
(100, NULL, NULL, 0, 'Cashier Staff', 'cashier@ilc.edu.ph', NULL, 'cashier', 1, 'active', 0, NULL, '2026-06-02 06:43:29', '$2y$12$n9.zr2G.RA9vxqt7DxJYKO4k0ao0D01r72cQK4e.q6i3Ecb32uREG', NULL, '2026-06-02 06:43:29', '2026-06-02 06:43:29', NULL),
(101, NULL, NULL, 0, 'Xendit Test Student', 'xendit-test@example.test', NULL, 'student', 1, 'active', 0, NULL, NULL, '$2y$12$dg8uRiknrqEXnOD7L04LEuklrQ2Di.to98xZAwJh8t46KSXkfi1Ay', NULL, '2026-09-02 07:10:36', '2026-09-02 07:10:36', NULL),
(102, NULL, NULL, 0, 'Cloud Nathaniel Go', 'cloud.bgo@gmail.com', NULL, 'student', 1, 'active', 0, NULL, '2026-09-04 09:22:16', '$2y$12$MrHTwRnteWahIFMhwPFP5uOTS9.vMEseRiRx6OCqQrXdPUtKxzoiW', NULL, '2026-09-04 09:21:26', '2026-09-04 09:22:16', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_index` (`user_id`),
  ADD KEY `activity_logs_event_type_index` (`event_type`),
  ADD KEY `activity_logs_created_at_index` (`created_at`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `announcements_teacher_id_foreign` (`teacher_id`),
  ADD KEY `announcements_section_id_foreign` (`section_id`),
  ADD KEY `idx_announcements_active` (`is_active`);

--
-- Indexes for table `attendances`
--
ALTER TABLE `attendances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `attendance_unique` (`student_id`,`section_id`,`subject_id`,`date`),
  ADD KEY `attendances_section_id_foreign` (`section_id`),
  ADD KEY `attendances_subject_id_foreign` (`subject_id`),
  ADD KEY `attendances_teacher_id_foreign` (`teacher_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enrollments_reference_number_unique` (`reference_number`),
  ADD UNIQUE KEY `unique_enrollment_per_user_year` (`user_id`,`school_year`),
  ADD KEY `enrollments_approved_by_foreign` (`approved_by`),
  ADD KEY `enrollments_declined_by_foreign` (`declined_by`),
  ADD KEY `idx_enrollments_status` (`status`),
  ADD KEY `idx_enrollments_payment_status` (`payment_status`),
  ADD KEY `idx_enrollments_school_year` (`school_year`),
  ADD KEY `idx_enrollments_grade_level` (`grade_level`),
  ADD KEY `idx_enrollments_school_year_status` (`school_year`,`status`),
  ADD KEY `idx_enrollments_payment_type_status` (`payment_status`,`payment_type`),
  ADD KEY `enrollments_assessed_by_foreign` (`assessed_by`),
  ADD KEY `enrollments_teacher_recommended_by_foreign` (`teacher_recommended_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_components`
--
ALTER TABLE `fee_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fee_components_fee_type_option_grade_level_index` (`fee_type`,`option`,`grade_level`);

--
-- Indexes for table `fee_settings`
--
ALTER TABLE `fee_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_student_id_subject_id_quarter_index` (`student_id`,`subject_id`),
  ADD KEY `grades_teacher_id_quarter_index` (`teacher_id`),
  ADD KEY `grades_subject_id_foreign` (`subject_id`),
  ADD KEY `grades_enrollment_id_foreign` (`enrollment_id`),
  ADD KEY `grades_student_subject_term_sy` (`student_id`,`subject_id`,`term`,`school_year`);

--
-- Indexes for table `guardians`
--
ALTER TABLE `guardians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guardians_user_id_foreign` (`user_id`);

--
-- Indexes for table `guidance_records`
--
ALTER TABLE `guidance_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guidance_records_student_id_foreign` (`student_id`),
  ADD KEY `guidance_records_counselor_id_foreign` (`counselor_id`),
  ADD KEY `idx_guidance_status` (`status`),
  ADD KEY `idx_guidance_concern_type` (`concern_type`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_posted_by_foreign` (`posted_by`);

--
-- Indexes for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `otp_verifications_token_unique` (`token`),
  ADD KEY `otp_verifications_email_index` (`email`);

--
-- Indexes for table `parent_teacher_conferences`
--
ALTER TABLE `parent_teacher_conferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_teacher_conferences_teacher_id_foreign` (`teacher_id`),
  ADD KEY `parent_teacher_conferences_student_id_foreign` (`student_id`);

--
-- Indexes for table `payment_installments`
--
ALTER TABLE `payment_installments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_installments_user_id_foreign` (`user_id`),
  ADD KEY `payment_installments_document_id_foreign` (`document_id`),
  ADD KEY `payment_installments_enrollment_id_status_index` (`enrollment_id`,`status`),
  ADD KEY `payment_installments_due_date_status_index` (`due_date`,`status`),
  ADD KEY `payment_installments_payment_transaction_id_index` (`payment_transaction_id`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_transactions_installment_id_foreign` (`installment_id`),
  ADD KEY `payment_transactions_processed_by_foreign` (`processed_by`),
  ADD KEY `payment_transactions_enrollment_id_status_index` (`enrollment_id`,`status`),
  ADD KEY `payment_transactions_user_id_payment_type_index` (`user_id`,`payment_type`),
  ADD KEY `payment_transactions_payment_type_status_index` (`payment_type`,`status`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `previous_schools`
--
ALTER TABLE `previous_schools`
  ADD PRIMARY KEY (`id`),
  ADD KEY `previous_schools_user_id_foreign` (`user_id`);

--
-- Indexes for table `promissory_notes`
--
ALTER TABLE `promissory_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promissory_notes_reference_number_unique` (`reference_number`),
  ADD KEY `promissory_notes_enrollment_id_foreign` (`enrollment_id`),
  ADD KEY `promissory_notes_student_id_foreign` (`student_id`),
  ADD KEY `promissory_notes_created_by_foreign` (`created_by`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promotions_from_section_id_foreign` (`from_section_id`),
  ADD KEY `promotions_to_section_id_foreign` (`to_section_id`),
  ADD KEY `promotions_promoted_by_foreign` (`promoted_by`),
  ADD KEY `promotions_student_id_from_school_year_index` (`student_id`,`from_school_year`),
  ADD KEY `promotions_to_school_year_status_index` (`to_school_year`,`status`),
  ADD KEY `promotions_lrn_index` (`lrn`);

--
-- Indexes for table `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `schedules_term_index` (`term`),
  ADD KEY `idx_schedules_is_active` (`is_active`),
  ADD KEY `idx_schedules_day` (`day_of_week`),
  ADD KEY `idx_schedules_section_active` (`section_id`,`is_active`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_sections_grade_level` (`grade_level`),
  ADD KEY `idx_sections_school_year` (`school_year`),
  ADD KEY `idx_sections_active` (`is_active`),
  ADD KEY `idx_sections_grade_year_active` (`grade_level`,`school_year`,`is_active`);

--
-- Indexes for table `section_student`
--
ALTER TABLE `section_student`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_student_section_id_user_id_unique` (`section_id`,`user_id`),
  ADD KEY `section_student_user_id_foreign` (`user_id`);

--
-- Indexes for table `section_subject`
--
ALTER TABLE `section_subject`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `section_subject_section_id_subject_id_unique` (`section_id`,`subject_id`),
  ADD KEY `section_subject_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `student_addresses`
--
ALTER TABLE `student_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_addresses_user_id_foreign` (`user_id`);

--
-- Indexes for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_documents_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `idx_student_documents_type` (`document_type`),
  ADD KEY `idx_student_documents_status` (`status`),
  ADD KEY `idx_student_documents_type_status` (`document_type`,`status`);

--
-- Indexes for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subjects_code_unique` (`code`),
  ADD KEY `subjects_teacher_id_foreign` (`teacher_id`),
  ADD KEY `subjects_grade_level_is_active_index` (`grade_level`,`is_active`),
  ADD KEY `idx_subjects_grade_level` (`grade_level`),
  ADD KEY `idx_subjects_active` (`is_active`);

--
-- Indexes for table `summer_classes`
--
ALTER TABLE `summer_classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `summer_classes_subject_id_foreign` (`subject_id`),
  ADD KEY `summer_classes_teacher_id_foreign` (`teacher_id`),
  ADD KEY `summer_classes_section_id_foreign` (`section_id`);

--
-- Indexes for table `summer_class_enrollments`
--
ALTER TABLE `summer_class_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `summer_class_enrollments_summer_class_id_student_id_unique` (`summer_class_id`,`student_id`),
  ADD KEY `summer_class_enrollments_student_id_foreign` (`student_id`);

--
-- Indexes for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_assignment_unique` (`teacher_id`,`subject_id`,`section_id`,`school_year`),
  ADD KEY `teacher_assignments_section_id_foreign` (`section_id`),
  ADD KEY `teacher_assignments_subject_id_foreign` (`subject_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD KEY `idx_users_role` (`role`),
  ADD KEY `idx_users_is_active` (`is_active`),
  ADD KEY `idx_users_role_active` (`role`,`is_active`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendances`
--
ALTER TABLE `attendances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fee_components`
--
ALTER TABLE `fee_components`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `fee_settings`
--
ALTER TABLE `fee_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `guardians`
--
ALTER TABLE `guardians`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `guidance_records`
--
ALTER TABLE `guidance_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `news`
--
ALTER TABLE `news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `otp_verifications`
--
ALTER TABLE `otp_verifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `parent_teacher_conferences`
--
ALTER TABLE `parent_teacher_conferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_installments`
--
ALTER TABLE `payment_installments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=255;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `previous_schools`
--
ALTER TABLE `previous_schools`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `promissory_notes`
--
ALTER TABLE `promissory_notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=178;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `section_student`
--
ALTER TABLE `section_student`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `section_subject`
--
ALTER TABLE `section_subject`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT for table `student_addresses`
--
ALTER TABLE `student_addresses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `student_documents`
--
ALTER TABLE `student_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `student_profiles`
--
ALTER TABLE `student_profiles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `summer_classes`
--
ALTER TABLE `summer_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `summer_class_enrollments`
--
ALTER TABLE `summer_class_enrollments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=104;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `announcements`
--
ALTER TABLE `announcements`
  ADD CONSTRAINT `announcements_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `announcements_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendances`
--
ALTER TABLE `attendances`
  ADD CONSTRAINT `attendances_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendances_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendances_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_assessed_by_foreign` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_declined_by_foreign` FOREIGN KEY (`declined_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_teacher_recommended_by_foreign` FOREIGN KEY (`teacher_recommended_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `enrollments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guardians`
--
ALTER TABLE `guardians`
  ADD CONSTRAINT `guardians_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `guidance_records`
--
ALTER TABLE `guidance_records`
  ADD CONSTRAINT `guidance_records_counselor_id_foreign` FOREIGN KEY (`counselor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `guidance_records_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `parent_teacher_conferences`
--
ALTER TABLE `parent_teacher_conferences`
  ADD CONSTRAINT `parent_teacher_conferences_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_teacher_conferences_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_installments`
--
ALTER TABLE `payment_installments`
  ADD CONSTRAINT `payment_installments_document_id_foreign` FOREIGN KEY (`document_id`) REFERENCES `student_documents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_installments_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_installments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payment_transactions_installment_id_foreign` FOREIGN KEY (`installment_id`) REFERENCES `payment_installments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `payment_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `previous_schools`
--
ALTER TABLE `previous_schools`
  ADD CONSTRAINT `previous_schools_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promissory_notes`
--
ALTER TABLE `promissory_notes`
  ADD CONSTRAINT `promissory_notes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promissory_notes_enrollment_id_foreign` FOREIGN KEY (`enrollment_id`) REFERENCES `enrollments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promissory_notes_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `promotions`
--
ALTER TABLE `promotions`
  ADD CONSTRAINT `promotions_from_section_id_foreign` FOREIGN KEY (`from_section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `promotions_promoted_by_foreign` FOREIGN KEY (`promoted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `promotions_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `promotions_to_section_id_foreign` FOREIGN KEY (`to_section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `section_student`
--
ALTER TABLE `section_student`
  ADD CONSTRAINT `section_student_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_student_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `section_subject`
--
ALTER TABLE `section_subject`
  ADD CONSTRAINT `section_subject_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_subject_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_addresses`
--
ALTER TABLE `student_addresses`
  ADD CONSTRAINT `student_addresses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documents`
--
ALTER TABLE `student_documents`
  ADD CONSTRAINT `student_documents_reviewed_by_foreign` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `student_profiles`
--
ALTER TABLE `student_profiles`
  ADD CONSTRAINT `student_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `summer_classes`
--
ALTER TABLE `summer_classes`
  ADD CONSTRAINT `summer_classes_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `summer_classes_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `summer_classes_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `summer_class_enrollments`
--
ALTER TABLE `summer_class_enrollments`
  ADD CONSTRAINT `summer_class_enrollments_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `summer_class_enrollments_summer_class_id_foreign` FOREIGN KEY (`summer_class_id`) REFERENCES `summer_classes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teacher_assignments`
--
ALTER TABLE `teacher_assignments`
  ADD CONSTRAINT `teacher_assignments_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_assignments_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `teacher_assignments_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
