-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 02, 2022 at 01:12 PM
-- Server version: 8.0.23
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `advanced_dillbill`
--

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE `conversation` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `startsAt` varchar(255) NOT NULL,
  `endsAt` varchar(255) NOT NULL,
  `tutorId` int DEFAULT NULL,
  `tutorName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tutorEmail` varchar(255) DEFAULT NULL,
  `tutorImage` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `level` enum('beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced','usa-elementary','usa-pre-intermediate','usa-intermediate') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  `visible` enum('yes','no') NOT NULL,
  `eventId` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `conversationUsers`
--

CREATE TABLE `conversationUsers` (
  `id` int NOT NULL,
  `conversationId` int NOT NULL,
  `conversationLevel` varchar(255) DEFAULT NULL,
  `conversationDate` varchar(255) DEFAULT NULL,
  `startsAT` varchar(255) DEFAULT NULL,
  `conversationTopic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tutorName` varchar(255) DEFAULT NULL,
  `tutorImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `userName` varchar(255) DEFAULT NULL,
  `userEmail` varchar(255) DEFAULT NULL,
  `action` enum('reserve','enter','cancel') NOT NULL DEFAULT 'reserve',
  `userId` int NOT NULL,
  `requestDate` date NOT NULL,
  `requestTime` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `developerSettings`
--

CREATE TABLE `developerSettings` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL,
  `value` varchar(255) NOT NULL,
  `active` enum('yes','no') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int NOT NULL,
  `userId` int NOT NULL,
  `classId` int NOT NULL,
  `tutorId` int NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `score` int NOT NULL,
  `comment` text,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filemanager_mediafile`
--

CREATE TABLE `filemanager_mediafile` (
  `id` int NOT NULL,
  `filename` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `alt` text,
  `size` varchar(255) NOT NULL,
  `description` text,
  `thumbs` text,
  `created_at` int NOT NULL,
  `updated_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filemanager_mediafile_tag`
--

CREATE TABLE `filemanager_mediafile_tag` (
  `mediafile_id` int NOT NULL,
  `tag_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filemanager_owners`
--

CREATE TABLE `filemanager_owners` (
  `mediafile_id` int NOT NULL,
  `owner_id` int NOT NULL,
  `owner` varchar(255) NOT NULL,
  `owner_attribute` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `filemanager_tag`
--

CREATE TABLE `filemanager_tag` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `grammar`
--

CREATE TABLE `grammar` (
  `id` int NOT NULL,
  `description` varchar(555) DEFAULT NULL,
  `url` varchar(555) DEFAULT NULL,
  `level` enum('beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced') NOT NULL,
  `type` varchar(255) DEFAULT NULL,
  `orderNumber` int DEFAULT NULL,
  `active` enum('yes','no') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `packets`
--

CREATE TABLE `packets` (
  `id` int NOT NULL,
  `period` int NOT NULL,
  `lesson` int DEFAULT NULL,
  `nameKeyword` varchar(255) DEFAULT NULL,
  `descriptionKeyword` varchar(255) DEFAULT NULL,
  `usd` float NOT NULL,
  `azn` float NOT NULL,
  `try` float NOT NULL,
  `brl` float NOT NULL,
  `discountPercent` float DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `paymentActions`
--

CREATE TABLE `paymentActions` (
  `id` int NOT NULL,
  `userId` int NOT NULL,
  `userName` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `packetId` int NOT NULL,
  `packetName` varchar(255) DEFAULT NULL,
  `planId` int DEFAULT NULL,
  `planName` varchar(255) DEFAULT NULL,
  `scheduleId` int DEFAULT NULL,
  `scheduleName` varchar(255) DEFAULT NULL,
  `priceId` int DEFAULT NULL,
  `priceName` varchar(255) DEFAULT NULL,
  `pricePeriod` int DEFAULT NULL,
  `priceDiscount` float DEFAULT NULL,
  `priceTotal` float DEFAULT NULL,
  `paidAmount` varchar(255) NOT NULL,
  `promoCode` varchar(255) DEFAULT NULL,
  `promoType` varchar(255) DEFAULT NULL,
  `promoDiscount` float DEFAULT NULL,
  `paymentType` varchar(255) DEFAULT NULL,
  `dateTime` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference` varchar(255) DEFAULT NULL,
  `amount` int DEFAULT NULL,
  `reimbursement` varchar(255) DEFAULT NULL,
  `currency` int DEFAULT NULL,
  `paymentDescription` varchar(255) DEFAULT NULL,
  `timestamp` varchar(255) DEFAULT NULL,
  `xid` varchar(255) DEFAULT NULL,
  `rrn` varchar(255) DEFAULT NULL,
  `approval` varchar(255) DEFAULT NULL,
  `pan` varchar(255) DEFAULT NULL,
  `rc` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `premiumCode`
--

CREATE TABLE `premiumCode` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `packetId` int NOT NULL,
  `discount` float NOT NULL,
  `nTime` int NOT NULL DEFAULT '1',
  `used` int NOT NULL DEFAULT '0',
  `type` enum('premium','coupon') NOT NULL DEFAULT 'premium',
  `active` enum('yes','no') NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `promoActions`
--

CREATE TABLE `promoActions` (
  `id` int NOT NULL,
  `givenByUser` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `givenByEmail` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `givenByID` int NOT NULL,
  `takenByEmail` varchar(255) NOT NULL,
  `takenByUser` varchar(255) NOT NULL,
  `takenByID` int NOT NULL,
  `condition` enum('used','unused') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `givenByPercent` float NOT NULL DEFAULT '0',
  `takenByPercent` float NOT NULL DEFAULT '0',
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `date` datetime NOT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int NOT NULL,
  `beforeLevel` varchar(255) DEFAULT NULL,
  `afterLevel` varchar(255) DEFAULT NULL,
  `stars` int NOT NULL DEFAULT '0',
  `orderNumber` int NOT NULL DEFAULT '0',
  `language` enum('en','az','ru','tr','pt') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'en',
  `description` varchar(1000) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(555) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `socketUsers`
--

CREATE TABLE `socketUsers` (
  `id` int NOT NULL,
  `resourceId` int NOT NULL,
  `userId` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `level` enum('beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced','empty','usa-elementary','usa-pre-intermediate','usa-intermediate') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `teacherName` varchar(255) NOT NULL,
  `teacherZoom` varchar(555) DEFAULT NULL,
  `image` varchar(555) NOT NULL,
  `presentation` varchar(555) DEFAULT NULL,
  `landing` enum('yes','no') NOT NULL DEFAULT 'yes',
  `orderNumber` int NOT NULL DEFAULT '0',
  `country` varchar(255) DEFAULT NULL,
  `experience` varchar(255) DEFAULT NULL,
  `description_az` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description_en` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description_ru` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description_tr` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `description_pt` varchar(1000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `todaysGrammar`
--

CREATE TABLE `todaysGrammar` (
  `id` int NOT NULL,
  `startDate` date NOT NULL,
  `level` enum('beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced') NOT NULL,
  `lessonId` int NOT NULL,
  `lessonName` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `translate`
--

CREATE TABLE `translate` (
  `id` int NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `az` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `en` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `ru` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `tr` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `pt` varchar(4000) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `trialConversation`
--

CREATE TABLE `trialConversation` (
  `id` int NOT NULL,
  `date` date NOT NULL,
  `startsAt` varchar(255) NOT NULL,
  `endsAt` varchar(255) NOT NULL,
  `tutorId` int DEFAULT NULL,
  `tutorName` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tutorEmail` varchar(255) DEFAULT NULL,
  `tutorImage` varchar(500) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `level` enum('beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced','usa-elementary','usa-pre-intermediate','usa-intermediate') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdAt` datetime DEFAULT NULL,
  `visible` enum('yes','no') NOT NULL,
  `eventId` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `trialConversationUsers`
--

CREATE TABLE `trialConversationUsers` (
  `id` int NOT NULL,
  `trialConversationId` int NOT NULL,
  `conversationLevel` varchar(255) DEFAULT NULL,
  `conversationDate` varchar(255) DEFAULT NULL,
  `startsAT` varchar(255) DEFAULT NULL,
  `conversationTopic` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `tutorName` varchar(255) DEFAULT NULL,
  `tutorImage` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL,
  `userName` varchar(255) DEFAULT NULL,
  `userEmail` varchar(255) DEFAULT NULL,
  `action` enum('reserve','enter','cancel') NOT NULL DEFAULT 'reserve',
  `userId` int NOT NULL,
  `requestDate` date NOT NULL,
  `requestTime` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int NOT NULL,
  `username` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `verification_token` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint NOT NULL DEFAULT '10',
  `created_at` int NOT NULL,
  `updated_at` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `userParameters`
--

CREATE TABLE `userParameters` (
  `id` int NOT NULL,
  `userId` int NOT NULL,
  `confirmed` enum('yes','no') NOT NULL DEFAULT 'no',
  `availability` varchar(255) DEFAULT NULL,
  `availabilityLCD` date DEFAULT NULL,
  `proficiency` enum('start-date','level-start-date','no','level') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'no',
  `startDate` date NOT NULL DEFAULT '2022-02-14',
  `currentLevel` enum('empty','beginner','elementary','pre-intermediate','intermediate','upper-intermediate','advanced') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'empty',
  `currentPacketId` int DEFAULT NULL,
  `currentSchedule` int NOT NULL DEFAULT '123456',
  `promoCode` varchar(255) DEFAULT NULL,
  `cp` int NOT NULL DEFAULT '0',
  `cpBalance` int NOT NULL DEFAULT '0',
  `lpd` date DEFAULT NULL,
  `googleCalendar` enum('yes','no') NOT NULL DEFAULT 'no',
  `calendarGmail` varchar(255) DEFAULT NULL,
  `stripeCustomerId` varchar(255) DEFAULT NULL,
  `selectedPriceId` int DEFAULT NULL,
  `trialLessonId` int DEFAULT NULL,
  `container` json DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userProfile`
--

CREATE TABLE `userProfile` (
  `id` int NOT NULL,
  `userId` int NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `surname` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `source` varchar(255) DEFAULT NULL,
  `aim` varchar(255) DEFAULT NULL,
  `preliminaryLevel` varchar(255) DEFAULT NULL,
  `timezone` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conversation`
--
ALTER TABLE `conversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorId` (`tutorId`);

--
-- Indexes for table `conversationUsers`
--
ALTER TABLE `conversationUsers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversationId` (`conversationId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `developerSettings`
--
ALTER TABLE `developerSettings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_ibfk_1` (`classId`),
  ADD KEY `feedback_ibfk_2` (`userId`);

--
-- Indexes for table `filemanager_mediafile`
--
ALTER TABLE `filemanager_mediafile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `filemanager_mediafile_tag`
--
ALTER TABLE `filemanager_mediafile_tag`
  ADD PRIMARY KEY (`mediafile_id`,`tag_id`),
  ADD KEY `filemanager_mediafile_tag_mediafile_id__mediafile_id` (`mediafile_id`),
  ADD KEY `filemanager_mediafile_tag_tag_id__tag_id` (`tag_id`);

--
-- Indexes for table `filemanager_owners`
--
ALTER TABLE `filemanager_owners`
  ADD PRIMARY KEY (`mediafile_id`,`owner_id`,`owner`,`owner_attribute`);

--
-- Indexes for table `filemanager_tag`
--
ALTER TABLE `filemanager_tag`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grammar`
--
ALTER TABLE `grammar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `packets`
--
ALTER TABLE `packets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `paymentActions`
--
ALTER TABLE `paymentActions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `premiumCode`
--
ALTER TABLE `premiumCode`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promoActions`
--
ALTER TABLE `promoActions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `socketUsers`
--
ALTER TABLE `socketUsers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `todaysGrammar`
--
ALTER TABLE `todaysGrammar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `translate`
--
ALTER TABLE `translate`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trialConversation`
--
ALTER TABLE `trialConversation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tutorId` (`tutorId`);

--
-- Indexes for table `trialConversationUsers`
--
ALTER TABLE `trialConversationUsers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversationId` (`trialConversationId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`);

--
-- Indexes for table `userParameters`
--
ALTER TABLE `userParameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId_2` (`userId`),
  ADD KEY `userId` (`userId`);

--
-- Indexes for table `userProfile`
--
ALTER TABLE `userProfile`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `userId_2` (`userId`),
  ADD KEY `userId` (`userId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conversation`
--
ALTER TABLE `conversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversationUsers`
--
ALTER TABLE `conversationUsers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `developerSettings`
--
ALTER TABLE `developerSettings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filemanager_mediafile`
--
ALTER TABLE `filemanager_mediafile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `filemanager_tag`
--
ALTER TABLE `filemanager_tag`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grammar`
--
ALTER TABLE `grammar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `packets`
--
ALTER TABLE `packets`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `paymentActions`
--
ALTER TABLE `paymentActions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `premiumCode`
--
ALTER TABLE `premiumCode`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promoActions`
--
ALTER TABLE `promoActions`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `socketUsers`
--
ALTER TABLE `socketUsers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todaysGrammar`
--
ALTER TABLE `todaysGrammar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `translate`
--
ALTER TABLE `translate`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trialConversation`
--
ALTER TABLE `trialConversation`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `trialConversationUsers`
--
ALTER TABLE `trialConversationUsers`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userParameters`
--
ALTER TABLE `userParameters`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `userProfile`
--
ALTER TABLE `userProfile`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`tutorId`) REFERENCES `teachers` (`id`) ON UPDATE RESTRICT;

--
-- Constraints for table `conversationUsers`
--
ALTER TABLE `conversationUsers`
  ADD CONSTRAINT `conversationusers_ibfk_1` FOREIGN KEY (`conversationId`) REFERENCES `conversation` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `conversationusers_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `feedback_ibfk_3` FOREIGN KEY (`classId`) REFERENCES `conversation` (`id`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `feedback_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON UPDATE RESTRICT;

--
-- Constraints for table `filemanager_mediafile_tag`
--
ALTER TABLE `filemanager_mediafile_tag`
  ADD CONSTRAINT `filemanager_mediafile_tag_mediafile_id__mediafile_id` FOREIGN KEY (`mediafile_id`) REFERENCES `filemanager_mediafile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `filemanager_mediafile_tag_tag_id__tag_id` FOREIGN KEY (`tag_id`) REFERENCES `filemanager_tag` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `filemanager_owners`
--
ALTER TABLE `filemanager_owners`
  ADD CONSTRAINT `filemanager_owners_ref_mediafile` FOREIGN KEY (`mediafile_id`) REFERENCES `filemanager_mediafile` (`id`);

--
-- Constraints for table `trialConversation`
--
ALTER TABLE `trialConversation`
  ADD CONSTRAINT `trialConversation_ibfk_1` FOREIGN KEY (`tutorId`) REFERENCES `teachers` (`id`) ON UPDATE RESTRICT,
  ADD CONSTRAINT `trialConversation_ibfk_2` FOREIGN KEY (`tutorId`) REFERENCES `teachers` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `trialConversationUsers`
--
ALTER TABLE `trialConversationUsers`
  ADD CONSTRAINT `trialConversationUsers_ibfk_1` FOREIGN KEY (`trialConversationId`) REFERENCES `trialConversation` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `trialConversationUsers_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trialConversationUsers_ibfk_3` FOREIGN KEY (`trialConversationId`) REFERENCES `trialConversation` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  ADD CONSTRAINT `trialConversationUsers_ibfk_4` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `userParameters`
--
ALTER TABLE `userParameters`
  ADD CONSTRAINT `user_parameters_constraint` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userparameters_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userProfile`
--
ALTER TABLE `userProfile`
  ADD CONSTRAINT `user_profile_constraint` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userProfile_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
