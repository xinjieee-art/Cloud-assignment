-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2026 at 04:59 PM
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
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(6) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `name` varchar(99) NOT NULL,
  `gender` char(1) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `profile`, `name`, `gender`, `password`, `email`) VALUES
(1, 'rose.jpg', 'Lim Xin Jie', 'F', '7f001b08e9fd415a4c15bd5f44c6a810f3980174', 'limxj@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(6) NOT NULL,
  `user_id` int(6) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `booking_date` date NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'confirm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `user_id`, `slot_id`, `booking_date`, `status`) VALUES
(3, 2, 4, '2026-07-26', 'confirm');

-- --------------------------------------------------------

--
-- Table structure for table `reservation_room`
--

CREATE TABLE `reservation_room` (
  `room_id` int(11) NOT NULL,
  `reservation_room` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_room`
--

INSERT INTO `reservation_room` (`room_id`, `reservation_room`) VALUES
(1, 3);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `room_id` int(6) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `capacity` int(11) NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`room_id`, `name`, `description`, `capacity`, `status`) VALUES
(1, 'Large Room A', 'Spacious discussion room with projector and whiteboard, suitable for group study.', 12, 'available'),
(2, 'Large Room B', 'Spacious discussion room with long table, ideal for group projects.', 12, 'available'),
(3, 'Medium Room A', 'Mid-size room with whiteboard, suitable for small group study.', 6, 'available'),
(4, 'Medium Room B', 'Mid-size room near the window, good natural lighting.', 6, 'available'),
(5, 'Small Room A', 'Compact room for individual or pair study sessions.', 2, 'available'),
(6, 'Small Room B', 'Quiet compact room, ideal for focused solo study.', 2, 'available'),
(7, 'nothing', '....', 3, 'unavailable');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `staff_id` int(6) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `name` varchar(99) NOT NULL,
  `gender` char(1) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(99) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `profile`, `name`, `gender`, `password`, `email`) VALUES
(1, 'justin.jpg', 'Justin', 'M', '0ce7911e6479995d6c346d6f03eb723b5135309e', 'justin@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `time_slot`
--

CREATE TABLE `time_slot` (
  `slot_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_slot`
--

INSERT INTO `time_slot` (`slot_id`, `start_time`, `end_time`) VALUES
(1, '08:00:00', '10:00:00'),
(2, '10:00:00', '12:00:00'),
(3, '12:00:00', '14:00:00'),
(4, '14:00:00', '16:00:00'),
(5, '16:00:00', '18:00:00'),
(6, '18:00:00', '20:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(6) NOT NULL,
  `name` varchar(99) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `gender` char(1) NOT NULL,
  `email` varchar(99) NOT NULL,
  `password` varchar(255) NOT NULL,
  `login_attemptes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `profile`, `gender`, `email`, `password`, `login_attemptes`) VALUES
(1, '', '', '', '', '', 0),
(2, 'IU', '6a64f29fb210e.jpg', 'F', 'iu@gmail.com', '123456', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `reservation_room`
--
ALTER TABLE `reservation_room`
  ADD PRIMARY KEY (`room_id`,`reservation_room`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`room_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `time_slot`
--
ALTER TABLE `time_slot`
  ADD PRIMARY KEY (`slot_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `room_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `staff_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `time_slot`
--
ALTER TABLE `time_slot`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
