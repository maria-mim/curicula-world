-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 14, 2025 at 08:52 PM
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
-- Database: `medialearn`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `first_name`, `last_name`, `address`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'maria', 'mim', 'Bangladesh Digital University.', 'mim@gmail.com', '01842542469', '$2y$10$eLMD9iutfb6SKgfXYBXxOuMXrbBV2/.ThhyE3qEwlCIfX08h0d/w2', '2025-05-16 08:10:17'),
(2, 'jawad', 'labib', 'dsfgbhds', 'admin@gmail.com', '3475634563', '$2y$10$5VWkZUB1oUxJWUUNMg72W.rP5VAhFvjOW.9KGVLSqLq4CJDFXV93q', '2025-07-14 03:24:59');

-- --------------------------------------------------------

--
-- Table structure for table `articles`
--

CREATE TABLE `articles` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `image1` varchar(255) DEFAULT NULL,
  `text1` text DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `text2` text DEFAULT NULL,
  `video` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `articles`
--

INSERT INTO `articles` (`id`, `title`, `image1`, `text1`, `image2`, `text2`, `video`, `created_at`) VALUES
(5, ' নাম্বার লাইন ', 'uploads/68783f2b47970.jpg', 'হ্যালো বন্ধুরা!\r\nআজকে আমরা শিখবো নাম্বার লাইন কী এবং এটা কীভাবে আমাদের গাণিতিক চিন্তাভাবনা সহজ করে।\r\n\r\n📍 নাম্বার লাইন কী?\r\nনাম্বার লাইন হলো একটা সোজা দাগ বা লাইন, যেখানে সংখ্যাগুলো একটার পর একটা সাজানো থাকে।\r\n\r\nআমরা সাধারণত শুরু করি ০ থেকে।\r\n\r\n০ --- ১ --- ২ --- ৩ --- ৪ --- ৫ --- ৬ --- ৭ --- ৮ --- ৯ --- ১০\r\nপ্রতিটা দাগের মানে একটা সংখ্যা। এগুলোকে ধাপ হিসেবে ভাবো।\r\n\r\n👉 কেন এটা ব্যবহার করবো?\r\nসংখ্যা বুঝতে সাহায্য করে।\r\n\r\nযোগ-বিয়োগ শিখতে সুবিধা হয়।\r\n\r\nচোখের সামনে সংখ্যা গুলো দেখলে মনে রাখা সহজ হয়।\r\n\r\n', NULL, '➕ যোগ কিভাবে করি?\r\nধরো তুমি করছো: ২ + ৩\r\n\r\nতাহলে ২ থেকে শুরু করো,\r\n\r\nতারপর ডানে গুনে গুনে ৩টা ধাপ যাও → ৩, ৪, ৫\r\n\r\nউত্তর হলো: ৫\r\n\r\n➖ বিয়োগ কিভাবে করি?\r\nধরো: ৭ − ২\r\n\r\n৭ থেকে শুরু করো,\r\n\r\nএবার বামে ফিরে ২ ধাপ যাও → ৬, ৫\r\n\r\nউত্তর: ৫\r\n\r\n🎨 ট্রিকস মনে রাখার জন্য:\r\nডানে যাও মানে সংখ্যা বাড়ে\r\n\r\nবামে যাও মানে সংখ্যা কমে\r\n\r\n০ হল তোমার শুরুর জায়গা\r\n\r\n', 'uploads/68783f2b47aba.mp4', '2025-07-17 00:09:15'),
(6, 'চলো চিনি ১ থেকে ১০', 'uploads/6878df2107127.jpg', '১, ২, ৩... এর মত সংখ্যা গুলো হলো আমাদের গণনা করার জন্য প্রথম ধাপ।\r\nসংখ্যা গুলো হলো:\r\n১, ২, ৩, ৪, ৫, ৬, ৭, ৮, ৯, ১০\r\n\r\nকিভাবে শিখব?\r\nপ্রতিটি সংখ্যার নাম বলো: “এক, দুই, তিন...”\r\n\r\nহাতে আঙুল দেখিয়ে গণনা করো।\r\n\r\nছবি বা বস্তু ব্যবহার করো: যেমন ১টা আপেল, ২টা কলম ইত্যাদি।\r\n\r\nসংখ্যা গুলোর ক্রম:\r\n১ থেকে শুরু করে ১০ পর্যন্ত একের পর এক পড়তে শিখো।\r\n', NULL, '“সংখ্যা আমার বন্ধু | ১ থেকে ১০ শিখি” ভিডিওটি ছোট শিশুদের, বিশেষ করে ডিসক্যালকুলিয়া রয়েছে এমন শিক্ষার্থীদের জন্য বানানো একটি মজার ও সহানুভূতিশীল শেখার উপকরণ। এই ভিডিওতে ১ থেকে ১০ পর্যন্ত সংখ্যাগুলো ধীরে ধীরে রঙিন চিত্র, আনন্দদায়ক শব্দ, বাস্তব উদাহরণ এবং সহজ ভাষার মাধ্যমে শেখানো হয়। প্রতিটি সংখ্যার সঙ্গে আঙুল দেখানো, প্রাণী বা ফলের উদাহরণ এবং ছন্দযুক্ত গান যুক্ত করে শেখার অভিজ্ঞতাকে আরও আনন্দদায়ক ও মনে রাখার মতো করে তোলা হয়েছে। ভিডিওটি শিশুদের সংখ্যা শেখাকে ভয়হীন ও আনন্দদায়ক করে তোলে।', 'uploads/6883c467553c6.mp4', '2025-07-17 11:31:45'),
(7, 'সহজ করে আকার শিখি', NULL, '📐 আকার কী?\r\nআকার হলো কোনো জিনিসের বহিরাগত রূপ বা ধরন। যেমন — বৃত্ত, বর্গ, ত্রিভুজ ইত্যাদি।\r\n\r\n🔵 বৃত্ত (Circle):\r\nএকটি গোলাকার আকার, যার কোনো কোনা থাকে না।\r\nযেমন সাইকেলের চাকা বা একটি বেলুন।\r\n\r\n◼️ বর্গ (Square):\r\nচারটি সমান দৈর্ঘ্যের সোজা রেখা দিয়ে গঠিত, চারটি কোনা ৯০ ডিগ্রি।\r\nযেমন টেবিলের পা বা স্কুলের বইয়ের কাভার।\r\n\r\n🔺 ত্রিভুজ (Triangle):\r\nতিনটি রেখা দিয়ে তৈরি আকার, তিনটি কোনা থাকে।\r\nযেমন ট্রাফিক সাইনবোর্ড বা ছাদের আকার।\r\n\r\n', 'uploads/6878ec9f25249.jpg', 'ছবি ও রঙ ব্যবহার করো।\r\n\r\nবস্তুর সাথে মিলিয়ে দেখাও।\r\n\r\nআকার আঁকতে বলো এবং নাম বলাও।\r\n\r\n🗣️ সহজ বাক্য:\r\n“বৃত্ত হলো গোলাকার, বর্গ হলো চতুর্ভুজ, ত্রিভুজ হলো তিন পাশের আকার।”\r\n', NULL, '2025-07-17 11:34:04'),
(8, 'সময় বুঝি: ঘণ্টা, মিনিট ও তারিখ শেখা', 'uploads/6878ec881f551.jpg', '⏳ সময় কি?\r\nসময় হলো দিনের বিভিন্ন মুহূর্ত বা অংশ, যেগুলো আমরা বুঝি ঘড়ির সাহায্যে। সময় জানতে পারলে আমরা কাজ ঠিক সময়ে করতে পারি।\r\n\r\n🕐 ঘণ্টা কী?\r\nঘণ্টা হলো সময়ের বড় একক। এক ঘণ্টায় ৬০ মিনিট থাকে।\r\nযেমন, সকাল ৯টা মানে সকাল হওয়ার পর ৯ ঘণ্টা পেরিয়ে গেছে।\r\n\r\n🕒 মিনিট কী?\r\nমিনিট হলো ঘণ্টার ছোট অংশ। ১ ঘণ্টায় ৬০ মিনিট থাকে।\r\nযেমন, ৯:৩০ মানে সকাল ৯টা ৩০ মিনিট।\r\n\r\n📅 তারিখ কী?\r\nতারিখ হলো মাসের দিন। মাসে সাধারণত ৩০ বা ৩১ দিন থাকে।\r\nযেমন, ২৫ জুলাই মানে জুলাই মাসের ২৫ তম দিন।\r\n\r\n🧠 কিভাবে শেখানো যায়?\r\nঘড়ির ছবি দেখাও: বড় সুঁই ঘণ্টা, ছোট সুঁই মিনিট বোঝায়।\r\n\r\nঘড়ির সামনে হাতে ধরে সময় বলো: “এখন সময় ৩টা” বা “৫টা ৩০ মিনিট”।\r\n\r\nক্যালেন্ডার দিয়ে তারিখ বোঝাও: “আজ ২৫ জুলাই, আগামী কাল ২৬ জুলাই”।\r\n\r\nরোজকার জীবনের উদাহরণ দাও:\r\n\r\nসকাল ৭টায় উঠে স্কুলে যাওয়া।\r\n\r\nসন্ধ্যা ৬টায় খাবার খাওয়া।\r\n\r\nরবিবার হল ছুটির দিন।\r\n\r\n', NULL, '🗣️ সহজ বাক্য:\r\n“ঘণ্টা বলে সময় কতটা বড়”\r\n\r\n“মিনিট হলো ঘণ্টার ছোট ছোট ভাগ”\r\n\r\n“তারিখ বলে মাসের কোন দিন চলছে”\r\n\r\n“সময় জানলে আমরা কাজ ঠিক সময়ে করতে পারি।”\r\n\r\n', NULL, '2025-07-17 11:34:45'),
(10, 'যোগ করা শিখি', NULL, '“+” চিহ্ন মানে হলো কিছু “আরও যোগ হওয়া” বা “বাড়ানো”।\r\n\r\nযেমনঃ\r\nতোমার কাছে ২টা কলম আছে, আমি দিলাম আরও ৩টা কলম।\r\nতাহলে সব মিলে হলো: ২ + ৩ = ৫\r\n\r\n👉 বুঝে রাখো:\r\n\r\n“+” চিহ্ন মানে ডানে এগিয়ে যাওয়া।\r\n\r\nনাম্বার লাইনে ২ থেকে শুরু করে ডানে ৩ ধাপ গেলে পৌঁছাবে ৫-এ।\r\n\r\n', NULL, '🎨 বাস্তব জিনিস দিয়ে শেখাও:\r\n২টা চক দেখাও\r\n\r\nতারপর আরও ৩টা দাও\r\n\r\nসব মিলে গুনো — ১, ২, ৩, ৪, ৫\r\n\r\n🗣️ মনে রাখার মত লাইন:\r\n“প্লাস মানে বাড়াও, নতুন কিছু যোগ হও।”', 'uploads/6883ca5f21c36.mp4', '2025-07-25 18:18:07'),
(12, 'গল্পে শেখা বিয়োগ', NULL, '', NULL, '', 'uploads/689c3076744fd.mp4', '2025-08-13 06:28:06'),
(13, 'চলো সংখ্যা নিয়ে খেলি!', NULL, '', NULL, '', 'uploads/689c30ad4d85e.mp4', '2025-08-13 06:29:01');

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(7, 3, '২', 0),
(8, 3, '৩', 1),
(9, 3, '৪', 0),
(10, 4, '৪', 0),
(11, 4, '৫', 1),
(12, 4, '২', 0),
(13, 5, '৩', 0),
(14, 5, '৮ ', 1),
(15, 6, '৪ ', 0),
(16, 6, '৫', 1),
(17, 7, 'সংখ্যা কমানো', 0),
(18, 7, 'সংখ্যা বাড়ানো বা নতুন কিছু যোগ করা', 1);

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `explanation` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `quiz_id`, `question_text`, `explanation`) VALUES
(3, 2, '১. ২ + ১ = ?', 'সঠিক উত্তর: b) ৩'),
(4, 2, 'যদি তোমার কাছে ৩টা আপেল থাকে, আর বন্ধু ২টা আপেল দেয়, তাহলে মোট কত আপেল হবে?', 'সঠিক উত্তর: b) ৫'),
(5, 3, 'নাম্বার লাইনে ৫ থেকে ৩ ধাপ ডানে গেলে কোন সংখ্যায় পৌঁছাবে?', '৫ থেকে ৩ ধাপ ডানে গেলে ৮ এ পৌঁছাবে।'),
(6, 3, 'তুমি হাতে ১টা চকোলেট নিয়ে ছিলে। আমি তোমাকে ৪টা আরও দিলাম। এখন তোমার কাছে কত চকোলেট আছে?', ''),
(7, 3, '“+” চিহ্ন মানে কী?', '');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `article_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `article_id`, `created_at`) VALUES
(2, 5, '2025-07-17 00:09:15'),
(3, 10, '2025-07-25 18:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `responses`
--

CREATE TABLE `responses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) NOT NULL,
  `responded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responses`
--

INSERT INTO `responses` (`id`, `user_id`, `quiz_id`, `question_id`, `selected_option_id`, `responded_at`) VALUES
(5, 2, 2, 3, 8, '2025-07-17 20:19:29'),
(6, 2, 2, 4, 10, '2025-07-17 20:19:29'),
(7, 2, 3, 5, 14, '2025-08-14 02:14:05'),
(8, 2, 3, 6, 15, '2025-08-14 02:14:05'),
(9, 2, 3, 7, 18, '2025-08-14 02:14:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` text NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `address`, `email`, `phone`, `password`, `created_at`) VALUES
(1, 'mim', 'maria', 'BDU', 'Rafi@gmail.com', '01842542469', '$2y$10$wMW1a1D0lT8x.mbTNvtif.5e2Ufk42WDbDR6GFzzPDg1PJSR7fx7e', '2025-05-16 08:17:12'),
(2, 'Jawad', 'Labib', 'Naogan', 'labib@gmail.com', '0172324', '$2y$10$SCXBr7kJhJdP4ERoKxDaIO44RSsUbpCIDKTqq5ER.MleyLgoyv35S', '2025-07-14 03:22:06'),
(3, 'Sazia', 'Aroni', 'Lotifpur govt. primary school', 'aroni@gmail.com', '01754004547', '$2y$10$qSqX841TSCjijCEXq4vujOoL4vkCgR1owq5iJxDN5uGnN8vYS4wPK', '2025-08-14 17:30:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `articles`
--
ALTER TABLE `articles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `article_id` (`article_id`);

--
-- Indexes for table `responses`
--
ALTER TABLE `responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE `articles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `responses`
--
ALTER TABLE `responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `options`
--
ALTER TABLE `options`
  ADD CONSTRAINT `options_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `responses`
--
ALTER TABLE `responses`
  ADD CONSTRAINT `responses_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `responses_ibfk_2` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
