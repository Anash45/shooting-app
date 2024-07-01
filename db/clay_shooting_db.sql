-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 01, 2024 at 02:13 AM
-- Server version: 8.0.35
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `clay_shooting_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `ammo`
--

CREATE TABLE `ammo` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ammo`
--

INSERT INTO `ammo` (`id`, `name`) VALUES
(1, 'Federal Top Gun 1145fps 1 1/8 oz. Size 8'),
(2, 'Federal Top Gun 1145fps 1 1/8 oz. Size 7.5'),
(3, 'Federal Top Gun 1200fps 1 1/8 oz. Size 8'),
(4, 'Federal Top Gun 1200fps 1 1/8 oz. Size 7.5'),
(5, 'Winchester AA 1200fps 1 1/8 oz. Size 8'),
(6, 'Winchester AA 1200fps 1 1/8 oz. Size 7.5'),
(7, 'Federal HOA 1200fps 1 1/8 oz. Size 7.5'),
(8, 'Estate 1200fps 1 1/8 oz. Size 7.5'),
(9, 'Remington Gun Club 1145fps 1 1/8 oz. Size 8'),
(10, 'Remington Gun Club 1145fps 1 1/8 oz. Size 7.5'),
(11, 'Fiochi Shooting Dynamics 1200fps 1 1/8 oz. Size 8'),
(12, 'Fiochi Shooting Dynamics 1200fps 1 1/8 oz. Size 7.5'),
(13, 'Fiochi Shooting Dynamics 1145fps 1 1/8 oz. Size 8'),
(14, 'Fiochi Shooting Dynamics 1145fps 1 1/8 oz. Size 7.5');

-- --------------------------------------------------------

--
-- Table structure for table `ears`
--

CREATE TABLE `ears` (
  `id` int NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ears`
--

INSERT INTO `ears` (`id`, `type`) VALUES
(1, 'Standard'),
(2, 'Passive'),
(3, 'Foam'),
(4, 'Music'),
(5, 'Foam/Music');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int NOT NULL,
  `user_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '1',
  `type` int DEFAULT NULL,
  `date` date NOT NULL,
  `location` int DEFAULT NULL,
  `weather` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ammo` int DEFAULT NULL,
  `poi` int DEFAULT NULL,
  `glasses` int DEFAULT NULL,
  `ears` int DEFAULT NULL,
  `totalShots` int NOT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `glasses`
--

CREATE TABLE `glasses` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glasses`
--

INSERT INTO `glasses` (`id`, `name`) VALUES
(1, 'Ranger'),
(2, 'Pilla 53CIK'),
(3, 'Pilla 78CIHC'),
(4, 'Pilla15CIHC');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `id` int NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`id`, `location`) VALUES
(1, 'Watervliet Fish and Game Protective Association - 70 Rifle Range Rd, Colonie, NY 12205'),
(2, 'West Albany Rod & Gun Club Inc - 100 Willoughby Dr, Albany, NY 12205'),
(3, 'Voorheesville Rod & Gun Club - 52 Foundry Rd, Voorheesville, NY 12186'),
(4, 'A R Sportsman\'s Club. - 79 UDELL RD Reidsville, NY 12193'),
(5, 'Iroquois Rod & Gun Club - 590 Feuz Rd, Schenectady, NY 12306'),
(6, 'Guan Ho Ha Fish and Game Club - 1451 Rector Rd, Scotia, NY 12302'),
(7, 'Elsmere Rod & Gun Club - 3131 Delaware TurnpikeVoorheesville, NY 12186'),
(8, 'NYS ATA Shooting Grounds - 7400 Bullt Street, Bridgeport NY 13030'),
(9, 'Pennsylvania State Shotgunning Association - 405 Monastery Rd, Elysburg, PA 17824'),
(10, 'Sportsman Club Of Clifton Park - 644 Englemore Rd, Clifton Park, NY 12065'),
(11, 'Pine Belt Sportsman\'s Club - 377 Stokes Rd, Shamong, NJ 08088'),
(12, 'Hartford Gun Club - 157 S Main St, East Granby, CT 06026'),
(13, 'World Shooting and Recreational Complex - 1 Main Event Ln, Sparta, IL 62286');

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` int NOT NULL,
  `userID` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `eventID` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `poi`
--

CREATE TABLE `poi` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poi`
--

INSERT INTO `poi` (`id`, `name`) VALUES
(1, 'Select POI'),
(2, '50/50'),
(3, '60/40'),
(4, '70/30'),
(5, '80/20'),
(6, '90/10'),
(7, '110/110'),
(8, '120/120');

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE `rounds` (
  `roundID` int NOT NULL,
  `eventID` int NOT NULL,
  `round_type` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rounds` int NOT NULL,
  `createdAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `type`
--

INSERT INTO `type` (`id`, `name`) VALUES
(1, 'Practice'),
(2, 'Registered'),
(3, 'Misc.');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `google_id`, `email`, `name`, `picture`, `created_at`) VALUES
(1, '117878659336716057989', 'f4futuretech@gmail.com', 'Future Tech', 'https://lh3.googleusercontent.com/a/ACg8ocJtkK_9hgj1WvOaTM3Mzl4JQ2ITFFD-aJRyK-d_DU8Hiz5gg3yl=s96-c', '2024-06-11 20:47:03'),
(2, '102918864814109964815', 'anas14529@gmail.com', 'Future Tech (Anas Bukhari)', 'https://lh3.googleusercontent.com/a/ACg8ocJkklSxtBNQwXUE8IJUiZKHkTe22q6D2f4KEasjxMIEluDvWdRotA=s96-c', '2024-06-11 20:47:43');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ammo`
--
ALTER TABLE `ammo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ears`
--
ALTER TABLE `ears`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`),
  ADD KEY `fk_google_id` (`user_id`),
  ADD KEY `fk_event_type` (`type`),
  ADD KEY `fk_event_location` (`location`),
  ADD KEY `fk_event_poi` (`poi`),
  ADD KEY `fk_event_glasses` (`glasses`),
  ADD KEY `fk_event_ammo` (`ammo`),
  ADD KEY `fk_event_ears` (`ears`);

--
-- Indexes for table `glasses`
--
ALTER TABLE `glasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eventID` (`eventID`),
  ADD KEY `notes_ibfk_1` (`userID`);

--
-- Indexes for table `poi`
--
ALTER TABLE `poi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`roundID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ammo`
--
ALTER TABLE `ammo`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ears`
--
ALTER TABLE `ears`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `glasses`
--
ALTER TABLE `glasses`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `poi`
--
ALTER TABLE `poi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `roundID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=164;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_ammo` FOREIGN KEY (`ammo`) REFERENCES `ammo` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_ears` FOREIGN KEY (`ears`) REFERENCES `ears` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_glasses` FOREIGN KEY (`glasses`) REFERENCES `glasses` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_location` FOREIGN KEY (`location`) REFERENCES `locations` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_poi` FOREIGN KEY (`poi`) REFERENCES `poi` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_event_type` FOREIGN KEY (`type`) REFERENCES `type` (`id`) ON DELETE SET NULL ON UPDATE SET NULL,
  ADD CONSTRAINT `fk_google_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`google_id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`google_id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
