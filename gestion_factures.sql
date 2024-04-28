-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3308
-- Generation Time: Mar 10, 2024 at 10:21 PM
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
-- Table structure for table `annual_consumption_files`
--

CREATE TABLE `annual_consumption_files` (
  `file_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `year` year(4) NOT NULL,
  `total_consumption` decimal(10,2) NOT NULL,
  `file_path` text NOT NULL,
  `insertion_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annual_consumption_files`
--

INSERT INTO `annual_consumption_files` (`file_id`, `customer_id`, `year`, `total_consumption`, `file_path`, `insertion_date`) VALUES
(12, 1, '2023', 1500.00, '../../public/images/Consommation_annuelle.txt', '2024-03-10 21:10:56'),
(13, 2, '2023', 1200.00, '../../public/images/Consommation_annuelle.txt', '2024-03-10 21:10:56'),
(14, 18, '2024', 120000.00, '../../public/images/Consommation_annuelle.txt', '2024-03-10 21:10:56');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `bill_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `amount_ht` decimal(10,2) NOT NULL,
  `amount_ttc` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `declaration_date` datetime NOT NULL,
  `monthly_consumption` decimal(10,2) NOT NULL,
  `photo_url` text DEFAULT NULL,
  `rate_id` int(11) DEFAULT NULL,
  `validation_status` enum('pending_validation','validated','invalid') DEFAULT 'pending_validation',
  `invalid_cause` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`bill_id`, `issue_date`, `due_date`, `amount_ht`, `amount_ttc`, `status`, `customer_id`, `declaration_date`, `monthly_consumption`, `photo_url`, `rate_id`, `validation_status`, `invalid_cause`) VALUES
(85, '2024-01-01', '2024-02-01', 80.00, 91.20, 'unpaid', 1, '2024-03-10 21:19:41', 100.00, 'photo_65ee15ddd4c133.42270416.jpg', 1, 'validated', NULL),
(86, '2024-02-01', '2024-03-01', 400.00, 456.00, 'paid', 1, '2024-03-10 21:20:36', 400.00, 'photo_65ee161430dff7.95828962.jpg', 3, 'validated', NULL),
(89, '2024-03-01', '2024-04-01', 2500.00, 2850.00, 'unpaid', 1, '2024-03-10 21:27:06', 2500.00, 'photo_65ee179a43dae9.86647755.jpg', 3, 'validated', 'Invalid consumption calculated: 2500 kWh.'),
(90, '2024-01-01', '2024-02-01', 110.70, 126.20, 'unpaid', 18, '2024-03-10 21:48:56', 123.00, 'photo_65ee1cb85c9b28.70113050.jpg', 2, 'validated', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `profile` text DEFAULT 'Default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `user_id`, `first_name`, `last_name`, `address`, `phone`, `profile`) VALUES
(1, 2, 'John', 'Doe', '123 Maple Street', '555-1234', '../../public/images/images.png'),
(2, 3, 'Jane', 'Smith', '456 Oak Avenue', '555-5678', 'Default.png'),
(18, 46, 'Abdelmounaim', 'BOUBASTA', 'Tétouan', '0617014503', '../../public/images/github.png');

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `rate_id` int(11) NOT NULL,
  `consumption_from` int(11) NOT NULL,
  `consumption_to` int(11) NOT NULL,
  `price_per_kwh` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rates`
--

INSERT INTO `rates` (`rate_id`, `consumption_from`, `consumption_to`, `price_per_kwh`) VALUES
(1, 0, 100, 0.80),
(2, 101, 200, 0.90),
(3, 201, 1000, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `reclamations`
--

CREATE TABLE `reclamations` (
  `reclamation_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` date NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'in_review'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reclamations`
--

INSERT INTO `reclamations` (`reclamation_id`, `customer_id`, `type`, `description`, `date`, `status`) VALUES
(1, 1, 'external leak', 'Leak observed near the meter outside the house.', '2024-02-05', 'reviewed'),
(2, 2, 'internal leak', 'Leak observed in the bathroom plumbing.', '2024-02-06', 'reviewed'),
(3, 1, 'external leak', 'Leak observed in the bathroom plumbing and others.', '2024-02-06', 'reviewed'),
(11, 1, 'mixed', 'test desc', '2024-03-01', 'in_review'),
(12, 1, 'external', 'somthing wet wrong it my kitchen', '2024-03-05', 'in_review'),
(13, 1, 'external', 'somthing wet wrong it my kitchen', '2024-03-05', 'in_review'),
(14, 1, 'internal', 'test2345678', '2024-03-05', 'reviewed'),
(15, 1, 'just a message ', 'traet me ', '2024-03-05', 'reviewed'),
(16, 1, 'bill need to be paid of month X', 'i need my bill to accure paid of the month x', '2024-03-10', 'in_review');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`) VALUES
(1, 'admin'),
(2, 'customer');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `password`, `role_id`) VALUES
(1, 'admin@example.com', 'admin123', 1),
(2, 'customer@example.com', 'costumer123', 2),
(3, 'customer2@example.com', 'costumer321', 2),
(46, 'mounaim.boubasta@gmail.com', '123123123', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annual_consumption_files`
--
ALTER TABLE `annual_consumption_files`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `annual_consumption_files_ibfk_1` (`customer_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `bills_rate_fk` (`rate_id`),
  ADD KEY `bills_customer_fk` (`customer_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `customers_ibfk_1` (`user_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`rate_id`);

--
-- Indexes for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`reclamation_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annual_consumption_files`
--
ALTER TABLE `annual_consumption_files`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `rate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `reclamation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annual_consumption_files`
--
ALTER TABLE `annual_consumption_files`
  ADD CONSTRAINT `annual_consumption_files_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bills_rate_fk` FOREIGN KEY (`rate_id`) REFERENCES `rates` (`rate_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reclamations`
--
ALTER TABLE `reclamations`
  ADD CONSTRAINT `reclamations_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
