-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 13, 2025 at 09:38 AM
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
-- Database: `ojt`
--

-- --------------------------------------------------------

--
-- Table structure for table `pur_req`
--

CREATE TABLE `pur_req` (
  `id` int(11) NOT NULL,
  `pr_id` varchar(50) NOT NULL,
  `pr_no` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `date` varchar(50) NOT NULL,
  `purpose` text NOT NULL,
  `status` varchar(10) NOT NULL,
  `remarks` text NOT NULL,
  `documents` varchar(100) NOT NULL,
  `last_ud` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pur_req`
--

INSERT INTO `pur_req` (`id`, `pr_id`, `pr_no`, `province`, `date`, `purpose`, `status`, `remarks`, `documents`, `last_ud`) VALUES
(77, '002', '2025-04-002', 'Aurora', '2025-04-10', 'Tools', 'Done', 'PCV', '2025-04-002.pdf', 'user'),
(78, '003', '2025-04-003', 'Aurora', '2025-04-10', 'UTANG ', 'On-going', 'walang pera', '2025-04-003.pdf', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `user_type`) VALUES
(9, 'admin', '$2y$10$Tp40aOAZNLWg3HZRf6e5X.Sm3RJB5yojLh3hj/ORSzONINL45x8V.', 'admin'),
(10, 'user', '$2y$10$hcI9EWHFxA6LVPx1CBrlhOxluWz9vOzQm.9JY8ZzPR8GJ9duXmWd2', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pur_req`
--
ALTER TABLE `pur_req`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pur_req`
--
ALTER TABLE `pur_req`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
