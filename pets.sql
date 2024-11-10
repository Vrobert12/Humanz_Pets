-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2024. Nov 09. 23:25
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
(97, 'file Upload', 'Unknown', 'Someone tried to upload a picture from a not valid page', '2024-11-09 22:24:05.000000');

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
  `qr_code_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `pet`
--

INSERT INTO `pet` (`petId`, `petName`, `bred`, `petSpecies`, `petPicture`, `userId`, `qr_code_id`) VALUES
(1, 'Buksi', 'Mixed', 'Dog', '20241109232135.png', 19, 1);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `product`
--

CREATE TABLE `product` (
  `productId` int(11) NOT NULL,
  `productName` varchar(100) NOT NULL,
  `productCost` decimal(6,2) NOT NULL,
  `productRelease` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `qr_code`
--

CREATE TABLE `qr_code` (
  `qr_code_id` int(11) NOT NULL,
  `qrCodeName` varchar(100) NOT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `qr_code`
--

INSERT INTO `qr_code` (`qr_code_id`, `qrCodeName`, `generated_at`, `updated_at`) VALUES
(1, 'QRcodes/qrcode_672fe06d0f506.png', '2024-11-09 22:21:35', '2024-11-09 22:21:35');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `reservation`
--

CREATE TABLE `reservation` (
  `reservationId` int(11) NOT NULL,
  `reservationDay` date DEFAULT NULL,
  `reservationTime` time DEFAULT NULL,
  `veterinarId` int(11) NOT NULL,
  `petId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `review`
--

CREATE TABLE `review` (
  `reviewId` int(11) NOT NULL,
  `review` int(11) NOT NULL,
  `reviewTime` datetime NOT NULL DEFAULT current_timestamp(),
  `userId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

CREATE TABLE `user` (
  `userId` int(6) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `phoneNumber` int(10) NOT NULL,
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
  `passwordValidationTime` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`userId`, `firstName`, `lastName`, `phoneNumber`, `userMail`, `userPassword`, `profilePic`, `privilage`, `registrationTime`, `verification_code`, `verify`, `verification_time`, `banned`, `banned_time`, `passwordValidation`, `passwordValidationTime`) VALUES
(6, 'Nikoletta', 'Varro', 0, 'nikolettavarro12@gmail.com', '$2y$10$ZJtAXGLi1y8Y7VlLzE4Ru.nH.SbV5pbDRtoQTlOv88WgemWiSIrB2', 'logInPic.png', 'Guest', '0000-00-00 00:00:00', 401081, 0, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 0, '2024-04-23 09:54:10'),
(7, 'Nikoletta', 'Varro', 0, 'nikolettavarro@gmail.com', '$2y$10$GZ9eslD9.lWIwuBi0by.sunJYqe1s8Jn8K2eX4CefmMN/LOnyRNua', 'logInPic.png', 'Guest', '0000-00-00 00:00:00', 102107, 0, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 0, '2024-04-23 09:54:10'),
(19, 'Róbert', 'Hupko', 649420637, 'robertvarro12@gmail.com', '$2y$10$cPLcJMsanfIKcT/RSF3rgO1zc/9JEbFgnD9YuEMZoFbNstohYDBha', '20241109222127.png', 'Admin', '0000-00-00 00:00:00', 229527, 1, '2024-04-29 22:00:00', 0, '0000-00-00 00:00:00', 136415, '2024-06-15 11:28:52'),
(25, 'Dominik', 'Hupko', 628277140, 'hupkodominik143@gmail.com', '$2y$10$2GtJU92kqTP4FioPdkS0WuXGDZMTqQwpEuDToq9suKr3T7XP37EEm', 'logInPic.png', 'Guest', '2024-06-03 12:23:54', 2047970, 1, '2024-06-03 12:33:54', 0, '0000-00-00 00:00:00', NULL, '2024-06-03 12:23:54'),
(26, 'Robert', 'Hupko', 649420637, 'varorobert03@gmail.com', '$2y$10$vVRKD1BHdxuVyK2Q1yAFiutrZafKmdOPO5HD1GhEctq8BgxUuQoa.', 'logInPic.png', 'Guest', '2024-06-12 16:21:21', 3652391, 1, '2024-06-12 16:31:21', 0, '2024-06-12 16:21:22', NULL, '2024-06-12 16:21:22'),
(27, 'Robert', 'Varro', 649420637, 'vaorobert03@gmail.com', '$2y$10$WLNUBEgiMnWyRdJYBqrQXOUu3kKCaJlkirZYbg.u4.UNkFMavtedi', '20240615204340.avif', 'Worker', '2024-06-15 11:30:27', 9844306, 1, '2024-06-15 11:40:27', 0, '0000-00-00 00:00:00', NULL, '2024-06-15 11:30:27'),
(28, 'Robert', 'Varro', 649420637, 'vadrrorobert03@gmail.com', '$2y$10$6TqYvJ6B6htGzaHUzOA/SuZwEnoXELGaDRKtU2HCkiqPvDWRN5h4u', 'logInPic.png', 'Worker', '2024-06-15 19:59:50', 1302342, 1, '2024-06-15 20:09:50', 0, '2024-06-15 19:59:50', NULL, '2024-06-15 19:59:50'),
(29, 'Robert', 'Varro', 649420637, 'varrosrobert03@gmail.com', '$2y$10$/g5cMYoHnlvFQzuzCPLE/e2Dp1QG3d3cEDooOOu66Cy64Uaw/2uue', 'logInPic.png', 'Guest', '2024-06-19 10:25:41', 2120816, 1, '2024-06-19 10:35:41', 0, '2024-06-19 10:25:41', NULL, '2024-06-19 10:25:41'),
(30, 'Robert', 'Varro', 189420637, 'varrorobert03@gmail.com', '$2y$10$O.HokAcRDr1KVtBkK0z7aOBdtOSi/LQ5Cz.gbTaSENxHWJYPilHEu', 'logInPic.png', 'Guest', '2024-06-20 20:28:11', 4798809, 1, '2024-06-20 20:38:11', 0, '2024-06-20 20:28:11', NULL, '2024-06-20 20:28:11');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user_product_relation`
--

CREATE TABLE `user_product_relation` (
  `userProductRelationId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `productId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `phoneNumber` int(10) NOT NULL,
  `veterinarianMail` varchar(100) NOT NULL,
  `veterinarianPassword` varchar(60) NOT NULL,
  `profilePic` varchar(100) DEFAULT NULL,
  `registrationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` int(50) DEFAULT NULL,
  `verify` int(11) NOT NULL,
  `verification_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `passwordValidation` int(10) DEFAULT NULL,
  `passwordValidationTime` timestamp NOT NULL DEFAULT current_timestamp(),
  `workAddressId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  ADD KEY `qr_code_id` (`qr_code_id`),
  ADD KEY `userId` (`userId`);

--
-- A tábla indexei `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`productId`);

--
-- A tábla indexei `qr_code`
--
ALTER TABLE `qr_code`
  ADD PRIMARY KEY (`qr_code_id`);

--
-- A tábla indexei `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservationId`),
  ADD KEY `userId` (`veterinarId`),
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
  MODIFY `errorLogId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT a táblához `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT a táblához `pet`
--
ALTER TABLE `pet`
  MODIFY `petId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `product`
--
ALTER TABLE `product`
  MODIFY `productId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `qr_code`
--
ALTER TABLE `qr_code`
  MODIFY `qr_code_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT a táblához `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservationId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `userId` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT a táblához `user_product_relation`
--
ALTER TABLE `user_product_relation`
  MODIFY `userProductRelationId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `user_review_relation`
--
ALTER TABLE `user_review_relation`
  MODIFY `userReviewRelationId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `veterinarian`
--
ALTER TABLE `veterinarian`
  MODIFY `veterinarianId` int(6) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `veterinar_review_relation`
--
ALTER TABLE `veterinar_review_relation`
  MODIFY `veterinarReviewRelationID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `work_address`
--
ALTER TABLE `work_address`
  MODIFY `workAddressId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Megkötések a kiírt táblákhoz
--

--
-- Megkötések a táblához `pet`
--
ALTER TABLE `pet`
  ADD CONSTRAINT `pet_ibfk_1` FOREIGN KEY (`qr_code_id`) REFERENCES `qr_code` (`qr_code_id`),
  ADD CONSTRAINT `pet_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`userId`);

--
-- Megkötések a táblához `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`petId`) REFERENCES `pet` (`petId`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`veterinarId`) REFERENCES `veterinarian` (`veterinarianId`);

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
