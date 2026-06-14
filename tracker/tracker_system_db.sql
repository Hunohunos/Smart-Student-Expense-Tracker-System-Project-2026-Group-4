-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2026 at 04:34 PM
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
-- Database: `tracker_system_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id_categories` int(5) NOT NULL,
  `name_categories` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id_categories`, `name_categories`) VALUES
(1, 'Food'),
(2, 'Transport'),
(3, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id_expense` int(5) NOT NULL,
  `user_expense` int(5) NOT NULL,
  `name_expense` varchar(250) NOT NULL,
  `value_expense` int(250) NOT NULL,
  `date_expense` int(11) NOT NULL,
  `id_categories` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id_expense`, `user_expense`, `name_expense`, `value_expense`, `date_expense`, `id_categories`) VALUES
(1, 2, 'Food money', 430, 1778623200, 1),
(2, 2, 'Gas money', 15, 1778623200, 2),
(3, 2, 'Donation', 100, 1778623200, 3),
(5, 3, 'duit makan', 900, 1779314400, 1);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id_feedback` int(5) NOT NULL,
  `name_feedback` varchar(250) NOT NULL,
  `email_feedback` varchar(250) NOT NULL,
  `message_feedback` text NOT NULL,
  `date_feedback` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id_feedback`, `name_feedback`, `email_feedback`, `message_feedback`, `date_feedback`) VALUES
(1, 'jane', 'jane@example.com', 'Can\'t open budget page!!!', 1778665801),
(2, 'Ali', 'Ali@gmail.com', 'Cannot Account is lost!!', 1778682165),
(3, 'Maliki', 'maliki@gmail.com', 'Account hilang!!!', 1778722821),
(4, 'Jane doe', 'jane@example.com', 'forgot password!!', 1779329273);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(5) NOT NULL,
  `name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(100) NOT NULL,
  `user_types` text NOT NULL,
  `budget` decimal(10,2) NOT NULL DEFAULT 1000.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `name`, `email`, `password`, `user_types`, `budget`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$elMC2Dtg9ya0JQTj72fFYOOyhVjynLz719DD0GUEuf.ccIdLiSCqq', 'admin', 1000.00),
(2, 'Jane Doe', 'jane@example.com', '$2y$10$aombhmv7oVN4M7rtcRDf9.b1oM8EZDSb.I9SCkvPS4GdrR4Nl4aEa', 'user', 600.00),
(3, 'ali', 'ali@gmail.com', '$2y$10$vtea6E.aGGnNSV726R2XDeY.4pfwT8tykfuX4Mrn73cVe86V42MGO', 'user', 1000.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categories`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id_expense`),
  ADD KEY `id_categories` (`id_categories`),
  ADD KEY `user_expense` (`user_expense`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id_feedback`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categories` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id_expense` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id_feedback` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`id_categories`) REFERENCES `categories` (`id_categories`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`user_expense`) REFERENCES `user` (`id_user`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
