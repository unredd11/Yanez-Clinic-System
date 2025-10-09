-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 07:38 PM
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_id`, `patient_id`, `service`, `appointment_date`, `appointment_time`, `status`, `appointment_details`, `updated_at`) VALUES
(1, 1, 'Physical Examination', '2025-09-30', '16:00:00', 'Completed', 'headache', '2025-10-09 17:10:36'),
(2, 1, 'Laboratory Testing', '2025-09-30', '15:00:00', 'Pending', 'sakit tiyan', '2025-10-09 17:05:20'),
(7, 1, 'Physical Examination', '2025-10-15', '03:00:00', 'Pending', 'sakit tiyan', '2025-10-09 17:05:14'),
(9, 1, 'Laboratory Testing', '2025-10-22', '15:30:00', 'Pending', 'urine acid', '2025-10-09 17:03:43'),
(10, 1, 'Physical Examination', '2025-10-17', '03:00:00', 'Pending', 'headache', '2025-10-09 17:03:43'),
(11, 1, 'X-ray & Diagnostics', '2025-10-25', '10:30:00', 'Pending', 'chest pain', '2025-10-09 17:05:17'),
(12, 1, 'Physical Examination', '2025-10-10', '09:00:00', 'Accepted', 'sakit mata', '2025-10-09 17:07:26'),
(13, 1, 'X-Ray', '2025-10-10', '08:00:00', 'Accepted', 'arm xray', '2025-10-09 17:10:24');

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
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `first_name`, `last_name`, `email`, `phone_number`, `birthdate`, `username`, `password`) VALUES
(1, 'Eddison', 'Abragan', 'eddison2x@gmail.com', '09059596798', '2005-08-11', 'edd2x', '$2y$10$KSM3erh49p5dk7bvH/krPuGgSBkfcRE3h/gOvWRB4vxGyqNPay2QW'),
(2, 'Eddison', 'Abragan', 'gmyuriel@gmail.com', '09059596798', '2005-08-11', 'eddison2x', '$2y$10$MvMNx0dyvmX0HT4zeRvuZe.w00hbM.TGOFW7222datIJS9A2dlyWe');

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
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`result_id`, `appointment_id`, `patient_id`, `result_text`, `result_file`, `created_at`) VALUES
(1, 2, 1, 'ok naka', '1759938402_yanezlogo_white.png', '2025-10-08 15:46:42'),
(2, 7, 1, 'robot', '1759938528_mobile-ss.jpg', '2025-10-08 15:48:48'),
(3, 2, 1, 'pokemon', '1760015297_png-clipart-pokemon-gold-and-silver-pokemon-crystal-growlithe-pixel-art-pokemon-text-carnivoran-thumbnail-removebg-preview.png', '2025-10-09 13:08:17');

--
-- Indexes for dumped tables
--

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
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appointment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `results`
--
ALTER TABLE `results`
  ADD CONSTRAINT `results_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointment` (`appointment_id`),
  ADD CONSTRAINT `results_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
