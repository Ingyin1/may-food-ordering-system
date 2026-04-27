-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 27, 2026 at 07:49 PM
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
-- Database: `restaurant`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` double NOT NULL,
  `total_price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menucategory`
--

CREATE TABLE `menucategory` (
  `catId` int(11) NOT NULL,
  `catName` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menucategory`
--

INSERT INTO `menucategory` (`catId`, `catName`) VALUES
(5, 'Malar Series'),
(6, 'Drinks Series'),
(7, 'Snacks Series'),
(8, 'Noodle Series'),
(10, 'Salad Series');

-- --------------------------------------------------------

--
-- Table structure for table `menuitem`
--

CREATE TABLE `menuitem` (
  `itemId` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `catId` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('Available','Unavailable') NOT NULL DEFAULT 'Available',
  `description` text DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `is_popular` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menuitem`
--

INSERT INTO `menuitem` (`itemId`, `itemName`, `catId`, `price`, `status`, `description`, `image`, `is_popular`) VALUES
(7, 'Malar Xaing Gou', 5, 1700.00, 'Available', 'Single Set with Pork', '1768634600_s827949095227762158_p1_i1_w600.jpeg', 0),
(8, 'Malar Sticks', 5, 19000.00, 'Available', 'Meet Ball&Seafood', '1769249392_photo_2026-01-24_16-29-48.jpg', 0),
(9, 'Hot Pot ', 5, 30000.00, 'Available', 'Seafood Set', '1769249448_photo_2026-01-24_16-29-58.jpg', 1),
(10, 'Mauk Chaik', 5, 35000.00, 'Available', 'Couple Set with beef', '1769249554_photo_2026-01-24_16-41-04.jpg', 0),
(11, 'Burger', 7, 7500.00, 'Available', 'Chicken,Egg,Cheese', '1769249709_photo_2026-01-24_16-45-01.jpg', 0),
(12, 'Fried Potato', 7, 3000.00, 'Available', 'Single', '1769249879_Main_snack.jpg', 0),
(13, 'Corn Dog', 7, 1500.00, 'Unavailable', 'Sausages', '1769250083_photo_2026-01-24_16-50-22.jpg', 0),
(14, 'Snadwich', 7, 3500.00, 'Available', 'Ham,Cheese', '1769250132_photo_2026-01-24_16-50-25.jpg', 1),
(15, 'Bubble Tea', 6, 8500.00, 'Available', 'Normal Size', '1769250641_photo_2026-01-24_16-58-40.jpg', 0),
(16, 'Sparkling Blueberry Soda', 6, 6000.00, 'Available', 'Blueberry,Lime,Soda', '1769250730_photo_2026-01-24_16-59-46.jpg', 1),
(17, 'Soda com Menta', 6, 6500.00, 'Available', 'Soda,Lime', '1769250776_photo_2026-01-24_16-58-40 (2).jpg', 1),
(18, 'Strawberry Milkshake', 6, 7800.00, 'Available', 'Frozan strawberry', '1769251430_photo_2026-01-24_17-13-31.jpg', 0),
(19, 'Ramen', 8, 25000.00, 'Available', 'Japanese Noodle', '1769251614_photo_2026-01-24_17-15-49 (2).jpg', 1),
(20, 'Chinese Snail Noodle ', 8, 9500.00, 'Available', 'Beef ', '1769251676_Main_noodle.jpg', 0),
(21, 'Noodle Soup', 8, 18000.00, 'Available', 'With Dumpling ', '1769251725_photo_2026-01-24_17-15-49.jpg', 0),
(22, 'Spicy Noodle', 8, 8000.00, 'Available', 'Cheese', '1769251765_photo_2026-01-24_17-15-43.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` timestamp NULL DEFAULT current_timestamp(),
  `sub_total` decimal(10,2) NOT NULL,
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `grand_total` decimal(10,2) NOT NULL,
  `order_status` enum('Pending','Processing','On the way','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `pmode` enum('Cash','Card','Takeaway') NOT NULL,
  `payment_status` enum('Pending','Successful','Rejected') NOT NULL DEFAULT 'Pending',
  `cancel_reason` varchar(255) DEFAULT NULL,
  `note` varchar(255) NOT NULL,
  `address` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `sub_total`, `delivery_fee`, `grand_total`, `order_status`, `pmode`, `payment_status`, `cancel_reason`, `note`, `address`) VALUES
(14, 14, '2026-01-25 05:59:07', 6000.00, 0.00, 6000.00, 'Completed', 'Takeaway', 'Pending', '', 'hurry up', 'Manadalay'),
(15, 15, '2026-01-25 06:05:58', 26000.00, 0.00, 26000.00, 'Completed', 'Takeaway', 'Pending', '', 'Nothing', ' Manadalay'),
(16, 16, '2026-01-25 14:23:38', 8500.00, 0.00, 8500.00, 'Completed', 'Takeaway', 'Pending', '', 'fdf', 'Manadalay');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `itemName` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `itemId`, `itemName`, `quantity`, `price`, `total_price`) VALUES
(19, 14, 16, 'Sparkling Blueberry Soda', 1, 6000.00, 6000.00),
(20, 15, 21, 'Noodle Soup', 1, 18000.00, 18000.00),
(21, 15, 22, 'Spicy Noodle', 1, 8000.00, 8000.00),
(22, 16, 15, 'Bubble Tea', 1, 8500.00, 8500.00);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `noOfGuests` int(11) NOT NULL,
  `reservedTime` time NOT NULL,
  `reservedDate` date NOT NULL,
  `status` enum('Pending','On Process','Completed','Cancelled') NOT NULL DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`reservation_id`, `user_id`, `name`, `contact`, `noOfGuests`, `reservedTime`, `reservedDate`, `status`) VALUES
(8, 7, 'Ingyin', '09882260033', 1, '00:33:00', '2026-01-19', 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `status` enum('approved','pending','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `first_name`, `user_id`, `order_id`, `rating`, `review_text`, `status`, `created_at`) VALUES
(13, 'Htet', 14, 14, 5, 'Very Good Food', 'approved', '2026-01-25 06:01:05'),
(14, 'Mary', 15, 15, 3, 'Noodle Soup is a little bit spicy', 'approved', '2026-01-25 06:10:40'),
(15, 'Coe', 16, 16, 4, '<script>alert(\'XSS Vulnerable!\');</script>', 'approved', '2026-01-27 16:32:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `contact` varchar(15) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('Customer','Admin') DEFAULT 'Customer',
  `password` varchar(255) NOT NULL,
  `dateCreated` timestamp NULL DEFAULT current_timestamp(),
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `email`, `firstName`, `lastName`, `contact`, `address`, `role`, `password`, `dateCreated`, `profile_image`) VALUES
(7, 'sulat@gmail.com', 'Su', 'Latt', '09876543657', NULL, 'Admin', '$2y$10$FiadGeoIHy9UQTNDePuMaOCBiH9aKoEVwm2OjFYbw5nlXdD/J9kuq', '2026-01-16 16:09:15', 'd310a89b3576c93dfc2e5c775f9ec224.jpg'),
(8, 'john@gmail.com', 'John', 'Doe', '09876543576', 'United City', 'Customer', '$2y$10$bSbxLBbIfjLb5IDfQN3UYu2R//32h2bkQpGhUikXPqJxyJ4JledFK', '2026-01-18 02:39:11', '8a6f47f9d74b53cabde55f28a71ebc92.jpg'),
(9, 'aye@gmail.com', 'Aye', 'Thida', '0943088611', 'Yangon', 'Customer', '$2y$10$jBQU7JNxTv6.c7YY1qaHPuBagyCQfD9V5IFCq6B0RTZ9xsmwJ3GlW', '2026-01-22 05:21:26', 'photo_2024-06-28_19-16-56.jpg'),
(14, 'htet@gmail.com', 'Htet', 'Naing', '098765432', 'Yangon', 'Customer', '$2y$10$QK6Y2OYsSC8A.EvIwbfJ0uIu88MsIkjrW2FzogsgM.hPVje11xGFG', '2026-01-24 07:08:34', '1769320461_cat8.jpg'),
(15, 'mary@gmail.com', 'Mary', 'Jane', '09156283299', 'Manadalay', 'Customer', '$2y$10$.SPWQn//G3GSW2arh8nqiuSpqwLYv2AKqHM8F5xhjnj871a87miwy', '2026-01-24 07:11:55', NULL),
(16, 'Coe@gmail.com', 'Coe', 'Coe', '09123456789', 'Norway', 'Customer', '$2y$10$DQVmIeAcqSH39uPBTkp0MO4eXQ07x1jajtW0zJzNJ1cyHeDH2.Cie', '2026-01-24 08:35:39', '1769321667_d621ab5cfb9afb1cfcaea8a8c8d752ec.jpg'),
(18, 'Sapal@gmail.com', 'Sapal', 'Phyu', '0943088611', 'Yangon', 'Customer', '$2y$10$JVVfzIzcsrpbf87acG3SB.x8yv2pw.dOmRJ7E7w4SMJC3U/yYWduu', '2026-01-24 13:27:08', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `menucategory`
--
ALTER TABLE `menucategory`
  ADD PRIMARY KEY (`catId`);

--
-- Indexes for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD PRIMARY KEY (`itemId`),
  ADD KEY `catId` (`catId`),
  ADD KEY `idx_menu_name` (`itemName`),
  ADD KEY `idx_menu_price` (`price`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `idx_order_user` (`user_id`),
  ADD KEY `idx_order_status` (`order_status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `itemId` (`itemId`),
  ADD KEY `idx_order_items_id` (`order_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_reservation_date` (`reservedDate`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_user_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `menucategory`
--
ALTER TABLE `menucategory`
  MODIFY `catId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `itemId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menuitem`
--
ALTER TABLE `menuitem`
  ADD CONSTRAINT `menuitem_ibfk_1` FOREIGN KEY (`catId`) REFERENCES `menucategory` (`catId`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`itemId`) REFERENCES `menuitem` (`itemId`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
