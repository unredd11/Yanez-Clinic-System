-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 12:14 AM
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
-- Database: `yanezclinic_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`admin_id`, `email`, `password`, `name`) VALUES
(1, 'admin@yanezclinic.com', '$2y$10$9s8d7f6g5h4j3k2l1m0nOpQrsTuVwXyZzAaBbCcDdEeFfGgHhIi', 'Administrator'),
(2, 'adminyanez@gmail.com', '$2y$10$EWAy2vGNwUkYBumZF67FUes4A6ZxDG6mpqOm82TAuMuYFT/c/AzQK', 'Admin'),
(4, 'admin', '$2y$10$zn0wtFNfE.paiURuReiDHeTXUtNeD7DUJQywIEXPw3tnwZJ96V.G6', 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `service` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Accepted','Rejected','Completed') DEFAULT 'Pending',
  `appointment_details` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `paypal_transaction_id` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `patient_id`, `service`, `appointment_date`, `appointment_time`, `status`, `appointment_details`, `payment_method`, `paypal_transaction_id`, `updated_at`) VALUES
(1, 1, 'Physical Examination', '2025-09-30', '16:00:00', 'Completed', 'headache', NULL, NULL, '2025-10-09 17:10:36'),
(2, 1, 'Laboratory Testing', '2025-09-30', '15:00:00', 'Completed', 'sakit tiyan', NULL, NULL, '2025-10-13 15:00:27'),
(9, 1, 'Laboratory Testing', '2025-10-22', '15:30:00', 'Pending', 'urine acid', NULL, NULL, '2025-10-09 17:03:43'),
(11, 1, 'X-ray & Diagnostics', '2025-10-25', '10:30:00', 'Pending', 'chest pain', NULL, NULL, '2025-10-09 17:05:17'),
(12, 1, 'Physical Examination', '2025-10-10', '09:00:00', 'Accepted', 'sakit mata', NULL, NULL, '2025-10-09 17:07:26'),
(13, 1, 'X-Ray', '2025-10-10', '08:00:00', 'Accepted', 'arm xray', NULL, NULL, '2025-10-09 17:10:24'),
(14, 1, 'Laboratory Testing', '2025-10-10', '09:00:00', 'Completed', 'sakit tiyan', NULL, NULL, '2025-10-10 16:04:50'),
(15, 1, 'Physical Examination', '2025-10-10', '09:00:00', 'Pending', 'sakit ulo', NULL, NULL, '2025-10-10 13:15:52'),
(16, 1, 'Physical Examination', '2025-10-10', '10:00:00', 'Completed', 'sakit head', NULL, NULL, '2025-10-10 15:37:06'),
(18, 1, 'X-Ray', '2025-10-24', '10:30:00', 'Pending', 'chest checkup', NULL, NULL, '2025-10-10 13:28:14'),
(19, 1, 'Laboratory Testing', '2025-10-22', '10:30:00', 'Pending', 'urine', NULL, NULL, '2025-10-10 16:04:28'),
(20, 1, 'Laboratory Testing', '2025-10-11', '08:30:00', 'Completed', 'urine acid', NULL, NULL, '2025-10-11 14:32:56'),
(22, 3, 'Physical Examination', '2025-10-11', '01:30:00', 'Completed', 'Galabad ulo', 'walkin', NULL, '2025-10-13 15:03:05'),
(24, 3, 'Physical Examination', '2025-11-12', '01:00:00', 'Completed', 'sakit ulo', 'walkin', NULL, '2025-10-15 15:44:48'),
(26, 3, 'Physical Examination', '2025-10-13', '11:30:00', 'Accepted', 'galain tiyan', 'walkin', NULL, '2025-10-13 15:57:48'),
(27, 1, 'Laboratory Testing', '2025-10-29', '01:00:00', 'Accepted', 'Blood chemistry', 'walkin', NULL, '2025-10-14 02:38:59'),
(28, 1, 'Laboratory Testing', '2025-10-14', '01:00:00', 'Pending', 'Blood Chemistry', 'walkin', NULL, '2025-10-14 02:39:25'),
(29, 6, 'X-Ray', '2025-10-17', '09:00:00', 'Accepted', 'Hands X-ray', 'walkin', '', '2025-10-16 15:56:47');

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `first_name`, `last_name`, `email`, `phone_number`, `birthdate`, `address`, `username`, `password`) VALUES
(1, 'Eddison', 'Abragan', 'eddison2x@gmail.com', '09059596798', '2005-08-11', NULL, 'edd2x', '$2y$10$KSM3erh49p5dk7bvH/krPuGgSBkfcRE3h/gOvWRB4vxGyqNPay2QW'),
(3, 'Kim', 'Escobido', 'kimescobido@gmail.com', '09356598799', '2004-08-29', NULL, 'kim2x', '$2y$10$l8VDTzksUhJhTd.fTMFneud5fNwuvdiNwnMwla.Dsh9Y1.6JEZkru'),
(5, 'Carl Louis', 'Lacre', 'carl@gmail.com', '09356598799', '2004-07-15', 'Brgy. Saray, Roosevelt', 'carl2x', '$2y$10$hUG/2voq.rqP8gwjJSAOeOeuKfgg0v0XG3bO0vDDcz2flKrN/fcoC'),
(6, 'Eddison', 'Abragan', 'gmyuriel@gmail.com', '09059596798', '2005-08-11', 'Brgy. Del Carmen, Zone 3', 'eddison2x', '$2y$10$PYy7uklb4OdS.LuZciibOeKOc8Yjh0iWdYBFBlbvOqOtcZ9f1HdT.');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `result_id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `result_text` text DEFAULT NULL,
  `result_file` varchar(255) DEFAULT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `appointment_id`, `patient_id`, `result_text`, `result_file`, `pdf_file`, `created_at`) VALUES
(1, 2, 1, 'ok naka', '1759938402_yanezlogo_white.png', '1760332811_result.pdf', '2025-10-08 15:46:42'),
(3, 2, 1, 'pokemon', '1760015297_png-clipart-pokemon-gold-and-silver-pokemon-crystal-growlithe-pixel-art-pokemon-text-carnivoran-thumbnail-removebg-preview.png', '1760332811_result.pdf', '2025-10-09 13:08:17'),
(4, 24, 3, 'New GPU', '1760256144_Rtx 5090.jpg', '1760332259_result.pdf', '2025-10-12 08:02:24'),
(5, 2, 1, 'rocky', '1760332037_istockphoto-825383494-612x612.jpg', '1760332811_result.pdf', '2025-10-13 05:07:17'),
(6, 2, 1, 'rocky', '1760332116_istockphoto-825383494-612x612.jpg', '1760332811_result.pdf', '2025-10-13 05:08:36'),
(7, 2, 1, 'yeah', '1760332148_istockphoto-825383494-612x612.jpg', '1760332811_result.pdf', '2025-10-13 05:09:08'),
(8, 2, 1, 'yeah', '1760332246_istockphoto-825383494-612x612.jpg', '1760332811_result.pdf', '2025-10-13 05:10:46'),
(9, 24, 3, 'rock', '1760332259_istockphoto-825383494-612x612.jpg', '1760332259_result.pdf', '2025-10-13 05:10:59'),
(10, 2, 1, 'yea rock', '1760332811_istockphoto-825383494-612x612.jpg', '1760332811_result.pdf', '2025-10-13 05:20:11'),
(11, 24, 3, 'GPU', '1760352757_ROG-SS.jpg', NULL, '2025-10-13 10:52:37'),
(13, 24, 3, 'sakit tiyan', '1760365216_istockphoto-825383494-612x612.jpg', NULL, '2025-10-13 14:20:16'),
(14, 2, 1, 'yeah', '1760367627_result.pdf', NULL, '2025-10-13 15:00:27'),
(15, 2, 1, 'yeah', '1760367631_result.pdf', NULL, '2025-10-13 15:00:31'),
(16, 24, 3, 'nice', '1760367640_result.pdf', NULL, '2025-10-13 15:00:40'),
(17, 22, 3, 'ge', '1760367785_result.pdf', NULL, '2025-10-13 15:03:05'),
(18, 24, 3, 'test 1', '1760368194_result.pdf', NULL, '2025-10-13 15:09:54'),
(19, 24, 3, 'Gold Roger was known as the \"Pirate King,\" the strongest and most infamous being to have sailed the Grand Line. The capture and execution of Roger by the World Government brought a change throughout the world. His last words before his death revealed the existence of the greatest treasure in the world, One Piece. It was this revelation that brought about the Grand Age of Pirates, men who dreamed of finding One Piece—which promises an unlimited amount of riches and fame—and quite possibly the pinnacle of glory and the title of the Pirate King. Enter Monkey Luffy, a 17-year-old boy who defies your standard definition of a pirate', '1760368310_result.pdf', NULL, '2025-10-13 15:11:50'),
(20, 24, 3, 'this is your final result with your physical examination.', '1760368854_result.pdf', NULL, '2025-10-13 15:20:54'),
(21, 24, 3, 'Drug Name & Strength	Amoxicillin 500 mg tablets\r\nSig. (Directions to Patient)	Take one (1) tablet by mouth (PO) three times a day (TID) for 10 days.\r\nDisp. (Quantity to Dispense)	Dispense #30 (Thirty) tablets\r\nRefills	0 (Zero)\r\nDispense as Written (DAW)	(Checked or written out if brand name is required)\r\nIndication (Optional)	For Strep Throat', '1760369554_result.pdf', NULL, '2025-10-13 15:32:34'),
(22, 24, 3, 'yeah', '1760369707_result.pdf', NULL, '2025-10-13 15:35:07'),
(23, 24, 3, 'You\'re now okay.', '1760369731_result.pdf', NULL, '2025-10-13 15:35:31'),
(24, 26, 3, 'lugia', '1760370848_result.pdf', NULL, '2025-10-13 15:54:08'),
(25, 24, 3, 'pokemon mew', '1760370978_result.pdf', NULL, '2025-10-13 15:56:18'),
(26, 24, 3, 'Impression:\r\nCOPD. No acute pulmonary disease.\r\nFindings:\r\nthe lungs are clear. there is hyperinflation of the lungs. there is no pleural effusion or pneumothorax. the heart and mediastinum\r\nare normal. the skeletal structures are normal.\r\nLabels:\r\nhyperinflation; chronic obstructive;\r\ncopd; pulmonary disease', '1760543088_result.pdf', NULL, '2025-10-15 15:44:48'),
(27, 24, 3, 'Impression:\r\nCOPD. No acute pulmonary disease.\r\nFindings:\r\nthe lungs are clear. there is hyperinflation of the lungs. there is no pleural effusion or pneumothorax. the heart and mediastinum\r\nare normal. the skeletal structures are normal.\r\nLabels:\r\nhyperinflation; chronic obstructive;\r\ncopd; pulmonary disease', '1760543132_result.pdf', NULL, '2025-10-15 15:45:32'),
(28, 24, 3, 'Impression:\r\nCOPD. No acute pulmonary disease.\r\nFindings:\r\nthe lungs are clear. there is hyperinflation of the lungs. there is no pleural effusion or pneumothorax. the heart and mediastinum\r\nare normal. the skeletal structures are normal.\r\nLabels:\r\nhyperinflation; chronic obstructive;\r\ncopd; pulmonary disease', '1760543152_result.pdf', NULL, '2025-10-15 15:45:52'),
(29, 2, 1, 'yeah', '1760583709_result.pdf', NULL, '2025-10-16 03:01:49'),
(30, 2, 1, 'logo', '1760584205_result.pdf', NULL, '2025-10-16 03:10:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appointment_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `results_ibfk_1` (`appointment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
