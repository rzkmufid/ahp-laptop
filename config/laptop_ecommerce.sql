-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 16, 2025 at 08:50 AM
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
-- Database: `laptop_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `ahp_scores`
--

CREATE TABLE `ahp_scores` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `processor_score` float NOT NULL,
  `ram_score` float NOT NULL,
  `storage_score` float NOT NULL,
  `gpu_score` float NOT NULL,
  `display_score` float NOT NULL,
  `harga_score` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ahp_scores`
--

INSERT INTO `ahp_scores` (`id`, `product_id`, `processor_score`, `ram_score`, `storage_score`, `gpu_score`, `display_score`, `harga_score`) VALUES
(4, 10, 0.75, 0.5, 0.5, 0.25, 0.5, 0.25),
(5, 11, 0.5, 0.1, 0.5, 0.75, 0.5, 0.75),
(6, 12, 0.5, 0.1, 0.5, 0.5, 0.5, 0.25),
(7, 13, 0.75, 0.5, 0.5, 0.75, 0.5, 1),
(8, 14, 0.75, 0.5, 0.5, 1, 0.5, 1),
(9, 15, 0.5, 0.1, 0.5, 0.25, 0.5, 0.25),
(10, 16, 0.75, 0.5, 0.75, 0.25, 0.75, 1),
(11, 17, 0.75, 0.5, 0.5, 0.25, 0.5, 0.1),
(12, 18, 0.75, 0.5, 0.5, 0.25, 0.5, 0.5),
(13, 19, 0.75, 0.1, 0.5, 0.1, 0.5, 0.5),
(14, 20, 0.5, 0.5, 0.5, 0.1, 1, 0.75),
(15, 21, 0.5, 0.1, 0.5, 0.5, 0.5, 0.25),
(16, 22, 0.5, 0.5, 1, 0.5, 0.5, 0.25),
(17, 23, 0.5, 0.1, 0.5, 0.5, 0.5, 0.5),
(18, 24, 0.5, 0.25, 0.5, 0.5, 0.5, 0.5);

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `product_id`, `quantity`) VALUES
(1, 1, 9, 1);

-- --------------------------------------------------------

--
-- Table structure for table `laptop_specifications`
--

CREATE TABLE `laptop_specifications` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `processor` varchar(100) NOT NULL,
  `processor_detail` varchar(255) NOT NULL,
  `ram` varchar(50) NOT NULL,
  `storage` varchar(100) NOT NULL,
  `gpu` varchar(100) NOT NULL,
  `display` varchar(255) NOT NULL,
  `battery` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `laptop_specifications`
--

INSERT INTO `laptop_specifications` (`id`, `product_id`, `processor`, `processor_detail`, `ram`, `storage`, `gpu`, `display`, `battery`) VALUES
(10, 10, 'AMD Ryzen 7', 'AMD Ryzen 7', '16 GB', '512 GB', 'AMD RADEON', 'FHD IPS', '99999 mAh'),
(11, 11, 'Intel Core i5', 'Intel Core i5', '8 GB', '512 GB', 'INVIDIA RTX 3050', 'FHD', '3422'),
(12, 12, 'AMD Ryzen 5', 'AMD Ryzen 5', '8 GB', '512 GB', 'INVIDIA RTX 2050', 'FHD', '212312'),
(13, 13, 'Intel Core i7', 'Intel Core i7', '16 GB', '512 GB', 'INVIDIA RTX 3050', 'FHD', '345345345'),
(14, 14, 'AMD Ryzen 7', 'AMD Ryzen 7', '16 GB', '512 GB', 'INVIDIA RTX 4060', 'FHD', '3332232'),
(15, 15, 'AMD Ryzen 5', 'AMD Ryzen 5', '8 GB', '512 GB', 'AMD RADEON', 'FHD', '354345453'),
(16, 16, 'AMD Ryzen 7', 'AMD Ryzen 7', '16 GB', '1 TB', 'AMD RADEON', 'FHD TOUCHSCREEN', '1123123123'),
(17, 17, 'AMD Ryzen 7', 'AMD Ryzen 7', '16 GB', '512 GB', 'AMD RADEON', 'FHD IPS', '16000'),
(18, 18, 'AMD Ryzen 7', 'AMD Ryzen 7', '16 GB', '512 GB', 'AMD RADEON', 'FHD', '123123123'),
(19, 19, 'Intel Core i7', 'Intel Core i7', '8 GB', '512 GB', 'INTEL GRAPICH', 'FHD IPS', '123123123'),
(20, 20, 'Intel Core i5', 'Intel Core i5', '16 GB', '512 GB', 'INTEL GRAPICH', 'OLED', '12312'),
(21, 21, 'Intel Core i5', 'Intel Core i5', '8 GB', '512 GB', 'INVIDIA RTX 2050', 'FHD', '123123123'),
(22, 22, 'Intel Core i5', 'Intel Core i5', '16 GB', '512 GB', 'INVIDIA RTX 2050', 'FHD', '111111'),
(23, 23, 'Intel Core i5', 'Intel Core i5', '8 GB', '512 GB', 'INVIDIA RTX 2050', 'FHD IPS', '1111111'),
(24, 24, 'Intel Core i5', 'Intel Core i5', '12 GB', '512 GB', 'INVIDIA RTX 2050', 'FHD', '111111');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 0, 19.00, 'pending', '2025-02-14 10:45:49'),
(2, 1, 95.00, 'Rejected', '2025-02-14 10:49:29'),
(3, 1, 1499.99, 'Processed', '2025-02-14 10:49:54'),
(4, 1, 1499.99, 'Pending', '2025-02-14 10:51:48'),
(5, 2, 8899000.00, 'Pending', '2025-02-15 03:50:40'),
(6, 2, 12000000.00, 'Pending', '2025-02-15 05:30:53');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 2, 7, 5, 19.00),
(2, 3, 1, 1, 1499.99),
(3, 4, 1, 1, 1499.99),
(4, 5, 17, 1, 8899000.00),
(5, 6, 18, 1, 12000000.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `stock`, `image`, `created_at`) VALUES
(10, 'Asus M1405 YA', 'Asus M1405 YA\r\n', 10299000.00, 77, '67af64a7eb442.jpg', '2025-02-14 15:43:35'),
(11, 'Asus TUF FX506NF', 'Asus TUF FX506NF\r\n', 13199000.00, 56, '67af67640b3a4.jpg', '2025-02-14 15:55:16'),
(12, 'Asus TUF FX506N', 'Asus TUF FX506N\r\n', 10889000.00, 77, '67af68f620f6e.jpg', '2025-02-14 16:01:58'),
(13, 'Asus TUF FX507ZC', 'Asus TUF FX507ZC\r\n', 15199000.00, 100, '67af69bfb7ab9.png', '2025-02-14 16:05:19'),
(14, 'Asus TUF FX507NU', 'Asus TUF FX507NU\r\n', 17000000.00, 66, '67af6b1a80342.jpg', '2025-02-14 16:11:06'),
(15, 'Lenovo IP SLIM 5', 'Lenovo IP SLIM 5\r\n', 9599000.00, 999, '67af6c0aefc4b.avif', '2025-02-14 16:15:06'),
(16, 'Lenovo YOGA 7', 'Lenovo YOGA 7\r\n', 16799000.00, 200, '67af6c8ed91f9.jpg', '2025-02-14 16:17:18'),
(17, 'Acer 3-A314', 'Acer 3-A314\r\n', 8899000.00, 842, '67af6d1898ee4.png', '2025-02-14 16:19:36'),
(18, 'Acer SWIFT 14', 'Acer SWIFT 14\r\n', 12000000.00, 145, '67af6d8990e56.jpg', '2025-02-14 16:21:29'),
(19, 'Acer Aspire 5', 'Acer Aspire 5\r\n', 13000000.00, 342, '67af6de8bffe4.jpg', '2025-02-14 16:23:04'),
(20, 'Acer SWIFT 3 OLED', 'Acer SWIFT 3 OLED\r\n', 14.00, 442, '67af6e312d935.webp', '2025-02-14 16:24:17'),
(21, 'MSI THIN G 15', 'MSI THIN G 15\r\n', 9999000.00, 111, '67af6e75120a0.png', '2025-02-14 16:25:25'),
(22, 'MSI THIN G 15', 'MSI THIN G 15\r\n', 10599000.00, 331, '67af6ee875a86.png', '2025-02-14 16:27:20'),
(23, 'Acer NITRO V15', 'Acer NITRO V15\r\n', 11699000.00, 200, '67af6f32412d8.jpg', '2025-02-14 16:28:34'),
(24, 'Lenovo LOQ GAMING', 'Lenovo LOQ GAMING\r\n', 11899000.00, 621, '67af6f8dde12e.jpg', '2025-02-14 16:30:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`) VALUES
(1, 'admin', 'admin@gmail.com', '$2y$10$ndX42XDvi0tX52patjFTQueqBBaPNUu8PXo5pHDrp3yFZffndBV12', '2025-02-14 10:49:18'),
(2, 'user', 'user@gmail.com', '$2y$10$zR85OWl.4LpauCFU.leYS.vtx1HW0B9LnMc6tAkAK81PGFkLah8tS', '2025-02-15 03:49:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ahp_scores`
--
ALTER TABLE `ahp_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laptop_specifications`
--
ALTER TABLE `laptop_specifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
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
-- AUTO_INCREMENT for table `ahp_scores`
--
ALTER TABLE `ahp_scores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `laptop_specifications`
--
ALTER TABLE `laptop_specifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ahp_scores`
--
ALTER TABLE `ahp_scores`
  ADD CONSTRAINT `ahp_scores_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laptop_specifications`
--
ALTER TABLE `laptop_specifications`
  ADD CONSTRAINT `laptop_specifications_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
