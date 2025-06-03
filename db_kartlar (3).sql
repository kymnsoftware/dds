-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 02 Haz 2025, 20:56:03
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `db_kartlar`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `absences`
--

CREATE TABLE `absences` (
  `id` int(11) NOT NULL,
  `user_id` varchar(20) NOT NULL,
  `absence_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` int(11) NOT NULL,
  `reason` text DEFAULT NULL,
  `is_justified` tinyint(1) DEFAULT 0,
  `admin_note` text DEFAULT NULL,
  `created_by` varchar(20) DEFAULT NULL,
  `auto_generated` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `absences`
--

INSERT INTO `absences` (`id`, `user_id`, `absence_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `is_justified`, `admin_note`, `created_by`, `auto_generated`, `created_at`, `updated_at`) VALUES
(1, '8', 3, '2025-05-29', '2025-05-30', 2, 'DEDESİ VEFAT ETTİ', 1, NULL, '1', 0, '2025-05-29 07:43:06', '2025-05-29 07:43:06'),
(2, '10', 5, '2025-05-29', '2025-05-29', 1, '', 1, NULL, '1', 0, '2025-05-29 07:44:13', '2025-05-29 07:44:13'),
(3, '1', 5, '2025-05-29', '2025-05-29', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:06:24', '2025-05-29 08:06:24'),
(4, '1', 1, '2025-05-29', '2025-05-29', 1, '', 1, NULL, '1', 0, '2025-05-29 08:11:45', '2025-05-29 08:11:45'),
(5, '6', 5, '2025-05-28', '2025-05-28', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:39:35', '2025-05-29 08:39:35'),
(6, '10', 5, '2025-05-28', '2025-05-28', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:39:35', '2025-05-29 08:39:35'),
(8, '9', 5, '2025-05-28', '2025-05-28', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:39:35', '2025-05-29 08:39:35'),
(9, '11', 5, '2025-05-28', '2025-05-28', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:39:35', '2025-05-29 08:39:35'),
(10, '7', 5, '2025-05-28', '2025-05-28', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 08:39:35', '2025-05-29 08:39:35'),
(11, '8', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(12, '6', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(13, '10', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(14, '1', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(15, '9', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(16, '2', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(17, '11', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(18, '7', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(19, '3', 5, '2025-05-21', '2025-05-21', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-05-29 10:35:06', '2025-05-29 10:35:06'),
(20, '6', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(21, '10', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(22, '1', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(23, '9', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(24, '2', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(25, '11', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25'),
(26, '7', 5, '2025-06-02', '2025-06-02', 1, 'Otomatik tespit: Giriş kaydı bulunamadı', 0, NULL, '1', 1, '2025-06-02 14:01:25', '2025-06-02 14:01:25');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `absence_types`
--

CREATE TABLE `absence_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#ff0000',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Tablo döküm verisi `absence_types`
--

INSERT INTO `absence_types` (`id`, `name`, `description`, `color`, `is_active`, `created_at`) VALUES
(1, 'Hastalık', 'Sağlık sorunları nedeniyle devamsızlık', '#e74c3c', 1, '2025-05-29 07:38:06'),
(2, 'Kişisel İşler', 'Kişisel işler nedeniyle devamsızlık', '#f39c12', 1, '2025-05-29 07:38:06'),
(3, 'Ailevi Nedenler', 'Ailevi sorunlar nedeniyle devamsızlık', '#9b59b6', 1, '2025-05-29 07:38:06'),
(4, 'Ulaşım Sorunu', 'Ulaşım problemleri nedeniyle devamsızlık', '#3498db', 1, '2025-05-29 07:38:06'),
(5, 'Mazeretsiz', 'Herhangi bir mazeret bildirmeden devamsızlık', '#95a5a6', 1, '2025-05-29 07:38:06');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` int(11) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `event_time` datetime NOT NULL,
  `event_type` enum('ENTRY','EXIT') NOT NULL,
  `device_id` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `card_number`, `user_id`, `name`, `event_time`, `event_type`, `device_id`, `created_at`) VALUES
(205, '7500551', 6, 'CKT', '2025-05-26 04:43:41', 'ENTRY', '1', '2025-05-26 01:43:47'),
(206, '7500551', 6, 'CKT', '2025-05-26 04:43:50', 'EXIT', '1', '2025-05-26 01:43:57'),
(207, '7500551', 6, 'CKT', '2025-05-26 04:44:53', 'ENTRY', '1', '2025-05-26 01:44:59'),
(208, '7500551', 6, 'CKT', '2025-03-01 09:00:00', 'ENTRY', '1', '2025-03-01 06:00:05'),
(209, '7500551', 6, 'CKT', '2025-03-01 18:00:00', 'EXIT', '1', '2025-03-01 15:00:05'),
(210, '7500551', 6, 'CKT', '2025-03-02 09:00:00', 'ENTRY', '1', '2025-03-02 06:00:05'),
(211, '7500551', 6, 'CKT', '2025-03-02 18:00:00', 'EXIT', '1', '2025-03-02 15:00:05'),
(212, '7500551', 6, 'CKT', '2025-03-03 09:00:00', 'ENTRY', '1', '2025-03-03 06:00:05'),
(213, '7500551', 6, 'CKT', '2025-03-03 18:00:00', 'EXIT', '1', '2025-03-03 15:00:05'),
(214, '7500551', 6, 'CKT', '2025-03-04 09:00:00', 'ENTRY', '1', '2025-03-04 06:00:05'),
(215, '7500551', 6, 'CKT', '2025-03-04 18:00:00', 'EXIT', '1', '2025-03-04 15:00:05'),
(216, '7500551', 6, 'CKT', '2025-03-05 09:00:00', 'ENTRY', '1', '2025-03-05 06:00:05'),
(217, '7500551', 6, 'CKT', '2025-03-05 18:00:00', 'EXIT', '1', '2025-03-05 15:00:05'),
(218, '7500551', 6, 'CKT', '2025-03-06 09:00:00', 'ENTRY', '1', '2025-03-06 06:00:05'),
(219, '7500551', 6, 'CKT', '2025-03-06 18:00:00', 'EXIT', '1', '2025-03-06 15:00:05'),
(220, '7500551', 6, 'CKT', '2025-03-07 09:00:00', 'ENTRY', '1', '2025-03-07 06:00:05'),
(221, '7500551', 6, 'CKT', '2025-03-07 18:00:00', 'EXIT', '1', '2025-03-07 15:00:05'),
(222, '7500551', 6, 'CKT', '2025-03-08 09:00:00', 'ENTRY', '1', '2025-03-08 06:00:05'),
(223, '7500551', 6, 'CKT', '2025-03-08 18:00:00', 'EXIT', '1', '2025-03-08 15:00:05'),
(224, '7500551', 6, 'CKT', '2025-03-09 09:00:00', 'ENTRY', '1', '2025-03-09 06:00:05'),
(225, '7500551', 6, 'CKT', '2025-03-09 18:00:00', 'EXIT', '1', '2025-03-09 15:00:05'),
(226, '7500551', 6, 'CKT', '2025-03-10 09:00:00', 'ENTRY', '1', '2025-03-10 06:00:05'),
(227, '7500551', 6, 'CKT', '2025-03-10 18:00:00', 'EXIT', '1', '2025-03-10 15:00:05'),
(228, '7500551', 6, 'CKT', '2025-03-11 09:00:00', 'ENTRY', '1', '2025-03-11 06:00:05'),
(229, '7500551', 6, 'CKT', '2025-03-11 18:00:00', 'EXIT', '1', '2025-03-11 15:00:05'),
(230, '7500551', 6, 'CKT', '2025-03-12 09:00:00', 'ENTRY', '1', '2025-03-12 06:00:05'),
(231, '7500551', 6, 'CKT', '2025-03-12 18:00:00', 'EXIT', '1', '2025-03-12 15:00:05'),
(232, '7500551', 6, 'CKT', '2025-03-13 09:00:00', 'ENTRY', '1', '2025-03-13 06:00:05'),
(233, '7500551', 6, 'CKT', '2025-03-13 18:00:00', 'EXIT', '1', '2025-03-13 15:00:05'),
(234, '7500551', 6, 'CKT', '2025-03-14 09:00:00', 'ENTRY', '1', '2025-03-14 06:00:05'),
(235, '7500551', 6, 'CKT', '2025-03-14 18:00:00', 'EXIT', '1', '2025-03-14 15:00:05'),
(236, '7500551', 6, 'CKT', '2025-03-15 09:00:00', 'ENTRY', '1', '2025-03-15 06:00:05'),
(237, '7500551', 6, 'CKT', '2025-03-15 18:00:00', 'EXIT', '1', '2025-03-15 15:00:05'),
(238, '7500551', 6, 'CKT', '2025-03-16 09:00:00', 'ENTRY', '1', '2025-03-16 06:00:05'),
(239, '7500551', 6, 'CKT', '2025-03-16 18:00:00', 'EXIT', '1', '2025-03-16 15:00:05'),
(240, '7500551', 6, 'CKT', '2025-03-17 09:00:00', 'ENTRY', '1', '2025-03-17 06:00:05'),
(241, '7500551', 6, 'CKT', '2025-03-17 18:00:00', 'EXIT', '1', '2025-03-17 15:00:05'),
(242, '7500551', 6, 'CKT', '2025-03-18 09:00:00', 'ENTRY', '1', '2025-03-18 06:00:05'),
(243, '7500551', 6, 'CKT', '2025-03-18 18:00:00', 'EXIT', '1', '2025-03-18 15:00:05'),
(244, '7500551', 6, 'CKT', '2025-03-19 09:00:00', 'ENTRY', '1', '2025-03-19 06:00:05'),
(245, '7500551', 6, 'CKT', '2025-03-19 18:00:00', 'EXIT', '1', '2025-03-19 15:00:05'),
(246, '7500551', 6, 'CKT', '2025-03-20 09:00:00', 'ENTRY', '1', '2025-03-20 06:00:05'),
(247, '7500551', 6, 'CKT', '2025-03-20 18:00:00', 'EXIT', '1', '2025-03-20 15:00:05'),
(248, '7500551', 6, 'CKT', '2025-03-21 09:00:00', 'ENTRY', '1', '2025-03-21 06:00:05'),
(249, '7500551', 6, 'CKT', '2025-03-21 18:00:00', 'EXIT', '1', '2025-03-21 15:00:05'),
(250, '7500551', 6, 'CKT', '2025-03-22 09:00:00', 'ENTRY', '1', '2025-03-22 06:00:05'),
(251, '7500551', 6, 'CKT', '2025-03-22 18:00:00', 'EXIT', '1', '2025-03-22 15:00:05'),
(252, '7500551', 6, 'CKT', '2025-03-23 09:00:00', 'ENTRY', '1', '2025-03-23 06:00:05'),
(253, '7500551', 6, 'CKT', '2025-03-23 18:00:00', 'EXIT', '1', '2025-03-23 15:00:05'),
(254, '7500551', 6, 'CKT', '2025-03-24 09:00:00', 'ENTRY', '1', '2025-03-24 06:00:05'),
(255, '7500551', 6, 'CKT', '2025-03-24 18:00:00', 'EXIT', '1', '2025-03-24 15:00:05'),
(256, '7500551', 6, 'CKT', '2025-03-25 09:00:00', 'ENTRY', '1', '2025-03-25 06:00:05'),
(257, '7500551', 6, 'CKT', '2025-03-25 18:00:00', 'EXIT', '1', '2025-03-25 15:00:05'),
(258, '7500551', 6, 'CKT', '2025-03-26 09:00:00', 'ENTRY', '1', '2025-03-26 06:00:05'),
(259, '7500551', 6, 'CKT', '2025-03-26 18:00:00', 'EXIT', '1', '2025-03-26 15:00:05'),
(260, '7500551', 6, 'CKT', '2025-03-27 09:00:00', 'ENTRY', '1', '2025-03-27 06:00:05'),
(261, '7500551', 6, 'CKT', '2025-03-27 18:00:00', 'EXIT', '1', '2025-03-27 15:00:05'),
(262, '7500551', 6, 'CKT', '2025-03-28 09:00:00', 'ENTRY', '1', '2025-03-28 06:00:05'),
(263, '7500551', 6, 'CKT', '2025-03-28 18:00:00', 'EXIT', '1', '2025-03-28 15:00:05'),
(264, '7500551', 6, 'CKT', '2025-03-29 09:00:00', 'ENTRY', '1', '2025-03-29 06:00:05'),
(265, '7500551', 6, 'CKT', '2025-03-29 18:00:00', 'EXIT', '1', '2025-03-29 15:00:05'),
(266, '7500551', 6, 'CKT', '2025-03-30 09:00:00', 'ENTRY', '1', '2025-03-30 06:00:05'),
(267, '7500551', 6, 'CKT', '2025-03-30 18:00:00', 'EXIT', '1', '2025-03-30 15:00:05'),
(268, '7500551', 6, 'CKT', '2025-03-31 09:00:00', 'ENTRY', '1', '2025-03-31 06:00:05'),
(269, '7500551', 6, 'CKT', '2025-03-31 18:00:00', 'EXIT', '1', '2025-03-31 15:00:05'),
(270, '7500551', 6, 'CKT', '2025-02-01 09:00:00', 'ENTRY', '1', '2025-02-01 06:00:05'),
(271, '7500551', 6, 'CKT', '2025-02-01 18:00:00', 'EXIT', '1', '2025-02-01 15:00:05'),
(272, '7500551', 6, 'CKT', '2025-02-02 09:00:00', 'ENTRY', '1', '2025-02-02 06:00:05'),
(273, '7500551', 6, 'CKT', '2025-02-02 18:00:00', 'EXIT', '1', '2025-02-02 15:00:05'),
(274, '7500551', 6, 'CKT', '2025-02-03 09:00:00', 'ENTRY', '1', '2025-02-03 06:00:05'),
(275, '7500551', 6, 'CKT', '2025-02-03 18:00:00', 'EXIT', '1', '2025-02-03 15:00:05'),
(276, '7500551', 6, 'CKT', '2025-02-04 09:00:00', 'ENTRY', '1', '2025-02-04 06:00:05'),
(277, '7500551', 6, 'CKT', '2025-02-04 18:00:00', 'EXIT', '1', '2025-02-04 15:00:05'),
(278, '7500551', 6, 'CKT', '2025-02-05 09:00:00', 'ENTRY', '1', '2025-02-05 06:00:05'),
(279, '7500551', 6, 'CKT', '2025-02-05 18:00:00', 'EXIT', '1', '2025-02-05 15:00:05'),
(280, '7500551', 6, 'CKT', '2025-02-06 09:00:00', 'ENTRY', '1', '2025-02-06 06:00:05'),
(281, '7500551', 6, 'CKT', '2025-02-06 18:00:00', 'EXIT', '1', '2025-02-06 15:00:05'),
(282, '7500551', 6, 'CKT', '2025-02-07 09:00:00', 'ENTRY', '1', '2025-02-07 06:00:05'),
(283, '7500551', 6, 'CKT', '2025-02-07 18:00:00', 'EXIT', '1', '2025-02-07 15:00:05'),
(284, '7500551', 6, 'CKT', '2025-02-08 09:00:00', 'ENTRY', '1', '2025-02-08 06:00:05'),
(285, '7500551', 6, 'CKT', '2025-02-08 18:00:00', 'EXIT', '1', '2025-02-08 15:00:05'),
(286, '7500551', 6, 'CKT', '2025-02-09 09:00:00', 'ENTRY', '1', '2025-02-09 06:00:05'),
(287, '7500551', 6, 'CKT', '2025-02-09 18:00:00', 'EXIT', '1', '2025-02-09 15:00:05'),
(288, '7500551', 6, 'CKT', '2025-02-10 09:00:00', 'ENTRY', '1', '2025-02-10 06:00:05'),
(289, '7500551', 6, 'CKT', '2025-02-10 18:00:00', 'EXIT', '1', '2025-02-10 15:00:05'),
(290, '7500551', 6, 'CKT', '2025-02-11 09:00:00', 'ENTRY', '1', '2025-02-11 06:00:05'),
(291, '7500551', 6, 'CKT', '2025-02-11 18:00:00', 'EXIT', '1', '2025-02-11 15:00:05'),
(292, '7500551', 6, 'CKT', '2025-02-12 09:00:00', 'ENTRY', '1', '2025-02-12 06:00:05'),
(293, '7500551', 6, 'CKT', '2025-02-12 18:00:00', 'EXIT', '1', '2025-02-12 15:00:05'),
(294, '7500551', 6, 'CKT', '2025-02-13 09:00:00', 'ENTRY', '1', '2025-02-13 06:00:05'),
(295, '7500551', 6, 'CKT', '2025-02-13 18:00:00', 'EXIT', '1', '2025-02-13 15:00:05'),
(296, '7500551', 6, 'CKT', '2025-02-14 09:00:00', 'ENTRY', '1', '2025-02-14 06:00:05'),
(297, '7500551', 6, 'CKT', '2025-02-14 18:00:00', 'EXIT', '1', '2025-02-14 15:00:05'),
(298, '7500551', 6, 'CKT', '2025-02-15 09:00:00', 'ENTRY', '1', '2025-02-15 06:00:05'),
(299, '7500551', 6, 'CKT', '2025-02-15 18:00:00', 'EXIT', '1', '2025-02-15 15:00:05'),
(300, '7500551', 6, 'CKT', '2025-02-16 09:00:00', 'ENTRY', '1', '2025-02-16 06:00:05'),
(301, '7500551', 6, 'CKT', '2025-02-16 18:00:00', 'EXIT', '1', '2025-02-16 15:00:05'),
(302, '7500551', 6, 'CKT', '2025-02-17 09:00:00', 'ENTRY', '1', '2025-02-17 06:00:05'),
(303, '7500551', 6, 'CKT', '2025-02-17 18:00:00', 'EXIT', '1', '2025-02-17 15:00:05'),
(304, '7500551', 6, 'CKT', '2025-02-18 09:00:00', 'ENTRY', '1', '2025-02-18 06:00:05'),
(305, '7500551', 6, 'CKT', '2025-02-18 18:00:00', 'EXIT', '1', '2025-02-18 15:00:05'),
(306, '7500551', 6, 'CKT', '2025-02-19 09:00:00', 'ENTRY', '1', '2025-02-19 06:00:05'),
(307, '7500551', 6, 'CKT', '2025-02-19 18:00:00', 'EXIT', '1', '2025-02-19 15:00:05'),
(308, '7500551', 6, 'CKT', '2025-02-20 09:00:00', 'ENTRY', '1', '2025-02-20 06:00:05'),
(309, '7500551', 6, 'CKT', '2025-02-20 18:00:00', 'EXIT', '1', '2025-02-20 15:00:05'),
(310, '7500551', 6, 'CKT', '2025-02-21 09:00:00', 'ENTRY', '1', '2025-02-21 06:00:05'),
(311, '7500551', 6, 'CKT', '2025-02-21 18:00:00', 'EXIT', '1', '2025-02-21 15:00:05'),
(312, '7500551', 6, 'CKT', '2025-02-22 09:00:00', 'ENTRY', '1', '2025-02-22 06:00:05'),
(313, '7500551', 6, 'CKT', '2025-02-22 18:00:00', 'EXIT', '1', '2025-02-22 15:00:05'),
(314, '7500551', 6, 'CKT', '2025-02-23 09:00:00', 'ENTRY', '1', '2025-02-23 06:00:05'),
(315, '7500551', 6, 'CKT', '2025-02-23 18:00:00', 'EXIT', '1', '2025-02-23 15:00:05'),
(316, '7500551', 6, 'CKT', '2025-02-24 09:00:00', 'ENTRY', '1', '2025-02-24 06:00:05'),
(317, '7500551', 6, 'CKT', '2025-02-24 18:00:00', 'EXIT', '1', '2025-02-24 15:00:05'),
(318, '7500551', 6, 'CKT', '2025-02-25 09:00:00', 'ENTRY', '1', '2025-02-25 06:00:05'),
(319, '7500551', 6, 'CKT', '2025-02-25 18:00:00', 'EXIT', '1', '2025-02-25 15:00:05'),
(320, '7500551', 6, 'CKT', '2025-02-26 09:00:00', 'ENTRY', '1', '2025-02-26 06:00:05'),
(321, '7500551', 6, 'CKT', '2025-02-26 18:00:00', 'EXIT', '1', '2025-02-26 15:00:05'),
(322, '7500551', 6, 'CKT', '2025-02-27 09:00:00', 'ENTRY', '1', '2025-02-27 06:00:05'),
(323, '7500551', 6, 'CKT', '2025-02-27 18:00:00', 'EXIT', '1', '2025-02-27 15:00:05'),
(324, '7500551', 6, 'CKT', '2025-02-28 09:00:00', 'ENTRY', '1', '2025-02-28 06:00:05'),
(325, '7500551', 6, 'CKT', '2025-02-28 18:00:00', 'EXIT', '1', '2025-02-28 15:00:05'),
(326, '7500551', 6, 'CKT', '2025-05-26 06:04:33', 'EXIT', '1', '2025-05-26 03:04:40'),
(327, '7500551', 6, 'CKT', '2025-05-29 10:46:40', 'ENTRY', '1', '2025-05-29 07:46:48'),
(328, '7500551', 6, 'CKT', '2025-05-29 10:47:57', 'EXIT', '1', '2025-05-29 07:48:05'),
(329, '4963194', 2, 'Mustafa ', '2025-05-29 10:48:00', 'ENTRY', '1', '2025-05-29 07:48:09'),
(330, '6956841', 10, 'DENEMEMAAS', '2025-05-29 10:48:22', 'ENTRY', '1', '2025-05-29 07:48:31'),
(331, '7498554', 7, 'Selçuk ', '2025-05-29 10:48:26', 'ENTRY', '1', '2025-05-29 07:48:35'),
(332, '7500551', 6, 'CKT', '2025-05-29 10:48:36', 'ENTRY', '1', '2025-05-29 07:48:45'),
(333, '6665067', 9, 'Hasan', '2025-05-29 10:48:47', 'ENTRY', '1', '2025-05-29 07:48:55'),
(334, '7059117', 11, 'ŞAKŞUKA', '2025-05-29 10:48:56', 'ENTRY', '1', '2025-05-29 07:49:05'),
(335, '5384286', 8, 'Arkin', '2025-05-29 10:49:11', 'ENTRY', '1', '2025-05-29 07:49:20'),
(336, '5384286', 8, 'Arkin', '2025-05-29 11:12:19', 'EXIT', '1', '2025-05-29 08:12:27'),
(337, '4963194', 2, 'Mustafa ', '2025-05-30 02:12:16', 'EXIT', '1', '2025-05-29 23:12:23'),
(338, '6956841', 10, 'DENEMEMAAS', '2025-05-30 02:12:22', 'EXIT', '1', '2025-05-29 23:12:29'),
(339, '7498554', 7, 'Selçuk ', '2025-05-30 02:12:31', 'EXIT', '1', '2025-05-29 23:12:38'),
(340, '6665067', 9, 'Hasan', '2025-05-30 02:12:46', 'EXIT', '1', '2025-05-29 23:12:52'),
(341, '7059117', 11, 'ŞAKŞUKA', '2025-05-30 02:12:52', 'EXIT', '1', '2025-05-29 23:12:58'),
(342, '7500551', 6, 'CKT', '2025-05-30 02:13:12', 'EXIT', '1', '2025-05-29 23:13:19'),
(343, '7059117', 11, 'ŞAKŞUKA', '2025-05-30 02:16:19', 'ENTRY', '1', '2025-05-29 23:16:26'),
(344, '7059117', 11, 'ŞAKŞUKA', '2025-05-30 02:16:26', 'EXIT', '1', '2025-05-29 23:16:32'),
(345, '8832330', 3, 'Yusuf', '2025-06-02 16:28:54', 'ENTRY', '1', '2025-06-02 13:29:02'),
(346, '2436491', 12, 'Kenan', '2025-06-02 16:58:54', 'ENTRY', '1', '2025-06-02 13:59:02'),
(347, '2436491', 12, 'Kenan', '2025-06-02 16:59:00', 'EXIT', '1', '2025-06-02 13:59:09'),
(348, '2436491', 12, 'ADEM ATABERK', '2025-06-02 19:04:38', 'ENTRY', '1', '2025-06-02 16:04:46'),
(349, '2436491', 12, 'ADEM ATABERK', '2025-06-02 19:04:46', 'EXIT', '1', '2025-06-02 16:04:53'),
(350, '2436491', 12, 'ADEM ATABERK', '2025-06-02 19:04:55', 'ENTRY', '1', '2025-06-02 16:05:03'),
(351, '2436491', 12, 'ADEM ATABERK', '2025-06-02 19:05:03', 'EXIT', '1', '2025-06-02 16:05:11'),
(352, '2436491', 12, 'ADEM ATABERK', '2025-06-02 19:05:03', 'ENTRY', '1', '2025-06-02 16:05:11'),
(353, '8832330', 3, 'Yusuf', '2025-06-02 20:20:08', 'EXIT', '1', '2025-06-02 17:20:16'),
(354, '8832330', 3, 'Yusuf', '2025-06-02 20:20:08', 'ENTRY', '1', '2025-06-02 17:20:16');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cards`
--

CREATE TABLE `cards` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `surname` varchar(100) DEFAULT NULL,
  `card_number` varchar(50) NOT NULL,
  `privilege` varchar(10) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `enabled` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `synced_to_device` tinyint(4) DEFAULT 0,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT 0.00 COMMENT 'Temel Maaş',
  `hourly_rate` decimal(10,2) DEFAULT 0.00 COMMENT 'Saat Başı Ücret',
  `overtime_rate` decimal(10,2) DEFAULT 1.50 COMMENT 'Mesai Çarpanı (1.5x, 2x)',
  `daily_work_hours` decimal(5,2) DEFAULT 8.00 COMMENT 'Günlük Normal Çalışma Saati',
  `monthly_work_days` int(11) DEFAULT 22 COMMENT 'Aylık Çalışma Günü',
  `fixed_salary` decimal(10,2) DEFAULT 35000.00 COMMENT 'Sabit Maaş',
  `salary_type` enum('fixed','hourly') DEFAULT 'fixed' COMMENT 'Maaş Türü: fixed=Sabit, hourly=Saatlik'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `cards`
--

INSERT INTO `cards` (`id`, `user_id`, `name`, `surname`, `card_number`, `privilege`, `password`, `enabled`, `created_at`, `synced_to_device`, `department`, `position`, `phone`, `email`, `hire_date`, `birth_date`, `address`, `photo_path`, `base_salary`, `hourly_rate`, `overtime_rate`, `daily_work_hours`, `monthly_work_days`, `fixed_salary`, `salary_type`) VALUES
(1, '1', 'Halil', NULL, '2587674', '1', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(2, '2', 'Mustafa ', NULL, '4963194', '1', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(3, '3', 'Yusuf', NULL, '8832330', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(4, '11', 'ŞAKŞUKA', NULL, '7059117', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(5, '6', 'CKT', NULL, '7500551', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(6, '7', 'Selçuk ', NULL, '7498554', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(7, '8', 'Arkin', NULL, '5384286', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(8, '9', 'Hasan', NULL, '6665067', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(9, '10', 'DENEMEMA', NULL, '6956841', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(10, '5', '10uncu', NULL, '7059117', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed'),
(11, '12', 'SILA', NULL, '2436491', '0', '', 'true', '2025-06-02 17:10:48', 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, 0.00, 1.50, 8.00, 22, 35000.00, 'fixed');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `card_logs`
--

CREATE TABLE `card_logs` (
  `id` int(11) NOT NULL,
  `card_number` varchar(50) NOT NULL,
  `scan_time` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `commands`
--

CREATE TABLE `commands` (
  `id` int(11) NOT NULL,
  `command_type` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `commands`
--

INSERT INTO `commands` (`id`, `command_type`, `user_id`, `status`, `created_at`, `processed_at`) VALUES
(55, 'sync_user', 6, 'failed', '2025-05-25 22:45:50', '2025-05-25 22:45:55'),
(56, 'sync_user', 6, 'failed', '2025-05-25 22:47:17', '2025-05-25 22:47:25'),
(57, 'sync_user', 9, 'failed', '2025-05-25 22:52:15', '2025-05-25 22:52:25'),
(58, 'sync_all', NULL, 'failed', '2025-05-25 22:52:52', '2025-05-25 22:52:55'),
(59, 'sync_user', 10, 'failed', '2025-05-25 23:11:51', '2025-05-25 23:11:55'),
(60, 'sync_user', 10, 'failed', '2025-05-25 23:14:40', '2025-05-25 23:14:55'),
(61, 'sync_user', 10, 'failed', '2025-05-26 00:08:21', '2025-05-26 00:08:25'),
(62, 'sync_user', 5, 'failed', '2025-05-26 00:11:23', '2025-05-26 00:11:25'),
(63, 'delete_user', 4, 'completed', '2025-05-26 00:15:54', '2025-05-26 00:15:55'),
(64, 'sync_user', 11, 'failed', '2025-05-26 00:17:38', '2025-05-26 00:17:55'),
(65, 'sync_user', 6, 'failed', '2025-05-26 01:38:50', '2025-05-26 01:38:55'),
(66, 'sync_user', 6, 'failed', '2025-05-26 01:40:19', '2025-05-26 01:40:25'),
(67, 'delete_from_device', 6, 'failed', '2025-05-26 01:44:32', '2025-05-26 01:44:55'),
(68, 'delete_from_device', 6, 'failed', '2025-05-26 01:45:16', '2025-05-26 01:45:25'),
(69, 'sync_all', NULL, 'failed', '2025-06-02 13:34:10', '2025-06-02 13:34:13'),
(70, 'sync_all', NULL, 'failed', '2025-06-02 13:34:35', '2025-06-02 13:34:43'),
(71, 'sync_all', NULL, 'failed', '2025-06-02 13:34:43', '2025-06-02 13:35:13'),
(72, 'sync_all', NULL, 'failed', '2025-06-02 13:34:45', '2025-06-02 13:35:13'),
(73, 'sync_all', NULL, 'failed', '2025-06-02 13:35:00', '2025-06-02 13:35:13'),
(74, 'sync_all', NULL, 'failed', '2025-06-02 13:35:01', '2025-06-02 13:35:13'),
(75, 'sync_all', NULL, 'failed', '2025-06-02 13:36:35', '2025-06-02 13:49:14'),
(76, 'sync_all', NULL, 'failed', '2025-06-02 13:36:38', '2025-06-02 13:49:14'),
(77, 'sync_all', NULL, 'failed', '2025-06-02 13:50:21', '2025-06-02 13:50:43'),
(78, 'sync_user', 12, 'failed', '2025-06-02 13:58:10', '2025-06-02 13:58:13'),
(79, 'sync_user', 12, 'failed', '2025-06-02 14:23:51', '2025-06-02 14:24:40'),
(80, 'sync_user', 12, 'failed', '2025-06-02 14:23:52', '2025-06-02 14:24:40'),
(81, 'sync_user', 12, 'failed', '2025-06-02 14:23:52', '2025-06-02 14:24:40'),
(82, 'sync_user', 12, 'failed', '2025-06-02 14:23:56', '2025-06-02 14:24:40'),
(83, 'sync_all', NULL, 'failed', '2025-06-02 14:24:35', '2025-06-02 14:24:40'),
(84, 'sync_user', 12, 'failed', '2025-06-02 14:26:11', '2025-06-02 14:26:13'),
(85, 'sync_user', 12, 'failed', '2025-06-02 14:26:15', '2025-06-02 14:26:43'),
(86, 'sync_user', 12, 'failed', '2025-06-02 14:26:16', '2025-06-02 14:26:43'),
(87, 'sync_user', 12, 'failed', '2025-06-02 14:33:13', '2025-06-02 14:33:13'),
(88, 'sync_all', NULL, 'failed', '2025-06-02 16:00:21', '2025-06-02 16:00:29'),
(89, 'delete_user', 12, 'completed', '2025-06-02 16:00:41', '2025-06-02 16:01:02'),
(90, 'sync_all', NULL, 'failed', '2025-06-02 16:00:44', '2025-06-02 16:01:02'),
(91, 'sync_user', 12, 'failed', '2025-06-02 16:02:12', '2025-06-02 16:02:29'),
(92, 'sync_all', NULL, 'failed', '2025-06-02 16:40:13', '2025-06-02 16:40:30'),
(93, 'sync_all', NULL, 'failed', '2025-06-02 16:40:19', '2025-06-02 16:40:30');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `department_managers`
--

CREATE TABLE `department_managers` (
  `id` int(11) NOT NULL,
  `department` varchar(100) NOT NULL,
  `manager_id` varchar(50) NOT NULL,
  `can_approve_leave` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `department_managers`
--

INSERT INTO `department_managers` (`id`, `department`, `manager_id`, `can_approve_leave`, `created_at`) VALUES
(1, 'PATRON', '1', 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `holidays`
--

CREATE TABLE `holidays` (
  `id` int(11) NOT NULL,
  `holiday_date` date NOT NULL,
  `name` varchar(255) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `holidays`
--

INSERT INTO `holidays` (`id`, `holiday_date`, `name`, `is_active`) VALUES
(1, '2024-01-01', 'Yılbaşı', 1),
(2, '2024-04-23', '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı', 1),
(3, '2024-05-01', 'İşçi Bayramı', 1),
(4, '2024-05-19', '19 Mayıs Atatürk\'ü Anma, Gençlik ve Spor Bayramı', 1),
(5, '2024-08-30', '30 Ağustos Zafer Bayramı', 1),
(6, '2024-10-29', '29 Ekim Cumhuriyet Bayramı', 1),
(7, '2025-01-01', 'Yılbaşı', 1),
(8, '2025-04-23', '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı', 1),
(9, '2025-05-01', 'İşçi Bayramı', 1),
(10, '2025-05-19', '19 Mayıs Atatürk\'ü Anma, Gençlik ve Spor Bayramı', 1),
(11, '2025-08-30', '30 Ağustos Zafer Bayramı', 1),
(12, '2025-10-29', '29 Ekim Cumhuriyet Bayramı', 1),
(13, '2026-01-01', 'Yılbaşı', 1),
(14, '2026-04-23', '23 Nisan Ulusal Egemenlik ve Çocuk Bayramı', 1),
(15, '2026-05-01', 'İşçi Bayramı', 1),
(16, '2026-05-19', '19 Mayıs Atatürk\'ü Anma, Gençlik ve Spor Bayramı', 1),
(17, '2026-08-30', '30 Ağustos Zafer Bayramı', 1),
(18, '2026-10-29', '29 Ekim Cumhuriyet Bayramı', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `total_days` decimal(5,1) NOT NULL,
  `used_days` decimal(5,1) DEFAULT 0.0,
  `remaining_days` decimal(5,1) GENERATED ALWAYS AS (`total_days` - `used_days`) VIRTUAL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `leave_balances`
--

INSERT INTO `leave_balances` (`id`, `user_id`, `leave_type_id`, `year`, `total_days`, `used_days`, `created_at`, `updated_at`) VALUES
(1, '8', 5, 2025, 10.0, 12.0, '2025-05-24 01:59:52', '2025-05-25 21:59:50'),
(2, '2', 4, 2025, 30.0, 24.0, '2025-05-24 02:03:39', '2025-05-24 02:05:55'),
(3, '3', 5, 2025, 10.0, 8.0, '2025-05-25 22:39:47', '2025-05-26 23:56:41'),
(4, '3', 4, 2025, 10.0, 0.0, '2025-05-25 22:39:57', NULL),
(5, '3', 2, 2025, 10.0, 4.0, '2025-05-25 22:40:04', '2025-05-26 23:49:53'),
(6, '3', 3, 2025, 10.0, 0.0, '2025-05-25 22:40:10', NULL),
(7, '3', 6, 2025, 10.0, 0.0, '2025-05-25 22:40:16', NULL),
(8, '3', 1, 2025, 25.0, 0.0, '2025-05-25 22:40:21', NULL),
(9, '7', 2, 2025, 5.0, 0.0, '2025-05-26 23:50:12', NULL),
(10, '7', 1, 2025, 14.0, 0.0, '2025-05-26 23:57:02', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,1) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `approved_by` varchar(50) DEFAULT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `leave_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `approved_by`, `comment`, `created_at`, `updated_at`) VALUES
(1, '2', 4, '2025-05-24', '2025-05-31', 8.0, 'Evleniyom amk izin verin', 'approved', NULL, 'Hayırlı olsun TAM ALTIN gönderdim.', '2025-05-24 02:01:19', '2025-05-24 02:04:14'),
(2, '2', 4, '2025-05-24', '2025-05-31', 8.0, 'Evleniyom amk izin verin', 'approved', NULL, '', '2025-05-24 02:03:51', '2025-05-24 02:04:24'),
(3, '8', 5, '2025-05-24', '2025-06-04', 12.0, '', 'approved', NULL, 'asd', '2025-05-24 02:34:11', '2025-05-25 21:59:50'),
(4, '3', 5, '2025-05-26', '2025-05-29', 4.0, 'Doğum izni', 'approved', NULL, 'Onaylandı', '2025-05-25 22:38:05', '2025-05-25 22:41:56'),
(5, '3', 2, '2025-05-27', '2025-05-30', 4.0, 'Grip olduğum için gelemiyorum..', 'approved', NULL, 'Onaylandı', '2025-05-26 23:47:22', '2025-05-26 23:49:53'),
(6, '3', 5, '2025-05-27', '2025-05-30', 4.0, 'Eşim doğum yapacak..\r\n', 'approved', NULL, 'ONAYLANDI\r\n', '2025-05-26 23:56:07', '2025-05-26 23:56:41'),
(7, '3', 3, '2025-05-27', '2025-05-30', 4.0, 'Yollar karlı gelemiyorum..', 'rejected', NULL, 'ÖYLE BİR MAZERET YOK... GELECEKSİN :DD', '2025-05-27 00:00:28', '2025-05-27 00:01:39');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `max_days` int(11) DEFAULT NULL,
  `color` varchar(20) DEFAULT '#3498db',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `description`, `max_days`, `color`, `is_active`, `created_at`) VALUES
(1, 'Yıllık İzin', 'Normal yıllık izin hakkı', NULL, '#2ecc71', 1, '2025-05-24 01:43:05'),
(2, 'Hastalık İzni', 'Sağlık nedeniyle alınan izinler', NULL, '#e74c3c', 1, '2025-05-24 01:43:05'),
(3, 'Mazeret İzni', 'Özel durumlarda kullanılan kısa süreli izinler', 3, '#f39c12', 1, '2025-05-24 01:43:05'),
(4, 'Evlilik İzni', 'Evlilik nedeniyle verilen izin', 3, '#9b59b6', 1, '2025-05-24 01:43:05'),
(5, 'Doğum İzni', 'Doğum nedeniyle verilen izin', NULL, '#3498db', 1, '2025-05-24 01:43:05'),
(6, 'Ücretsiz İzin', 'Ücretsiz olarak kullanılan izinler', NULL, '#95a5a6', 1, '2025-05-24 01:43:05');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'company_name', 'Şirket Adı'),
(2, 'system_title', 'PDKS - Personel '),
(3, 'auto_sync', 'enabled'),
(4, 'smtp_server', ''),
(5, 'smtp_email', ''),
(6, 'smtp_password', ''),
(31, 'salary_minimum_work_days', '20'),
(32, 'salary_minimum_work_rate', '90'),
(33, 'salary_minimum_type', 'percentage'),
(34, 'salary_exclude_weekends', 'true'),
(35, 'salary_exclude_holidays', 'true');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `absence_type_id` (`absence_type_id`),
  ADD KEY `start_date` (`start_date`),
  ADD KEY `idx_date_range` (`start_date`,`end_date`),
  ADD KEY `idx_user_date` (`user_id`,`start_date`);

--
-- Tablo için indeksler `absence_types`
--
ALTER TABLE `absence_types`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `card_logs`
--
ALTER TABLE `card_logs`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `commands`
--
ALTER TABLE `commands`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `department_managers`
--
ALTER TABLE `department_managers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `department` (`department`,`manager_id`);

--
-- Tablo için indeksler `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `holiday_date` (`holiday_date`);

--
-- Tablo için indeksler `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`leave_type_id`,`year`);

--
-- Tablo için indeksler `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `absences`
--
ALTER TABLE `absences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Tablo için AUTO_INCREMENT değeri `absence_types`
--
ALTER TABLE `absence_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=355;

--
-- Tablo için AUTO_INCREMENT değeri `cards`
--
ALTER TABLE `cards`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Tablo için AUTO_INCREMENT değeri `card_logs`
--
ALTER TABLE `card_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=368;

--
-- Tablo için AUTO_INCREMENT değeri `commands`
--
ALTER TABLE `commands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- Tablo için AUTO_INCREMENT değeri `department_managers`
--
ALTER TABLE `department_managers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `holidays`
--
ALTER TABLE `holidays`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Tablo için AUTO_INCREMENT değeri `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Tablo için AUTO_INCREMENT değeri `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`absence_type_id`) REFERENCES `absence_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
