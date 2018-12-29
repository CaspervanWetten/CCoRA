-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 29, 2018 at 11:11 PM
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
-- Database: `cora`
--

-- --------------------------------------------------------

--
-- Table structure for table `petrinet`
--

CREATE TABLE `petrinet` (
  `id` int(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_on` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_element`
--

CREATE TABLE `petrinet_element` (
  `id` int(11) NOT NULL,
  `petrinet` int(11) NOT NULL,
  `type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_flow`
--

CREATE TABLE `petrinet_flow` (
  `id` int(11) NOT NULL,
  `petrinet` int(11) NOT NULL,
  `from_element` int(11) NOT NULL,
  `to_element` int(11) NOT NULL,
  `weight` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `petrinet_marking_pair`
--

CREATE TABLE `petrinet_marking_pair` (
  `id` int(11) NOT NULL,
  `petrinet` int(11) NOT NULL,
  `place` int(11) NOT NULL,
  `tokens` int(11) NOT NULL
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
  ADD KEY `petrinet_creator` (`creator`);

--
-- Indexes for table `petrinet_element`
--
ALTER TABLE `petrinet_element`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petrinet_petrinetElement` (`petrinet`);

--
-- Indexes for table `petrinet_flow`
--
ALTER TABLE `petrinet_flow`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petrinet_petrinetFlow` (`petrinet`),
  ADD KEY `petrinetFlow_from` (`from_element`),
  ADD KEY `petrinetFlow_to` (`to_element`);

--
-- Indexes for table `petrinet_marking_pair`
--
ALTER TABLE `petrinet_marking_pair`
  ADD PRIMARY KEY (`id`),
  ADD KEY `petrinet_markingPair` (`petrinet`),
  ADD KEY `markingPair_place` (`place`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `petrinet_element`
--
ALTER TABLE `petrinet_element`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1769;

--
-- AUTO_INCREMENT for table `petrinet_flow`
--
ALTER TABLE `petrinet_flow`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2146;

--
-- AUTO_INCREMENT for table `petrinet_marking_pair`
--
ALTER TABLE `petrinet_marking_pair`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=376;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=212;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `petrinet`
--
ALTER TABLE `petrinet`
  ADD CONSTRAINT `petrinet_creator` FOREIGN KEY (`creator`) REFERENCES `user` (`id`);

--
-- Constraints for table `petrinet_element`
--
ALTER TABLE `petrinet_element`
  ADD CONSTRAINT `petrinet_petrinetElement` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_flow`
--
ALTER TABLE `petrinet_flow`
  ADD CONSTRAINT `petrinetFlow_from` FOREIGN KEY (`from_element`) REFERENCES `petrinet_element` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `petrinetFlow_to` FOREIGN KEY (`to_element`) REFERENCES `petrinet_element` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `petrinet_petrinetFlow` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `petrinet_marking_pair`
--
ALTER TABLE `petrinet_marking_pair`
  ADD CONSTRAINT `markingPair_place` FOREIGN KEY (`place`) REFERENCES `petrinet_element` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `petrinet_markingPair` FOREIGN KEY (`petrinet`) REFERENCES `petrinet` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
