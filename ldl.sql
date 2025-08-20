-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 20, 2025 at 10:28 AM
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
-- Database: `ldl`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `status` enum('present','absent') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `member_id`, `date`, `status`) VALUES
(201, 15, '2025-05-01', 'present'),
(202, 15, '2025-05-02', 'absent'),
(203, 15, '2025-05-03', 'present'),
(204, 16, '2025-05-01', 'present'),
(205, 16, '2025-05-02', 'present'),
(206, 16, '2025-05-03', 'absent'),
(207, 17, '2025-05-01', 'absent'),
(208, 17, '2025-05-02', 'present'),
(209, 17, '2025-05-03', 'present'),
(210, 18, '2025-05-01', 'present'),
(211, 18, '2025-05-02', 'present'),
(212, 19, '2025-05-01', 'absent'),
(213, 19, '2025-05-02', 'present'),
(214, 20, '2025-05-01', 'present'),
(215, 20, '2025-05-02', 'present'),
(216, 20, '2025-05-03', 'present'),
(217, 21, '2025-05-01', 'present'),
(218, 21, '2025-05-02', 'absent'),
(219, 22, '2025-05-01', 'present'),
(220, 22, '2025-05-02', 'present'),
(221, 23, '2025-05-01', 'present'),
(222, 23, '2025-05-02', 'present'),
(223, 24, '2025-05-01', 'present'),
(224, 15, '2025-05-01', 'present'),
(225, 15, '2025-05-03', 'absent'),
(226, 15, '2025-05-05', 'present'),
(227, 15, '2025-05-10', 'present'),
(228, 15, '2025-05-15', 'absent'),
(229, 15, '2025-05-20', 'present'),
(230, 16, '2025-05-02', 'absent'),
(231, 16, '2025-05-04', 'present'),
(232, 16, '2025-05-07', 'present'),
(233, 16, '2025-05-12', 'present'),
(234, 16, '2025-05-18', 'absent'),
(235, 16, '2025-05-22', 'present'),
(236, 17, '2025-05-01', 'present'),
(237, 17, '2025-05-08', 'present'),
(238, 17, '2025-05-14', 'present'),
(239, 17, '2025-05-21', 'absent'),
(240, 18, '2025-05-03', 'absent'),
(241, 18, '2025-05-06', 'present'),
(242, 18, '2025-05-11', 'present'),
(243, 18, '2025-05-17', 'present'),
(244, 19, '2025-05-04', 'present'),
(245, 19, '2025-05-09', 'absent'),
(246, 19, '2025-05-16', 'present'),
(247, 20, '2025-05-02', 'present'),
(248, 20, '2025-05-05', 'present'),
(249, 20, '2025-05-10', 'present'),
(250, 21, '2025-05-07', 'absent'),
(251, 21, '2025-05-13', 'present'),
(252, 21, '2025-05-20', 'present'),
(253, 22, '2025-05-01', 'present'),
(254, 22, '2025-05-08', 'absent'),
(255, 22, '2025-05-15', 'present'),
(256, 23, '2025-05-03', 'present'),
(257, 23, '2025-05-12', 'absent'),
(258, 23, '2025-05-19', 'present'),
(259, 24, '2025-05-06', 'present'),
(260, 24, '2025-05-14', 'present'),
(261, 24, '2025-05-21', 'absent'),
(262, 15, '2025-06-01', 'present'),
(263, 15, '2025-06-04', 'present'),
(264, 15, '2025-06-08', 'absent'),
(265, 16, '2025-06-02', 'absent'),
(266, 16, '2025-06-06', 'present'),
(267, 16, '2025-06-10', 'present'),
(268, 17, '2025-06-01', 'present'),
(269, 17, '2025-06-05', 'present'),
(270, 17, '2025-06-09', 'absent'),
(271, 18, '2025-06-03', 'present'),
(272, 18, '2025-06-07', 'present'),
(273, 18, '2025-06-11', 'present'),
(274, 19, '2025-06-01', 'absent'),
(275, 19, '2025-06-04', 'present'),
(276, 20, '2025-06-02', 'present'),
(277, 20, '2025-06-06', 'present'),
(278, 21, '2025-06-03', 'present'),
(279, 21, '2025-06-07', 'absent'),
(280, 22, '2025-06-01', 'present'),
(281, 22, '2025-06-05', 'present'),
(282, 23, '2025-06-02', 'absent'),
(283, 23, '2025-06-06', 'present'),
(284, 24, '2025-06-03', 'present'),
(285, 24, '2025-06-08', 'present'),
(286, 15, '2025-05-25', 'present'),
(287, 16, '2025-05-25', 'present'),
(288, 22, '2025-05-25', 'present'),
(289, 15, '2025-05-06', 'present'),
(290, 19, '2025-05-25', 'present'),
(291, 16, '2025-05-26', 'present'),
(292, 25, '2025-05-27', 'present'),
(293, 15, '2025-05-31', 'present'),
(294, 15, '2025-06-02', 'present'),
(295, 25, '2025-08-14', 'present'),
(296, 25, '2025-08-18', 'present');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `enrollment_no` varchar(50) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `day` varchar(155) NOT NULL,
  `role` enum('admin','student') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `enrollment_no`, `phone`, `email`, `password`, `day`, `role`) VALUES
(1, 'Admin', NULL, '0', 'admin@example.com', '0192023a7bbd73250516f069df18b500', '', 'admin'),
(15, 'Aman Sharma', 'ENR001', '9876543210', 'aman@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Thursday', 'student'),
(16, 'Riya Patel', 'ENR002', '9876543211', 'riya@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Tuesday', 'student'),
(17, 'Mohit Verma', 'ENR003', '9876543212', 'mohit@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Wednesday', 'student'),
(18, 'Sneha Das', 'ENR004', '9876543213', 'sneha@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Friday', 'student'),
(19, 'Vikram Singh', 'ENR005', '9876543214', 'vikram@example.com', 'e10adc3949ba59abbe56e057f20f883e', '', 'student'),
(20, 'Anjali Mehra', 'ENR006', '9876543215', 'anjali@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Thursday', 'student'),
(21, 'Rahul Jain', 'ENR007', '9876543216', 'rahul@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Monday', 'student'),
(22, 'Priya Iyer', 'ENR008', '9876543217', 'priya@example.com', 'e10adc3949ba59abbe56e057f20f883e', 'Sunday', 'student'),
(23, 'Karan Joshi', 'ENR009', '9876543218', 'karan@example.com', 'e10adc3949ba59abbe56e057f20f883e', '', 'student'),
(24, 'Neha Kapoor', 'ENR010', '9876543219', 'neha@example.com', 'e10adc3949ba59abbe56e057f20f883e', '', 'student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=297;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
