-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 16, 2026 at 08:44 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laravel`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `title`, `content`, `created_at`, `updated_at`) VALUES
(1, 2, 'My first game review', 'I loved playing Elden Ring. The world is huge and mysterious!', '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(2, 2, 'Top 5 RPGs of all time', 'In my opinion, Dark Souls and The Witcher 3 are untouchable classics.', '2026-06-16 22:11:31', '2026-06-16 22:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel_cache_user-active-1', 'b:1;', 1781642801),
('laravel_cache_user-active-21', 'b:1;', 1781642735);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `commentable_id` bigint UNSIGNED DEFAULT NULL,
  `commentable_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `parent_id`, `content`, `created_at`, `updated_at`, `commentable_id`, `commentable_type`) VALUES
(1, 3, NULL, 'Totally agree, Elden Ring is wild!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 4, 'App\\Models\\Game'),
(2, 2, NULL, 'Thanks for sharing your review!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 1, 'App\\Models\\Blog'),
(3, 3, NULL, 'I’d add Skyrim to that RPG list!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 1, 'App\\Models\\Blog'),
(4, 2, NULL, 'Glad someone remembers Dark Souls 🖤', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 4, 'App\\Models\\Game'),
(5, 3, NULL, 'This blog is awesome!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 1, 'App\\Models\\Blog'),
(6, 1, NULL, 'Glad to be here! Looking forward to discussing the latest patches.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 1, 'App\\Models\\Thread'),
(7, 1, NULL, 'Awesome addition to the site. Thanks admin.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 1, 'App\\Models\\Thread'),
(8, 1, NULL, 'Definitely stack agility. If you time your dodges right, phase 2 becomes a cakewalk.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 2, 'App\\Models\\Thread'),
(9, 1, NULL, 'Tank builds work too, but the fight takes twice as long. Good luck!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 2, 'App\\Models\\Thread'),
(10, 1, NULL, 'Custom lobbies would be huge for the community scene.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 3, 'App\\Models\\Thread'),
(11, 1, NULL, 'Finally, that level 3 crash was driving me crazy. Thanks for the quick fix!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 4, 'App\\Models\\Thread'),
(12, 1, NULL, 'Agreed. Pure turn-based gives you actual time to think strategically.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 5, 'App\\Models\\Thread'),
(13, 1, NULL, 'Found a UI clipping bug when resolution is set to ultrawide.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 6, 'App\\Models\\Thread'),
(14, 1, NULL, 'Beta was super smooth, can\'t wait for full release.', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 6, 'App\\Models\\Thread'),
(15, 1, 6, 'sdasdasddsaasd', '2026-06-16 23:41:22', '2026-06-16 23:41:22', 1, 'App\\Models\\Thread');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `games`
--

CREATE TABLE `games` (
  `game_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `img_src` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_rating` decimal(3,1) NOT NULL DEFAULT '0.0',
  `ratings_count` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `games`
--

INSERT INTO `games` (`game_id`, `title`, `description`, `img_src`, `average_rating`, `ratings_count`, `created_at`, `updated_at`) VALUES
(1, 'The Legend of Zelda: Breath of the Wild', 'An open-world action-adventure game where you explore the vast lands of Hyrule.', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSTTbvEycJxlpEdRQOxUQaHdkZdW63veWvFdA&s', 0.0, 0, NULL, NULL),
(2, 'Red Dead Redemption 2', 'A western-themed open-world game with a deep narrative and immersive gameplay.', 'https://image.api.playstation.com/gs2-sec/appkgo/prod/CUSA08519_00/12/i_3da1cf7c41dc7652f9b639e1680d96436773658668c7dc3930c441291095713b/i/icon0.png', 0.0, 0, NULL, NULL),
(3, 'Minecraft', 'A sandbox game where players build, explore, and survive in a blocky world.', 'https://upload.wikimedia.org/wikipedia/ru/f/f4/Minecraft_Cover_Art.png', 0.0, 0, NULL, NULL),
(4, 'Elden Ring', 'An action RPG set in a vast open world with deep lore and challenging combat.', 'https://gamestorecolombia.com/files/images/productos/1639688027-elden-ring-ps5-pre-orden.jpg', 0.0, 0, NULL, NULL),
(5, 'Dark Souls', 'The Legend', 'https://upload.wikimedia.org/wikipedia/en/8/8d/Dark_Souls_Cover_Art.jpg', 0.0, 0, NULL, NULL),
(6, 'Kenshi', 'Test description', '/storage/game_images/fZKY10CipxZiD7SvtkoXQeEMBji70z4AjWUaoKb7.jpg', 8.0, 1, '2026-06-16 23:13:57', '2026-06-16 23:13:57');

-- --------------------------------------------------------

--
-- Table structure for table `game_mechanic`
--

CREATE TABLE `game_mechanic` (
  `id` bigint UNSIGNED NOT NULL,
  `game_id` bigint UNSIGNED NOT NULL,
  `mechanic_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_mechanic`
--

INSERT INTO `game_mechanic` (`id`, `game_id`, `mechanic_id`, `created_at`, `updated_at`) VALUES
(1, 6, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `game_tag`
--

CREATE TABLE `game_tag` (
  `id` bigint UNSIGNED NOT NULL,
  `game_id` bigint UNSIGNED NOT NULL,
  `tag_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `game_tag`
--

INSERT INTO `game_tag` (`id`, `game_id`, `tag_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 1, NULL, NULL),
(3, 2, 4, NULL, NULL),
(4, 3, 1, NULL, NULL),
(5, 3, 3, NULL, NULL),
(6, 4, 1, NULL, NULL),
(7, 4, 2, NULL, NULL),
(8, 4, 4, NULL, NULL),
(9, 5, 2, NULL, NULL),
(10, 5, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `game_user`
--

CREATE TABLE `game_user` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `game_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` bigint UNSIGNED NOT NULL,
  `session_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_id` bigint UNSIGNED NOT NULL,
  `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mechanics`
--

CREATE TABLE `mechanics` (
  `mechanic_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mechanics`
--

INSERT INTO `mechanics` (`mechanic_id`, `title`, `content`, `approved`, `created_at`, `updated_at`) VALUES
(1, 'mmasdasdas', 'sdsadsadasdasdasd', 1, '2026-06-16 23:41:00', '2026-06-16 23:41:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_05_12_173559_create_games_table', 1),
(5, '2025_05_15_201048_create_game_user_table', 1),
(6, '2025_05_17_190438_add_role_to_users_table', 1),
(7, '2025_06_29_171106_create_comments_table', 1),
(8, '2025_07_29_170744_create_profiles_table', 1),
(9, '2025_07_31_060316_add_banned_to_users_table', 1),
(10, '2025_08_01_054158_add_location_to_profiles_table', 1),
(11, '2025_08_01_061157_create_blogs_table', 1),
(12, '2025_08_01_072848_create_images_table', 1),
(13, '2025_08_01_074513_add_session_id_to_images_table', 1),
(14, '2025_08_01_214109_update_comments_for_polymorphism', 1),
(15, '2026_03_26_195130_create_ratings_table', 1),
(16, '2026_03_26_204746_create_reviews_table', 1),
(17, '2026_04_01_181703_create_table_projects', 1),
(18, '2026_04_01_183859_create_table_threads', 1),
(19, '2026_05_19_193751_create_tags_table', 1),
(20, '2026_05_19_194019_create_game_tag_table', 1),
(21, '2026_05_26_203203_create_mechanics_table', 1),
(22, '2026_05_26_203336_create_game_mechanic_table', 1),
(23, '2026_05_27_183225_add_aggregates_to_games_table', 1),
(24, '2026_06_04_184204_create_project_teams_pivot_tables', 1),
(25, '2026_06_09_194038_add_parent_id_to_comments_table', 1),
(26, '2026_06_11_180646_create_project_mechanic_table', 1),
(27, '2026_06_11_181920_add_approved_to_mechanics_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

CREATE TABLE `profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female','other') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about_me` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `avatar`, `birth_date`, `gender`, `about_me`, `created_at`, `updated_at`, `location`) VALUES
(1, 2, 'https://i.pravatar.cc/150?u=alice@example.com', '1996-06-16', 'male', 'Hi, I’m Alice and I love games!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 'Tokyo'),
(2, 3, 'https://i.pravatar.cc/150?u=bob@example.com', '1993-06-16', 'female', 'Hi, I’m Bob and I love games!', '2026-06-16 22:11:31', '2026-06-16 22:11:31', 'Tokyo'),
(3, 21, NULL, '1998-11-13', NULL, NULL, '2026-06-16 23:40:17', '2026-06-16 23:40:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `is_public` tinyint(1) NOT NULL DEFAULT '0',
  `icon_big` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_small` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `projects`
--

INSERT INTO `projects` (`id`, `title`, `content`, `is_public`, `icon_big`, `icon_small`, `created_at`, `updated_at`) VALUES
(1, 'project t', 'test text', 1, NULL, NULL, '2026-06-16 22:17:33', '2026-06-16 22:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_editors`
--

CREATE TABLE `project_editors` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_editors`
--

INSERT INTO `project_editors` (`id`, `project_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 4, '2026-06-16 22:17:33', '2026-06-16 22:17:33'),
(2, 1, 8, '2026-06-16 22:17:33', '2026-06-16 22:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_mechanic`
--

CREATE TABLE `project_mechanic` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `mechanic_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_owners`
--

CREATE TABLE `project_owners` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_owners`
--

INSERT INTO `project_owners` (`id`, `project_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-06-16 22:17:33', '2026-06-16 22:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `project_participants`
--

CREATE TABLE `project_participants` (
  `id` bigint UNSIGNED NOT NULL,
  `project_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `project_participants`
--

INSERT INTO `project_participants` (`id`, `project_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 5, '2026-06-16 22:17:33', '2026-06-16 22:17:33'),
(2, 1, 6, '2026-06-16 22:17:33', '2026-06-16 22:17:33'),
(3, 1, 20, '2026-06-16 22:17:33', '2026-06-16 22:17:33'),
(4, 1, 7, '2026-06-16 22:17:33', '2026-06-16 22:17:33');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `game_id` bigint UNSIGNED NOT NULL,
  `rating` tinyint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`id`, `user_id`, `game_id`, `rating`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 8, '2026-06-16 23:19:48', '2026-06-16 23:34:35');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `game_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `tag_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_r18` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`tag_id`, `title`, `description`, `is_r18`, `created_at`, `updated_at`) VALUES
(1, 'Open World', 'Games featuring vast, explorable landscapes.', 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(2, 'RPG', 'Role-playing games featuring character progression and deep lore.', 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(3, 'Sandbox', 'Games focused on creativity, building, and player freedom.', 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(4, 'Dark Fantasy', 'Grim, brutal, and mature fantasy settings.', 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `threads`
--

CREATE TABLE `threads` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `is_pinned` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `threads`
--

INSERT INTO `threads` (`id`, `user_id`, `title`, `content`, `is_locked`, `is_pinned`, `created_at`, `updated_at`) VALUES
(1, 1, 'Welcome to the Official Game Forum!', 'Please read the community guidelines before posting. Be respectful, helpful, and have fun!', 0, 1, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(2, 1, 'What is the best build for the final boss?', 'I am struggling on phase 2. Should I stack agility or go pure tank?', 0, 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(3, 1, 'Suggestions for the upcoming multiplayer update', 'We really need a dedicated match history log and custom lobbies for tournaments.', 0, 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(4, 1, 'Patch Notes v1.04 - Balance Adjustments & Bug Fixes', 'We have optimized loading screens and resolved the audio crash occurring in level 3.', 0, 1, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(5, 1, 'What classic roguelike mechanic should return?', 'Personally, I miss strict grid-based turn resolution. Real-time hybrid systems feel too chaotic sometimes.', 0, 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31'),
(6, 1, '[Archived] Open Beta Bug Reporting Thread', 'The open beta has officially concluded. Thank you all for your telemetry submissions!', 1, 0, '2026-06-16 22:11:31', '2026-06-16 22:11:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `banned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `banned`) VALUES
(1, 'Admin', 'admin@example.com', NULL, '$2y$12$By394RCv7CAewBOG8eLPGetXB5w6do7tv2k9aEGRoMTz24HGK5zGS', 'admin', NULL, '2026-06-16 22:11:31', '2026-06-16 23:41:41', 0),
(2, 'Alice', 'alice@example.com', NULL, '$2y$12$4BhZRWRjW7GkrUae3zXjAutxTgvyaTYmEod74wJjYfc1hvhSWfBP6', 'user', NULL, '2026-06-16 22:11:31', '2026-06-16 22:11:31', 0),
(3, 'Bob', 'bob@example.com', NULL, '$2y$12$OI39NVbbgp5HUVILL2LeceUOlKn3X4xgjL/IFLcUAEqHv5zYUMcIu', 'user', NULL, '2026-06-16 22:11:31', '2026-06-16 22:11:31', 0),
(4, 'Alex Leadman', 'lead@example.com', NULL, '$2y$12$hhv9vLDK.Mw20mRJPC56meMVc8QdXWGEhGTgYqSvAciXgJ5SrAzVm', 'user', NULL, '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(5, 'Jordan Editor', 'editor@example.com', NULL, '$2y$12$rRe.2ae4tZzbbmIBXt33bu6qL/dsziIswMV.gYTltMQto4BvBeWJe', 'user', NULL, '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(6, 'Neha Corwin', 'aborer@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'hsModU6baY', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(7, 'Rita Collins', 'cormier.jarrell@example.com', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'j4bLvI7Z7o', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(8, 'Cora Shields IV', 'hunter.bauch@example.net', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'Gj9fOFhS7w', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(9, 'Deondre Larkin', 'kenny89@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'zxCImePF79', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(10, 'Patrick Smith', 'rstroman@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'zHbrsbHe84', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(11, 'Prof. Kitty Blick DVM', 'rschowalter@example.net', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'Feb8YwGGd8', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(12, 'Dr. Loyal Hane DVM', 'brannon90@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', '4aajN0YuQC', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(13, 'Emmett Bednar PhD', 'tfahey@example.com', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'Fww2GPLmpM', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(14, 'Prof. Domenic Huels', 'mohammad47@example.com', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'XcuTQHIjNd', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(15, 'Bernita Block', 'ahill@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', '98N1BkQZfu', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(16, 'Courtney Kutch MD', 'yasmin93@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'Nz9Ghi2E5j', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(17, 'Allison Baumbach', 'orn.marguerite@example.net', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'QUyOGKWPkB', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(18, 'Alene Gislason', 'xchamplin@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'p4mutziQwG', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(19, 'Stephen Hudson', 'smurphy@example.org', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'ov0ezgkXWV', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(20, 'Otho Donnelly', 'margarette.christiansen@example.com', '2026-06-16 22:11:32', '$2y$12$xnbacVr5aMd.aImWMM5EFOgQoSoaN3KLJnkDcJPYQqxtYTi6UNq2O', 'user', 'KTlkR2D2Ap', '2026-06-16 22:11:32', '2026-06-16 22:11:32', 0),
(21, 'NullBrainReference', 'skapinzev@gmail.com', NULL, '$2y$12$Fez6yQ6s93PLWpfWjVDsH.AfCYXnczbG2GfUA4ANWz996ly13fuhS', 'user', 'LkqlhysVVXTvgrqm12NJJIKNX7Nf01RBDVVgpqk7wRAqjoYTL3nR53sfmhuB', '2026-06-16 22:57:09', '2026-06-16 23:40:35', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blogs_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `games`
--
ALTER TABLE `games`
  ADD PRIMARY KEY (`game_id`),
  ADD KEY `games_average_rating_index` (`average_rating`),
  ADD KEY `games_ratings_count_index` (`ratings_count`);

--
-- Indexes for table `game_mechanic`
--
ALTER TABLE `game_mechanic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `game_mechanic_game_id_mechanic_id_unique` (`game_id`,`mechanic_id`),
  ADD KEY `game_mechanic_mechanic_id_foreign` (`mechanic_id`);

--
-- Indexes for table `game_tag`
--
ALTER TABLE `game_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_tag_game_id_foreign` (`game_id`),
  ADD KEY `game_tag_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `game_user`
--
ALTER TABLE `game_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_user_user_id_foreign` (`user_id`),
  ADD KEY `game_user_game_id_foreign` (`game_id`);

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `images_blog_id_foreign` (`blog_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mechanics`
--
ALTER TABLE `mechanics`
  ADD PRIMARY KEY (`mechanic_id`),
  ADD UNIQUE KEY `mechanics_title_unique` (`title`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profiles_user_id_foreign` (`user_id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `project_editors`
--
ALTER TABLE `project_editors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_editors_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_editors_user_id_foreign` (`user_id`);

--
-- Indexes for table `project_mechanic`
--
ALTER TABLE `project_mechanic`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_mechanic_project_id_mechanic_id_unique` (`project_id`,`mechanic_id`),
  ADD KEY `project_mechanic_mechanic_id_foreign` (`mechanic_id`);

--
-- Indexes for table `project_owners`
--
ALTER TABLE `project_owners`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_owners_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_owners_user_id_foreign` (`user_id`);

--
-- Indexes for table `project_participants`
--
ALTER TABLE `project_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_participants_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_participants_user_id_foreign` (`user_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ratings_user_id_game_id_unique` (`user_id`,`game_id`),
  ADD KEY `ratings_game_id_foreign` (`game_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_user_id_foreign` (`user_id`),
  ADD KEY `reviews_game_id_foreign` (`game_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `threads`
--
ALTER TABLE `threads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `threads_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `games`
--
ALTER TABLE `games`
  MODIFY `game_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `game_mechanic`
--
ALTER TABLE `game_mechanic`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `game_tag`
--
ALTER TABLE `game_tag`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `game_user`
--
ALTER TABLE `game_user`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `mechanics`
--
ALTER TABLE `mechanics`
  MODIFY `mechanic_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_editors`
--
ALTER TABLE `project_editors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `project_mechanic`
--
ALTER TABLE `project_mechanic`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_owners`
--
ALTER TABLE `project_owners`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `project_participants`
--
ALTER TABLE `project_participants`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `tag_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `threads`
--
ALTER TABLE `threads`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_mechanic`
--
ALTER TABLE `game_mechanic`
  ADD CONSTRAINT `game_mechanic_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_mechanic_mechanic_id_foreign` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`mechanic_id`) ON DELETE CASCADE;

--
-- Constraints for table `game_tag`
--
ALTER TABLE `game_tag`
  ADD CONSTRAINT `game_tag_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`tag_id`) ON DELETE CASCADE;

--
-- Constraints for table `game_user`
--
ALTER TABLE `game_user`
  ADD CONSTRAINT `game_user_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `game_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_blog_id_foreign` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_editors`
--
ALTER TABLE `project_editors`
  ADD CONSTRAINT `project_editors_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_editors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_mechanic`
--
ALTER TABLE `project_mechanic`
  ADD CONSTRAINT `project_mechanic_mechanic_id_foreign` FOREIGN KEY (`mechanic_id`) REFERENCES `mechanics` (`mechanic_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_mechanic_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_owners`
--
ALTER TABLE `project_owners`
  ADD CONSTRAINT `project_owners_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_owners_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_participants`
--
ALTER TABLE `project_participants`
  ADD CONSTRAINT `project_participants_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `games` (`game_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `threads`
--
ALTER TABLE `threads`
  ADD CONSTRAINT `threads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
