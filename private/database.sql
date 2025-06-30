-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 10.10.11.21
-- Generation Time: Jun 27, 2025 at 12:05 PM
-- Server version: 11.4.5-MariaDB-log
-- PHP Version: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Stemwijzer_database`
--
CREATE DATABASE IF NOT EXISTS `Stemwijzer_database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `Stemwijzer_database`;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL,
  `nieuws_id` int(11) NOT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`comment_id`, `nieuws_id`, `gebruiker_id`, `comment_text`, `created_at`, `updated_at`) VALUES
(21, 1, 1, 'Geweldig nieuws, ik was hierop aan het wachten!', '2025-06-02 07:30:00', '2025-06-26 08:00:25'),
(23, 1, 4, 'Typisch voorbeeld van slechte journalistiek.', '2025-06-04 12:10:00', '2025-06-04 12:10:00'),
(25, 2, 5, 'Erg nuttig artikel, bedankt!', '2025-06-05 08:20:00', '2025-06-05 08:20:00'),
(26, 1, 1, 'Waarom wordt dit nu pas besproken?', '2025-06-06 10:00:00', '2025-06-26 08:00:28'),
(27, 1, 5, 'Helemaal mee eens, dit moest gezegd worden.', '2025-06-07 14:30:00', '2025-06-07 14:35:00'),
(39, 2, 1, 'wereldnieuws?', '2025-06-13 14:17:46', '2025-06-26 08:00:29'),
(50, 2, 7, 'Ik ben stoer. Ook klein natuurlijk :) gr stan', '2025-06-16 10:12:45', '2025-06-25 10:14:15'),
(51, 2, 7, 'ik ben stan', '2025-06-16 10:18:18', '2025-06-19 08:09:28'),
(53, 2, 8, 'oke dat werkt :P', '2025-06-19 06:03:53', '2025-06-19 06:03:53'),
(57, 1, 1, 'test\r\n\r\n\r\n123\r\n\r\n\r\n\r\n\r\n\r\ntest', '2025-06-19 08:30:43', '2025-06-26 08:00:32'),
(58, 2, 8, 'HOLY FUCK AMONG US', '2025-06-19 06:33:58', '2025-06-19 06:33:58'),
(59, 2, 8, 'Oh ja politiek...', '2025-06-19 06:35:46', '2025-06-19 06:35:46'),
(61, 2, 8, 'VUILE FASCISTEN!!!!!', '2025-06-19 06:36:26', '2025-06-19 06:36:26'),
(65, 2, 1, 'tes\r\n\r\n\r\n\r\n\r\na', '2025-06-19 10:17:31', '2025-06-26 08:00:34'),
(66, 2, 10, 'huhhhh', '2025-06-23 12:17:03', '2025-06-23 12:17:03'),
(67, 2, 1, 'hallo', '2025-06-26 08:40:44', '2025-06-26 08:40:44'),
(70, 2, 1, '@DaanBanaan heyyy daan', '2025-06-26 09:59:01', '2025-06-26 09:59:01'),
(71, 2, 8, '@DaintyDust heyyy nickk', '2025-06-26 09:59:47', '2025-06-26 09:59:59'),
(72, 2, 15, 'hallo', '2025-06-26 10:32:48', '2025-06-26 10:32:48'),
(73, 2, 15, '@DaintyDust hallo\r\n\r\ntest', '2025-06-26 10:33:07', '2025-06-26 10:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `gebruikers`
--

CREATE TABLE `gebruikers` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wachtwoord_hash` varchar(255) NOT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_verloopdatum` timestamp NULL DEFAULT NULL,
  `rol` enum('gebruiker','beheerder') DEFAULT 'gebruiker',
  `profielfoto` varchar(8000) DEFAULT NULL,
  `aangemaakt_op` timestamp NULL DEFAULT current_timestamp(),
  `uitslag` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gebruikers`
--

INSERT INTO `gebruikers` (`id`, `naam`, `email`, `wachtwoord_hash`, `reset_token`, `reset_verloopdatum`, `rol`, `profielfoto`, `aangemaakt_op`, `uitslag`) VALUES
(1, 'DaintyDust', 'testing@school.nl', '$2y$10$yYzwplhyfmvwIDpbB8uhUOsyQ8XnhXvmfIsycsl4ekzpDJR/GMN6y', 'ded23d9bdc9d511333f091194d32214cac97b99533b0638e8e32f09caefcd488', '2025-06-25 12:13:40', 'beheerder', 'alan walker achtergrond.jpg', '2025-06-02 13:51:07', 'PVV 13%, CDA 13%, D66 13%, SP 13%, FVD 12%, VVD 12%, GroenLinks 12%, GroenLinks 12%'),
(4, 'test', 'tester@school.nl', '$2y$10$mJa6tw5xXYUDpyuJGriVned4pXMiM6NSBKfn95qKtInmubVp/YBKu', NULL, NULL, 'gebruiker', 'https://example.com/profiel1.jpg', '2025-06-02 14:21:58', NULL),
(5, 'Admin', 'Admin@school.nl', '$2y$10$CTb5HmgUOLZ2bkWW1IdXdedmGup21R7BXV2toFibleMfkrC9LQteC', NULL, NULL, 'beheerder', 'https://example.com/profiel1.jpg', '2025-06-02 16:22:10', NULL),
(8, 'DaanBanaan', '123@hotmail.com', '$2y$12$82XfgKltkCi4ZYuDM10WnebGlbMDrt0yqCOn0n38VtSvvn.yFj4tS', NULL, NULL, 'beheerder', 'https://example.com', '2025-06-19 05:19:59', 'FVD 14%, VVD 14%, GroenLinks 14%, GroenLinks 14%, PVV 11%, CDA 11%, D66 11%, SP 11%'),
(10, 'DaintyDust2', 'a@a.com', '$2y$10$yYzwplhyfmvwIDpbB8uhUOsyQ8XnhXvmfIsycsl4ekzpDJR/GMN6y', NULL, NULL, 'gebruiker', 'Nick_Studios_Logo_8.png', '2025-06-23 12:16:28', NULL),
(12, 'ik test', 'iktest@hotmail.com', '09876', NULL, NULL, 'beheerder', '', '2025-06-24 07:46:02', ''),
(13, 'daanvandesande2', 'test@hotmail.com', '$2y$10$ajS/lzmHPvcN5YSnu4mcm.U/9ufUOGkWntQA6DLOCwNsOoHA2vDjW', NULL, NULL, 'beheerder', 'https://example.com/profiel1.jpg', '2025-06-24 19:56:01', 'FVD 14%, VVD 14%, GroenLinks 14%, GroenLinks 14%, PVV 11%, CDA 11%, D66 11%, SP 11%'),
(14, 'TestDaanBanaanthe', 'test@gmail.com', '$2y$12$Owrmr9VUXta2bfT8QJV4P.s80f45W43o2K3E8Kn2k0CzKU2WPLnca', NULL, NULL, 'gebruiker', 'https://example.com/profiel1.jpg', '2025-06-25 08:30:53', 'PVV 13%, FVD 13%, CDA 13%, VVD 13%, D66 13%, GroenLinks 13%, SP 13%, SP 13%'),
(15, 'Piet', 'piet@hotmail.com', 'test', NULL, NULL, 'beheerder', 'https://example.com/profiel1.jpg', '2025-06-26 10:29:03', 'PVV 14%, CDA 14%, D66 14%, SP 14%, FVD 11%, VVD 11%, GroenLinks 11%, GroenLinks 11%');

-- --------------------------------------------------------

--
-- Table structure for table `nieuws`
--

CREATE TABLE `nieuws` (
  `id` int(11) NOT NULL,
  `titel` varchar(255) NOT NULL,
  `inhoud` text NOT NULL,
  `afbeelding` varchar(8000) DEFAULT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `aangemaakt_op` timestamp NULL DEFAULT current_timestamp(),
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nieuws`
--

INSERT INTO `nieuws` (`id`, `titel`, `inhoud`, `afbeelding`, `auteur_id`, `aangemaakt_op`, `status`) VALUES
(1, 'Stemmen gestart in Nederland', 'het verkiezingen zijn vandaag van start gegaan in het hele land.', 'https://example.com/verkiezing.jpg', 1, '2025-05-22 10:11:52', 1),
(2, 'GroenLinks stijgt in de peilingen', 'Donec sodales sagittis magna. Sed consequat, leo eget bibendum sodales, augue velit cursus nunc,', 'https://example.com/peiling.jpg', 8, '1995-05-10 10:11:52', 1),
(3, 'Titel', 'Humor met inhoud', NULL, 3, '2025-06-22 14:30:20', 1),
(6, 'ik test', 'werk jij', 'https://example.com/idk.png', 8, '2025-06-24 07:46:45', 1),
(7, 'doe jij het nu', 'popop', NULL, 8, '2025-06-24 07:47:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `partijen`
--

CREATE TABLE `partijen` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `afkorting` varchar(10) DEFAULT NULL,
  `beschrijving` text DEFAULT NULL,
  `afbeelding` varchar(8000) DEFAULT NULL,
  `PositiePartij` varchar(100) DEFAULT NULL COMMENT 'Links Of Rechts?',
  `type_partij` varchar(100) DEFAULT NULL,
  `partij_leider` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partijen`
--

INSERT INTO `partijen` (`id`, `naam`, `afkorting`, `beschrijving`, `afbeelding`, `PositiePartij`, `type_partij`, `partij_leider`) VALUES
(1, 'PVV', 'PVV', 'De Partij voor de Vrijheid. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/pvv-banner-1.jpg,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/pvv-banner-2.webp', 'summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/pvv.jpg', 'Rechts', 'Organisatie', 'Geert Wilders'),
(3, 'CDA', 'CDA', 'Christen-Democratisch Appèl. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/cda-banner-1.jpg,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/cda-banner-2.jpg', 'summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/cda.jpg', NULL, NULL, 'Henri Bontenbal'),
(4, 'VVD', 'VVD', 'Volkspartij voor Vrijheid en Democratie. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/vvd-banner-1.webp,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/vvd-banner-2.png', 'summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/vvd.png', NULL, NULL, 'Dilan Yeşilgöz'),
(5, 'D66', 'D66', 'Democraten 66. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/d66-banner-1.webp,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/d66-banner-2.webp', 'summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/d66.png', NULL, NULL, 'Rob jetten'),
(6, 'GroenLinks', 'GL', 'GroenLinks, partij voor duurzaamheid en gelijkheid. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/frans-timmermans.jpg,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/groenlinks-banner.jpg', 'upload.wikimedia.org/wikipedia/commons/thumb/4/47/PNG_transparency_demonstration_1.png/200px-PNG_transparency_demonstration_1.png', '', 'Organisatie', 'Jesse klaver'),
(7, 'SP', 'SP', 'Socialistische Partij. Banner afbeeldingen: summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/SP.jpg,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/banners/JSV_7229.avif', 'summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/sp.png,summacollege.daanvandesande.nl/stemwijzer/afbeeldingen/logos/sp-tomaat.png', NULL, NULL, 'Lilian Marijnissen'),
(10, 'YUP', 'YU', 'hfhdhhdd', '', 'Links', 'Commercieel', 'IK');

-- --------------------------------------------------------

--
-- Table structure for table `partijen_info`
--

CREATE TABLE `partijen_info` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `logo` longblob DEFAULT NULL,
  `informatie` text DEFAULT NULL,
  `type_partij` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partij_stellingen`
--

CREATE TABLE `partij_stellingen` (
  `id` int(11) NOT NULL,
  `partij_id` int(11) NOT NULL,
  `stelling_text` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `partij_verkiezing`
--

CREATE TABLE `partij_verkiezing` (
  `id` int(11) NOT NULL,
  `partij_id` int(11) NOT NULL,
  `verkiezing_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partij_verkiezing`
--

INSERT INTO `partij_verkiezing` (`id`, `partij_id`, `verkiezing_id`) VALUES
(1, 1, 1),
(3, 3, 1),
(4, 4, 1),
(5, 5, 1),
(6, 6, 1),
(7, 7, 1);

-- --------------------------------------------------------

--
-- Table structure for table `stellingen`
--

CREATE TABLE `stellingen` (
  `id` int(11) NOT NULL,
  `thema_id` int(11) DEFAULT NULL,
  `tekst` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stellingen`
--

INSERT INTO `stellingen` (`id`, `thema_id`, `tekst`) VALUES
(1, 3, 'Nederland moet sneller overschakelen op groene energie.'),
(2, 4, 'Er moet meer politie op straat komen.'),
(3, 6, 'Gratis openbaar vervoer voor studenten en 65-plussers'),
(4, 3, 'Verbod op fossiele subsidies vóór 2030'),
(5, 6, 'Basisinkomen-experimenten in 5 gemeenten starten'),
(6, 6, 'Verplichte loontransparantie voor bedrijven met >50 werknemers'),
(7, 4, 'Legalisering en regulering van softdrugsverkoop'),
(8, 7, '30% sociale woningbouw bij alle nieuwbouwprojecten'),
(9, 1, 'Gratis kinderopvang voor werkende ouders'),
(10, 2, 'Stemrecht vanaf 16 jaar'),
(11, 6, 'Vrijstelling erfbelasting voor kleine familiebedrijven'),
(12, 8, 'Introductie ‘burgerjury’ bij grote infrastructurele projecten'),
(13, 2, 'Volledige afschaffing van flexwerkcontracten in het onderwijs'),
(14, 3, 'Belastingkorting voor circulaire bedrijven'),
(15, 1, 'Loonverhoging van 10% voor zorgpersoneel'),
(16, 6, 'Vrijstelling erfbelasting voor kleine familiebedrijven'),
(17, 3, 'Nachtvluchten op Schiphol volledig afschaffen'),
(18, 6, 'Werknemers krijgen stemrecht in raden van bestuur'),
(19, 6, 'Eén loket voor alle overheidsdiensten met AI-ondersteuning'),
(20, 1, 'Gratis ov-kaart voor mantelzorgers');

-- --------------------------------------------------------

--
-- Table structure for table `stemmen`
--

CREATE TABLE `stemmen` (
  `id` int(11) NOT NULL,
  `gebruiker_id` int(11) NOT NULL,
  `partij_id` int(11) NOT NULL,
  `verkiezing_id` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `thema`
--

CREATE TABLE `thema` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `thema`
--

INSERT INTO `thema` (`id`, `naam`) VALUES
(1, 'Zorg (bijv. ziekenhuizen, ouderenzorg, zorgverzekeringen)'),
(2, 'Onderwijs (bijv. schoolbudgetten, lesmethoden, onderwijskwaliteit)'),
(3, 'Klimaat en milieu (bijv. CO₂-uitstoot, stikstof, windmolens)'),
(4, 'Veiligheid (bijv. politie, criminaliteit, terrorismebestrijding)'),
(5, 'Migratie en integratie (bijv. asielzoekers, verblijfsvergunningen)'),
(6, 'Economie (bijv. belastingen, minimumloon, uitkeringen)'),
(7, 'Woningmarkt (bijv. sociale huur, woningnood)'),
(8, 'Internationale relaties (bijv. EU, NAVO, ontwikkelingshulp)');

-- --------------------------------------------------------

--
-- Table structure for table `verkiezingen`
--

CREATE TABLE `verkiezingen` (
  `id` int(11) NOT NULL,
  `naam` varchar(255) NOT NULL,
  `startdatum` date DEFAULT NULL,
  `einddatum` date DEFAULT NULL,
  `verkiezing_beschrijving` longtext NOT NULL COMMENT 'De beschrijving van een verkiezing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `verkiezingen`
--

INSERT INTO `verkiezingen` (`id`, `naam`, `startdatum`, `einddatum`, `verkiezing_beschrijving`) VALUES
(1, 'Gemeenteraadsverkiezing 2025', '2025-03-01', '2025-03-15', '');

-- --------------------------------------------------------

--
-- Table structure for table `verkiezing_stellingen`
--

CREATE TABLE `verkiezing_stellingen` (
  `verkiezing_id` int(11) NOT NULL,
  `stelling_id` int(11) NOT NULL,
  `volgorde` int(11) DEFAULT NULL,
  `actief` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`comment_id`),
  ADD KEY `idx_nieuws_comments` (`nieuws_id`),
  ADD KEY `idx_user_comments` (`gebruiker_id`);

--
-- Indexes for table `gebruikers`
--
ALTER TABLE `gebruikers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `nieuws`
--
ALTER TABLE `nieuws`
  ADD PRIMARY KEY (`id`),
  ADD KEY `auteur_id` (`auteur_id`);

--
-- Indexes for table `partijen`
--
ALTER TABLE `partijen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partijen_info`
--
ALTER TABLE `partijen_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `partij_stellingen`
--
ALTER TABLE `partij_stellingen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_partij_stellingen_partij` (`partij_id`);

--
-- Indexes for table `partij_verkiezing`
--
ALTER TABLE `partij_verkiezing`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_partij_verkiezing` (`partij_id`,`verkiezing_id`),
  ADD KEY `verkiezing_id` (`verkiezing_id`);

--
-- Indexes for table `stellingen`
--
ALTER TABLE `stellingen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `thema_id` (`thema_id`);

--
-- Indexes for table `stemmen`
--
ALTER TABLE `stemmen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gebruiker_id` (`gebruiker_id`),
  ADD KEY `partij_id` (`partij_id`),
  ADD KEY `verkiezing_id` (`verkiezing_id`);

--
-- Indexes for table `thema`
--
ALTER TABLE `thema`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verkiezingen`
--
ALTER TABLE `verkiezingen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verkiezing_stellingen`
--
ALTER TABLE `verkiezing_stellingen`
  ADD PRIMARY KEY (`verkiezing_id`,`stelling_id`),
  ADD KEY `idx_verkiezing_stellingen_verkiezing` (`verkiezing_id`),
  ADD KEY `idx_verkiezing_stellingen_stelling` (`stelling_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `gebruikers`
--
ALTER TABLE `gebruikers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `nieuws`
--
ALTER TABLE `nieuws`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `partijen`
--
ALTER TABLE `partijen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `partijen_info`
--
ALTER TABLE `partijen_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partij_stellingen`
--
ALTER TABLE `partij_stellingen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `partij_verkiezing`
--
ALTER TABLE `partij_verkiezing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `stellingen`
--
ALTER TABLE `stellingen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `stemmen`
--
ALTER TABLE `stemmen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `thema`
--
ALTER TABLE `thema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `verkiezingen`
--
ALTER TABLE `verkiezingen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`nieuws_id`) REFERENCES `nieuws` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`);

--
-- Constraints for table `nieuws`
--
ALTER TABLE `nieuws`
  ADD CONSTRAINT `nieuws_ibfk_1` FOREIGN KEY (`auteur_id`) REFERENCES `gebruikers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `partij_stellingen`
--
ALTER TABLE `partij_stellingen`
  ADD CONSTRAINT `partij_stellingen_ibfk_1` FOREIGN KEY (`partij_id`) REFERENCES `partijen` (`id`);

--
-- Constraints for table `partij_verkiezing`
--
ALTER TABLE `partij_verkiezing`
  ADD CONSTRAINT `partij_verkiezing_ibfk_1` FOREIGN KEY (`partij_id`) REFERENCES `partijen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `partij_verkiezing_ibfk_2` FOREIGN KEY (`verkiezing_id`) REFERENCES `verkiezingen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stellingen`
--
ALTER TABLE `stellingen`
  ADD CONSTRAINT `stellingen_ibfk_1` FOREIGN KEY (`thema_id`) REFERENCES `thema` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stemmen`
--
ALTER TABLE `stemmen`
  ADD CONSTRAINT `stemmen_ibfk_1` FOREIGN KEY (`gebruiker_id`) REFERENCES `gebruikers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stemmen_ibfk_2` FOREIGN KEY (`partij_id`) REFERENCES `partijen` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stemmen_ibfk_3` FOREIGN KEY (`verkiezing_id`) REFERENCES `verkiezingen` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `verkiezing_stellingen`
--
ALTER TABLE `verkiezing_stellingen`
  ADD CONSTRAINT `verkiezing_stellingen_ibfk_1` FOREIGN KEY (`verkiezing_id`) REFERENCES `verkiezingen` (`id`),
  ADD CONSTRAINT `verkiezing_stellingen_ibfk_2` FOREIGN KEY (`stelling_id`) REFERENCES `stellingen` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
