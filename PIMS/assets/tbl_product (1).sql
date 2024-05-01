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
-- Table structure for table `tbl_product`
--

CREATE TABLE `tbl_product` (
  `user_id` varchar(20) NOT NULL,
  `product_sku` varchar(200) NOT NULL,
  `product_location` varchar(200) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `product_price` float NOT NULL,
  `product_quantity` int(100) NOT NULL,
  `supplier_contact` varchar(200) NOT NULL,
  `time_created` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_product`
--

INSERT INTO `tbl_product` (`user_id`, `product_sku`, `product_location`, `product_name`, `product_price`, `product_quantity`, `supplier_contact`, `time_created`) VALUES
('A001', 'SKU-74972', 'A003', 'ME', 12, 49, '01111111', '2024-01-23'),
('A002', 'SKU-61341', 'A001', 'Roti', 11, 50, '01111111', '2024-01-23'),
('A001', 'SKU-86971', 'L004', 'Roti', 13, 25, '13', '2024-03-23');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
