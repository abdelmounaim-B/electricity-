-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Feb 23, 2024 at 02:07 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gestion_factures`
--

-- --------------------------------------------------------

--
-- Table structure for table `consumption`
--

CREATE TABLE `consumption` (
  `consumption_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `date` date NOT NULL,
  `monthly_consumption` decimal(10,2) NOT NULL,
  `photo_url` text DEFAULT NULL,
  `rate_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consumption`
--

INSERT INTO `consumption` (`consumption_id`, `customer_id`, `date`, `monthly_consumption`, `photo_url`, `rate_id`) VALUES
(1, 1, '2024-01-15', 150.00, 'http://example.com/photos/january.jpg', NULL),
(2, 1, '2024-02-15', 120.00, 'http://example.com/photos/february.jpg', NULL),
(3, 2, '2024-01-16', 100.00, 'http://example.com/photos/january.jpg', NULL),
(4, 2, '2024-02-16', 110.00, 'http://example.com/photos/february.jpg', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `consumption`
--
ALTER TABLE `consumption`
  ADD PRIMARY KEY (`consumption_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `consumption_rate_fk` (`rate_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `consumption`
--
ALTER TABLE `consumption`
  MODIFY `consumption_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `consumption`
--
ALTER TABLE `consumption`
  ADD CONSTRAINT `consumption_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `consumption_rate_fk` FOREIGN KEY (`rate_id`) REFERENCES `rates` (`rate_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
