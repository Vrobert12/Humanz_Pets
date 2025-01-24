-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Jan 24. 23:37
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `pets`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `errorlog`
--

CREATE TABLE `errorlog` (
  `errorLogId` int(11) NOT NULL,
  `errorType` varchar(30) NOT NULL,
  `errorMail` varchar(100) NOT NULL,
  `errorText` text NOT NULL,
  `errorTime` datetime(6) NOT NULL DEFAULT current_timestamp(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `errorlog`
--

INSERT INTO `errorlog` (`errorLogId`, `errorType`, `errorMail`, `errorText`, `errorTime`) VALUES
(1, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-24 10:54:34.000000'),
(2, 'Log in', 'robertvarro1fd2@gmail.com', 'Not registered E-mail!', '2024-04-24 10:54:49.000000'),
(3, 'Password change', 'robertvarro123@gmail.com', 'Not registered E-mail!', '2024-04-24 10:59:50.000000'),
(4, 'E-mail validation', 'varrorobert03@gmail.com', 'The validation code is not correct!', '2024-04-24 11:18:29.000000'),
(5, 'E-mail validation', 'varrorobert03@gmail.com', 'The validation code is not correct!', '2024-04-24 11:19:42.000000'),
(6, 'E-mail validation', 'varrorobert03@gmail.com', 'Time for validation has expired', '2024-04-24 11:21:34.000000'),
(7, 'E-mail validation', 'varrorobert03@gmail.com', 'Time for validation has expired', '2024-04-24 11:23:54.000000'),
(8, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-25 07:51:58.000000'),
(9, 'Log in', 'hupkodominik1ee4rertg43@gmail.com', 'Not registered E-mail!', '2024-04-25 07:52:19.000000'),
(10, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-27 14:25:52.000000'),
(11, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-27 14:26:03.000000'),
(12, 'Password change', 'robertvarro12@gmail.com', 'Not registered E-mail!', '2024-04-27 14:40:17.000000'),
(13, 'Password change', 'robertvarro12@gmail.com', 'Not registered E-mail!', '2024-04-27 14:40:30.000000'),
(14, 'Log in', '', 'Not registered E-mail!', '2024-04-27 15:55:55.000000'),
(15, 'Log in', '', 'Not registered E-mail!', '2024-04-27 15:55:57.000000'),
(16, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-27 17:06:15.000000'),
(17, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-27 20:34:24.000000'),
(18, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-27 21:18:09.000000'),
(19, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-27 21:49:53.000000'),
(20, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-27 21:51:25.000000'),
(21, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-27 22:13:44.000000'),
(22, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-28 22:24:59.000000'),
(23, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-28 22:37:27.000000'),
(24, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-28 22:45:12.000000'),
(25, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-28 22:46:38.000000'),
(26, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-28 22:46:52.000000'),
(27, 'Adding a Worker', 'robertvarro12@gmail.com', 'The worker is already registered', '2024-04-28 22:47:16.000000'),
(28, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:05:25.000000'),
(29, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:06:52.000000'),
(30, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:08:17.000000'),
(31, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:10:24.000000'),
(32, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:10:34.000000'),
(33, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:11:10.000000'),
(34, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:11:34.000000'),
(35, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:11:50.000000'),
(36, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:13:55.000000'),
(37, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:14:21.000000'),
(38, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:14:48.000000'),
(39, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:16:30.000000'),
(40, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:17:05.000000'),
(41, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:18:18.000000'),
(42, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-28 23:19:05.000000'),
(43, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:19:33.000000'),
(44, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:21:12.000000'),
(45, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:22:16.000000'),
(46, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:23:09.000000'),
(47, 'Log in', 'varrorobert03@gmail.com', 'The worker did not set up a password!', '2024-04-28 23:25:00.000000'),
(48, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-30 19:53:34.000000'),
(49, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-30 19:53:45.000000'),
(50, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-30 21:21:13.000000'),
(51, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-30 21:21:25.000000'),
(52, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-30 21:22:05.000000'),
(53, 'Log in', 'varrorobert03@gmail.com', 'Wrong password!', '2024-04-30 21:22:07.000000'),
(54, 'Banned', 'varrorobert03@gmail.com', 'User tried to log in while he is banned!', '2024-04-30 21:25:42.000000'),
(55, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-04-30 21:42:30.000000'),
(56, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-05-04 21:26:15.000000'),
(57, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-05-04 23:54:17.000000'),
(58, 'Log in', 'robertvarro12@gmail.com', 'Wrong password!', '2024-05-05 18:17:50.000000'),
(59, 'Log in', '', 'Not registered E-mail!', '2024-05-23 17:11:26.000000'),
(60, 'Log in', 'robertvrro12@gmail.com', 'Not registered E-mail!', '2024-05-24 08:34:28.000000'),
(61, 'E-mail validation', 'varrorobert03@gmail.com', 'Time for validation has expired', '2024-06-11 13:24:20.000000'),
(62, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-06-15 20:47:21.000000'),
(63, 'UserModify', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-15 21:05:30.000000'),
(64, 'UserModify', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-15 21:05:32.000000'),
(65, 'UserModify', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-15 21:05:34.000000'),
(66, 'UserModify', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-15 21:05:42.000000'),
(67, 'Log in', 'robertarro12@gmail.com', 'The E-mal is not in our database', '2024-06-16 10:31:47.000000'),
(68, 'Log in', 'robertarro12@gmail.com', 'The E-mal is not in our database', '2024-06-16 11:06:12.000000'),
(69, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-19 10:07:14.000000'),
(70, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-19 13:27:56.000000'),
(71, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-06-19 14:08:35.000000'),
(72, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:13:52.000000'),
(73, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:15:09.000000'),
(74, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-19 20:23:44.000000'),
(75, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:34:10.000000'),
(76, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:34:51.000000'),
(77, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:36:27.000000'),
(78, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:38:45.000000'),
(79, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:39:53.000000'),
(80, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:40:54.000000'),
(81, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:41:46.000000'),
(82, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:42:41.000000'),
(83, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:43:13.000000'),
(84, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:44:06.000000'),
(85, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:45:58.000000'),
(86, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:48:52.000000'),
(87, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:50:13.000000'),
(88, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 20:50:30.000000'),
(89, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-06-19 21:45:06.000000'),
(90, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-06-20 13:34:28.000000'),
(91, 'Log in', 'varrorobert03@gmail.com', 'The password was not valid!', '2024-10-31 19:29:32.000000'),
(92, 'Log in', 'robertvarro12@gmail.com', 'The E-mail is not in our database', '2024-11-08 09:57:10.000000'),
(93, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-11-09 20:15:17.000000'),
(94, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-11-09 20:15:27.000000'),
(95, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 200KB', '2024-11-09 20:16:28.000000'),
(96, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 300KB', '2024-11-09 21:43:05.000000'),
(97, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-11-09 22:24:05.000000'),
(98, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 300KB', '2024-11-11 14:26:41.000000'),
(99, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-11-11 19:04:43.000000'),
(100, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:49:04.000000'),
(101, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:49:15.000000'),
(102, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:51:54.000000'),
(103, 'Log in', 'varrorobert03@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:52:03.000000'),
(104, 'Log in', 'varrorobert03@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:52:39.000000'),
(105, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-11-30 16:53:45.000000'),
(106, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:57:28.000000'),
(107, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:57:41.000000'),
(108, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:58:18.000000'),
(109, 'Log in', 'robertvarro12@gmail.com', 'The E-mal is not in our database', '2024-11-30 16:59:06.000000'),
(110, 'Log in', 'varrorobert03@gmail.com', 'The password was not valid!', '2024-11-30 17:12:25.000000'),
(111, 'Log in', 'varrorobert03@gmail.com', 'The password was not valid!', '2024-11-30 18:13:45.000000'),
(112, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-12-01 15:37:30.000000'),
(113, 'Picture', 'varrorobert03@gmail.com', 'The file is bigger than 300KB', '2024-12-14 13:46:40.000000'),
(114, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-12-14 14:42:20.000000'),
(115, 'Log in', 'varrorobert03@gmail.com', 'The password was not valid!', '2024-12-14 15:26:41.000000'),
(116, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 300KB', '2024-12-21 19:00:25.000000'),
(117, 'Picture', 'varrorobert03@gmail.com', 'The file is bigger than 300KB', '2024-12-22 20:24:22.000000'),
(118, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2024-12-23 14:18:41.000000'),
(119, 'Picture', 'robertvarro12@gmail.com', 'The file is bigger than 300KB', '2025-01-02 21:13:02.000000'),
(120, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-02 22:07:11.000000'),
(121, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:19:26.000000'),
(122, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:25:16.000000'),
(123, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:25:37.000000'),
(124, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:25:39.000000'),
(125, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:26:45.000000'),
(126, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:26:47.000000'),
(127, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:42:03.000000'),
(128, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:42:05.000000'),
(129, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:42:30.000000'),
(130, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:42:32.000000'),
(131, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-02 23:42:48.000000'),
(132, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:53:55.000000'),
(133, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:53:57.000000'),
(134, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:54:09.000000'),
(135, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:54:11.000000'),
(136, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:54:47.000000'),
(137, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:54:49.000000'),
(138, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-02 23:57:45.000000'),
(139, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-02 23:57:47.000000'),
(140, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'banned\' in \'field list\'', '2025-01-03 00:00:15.000000'),
(141, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-03 00:00:17.000000'),
(142, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'privilage\' in \'field list\'', '2025-01-03 00:03:40.000000'),
(143, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-03 00:03:42.000000'),
(144, 'Fetch User/Vet', 'varrorobert03@gmail.com', 'Database error: SQLSTATE[42S22]: Column not found: 1054 Unknown column \'privilage\' in \'field list\'', '2025-01-03 00:03:53.000000'),
(145, 'Log in', 'varrorobert03@gmail.com', 'The E-mail is not in our database', '2025-01-03 00:03:55.000000'),
(146, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-03 00:17:35.000000'),
(147, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-03 00:32:27.000000'),
(148, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-03 21:16:29.000000'),
(149, 'Password change', 'varrorobert03@gmail.com', 'Not registered E-mail!', '2025-01-03 23:32:03.000000'),
(150, 'Password change', 'varrorobert03@gmail.com', 'Not registered E-mail!', '2025-01-03 23:32:15.000000'),
(151, 'Log in', 'robertvrro12@gmail.com', 'The E-mail is not in our database', '2025-01-04 19:07:13.000000'),
(152, 'Picture', 'varrorobert03@gmail.com', 'The file is bigger than 300KB', '2025-01-04 19:08:32.000000'),
(153, 'Log in', 'varrorobert03@gmail.com', 'The password was not valid!', '2025-01-04 19:47:20.000000'),
(154, 'Log in', 'robertvarro12@gmail.com', 'The password was not valid!', '2025-01-04 20:42:38.000000'),
(155, 'Log in', 'varrorobert03@gmail.co', 'The E-mail is not in our database', '2025-01-24 15:18:51.000000'),
(156, 'Log in', 'hupkodominik143@gmail.com', 'The password was not valid!', '2025-01-24 15:19:19.000000');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `log`
--

CREATE TABLE `log` (
  `id_log` int(6) NOT NULL,
  `user_agent` varchar(250) NOT NULL,
  `ip_address` varchar(6) NOT NULL,
  `country` varchar(11) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `device_type` varchar(11) NOT NULL,
  `proxy` tinyint(1) NOT NULL,
  `isp` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `log`
--

INSERT INTO `log` (`id_log`, `user_agent`, `ip_address`, `country`, `date_time`, `device_type`, `proxy`, `isp`) VALUES
(1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', '0', '0000-00-00 00:00:00', '0', 1, '0'),
(2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '0000-00-00 00:00:00', 'computer', 1, 'Latitude.sh'),
(3, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '2024-10-09 12:33:02', 'computer', 1, 'Latitude.sh'),
(4, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '2024-10-09 12:34:40', 'computer', 1, 'Latitude.sh'),
(5, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '2024-10-09 12:40:30', 'computer', 1, 'Latitude.sh'),
(6, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '2024-10-09 12:41:05', 'computer', 1, 'Latitude.sh'),
(7, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36 GLS/100.10.9989.100', '103.14', 'Mexico', '2024-10-09 12:41:16', 'computer', 1, 'Latitude.sh'),
(8, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 10:18:45', 'computer', 0, 'KE-ING'),
(9, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 11:17:41', 'computer', 0, 'KE-ING'),
(10, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 11:18:26', 'computer', 0, 'KE-ING'),
(11, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 11:19:03', 'computer', 0, 'KE-ING'),
(12, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 11:30:16', 'computer', 0, 'KE-ING'),
(13, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/129.0.0.0 Safari/537.36', '119.14', 'Taiwan', '2024-10-16 11:39:32', 'computer', 0, 'KE-ING');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `pet`
--

CREATE TABLE `pet` (
  `petId` int(11) NOT NULL,
  `petName` varchar(50) NOT NULL,
  `bred` varchar(50) NOT NULL,
  `petSpecies` varchar(50) NOT NULL,
  `petPicture` varchar(100) NOT NULL,
  `userId` int(6) NOT NULL,
  `veterinarId` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `pet`
--

INSERT INTO `pet` (`petId`, `petName`, `bred`, `petSpecies`, `petPicture`, `userId`, `veterinarId`) VALUES
(1, 'Buksi', 'Mixed', 'Dog  ', '20241223153456.png', 19, 1),
(2, 'Tigrincs', 'Mixed', 'Cat ', '20241223151911.png', 19, 1),
(3, 'Buksi', 'Mixed', 'Dog', '20241214163313.png', 26, 1),
(4, 'Tigrincs', 'Mixed', 'Cat', '20241214163628.png', 26, 1),
(9, 'Buksi', 'Mixed', 'Cat', '20241214225926.png', 26, 1),
(10, 'Buksi', 'Mixed', 'Dog', '20241214230315.png', 27, 1),
(11, 'Tigrincs', 'Mixed', 'Cat', '20241214230343.png', 27, 1),
(12, 'Buksi', 'Mixed', 'Cat', '20241214230428.png', 27, 1),
(13, 'Zizi', 'Pug', 'Dog', '20241215162746.png', 25, 1),
(14, 'Zizi2', 'Pug', 'Dog', '20241215190624.png', 25, 1),
(15, 'Tigrincs', 'Mixed', 'Cat', '20241221144748.png', 28, 1),
(40, 'Buksi', 'Tiszta', 'Dog  ', '20241223000554.png', 29, 1),
(44, 'Tigrincs', 'Mixed', 'Dog ', '20241223140237.png', 29, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

CREATE TABLE `product` (
  `productId` int(11) NOT NULL,
  `productName` varchar(100) NOT NULL,
  `productPicture` varchar(100) NOT NULL,
  `description` varchar(255) NOT NULL,
  `productCost` decimal(6,2) NOT NULL,
  `productRelease` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `product`
--

INSERT INTO `product` (`productId`, `productName`, `productPicture`, `description`, `productCost`, `productRelease`) VALUES
(1, 'Collar', 'logInPic.png', 'This is a collar.', 5.00, '2024-12-16 18:27:39'),
(2, 'Leash', '20241226145950.png', 'This is a leash.', 6.99, '2024-12-26 14:59:50'),
(3, 'Cicamica', '20241221193351.png', 'Da vidiomo', 23.99, '2024-12-21 20:00:13'),
(4, 'Asd', '20241221193523.png', 'Egyedi textarea szoveg valamit meg irjunk domi', 23.34, '2024-12-21 19:57:39');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `qr_code`
--

CREATE TABLE `qr_code` (
  `qr_code_id` int(11) NOT NULL,
  `qrCodeName` varchar(100) NOT NULL,
  `userId` int(6) NOT NULL,
  `generated_at` datetime NOT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `qr_code`
--

INSERT INTO `qr_code` (`qr_code_id`, `qrCodeName`, `userId`, `generated_at`, `updated_at`) VALUES
(1, 'QRcodes/qrcode_6793984e03426.png', 19, '2024-11-11 19:47:18', '2025-01-24 13:40:30'),
(2, 'QRcodes/qrcode_675da53946388.png', 26, '2024-12-14 16:33:13', '2024-12-14 15:33:13'),
(3, 'QRcodes/qrcode_675e00a3bfff4.png', 27, '2024-12-14 23:03:15', '2024-12-14 22:03:15'),
(4, 'QRcodes/qrcode_675ef571ca9a1.png', 25, '2024-12-15 16:27:46', '2024-12-15 15:27:46'),
(5, 'QRcodes/qrcode_6766c7046bb35.png', 28, '2024-12-21 14:47:48', '2024-12-21 13:47:48'),
(6, 'QRcodes/qrcode_676857dc08696.png', 29, '2024-12-22 19:18:04', '2024-12-22 18:18:04');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `reservation`
--

CREATE TABLE `reservation` (
  `reservationId` int(11) NOT NULL,
  `reservationDay` date DEFAULT NULL,
  `reservationTime` time DEFAULT NULL,
  `period` time NOT NULL,
  `animalChecked` tinyint(1) DEFAULT 0,
  `veterinarianId` int(11) NOT NULL,
  `petId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `reservation`
--

INSERT INTO `reservation` (`reservationId`, `reservationDay`, `reservationTime`, `period`, `animalChecked`, `veterinarianId`, `petId`) VALUES
(1, '2025-01-13', '09:00:00', '10:00:00', 0, 3, 1),
(2, '2025-01-31', '17:00:00', '18:00:00', 0, 3, 40),
(4, '2025-01-12', '17:00:00', '18:00:00', 0, 3, 1),
(9, '2025-01-14', '18:00:00', '19:00:00', 0, 3, 2),
(20, '2025-01-25', '13:00:00', '14:00:00', 1, 3, 1),
(21, '2025-01-25', '14:00:00', '15:00:00', 1, 3, 2),
(22, '2025-01-30', '10:00:00', '11:00:00', 0, 3, 44);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `review`
--

CREATE TABLE `review` (
  `reviewId` int(11) NOT NULL,
  `review` int(11) DEFAULT NULL,
  `reviewTime` datetime NOT NULL DEFAULT current_timestamp(),
  `reviewCode` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `veterinarianId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `review`
--

INSERT INTO `review` (`reviewId`, `review`, `reviewTime`, `reviewCode`, `userId`, `veterinarianId`) VALUES
(1, NULL, '2025-01-24 23:03:59', 2147483647, 19, 3),
(2, NULL, '2025-01-24 23:05:33', 1471108962, 19, 3);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

CREATE TABLE `user` (
  `userId` int(6) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `phoneNumber` varchar(13) NOT NULL,
  `userMail` varchar(100) NOT NULL,
  `userPassword` varchar(60) NOT NULL,
  `profilePic` varchar(100) DEFAULT NULL,
  `privilage` varchar(25) NOT NULL,
  `registrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` int(50) DEFAULT NULL,
  `verify` int(11) NOT NULL,
  `verification_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `banned` tinyint(1) NOT NULL,
  `banned_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `passwordValidation` int(10) DEFAULT NULL,
  `passwordValidationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `usedLanguage` enum('en','hu','sr','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`userId`, `firstName`, `lastName`, `phoneNumber`, `userMail`, `userPassword`, `profilePic`, `privilage`, `registrationTime`, `verification_code`, `verify`, `verification_time`, `banned`, `banned_time`, `passwordValidation`, `passwordValidationTime`, `usedLanguage`) VALUES
(6, 'Nikoletta', 'Varro', '0', 'nikolettavarro12@gmail.com', '$2y$10$ZJtAXGLi1y8Y7VlLzE4Ru.nH.SbV5pbDRtoQTlOv88WgemWiSIrB2', 'logInPic.png', 'Guest', '0000-00-00 00:00:00', 401081, 0, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 0, '2024-04-23 09:54:10', 'en'),
(7, 'Nikoletta', 'Varro', '0', 'nikolettavarro@gmail.com', '$2y$10$GZ9eslD9.lWIwuBi0by.sunJYqe1s8Jn8K2eX4CefmMN/LOnyRNua', 'logInPic.png', 'Guest', '0000-00-00 00:00:00', 102107, 0, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 0, '2024-04-23 09:54:10', 'en'),
(19, 'Robert', 'Varro', '0649420637', 'robertvarro12@gmail.com', '$2y$10$sZKeBUr3S43dBnkiLkzP..1SJCekw6MqWlEpwwqj1puauC2HzEQ0O', '20250104204023.png', 'Admin', '0000-00-00 00:00:00', 229527, 1, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 167710, '2025-01-03 23:15:50', 'hu'),
(25, 'Dominik', 'Hupko', '628277140', 'hupkodominik143@gmail.comk', '$2y$10$XFNjOOWh2HAr8ZiYb.ur8egpiw4TlYdIIDEv2L/qnysTyqxDh8cb2', 'logInPic.png', 'Admin', '2024-06-03 12:23:54', 2047970, 1, '2024-06-03 12:33:54', 0, '0000-00-00 00:00:00', NULL, '2024-06-03 12:23:54', 'en'),
(26, 'Robert', 'Varro', '0649420637', 'varrorobert03@gmail.coml', '$2y$10$T5p19yMDoZ0EKiRXocAjJuNmNFK8INcDDWFkyC97i5rB6rODkLO82', 'logInPic.png', 'Guest', '2024-12-14 15:32:29', 1740492, 1, '2024-12-14 15:42:29', 0, '2024-12-14 15:32:29', NULL, '2024-12-14 15:32:29', 'en'),
(27, 'Robert', 'Varro', '0649420637', 'varro7robert03@gmail.com', '$2y$10$7.Vqm7RZ7EaQISuO6xdo3OcNPbEAERoUAJF8hsBBxD8mCAvaH5Bpi', 'logInPic.png', 'Guest', '2024-12-14 22:01:34', 3299090, 1, '2024-12-14 22:11:34', 0, '2024-12-14 22:01:34', NULL, '2024-12-14 22:01:34', 'en'),
(28, 'Robert', 'Varro', '0649420637', 'varkrorobert03@gmail.com', '$2y$10$WuiMPKYoHYmik23BpzphiuOlUZIL.pVOsGDkuUl.zzW.csVbAaaCG', 'logInPic.png', 'Guest', '2024-12-21 13:46:39', 1176453, 1, '2024-12-21 13:56:39', 0, '2024-12-21 13:46:39', NULL, '2024-12-21 13:46:39', 'en'),
(29, 'Robert', 'Varro', '0649420637', 'varrorobert03@gmail.comj', '$2y$10$eQ64rp7wa7khUMrxClHZxOjSnZKmL57c6ZlP84OWG.YeKAbgRAdji', 'logInPic.png', 'Guest', '2024-12-22 18:15:01', 2028307, 1, '2024-12-22 18:25:01', 0, '2024-12-22 18:15:01', NULL, '2024-12-22 18:15:01', 'en');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_product_relation`
--

CREATE TABLE `user_product_relation` (
  `userProductRelationId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `productId` int(11) NOT NULL,
  `sum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `user_product_relation`
--

INSERT INTO `user_product_relation` (`userProductRelationId`, `userId`, `productId`, `sum`) VALUES
(1, 19, 2, 1),
(3, 19, 2, 1),
(4, 19, 2, 3),
(5, 28, 3, 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_review_relation`
--

CREATE TABLE `user_review_relation` (
  `userReviewRelationId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `reviewId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `veterinarian`
--

CREATE TABLE `veterinarian` (
  `veterinarianId` int(6) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `phoneNumber` varchar(13) NOT NULL,
  `veterinarianMail` varchar(100) NOT NULL,
  `veterinarianPassword` varchar(60) DEFAULT NULL,
  `profilePic` varchar(100) DEFAULT NULL,
  `registrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` int(50) DEFAULT NULL,
  `verify` int(11) NOT NULL,
  `verification_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `passwordValidation` int(10) DEFAULT NULL,
  `passwordValidationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `workAddressId` int(11) NOT NULL,
  `usedLanguage` enum('en','hu','sr','') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `veterinarian`
--

INSERT INTO `veterinarian` (`veterinarianId`, `firstName`, `lastName`, `phoneNumber`, `veterinarianMail`, `veterinarianPassword`, `profilePic`, `registrationTime`, `verification_code`, `verify`, `verification_time`, `passwordValidation`, `passwordValidationTime`, `workAddressId`, `usedLanguage`) VALUES
(1, 'Robert', 'Varro', '0649420637', 'varrorobejrt03@gmail.com', '$2y$10$kDYqhdUxCiKKb86iW8vGIe6yi.8fmADky4QYmpcOXlV3Fh8hN1jOq', 'logInPic.png', '2024-12-14 13:02:10', NULL, 1, '2024-12-14 13:02:10', 1, '2024-12-14 13:02:10', 1, 'en'),
(2, 'Robert', 'Varro', '0649420637', 'hupkodominik143@gmail.com', '$2y$10$BkQO5/YgtNn7TA8mHdhJpecVxchuQTTtJRtuuoZpW6xyOTTr92mw.', 'logInPic.png', '2025-01-03 23:21:19', 1607888, 1, '2025-01-03 23:31:19', NULL, '2025-01-03 23:21:19', 0, 'hu'),
(3, 'Robert', 'Varro', '0649420637', 'varrorobert03@gmail.com', '$2y$10$u1KvS5Nh7JQBo89d7jM9yeFVAS6rrfptOrHum0v/KLNYPRfs60W/2', '20250124221618.jpg', '2025-01-04 18:44:36', 3296064, 1, '2025-01-04 18:54:36', NULL, '2025-01-04 18:44:36', 0, 'sr');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `veterinar_review_relation`
--

CREATE TABLE `veterinar_review_relation` (
  `veterinarReviewRelationID` int(11) NOT NULL,
  `veterinarId` int(11) NOT NULL,
  `reviewId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `work_address`
--

CREATE TABLE `work_address` (
  `workAddressId` int(11) NOT NULL,
  `workAddress` varchar(100) NOT NULL,
  `workCity` varchar(100) NOT NULL,
  `workDisctrict` varchar(100) NOT NULL,
  `workStart` int(11) NOT NULL,
  `workEnd` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `work_address`
--

INSERT INTO `work_address` (`workAddressId`, `workAddress`, `workCity`, `workDisctrict`, `workStart`, `workEnd`) VALUES
(1, 'Strostmajerova 12', 'Subotica', 'Serverno Backi', 123443, 124325);

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `errorlog`
--
ALTER TABLE `errorlog`
  ADD PRIMARY KEY (`errorLogId`);

--
-- A tábla indexei `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id_log`);

--
-- A tábla indexei `pet`
--
ALTER TABLE `pet`
  ADD PRIMARY KEY (`petId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `veterinarId` (`veterinarId`);

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productId`);

--
-- A tábla indexei `qr_code`
--
ALTER TABLE `qr_code`
  ADD PRIMARY KEY (`qr_code_id`),
  ADD KEY `userId` (`userId`);

--
-- A tábla indexei `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservationId`),
  ADD KEY `userId` (`veterinarianId`),
  ADD KEY `petId` (`petId`);

--
-- A tábla indexei `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`reviewId`),
  ADD KEY `userId` (`userId`);

--
-- A tábla indexei `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userId`);

--
-- A tábla indexei `user_product_relation`
--
ALTER TABLE `user_product_relation`
  ADD PRIMARY KEY (`userProductRelationId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `productId` (`productId`);

--
-- A tábla indexei `user_review_relation`
--
ALTER TABLE `user_review_relation`
  ADD PRIMARY KEY (`userReviewRelationId`),
  ADD KEY `userId` (`userId`),
  ADD KEY `reviewId` (`reviewId`);

--
-- A tábla indexei `veterinarian`
--
ALTER TABLE `veterinarian`
  ADD PRIMARY KEY (`veterinarianId`),
  ADD KEY `workAddressId` (`workAddressId`);

--
-- A tábla indexei `veterinar_review_relation`
--
ALTER TABLE `veterinar_review_relation`
  ADD PRIMARY KEY (`veterinarReviewRelationID`),
  ADD KEY `veterinarId` (`veterinarId`),
  ADD KEY `reviewId` (`reviewId`);

--
-- A tábla indexei `work_address`
--
ALTER TABLE `work_address`
  ADD PRIMARY KEY (`workAddressId`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `errorlog`
--
ALTER TABLE `errorlog`
  MODIFY `errorLogId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT a táblához `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT a táblához `pet`
--
ALTER TABLE `pet`
  MODIFY `petId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `productId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `qr_code`
--
ALTER TABLE `qr_code`
  MODIFY `qr_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT a táblához `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT a táblához `review`
--
ALTER TABLE `review`
  MODIFY `reviewId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT a táblához `user_product_relation`
--
ALTER TABLE `user_product_relation`
  MODIFY `userProductRelationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `user_review_relation`
--
ALTER TABLE `user_review_relation`
  MODIFY `userReviewRelationId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `veterinarian`
--
ALTER TABLE `veterinarian`
  MODIFY `veterinarianId` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `veterinar_review_relation`
--
ALTER TABLE `veterinar_review_relation`
  MODIFY `veterinarReviewRelationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `work_address`
--
ALTER TABLE `work_address`
  MODIFY `workAddressId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`),
  ADD CONSTRAINT `pet_ibfk_3` FOREIGN KEY (`veterinarId`) REFERENCES `veterinarian` (`veterinarianId`);

--
-- Megkötések a táblához `qr_code`
--
ALTER TABLE `qr_code`
  ADD CONSTRAINT `qr_code_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`);

--
-- Megkötések a táblához `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`petId`) REFERENCES `pet` (`petId`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`veterinarianId`) REFERENCES `veterinarian` (`veterinarianId`);

--
-- Megkötések a táblához `user_product_relation`
--
ALTER TABLE `user_product_relation`
  ADD CONSTRAINT `user_product_relation_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`),
  ADD CONSTRAINT `user_product_relation_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `product` (`productId`);

--
-- Megkötések a táblához `user_review_relation`
--
ALTER TABLE `user_review_relation`
  ADD CONSTRAINT `user_review_relation_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`),
  ADD CONSTRAINT `user_review_relation_ibfk_2` FOREIGN KEY (`reviewId`) REFERENCES `review` (`reviewId`);

--
-- Megkötések a táblához `veterinar_review_relation`
--
ALTER TABLE `veterinar_review_relation`
  ADD CONSTRAINT `veterinar_review_relation_ibfk_1` FOREIGN KEY (`reviewId`) REFERENCES `review` (`reviewId`),
  ADD CONSTRAINT `veterinar_review_relation_ibfk_2` FOREIGN KEY (`veterinarId`) REFERENCES `veterinarian` (`veterinarianId`);

--
-- Megkötések a táblához `work_address`
--
ALTER TABLE `work_address`
  ADD CONSTRAINT `work_address_ibfk_1` FOREIGN KEY (`workAddressId`) REFERENCES `veterinarian` (`workAddressId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
