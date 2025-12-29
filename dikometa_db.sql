-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: Dec 28, 2025 at 04:22 PM
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
-- Database: `dikometa_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `joined_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`id`, `name`, `address`, `phone`, `status`, `joined_date`) VALUES
(1, 'Budi Santoso', NULL, NULL, 'Active', '2024-01-10'),
(2, 'Siti Aminah', NULL, NULL, 'Active', '2024-02-15'),
(3, 'Joko Anwar', NULL, NULL, 'Inactive', '2023-11-20'),
(4, 'Rina Nose', NULL, NULL, 'Active', '2025-12-01'),
(5, 'Dummy 1', 'hgfghdfg', '84598455656', 'Active', '2025-12-28'),
(6, 'Dummy 2', 'ghcffyty', '4554', 'Active', '2025-12-28');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `member_id` int(11) DEFAULT NULL,
  `type` enum('loan_out','loan_pay','saving_in','saving_out','expense') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `trans_date` date NOT NULL,
  `description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `member_id`, `type`, `amount`, `trans_date`, `description`) VALUES
(1, 1, 'saving_in', 1500000.00, '2025-12-01', 'Simpanan Wajib'),
(2, 2, 'saving_in', 1500000.00, '2025-12-05', 'Simpanan Pokok'),
(3, 0, 'saving_in', 40000000.00, '2025-12-01', 'Modal Awal Koperasi'),
(4, 1, 'saving_in', 1000000.00, '2025-12-28', ''),
(5, 1, 'saving_in', 1500000.00, '2025-12-28', ''),
(7, 2, 'loan_out', 100000.00, '2025-12-28', ''),
(8, 4, 'loan_out', 100000.00, '2025-12-28', ''),
(10, 2, 'loan_out', 2000000.00, '2025-12-28', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `status` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`) VALUES
(1, 'admin', '62f04a011fbb80030bb0a13701c20b41', 'admin', 'Active'),
(2, 'staff1', '62f04a011fbb80030bb0a13701c20b41', 'admin', 'Active'),
(3, 'staff2', '62f04a011fbb80030bb0a13701c20b41', 'admin', 'Active'),
(4, 'manager', '62f04a011fbb80030bb0a13701c20b41', 'admin', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
