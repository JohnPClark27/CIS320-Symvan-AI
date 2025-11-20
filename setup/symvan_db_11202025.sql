-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 20, 2025 at 08:33 PM
-- Server version: 8.0.43-0ubuntu0.24.04.2
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symvan_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `action_description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `affected_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action_description`, `affected_id`, `created_at`) VALUES
(1, 8, 'User logged in', 8, '2025-11-18 01:40:52'),
(2, 8, 'Created new task', 10, '2025-11-18 01:42:52'),
(3, 8, 'User logged in', 8, '2025-11-18 02:05:31'),
(4, 8, 'User logged in', 8, '2025-11-18 02:13:11'),
(5, 8, 'User logged in', 8, '2025-11-18 02:13:55'),
(6, 8, 'User logged in', 8, '2025-11-18 02:14:10'),
(7, 14, 'Created new user account', 14, '2025-11-18 02:15:55'),
(8, 14, 'User logged in', 14, '2025-11-18 02:16:03'),
(9, 8, 'User logged in', 8, '2025-11-18 02:17:41'),
(10, 8, 'User logged in', 8, '2025-11-18 02:19:48'),
(11, 15, 'Created new user account', 15, '2025-11-18 02:20:10'),
(12, 15, 'User logged in', 15, '2025-11-18 02:20:22'),
(13, 8, 'User logged in', 8, '2025-11-18 02:26:04'),
(14, 8, 'User logged in', 8, '2025-11-18 02:36:03'),
(15, 8, 'User logged in', 8, '2025-11-18 14:37:36'),
(16, 8, 'Created new event', 13, '2025-11-18 14:40:31'),
(17, 8, 'Created new event', 14, '2025-11-18 14:41:25'),
(18, 8, 'Created new event', 15, '2025-11-18 14:42:27'),
(19, 8, 'Enrolled in event', 13, '2025-11-18 14:42:52'),
(20, 8, 'Added new task', 13, '2025-11-18 14:43:24'),
(21, 8, 'Updated task status', 11, '2025-11-18 14:43:28'),
(22, 8, 'Added new task', 13, '2025-11-18 14:43:39'),
(23, 8, 'Deleted task', 12, '2025-11-18 14:43:43'),
(24, 8, 'Added new task', 13, '2025-11-18 14:43:51'),
(25, 8, 'Added new task', 13, '2025-11-18 14:44:00'),
(26, 8, 'Deleted task', 14, '2025-11-18 14:44:03'),
(27, 8, 'Added new task', 13, '2025-11-18 14:44:47'),
(28, 8, 'Updated task status', 15, '2025-11-18 14:44:49'),
(29, 8, 'User logged in', 8, '2025-11-18 14:50:28'),
(30, 16, 'Created new user account', 16, '2025-11-18 15:28:22'),
(31, 16, 'User logged in', 16, '2025-11-18 15:28:48'),
(32, 16, 'Enrolled in event', 13, '2025-11-18 15:29:25'),
(33, 16, 'Joined organization as Member', 1, '2025-11-18 15:30:02'),
(34, 16, 'Left organization', 1, '2025-11-18 15:31:03'),
(35, 16, 'Joined organization as Admin', 1, '2025-11-18 15:31:31'),
(36, 16, 'Created new event', 16, '2025-11-18 15:32:21'),
(37, 16, 'Added new task', 16, '2025-11-18 15:33:01'),
(38, 16, 'Added new task', 16, '2025-11-18 15:33:27'),
(39, 16, 'Added new task', 16, '2025-11-18 15:33:42'),
(40, 16, 'Updated task status', 16, '2025-11-18 15:33:54'),
(41, 16, 'Updated task status', 17, '2025-11-18 15:33:57'),
(42, 16, 'Deleted task', 17, '2025-11-18 15:34:09'),
(43, 16, 'User logged in', 16, '2025-11-18 15:36:10'),
(44, 8, 'User logged in', 8, '2025-11-18 17:01:05'),
(45, 8, 'Updated event status to \'Posted\'', 16, '2025-11-18 17:01:21'),
(46, 8, 'Updated event status to \'Draft\'', 16, '2025-11-18 17:01:24'),
(47, 8, 'User logged in', 8, '2025-11-19 13:53:36'),
(48, 8, 'User logged in', 8, '2025-11-19 14:46:37'),
(49, 8, 'Enrolled in event', 14, '2025-11-19 14:48:52'),
(50, 8, 'Unenrolled from event', 14, '2025-11-19 14:48:55'),
(51, 8, 'Enrolled in event', 14, '2025-11-19 14:49:21'),
(52, 8, 'Unenrolled from event', 14, '2025-11-19 14:49:23'),
(53, 8, 'Unenrolled from event', 13, '2025-11-19 14:49:24'),
(54, 8, 'Enrolled in event', 13, '2025-11-19 14:49:28'),
(55, 8, 'Enrolled in event', 14, '2025-11-19 14:49:28'),
(56, 8, 'Unenrolled from event', 14, '2025-11-19 14:49:29'),
(57, 8, 'Enrolled in event', 14, '2025-11-19 14:49:37'),
(58, 8, 'Unenrolled from event', 14, '2025-11-19 14:49:43'),
(59, 8, 'Enrolled in event', 14, '2025-11-19 14:49:52'),
(60, 8, 'Unenrolled from event', 14, '2025-11-19 14:49:58'),
(61, 8, 'Unenrolled from event', 13, '2025-11-19 14:49:59'),
(62, 8, 'Enrolled in event', 14, '2025-11-19 14:50:04'),
(63, 8, 'Unenrolled from event', 14, '2025-11-19 14:50:13'),
(64, 8, 'Enrolled in event', 14, '2025-11-19 14:50:18'),
(65, 8, 'Enrolled in event', 13, '2025-11-19 14:50:27'),
(66, 8, 'Unenrolled from event', 14, '2025-11-19 14:50:46'),
(67, 8, 'User logged in', 8, '2025-11-19 16:07:46'),
(68, 8, 'User logged in', 8, '2025-11-19 17:46:26'),
(69, 17, 'Created new user account', 17, '2025-11-19 18:27:14'),
(70, 17, 'User logged in', 17, '2025-11-19 18:27:27'),
(71, 17, 'Enrolled in event', 13, '2025-11-19 18:28:06'),
(72, 17, 'Enrolled in event', 14, '2025-11-19 18:28:06'),
(73, 17, 'Joined organization as Member', 1, '2025-11-19 18:28:36'),
(74, 17, 'Updated account password', 17, '2025-11-19 18:29:21'),
(75, 17, 'Left organization', 1, '2025-11-19 18:29:32'),
(76, 17, 'Joined organization as Admin', 1, '2025-11-19 18:29:52'),
(77, 17, 'Created new event', 17, '2025-11-19 18:30:59'),
(78, 17, 'Updated event status to \'Posted\'', 17, '2025-11-19 18:31:25'),
(79, 17, 'Added new task', 17, '2025-11-19 18:31:52'),
(80, 17, 'Added new task', 17, '2025-11-19 18:32:09'),
(81, 17, 'Added new task', 17, '2025-11-19 18:32:32'),
(82, 17, 'Updated task status', 19, '2025-11-19 18:32:40'),
(83, 17, 'Updated task status', 21, '2025-11-19 18:32:44'),
(84, 17, 'Deleted task', 20, '2025-11-19 18:32:56'),
(85, 17, 'User logged in', 17, '2025-11-19 18:33:58'),
(86, 8, 'User logged in', 8, '2025-11-19 19:04:21'),
(87, 8, 'User logged in', 8, '2025-11-19 21:43:32'),
(88, 8, 'User logged in', 8, '2025-11-20 00:22:49'),
(89, 8, 'Updated profile information', NULL, '2025-11-20 00:37:18'),
(90, 8, 'User logged in', 8, '2025-11-20 02:02:31'),
(91, 8, 'User logged in', 8, '2025-11-20 14:24:34'),
(92, 8, 'Updated event status for event ID 15 to Posted', 15, '2025-11-20 14:24:47'),
(93, 8, 'Updated event status for event ID 15 to Draft', 15, '2025-11-20 14:24:49'),
(94, 18, 'User account created', 18, '2025-11-20 14:30:09'),
(95, 18, 'User logged in', 18, '2025-11-20 14:30:22'),
(96, 18, 'Enrolled in event ID 13', 13, '2025-11-20 14:30:42'),
(97, 18, 'Enrolled in event ID 14', 14, '2025-11-20 14:30:42'),
(98, 18, 'Joined organization ID 1 as Member', 1, '2025-11-20 14:31:13'),
(99, 18, 'Changed account password', NULL, '2025-11-20 14:31:55'),
(100, 18, 'Joined organization ID 1 as Admin', 1, '2025-11-20 14:32:20'),
(101, 18, 'Created event \'Tutoring Session\' for organization ID 1', 1, '2025-11-20 14:33:37'),
(102, 18, 'Updated event status for event ID 18 to Posted', 18, '2025-11-20 14:33:58'),
(103, 18, 'Added new task', 22, '2025-11-20 14:34:11'),
(104, 18, 'Added new task', 23, '2025-11-20 14:34:22'),
(105, 18, 'Updated task status for task ID 22 to In Progress', 18, '2025-11-20 14:34:28'),
(106, 18, 'Updated task status for task ID 23 to Completed', 18, '2025-11-20 14:34:31'),
(107, 18, 'Deleted task', 23, '2025-11-20 14:34:36'),
(108, 18, 'User logged in', 18, '2025-11-20 14:36:14'),
(109, 8, 'User logged in', 8, '2025-11-20 14:52:46');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `event_id` int NOT NULL,
  `enrolled_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`id`, `user_id`, `event_id`, `enrolled_at`) VALUES
(25, 16, 13, '2025-11-18 15:29:25'),
(34, 8, 13, '2025-11-19 14:50:27'),
(35, 17, 13, '2025-11-19 18:28:06'),
(36, 17, 14, '2025-11-19 18:28:06'),
(37, 18, 13, '2025-11-20 14:30:42'),
(38, 18, 14, '2025-11-20 14:30:42');

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

CREATE TABLE `event` (
  `id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `organization_id` int NOT NULL,
  `details` text COLLATE utf8mb4_general_ci,
  `date` datetime DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `location` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Draft',
  `attendees` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event`
--

INSERT INTO `event` (`id`, `name`, `organization_id`, `details`, `date`, `start_time`, `end_time`, `location`, `status`, `attendees`) VALUES
(13, 'CIS/Data Science Kickoff Meeting', 1, 'Meeting for the Computer Information Science and Data Science department.', '2025-11-20 00:00:00', '11:00:00', '00:00:00', 'Burns 321', 'Posted', 4),
(14, 'CIS Club Meeting', 1, 'Discussing research project.', '2025-11-24 00:00:00', '17:00:00', '18:00:00', 'Ott 123', 'Posted', 2),
(15, 'CIS Department Dinner', 1, 'Dinner for everyone in the CIS department.', '2025-11-26 00:00:00', '17:30:00', '00:00:00', 'Ott Atrium', 'Draft', 0),
(16, 'Test event', 1, 'test', '2025-11-19 00:00:00', '11:32:00', '00:32:00', 'Commons', 'Draft', 0),
(17, 'Programming Meeting', 1, 'Meeting to discuss computer programming strategies.', '2025-12-03 00:00:00', '19:00:00', '20:30:00', 'Ott Hall', 'Posted', 0),
(18, 'Tutoring Session', 1, 'Join Us for tutoring on Different Codes', '2025-11-25 00:00:00', '13:00:00', '00:00:00', 'Ott Hall Atrium', 'Posted', 0);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `organization_id` int NOT NULL,
  `permission_level` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `user_id`, `organization_id`, `permission_level`) VALUES
(14, 7, 1, 'Admin'),
(19, 9, 1, 'Admin'),
(20, 8, 1, 'Admin'),
(22, 11, 1, 'Admin'),
(24, 12, 1, 'Admin'),
(26, 16, 1, 'Admin'),
(28, 17, 1, 'Admin');

-- --------------------------------------------------------

--
-- Table structure for table `organization`
--

CREATE TABLE `organization` (
  `id` int NOT NULL,
  `name` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `password` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organization`
--

INSERT INTO `organization` (`id`, `name`, `description`, `password`) VALUES
(1, 'Computer Science Club', 'A club for students to build connections and learn more about special concepts in computer science.', 'testPW');

-- --------------------------------------------------------

--
-- Table structure for table `task`
--

CREATE TABLE `task` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('To Do','In Progress','Completed') COLLATE utf8mb4_general_ci DEFAULT 'To Do',
  `created_by` int DEFAULT NULL,
  `organization_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `event_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `task`
--

INSERT INTO `task` (`id`, `title`, `description`, `status`, `created_by`, `organization_id`, `created_at`, `event_id`) VALUES
(11, 'Prepare Presentation', '', 'In Progress', 8, NULL, '2025-11-18 14:43:24', 13),
(13, 'Buy Food', '- Pizza', 'To Do', 8, NULL, '2025-11-18 14:43:51', 13),
(15, 'Send out email invites.', '', 'Completed', 8, NULL, '2025-11-18 14:44:47', 13),
(16, 'Call CP', 'ensure security', 'In Progress', 16, NULL, '2025-11-18 15:33:01', 16),
(18, 'Confirm DJ', 'email', 'To Do', 16, NULL, '2025-11-18 15:33:42', 16),
(19, 'Reserve a clasroom', 'Reserve a location for the meeting in Ott hall', 'In Progress', 17, NULL, '2025-11-19 18:31:52', 17),
(21, 'Create an agenda', 'Write up the plan for what will be discussed during the meeting.', 'Completed', 17, NULL, '2025-11-19 18:32:32', 17),
(22, 'Find Tutor', '', 'In Progress', 18, NULL, '2025-11-20 14:34:11', 18);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `level` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'User',
  `username` varchar(80) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(120) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_hash` varchar(128) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `level`, `username`, `email`, `password_hash`) VALUES
(7, 'User', 'Ryan Junod', 'rryguy@gmail.com', '$2y$10$YK9N40NwCJupXFkgzUZtDeOrzlUWQvyEa2sSSepmE0x9cgxkInd5e'),
(8, 'User', 'John Clark', 'jpc198@icloud.com', '$2y$10$uJYXfV1YUgRNfs3W9GlSxun.fAyLHKHxCVo2XUShAINGeFdwR6qY.'),
(9, 'User', 'John Doe', 'mail@mail.com', '$2y$10$Llb9/5sw7/ORObLKtGkBuOG3MgEOvprRUzwinpr2brDyT5RV9Uu9O'),
(10, 'User', 'Freak Bob', 'freaky@bob.com', '$2y$10$25PE6IXPvb8OEii4pxB4Uuw3tm/ScTmp/FaPBTyKcXNQf8d8TP5VC'),
(11, 'User', 'hareld', 'hareld@com.com', '$2y$10$ZbwYbWULy2ZajFwvyN8nxeMzZaH1.m7T2sABfZ/AnsUGRe3icT2Qu'),
(12, 'User', 'Ryan J', 'r@gmail.com', '$2y$10$fnIFDV6v7yVfaDc5cuYS6eZ4R0oFsBWsbwfbpOiWp4zAdjotdAx4i'),
(13, 'User', 'Ryan J2', 'r@r.com', '$2y$10$QnW/VVduHugJmL7P3lM5.ehe.AwDBJO1/VtIrUdKThdzDyrERWBL.'),
(14, 'User', 'Test Account', 'test@test.test', '$2y$10$SnrPSa0XtByANUd3xn/w9ORVQqRjVE3fWLKhFAh6AXvKM6kkEWY8G'),
(15, 'User', 'Dan Dan', 'dan@gmail.com', '$2y$10$NIJCt.5zxtixTcDmkxecyeIZFpNdAEQgiHgE0UYbOHX2dxjvOl1Qi'),
(16, 'User', 'Jess Clark', 'jess.clark@indwes.edu', '$2y$10$rKGmOdlO0dGAi1IRogLnjO1wiHquPLJRl49DLLUVokMrg8Bso1.g6'),
(17, 'User', 'David Breyette', 'david.breyette@indwes.edu', '$2y$10$MIZzJKrlPwlmID3mgmtpPOMesD4s5mJvoemvx6Qeor7rQUhsVJL9K'),
(18, 'User', 'Jeremy Gross', 'jeremy.gross@indwes.edu', '$2y$10$s5KLEjmaBq5gm/hW19Ql7etxA1nTuAHWgs8oJ3F/mi1W8PkfLWAV2');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile`
--

CREATE TABLE `user_profile` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `phone` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `major` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `year` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `graduation_year` int DEFAULT NULL,
  `interests` text COLLATE utf8mb4_general_ci,
  `notify_email` tinyint(1) DEFAULT '1',
  `notify_reminder` tinyint(1) DEFAULT '1',
  `notify_weekly` tinyint(1) DEFAULT '1',
  `notify_sms` tinyint(1) DEFAULT '0',
  `notify_updates` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile`
--

INSERT INTO `user_profile` (`id`, `user_id`, `phone`, `major`, `year`, `graduation_year`, `interests`, `notify_email`, `notify_reminder`, `notify_weekly`, `notify_sms`, `notify_updates`) VALUES
(1, 7, '7656692467', '', 'Freshman', 0, 'Academic, Community Service', 1, 1, 1, 0, 1),
(7, 8, '7656181353', '1', 'Junior', 2027, 'Arts & Culture', 1, 1, 1, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_auditlog_user` (`user_id`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`event_id`),
  ADD KEY `fk_enrollment_event` (`event_id`);

--
-- Indexes for table `event`
--
ALTER TABLE `event`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `fk_event_organization` (`organization_id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uc_member` (`user_id`,`organization_id`),
  ADD KEY `fk_member_organization` (`organization_id`);

--
-- Indexes for table `organization`
--
ALTER TABLE `organization`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `task`
--
ALTER TABLE `task`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_task_user` (`created_by`),
  ADD KEY `fk_task_organization` (`organization_id`),
  ADD KEY `fk_task_event` (`event_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `event`
--
ALTER TABLE `event`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `organization`
--
ALTER TABLE `organization`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `task`
--
ALTER TABLE `task`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_profile`
--
ALTER TABLE `user_profile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `fk_auditlog_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `fk_enrollment_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_enrollment_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `event`
--
ALTER TABLE `event`
  ADD CONSTRAINT `fk_event_organization` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `member`
--
ALTER TABLE `member`
  ADD CONSTRAINT `fk_member_organization` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_member_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task`
--
ALTER TABLE `task`
  ADD CONSTRAINT `fk_task_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_organization` FOREIGN KEY (`organization_id`) REFERENCES `organization` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_task_user` FOREIGN KEY (`created_by`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_profile`
--
ALTER TABLE `user_profile`
  ADD CONSTRAINT `user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
