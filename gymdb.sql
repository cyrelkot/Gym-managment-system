-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2026 at 05:34 AM
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
-- Database: `gymdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbladdpackage`
--

CREATE TABLE `tbladdpackage` (
  `id` int(11) NOT NULL,
  `category` varchar(45) DEFAULT NULL,
  `titlename` varchar(450) DEFAULT NULL,
  `PackageDuration` varchar(45) DEFAULT NULL,
  `Price` varchar(45) DEFAULT NULL,
  `Description` varchar(450) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladdpackage`
--

INSERT INTO `tbladdpackage` (`id`, `category`, `titlename`, `PackageDuration`, `Price`, `Description`, `create_date`) VALUES
(1, '1', 'Beginner Strength', '1 Month', '599', 'Intro to gym with guided weight training 3x/week, access to all machines, and a starter nutrition guide.', '2026-05-16 06:25:15'),
(2, '1', 'Power Builder Pro', '3 Months', '2499', 'Full periodized strength program with personal trainer sessions, body composition tracking, and priority locker access.', '2026-05-16 06:25:15'),
(3, '2', 'Fat Burn Kickstart', '1 Month', '699', 'High-intensity interval training 4x/week targeting fat loss, includes diet plan and weekly weigh-in consultations.', '2026-05-16 06:25:15'),
(4, '2', '90-Day Body Transformation', '3 Months', '1999', 'Structured 12-week transformation combining HIIT, strength, and nutrition coaching with before/after progress photos.', '2026-05-16 06:25:15'),
(5, '3', 'Cardio Blast', '1 Month', '599', 'Daily treadmill, bike, and rowing programs with heart-rate zone training and unlimited group cardio classes.', '2026-05-16 06:25:15'),
(6, '3', 'Endurance Elite', '6 Months', '3999', 'Long-term endurance conditioning for runners and cyclists, includes VO2 max testing and custom training calendar.', '2026-05-16 06:25:15'),
(7, '4', 'Yoga & Flexibility', '1 Month', '799', 'Daily yoga sessions covering Hatha and Vinyasa styles, improve flexibility, balance, and mental focus.', '2026-05-16 06:25:15'),
(8, '4', 'Total Wellness Package', '6 Months', '4499', 'Comprehensive mind-body program with yoga, meditation, mobility drills, and monthly wellness assessments.', '2026-05-16 06:25:15');

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `id` int(11) NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`id`, `name`, `email`, `mobile`, `password`, `create_date`) VALUES
(1, 'admin', 'admin@gmail.com', '99197896857', '$2y$10$fV56mZhXshqV3xk/zXgSNuWCyldBkJ8aXw.tMkkYU/D05vBfH1xLq', '2022-01-19 11:25:17');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
--

CREATE TABLE `tblbooking` (
  `id` int(11) NOT NULL,
  `package_id` varchar(45) DEFAULT NULL,
  `userid` varchar(45) DEFAULT NULL,
  `booking_date` timestamp NULL DEFAULT current_timestamp(),
  `payment` varchar(45) DEFAULT NULL,
  `paymentType` varchar(45) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`id`, `package_id`, `userid`, `booking_date`, `payment`, `paymentType`) VALUES
(5, '1', '8', '2026-05-16 16:00:00', '599', 'Full Payment'),
(7, '5', '7', '2026-05-17 03:01:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcategory`
--

CREATE TABLE `tblcategory` (
  `id` int(11) NOT NULL,
  `category_name` varchar(45) DEFAULT NULL,
  `status` varchar(45) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcategory`
--

INSERT INTO `tblcategory` (`id`, `category_name`, `status`) VALUES
(1, 'Strength Training', '1'),
(2, 'Weight Loss', '1'),
(3, 'Cardio & Endurance', '1'),
(4, 'Yoga & Flexibility', '1');

-- --------------------------------------------------------

--
-- Table structure for table `tblpayment`
--

CREATE TABLE `tblpayment` (
  `id` int(11) NOT NULL,
  `bookingID` varchar(45) DEFAULT NULL,
  `paymentType` varchar(45) DEFAULT NULL,
  `payment` varchar(45) DEFAULT NULL,
  `payment_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpayment`
--

INSERT INTO `tblpayment` (`id`, `bookingID`, `paymentType`, `payment`, `payment_date`) VALUES
(12, '5', 'Full Payment', '599', '2026-05-16 16:16:35');

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `id` int(11) NOT NULL,
  `fname` varchar(45) DEFAULT NULL,
  `lname` varchar(45) DEFAULT NULL,
  `email` varchar(45) DEFAULT NULL,
  `mobile` varchar(45) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `state` varchar(45) DEFAULT NULL,
  `city` varchar(45) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `create_date` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`id`, `fname`, `lname`, `email`, `mobile`, `password`, `status`, `state`, `city`, `address`, `create_date`) VALUES
(7, 'angel ', 'yuki', 'angel@gmail.com', '09123456789', '$2y$10$WoveUy5bMRFhZ58q6prHqObKvZ4N3LYQ8kk8B1TmcJenbVEI3bN7u', 1, NULL, 'Ormoc', NULL, '2026-05-16 03:11:21'),
(8, 'yan', 'yan', 'yanyan@gmail.com', '12345678901', '$2y$10$c1rO8UZfwYAhAJTbmLO6u.TyDlvAPfa8ngVPMSawPbkkaWL7w5uW.', 1, NULL, 'Albuera', NULL, '2026-05-16 16:01:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbladdpackage`
--
ALTER TABLE `tbladdpackage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcategory`
--
ALTER TABLE `tblcategory`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpayment`
--
ALTER TABLE `tblpayment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbladdpackage`
--
ALTER TABLE `tbladdpackage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tblcategory`
--
ALTER TABLE `tblcategory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblpayment`
--
ALTER TABLE `tblpayment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
