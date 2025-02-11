-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 11, 2025 at 04:12 PM
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
-- Database: `vrs`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_us`
--

CREATE TABLE `about_us` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `story` text NOT NULL,
  `commitment` text NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about_us`
--

INSERT INTO `about_us` (`id`, `title`, `subtitle`, `story`, `commitment`, `image_url`) VALUES
(1, 'About Us', 'Welcome to Victoria Grill Restaurant, where culinary excellence meets warm hospitality.', 'Our mission is to offer a relaxing atmosphere where friends and family can gather over great food. We strive to provide exceptional customer service and high-quality meals at an affordable price', 'To be the go-to destination for casual dining, where every meal is a celebration, and our guests feel like part of the family. We envision a future where we are present in every neighborhood, offering consistent quality and comfort', '../uploads/download (1).jpg'),
(2, 'About Us', 'Welcome to Victoria Grill Restaurant, where culinary excellence meets warm hospitality.', 'At Victoria Grill Restaurant, we are passionate about serving delicious dishes that bring people together. Since our opening, we\'ve been dedicated to creating a dining experience that combines exquisite flavors, a cozy ambiance, and exceptional service.', 'We source the freshest ingredients, craft every dish with care, and strive to make every visit memorable. Whether you\'re celebrating a special occasion, catching up with friends, or simply enjoying a meal, Victoria Grill is the perfect destination.', 'victoria.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$QjEQN0yQIi3G6juDk9eHW..4Y8IM4hn2.MCF5UmC4OleoxOC6aI4q', 'victoriagrillrestaurant@gmail.com', '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `catering_menu`
--

CREATE TABLE `catering_menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` enum('pork','chicken','beef','pasta','vegetables') NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT NULL,
  `status` enum('available','not_available') DEFAULT 'available',
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catering_menu`
--

INSERT INTO `catering_menu` (`id`, `name`, `category`, `price`, `discounted_price`, `status`, `image_url`, `alt_text`, `created_at`) VALUES
(12, 'Beef Bakarte w/ Gata', 'beef', 0.00, NULL, 'available', '../uploads/kalderetawithgatarecipe-2.jpg', 'beef1', '2025-01-30 15:41:45'),
(13, 'Beef Kulma', 'beef', 0.00, NULL, 'available', '../uploads/beefstewincoconutmilkrecipe-beefkulmarecipe-1.jpg', 'Beef2', '2025-01-30 15:42:13'),
(14, 'Kare Kare', 'beef', 0.00, NULL, 'available', '../uploads/k_Photo_Recipes_2024-09-kare-kare_kare-kare-3458.jfif', 'Beef2', '2025-01-30 15:42:23'),
(15, 'Beef Broccoli', 'beef', 0.00, NULL, 'available', '../uploads/images.jfif', 'Beef2', '2025-01-30 15:42:29'),
(16, 'Stir Fried Beef and Sweet Peas', 'beef', 0.00, NULL, 'available', '../uploads/images (1).jfif', 'Stir Fried Beef and Sweet Peas', '2025-01-30 15:42:38'),
(17, 'Chicken Alexander', 'chicken', 0.00, NULL, 'available', '../uploads/images (4).jfif', 'chicken alexander', '2025-01-30 15:43:56'),
(18, 'Spicy Buffalo Chicken', 'chicken', 0.00, NULL, 'available', '../uploads/download.jfif', 'Spicy Buffalo Chicken', '2025-01-30 15:44:01'),
(19, 'Mayo Garlic Chicken', 'chicken', 0.00, NULL, 'available', '../uploads/download (1).jfif', 'Mayo Garlic Chicken', '2025-01-30 15:44:07'),
(20, 'Chicken Rosemary', 'chicken', 0.00, NULL, 'available', '../uploads/Rosemary-Lemon-Chicken.jpg', 'Chicken Rosemary', '2025-01-30 15:44:12'),
(21, 'Oriental Chicken', 'chicken', 0.00, NULL, 'available', '../uploads/download (2).jfif', 'Oriental Chicken', '2025-01-30 15:44:17'),
(22, 'Chicken Alfredo', 'pasta', 0.00, NULL, 'available', '../uploads/download (6).jfif', 'Chicken Alfredo', '2025-01-30 15:45:28'),
(23, 'Creamy Pesto', 'pasta', 0.00, NULL, 'available', '../uploads/download (7).jfif', 'Creamy Pesto', '2025-01-30 15:45:34'),
(24, 'Aglio Olio', 'pasta', 0.00, NULL, 'available', '../uploads/download (8).jfif', 'Aglio Olio', '2025-01-30 15:45:39'),
(25, 'Pasta in Garlic Butter Sauce', 'pasta', 0.00, NULL, 'available', '../uploads/download (9).jfif', 'Pasta in Garlic Butter Sauce', '2025-01-30 15:45:44'),
(26, 'Beef Fajita Pasta', 'pasta', 0.00, NULL, 'available', '../uploads/download (10).jfif', 'Beef Fajita Pasta', '2025-01-30 15:45:49'),
(27, 'Veggies Fiesta', 'vegetables', 0.00, NULL, 'available', '../uploads/download (11).jfif', 'Veggies Fiesta', '2025-01-30 15:47:05'),
(28, 'Buttered Veggies', 'vegetables', 0.00, NULL, 'available', '../uploads/download (12).jfif', 'Buttered Veggies', '2025-01-30 15:47:11'),
(29, 'Ginataang Sigarilyas', 'vegetables', 0.00, NULL, 'available', '../uploads/download (13).jfif', 'Ginataang Sigarilyas', '2025-01-30 15:47:16'),
(30, 'Chopseuy', 'vegetables', 0.00, NULL, 'available', '../uploads/download (14).jfif', 'Chopseuy', '2025-01-30 15:47:20'),
(31, 'Stir Fried Veggies', 'vegetables', 0.00, NULL, 'available', '../uploads/download (15).jfif', 'Stir Fried Veggies', '2025-01-30 15:47:25'),
(32, 'Patatim', 'pork', 0.00, NULL, 'available', '../uploads/download (16).jfif', 'Patatim', '2025-01-30 15:48:17'),
(33, 'Baby Back Ribs', 'pork', 0.00, NULL, 'available', '../uploads/images (3).jfif', 'Baby Back Ribs', '2025-01-30 15:48:25'),
(34, 'Meatballs', 'pork', 0.00, NULL, 'available', '../uploads/download (17).jfif', 'Meatballs', '2025-01-30 15:48:30'),
(35, 'Pork Menudo', 'pork', 0.00, NULL, 'available', '../uploads/download (18).jfif', 'Pork Menudo', '2025-01-30 15:48:35'),
(37, 'Beef Salpicao', 'beef', 0.00, NULL, 'available', '../uploads/images (2).jfif', 'Beef Salpicao', '2025-02-01 17:28:18'),
(38, 'Garlic - Parmesan', 'chicken', 0.00, NULL, 'available', '../uploads/download (3).jfif', 'Garlic - Parmesan', '2025-02-01 17:37:08'),
(39, 'Chicken Hamonado', 'chicken', 0.00, NULL, 'available', '../uploads/download (4).jfif', 'Chicken Hamonado', '2025-02-01 17:37:42'),
(40, 'Cordon Bleu', 'chicken', 0.00, NULL, 'available', '../uploads/download (5).jfif', 'Cordon Bleu', '2025-02-01 17:38:10');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `message_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`message_id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'hi\r\n', 1, '2025-02-04 15:38:51'),
(2, 1, 'dadasdadasdaasda', 1, '2025-02-04 15:54:35'),
(3, 1, 'dadasdasd', 1, '2025-02-04 16:05:27'),
(6, 1, 'gwA PO NAK', 1, '2025-02-04 16:50:47'),
(7, 1, 'hello ', 1, '2025-02-06 17:45:50');

-- --------------------------------------------------------

--
-- Table structure for table `contact_replies`
--

CREATE TABLE `contact_replies` (
  `id` int(11) NOT NULL,
  `contact_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `reply_message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_replies`
--

INSERT INTO `contact_replies` (`id`, `contact_id`, `admin_id`, `reply_message`, `created_at`) VALUES
(1, 34, 1, 'hello', '2025-01-23 20:15:16'),
(2, 33, 1, 'wen', '2025-02-01 16:20:04'),
(3, 3, 1, 'hello', '2025-02-04 16:41:54'),
(4, 7, 1, 'hi', '2025-02-06 17:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `hero_section`
--

CREATE TABLE `hero_section` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `background_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hero_section`
--

INSERT INTO `hero_section` (`id`, `title`, `subtitle`, `background_image`) VALUES
(1, 'Welcome to Victoria Grill Catering and Restaurant', 'Savor the flavors, relish the experience.', 'Screenshot 2025-01-28 013048.png');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) NOT NULL,
  `status` enum('available','not_available') NOT NULL DEFAULT 'available',
  `category` enum('specials','vip','pasta','pica-pica','sweets','drinks') NOT NULL DEFAULT 'specials'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `name`, `price`, `image_url`, `alt_text`, `status`, `category`) VALUES
(9, 'Pork Barbecue Ribs', 280.00, '../uploads/bbq.jpg', 'Pork Barbecue Ribs', 'available', 'specials'),
(10, 'Bang Bang Shrimp', 320.00, '../uploads/bbshrimp.jpg', 'Bang Bang Shrimp', 'available', 'specials'),
(11, 'Beef Salpicao', 230.00, '../uploads/Beef-Salpicao.jpg', 'Beef Salpicao', 'available', 'specials'),
(12, 'Honey Chipotle Wings', 185.00, '../uploads/honeychipotlewings-2-750x1000.jpg', 'Honey Chipotle Wings', 'available', 'specials'),
(13, 'Korean Wings', 185.00, '../uploads/koreanwings.jpg', 'Korean Wings', 'available', 'specials'),
(14, 'Chicken Parmesan', 185.00, '../uploads/chikenparmesan.jpg', 'Chicken Parmesan', 'available', 'specials'),
(15, 'Oriental Shrimp & Broccoli ', 320.00, '../uploads/shrimpbroco.jpg', 'Oriental Shrimp & Broccoli ', 'available', 'specials'),
(16, 'Crispy Pork Kare Kare', 280.00, '../uploads/porkkarekare.jpg', 'Crispy Pork Kare Kare', 'available', 'specials'),
(17, 'Beef Kare Kare', 320.00, '../uploads/beefkarekarerecipe-3.jpg', 'Beef Kare Kare', 'available', 'specials'),
(18, 'Lechon Kawali', 220.00, '../uploads/Lechon_Kawali.jpg', 'Lechon Kawali', 'available', 'specials'),
(19, 'Pork Sisig', 200.00, '../uploads/sisig.jpg', 'Pork Sisig', 'available', 'specials'),
(20, 'Shrimp Cajun', 320.00, '../uploads/Cajun-Shrimp-Recipe-5-1024x1536.webp', 'Shrimp Cajun', 'available', 'specials'),
(21, 'Sinigang na Baboy', 320.00, '../uploads/sinigangbaboy.jpg', 'Sinigang na Baboy', 'available', 'specials'),
(22, 'Sinigang na Hipon', 320.00, '../uploads/siniganghipon.jpg', 'Sinigang na Hipon', 'available', 'specials'),
(23, 'Beef in Garlic Mushroom', 230.00, '../uploads/beefgarlicmushroom.jpg', 'Beef in Garlic Mushroom', 'available', 'specials'),
(24, 'Mongolian Beef', 230.00, '../uploads/Mongolian_Beef_recipe.jpg', 'Mongolian Beef', 'available', 'specials'),
(25, 'Beef Steak in Red Wine Sauce', 480.00, '../uploads/beef redwine.jpg', 'Beef Steak in Red Wine Sauce', 'available', 'vip'),
(26, 'Salisbury Steak', 480.00, '../uploads/Salisbury-Steak-2.jpg', 'Salisbury Steak', 'available', 'vip'),
(27, 'Mama Es\' Lasagna', 150.00, '../uploads/mamasel lasagna.jpg', 'Mama Es\' Lasagna', 'available', 'pasta'),
(28, 'Aglio Olio', 110.00, '../uploads/spaghetti-aglio-e-olio.jpg', 'Aglio Olio', 'available', 'pasta'),
(29, 'Frutti Di Mare', 165.00, '../uploads/frutti-di-mare-recipe-card.jpg', 'Frutti Di Mare', 'available', 'pasta'),
(30, 'Chicken Alfredo', 125.00, '../uploads/chicken-alfredo-1.jpg', 'Chicken Alfredo', 'available', 'pasta'),
(31, 'Shrimp Alfredo', 135.00, '../uploads/Shrimp-Alfredo-Pasta-V12-800x1067.jpg', 'Shrimp Alfredo', 'available', 'pasta'),
(32, 'Chicken Cajun', 135.00, '../uploads/cajun chicken..jpg', 'Chicken Cajun', 'available', 'pasta'),
(33, 'Beef Fajita ', 135.00, '../uploads/Steak-Fajitas.jpg', 'Beef Fajita ', 'available', 'pasta'),
(34, 'Pasta Negra', 135.00, '../uploads/PastaNegra3.jpg', 'Pasta Negra', 'available', 'pasta'),
(36, 'Classic Pesto', 120.00, '../uploads/pesto.jpg', 'Pesto', 'available', 'pasta'),
(37, 'Creamy Tuscan Salmon', 165.00, '../uploads/Creamy-Garlic-Butter-Tuscan-Salmon-Trout-IMAGE-35.jpg', 'Creamy Tuscan Salmon', 'available', 'pasta'),
(38, 'Crispy Potato Wedges', 135.00, '../uploads/healthy-potato-wedges-recipe-3.jpg', 'Crispy Potato Wedges', 'available', 'pica-pica'),
(39, 'Nachos and Salsa', 180.00, '../uploads/nachos and salsa.jpg', 'Nachos and Salsa', 'available', 'pica-pica'),
(40, 'Potato Croquettes', 190.00, '../uploads/potato cro.jpg', 'Potato Croquettes', 'available', 'pica-pica'),
(41, 'Blueberry Cheesecake / Slice', 150.00, '../uploads/plated-blueberry-cheesecake-hero.jpg', 'Blueberry Cheesecake / Slice', 'available', 'sweets'),
(42, 'Triple Chocolate Cake / Slice', 150.00, '../uploads/tripple.jpg', 'Triple Chocolate Cake / Slice', 'available', 'sweets'),
(43, 'Red Velvet / Slice', 150.00, '../uploads/red velvet.jpg', 'Red Velvet / Slice', 'available', 'sweets'),
(44, 'Coke / Can', 50.00, '../uploads/CokeinCan.jpg', 'Coke / Can', 'available', 'drinks'),
(45, 'Sprite / Can', 50.00, '../uploads/SpriteinCan.jpg', 'Sprite / Can', 'available', 'drinks'),
(46, 'Royal / Can', 50.00, '../uploads/royal can.jpg', 'Royal Can', 'available', 'drinks'),
(47, 'Pineapple Juice / Can', 50.00, '../uploads/pineapple.png', 'Pineapple Juice / Can', 'available', 'drinks'),
(48, 'Four Season / Can', 50.00, '../uploads/four season.jpg', 'Four Season', 'available', 'drinks'),
(49, 'Pineapple Juice / 1 Liter', 160.00, '../uploads/pine apple.jpg', 'Pineapple Juice / 1 Liter', 'available', 'drinks'),
(50, 'Iced Tea Lemon / 1 Liter', 160.00, '../uploads/ice tea.jpg', 'Iced Tea Lemon / 1 Liter', 'available', 'drinks');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `reference_id`, `is_read`, `created_at`) VALUES
(58, 1, 'Your reservation #19 has been rejected', 'reservation_rejected', 19, 1, '2025-02-06 14:02:57'),
(59, 1, 'Your reservation #20 has been confirmed!', 'reservation_approved', 20, 1, '2025-02-06 14:03:05'),
(60, 1, 'Your reservation #21 has been confirmed!', 'reservation_approved', 21, 1, '2025-02-06 14:03:12'),
(61, 1, 'Your catering reservation #38 has been confirmed!', 'catering_approved', 38, 1, '2025-02-06 16:09:58'),
(62, 1, 'Your catering reservation #37 has been confirmed!', 'catering_approved', 37, 1, '2025-02-06 16:10:05'),
(63, 1, 'Your catering reservation #36 has been confirmed!', 'catering_approved', 36, 1, '2025-02-06 16:10:11'),
(64, 1, 'Your catering reservation #35 has been confirmed!', 'catering_approved', 35, 1, '2025-02-06 16:10:19'),
(65, 1, 'Your catering reservation #34 has been confirmed!', 'catering_approved', 34, 1, '2025-02-06 16:10:26'),
(66, 1, 'Your catering reservation #33 has been confirmed!', 'catering_approved', 33, 1, '2025-02-06 16:10:33'),
(67, 1, 'Your catering reservation #32 has been confirmed!', 'catering_approved', 32, 1, '2025-02-06 16:10:39'),
(68, 1, 'Your catering reservation #31 has been confirmed!', 'catering_approved', 31, 1, '2025-02-06 16:10:45'),
(69, 1, 'Your catering reservation #30 has been confirmed!', 'catering_approved', 30, 1, '2025-02-06 16:10:53'),
(70, 1, 'Your catering reservation #29 has been confirmed!', 'catering_approved', 29, 1, '2025-02-06 16:10:59'),
(71, 1, 'Your catering reservation #28 has been confirmed!', 'catering_approved', 28, 1, '2025-02-06 16:11:06'),
(72, 1, 'Your catering reservation #25 has been rejected.', 'catering_rejected', 25, 1, '2025-02-06 16:38:26'),
(73, 1, 'Your catering reservation #26 has been rejected.', 'catering_rejected', 26, 1, '2025-02-06 16:38:33'),
(74, 1, 'Your catering reservation #44 has been confirmed!', 'catering_approved', 44, 1, '2025-02-07 13:40:44'),
(75, 1, 'Your catering reservation #45 has been confirmed!', 'catering_approved', 45, 1, '2025-02-07 13:50:20'),
(76, 1, 'Your catering reservation #46 has been confirmed!', 'catering_approved', 46, 1, '2025-02-07 13:57:05'),
(77, 1, 'Your catering reservation #47 has been confirmed!', 'catering_approved', 47, 1, '2025-02-07 14:00:44'),
(78, 1, 'Your catering reservation #43 has been confirmed!', 'catering_approved', 43, 1, '2025-02-07 14:01:53'),
(79, 1, 'Your catering reservation #42 has been confirmed!', 'catering_approved', 42, 1, '2025-02-07 14:03:05'),
(80, 1, 'Your catering reservation #48 has been confirmed!', 'catering_approved', 48, 1, '2025-02-07 14:06:11'),
(81, 1, 'Your catering reservation #49 has been confirmed!', 'catering_approved', 49, 1, '2025-02-07 14:12:20'),
(82, 1, 'Your catering reservation #50 has been confirmed!', 'catering_approved', 50, 1, '2025-02-07 14:32:41'),
(83, 1, 'Your catering reservation #51 has been confirmed!', 'catering_approved', 51, 1, '2025-02-07 14:33:14'),
(84, 1, 'Your catering reservation #52 has been confirmed!', 'catering_approved', 52, 1, '2025-02-08 14:02:38'),
(85, 1, 'Your catering reservation #53 has been confirmed!', 'catering_approved', 53, 1, '2025-02-08 14:12:34'),
(86, 1, 'Your catering reservation #54 has been confirmed!', 'catering_approved', 54, 1, '2025-02-08 14:29:51'),
(87, 1, 'Your catering reservation #55 has been confirmed!', 'catering_approved', 55, 1, '2025-02-08 14:33:28'),
(88, 1, 'Your catering reservation #56 has been confirmed!', 'catering_approved', 56, 1, '2025-02-08 14:35:13'),
(89, 1, 'Your catering reservation #57 has been confirmed!', 'catering_approved', 57, 1, '2025-02-08 14:36:49'),
(90, 1, 'Your catering reservation #58 has been confirmed!', 'catering_approved', 58, 1, '2025-02-08 14:37:48'),
(91, 1, 'Your catering reservation #59 has been confirmed!', 'catering_approved', 59, 1, '2025-02-08 14:42:33'),
(92, 1, 'Your catering reservation #60 has been confirmed!', 'catering_approved', 60, 1, '2025-02-08 14:44:05'),
(93, 1, 'Your catering reservation #61 has been confirmed!', 'catering_approved', 61, 1, '2025-02-08 14:44:51');

-- --------------------------------------------------------

--
-- Table structure for table `notification_reads`
--

CREATE TABLE `notification_reads` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notification_reads`
--

INSERT INTO `notification_reads` (`id`, `reservation_id`, `admin_id`, `created_at`) VALUES
(27, 216, 1, '2025-01-23 16:40:49'),
(28, 0, 1, '2025-02-04 16:03:18');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `order_details` text DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` enum('Paid','Pending') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `payment_method` enum('Credit Card','Visa Only') NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Success','Failed') DEFAULT 'Success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_details`
--

CREATE TABLE `payment_details` (
  `id` int(11) NOT NULL,
  `payment_method` enum('Visa','Gcash') NOT NULL,
  `phone_number` varchar(15) DEFAULT NULL,
  `payment_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_details`
--

INSERT INTO `payment_details` (`id`, `payment_method`, `phone_number`, `payment_picture`) VALUES
(1, 'Visa', '0320234234', '../uploads/Untitled.jpeg'),
(2, 'Gcash', '09351455907', '../uploads/Untitled.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `promotions`
--

CREATE TABLE `promotions` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount` decimal(5,2) NOT NULL,
  `details` text NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `alt_text` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `valid_until` date DEFAULT '9999-12-31'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotions`
--

INSERT INTO `promotions` (`id`, `name`, `discount`, `details`, `image_url`, `alt_text`, `created_at`, `price`, `discounted_price`, `valid_until`) VALUES
(2, 'Pork Sisig', 30.00, 'New Pork Sisig 30% Discount', '../uploads/sisig.jpg', 'Pork Sisig', '2025-01-27 17:37:57', 200.00, 140.00, '2025-01-31'),
(4, 'Valentine promo', 10.00, 'for couple', '../uploads/Mocha Brownie Ice Cream Cake _ The Best Homemade Ice Cream Cake Recipe.jpg', 'ii', '2025-02-03 03:46:49', 100.00, 90.00, '2025-12-02');

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `reservation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `dine_in_or_takeout` enum('Dine-in','Takeout') NOT NULL,
  `takeout_type` enum('Delivery','Pick-Up') DEFAULT NULL,
  `delivery_location` varchar(255) DEFAULT NULL,
  `guests` int(11) NOT NULL,
  `payment_method` enum('Visa','Gcash') NOT NULL,
  `special_requests` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Rejected','Cancelled','Pending Cancellation') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation`
--

INSERT INTO `reservation` (`reservation_id`, `user_id`, `reservation_date`, `reservation_time`, `dine_in_or_takeout`, `takeout_type`, `delivery_location`, `guests`, `payment_method`, `special_requests`, `status`, `created_at`, `payment_screenshot`, `cancel_reason`, `is_read`) VALUES
(19, 1, '2025-02-13', '07:44:00', 'Dine-in', NULL, '', 7, 'Gcash', '', 'Rejected', '2025-02-06 13:45:17', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(20, 1, '2025-02-07', '07:53:00', 'Dine-in', NULL, '', 8, 'Gcash', '', 'Confirmed', '2025-02-06 13:54:20', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(21, 1, '2025-02-12', '07:56:00', 'Dine-in', NULL, '', 7, 'Gcash', '', 'Confirmed', '2025-02-06 13:56:30', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(22, 1, '2025-02-14', '07:04:00', 'Dine-in', NULL, '', 7, 'Gcash', 'dasdadas', 'Pending', '2025-02-06 14:05:36', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(23, 1, '2025-02-14', '07:04:00', 'Dine-in', NULL, '', 7, 'Gcash', 'dasdadas', 'Pending', '2025-02-06 14:09:22', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(24, 1, '2025-02-14', '08:10:00', 'Dine-in', NULL, '', 7, 'Gcash', 'f', 'Pending', '2025-02-06 14:10:36', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(25, 1, '2025-02-06', '08:10:00', 'Dine-in', NULL, '', 7, 'Gcash', 'f', 'Pending', '2025-02-06 14:11:40', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1),
(26, 1, '2025-02-07', '08:21:00', 'Dine-in', NULL, '', 6, 'Gcash', '', 'Pending', '2025-02-06 14:20:35', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_catering`
--

CREATE TABLE `reservation_catering` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `contract_date` date NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `event_start` datetime NOT NULL,
  `event_end` datetime NOT NULL,
  `pax` int(11) NOT NULL,
  `services` text NOT NULL,
  `status` enum('Pending','Confirmed','Rejected','Cancelled','Pending Cancellation') DEFAULT 'Pending',
  `payment_screenshot` varchar(255) DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_catering`
--

INSERT INTO `reservation_catering` (`id`, `user_id`, `contract_date`, `event_name`, `company_name`, `address`, `phone_number`, `email`, `location`, `event_start`, `event_end`, `pax`, `services`, `status`, `payment_screenshot`, `cancel_reason`, `created_at`, `is_read`) VALUES
(42, 1, '2025-02-07', 'dasdasd', 'dasdasda', 'dadasd', '09351455907', 'janggisdump@gmail.com', 'dadasd', '2025-02-14 01:37:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot (19).png', NULL, '2025-02-06 17:39:19', 1),
(43, 1, '2025-02-07', 'dasdasd', 'dasdasda', 'dadasd', '09351455907', 'janggisdump@gmail.com', 'dadasd', '2025-02-14 01:37:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot (19).png', NULL, '2025-02-06 17:40:19', 1),
(44, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 13:40:05', 1),
(45, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 13:50:09', 0),
(46, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 13:56:53', 0),
(47, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 14:00:32', 0),
(48, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 14:06:01', 0),
(49, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 14:12:08', 0),
(50, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 14:32:32', 0),
(51, 1, '2025-02-07', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'Zone 7, Bangued Abra', '2025-02-14 21:39:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-07 14:33:03', 0),
(52, 1, '2025-02-08', 'Wedding', 'Hello World', 'Laguiben Lagangilang Abra', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-27 22:01:00', '0000-00-00 00:00:00', 210000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:02:10', 0),
(53, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:12:09', 0),
(54, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:29:40', 0),
(55, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:33:10', 0),
(56, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:34:56', 0),
(57, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:36:41', 0),
(58, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:37:36', 0),
(59, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:42:07', 0),
(60, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:43:54', 0),
(61, 1, '2025-02-08', 'Wedding', 'dasdasd', 'dasdsad', '09351455907', 'janggisdump@gmail.com', 'dsadas', '2025-02-15 22:12:00', '0000-00-00 00:00:00', 170000, '', 'Confirmed', 'uploads/Screenshot_20250125_181251_Gallery.jpg', NULL, '2025-02-08 14:44:41', 0);

-- --------------------------------------------------------

--
-- Table structure for table `reservation_menu`
--

CREATE TABLE `reservation_menu` (
  `reservation_menu_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `menu_item_id` int(11) DEFAULT NULL,
  `promo_item_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservation_menu`
--

INSERT INTO `reservation_menu` (`reservation_menu_id`, `reservation_id`, `menu_item_id`, `promo_item_id`, `quantity`) VALUES
(644, 10, 12, NULL, 4),
(645, 10, 18, NULL, 1),
(646, 10, 22, NULL, 1),
(647, 10, 28, NULL, 1),
(648, 10, 33, NULL, 1),
(649, 11, 28, NULL, 1),
(650, 13, 12, NULL, 16),
(651, 13, 18, NULL, 2),
(652, 13, 23, NULL, 14),
(653, 13, 28, NULL, 4),
(654, 13, 34, NULL, 5),
(655, 14, 14, NULL, 1),
(656, 14, 19, NULL, 1),
(657, 14, 24, NULL, 1),
(658, 14, 29, NULL, 1),
(659, 14, 34, NULL, 1),
(660, 15, 14, NULL, 1),
(661, 15, 19, NULL, 1),
(662, 15, 24, NULL, 1),
(663, 15, 29, NULL, 1),
(664, 15, 34, NULL, 1),
(665, 16, 33, NULL, 1),
(668, 17, 23, NULL, 3),
(669, 17, 27, NULL, 4),
(670, 17, 33, NULL, 14),
(671, 11, 9, NULL, 1),
(672, 11, 10, NULL, 1),
(673, 11, 14, NULL, 1),
(674, 11, 15, NULL, 1),
(675, 11, 16, NULL, 1),
(676, 11, 25, NULL, 1),
(677, 11, 26, NULL, 1),
(678, 11, 41, NULL, 2),
(679, 11, 45, NULL, 1),
(680, 11, 46, NULL, 1),
(681, 11, 48, NULL, 1),
(682, 11, 49, NULL, 1),
(683, 11, NULL, 2, 1),
(684, 12, 9, NULL, 7),
(685, 12, 10, NULL, 1),
(686, 12, NULL, 2, 1),
(687, 11, 14, NULL, 1),
(688, 11, 17, NULL, 13),
(689, 11, 23, NULL, 1),
(690, 11, 29, NULL, 1),
(691, 11, 33, NULL, 4),
(692, 13, 28, NULL, 1),
(693, 13, 29, NULL, 1),
(694, 14, 10, NULL, 1),
(695, 15, 9, NULL, 1),
(696, 15, NULL, 4, 1),
(697, 13, 14, NULL, 5),
(698, 13, 18, NULL, 5),
(699, 13, 30, NULL, 5),
(700, 13, 32, NULL, 3),
(701, 14, 13, NULL, 1),
(702, 14, 18, NULL, 1111),
(703, 14, 23, NULL, 1),
(704, 14, 28, NULL, 1),
(705, 14, 33, NULL, 1),
(706, 16, 9, NULL, 1),
(707, 16, 15, NULL, 1),
(708, 16, NULL, 2, 1),
(709, 15, 13, NULL, 9),
(710, 15, 17, NULL, 2),
(711, 15, 24, NULL, 9),
(712, 15, 28, NULL, 6),
(713, 15, 33, NULL, 6),
(714, 17, 20, NULL, 1),
(715, 17, 26, NULL, 1),
(716, 17, 40, NULL, 1),
(717, 17, 42, NULL, 1),
(718, 17, 45, NULL, 1),
(719, 17, 46, NULL, 1),
(720, 16, 12, NULL, 1),
(721, 16, 17, NULL, 1),
(722, 16, 22, NULL, 1),
(723, 16, 27, NULL, 1),
(724, 16, 32, NULL, 1),
(725, 17, 14, NULL, 1),
(726, 17, 19, NULL, 1),
(727, 17, 23, NULL, 1),
(728, 17, 27, NULL, 1),
(729, 17, 34, NULL, 1),
(730, 18, 33, NULL, 1),
(731, 19, 12, NULL, 1),
(732, 19, 18, NULL, 1),
(733, 19, 23, NULL, 1),
(734, 19, 29, NULL, 1),
(735, 19, 33, NULL, 1),
(736, 20, 34, NULL, 1),
(737, 21, 12, NULL, 1),
(738, 21, 13, NULL, 1),
(739, 21, 14, NULL, 1),
(740, 21, 17, NULL, 1),
(741, 21, 18, NULL, 1),
(742, 21, 19, NULL, 1),
(743, 21, 32, NULL, 1),
(744, 21, 33, NULL, 1),
(745, 21, 34, NULL, 1),
(746, 22, 32, NULL, 3),
(747, 22, 33, NULL, 15),
(748, 22, 34, NULL, 1),
(749, 23, 33, NULL, 1),
(750, 18, NULL, 2, 1),
(751, 19, 9, NULL, 1),
(752, 20, 11, NULL, 1),
(753, 21, 9, NULL, 1),
(754, 22, 15, NULL, 1),
(755, 23, 15, NULL, 1),
(756, 24, 12, NULL, 1),
(757, 25, 12, NULL, 1),
(758, 26, 14, NULL, 1),
(759, 25, 32, NULL, 1),
(760, 26, 34, NULL, 1),
(761, 27, 18, NULL, 1),
(762, 28, 13, NULL, 1),
(763, 29, 23, NULL, 1),
(764, 30, 13, NULL, 1),
(765, 30, 18, NULL, 1),
(766, 30, 23, NULL, 1),
(767, 30, 32, NULL, 1),
(768, 31, 13, NULL, 1),
(769, 31, 18, NULL, 1),
(770, 31, 23, NULL, 1),
(771, 31, 27, NULL, 1),
(772, 31, 33, NULL, 1),
(773, 32, 33, NULL, 1),
(774, 33, 13, NULL, 1),
(775, 33, 18, NULL, 1),
(776, 33, 33, NULL, 1),
(777, 38, 13, NULL, 1),
(778, 38, 18, NULL, 1),
(779, 38, 22, NULL, 1),
(780, 38, 28, NULL, 1),
(781, 38, 32, NULL, 1),
(782, 39, 12, NULL, 1),
(783, 39, 18, NULL, 1),
(784, 39, 24, NULL, 1),
(785, 39, 31, NULL, 1),
(786, 39, 34, NULL, 1),
(787, 40, 14, NULL, 1),
(788, 40, 17, NULL, 1),
(789, 40, 23, NULL, 1),
(790, 40, 30, NULL, 1),
(791, 40, 33, NULL, 1),
(792, 41, 14, NULL, 1),
(793, 41, 18, NULL, 1),
(794, 41, 24, NULL, 1),
(795, 41, 28, NULL, 1),
(796, 41, 34, NULL, 1),
(797, 42, 14, NULL, 1),
(798, 42, 18, NULL, 1),
(799, 42, 24, NULL, 1),
(800, 42, 28, NULL, 1),
(801, 42, 34, NULL, 1),
(802, 43, 14, NULL, 1),
(803, 43, 18, NULL, 1),
(804, 43, 24, NULL, 1),
(805, 43, 28, NULL, 1),
(806, 43, 34, NULL, 1),
(807, 44, 13, NULL, 1),
(808, 44, 18, NULL, 1),
(809, 44, 23, NULL, 1),
(810, 44, 28, NULL, 1),
(811, 44, 34, NULL, 1),
(812, 45, 13, NULL, 1),
(813, 45, 18, NULL, 1),
(814, 45, 23, NULL, 1),
(815, 45, 28, NULL, 1),
(816, 45, 34, NULL, 1),
(817, 46, 13, NULL, 1),
(818, 46, 18, NULL, 1),
(819, 46, 23, NULL, 1),
(820, 46, 28, NULL, 1),
(821, 46, 34, NULL, 1),
(822, 47, 13, NULL, 1),
(823, 47, 18, NULL, 1),
(824, 47, 23, NULL, 1),
(825, 47, 28, NULL, 1),
(826, 47, 34, NULL, 1),
(827, 48, 13, NULL, 1),
(828, 48, 18, NULL, 1),
(829, 48, 23, NULL, 1),
(830, 48, 28, NULL, 1),
(831, 48, 34, NULL, 1),
(832, 49, 13, NULL, 1),
(833, 49, 18, NULL, 1),
(834, 49, 23, NULL, 1),
(835, 49, 28, NULL, 1),
(836, 49, 34, NULL, 1),
(837, 50, 13, NULL, 1),
(838, 50, 18, NULL, 1),
(839, 50, 23, NULL, 1),
(840, 50, 28, NULL, 1),
(841, 50, 34, NULL, 1),
(842, 51, 13, NULL, 1),
(843, 51, 18, NULL, 1),
(844, 51, 23, NULL, 1),
(845, 51, 28, NULL, 1),
(846, 51, 34, NULL, 1),
(847, 52, 13, NULL, 1),
(848, 52, 17, NULL, 1),
(849, 52, 24, NULL, 1),
(850, 52, 27, NULL, 1),
(851, 52, 34, NULL, 1),
(852, 53, 13, NULL, 1),
(853, 53, 18, NULL, 1),
(854, 53, 24, NULL, 1),
(855, 53, 28, NULL, 1),
(856, 53, 33, NULL, 1),
(857, 54, 13, NULL, 1),
(858, 54, 18, NULL, 1),
(859, 54, 24, NULL, 1),
(860, 54, 28, NULL, 1),
(861, 54, 33, NULL, 1),
(862, 55, 13, NULL, 1),
(863, 55, 18, NULL, 1),
(864, 55, 24, NULL, 1),
(865, 55, 28, NULL, 1),
(866, 55, 33, NULL, 1),
(867, 56, 13, NULL, 1),
(868, 56, 18, NULL, 1),
(869, 56, 24, NULL, 1),
(870, 56, 28, NULL, 1),
(871, 56, 33, NULL, 1),
(872, 57, 13, NULL, 1),
(873, 57, 18, NULL, 1),
(874, 57, 24, NULL, 1),
(875, 57, 28, NULL, 1),
(876, 57, 33, NULL, 1),
(877, 58, 13, NULL, 1),
(878, 58, 18, NULL, 1),
(879, 58, 24, NULL, 1),
(880, 58, 28, NULL, 1),
(881, 58, 33, NULL, 1),
(882, 59, 13, NULL, 1),
(883, 59, 18, NULL, 1),
(884, 59, 24, NULL, 1),
(885, 59, 28, NULL, 1),
(886, 59, 33, NULL, 1),
(887, 60, 13, NULL, 1),
(888, 60, 18, NULL, 1),
(889, 60, 24, NULL, 1),
(890, 60, 28, NULL, 1),
(891, 60, 33, NULL, 1),
(892, 61, 13, NULL, 1),
(893, 61, 18, NULL, 1),
(894, 61, 24, NULL, 1),
(895, 61, 28, NULL, 1),
(896, 61, 33, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `reservation_id`, `rating`, `comment`, `created_at`) VALUES
(1, 7, 4, 'dasdasdasd', '2025-01-29 09:32:42'),
(2, 5, 4, 'dasdasdasdad', '2025-01-29 09:33:15'),
(3, 12, 5, 'nagimas', '2025-02-02 14:15:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `user_type` enum('client','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` varchar(20) NOT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.jpg',
  `status` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `password`, `email`, `user_type`, `created_at`, `phone`, `profile_picture`, `status`) VALUES
(1, 'John Rix Domaoal', '$2y$10$u8srCTefgWiMvvwnjcS1uOipXGsJbXvFG/E3p6rweqxsXRmjZJ9/m', 'janggisdump@gmail.com', 'client', '2025-01-13 05:09:44', '09351455907', 'uploads/67a558ec408f8_github.jpg', ''),
(2, 'Hello World', '$2y$10$sYJAaf5TSld696qhSC.w5e4nZ0.lftIcAU53xZI2mW4I7kQo4akGG', 'domaoalj11@gmail.com', 'client', '2025-01-24 17:53:33', '09351455907', 'default.jpg', ''),
(3, 'jdsfbjsn', '$2y$10$4/g60pFg0M5lf/U5mdIbN.eNY8C3NQKClOtKH1QLDULL3znm2qdpi', 'markarvinbackup@gmail.com', 'client', '2025-01-24 17:59:57', '0123456789', 'default.jpg', ''),
(5, 'Hello World ', '$2y$10$sFEmrWN2xfi5rXcABAwazOoopvw0DXdHsmS2zNWAFlH5A/GwmTapm', 'johnrixdomaoal6@gmail.com', 'client', '2025-02-02 03:57:28', '09351455907', 'default.jpg', ''),
(6, 'John Rix Domaoal', '$2y$10$SfCCrEmzvshN079W5s44tOUxz7HFVdIIJVPlWDkWmprrN6ScKiUO2', 'johnrixbuenafe@gmail.com', 'client', '2025-02-02 14:06:28', '09351455907', 'default.jpg', ''),
(7, 'Tyron Gabriel Q. Paderes', '$2y$10$XVmRmDRTMZ3qMmFfQOwTt.cUzOT6ytWk1xZF5mm4XUmn55LNaALD6', 'tyronpaderes2018@gmail.com', 'client', '2025-02-02 15:09:05', '09060668618', 'default.jpg', ''),
(8, 'kevin pada', '$2y$10$E3wEdLrJlwt/giiCCYFKTO1brw1m6pPFr8aSp7cIBwHOiqfXG0ZCS', 'juliabanez07@gmail.com', 'client', '2025-02-03 07:52:58', '09164447185', 'default.jpg', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_us`
--
ALTER TABLE `about_us`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `catering_menu`
--
ALTER TABLE `catering_menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `contact_replies`
--
ALTER TABLE `contact_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contact_id` (`contact_id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `hero_section`
--
ALTER TABLE `hero_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notification_reads`
--
ALTER TABLE `notification_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_notification` (`reservation_id`,`admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_details`
--
ALTER TABLE `payment_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promotions`
--
ALTER TABLE `promotions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reservation_id`);

--
-- Indexes for table `reservation_catering`
--
ALTER TABLE `reservation_catering`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `reservation_menu`
--
ALTER TABLE `reservation_menu`
  ADD PRIMARY KEY (`reservation_menu_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_us`
--
ALTER TABLE `about_us`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `catering_menu`
--
ALTER TABLE `catering_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `contact_replies`
--
ALTER TABLE `contact_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `hero_section`
--
ALTER TABLE `hero_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT for table `notification_reads`
--
ALTER TABLE `notification_reads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_details`
--
ALTER TABLE `payment_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `promotions`
--
ALTER TABLE `promotions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `reservation_catering`
--
ALTER TABLE `reservation_catering`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `reservation_menu`
--
ALTER TABLE `reservation_menu`
  MODIFY `reservation_menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=897;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD CONSTRAINT `contact_messages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reservation_catering`
--
ALTER TABLE `reservation_catering`
  ADD CONSTRAINT `reservation_catering_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservation` (`reservation_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
