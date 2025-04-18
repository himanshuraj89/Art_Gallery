-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2025 at 05:39 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `art_gallery`
--

-- --------------------------------------------------------

--
-- Table structure for table `artists`
--

CREATE TABLE `artists` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `artworks`
--

CREATE TABLE `artworks` (
  `id` varchar(36) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `artist_name` varchar(255) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `artist` varchar(255) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `artist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `artworks`
--

INSERT INTO `artworks` (`id`, `title`, `description`, `price`, `image_url`, `category`, `artist_name`, `stock`, `artist`, `dimensions`, `created_at`, `updated_at`, `artist_id`) VALUES
('1000', 'Dark Soul', 'This artwork captures the essence of Soul through a Melancholic perspective. The artist created this piece using bold strokes.', 669.00, 'uploads/artworks/Jagannath-Paul-Rhythm-Art-Artist.jpg', 'Expressionism', 'Jane Smith', 10, 'Emma Thompson', '15\" × 22\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('953c174d-71cb-483f-9e2e-6549e477be90', '7 Horshes', '', 1000.00, 'uploads/artworks/953c174d-71cb-483f-9e2e-6549e477be90.jpg', 'Photography', NULL, 0, 'Mr.Devid', '434x333', '2025-04-18 07:10:15', '2025-04-18 08:41:15', NULL),
('954', 'Joyful Cosmos', 'This artwork captures the essence of Passage through a Ethereal perspective. The artist created this piece using subtle textures.', 457.00, 'uploads/artworks/350_p-1.jpg', 'Landscape', 'Sara Johnson', 6, 'John Doe', '30\" × 8\"', '2025-04-18 04:26:48', '2025-04-18 14:18:34', NULL),
('955', 'Chaotic Nature', '', 3410.00, 'uploads/artworks/44-1.jpg', 'Paintings', 'Sara Johnson', 0, 'Jane Smith', '22&#34; × 46&#34;', '2025-04-18 04:26:48', '2025-04-18 14:01:58', NULL),
('956', 'Joyful Journey', 'This artwork captures the essence of Journey through a Chaotic perspective. The artist created this piece using layered composition.', 1768.00, 'uploads/artworks/7-Horse-Seven-Running-Horse-vastu-Rhythm-Art-Gallery-Mumbai-2-1.jpg', 'Landscape', 'Sara Johnson', 7, 'David Wilson', '41\" × 36\"', '2025-04-18 04:26:48', '2025-04-18 08:56:57', NULL),
('957', 'Ethereal Memory', 'This artwork captures the essence of Reflection through a Melancholic perspective. The artist created this piece using layered composition.', 1503.00, 'uploads/artworks/A-J-Moujan-Rhythm-Art-Artist.jpg', 'Still Life', 'Emma Thompson', 5, 'John Doe', '10\" × 23\"', '2025-04-18 04:26:48', '2025-04-18 08:41:15', NULL),
('958', 'Ethereal Horizon', '', 1158.00, 'uploads/artworks/Abstract-Rhythm-Art-Gallery-Mumbai-1.jpg', 'Paintings', 'David Wilson', 10, 'Jane Smith', '16&#34; × 43&#34;', '2025-04-18 04:26:48', '2025-04-18 08:39:00', NULL),
('959', 'Joyful Symphony', 'This artwork captures the essence of Memory through a Dark perspective. The artist created this piece using layered composition.', 1813.00, 'uploads/artworks/Amit-Srivastava-RHYTHM-ART-MUMBAI.jpg', 'Abstract', 'John Doe', 2, 'Alex Rivera', '17\" × 22\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('960', 'Beautiful Nature', 'This artwork captures the essence of Nature through a Serene perspective. The artist created this piece using vibrant colors.', 4866.00, 'uploads/artworks/Anand-Panchal-Rhythm-Art-Artist.jpg', 'Modern', 'Morgan Chen', 8, 'David Wilson', '36\" × 44\"', '2025-04-18 04:26:48', '2025-04-18 14:20:27', NULL),
('961', 'Chaotic Journey', '', 1642.00, 'uploads/artworks/Anil-Kumar-Yadav-Rhythm-Art-Artist.jpg', 'Paintings', 'John Doe', 4, 'Morgan Chen', '25&#34; × 48&#34;', '2025-04-18 04:26:48', '2025-04-18 08:39:09', NULL),
('962', 'Vibrant Dreams', 'This artwork captures the essence of Dreams through a Chaotic perspective. The artist created this piece using delicate techniques.', 2338.00, 'uploads/artworks/Anita-Kumar-Rhythm-Art-Artist.jpg', 'Surrealism', 'David Wilson', 3, 'Morgan Chen', '18\" × 45\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('963', 'Beautiful Reflection', 'This artwork captures the essence of Soul through a Mysterious perspective. The artist created this piece using subtle textures.', 3457.00, 'uploads/artworks/Arpan-Bhowmik-Rhythm-Art-Artist.jpg', 'Still Life', 'Emma Thompson', 3, 'Emma Thompson', '48\" × 48\"', '2025-04-18 04:26:48', '2025-04-18 14:20:03', NULL),
('964', 'Mysterious Journey', 'This artwork captures the essence of Journey through a Vibrant perspective. The artist created this piece using delicate techniques.', 4023.00, 'uploads/artworks/Artist-Anuradha-Thakur-Rhythm-Art-Gallery-Mumbai.jpg', 'Abstract', 'Morgan Chen', 2, 'Alex Rivera', '34\" × 18\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('965', 'Vibrant Passage', 'This artwork captures the essence of Cosmos through a Vibrant perspective. The artist created this piece using subtle textures.', 3527.00, 'uploads/artworks/Artist-Jayashree-Patankar-1.jpg', 'Still Life', 'Morgan Chen', 9, 'Alex Rivera', '39\" × 27\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('966', 'Mysterious Reflection', 'This artwork captures the essence of Symphony through a Serene perspective. The artist created this piece using subtle textures.', 2673.00, 'uploads/artworks/Artist-K-K-Rajshekhar.jpg', 'Expressionism', 'David Wilson', 3, 'Morgan Chen', '33\" × 22\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('967', 'Ethereal Soul', 'This artwork captures the essence of Cosmos through a Chaotic perspective. The artist created this piece using layered composition.', 2311.00, 'uploads/artworks/Artist-K-Prakash-1.jpg', 'Expressionism', 'David Wilson', 1, 'David Wilson', '9\" × 33\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('968', 'Joyful Symphony', 'This artwork captures the essence of Horizon through a Beautiful perspective. The artist created this piece using bold strokes.', 4113.00, 'uploads/artworks/Artist-Mihir-Das-39-x-60-inches-copy.jpg', 'Abstract', 'Morgan Chen', 2, 'John Doe', '15\" × 55\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('969', 'Dark Nature', '', 4579.00, 'uploads/artworks/Artist-Partho-Dutta-1.jpg', 'Paintings', 'David Wilson', 6, 'David Wilson', '14&#34; × 60&#34;', '2025-04-18 04:26:48', '2025-04-18 08:39:21', NULL),
('970', 'Serene Nature', 'This artwork captures the essence of Cosmos through a Beautiful perspective. The artist created this piece using layered composition.', 1195.00, 'uploads/artworks/Artist-Purnendu-Mandal-1-1.jpg', 'Contemporary', 'Emma Thompson', 7, 'Sara Johnson', '30\" × 45\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('971', 'Chaotic Horizon', 'This artwork captures the essence of Nature through a Chaotic perspective. The artist created this piece using vibrant colors.', 3932.00, 'uploads/artworks/Artist-Sachin-Akalekar-for-rhythmartgallery.com-copy.jpg', 'Modern', 'David Wilson', 4, 'John Doe', '45\" × 43\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('972', 'Dark Horizon', 'This artwork captures the essence of Journey through a Ethereal perspective. The artist created this piece using bold strokes.', 1279.00, 'uploads/artworks/Artist-Umakant-Tawde-1.jpg', 'Modern', 'Jane Smith', 4, 'David Wilson', '37\" × 30\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('973', 'Dark Horizon', 'This artwork captures the essence of Soul through a Dark perspective. The artist created this piece using delicate techniques.', 1329.00, 'uploads/artworks/Arvind-Mahajan-Artist-Rhythm-Art-Mumbai.jpg', 'Surrealism', 'Morgan Chen', 8, 'Morgan Chen', '20\" × 24\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('974', 'Vibrant Journey', 'This artwork captures the essence of Passage through a Dark perspective. The artist created this piece using vibrant colors.', 1289.00, 'uploads/artworks/Ashif-Hossain-Rhythm-Art-Artist-Mumbai-1.jpg', 'Portrait', 'Sara Johnson', 0, 'Jane Smith', '16\" × 21\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('975', 'Ethereal Reflection', 'This artwork captures the essence of Reflection through a Serene perspective. The artist created this piece using layered composition.', 4588.00, 'uploads/artworks/Ashish-Kamble-Rhythm-Art-Artist.jpg', 'Portrait', 'Sara Johnson', 0, 'David Wilson', '35\" × 26\"', '2025-04-18 04:26:48', '2025-04-18 09:05:44', NULL),
('976', 'Mysterious Soul', 'This artwork captures the essence of Nature through a Ethereal perspective. The artist created this piece using subtle textures.', 4575.00, 'uploads/artworks/Ashoka-Rhythm-Art-Mumbai-1.jpg', 'Modern', 'John Doe', 2, 'David Wilson', '20\" × 24\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('977', 'Mysterious Soul', 'This artwork captures the essence of Horizon through a Dark perspective. The artist created this piece using vibrant colors.', 2534.00, 'uploads/artworks/Bharti-Ambi-Rhythm-Art.jpg', 'Abstract', 'John Doe', 6, 'Morgan Chen', '43\" × 25\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('978', 'Chaotic Journey', 'This artwork captures the essence of Dreams through a Beautiful perspective. The artist created this piece using subtle textures.', 1027.00, 'uploads/artworks/Bhiva-Pundekar-Rhythm-Art-Artist.jpg', 'Still Life', 'Sara Johnson', 10, 'Sara Johnson', '11\" × 58\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('979', 'Ethereal Dreams', 'This artwork captures the essence of Symphony through a Beautiful perspective. The artist created this piece using vibrant colors.', 1269.00, 'uploads/artworks/Biltu-Rhythm-Art-Artist.jpg', 'Still Life', 'Morgan Chen', 2, 'Morgan Chen', '24\" × 59\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('980', 'Mysterious Journey', 'This artwork captures the essence of Horizon through a Melancholic perspective. The artist created this piece using delicate techniques.', 400.00, 'uploads/artworks/Chandra-Shekar-Morkonda-Rhythm-Art-Mumbai-Artist-1.jpg', 'Modern', 'Sara Johnson', 2, 'Jane Smith', '29\" × 46\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('981', 'Joyful Soul', 'This artwork captures the essence of Reflection through a Melancholic perspective. The artist created this piece using bold strokes.', 1728.00, 'uploads/artworks/Chandrakant-Tajbije-Artist-Nandi-Rhythm-Art-Gallery-Mumbai.jpg', 'Surrealism', 'John Doe', 4, 'David Wilson', '43\" × 25\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('982', 'Mysterious Symphony', 'This artwork captures the essence of Soul through a Mysterious perspective. The artist created this piece using subtle textures.', 4088.00, 'uploads/artworks/Datta-Bansode-Artist-1.jpg', 'Abstract', 'John Doe', 6, 'Jane Smith', '43\" × 37\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('983', 'Mysterious Journey', 'This artwork captures the essence of Horizon through a Chaotic perspective. The artist created this piece using layered composition.', 1526.00, 'uploads/artworks/Deb-Rhythm-Art-Artist-Mumbai-1.jpg', 'Contemporary', 'Sara Johnson', 2, 'David Wilson', '30\" × 50\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('984', 'Ethereal Reflection', 'This artwork captures the essence of Passage through a Chaotic perspective. The artist created this piece using layered composition.', 2099.00, 'uploads/artworks/Dilip_Chaudhury_1-1.jpg', 'Still Life', 'Morgan Chen', 7, 'Jane Smith', '9\" × 34\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('985', 'Serene Memory', 'This artwork captures the essence of Symphony through a Chaotic perspective. The artist created this piece using vibrant colors.', 3865.00, 'uploads/artworks/Dinkar-Jadhav-Rhythm-Art-Artist.jpg', 'Expressionism', 'Alex Rivera', 6, 'Sara Johnson', '9\" × 59\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('986', 'Chaotic Dreams', 'This artwork captures the essence of Symphony through a Mysterious perspective. The artist created this piece using vibrant colors.', 2277.00, 'uploads/artworks/Dipti-Kumar-Elephant-Artist-1.jpg', 'Still Life', 'Morgan Chen', 0, 'Jane Smith', '31\" × 53\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('987', 'Joyful Passage', 'This artwork captures the essence of Symphony through a Chaotic perspective. The artist created this piece using delicate techniques.', 3478.00, 'uploads/artworks/Dnyaneshwar-Mane-Artist-1-1.jpg', 'Abstract', 'Sara Johnson', 7, 'Emma Thompson', '12\" × 37\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('988', 'Beautiful Horizon', 'This artwork captures the essence of Dreams through a Beautiful perspective. The artist created this piece using subtle textures.', 3199.00, 'uploads/artworks/Ganapati-Hegde-Rhythm-Art-Artist-.jpg', 'Portrait', 'David Wilson', 10, 'Alex Rivera', '14\" × 20\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('989', 'Melancholic Symphony', 'This artwork captures the essence of Dreams through a Vibrant perspective. The artist created this piece using layered composition.', 2758.00, 'uploads/artworks/Ganesh-Panda-Rhythm-Art-Gallery-Mumbai.jpg', 'Surrealism', 'Jane Smith', 9, 'David Wilson', '39\" × 60\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('990', 'Serene Soul', 'This artwork captures the essence of Symphony through a Dark perspective. The artist created this piece using delicate techniques.', 4230.00, 'uploads/artworks/Ghanshyam-Gupta-Artist-2-1.jpg', 'Contemporary', 'Emma Thompson', 7, 'Jane Smith', '21\" × 44\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('991', 'Chaotic Cosmos', 'This artwork captures the essence of Passage through a Mysterious perspective. The artist created this piece using vibrant colors.', 2524.00, 'uploads/artworks/Giram-Eknath-Artist-1.jpg', 'Contemporary', 'Alex Rivera', 2, 'Jane Smith', '35\" × 29\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('992', 'Beautiful Symphony', 'This artwork captures the essence of Passage through a Joyful perspective. The artist created this piece using bold strokes.', 4577.00, 'uploads/artworks/Govind-Dumbre-Rhythm-Art-Artist-.jpg', 'Contemporary', 'Emma Thompson', 0, 'John Doe', '9\" × 26\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('993', 'Melancholic Soul', 'This artwork captures the essence of Passage through a Beautiful perspective. The artist created this piece using delicate techniques.', 2821.00, 'uploads/artworks/H-R-Das-Rhythm-Art-Artist.jpg', 'Still Life', 'Sara Johnson', 10, 'Alex Rivera', '19\" × 39\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('994', 'Vibrant Nature', 'This artwork captures the essence of Passage through a Chaotic perspective. The artist created this piece using delicate techniques.', 4410.00, 'uploads/artworks/Harendra-Shah-Rhythm-Art-Artist.jpg', 'Contemporary', 'Emma Thompson', 6, 'David Wilson', '31\" × 43\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('995', 'Joyful Memory', 'This artwork captures the essence of Soul through a Serene perspective. The artist created this piece using subtle textures.', 4229.00, 'uploads/artworks/Harish-Kumar-Artist-1.jpg', 'Landscape', 'Sara Johnson', 3, 'Emma Thompson', '33\" × 17\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('996', 'Melancholic Reflection', 'This artwork captures the essence of Memory through a Beautiful perspective. The artist created this piece using bold strokes.', 4839.00, 'uploads/artworks/Hemlata-Uikey-Rhythm-Art-Mumbai.jpg', 'Portrait', 'John Doe', 2, 'John Doe', '41\" × 52\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('997', 'Serene Cosmos', 'This artwork captures the essence of Memory through a Chaotic perspective. The artist created this piece using layered composition.', 3618.00, 'uploads/artworks/Hit-Hari-Rhythm-Art-Mumbai.jpg', 'Still Life', 'Jane Smith', 9, 'Alex Rivera', '20\" × 40\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('998', 'Beautiful Passage', 'This artwork captures the essence of Horizon through a Beautiful perspective. The artist created this piece using bold strokes.', 4299.00, 'uploads/artworks/Indian_Artist_Prakash_Deshmukh-1.jpg', 'Abstract', 'John Doe', 6, 'Emma Thompson', '15\" × 21\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL),
('999', 'Ethereal Reflection', 'This artwork captures the essence of Symphony through a Dark perspective. The artist created this piece using layered composition.', 2149.00, 'uploads/artworks/Indian_Artist_Shashikant_Patade-1.jpg', 'Contemporary', 'Alex Rivera', 6, 'Morgan Chen', '43\" × 21\"', '2025-04-18 04:26:48', '2025-04-18 04:26:48', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` varchar(36) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `artwork_id` varchar(36) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `artwork_id`, `quantity`, `created_at`) VALUES
('9f0ce519-d0e0-42d6-92bf-de0d39a8284a', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', '954', 1, '2025-04-18 15:16:08');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('new','read','responded') DEFAULT 'new'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(4, 'ankit vishwakarma', 'akash369soni@gmail.com', 'maansvi ai', 'sd', '2025-04-18 15:25:30', 'new');

-- --------------------------------------------------------

--
-- Table structure for table `newsletter_subscribers`
--

CREATE TABLE `newsletter_subscribers` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subscription_date` datetime NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsletter_subscribers`
--

INSERT INTO `newsletter_subscribers` (`id`, `email`, `subscription_date`, `is_active`) VALUES
(1, 'akash369soni@gmail.com', '2025-04-18 19:59:40', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` varchar(36) NOT NULL,
  `user_id` varchar(36) DEFAULT NULL,
  `status` enum('pending','processing','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total_amount`, `created_at`) VALUES
('24460de7-3d76-4003-b66a-7e7f9996a16b', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'completed', 457.00, '2025-04-18 14:18:34'),
('43e80904-64a5-4757-ae2d-0f3e2006df4a', '399f9e4d-b1c1-444c-b484-3552b8175e21', 'pending', 457.00, '2025-04-18 09:21:31'),
('58f5b60f-1909-474b-ae5b-8796787b4cf7', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'completed', 4866.00, '2025-04-18 14:20:27'),
('6e60c7f8-d23a-4c59-9d6e-f6e29a37efe8', '399f9e4d-b1c1-444c-b484-3552b8175e21', 'pending', 5761.00, '2025-04-18 08:56:57'),
('b0dd9a8e-ed52-44e1-9de9-231b56d49267', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'pending', 10012.00, '2025-04-18 08:41:15'),
('d30ebaa4-deae-495e-b863-264141159711', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'completed', 10230.00, '2025-04-18 14:01:58'),
('dfcf6678-da4f-4015-b26d-7f5477e0d7e5', '399f9e4d-b1c1-444c-b484-3552b8175e21', 'pending', 13764.00, '2025-04-18 09:05:44'),
('f2222f07-854b-4e6b-969c-3264ec27c338', '8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'completed', 3457.00, '2025-04-18 14:20:03');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` varchar(36) NOT NULL,
  `order_id` varchar(36) DEFAULT NULL,
  `artwork_id` varchar(36) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price_at_time` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `artwork_id`, `quantity`, `price_at_time`) VALUES
('07616a31-7eab-44bf-a5e2-4fe5f7383cb8', '976b57eb-b698-400b-b1b8-ba8d7e8cad9c', NULL, 2, 33.00),
('0d1d1723-2fda-4043-a9d3-48b290ae863b', 'dfcf6678-da4f-4015-b26d-7f5477e0d7e5', '975', 3, 4588.00),
('21c933ad-b862-4d35-822e-7abc43b59ddd', 'fb223cb5-af03-4d1f-9f71-42a13efbc910', '953c174d-71cb-483f-9e2e-6549e477be90', 1, 1000.00),
('298eff79-2730-475c-bd82-7d53c2d55201', '6e60c7f8-d23a-4c59-9d6e-f6e29a37efe8', '954', 1, 457.00),
('2c3a8d6b-69d2-4377-8e36-f9aea896ce16', '43e80904-64a5-4757-ae2d-0f3e2006df4a', '954', 1, 457.00),
('2f4b367a-b1dd-4f65-990a-0ae2f902d33d', '9f148356-102b-42ad-aaee-44175ab6b766', NULL, 1, 33.00),
('30d51c51-e3fa-4b4e-a9ba-4e4108edb05c', '75458744-4f03-432a-b930-cc4fe645a527', '953c174d-71cb-483f-9e2e-6549e477be90', 1, 1000.00),
('4171ca21-76b3-4c87-ad67-e88762948b92', '018beb62-fe94-48d1-baff-56174a204c77', NULL, 1, 33.00),
('4bbf0f6b-9b9a-405d-9bfb-99110022b9f6', '24460de7-3d76-4003-b66a-7e7f9996a16b', '954', 1, 457.00),
('54b8d2bf-8f5e-4bd4-86d0-4fcf0747c970', 'b0dd9a8e-ed52-44e1-9de9-231b56d49267', '953c174d-71cb-483f-9e2e-6549e477be90', 4, 1000.00),
('6cea4906-bcef-442c-b79c-c67c7854d01e', 'fe74db7c-f196-4af6-b110-b4670f189788', NULL, 2, 33.00),
('71846330-cc81-484b-a842-bbb861b93def', 'b0dd9a8e-ed52-44e1-9de9-231b56d49267', '957', 4, 1503.00),
('7d13964e-bcf0-4114-9be8-6cf9650f75af', '65362a12-bd22-4680-950d-7b1ca7bf3264', NULL, 1, 33.00),
('87cb559c-86fc-4fda-b986-4fdcfa809772', '57cec4de-531b-4667-97a9-d8623a49bdd2', '953c174d-71cb-483f-9e2e-6549e477be90', 6, 1000.00),
('b780177f-2b00-48c6-bc0b-7dd7f8cd62ac', 'f2222f07-854b-4e6b-969c-3264ec27c338', '963', 1, 3457.00),
('bac992c1-5c07-4eae-b87f-b62d248665c5', '75458744-4f03-432a-b930-cc4fe645a527', '954', 1, 457.00),
('c10e4879-123d-43ff-bdeb-6667fe04d45f', '9f7600a6-6fb9-403c-a345-3579103512ad', NULL, 1, 33.00),
('c6152003-d095-4a5b-9792-d46a373c29d3', '765fc89e-0ea0-490f-8b9e-39bf3489e4ae', '953c174d-71cb-483f-9e2e-6549e477be90', 6, 1000.00),
('cf70d24f-fe2a-43ce-b533-de985e981f71', 'd30ebaa4-deae-495e-b863-264141159711', '955', 3, 3410.00),
('cfa93c48-7405-4865-af0f-78d92f02f647', 'e810d688-bc7e-4eaa-9bb1-4149bfc0aa6a', NULL, 2, 33.00),
('cffe36b4-7c96-47ca-86ca-3a830f5ea15e', '58f5b60f-1909-474b-ae5b-8796787b4cf7', '960', 1, 4866.00),
('d5fb9d88-3f0b-462d-8ce4-55de8df5839c', '6e60c7f8-d23a-4c59-9d6e-f6e29a37efe8', '956', 3, 1768.00),
('e4c4b00c-d52d-4392-8b28-55d328ca6b17', '0ddb2217-b270-4353-baad-5b5a20c75ca9', NULL, 1, 33.00),
('fbb1ec9d-c062-454f-b06d-7feb510a4fc6', '10a5d9f5-9f78-4982-bfc0-d011ec86e84d', '953c174d-71cb-483f-9e2e-6549e477be90', 1, 1000.00);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` varchar(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pending_artworks`
--

CREATE TABLE `pending_artworks` (
  `id` varchar(36) NOT NULL,
  `artwork_id` varchar(36) DEFAULT NULL,
  `contributor_id` varchar(36) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `artist` varchar(255) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(36) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `role` enum('user','admin','contributor') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `role`, `created_at`) VALUES
('399f9e4d-b1c1-444c-b484-3552b8175e21', 'kartik@gmail.com', '$2y$10$thUT5QDXn1DRNJkgHUvOFeL6i5KgaqD9yj.5BbuTFzEsCrqn7Gsym', 'kartik', 'user', '2025-04-18 08:35:23'),
('8ad3b9e8-0787-40f6-b9c9-438e6a45a999', 'ankitvishwa114@gmail.com', '$2y$10$FX9vb77Nzkhu9mENITJlEOXZy854GXubWax9D6OSvu0XWOW5uPYTe', 'Ankit vishwakarma', 'admin', '2025-04-17 18:06:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artists`
--
ALTER TABLE `artists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `artworks`
--
ALTER TABLE `artworks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `artist_id` (`artist_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `artwork_id` (`artwork_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `pending_artworks`
--
ALTER TABLE `pending_artworks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contributor_id` (`contributor_id`);

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
-- AUTO_INCREMENT for table `artists`
--
ALTER TABLE `artists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `newsletter_subscribers`
--
ALTER TABLE `newsletter_subscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artworks`
--
ALTER TABLE `artworks`
  ADD CONSTRAINT `artworks_ibfk_1` FOREIGN KEY (`artist_id`) REFERENCES `artists` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`artwork_id`) REFERENCES `artworks` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`email`) REFERENCES `users` (`email`);

--
-- Constraints for table `pending_artworks`
--
ALTER TABLE `pending_artworks`
  ADD CONSTRAINT `pending_artworks_ibfk_1` FOREIGN KEY (`contributor_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
