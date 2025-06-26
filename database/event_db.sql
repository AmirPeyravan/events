-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2021 at 10:21 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `event_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `event_audience`
--

CREATE TABLE `event_audience` (
  `id` int(30) NOT NULL,
  `event_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` text NOT NULL,
  `remarks` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event_audience`
--

INSERT INTO `event_audience` (`id`, `event_id`, `name`, `contact`, `email`, `remarks`, `status`, `date_created`, `date_updated`) VALUES
(1, 1, 'Claire Blake', '09123456789', 'cblake@sample.com', '09123456789', 0, '2021-07-26 11:59:02', NULL),
(3, 1, 'Mike Williams', '09123456789', 'mwilliams@sample.com', 'Sample', 0, '2021-07-26 15:33:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `event_list`
--

CREATE TABLE `event_list` (
  `id` int(30) NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `venue` text NOT NULL,
  `limit_registration` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=Don''t Close, 1= entry has timeout',
  `datetime_start` datetime NOT NULL,
  `datetime_end` datetime NOT NULL,
  `limit_time` float DEFAULT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_update` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `event_list`
--

INSERT INTO `event_list` (`id`, `title`, `description`, `venue`, `limit_registration`, `datetime_start`, `datetime_end`, `limit_time`, `user_id`, `date_created`, `date_update`) VALUES
(1, 'Sample 101', 'Sample Event Only', 'Venue 101', 0, '2021-07-26 13:00:00', '2021-07-26 17:00:00', 0, 3, '2021-07-26 11:10:12', '2021-07-26 11:29:25'),
(3, 'Sample 102', 'Sample only', 'Venue 102', 1, '2021-07-26 15:00:00', '2021-07-26 17:30:00', 30, 3, '2021-07-26 13:36:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `registration_history`
--

CREATE TABLE `registration_history` (
  `id` int(30) NOT NULL,
  `event_id` int(30) NOT NULL,
  `audience_id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `registration_history`
--

INSERT INTO `registration_history` (`id`, `event_id`, `audience_id`, `user_id`, `date_created`) VALUES
(1, 1, 3, 3, '2021-07-26 15:35:33'),
(2, 1, 1, 3, '2021-07-26 15:40:04');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Event Registration System with QR Code - PHP'),
(2, 'address', 'Philippines'),
(3, 'contact', '+1234567890'),
(4, 'email', 'info@sample.com'),
(5, 'fb_page', 'https://www.facebook.com/myPageName'),
(6, 'short_name', 'ERS-QR-PHP'),
(9, 'logo', 'uploads/1627260420_checklist.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 2 COMMENT '1=Admin,2=Registrar',
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Adminstrator', 'Admin', 'admin', '0192023a7bbd73250516f069df18b500', 'uploads/1627261440_avatar.png', NULL, 1, '2021-01-20 14:02:37', '2021-07-26 09:57:03'),
(3, 'John', 'Smith', 'jsmith', '39ce7e2a8573b41ce73b5ba41617f8f7', 'uploads/1627264800_avatar.png', NULL, 2, '2021-07-26 10:00:18', '2021-07-26 10:04:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `event_audience`
--
ALTER TABLE `event_audience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_list`
--
ALTER TABLE `event_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration_history`
--
ALTER TABLE `registration_history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
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
-- AUTO_INCREMENT for table `event_audience`
--
ALTER TABLE `event_audience`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `event_list`
--
ALTER TABLE `event_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `registration_history`
--
ALTER TABLE `registration_history`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
