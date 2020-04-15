-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Erstellungszeit: 15. Apr 2020 um 11:30
-- Server-Version: 10.1.16-MariaDB
-- PHP-Version: 5.6.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `korpus`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_document`
--

CREATE TABLE `tbl_document` (
  `d_id` int(11) NOT NULL,
  `d_token` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `d_step` int(11) NOT NULL,
  `d_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `d_date` date NOT NULL,
  `d_entry_creation_date` datetime NOT NULL,
  `d_entry_creation_user` int(11) NOT NULL,
  `d_document_type` int(11) NOT NULL,
  `d_document_lang` int(11) DEFAULT NULL,
  `d_document_tool` int(11) DEFAULT NULL,
  `d_document_tags` text COLLATE utf8mb4_unicode_ci,
  `d_author_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `d_author_age` int(11) DEFAULT NULL,
  `d_author_gender` int(11) DEFAULT NULL,
  `d_author_uni` int(11) DEFAULT NULL,
  `d_author_bama` int(11) DEFAULT NULL,
  `d_author_semesters` int(11) DEFAULT NULL,
  `d_author_subjects` int(11) DEFAULT NULL,
  `d_author_native_lang` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_document_type`
--

CREATE TABLE `tbl_document_type` (
  `dt_id` int(11) NOT NULL,
  `dt_name` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dt_shortname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_edittool`
--

CREATE TABLE `tbl_edittool` (
  `e_id` int(11) NOT NULL,
  `e_name` varchar(255) NOT NULL,
  `e_shortname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_gender`
--

CREATE TABLE `tbl_gender` (
  `g_id` int(11) NOT NULL,
  `g_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `g_shortname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_language`
--

CREATE TABLE `tbl_language` (
  `l_id` int(11) NOT NULL,
  `l_name` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `l_shortname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_university`
--

CREATE TABLE `tbl_university` (
  `u_id` int(11) NOT NULL,
  `u_name` varchar(255) NOT NULL,
  `u_shortname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_user`
--

CREATE TABLE `tbl_user` (
  `u_id` int(11) NOT NULL,
  `u_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `u_email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `u_password` text CHARACTER SET latin1 NOT NULL,
  `u_rights` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `tbl_document`
--
ALTER TABLE `tbl_document`
  ADD PRIMARY KEY (`d_id`),
  ADD KEY `d_document_type` (`d_document_type`),
  ADD KEY `d_author_gender` (`d_author_gender`),
  ADD KEY `d_document_lang` (`d_document_lang`),
  ADD KEY `d_author_native_lang` (`d_author_native_lang`),
  ADD KEY `d_entry_creation_user` (`d_entry_creation_user`) USING BTREE,
  ADD KEY `d_document_tool` (`d_document_tool`),
  ADD KEY `d_author_uni` (`d_author_uni`);

--
-- Indizes für die Tabelle `tbl_document_type`
--
ALTER TABLE `tbl_document_type`
  ADD PRIMARY KEY (`dt_id`);

--
-- Indizes für die Tabelle `tbl_edittool`
--
ALTER TABLE `tbl_edittool`
  ADD PRIMARY KEY (`e_id`);

--
-- Indizes für die Tabelle `tbl_gender`
--
ALTER TABLE `tbl_gender`
  ADD PRIMARY KEY (`g_id`);

--
-- Indizes für die Tabelle `tbl_language`
--
ALTER TABLE `tbl_language`
  ADD PRIMARY KEY (`l_id`);

--
-- Indizes für die Tabelle `tbl_university`
--
ALTER TABLE `tbl_university`
  ADD PRIMARY KEY (`u_id`);

--
-- Indizes für die Tabelle `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`u_id`),
  ADD UNIQUE KEY `u_email` (`u_email`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `tbl_document`
--
ALTER TABLE `tbl_document`
  MODIFY `d_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;
--
-- AUTO_INCREMENT für Tabelle `tbl_document_type`
--
ALTER TABLE `tbl_document_type`
  MODIFY `dt_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `tbl_edittool`
--
ALTER TABLE `tbl_edittool`
  MODIFY `e_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT für Tabelle `tbl_gender`
--
ALTER TABLE `tbl_gender`
  MODIFY `g_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT für Tabelle `tbl_language`
--
ALTER TABLE `tbl_language`
  MODIFY `l_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `tbl_university`
--
ALTER TABLE `tbl_university`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `u_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `tbl_document`
--
ALTER TABLE `tbl_document`
  ADD CONSTRAINT `tbl_document_ibfk_1` FOREIGN KEY (`d_document_type`) REFERENCES `tbl_document_type` (`dt_id`),
  ADD CONSTRAINT `tbl_document_ibfk_2` FOREIGN KEY (`d_document_lang`) REFERENCES `tbl_language` (`l_id`),
  ADD CONSTRAINT `tbl_document_ibfk_3` FOREIGN KEY (`d_author_gender`) REFERENCES `tbl_gender` (`g_id`),
  ADD CONSTRAINT `tbl_document_ibfk_4` FOREIGN KEY (`d_author_native_lang`) REFERENCES `tbl_language` (`l_id`),
  ADD CONSTRAINT `tbl_document_ibfk_5` FOREIGN KEY (`d_entry_creation_user`) REFERENCES `tbl_user` (`u_id`),
  ADD CONSTRAINT `tbl_document_ibfk_6` FOREIGN KEY (`d_author_uni`) REFERENCES `tbl_university` (`u_id`),
  ADD CONSTRAINT `tbl_document_ibfk_7` FOREIGN KEY (`d_document_tool`) REFERENCES `tbl_edittool` (`e_id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
