-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 31, 2018 at 02:44 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cora_model_update_trunc`
--

-- --------------------------------------------------------

--
-- Table structure for table `petrinet`
--

CREATE TABLE `petrinet` (
  `id` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `creator` int(11) NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_flow_pt`
--

CREATE TABLE `petrinet_flow_pt` (
  `petrinet` int(11) NOT NULL,
  `from_element` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_element` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_flow_tp`
--

CREATE TABLE `petrinet_flow_tp` (
  `petrinet` int(11) NOT NULL,
  `from_element` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `to_element` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `weight` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_marking`
--

CREATE TABLE `petrinet_marking` (
  `petrinet` int(11) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_marking_pair`
--

CREATE TABLE `petrinet_marking_pair` (
  `marking` int(11) NOT NULL,
  `place` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokens` int(11) NOT NULL,
  `coordx` varchar(80) COLLATE utf8mb4_unicode_ci NULL,
  `coordy` varchar(80) COLLATE utf8mb4_unicode_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_place`
--

CREATE TABLE `petrinet_place` (
  `petrinet` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(80) COLLATE utf8mb4_unicode_ci NULL,
  `coordx` varchar(80) COLLATE utf8mb4_unicode_ci NULL,
  `coordy` varchar(80) COLLATE utf8mb4_unicode_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_transition`
--

CREATE TABLE `petrinet_transition` (
  `petrinet` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(80) COLLATE utf8mb4_unicode_ci NULL,
  `coordx` varchar(80) COLLATE utf8mb4_unicode_ci NULL,
  `coordy` varchar(80) COLLATE utf8mb4_unicode_ci NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `petrinet`
--
ALTER TABLE `petrinet`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator` (`creator`);

--
-- Indexes for table `petrinet_flow_pt`
--
ALTER TABLE `petrinet_flow_pt`
  ADD PRIMARY KEY (`petrinet`,`from_element`,`to_element`),
  ADD KEY `petrinet` (`petrinet`);

--
-- Indexes for table `petrinet_flow_tp`
--
ALTER TABLE `petrinet_flow_tp`
  ADD PRIMARY KEY (`petrinet`,`from_element`,`to_element`),
  ADD KEY `petrinet` (`petrinet`);

--
-- Indexes for table `petrinet_marking`
--
ALTER TABLE `petrinet_marking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petrinet` (`petrinet`),
  ADD KEY `petrinet_2` (`petrinet`);

--
-- Indexes for table `petrinet_marking_pair`
--
ALTER TABLE `petrinet_marking_pair`
  ADD PRIMARY KEY (`marking`,`place`);

--
-- Indexes for table `petrinet_place`
--
ALTER TABLE `petrinet_place`
  ADD PRIMARY KEY (`petrinet`,`name`);

--
-- Indexes for table `petrinet_transition`
--
ALTER TABLE `petrinet_transition`
  ADD PRIMARY KEY (`petrinet`,`name`),
  ADD KEY `petrinet` (`petrinet`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `petrinet`
--
ALTER TABLE `petrinet`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `petrinet_marking`
--
ALTER TABLE `petrinet_marking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `petrinet`
--
ALTER TABLE `petrinet`
  ADD CONSTRAINT `petrinet_creator` FOREIGN KEY (`creator`) REFERENCES `user` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_flow_pt`
--
ALTER TABLE `petrinet_flow_pt`
  ADD CONSTRAINT `petrinet_flow_pt` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_flow_tp`
--
ALTER TABLE `petrinet_flow_tp`
  ADD CONSTRAINT `petrinet_flow_tp` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_marking`
--
ALTER TABLE `petrinet_marking`
  ADD CONSTRAINT `petrinet_marking` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_marking_pair`
--
ALTER TABLE `petrinet_marking_pair`
  ADD CONSTRAINT `markingPair_marking` FOREIGN KEY (`marking`) REFERENCES `petrinet_marking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_place`
--
ALTER TABLE `petrinet_place`
  ADD CONSTRAINT `petrinet_place` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_transition`
--
ALTER TABLE `petrinet_transition`
  ADD CONSTRAINT `petrinet_transition` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
