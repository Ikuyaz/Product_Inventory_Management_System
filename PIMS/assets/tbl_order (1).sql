-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2024 at 09:12 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pims_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_order`
--

CREATE TABLE `tbl_order` (
  `user_id` varchar(20) NOT NULL,
  `order_id` int(8) UNSIGNED ZEROFILL NOT NULL,
  `order_date` datetime NOT NULL DEFAULT current_timestamp(),
  `cust_number` varchar(200) NOT NULL,
  `order_cust` varchar(200) NOT NULL,
  `payment_type` varchar(7) NOT NULL,
  `order_amount` float NOT NULL,
  `cust_address` varchar(200) NOT NULL,
  `cust_items` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbl_order`
--

INSERT INTO `tbl_order` (`user_id`, `order_id`, `order_date`, `cust_number`, `order_cust`, `payment_type`, `order_amount`, `cust_address`, `cust_items`) VALUES
('A001', 00000001, '2024-04-18 23:48:40', '016', 'Go', 'Credit', 63, 'Kapar', '[{\"name\":\"ME\",\"quantity\":2},{\"name\":\"Roti\",\"quantity\":3}]'),
('A001', 00000003, '2024-04-19 00:03:55', '017224', 'Go', 'Credit', 113, 'Kapar, Selangor.1', 'ME ------ 4\nRoti ------ 5'),
('A001', 00000004, '0000-00-00 00:00:00', 'abc', 'abc', 'abc', 50, 'abc', 'ME ------ 2\nRoti ------ 2'),
('A001', 00000005, '2024-04-19 00:08:35', '016', 'Go2', 'abc', 75, 'Selangor', 'ME ------ 3\nRoti ------ 3'),
('A001', 00000006, '2024-04-19 00:10:25', '017', 'Go3', 'abc', 50, 'Go3', 'ME ------ 2\nRoti ------ 2'),
('A001', 00000007, '2024-04-19 00:16:39', '016-2420019', 'Go', 'Credit ', 63, 'Kapar', 'ME ------ 2\nRoti ------ 3'),
('A001', 00000008, '2024-04-21 10:23:44', '016-6749235', 'Lim', 'Cash on', 25, 'UUM', 'ME ------ 1\nRoti ------ 1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_order`
--
ALTER TABLE `tbl_order`
  ADD PRIMARY KEY (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_order`
--
ALTER TABLE `tbl_order`
  MODIFY `order_id` int(8) UNSIGNED ZEROFILL NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
