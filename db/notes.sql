-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 25, 2024 at 04:50 PM
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

--
-- Dumping data for table `notes`
--

INSERT INTO `notes` (`id`, `userID`, `eventID`, `title`, `description`, `file`, `created_at`, `updated_at`) VALUES
(1, '117878659336716057989', 8, 'Test.', 'Additional Notes\nThis example assumes you have set up a session and userID is available in the session.\neventID is expected to be passed via the URL ($_GET).\nMake sure the uploads folder is correctly configured to store and serve the PDF files.\nBy following these steps, you will have a responsive, dynamically generated list of notes for a user and event displayed in a card format using Tailwind CSS.', 'file_667acfd3954ee3.36515331.pdf', '2024-06-25 11:10:27', '2024-06-25 14:43:17'),
(2, '117878659336716057989', 8, 'Note Title.', 'To modify the description to show only the first 100 characters and include a \"Read more\" link that expands the full text, you can use JavaScript along with PHP. This approach will handle the truncation in PHP and the expansion in JavaScript.', 'file_667ad52def5bf7.72534171.pdf', '2024-06-25 11:33:17', '2024-06-25 14:42:56');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eventID` (`eventID`),
  ADD KEY `notes_ibfk_1` (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`google_id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
