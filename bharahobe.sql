-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 24, 2025 at 10:50 AM
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
-- Database: `bharahobe`
--

-- --------------------------------------------------------

--
-- Table structure for table `apartment`
--

CREATE TABLE `apartment` (
  `advert_id` int(10) UNSIGNED NOT NULL,
  `no_of_bed` int(10) UNSIGNED NOT NULL,
  `no_of_wash` int(10) UNSIGNED NOT NULL,
  `available_from` date NOT NULL,
  `PO_District` varchar(30) NOT NULL,
  `Road` varchar(100) NOT NULL,
  `House_no` varchar(20) NOT NULL,
  `rent` int(10) UNSIGNED NOT NULL,
  `area` int(10) UNSIGNED DEFAULT NULL,
  `apt_type` enum('Sublet','Full Unit') DEFAULT NULL,
  `description` text NOT NULL,
  `renter_username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `username` varchar(50) NOT NULL,
  `events` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `advert_id` int(10) UNSIGNED NOT NULL,
  `images` longblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `request_apply`
--

CREATE TABLE `request_apply` (
  `advert_id` int(11) UNSIGNED NOT NULL,
  `renter_username` varchar(50) NOT NULL,
  `tenant_username` varchar(50) NOT NULL,
  `status` enum('Pending','Accepted','Rejected') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `tour_id` int(10) UNSIGNED NOT NULL,
  `review` text NOT NULL,
  `rating` enum('0','1','2','3','4','5') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scheduled_tour`
--

CREATE TABLE `scheduled_tour` (
  `tour_id` int(10) UNSIGNED NOT NULL,
  `advert_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tour`
--

CREATE TABLE `tour` (
  `tour_id` int(10) UNSIGNED NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('Pending','Completed') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `NID` int(20) UNSIGNED NOT NULL,
  `date_of_birth` date NOT NULL,
  `PO_District` varchar(30) NOT NULL,
  `Road` varchar(100) NOT NULL,
  `House_no` varchar(20) NOT NULL,
  `Phone_no` int(20) UNSIGNED NOT NULL,
  `Email` varchar(50) NOT NULL,
  `ten_flag` tinyint(1) NOT NULL,
  `ten_type` enum('Bachelor','Family') DEFAULT NULL,
  `ren_flag` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `apartment`
--
ALTER TABLE `apartment`
  ADD PRIMARY KEY (`advert_id`),
  ADD KEY `renter_username` (`renter_username`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`advert_id`);

--
-- Indexes for table `request_apply`
--
ALTER TABLE `request_apply`
  ADD PRIMARY KEY (`advert_id`,`renter_username`,`tenant_username`),
  ADD KEY `renter_username` (`renter_username`),
  ADD KEY `tenant_username` (`tenant_username`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`tour_id`);

--
-- Indexes for table `scheduled_tour`
--
ALTER TABLE `scheduled_tour`
  ADD PRIMARY KEY (`tour_id`,`advert_id`),
  ADD KEY `scheduled_tour_ibfk_2` (`advert_id`);

--
-- Indexes for table `tour`
--
ALTER TABLE `tour`
  ADD PRIMARY KEY (`tour_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`username`),
  ADD UNIQUE KEY `NID` (`NID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `apartment`
--
ALTER TABLE `apartment`
  MODIFY `advert_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `tour_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scheduled_tour`
--
ALTER TABLE `scheduled_tour`
  MODIFY `tour_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `tour_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `apartment`
--
ALTER TABLE `apartment`
  ADD CONSTRAINT `apartment_ibfk_1` FOREIGN KEY (`renter_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `history_ibfk_1` FOREIGN KEY (`username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`advert_id`) REFERENCES `apartment` (`advert_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `request_apply`
--
ALTER TABLE `request_apply`
  ADD CONSTRAINT `request_apply_ibfk_1` FOREIGN KEY (`advert_id`) REFERENCES `apartment` (`advert_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_apply_ibfk_2` FOREIGN KEY (`renter_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `request_apply_ibfk_3` FOREIGN KEY (`tenant_username`) REFERENCES `users` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `scheduled_tour` (`tour_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `scheduled_tour`
--
ALTER TABLE `scheduled_tour`
  ADD CONSTRAINT `scheduled_tour_ibfk_2` FOREIGN KEY (`advert_id`) REFERENCES `request_apply` (`advert_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tour`
--
ALTER TABLE `tour`
  ADD CONSTRAINT `tour_ibfk_1` FOREIGN KEY (`tour_id`) REFERENCES `scheduled_tour` (`tour_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
