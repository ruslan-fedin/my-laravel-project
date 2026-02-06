-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: 127.0.0.1    Database: laravel
-- ------------------------------------------------------
-- Server version	8.4.8

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `brigade_locations`
--

DROP TABLE IF EXISTS `brigade_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brigade_locations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `brigadier_id` bigint unsigned NOT NULL,
  `location_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brigade_locations_brigadier_id_foreign` (`brigadier_id`),
  CONSTRAINT `brigade_locations_brigadier_id_foreign` FOREIGN KEY (`brigadier_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brigade_locations`
--

/*!40000 ALTER TABLE `brigade_locations` DISABLE KEYS */;
INSERT INTO `brigade_locations` VALUES (1,2,'Новая пойма',NULL,'2026-02-04 19:26:34'),(2,7,'юг',NULL,'2026-02-04 19:26:31'),(3,5,'Старая пойма',NULL,'2026-02-04 16:35:46'),(4,4,'Ста',NULL,'2026-02-04 18:29:34');
/*!40000 ALTER TABLE `brigade_locations` ENABLE KEYS */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `middle_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `substitute_id` bigint unsigned DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `hired_at` date DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `employees_position_id_foreign` (`position_id`),
  KEY `employees_parent_id_foreign` (`parent_id`),
  KEY `employees_substitute_id_index` (`substitute_id`),
  CONSTRAINT `employees_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL,
  CONSTRAINT `employees_position_id_foreign` FOREIGN KEY (`position_id`) REFERENCES `positions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'Иванов','Иван','Иванович','active',NULL,NULL,NULL,NULL,NULL,NULL,3,2,1,'2026-02-04 14:47:20','2026-02-04 18:58:24',NULL),(2,'Панина','Татьяна',NULL,'active',NULL,NULL,NULL,NULL,NULL,NULL,2,3,1,'2026-02-04 14:47:36','2026-02-04 17:54:00',NULL),(3,'Федин','Руслан','Анатольевич','active',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,1,'2026-02-04 14:48:22','2026-02-04 14:48:22',NULL),(4,'Петров','Петр','Петрович','active',NULL,NULL,NULL,NULL,NULL,NULL,3,7,1,'2026-02-04 15:09:53','2026-02-04 19:21:14',NULL),(5,'Сидоров','Семен','Семеныч','active',NULL,NULL,NULL,NULL,NULL,NULL,3,7,1,'2026-02-04 15:10:10','2026-02-04 18:56:07',NULL),(6,'Артемов','Павел','Александрович','active',NULL,NULL,NULL,NULL,NULL,NULL,3,3,1,'2026-02-04 15:10:38','2026-02-04 19:38:28',NULL),(7,'Лукьянов','Алексей','Иванович','active',NULL,NULL,NULL,NULL,NULL,NULL,2,3,1,'2026-02-04 15:10:57','2026-02-04 19:39:47',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
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
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_17_051438_create_positions_table',1),(5,'2026_01_17_051503_create_statuses_table',1),(6,'2026_01_17_051522_create_employees_table',1),(7,'2026_01_17_051546_create_timesheets_table',1),(8,'2026_01_17_051554_create_timesheet_items_table',1),(9,'2026_01_17_053859_make_employee_dates_nullable',1),(10,'2026_01_17_085653_add_details_to_employees_table_v2',1),(11,'2026_01_17_090924_make_employee_fields_nullable',1),(12,'2026_01_17_091544_add_color_to_statuses_table',1),(13,'2026_01_17_092022_add_short_name_to_statuses_table',1),(14,'2026_01_18_194828_create_travel_timesheets_table',1),(15,'2026_01_18_195622_create_travel_records_table',1),(16,'2026_01_18_200955_create_travel_system_tables',1),(17,'2026_01_18_233827_create_travel_timesheet_items_table',1),(18,'2026_02_04_132437_add_soft_deletes_to_employees_table',1),(19,'2026_02_04_140821_add_dates_to_timesheets_table',1),(20,'2026_02_04_141144_change_employee_id_in_timesheets_table',1),(21,'2026_02_04_143432_add_hierarchy_to_employees',1),(22,'2026_02_04_144258_create_substitutions_table',1),(23,'2026_02_04_153226_create_brigade_locations_table',2),(24,'2026_02_04_155633_add_vacation_fields_to_employees_table',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'Мастер','2026-02-04 14:46:46','2026-02-04 14:46:46'),(2,'Бригадир','2026-02-04 14:46:53','2026-02-04 14:46:53'),(3,'Рабочий озх','2026-02-04 14:47:03','2026-02-04 14:47:03');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('bwNaxOg1stjD3d12JpozmeugW1tV3Egucbw1AHsA',1,'192.168.65.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.0.1 Safari/605.1.15','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiazRHNnd2clhCZlBFOWtGcHIwWHZSR0tnbzRHWnJuNERPaGppaHRDQSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly9sb2NhbGhvc3QvcG9zaXRpb25zIjtzOjU6InJvdXRlIjtzOjE1OiJwb3NpdGlvbnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9',1770234243);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;

--
-- Table structure for table `statuses`
--

DROP TABLE IF EXISTS `statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `statuses`
--

/*!40000 ALTER TABLE `statuses` DISABLE KEYS */;
INSERT INTO `statuses` VALUES (1,'Больничный','Б','#b1dd8c','2026-02-04 14:46:39','2026-02-04 19:43:26'),(2,'Не полный день','НД','#77bb41','2026-02-04 19:41:21','2026-02-04 19:41:21'),(3,'Неявка','Н','#fffc41','2026-02-04 19:41:37','2026-02-04 19:41:37'),(4,'Отпуск','О','#ffaa00','2026-02-04 19:41:57','2026-02-04 19:41:57'),(5,'Прогул','ПР','#791a3e','2026-02-04 19:42:21','2026-02-04 19:42:21'),(6,'Сверхурочная работа','С','#874efe','2026-02-04 19:42:49','2026-02-04 19:42:49'),(7,'Центр','Ц','#ff2600','2026-02-04 19:43:09','2026-02-04 19:43:09'),(8,'Явка','Я','#3b82f6','2026-02-04 19:43:38','2026-02-04 19:43:38');
/*!40000 ALTER TABLE `statuses` ENABLE KEYS */;

--
-- Table structure for table `substitutions`
--

DROP TABLE IF EXISTS `substitutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `substitutions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `absent_id` bigint unsigned NOT NULL,
  `substitute_id` bigint unsigned NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `substitutions_absent_id_foreign` (`absent_id`),
  KEY `substitutions_substitute_id_foreign` (`substitute_id`),
  CONSTRAINT `substitutions_absent_id_foreign` FOREIGN KEY (`absent_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `substitutions_substitute_id_foreign` FOREIGN KEY (`substitute_id`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `substitutions`
--

/*!40000 ALTER TABLE `substitutions` DISABLE KEYS */;
INSERT INTO `substitutions` VALUES (1,7,3,'2026-02-04','2026-02-04',0,'2026-02-04 15:16:30','2026-02-04 15:16:38');
/*!40000 ALTER TABLE `substitutions` ENABLE KEYS */;

--
-- Table structure for table `timesheet_items`
--

DROP TABLE IF EXISTS `timesheet_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheet_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `timesheet_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timesheet_items_timesheet_id_foreign` (`timesheet_id`),
  KEY `timesheet_items_employee_id_foreign` (`employee_id`),
  KEY `timesheet_items_status_id_foreign` (`status_id`),
  CONSTRAINT `timesheet_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`),
  CONSTRAINT `timesheet_items_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`),
  CONSTRAINT `timesheet_items_timesheet_id_foreign` FOREIGN KEY (`timesheet_id`) REFERENCES `timesheets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheet_items`
--

/*!40000 ALTER TABLE `timesheet_items` DISABLE KEYS */;
INSERT INTO `timesheet_items` VALUES (1,1,1,'2026-02-16',NULL,NULL,'2026-02-04 17:03:49','2026-02-04 17:03:49'),(2,1,2,'2026-02-16',NULL,NULL,'2026-02-04 17:03:49','2026-02-04 17:03:49'),(4,1,4,'2026-02-16',NULL,NULL,'2026-02-04 17:03:49','2026-02-04 17:03:49'),(5,1,5,'2026-02-16',NULL,NULL,'2026-02-04 17:03:49','2026-02-04 17:03:49'),(6,1,6,'2026-02-16',1,NULL,'2026-02-04 17:03:49','2026-02-04 17:04:00'),(7,1,7,'2026-02-16',NULL,NULL,'2026-02-04 17:03:49','2026-02-04 17:03:49'),(8,1,6,'2026-02-17',1,NULL,'2026-02-04 17:04:01','2026-02-04 17:04:01'),(9,1,6,'2026-02-18',1,NULL,'2026-02-04 17:04:03','2026-02-04 17:04:03');
/*!40000 ALTER TABLE `timesheet_items` ENABLE KEYS */;

--
-- Table structure for table `timesheets`
--

DROP TABLE IF EXISTS `timesheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `timesheets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `employee_id` bigint unsigned DEFAULT NULL,
  `date` date NOT NULL,
  `hours` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `timesheets_employee_id_foreign` (`employee_id`),
  CONSTRAINT `timesheets_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `timesheets`
--

/*!40000 ALTER TABLE `timesheets` DISABLE KEYS */;
INSERT INTO `timesheets` VALUES (1,'2026-02-16','2026-02-28',1,'2026-02-16',0,'2026-02-04 17:01:22','2026-02-04 17:01:22');
/*!40000 ALTER TABLE `timesheets` ENABLE KEYS */;

--
-- Table structure for table `travel_items`
--

DROP TABLE IF EXISTS `travel_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `travel_timesheet_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `travel_status_id` bigint unsigned DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `travel_items_travel_timesheet_id_foreign` (`travel_timesheet_id`),
  KEY `travel_items_employee_id_foreign` (`employee_id`),
  KEY `travel_items_travel_status_id_foreign` (`travel_status_id`),
  CONSTRAINT `travel_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travel_items_travel_status_id_foreign` FOREIGN KEY (`travel_status_id`) REFERENCES `travel_statuses` (`id`) ON DELETE SET NULL,
  CONSTRAINT `travel_items_travel_timesheet_id_foreign` FOREIGN KEY (`travel_timesheet_id`) REFERENCES `travel_timesheets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_items`
--

/*!40000 ALTER TABLE `travel_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `travel_items` ENABLE KEYS */;

--
-- Table structure for table `travel_records`
--

DROP TABLE IF EXISTS `travel_records`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_records` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `travel_timesheet_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `location_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'center',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `travel_records_travel_timesheet_id_foreign` (`travel_timesheet_id`),
  KEY `travel_records_employee_id_foreign` (`employee_id`),
  CONSTRAINT `travel_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `travel_records_travel_timesheet_id_foreign` FOREIGN KEY (`travel_timesheet_id`) REFERENCES `travel_timesheets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_records`
--

/*!40000 ALTER TABLE `travel_records` DISABLE KEYS */;
/*!40000 ALTER TABLE `travel_records` ENABLE KEYS */;

--
-- Table structure for table `travel_statuses`
--

DROP TABLE IF EXISTS `travel_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_statuses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `short_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_statuses`
--

/*!40000 ALTER TABLE `travel_statuses` DISABLE KEYS */;
/*!40000 ALTER TABLE `travel_statuses` ENABLE KEYS */;

--
-- Table structure for table `travel_timesheet_items`
--

DROP TABLE IF EXISTS `travel_timesheet_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_timesheet_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `travel_timesheet_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `date` date NOT NULL,
  `status_id` bigint unsigned DEFAULT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `travel_timesheet_items_travel_timesheet_id_index` (`travel_timesheet_id`),
  KEY `travel_timesheet_items_employee_id_index` (`employee_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_timesheet_items`
--

/*!40000 ALTER TABLE `travel_timesheet_items` DISABLE KEYS */;
INSERT INTO `travel_timesheet_items` VALUES (1,1,1,'2026-02-16',NULL,NULL,'2026-02-04 17:02:03','2026-02-04 17:02:03'),(2,1,2,'2026-02-16',NULL,NULL,'2026-02-04 17:02:03','2026-02-04 17:02:03'),(4,1,4,'2026-02-16',NULL,NULL,'2026-02-04 17:02:03','2026-02-04 17:02:03'),(5,1,5,'2026-02-16',NULL,NULL,'2026-02-04 17:02:03','2026-02-04 17:02:03'),(6,1,6,'2026-02-16',1,NULL,'2026-02-04 17:02:03','2026-02-04 17:44:13'),(7,1,7,'2026-02-16',NULL,NULL,'2026-02-04 17:02:03','2026-02-04 17:02:03'),(8,1,6,'2026-02-23',1,NULL,'2026-02-04 17:44:16','2026-02-04 17:44:16');
/*!40000 ALTER TABLE `travel_timesheet_items` ENABLE KEYS */;

--
-- Table structure for table `travel_timesheets`
--

DROP TABLE IF EXISTS `travel_timesheets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `travel_timesheets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `travel_timesheets`
--

/*!40000 ALTER TABLE `travel_timesheets` DISABLE KEYS */;
INSERT INTO `travel_timesheets` VALUES (1,'2026-02-16','2026-02-28',NULL,'2026-02-04 17:01:58','2026-02-04 17:01:58');
/*!40000 ALTER TABLE `travel_timesheets` ENABLE KEYS */;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Федин Руслан Анатольевич','ruslan-fedin@yandex.ru',NULL,'$2y$12$tS4k3KTl.xHsWlxoI7VzQ.2UjYzBSaE3yGdMtkuZRsGjD8Wq71n.S',NULL,'2026-02-04 14:45:56','2026-02-04 14:45:56');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;

--
-- Dumping routines for database 'laravel'
--
--
-- WARNING: can't read the INFORMATION_SCHEMA.libraries table. It's most probably an old server 8.4.8.
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-02-04 22:45:02
