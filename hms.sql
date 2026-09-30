-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 27, 2026 at 05:16 PM
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
-- Database: `hms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `updationDate` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `updationDate`) VALUES
(1, 'admin', '$2y$10$Mf1nyZSTpNqfpf6.SkUwlukUlqshzTqfRnP0pX4XWR2pyjoL.sbSe', '04-03-2024 11:42:05 AM');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `id` int(11) NOT NULL,
  `doctorSpecialization` varchar(255) DEFAULT NULL,
  `doctorId` int(11) DEFAULT NULL,
  `userId` int(11) DEFAULT NULL,
  `consultancyFees` int(11) DEFAULT NULL,
  `appointmentDate` varchar(255) DEFAULT NULL,
  `appointmentTime` varchar(255) DEFAULT NULL,
  `postingDate` timestamp NULL DEFAULT current_timestamp(),
  `userStatus` int(11) DEFAULT NULL,
  `doctorStatus` int(11) DEFAULT NULL,
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `status` enum('Pending','Approved','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
  `status_updated_at` datetime DEFAULT NULL,
  `status_updated_by` varchar(80) DEFAULT NULL,
  `reschedule_reason` varchar(255) DEFAULT NULL,
  `cancel_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`id`, `doctorSpecialization`, `doctorId`, `userId`, `consultancyFees`, `appointmentDate`, `appointmentTime`, `postingDate`, `userStatus`, `doctorStatus`, `updationDate`, `status`, `status_updated_at`, `status_updated_by`, `reschedule_reason`, `cancel_reason`) VALUES
(1, 'ENT', 1, 1, 500, '2024-05-30', '9:15 AM', '2024-05-15 03:42:11', 1, 1, '2026-06-15 16:37:28', 'Completed', '2026-06-15 22:07:28', 'doctor:1', NULL, NULL),
(2, 'Endocrinologists', 2, 2, 800, '2024-05-31', '2:45 PM', '2024-05-16 09:08:54', 1, 0, '2026-06-15 17:00:32', 'Cancelled', '2026-06-15 22:30:32', 'admin:1', NULL, NULL),
(3, 'ENT', 1, 1, 500, '2026-06-16', '10:30:00', '2026-06-15 16:44:59', 1, 1, '2026-06-15 16:44:59', 'Completed', '2026-06-15 22:14:59', 'doctor:1', NULL, NULL),
(4, 'Dental Care', 8, 3, 800, '2026-06-20', '11:42:00', '2026-06-18 07:13:11', 1, 1, '2026-06-18 07:32:15', 'Completed', '2026-06-18 13:02:15', 'doctor:8', NULL, NULL),
(5, 'Dental Care', 9, 4, 500, '2026-06-22', '11:50:00', '2026-06-21 16:17:00', 1, 1, '2026-06-21 16:17:50', 'Completed', '2026-06-21 21:47:50', 'doctor:9', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `actor_type` varchar(30) NOT NULL,
  `actor_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(80) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `actor_type`, `actor_id`, `action`, `entity_type`, `entity_id`, `details`, `ip_address`, `created_at`) VALUES
(1, 'doctor', 1, 'appointment_status_changed', 'appointment', 1, 'Status changed to Completed', '::1', '2026-06-15 22:06:25'),
(2, 'doctor', 1, 'prescription_saved', 'appointment', 1, 'Prescription saved for John Doe', '::1', '2026-06-15 22:06:25'),
(3, 'doctor', 1, 'appointment_status_changed', 'appointment', 1, 'Status changed to Completed', '::1', '2026-06-15 22:06:51'),
(4, 'doctor', 1, 'appointment_status_changed', 'appointment', 1, 'Status changed to Completed', '::1', '2026-06-15 22:07:28'),
(5, 'doctor', 1, 'prescription_saved', 'appointment', 1, 'Prescription saved for John Doe', '::1', '2026-06-15 22:07:28'),
(6, 'doctor', 1, 'availability_created', 'doctor_availability', 1, 'Created availability slot.', '::1', '2026-06-15 22:09:38'),
(7, 'admin', 1, 'appointment_status_changed', 'appointment', 3, 'Status changed to Approved', '', '2026-06-15 22:14:59'),
(8, 'doctor', 1, 'appointment_status_changed', 'appointment', 3, 'Status changed to Completed', '', '2026-06-15 22:14:59'),
(9, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-15 22:20:47'),
(10, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-15 22:26:23'),
(11, 'admin', 1, 'appointment_status_changed', 'appointment', 2, 'Status changed to Cancelled', '::1', '2026-06-15 22:30:32'),
(12, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-16 07:34:07'),
(13, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-16 21:04:12'),
(14, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-18 12:38:02'),
(15, 'admin', 1, 'doctor_created', 'doctors', 8, 'Doctor created: Pankaj Sharma', '::1', '2026-06-18 12:40:41'),
(16, 'admin', 1, 'availability_created', 'doctor_availability', 3, 'Created doctor availability slot.', '::1', '2026-06-18 12:42:32'),
(17, 'patient', 3, 'appointment_created', 'appointment', 4, 'Booked with Dr. Pankaj Sharma', '::1', '2026-06-18 12:43:11'),
(18, 'doctor', 8, 'appointment_status_changed', 'appointment', 4, 'Status changed to Approved', '::1', '2026-06-18 12:51:27'),
(19, 'doctor', 8, 'appointment_status_changed', 'appointment', 4, 'Status changed to Completed', '::1', '2026-06-18 13:02:15'),
(20, 'doctor', 8, 'prescription_saved', 'appointment', 4, 'Prescription saved for John Karie', '::1', '2026-06-18 13:02:15'),
(21, 'doctor', 8, 'patient_created', 'tblpatient', 3, 'Patient created: Seema Nair', '::1', '2026-06-18 13:04:16'),
(22, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-18 13:15:44'),
(23, 'admin', 1, 'medical_history_added', 'tblpatient', 3, 'Admin added medical history.', '::1', '2026-06-18 13:31:20'),
(24, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-21 21:41:12'),
(25, 'admin', 1, 'admin_login', 'admin', 1, 'Admin logged in.', '::1', '2026-06-21 21:42:42'),
(26, 'admin', 1, 'doctor_created', 'doctors', 9, 'Doctor created: Garima Singh', '::1', '2026-06-21 21:44:46'),
(27, 'doctor', 9, 'availability_created', 'doctor_availability', 4, 'Created availability slot.', '::1', '2026-06-21 21:45:41'),
(28, 'patient', 4, 'appointment_created', 'appointment', 5, 'Booked with Dr. Garima Singh', '::1', '2026-06-21 21:47:00'),
(29, 'doctor', 9, 'appointment_status_changed', 'appointment', 5, 'Status changed to Completed', '::1', '2026-06-21 21:47:19'),
(30, 'doctor', 9, 'appointment_status_changed', 'appointment', 5, 'Status changed to Completed', '::1', '2026-06-21 21:47:50'),
(31, 'doctor', 9, 'prescription_saved', 'appointment', 5, 'Prescription saved for Amit', '::1', '2026-06-21 21:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `specilization` varchar(255) DEFAULT NULL,
  `doctorName` varchar(255) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `docFees` varchar(255) DEFAULT NULL,
  `contactno` bigint(11) DEFAULT NULL,
  `docEmail` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `specilization`, `doctorName`, `address`, `docFees`, `contactno`, `docEmail`, `password`, `creationDate`, `updationDate`) VALUES
(1, 'ENT', 'Anuj kumar', 'A 123 XYZ Apartment Raj Nagar Ext Ghaziabad', '500', 142536250, 'anujk123@test.com', '$2y$10$N9HN4tPs/lJZ.v/8BN0yxOiYJROLO7XsozsAdSJqREBNp2bLbtyXy', '2024-04-10 18:16:52', '2026-06-15 16:25:28'),
(2, 'Endocrinologists', 'Charu Dua', 'X 1212 ABC Apartment Laxmi Nagar New Delhi ', '800', 1231231230, 'charudua12@test.com', 'f925916e2754e5e03f75dd58a5733251', '2024-04-11 01:06:41', '2024-05-14 09:26:28'),
(4, 'Pediatrics', 'Priyanka Sinha', 'A 123 Xyz Aparmtnent Ghaziabad', '700', 74561235, 'p12@t.com', 'f925916e2754e5e03f75dd58a5733251', '2024-05-16 09:12:23', NULL),
(5, 'Orthopedics', 'Vipin Tayagi', 'Yasho Hospital New Delhi', '1200', 95214563210, 'vpint123@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-05-16 09:13:11', NULL),
(6, 'Internal Medicine', 'Dr Romil', 'Max Hospital Vaishali  GZB', '1500', 8563214751, 'drromil12@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-05-16 09:14:11', NULL),
(7, 'Obstetrics and Gynecology', 'Bhavya rathore', 'Shop 12 Indira Puram Ghaziabad', '800', 745621330, 'bhawya12@tt.com', 'f925916e2754e5e03f75dd58a5733251', '2024-05-16 09:15:18', NULL),
(8, 'Dental Care', 'Pankaj Sharma', 'P-907, Navi Market\r\nKanpur Uttar Pradeah', '800', 2255647899, 'pankaj@gmail.com', '$2y$10$P9bNKHNLlg/QJv9Jv6r42eaOOMen3Fzd5LoU.JGd4Ymy7pmhalsJK', '2026-06-18 07:10:41', NULL),
(9, 'Dental Care', 'Garima Singh', 'Abc mayur Vihar new delhi', '500', 5264123032, 'ga12@test.com', '$2y$10$wXLPRArKIuweC9fz1ibz0./zEC7v9bbbbaGdQdjv2VwQDPtkSBdky', '2026-06-21 16:14:46', '2026-06-21 16:19:38');

-- --------------------------------------------------------

--
-- Table structure for table `doctorslog`
--

CREATE TABLE `doctorslog` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `userip` binary(16) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT current_timestamp(),
  `logout` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctorslog`
--

INSERT INTO `doctorslog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(1, 1, 'anujk123@test.com', 0x3a3a3100000000000000000000000000, '2024-05-16 05:19:33', NULL, 1),
(2, 1, 'anujk123@test.com', 0x3a3a3100000000000000000000000000, '2024-05-16 09:01:03', '16-05-2024 02:37:32 PM', 1),
(3, 1, 'anujk123@test.com', 0x3a3a3100000000000000000000000000, '2026-06-15 16:25:28', NULL, 1),
(4, 1, 'anujk123@test.com', 0x3a3a3100000000000000000000000000, '2026-06-15 16:31:47', NULL, 1),
(5, NULL, 'anuj@gmail.com', 0x3a3a3100000000000000000000000000, '2026-06-18 07:04:53', NULL, 0),
(6, 8, 'pankaj@gmail.com', 0x3a3a3100000000000000000000000000, '2026-06-18 07:16:50', '18-06-2026 01:09:38 PM', 1),
(7, 9, 'ga12@test.com', 0x3a3a3100000000000000000000000000, '2026-06-21 16:14:57', '21-06-2026 09:49:45 PM', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctorspecilization`
--

CREATE TABLE `doctorspecilization` (
  `id` int(11) NOT NULL,
  `specilization` varchar(255) DEFAULT NULL,
  `creationDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `doctorspecilization`
--

INSERT INTO `doctorspecilization` (`id`, `specilization`, `creationDate`, `updationDate`) VALUES
(1, 'Orthopedics', '2024-04-09 18:09:46', '2024-05-14 09:26:47'),
(2, 'Internal Medicine', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(3, 'Obstetrics and Gynecology', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(4, 'Dermatology', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(5, 'Pediatrics', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(6, 'Radiology', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(7, 'General Surgery', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(8, 'Ophthalmology', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(9, 'Anesthesia', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(10, 'Pathology', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(11, 'ENT', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(12, 'Dental Care', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(13, 'Dermatologists', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(14, 'Endocrinologists', '2024-04-09 18:09:46', '2024-05-14 09:26:56'),
(15, 'Neurologists', '2024-04-09 18:09:46', '2024-05-14 09:26:56');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL,
  `available_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `slot_duration_minutes` int(11) NOT NULL DEFAULT 30,
  `max_appointments` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_id`, `day_of_week`, `available_date`, `start_time`, `end_time`, `slot_duration_minutes`, `max_appointments`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-20', '11:10:00', '14:30:00', 10, 50, 1, '2026-06-15 22:09:38', NULL),
(2, 1, 2, '2026-06-16', '09:00:00', '12:00:00', 30, 3, 1, '2026-06-15 22:14:59', NULL),
(3, 8, 0, '2026-06-20', '09:30:00', '18:30:00', 30, 10, 1, '2026-06-18 12:42:32', NULL),
(4, 9, 1, '2026-06-22', '10:30:00', '12:50:00', 25, 10, 1, '2026-06-21 21:45:41', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_type`, `user_id`, `title`, `message`, `is_read`, `created_at`, `read_at`) VALUES
(1, 'patient', 1, 'Prescription available', 'Your prescription for appointment #1 is ready to download.', 0, '2026-06-15 22:06:25', NULL),
(2, 'patient', 1, 'Appointment status updated', 'Your appointment is now Completed.', 0, '2026-06-15 22:06:51', NULL),
(3, 'admin', NULL, 'Appointment status updated', 'Doctor updated appointment #1 to Completed.', 1, '2026-06-15 22:06:51', '2026-06-21 21:49:01'),
(4, 'patient', 1, 'Prescription available', 'Your prescription for appointment #1 is ready to download.', 0, '2026-06-15 22:07:28', NULL),
(5, 'patient', 1, 'Workflow verification', 'Prescription workflow verified.', 0, '2026-06-15 22:14:59', NULL),
(6, 'patient', 2, 'Appointment status updated', 'Your appointment is now Cancelled.', 0, '2026-06-15 22:30:32', NULL),
(7, 'doctor', 2, 'Appointment status updated', 'Appointment #2 is now Cancelled.', 0, '2026-06-15 22:30:32', NULL),
(8, 'admin', NULL, 'New appointment request', 'A patient booked appointment #4.', 1, '2026-06-18 12:43:11', '2026-06-21 21:49:01'),
(9, 'doctor', 8, 'New appointment request', 'A patient booked appointment #4.', 0, '2026-06-18 12:43:11', NULL),
(10, 'patient', 3, 'Appointment status updated', 'Your appointment is now Approved.', 0, '2026-06-18 12:51:27', NULL),
(11, 'admin', NULL, 'Appointment status updated', 'Doctor updated appointment #4 to Approved.', 1, '2026-06-18 12:51:27', '2026-06-21 21:49:01'),
(12, 'patient', 3, 'Prescription available', 'Your prescription for appointment #4 is ready to download.', 0, '2026-06-18 13:02:15', NULL),
(13, 'admin', NULL, 'New appointment request', 'A patient booked appointment #5.', 1, '2026-06-21 21:47:00', '2026-06-21 21:49:01'),
(14, 'doctor', 9, 'New appointment request', 'A patient booked appointment #5.', 0, '2026-06-21 21:47:00', NULL),
(15, 'patient', 4, 'Appointment status updated', 'Your appointment is now Completed.', 0, '2026-06-21 21:47:19', NULL),
(16, 'admin', NULL, 'Appointment status updated', 'Doctor updated appointment #5 to Completed.', 1, '2026-06-21 21:47:19', '2026-06-21 21:49:01'),
(17, 'patient', 4, 'Prescription available', 'Your prescription for appointment #5 is ready to download.', 0, '2026-06-21 21:47:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_user_id` int(11) NOT NULL,
  `diagnosis` text NOT NULL,
  `treatment` text NOT NULL,
  `medicines` text NOT NULL,
  `notes` text DEFAULT NULL,
  `followup_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `appointment_id`, `doctor_id`, `patient_user_id`, `diagnosis`, `treatment`, `medicines`, `notes`, `followup_date`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Fever', 'Take Reset', 'Dolo 600 Mg', 'NA', '2026-06-30', '2026-06-15 22:06:25', '2026-06-15 22:07:28'),
(3, 3, 1, 1, 'Workflow verification diagnosis', 'Workflow verification treatment', 'Workflow verification medicine', 'Created during completion check', '2026-06-22', '2026-06-15 22:14:59', '2026-06-15 22:14:59'),
(4, 4, 8, 3, 'Have sensitivity in teeth', 'A cleaning session must be taken twice in month', 'Vinarge Tooth Paste and Mouth Gargle', 'Not take carbonated drinks', '2026-06-27', '2026-06-18 13:02:15', '2026-06-18 13:02:15'),
(5, 5, 9, 4, 'RCT', 'RCT in two teeths', 'RX Tab 1, Tab2', 'NA', '2026-06-26', '2026-06-21 21:47:50', '2026-06-21 21:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactus`
--

CREATE TABLE `tblcontactus` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `contactno` bigint(12) DEFAULT NULL,
  `message` mediumtext DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp(),
  `AdminRemark` mediumtext DEFAULT NULL,
  `LastupdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `IsRead` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactus`
--

INSERT INTO `tblcontactus` (`id`, `fullname`, `email`, `contactno`, `message`, `PostingDate`, `AdminRemark`, `LastupdationDate`, `IsRead`) VALUES
(1, 'Anuj kumar', 'anujk30@test.com', 1425362514, 'This is for testing purposes.   This is for testing purposes.This is for testing purposes.This is for testing purposes.This is for testing purposes.This is for testing purposes.This is for testing purposes.This is for testing purposes.This is for testing purposes.', '2024-04-20 16:52:03', NULL, '2024-05-14 09:27:15', NULL),
(2, 'Anuj kumar', 'ak@gmail.com', 1111122233, 'This is for testing', '2024-04-23 13:13:41', 'Contact the patient', '2024-04-27 13:13:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblmedicalhistory`
--

CREATE TABLE `tblmedicalhistory` (
  `ID` int(10) NOT NULL,
  `PatientID` int(10) DEFAULT NULL,
  `BloodPressure` varchar(200) DEFAULT NULL,
  `BloodSugar` varchar(200) NOT NULL,
  `Weight` varchar(100) DEFAULT NULL,
  `Temperature` varchar(200) DEFAULT NULL,
  `MedicalPres` mediumtext DEFAULT NULL,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblmedicalhistory`
--

INSERT INTO `tblmedicalhistory` (`ID`, `PatientID`, `BloodPressure`, `BloodSugar`, `Weight`, `Temperature`, `MedicalPres`, `CreationDate`) VALUES
(1, 2, '80/120', '110', '85', '97', 'Dolo,\r\nLevocit 5mg', '2024-05-16 09:07:16'),
(2, 3, '101/60', '110/120', '52', '98', 'uyuikhkjhjkgh', '2026-06-18 08:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `tblpage`
--

CREATE TABLE `tblpage` (
  `ID` int(10) NOT NULL,
  `PageType` varchar(200) DEFAULT NULL,
  `PageTitle` varchar(200) DEFAULT NULL,
  `PageDescription` mediumtext DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `MobileNumber` bigint(10) DEFAULT NULL,
  `UpdationDate` timestamp NULL DEFAULT current_timestamp(),
  `OpenningTime` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblpage`
--

INSERT INTO `tblpage` (`ID`, `PageType`, `PageTitle`, `PageDescription`, `Email`, `MobileNumber`, `UpdationDate`, `OpenningTime`) VALUES
(1, 'aboutus', 'About Us', '<ul style=\"padding: 0px; margin-right: 0px; margin-bottom: 1.313em; margin-left: 1.655em;\" times=\"\" new=\"\" roman\";=\"\" font-size:=\"\" 14px;=\"\" text-align:=\"\" center;=\"\" background-color:=\"\" rgb(255,=\"\" 246,=\"\" 246);\"=\"\"><li style=\"text-align: left;\"><font color=\"#000000\">The Hospital Management System (HMS) is designed for Any Hospital to replace their existing manual, paper based system. The new system is to control the following information; patient information, room availability, staff and operating room schedules, and patient invoices. These services are to be provided in an efficient, cost effective manner, with the goal of reducing the time and resources currently required for such tasks.</font></li><li style=\"text-align: left;\"><font color=\"#000000\">A significant part of the operation of any hospital involves the acquisition, management and timely retrieval of great volumes of information. This information typically involves; patient personal information and medical history, staff information, room and ward scheduling, staff scheduling, operating theater scheduling and various facilities waiting lists. All of this information must be managed in an efficient and cost wise fashion so that an institution\'s resources may be effectively utilized HMS will automate the management of the hospital making it more efficient and error free. It aims at standardizing data, consolidating data ensuring data integrity and reducing inconsistencies.&nbsp;</font></li></ul>', NULL, NULL, '2020-05-20 07:21:52', NULL),
(2, 'contactus', 'Contact Details', 'D-204, Hole Town South West, Delhi-110096,India', 'info@gmail.com', 1122334455, '2020-05-20 07:24:07', '9 am To 8 Pm');

-- --------------------------------------------------------

--
-- Table structure for table `tblpatient`
--

CREATE TABLE `tblpatient` (
  `ID` int(10) NOT NULL,
  `Docid` int(10) DEFAULT NULL,
  `PatientName` varchar(200) DEFAULT NULL,
  `PatientContno` bigint(10) DEFAULT NULL,
  `PatientEmail` varchar(200) DEFAULT NULL,
  `PatientGender` varchar(50) DEFAULT NULL,
  `PatientAdd` mediumtext DEFAULT NULL,
  `PatientAge` int(10) DEFAULT NULL,
  `PatientMedhis` mediumtext DEFAULT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpatient`
--

INSERT INTO `tblpatient` (`ID`, `Docid`, `PatientName`, `PatientContno`, `PatientEmail`, `PatientGender`, `PatientAdd`, `PatientAge`, `PatientMedhis`, `CreationDate`, `UpdationDate`) VALUES
(1, 1, 'Rahul Singyh', 452463210, 'rahul12@gmail.com', 'male', 'NA', 32, 'Fever, Cold', '2024-05-16 05:23:35', NULL),
(2, 1, 'Amit', 4545454545, 'amitk@gmail.com', 'male', 'NA', 45, 'Fever', '2024-05-16 09:01:26', NULL),
(3, 8, 'Seema Nair', 1562368974, 'seema@gmail.com', 'Female', 'G-809, Kamal Apartment Hari Nagar', 38, 'Not Yet', '2026-06-18 07:34:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `userlog`
--

CREATE TABLE `userlog` (
  `id` int(11) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `userip` binary(16) DEFAULT NULL,
  `loginTime` timestamp NULL DEFAULT current_timestamp(),
  `logout` varchar(255) DEFAULT NULL,
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `userlog`
--

INSERT INTO `userlog` (`id`, `uid`, `username`, `userip`, `loginTime`, `logout`, `status`) VALUES
(1, 1, 'johndoe12@test.com', 0x3a3a3100000000000000000000000000, '2024-05-15 03:41:48', NULL, 1),
(2, 2, 'amitk@gmail.com', 0x3a3a3100000000000000000000000000, '2024-05-16 09:08:06', '16-05-2024 02:41:06 PM', 1),
(3, 1, 'johndoe12@test.com', 0x3a3a3100000000000000000000000000, '2026-06-15 16:24:54', '15-06-2026 09:55:07 PM', 1),
(4, 1, 'johndoe12@test.com', 0x3a3a3100000000000000000000000000, '2026-06-15 16:30:42', '15-06-2026 10:01:25 PM', 1),
(5, NULL, 'anujk123@test.com', 0x3a3a3100000000000000000000000000, '2026-06-16 15:35:31', NULL, 0),
(6, 1, 'johndoe12@test.com', 0x3a3a3100000000000000000000000000, '2026-06-16 15:36:03', '16-06-2026 09:09:00 PM', 1),
(7, 3, 'john@gmail.com', 0x3a3a3100000000000000000000000000, '2026-06-18 06:41:51', '18-06-2026 12:46:02 PM', 1),
(8, 4, 'amit12@test.com', 0x3a3a3100000000000000000000000000, '2026-06-21 16:16:37', '21-06-2026 09:50:04 PM', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullName` varchar(255) DEFAULT NULL,
  `address` longtext DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `regDate` timestamp NULL DEFAULT current_timestamp(),
  `updationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullName`, `address`, `city`, `gender`, `email`, `password`, `regDate`, `updationDate`) VALUES
(1, 'John Doe', 'A 123 ABC Apartment GZB 201017', 'Ghaziabad', 'male', 'johndoe12@test.com', '$2y$10$2J0mUm83Va63L5fQ.i9Ekeuaat/EH2cRHPVheD8r3yGyeyssQdKIi', '2024-04-20 12:13:56', '2026-06-15 16:24:54'),
(2, 'Amit kumar', 'new Delhi india', 'New Delhi', 'male', 'amitk@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2024-04-21 13:15:32', '2024-05-14 09:28:23'),
(3, 'John Karie', 'K-908, Kavi Apartment, Kanpur(Uttar Pradesh)', 'Kanpur', 'male', 'john@gmail.com', '$2y$10$XzL2wJx1MRRlU32LYwEbce08uWvACAJ62fDfCBhfYLHR3Mnwqw5xS', '2026-06-18 06:33:15', NULL),
(4, 'Amit', 'New Delhi', 'Delhi', 'male', 'amit12@test.com', '$2y$10$TAFjLEAVIO4O/FPLbr288ePVb7rqaf89VqszIs4Q5goIaGOuL.22C', '2026-06-21 16:16:30', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_actor` (`actor_type`,`actor_id`),
  ADD KEY `idx_audit_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctorslog`
--
ALTER TABLE `doctorslog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_doctor_day` (`doctor_id`,`day_of_week`),
  ADD KEY `idx_doctor_date` (`doctor_id`,`available_date`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notifications_user` (`user_type`,`user_id`,`is_read`),
  ADD KEY `idx_notifications_created` (`created_at`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_prescription_appointment` (`appointment_id`),
  ADD KEY `idx_prescription_doctor` (`doctor_id`),
  ADD KEY `idx_prescription_patient` (`patient_user_id`),
  ADD KEY `idx_prescription_followup` (`followup_date`);

--
-- Indexes for table `tblcontactus`
--
ALTER TABLE `tblcontactus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpage`
--
ALTER TABLE `tblpage`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblpatient`
--
ALTER TABLE `tblpatient`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `userlog`
--
ALTER TABLE `userlog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `doctorslog`
--
ALTER TABLE `doctorslog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `doctorspecilization`
--
ALTER TABLE `doctorspecilization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tblcontactus`
--
ALTER TABLE `tblcontactus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblmedicalhistory`
--
ALTER TABLE `tblmedicalhistory`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblpage`
--
ALTER TABLE `tblpage`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblpatient`
--
ALTER TABLE `tblpatient`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `userlog`
--
ALTER TABLE `userlog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
