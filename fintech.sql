-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 28, 2024 at 11:22 AM
-- Server version: 5.7.31
-- PHP Version: 8.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fintech`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

DROP TABLE IF EXISTS `activity_log`;
CREATE TABLE IF NOT EXISTS `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) UNSIGNED DEFAULT NULL,
  `log_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `causer_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `causer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `properties` json DEFAULT NULL,
  `batch_uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subject` (`subject_type`,`subject_id`),
  KEY `causer` (`causer_type`,`causer_id`),
  KEY `activity_log_log_name_index` (`log_name`),
  KEY `activity_log_module_id_index` (`module_id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`id`, `module_id`, `log_name`, `description`, `subject_type`, `event`, `subject_id`, `causer_type`, `causer_id`, `properties`, `batch_uuid`, `created_at`, `updated_at`) VALUES
(1, NULL, 'user', 'The user has been updated by Dinesh Patil', 'App\\Models\\User', 'updated', 2, NULL, NULL, '{\"old\": {\"password\": \"$2y$12$PVLvO0tl0Xtdvs0/psAstuM9zomQv9Q3NC4n39WCPHOW7F4uc1.tG\", \"updated_at\": \"2024-10-22T12:15:41.000000Z\"}, \"attributes\": {\"password\": \"$2y$12$eccJXGFOZ.qI21lSpOqhcuZ.VJNT22hsSi7hVP2eZ/0RZ9d2oXzKe\", \"updated_at\": \"2024-10-22T12:49:43.000000Z\"}}', NULL, '2024-10-22 07:19:43', '2024-10-22 07:19:43'),
(2, NULL, 'user', 'The user has been created by Dinesh Patil', 'App\\Models\\User', 'created', 1, NULL, NULL, '{\"attributes\": {\"id\": 1, \"role\": \"user\", \"email\": \"dinesh.softieons@gmail.com\", \"status\": 1, \"balance\": \"0.0000000000\", \"password\": \"$2y$12$GXoYwrW2Nh8B.gkPlS.rT.8TuXBPeqeF3LK0e1fI3VWTRFCKm4sQS\", \"fcm_token\": null, \"last_name\": \"Patil\", \"country_id\": 1, \"created_at\": \"2024-10-22T12:52:03.000000Z\", \"deleted_at\": null, \"first_name\": \"Dinesh\", \"is_company\": true, \"updated_at\": \"2024-10-22T12:52:03.000000Z\", \"referalcode\": \"ref123\", \"user_role_id\": 1, \"is_kyc_verify\": 0, \"mobile_number\": \"7507642090\", \"remember_token\": null, \"is_email_verify\": \"0\", \"formatted_number\": \"+917507642090\", \"is_mobile_verify\": 0, \"userRole.role_name\": \"Silver\"}}', NULL, '2024-10-22 07:22:03', '2024-10-22 07:22:03'),
(3, 1, 'company details', 'The company details has been created by Unknown User', 'App\\Models\\CompanyDetail', 'created', 1, NULL, NULL, '{\"attributes\": {\"id\": 1, \"tin\": \"147asd15\", \"vat\": \"3f1541dfe1\", \"user_id\": 1, \"postcode\": \"395007\", \"bank_code\": \"IDFC2024\", \"bank_name\": \"IDFC Bank\", \"user.name\": null, \"created_at\": \"2024-10-22T12:52:03.000000Z\", \"updated_at\": \"2024-10-22T12:52:03.000000Z\", \"company_name\": \"Softieons\", \"account_number\": \"21234567890123\", \"company_address\": \"5th floor Abhinandan Royal, Bhatar road, Althan, Surat\", \"business_licence\": \"test214soft\"}}', NULL, '2024-10-22 07:22:03', '2024-10-22 07:22:03'),
(4, NULL, 'user', 'The user has been updated by Unknown User', 'App\\Models\\User', 'updated', 1, NULL, NULL, '{\"old\": {\"password\": \"$2y$12$GXoYwrW2Nh8B.gkPlS.rT.8TuXBPeqeF3LK0e1fI3VWTRFCKm4sQS\", \"updated_at\": \"2024-10-22T12:52:03.000000Z\"}, \"attributes\": {\"password\": \"$2y$12$bqcdQ6p1ArkCs52q5NHjF.zYr1h/91KuRcdYvzIQsCWQoGhca0efS\", \"updated_at\": \"2024-10-22T12:58:01.000000Z\"}}', NULL, '2024-10-22 07:28:01', '2024-10-22 07:28:01'),
(5, NULL, 'user', 'The user has been updated by Unknown User', 'App\\Models\\User', 'updated', 1, NULL, NULL, '{\"old\": {\"password\": \"$2y$12$bqcdQ6p1ArkCs52q5NHjF.zYr1h/91KuRcdYvzIQsCWQoGhca0efS\", \"updated_at\": \"2024-10-22T12:58:01.000000Z\"}, \"attributes\": {\"password\": \"$2y$12$uIvBXRWkj31Vx.nNaT7uc.VAIeYCVHdo/P5rt/Efj1f9zgDjlUAnC\", \"updated_at\": \"2024-10-22T13:11:14.000000Z\"}}', NULL, '2024-10-22 07:41:14', '2024-10-22 07:41:14'),
(7, NULL, 'user', 'The user has been created by Unknown User', 'App\\Models\\User', 'created', 3, NULL, NULL, '{\"attributes\": {\"id\": 3, \"role\": \"user\", \"email\": \"nitesh.softieons@gmail.com\", \"status\": 1, \"balance\": \"0.0000000000\", \"password\": \"$2y$12$AGmUmi0BKTN70xuK7DJOPeTVQC3u3JQOlm5cC60fY8C/hNzJ3qTRu\", \"fcm_token\": null, \"last_name\": \"Kumar\", \"country_id\": 1, \"created_at\": \"2024-10-23T09:32:26.000000Z\", \"deleted_at\": null, \"first_name\": \"Nitesh\", \"is_company\": true, \"updated_at\": \"2024-10-23T09:32:26.000000Z\", \"referalcode\": \"ref123\", \"user_role_id\": 1, \"is_kyc_verify\": 0, \"mobile_number\": \"7874449936\", \"remember_token\": null, \"is_email_verify\": \"0\", \"formatted_number\": \"+917874449936\", \"is_mobile_verify\": 0, \"userRole.role_name\": \"Silver\", \"verification_token\": null}}', NULL, '2024-10-23 04:02:26', '2024-10-23 04:02:26'),
(8, NULL, 'user', 'The user has been updated by Unknown User', 'App\\Models\\User', 'updated', 3, NULL, NULL, '{\"old\": {\"status\": null, \"balance\": null, \"user_role_id\": null, \"is_kyc_verify\": null, \"is_email_verify\": null, \"is_mobile_verify\": null, \"userRole.role_name\": null, \"verification_token\": null}, \"attributes\": {\"status\": 1, \"balance\": \"0.0000000000\", \"user_role_id\": 1, \"is_kyc_verify\": 0, \"is_email_verify\": \"0\", \"is_mobile_verify\": 0, \"userRole.role_name\": \"Silver\", \"verification_token\": \"DWk3x6hX87h40jgOmXzJ17RSJdZqFoSAHOWCURkO1Dh0reqOxXLRdiIibQ36FHuT\"}}', NULL, '2024-10-23 04:02:26', '2024-10-23 04:02:26'),
(9, 3, 'company details', 'The company details has been created by Unknown User', 'App\\Models\\CompanyDetail', 'created', 2, NULL, NULL, '{\"attributes\": {\"id\": 2, \"tin\": \"147asd15\", \"vat\": \"3f1541dfe1\", \"user_id\": 3, \"postcode\": \"395007\", \"bank_code\": \"IDFC2024\", \"bank_name\": \"IDFC Bank\", \"user.name\": null, \"created_at\": \"2024-10-23T09:32:26.000000Z\", \"updated_at\": \"2024-10-23T09:32:26.000000Z\", \"company_name\": \"Softieons\", \"account_number\": \"21234567890123\", \"company_address\": \"5th floor Abhinandan Royal, Bhatar road, Althan, Surat\", \"business_licence\": \"test214soft\"}}', NULL, '2024-10-23 04:02:26', '2024-10-23 04:02:26'),
(10, NULL, 'user', 'The user has been updated by Unknown User', 'App\\Models\\User', 'updated', 3, NULL, NULL, '{\"old\": {\"updated_at\": \"2024-10-23T09:32:26.000000Z\", \"is_email_verify\": 0, \"verification_token\": \"DWk3x6hX87h40jgOmXzJ17RSJdZqFoSAHOWCURkO1Dh0reqOxXLRdiIibQ36FHuT\"}, \"attributes\": {\"updated_at\": \"2024-10-23T09:40:45.000000Z\", \"is_email_verify\": 1, \"verification_token\": null}}', NULL, '2024-10-23 04:10:45', '2024-10-23 04:10:45'),
(11, NULL, 'user meta kyc', 'The user meta kyc has been created by Unknown User', 'App\\Models\\UserKyc', 'created', 1, NULL, NULL, '{\"attributes\": {\"id\": 1, \"email\": \"dinesh.softieons@gmail.com\", \"video\": null, \"user_id\": 1, \"document\": null, \"created_at\": \"2024-10-23T14:46:15.000000Z\", \"updated_at\": \"2024-10-23T14:46:15.000000Z\", \"meta_response\": \"{\\\"identityId\\\": \\\"66deed0b6f9de1001d0d6d0a\\\", \\\"verificationId\\\": \\\"66deed0b6f9de1001d0d6d0b\\\"}\", \"user.last_name\": null, \"user.first_name\": null, \"verification_id\": \"66deed0b6f9de1001d0d6d0a\", \"identification_id\": \"66deed0b6f9de1001d0d6d0b\", \"verification_status\": \"pending\"}}', NULL, '2024-10-23 09:16:15', '2024-10-23 09:16:15'),
(12, NULL, 'user meta kyc', 'The user meta kyc has been updated by Unknown User', 'App\\Models\\UserKyc', 'updated', 1, NULL, NULL, '{\"old\": {\"updated_at\": \"2024-10-23T14:46:15.000000Z\"}, \"attributes\": {\"updated_at\": \"2024-10-23T14:46:52.000000Z\"}}', NULL, '2024-10-23 09:16:52', '2024-10-23 09:16:52'),
(13, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"last_name\": \"Patil\", \"updated_at\": \"2024-10-22T13:11:14.000000Z\"}, \"attributes\": {\"last_name\": \"Dinesh\", \"updated_at\": \"2024-10-26T09:07:39.000000Z\"}}', NULL, '2024-10-26 03:37:39', '2024-10-26 03:37:39'),
(14, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:07:39.000000Z\", \"profile_image\": null}, \"attributes\": {\"updated_at\": \"2024-10-26T09:09:17.000000Z\", \"profile_image\": \"profile_images/1/671cb1bd24a74.jpg\"}}', NULL, '2024-10-26 03:39:17', '2024-10-26 03:39:17'),
(15, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:09:17.000000Z\", \"profile_image\": \"profile_images/1/671cb1bd24a74.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:14:59.000000Z\", \"profile_image\": \"profile_images/1/671cb3130f585.jpg\"}}', NULL, '2024-10-26 03:44:59', '2024-10-26 03:44:59'),
(16, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:14:59.000000Z\", \"profile_image\": \"profile_images/1/671cb3130f585.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:21:54.000000Z\", \"profile_image\": \"profile_images/1/671cb4b2d4e34.jpg\"}}', NULL, '2024-10-26 03:51:54', '2024-10-26 03:51:54'),
(17, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:21:54.000000Z\", \"profile_image\": \"profile_images/1/671cb4b2d4e34.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:24:09.000000Z\", \"profile_image\": \"profile_images/1/671cb5399e399.jpg\"}}', NULL, '2024-10-26 03:54:09', '2024-10-26 03:54:09'),
(18, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:24:09.000000Z\", \"profile_image\": \"profile_images/1/671cb5399e399.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:26:20.000000Z\", \"profile_image\": \"profile_images/1/671cb5bc5e4df.jpg\"}}', NULL, '2024-10-26 03:56:20', '2024-10-26 03:56:20'),
(19, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:26:20.000000Z\", \"profile_image\": \"profile_images/1/671cb5bc5e4df.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:26:34.000000Z\", \"profile_image\": \"profile_images/1/671cb5caa62f3.jpg\"}}', NULL, '2024-10-26 03:56:34', '2024-10-26 03:56:34'),
(20, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:26:34.000000Z\", \"profile_image\": \"profile_images/1/671cb5caa62f3.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:31:42.000000Z\", \"profile_image\": \"profile_images/1/671cb6fe1cd9d.jpg\"}}', NULL, '2024-10-26 04:01:42', '2024-10-26 04:01:42'),
(21, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:31:42.000000Z\", \"profile_image\": \"profile_images/1/671cb6fe1cd9d.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-26T09:31:53.000000Z\", \"profile_image\": \"profile_images/1/671cb709561e9.jpg\"}}', NULL, '2024-10-26 04:01:53', '2024-10-26 04:01:53'),
(22, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-26T09:31:53.000000Z\", \"profile_image\": \"profile_images/1/671cb709561e9.jpg\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:04:12.000000Z\", \"profile_image\": \"profile_images/1/671dd7db54ac8. png\"}}', NULL, '2024-10-27 00:34:12', '2024-10-27 00:34:12'),
(23, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-27T06:04:12.000000Z\", \"profile_image\": \"profile_images/1/671dd7db54ac8. png\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:05:41.000000Z\", \"profile_image\": \"profile_images/1/671dd835249fa.png\"}}', NULL, '2024-10-27 00:35:41', '2024-10-27 00:35:41'),
(24, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-27T06:05:41.000000Z\", \"profile_image\": \"profile_images/1/671dd835249fa.png\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:09:13.000000Z\", \"profile_image\": \"profile_images/1/671dd90931d50.png\"}}', NULL, '2024-10-27 00:39:13', '2024-10-27 00:39:13'),
(25, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-27T06:09:13.000000Z\", \"profile_image\": \"profile_images/1/671dd90931d50.png\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:10:17.000000Z\", \"profile_image\": \"profile_images/1/671dd949467e3.jpeg\"}}', NULL, '2024-10-27 00:40:17', '2024-10-27 00:40:17'),
(26, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-27T06:10:17.000000Z\", \"profile_image\": \"profile_images/1/671dd949467e3.jpeg\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:10:34.000000Z\", \"profile_image\": \"profile_images/1/671dd95a6dfd1.jpeg\"}}', NULL, '2024-10-27 00:40:34', '2024-10-27 00:40:34'),
(27, NULL, 'user', 'The user has been updated by ', 'App\\Models\\User', 'updated', 1, 'App\\Models\\User', 1, '{\"old\": {\"updated_at\": \"2024-10-27T06:10:34.000000Z\", \"profile_image\": \"profile_images/1/671dd95a6dfd1.jpeg\"}, \"attributes\": {\"updated_at\": \"2024-10-27T06:10:58.000000Z\", \"profile_image\": \"profile_images/1/671dd972881b0_1730009458.jpeg\"}}', NULL, '2024-10-27 00:40:58', '2024-10-27 00:40:58');

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

DROP TABLE IF EXISTS `banners`;
CREATE TABLE IF NOT EXISTS `banners` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `app_image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `web_image` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `banners`
--

INSERT INTO `banners` (`id`, `name`, `app_image`, `web_image`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Home Web Banner', 'banners/yNnycBk3PfjX8MgKWecnZZZiQgPgYzpRyDlVjwbx.png', 'banners/7wkUwEvfv3tDHiVZPmyqZzXEVDoMEnJy9n7PCasx.png', NULL, '2024-08-25 05:07:11', '2024-08-25 05:07:11');

-- --------------------------------------------------------

--
-- Table structure for table `beneficiaries`
--

DROP TABLE IF EXISTS `beneficiaries`;
CREATE TABLE IF NOT EXISTS `beneficiaries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `country_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_first_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_middle_name` text COLLATE utf8mb4_unicode_ci,
  `b_last_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `b_mobile` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `b_email` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `relations` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `other_remarks` text COLLATE utf8mb4_unicode_ci,
  `remittance_purpose` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `beneficiary_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_id_expiry` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_dob` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `beneficiaries`
--

INSERT INTO `beneficiaries` (`id`, `user_id`, `type`, `country_id`, `bank_name`, `account_number`, `b_first_name`, `b_middle_name`, `b_last_name`, `b_address`, `b_state`, `b_mobile`, `b_email`, `relations`, `other_remarks`, `remittance_purpose`, `beneficiary_id`, `receiver_id_expiry`, `receiver_dob`, `created_at`, `updated_at`) VALUES
(1, '1', 1, 'US', 'Bank of America', '987654321', 'John', 'Doe', 'Smith', '123 Main St, Los Angeles, CA', 'California', '555-1234', 'john.doe@example.com', 1, NULL, 'Family support', 'BEN123456789', '2030-12-31', '1980-01-01', '2024-08-25 00:03:43', '2024-08-25 00:03:43'),
(5, '1', 1, 'US', 'Bank of America', '987654321', 'John', 'Doe', 'Smith', '123 Main St, Los Angeles, CA', 'California', '555-1234', 'john.doe@example.com', 1, NULL, 'Family support', 'BEN123456789', '2030-12-31', '1980-01-01', '2024-08-25 07:11:15', '2024-08-25 07:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `company_details`
--

DROP TABLE IF EXISTS `company_details`;
CREATE TABLE IF NOT EXISTS `company_details` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_licence` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tin` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `company_details_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_details`
--

INSERT INTO `company_details` (`id`, `user_id`, `company_name`, `business_licence`, `tin`, `vat`, `company_address`, `postcode`, `bank_name`, `account_number`, `bank_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Softieons', 'test214soft', '147asd15', '3f1541dfe1', '5th floor Abhinandan Royal, Bhatar road, Althan, Surat', '395007', 'IDFC Bank', '21234567890123', 'IDFC2024', '2024-10-22 07:22:03', '2024-10-22 07:22:03'),
(2, 3, 'Softieons', 'test214soft', '147asd15', '3f1541dfe1', '5th floor Abhinandan Royal, Bhatar road, Althan, Surat', '395007', 'IDFC Bank', '21234567890123', 'IDFC2024', '2024-10-23 04:02:26', '2024-10-23 04:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isdcode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_flag` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=235 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `isdcode`, `country_flag`, `created_at`, `updated_at`) VALUES
(1, 'Afghanistan ', '+93', 'afghanistan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(2, 'Albania ', '+355', 'albania.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(3, 'Algeria ', '+213', 'algeria.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(4, 'American Samoa', '+1684', 'american-samoa.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(5, 'Andorra, Principality of ', '+376', 'andorra.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(6, 'Angola', '+244', 'angola.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(7, 'Anguilla ', '+1264', 'anguilla.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(8, 'Antarctica', '+672', 'Antarctic_Treaty.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(9, 'Antigua and Barbuda', '+1-268', 'antigua-and-barbuda.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(10, 'Argentina ', '+54', 'argentina.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(11, 'Armenia', '+374', 'armenia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(12, 'Aruba', '+297', 'aruba.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(13, 'Australia', '+61', 'australia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(14, 'Austria', '+43', 'austria.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(15, 'Azerbaijan or Azerbaidjan (Former Azerbaijan Soviet Socialist Republic)', '+994', 'azerbaijan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(16, 'Bahamas, Commonwealth of The', '+1-242', 'bahamas.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(17, 'Bahrain, Kingdom of (Former Dilmun)', '+973', 'bahrain.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(18, 'Bangladesh (Former East Pakistan)', '+880', 'bangladesh.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(19, 'Barbados ', '+1246', 'barbados.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(20, 'Belarus (Former Belorussian [Byelorussian] Soviet Socialist Republic)', '+375', 'belarus.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(21, 'Belgium ', '+32', 'belgium.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(22, 'Belize (Former British Honduras)', '+501', 'belize.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(23, 'Benin (Former Dahomey)', '+229', 'benin.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(24, 'Bermuda ', '+1-441', 'bermuda.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(25, 'Bhutan, Kingdom of', '+975', 'bhutan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(26, 'Bolivia ', '+591', 'bolivia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(27, 'Bosnia and Herzegovina ', '+387', 'bosnia-and-herzegovina.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(28, 'Botswana (Former Bechuanaland)', '+267', 'botswana.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(29, 'Bouvet Island (Territory of Norway)', '+47', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(30, 'Brazil ', '+55', 'brazil.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(31, 'British Indian Ocean Territory (BIOT)', '+246', 'british-indian-ocean-territory.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(32, 'Brunei (Negara Brunei Darussalam) ', '+673', 'brunei.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(33, 'Bulgaria ', '+359', 'bulgaria.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(34, 'Burkina Faso (Former Upper Volta)', '+226', 'burkina-faso.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(35, 'Burundi (Former Urundi)', '+257', 'burundi.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(36, 'Cambodia, Kingdom of (Former Khmer Republic, Kampuchea Republic)', '+855', 'cambodia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(37, 'Cameroon (Former French Cameroon)', '+237', 'cameroon.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(38, 'Canada ', '+1', 'canada.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(39, 'Cape Verde ', '+238', 'cape-verde.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(40, 'Cayman Islands ', '+1-345', 'cayman-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(41, 'Central African Republic ', '+236', 'central-african-republic.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(42, 'Chad ', '+235', 'chad.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(43, 'Chile ', '+56', 'chile.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(44, 'China ', '+86', 'china.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(45, 'Christmas Island ', '+53', 'christmas-island.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(46, 'Cocos (Keeling) Islands ', '+61', 'cocos-island.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(47, 'Colombia ', '+57', 'colombia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(48, 'Comoros, Union of the ', '+269', 'comoros.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(49, 'Congo, Democratic Republic of the (Former Zaire) ', '+243', 'congo.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(50, 'Congo, Republic of the', '+242', 'Republic_of_the_Congo.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(51, 'Cook Islands (Former Harvey Islands)', '+682', 'cook-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(52, 'Costa Rica ', '+506', 'costa-rica.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(53, 'Cote D\'Ivoire (Former Ivory Coast) ', '+225', 'cote_d.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(54, 'Croatia (Hrvatska) ', '+385', 'croatia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(55, 'Cuba ', '+53', 'cuba.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(56, 'Cyprus ', '+357', 'cyprus.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(57, 'Czech Republic', '+420', 'czech-republic.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(58, 'Denmark ', '+45', 'denmark.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(59, 'Djibouti (Former French Territory of the Afars and Issas, French Somaliland)', '+253', 'djibouti.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(60, 'Dominica ', '+1-767', 'dominica.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(61, 'Dominican Republic ', '+1809', 'dominican-republic.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(62, 'East Timor (Former Portuguese Timor)', '+670', 'east-timor.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(63, 'Ecuador ', '+593 ', 'ecuador.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(64, 'Egypt (Former United Arab Republic - with Syria)', '+20', 'egypt.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(65, 'El Salvador ', '+503', 'El_Salvador.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(66, 'Equatorial Guinea (Former Spanish Guinea)', '+240', 'equatorial-guinea.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(67, 'Eritrea (Former Eritrea Autonomous Region in Ethiopia)', '+291', 'eritrea.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(68, 'Estonia (Former Estonian Soviet Socialist Republic)', '+372', 'estonia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(69, 'Ethiopia (Former Abyssinia, Italian East Africa)', '+251', 'ethiopia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(70, 'Falkland Islands (Islas Malvinas) ', '+500', 'falkland-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(71, 'Faroe Islands ', '+298', 'faroe-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(72, 'Fiji ', '+679', 'fiji.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(73, 'Finland ', '+358', 'finland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(74, 'France ', '+33', 'france.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(75, 'French Guiana or French Guyana ', '+594', 'French_Guiana.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(76, 'French Polynesia (Former French Colony of Oceania)', '+689', 'french-polynesia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(77, 'Gabon (Gabonese Republic)', '+241', 'gabon.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(78, 'Gambia, The ', '+220', 'gambia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(79, 'Georgia (Former Georgian Soviet Socialist Republic)', '+995', 'georgia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(80, 'Germany ', '+49', 'germany.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(81, 'Ghana (Former Gold Coast)', '+233', 'ghana.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(82, 'Gibraltar ', '+350', 'gibraltar.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(83, 'Greece ', '+30', 'greece.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(84, 'Greenland ', '+299', 'greenland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(85, 'Grenada ', '+1-473', 'grenada.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(86, 'Guadeloupe', '+590', 'GuadeloupeFlag.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(87, 'Guam', '+1-671', 'guam.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(88, 'Guatemala ', '+502', 'guatemala.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(89, 'Guinea (Former French Guinea)', '+224', 'guinea.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(90, 'Guinea-Bissau (Former Portuguese Guinea)', '+245', 'Guinea-Bissau.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(91, 'Guyana (Former British Guiana)', '+592', 'guyana.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(92, 'Haiti ', '+509', 'haiti.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(93, 'Holy See (Vatican City State)', '+379', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(94, 'Honduras ', '+504', 'honduras.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(95, 'Hong Kong ', '+852', 'hong-kong.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(96, 'Hungary ', '+36', 'hungary.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(97, 'Iceland ', '+354', 'iceland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(98, 'India ', '+91', 'india.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(99, 'Iran, Islamic Republic of', '+98', 'iran.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(100, 'Iraq ', '+964', 'iraq.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(101, 'Ireland ', '+353', 'ireland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(102, 'Israel ', '+972', 'israel.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(103, 'Italy ', '+39', 'italy.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(104, 'Jamaica ', '+1-876', 'jamaica.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(105, 'Japan ', '+81', 'japan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(106, 'Jordan (Former Transjordan)', '+962', 'jordan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(107, 'Kazakstan or Kazakhstan (Former Kazakh Soviet Socialist Republic)', '+7', 'kazakhstan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(108, 'Kiribati (Pronounced keer-ree-bahss) (Former Gilbert Islands)', '+686', 'kiribati.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(109, 'Korea, Democratic People\'s Republic of (North Korea)', '+850', 'korea_democratic.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(110, 'Korea, Republic of (South Korea) ', '+82', 'Republic_of_S_Korea.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(111, 'Kuwait ', '+965', 'kuwait.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(112, 'Kyrgyzstan (Kyrgyz Republic) (Former Kirghiz Soviet Socialist Republic)', '+996', 'kyrgyzstan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(113, 'Lao People\'s Democratic Republic (Laos)', '+856', 'laos.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(114, 'Latvia (Former Latvian Soviet Socialist Republic)', '+371', 'latvia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(115, 'Lebanon ', '+961', 'lebanon.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(116, 'Lesotho (Former Basutoland)', '+266', 'lesotho.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(117, 'Liberia ', '+231', 'liberia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(118, 'Libya (Libyan Arab Jamahiriya)', '+218', 'libya.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(119, 'Liechtenstein ', '+423', 'liechtenstein.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(120, 'Lithuania (Former Lithuanian Soviet Socialist Republic)', '+370', 'lithuania.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(121, 'Luxembourg ', '+352', 'luxembourg.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(122, 'Macau ', '+853', 'macao.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(123, 'Macedonia, The Former Yugoslav Republic of', '+389', 'Macedonia.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(124, 'Madagascar (Former Malagasy Republic)', '+261', 'madagascar.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(125, 'Malawi (Former British Central African Protectorate, Nyasaland)', '+265', 'malawi.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(126, 'Malaysia ', '+60', 'malaysia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(127, 'Maldives ', '+960', 'maldives.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(128, 'Mali (Former French Sudan and Sudanese Republic) ', '+223', 'mali.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(129, 'Malta ', '+356', 'malta.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(130, 'Marshall Islands (Former Marshall Islands District - Trust Territory of the Pacific Islands)', '+692', 'marshall-island.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(131, 'Martinique (French) ', '+596', 'martinique.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(132, 'Mauritania ', '+222', 'mauritania.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(133, 'Mauritius ', '+230', 'mauritius.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(134, 'Mayotte (Territorial Collectivity of Mayotte)', '+269', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(135, 'Mexico ', '+52', 'mexico.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(136, 'Micronesia, Federated States of (Former Ponape, Truk, and Yap Districts - Trust Territory of the Pacific Islands)', '+691', 'micronesia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(137, 'Moldova, Republic of', '+373', 'moldova.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(138, 'Monaco, Principality of', '+377', 'monaco.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(139, 'Mongolia (Former Outer Mongolia)', '+976', 'mongolia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(140, 'Montserrat ', '+1-664', 'montserrat.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(141, 'Morocco ', '+212', 'morocco.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(142, 'Mozambique (Former Portuguese East Africa)', '+258', 'mozambique.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(143, 'Myanmar, Union of (Former Burma)', '+95', 'myanmar.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(144, 'Namibia (Former German Southwest Africa, South-West Africa)', '+264', 'namibia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(145, 'Nauru (Former Pleasant Island)', '+674', 'nauru.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(146, 'Nepal ', '+977', 'nepal.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(147, 'Netherlands ', '+31', 'netherlands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(148, 'Netherlands Antilles (Former Curacao and Dependencies)', '+599', 'neatherlans-Antilles.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(149, 'New Caledonia ', '+687', 'NewCaledonia.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(150, 'New Zealand (Aotearoa) ', '+64', 'new-zealand.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(151, 'Nicaragua ', '+505', 'nicaragua.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(152, 'Niger ', '+227', 'niger.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(153, 'Nigeria ', '+234', 'nigeria.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(154, 'Niue (Former Savage Island)', '+683', 'niue.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(155, 'Norfolk Island ', '+672', 'norfolk-island.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(156, 'Northern Mariana Islands (Former Mariana Islands District - Trust Territory of the Pacific Islands)', '+1-670', 'northern-marianas-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(157, 'Norway ', '+47', 'norway.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(158, 'Oman, Sultanate of (Former Muscat and Oman)', '+968', 'oman.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(159, 'Pakistan (Former West Pakistan)', '+92', 'pakistan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(160, 'Palau (Former Palau District - Trust Terriroty of the Pacific Islands)', '+680', 'palau.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(161, 'Palestinian State (Proposed)', '+970', 'palestine.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(162, 'Panama ', '+507', 'panama.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(163, 'Papua New Guinea (Former Territory of Papua and New Guinea)', '+675', 'papua-new-guinea.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(164, 'Paraguay ', '+595', 'paraguay.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(165, 'Peru ', '+51', 'peru.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(166, 'Philippines ', '+63', 'philippines.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(167, 'Pitcairn Island', '+64', 'pitcairn-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(168, 'Poland ', '+48', 'Poland.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(169, 'Portugal ', '+351', 'portugal.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(170, 'Puerto Rico ', '+1939', 'puerto-rico.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(171, 'Qatar, State of ', '+974 ', 'qatar.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(172, 'Reunion (French) (Former Bourbon Island)', '+262', 'ReunionFrench.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(173, 'Romania ', '+40', 'romania.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(174, 'Russian Federation ', '+7', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(175, 'Rwanda (Rwandese Republic) (Former Ruanda)', '+250', 'rwanda.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(176, 'Saint Helena ', '+290', 'saint-helena.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(177, 'Saint Kitts and Nevis (Former Federation of Saint Christopher and Nevis)', '+1-869', 'saint-kitts-and-nevis.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(178, 'Saint Lucia ', '+1-758', 'st-lucia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(179, 'Saint Pierre and Miquelon ', '+508', 'Saint-Pierre_and_Miquelon.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(180, 'Saint Vincent and the Grenadines ', '+1-784', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(181, 'Samoa (Former Western Samoa)', '+685', 'samoa.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(182, 'San Marino ', '+378', 'san-marino.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(183, 'Sao Tome and Principe ', '+239', 'sao-tome-and-principe.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(184, 'Saudi Arabia ', '+966', 'saudi-arabia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(185, 'Serbia, Republic of', '+381', 'serbia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(186, 'Senegal ', '+221', 'senegal.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(187, 'Seychelles ', '+248', 'seychelles.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(188, 'Sierra Leone ', '+232', 'sierra-leone.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(189, 'Singapore ', '+65', 'singapore.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(190, 'Slovakia', '+421', 'slovakia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(191, 'Slovenia ', '+386', 'slovenia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(192, 'Solomon Islands (Former British Solomon Islands)', '+677', 'solomon-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(193, 'Somalia (Former Somali Republic, Somali Democratic Republic) ', '+252', 'somalia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(194, 'South Africa (Former Union of South Africa)', '+27', 'south-africa.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(195, 'Spain ', '+34', 'spain.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(196, 'Sri Lanka (Former Serendib, Ceylon) ', '+94', 'sri-lanka.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(197, 'Sudan (Former Anglo-Egyptian Sudan) ', '+249', 'sudan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(198, 'Suriname (Former Netherlands Guiana, Dutch Guiana)', '+597', 'suriname.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(199, 'Svalbard (Spitzbergen) and Jan Mayen Islands ', '+47', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(200, 'Swaziland, Kingdom of ', '+268', 'switzerland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(201, 'Sweden ', '+46', 'sweden.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(202, 'Switzerland ', '+41', 'switzerland.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(203, 'Syria (Syrian Arab Republic) (Former United Arab Republic - with Egypt)', '+963', 'syria.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(204, 'Taiwan (Former Formosa)', '+886', 'taiwan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(205, 'Tajikistan (Former Tajik Soviet Socialist Republic)', '+992', 'tajikistan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(206, 'Tanzania, United Republic of (Former United Republic of Tanganyika and Zanzibar)', '+255', 'tanzania.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(207, 'Thailand (Former Siam)', '+66', 'thailand.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(208, 'Tokelau ', '+690', 'tokelau.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(209, 'Tonga, Kingdom of (Former Friendly Islands)', '+676', 'tonga.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(210, 'Trinidad and Tobago ', '+1868', 'trinidad-and-tobago.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(211, 'Tunisia ', '+216', 'tunisia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(212, 'Turkey ', '+90', 'turkey.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(213, 'Turkmenistan (Former Turkmen Soviet Socialist Republic)', '+993', 'turkmenistan.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(214, 'Turks and Caicos Islands ', '+1-649', 'turks-and-caicos.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(215, 'Tuvalu (Former Ellice Islands)', '+688', 'tuvalu.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(216, 'Uganda, Republic of', '+256', 'uganda.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(217, 'United Arab Emirates (UAE) (Former Trucial Oman, Trucial States)', '+971', 'united-arab-emirates.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(218, 'United Kingdom (Great Britain / UK)', '+44', 'united-kingdom.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(219, 'United States ', '+1', 'united-states-of-america.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(220, 'Uruguay, Oriental Republic of (Former Banda Oriental, Cisplatine Province)', '+598', 'uruguay.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(221, 'Vanuatu (Former New Hebrides)', '+678', 'vanuatu.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(222, 'Vatican City State (Holy See)', '+418', 'vatican-city.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(223, 'Venezuela ', '+58', 'venezuela.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(224, 'Vietnam ', '+84', 'vietnam.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(225, 'Virgin Islands, British ', '+1-284', 'virgin-islands.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(226, 'Virgin Islands, United States (Former Danish West Indies) ', '+1-340', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(227, 'Wallis and Futuna Islands ', '+681', 'Wallis_and_Futuna.jpg', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(228, 'Western Sahara (Former Spanish Sahara)', '+212', 'western-sahara.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(229, 'Yemen ', '+967', 'yemen.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(230, 'Yugoslavia ', '+38', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(231, 'Zaire (Former Congo Free State, Belgian Congo, Congo/Leopoldville, Congo/Kinshasa, Zaire) Now CD - Congo, Democratic Republic of the ', '+243', 'img', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(232, 'Zambia, Republic of (Former Northern Rhodesia) ', '+260', 'zambia.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(233, 'Zimbabwe, Republic of (Former Southern Rhodesia, Rhodesia) ', '+263', 'zimbabwe.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44'),
(234, 'Togo', '+228', 'togo.png', '2024-10-26 01:22:44', '2024-10-26 01:22:44');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

DROP TABLE IF EXISTS `login_logs`;
CREATE TABLE IF NOT EXISTS `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'login, logout',
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `browser` text COLLATE utf8mb4_unicode_ci,
  `source` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WEB' COMMENT 'WEB, APP',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `login_logs_user_id_index` (`user_id`),
  KEY `login_logs_type_index` (`type`),
  KEY `login_logs_source_index` (`source`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `type`, `ip_address`, `device`, `browser`, `source`, `created_at`, `updated_at`) VALUES
(1, 1, 'login', '::1', 'Mobile', '0', 'WEB', '2024-10-22 08:24:36', '2024-10-22 08:24:36'),
(2, 1, 'login', '192.168.29.42', 'Mobile', '0', 'WEB', '2024-10-26 00:59:32', '2024-10-26 00:59:32'),
(3, 1, 'login', '192.168.29.68', 'Mobile', '0', 'WEB', '2024-10-26 00:59:38', '2024-10-26 00:59:38'),
(4, 1, 'login', '192.168.29.68', 'Mobile', '0', 'WEB', '2024-10-26 01:00:31', '2024-10-26 01:00:31'),
(5, 1, 'login', '192.168.29.42', 'Mobile', '0', 'WEB', '2024-10-26 03:19:14', '2024-10-26 03:19:14'),
(6, 1, 'login', '192.168.29.68', 'Mobile', '0', 'WEB', '2024-10-28 01:09:35', '2024-10-28 01:09:35');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(13, '2024_08_23_045609_create_transaction_limit_table', 3),
(14, '2024_08_23_113031_create_transactions_table', 4),
(15, '2024_08_25_051659_create_beneficiaries_table', 5),
(18, '2024_08_25_090833_create_banners_table', 7),
(28, '2024_08_27_102551_create_permission_tables', 9),
(37, '2014_10_12_000000_create_users_table', 12),
(38, '2024_08_22_092750_create_company_details_table', 12),
(39, '2024_08_25_120832_create_activity_log_table', 12),
(40, '2024_08_25_120833_add_event_column_to_activity_log_table', 12),
(41, '2024_08_25_120834_add_batch_uuid_column_to_activity_log_table', 12),
(42, '2024_08_27_105729_create_login_logs_table', 12),
(43, '2024_08_25_080606_create_otps_table', 13),
(45, '2024_08_23_045215_create_user_roles_table', 14),
(46, '2024_09_09_103046_create_user_kycs_table', 15),
(47, '2024_08_22_111619_create_countries_table', 16);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('d48117f19e3c38a742ef3e59c69ba724eced0830dcfd388e93aacaa9e0554df19878a3ffd68c7ef9', 1, 2, 'YourAppName', '[]', 0, '2024-08-22 05:35:31', '2024-08-22 05:35:31', '2025-08-22 11:05:31'),
('dadc2b7c7c10c00832c8c8b3f0ad6146476cc6319fe5abb2c1f73137beaf7f2ea9628a66a006a425', 1, 2, 'mag-srl', '[]', 0, '2024-08-22 05:36:47', '2024-08-22 05:36:47', '2025-08-22 11:06:47'),
('ff07b0e702c7fda1dbc1236970730012fb32bfb53ee134a831ffe35edd9542de5234038d0e6607f9', 1, 2, 'mag-srl', '[]', 0, '2024-08-22 05:36:55', '2024-08-22 05:36:55', '2025-08-22 11:06:55'),
('7ac1c7bf1eb9e5abd46f97674f1b587b701c7e8919d756ad411eee42cf86f0e36a8feba5a3d44dca', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 01:23:10', '2024-08-23 01:23:10', '2025-08-23 06:53:10'),
('90f9f8709da5aa7303502f0c6cec5bfdbf96253857dfe4fcd5ebdad0baea2368340e9499c0a5cd12', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 08:11:50', '2024-08-23 08:11:50', '2025-08-23 13:41:50'),
('df0d01c9faae79b4c51a7af27eebc06580f2fe618c17ac7ae2df9884bebbd857dc4bc88f6dfacf16', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 08:12:29', '2024-08-23 08:12:29', '2025-08-23 13:42:29'),
('62c00005e685ba10ba23f84120123d107e00a8596fa54cf616d0dea367af286d354e91637922a79c', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:08:08', '2024-08-23 23:08:08', '2025-08-24 04:38:08'),
('dcc405015fd21ca5a167db386c5a3780582236e3700d21a33b251fcd63c9b41e469eebb66e3705b4', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:08:23', '2024-08-23 23:08:23', '2025-08-24 04:38:23'),
('f5e1abae4eae641963109ded34863ad48f6254aa7ef17e16097f944a27561641ac13bff14c422ee6', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:19:52', '2024-08-23 23:19:52', '2025-08-24 04:49:52'),
('d93610ad2d2f0549bccce2b6b7e4a2b0c66fbba74fff05f686befe14483212cf62784dcb7a884da5', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:20:28', '2024-08-23 23:20:28', '2025-08-24 04:50:28'),
('22e83239a24a167a9a9b5567a0d59012b2ed5cff5b6eefa47a0c94d9108efe1c6958e27ec8a1fa7b', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:21:02', '2024-08-23 23:21:02', '2025-08-24 04:51:02'),
('bfb76aa6510d7afa7d77101602191812ca2e6c81870075b2fa468cf4750a53b4ec7f9abf38c955b6', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:22:23', '2024-08-23 23:22:23', '2025-08-24 04:52:23'),
('fac67daa49dc4f0b1640e4142ff4cb74e4e50bb054b5d8f537ab55155cb35d494cec7f8208d7f6ef', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:23:11', '2024-08-23 23:23:11', '2025-08-24 04:53:11'),
('1309c1f0b16142d6f3187c53740053509717374dff629593ad2db1868d223eb39db74d1d59b96647', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:24:29', '2024-08-23 23:24:29', '2025-08-24 04:54:29'),
('c7ff769f18abec1c693a03bbed46c33a4efae3136aade141e090cd61d28c9ac70957e458fd9cfeb0', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:27:37', '2024-08-23 23:27:37', '2025-08-24 04:57:37'),
('3e3003f35ad15498fb0aa043394131e78a97c0dceadd787322a32bf7bdefdbfe090a09902b6e52a6', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:27:58', '2024-08-23 23:27:58', '2025-08-24 04:57:58'),
('51ba665aa3d2d66bdbb73ee7828712db632525d14df81e035b1c03f2ac4ce28e880b76fa1331169e', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:34:26', '2024-08-23 23:34:26', '2025-08-24 05:04:26'),
('f29d52aa5994406035a4d00ccedd82473be2d3194f9b888ac5651cb3333b94dd6bba92bfe42c9204', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:35:15', '2024-08-23 23:35:15', '2025-08-24 05:05:15'),
('094064f507c44c33f8592a2c974b6f328d863831dadf5f68f885e948358bcc79a8c0d95d5175ef2e', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:36:22', '2024-08-23 23:36:22', '2025-08-24 05:06:22'),
('078df3f5e3cd7b283ffe1541f838b22a8f5d521170c3d0cad8b2bcc6dc6dd987a7f5a8ea9a14ea96', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:36:52', '2024-08-23 23:36:52', '2025-08-24 05:06:52'),
('1f70dca5da5002b7ffbc0d9cfa4403d9b9787e2c51e4ec55baa0c382c4fb68edc4e6b7e00daefcb0', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:43:39', '2024-08-23 23:43:40', '2025-08-24 05:13:39'),
('17d4a86e7b94283282862ccfdc2020b02115f8e9be006718e6fc7adc37f63cca63f100842b531073', 1, 2, 'mag-srl', '[]', 0, '2024-08-23 23:45:50', '2024-08-23 23:45:50', '2025-08-24 05:15:50'),
('6666c004e9bc0d504ebcee07063f70d87331cfd238145b6e63996a2b45e6f57b2fb9216319c238a0', 3, 2, 'mag-srl', '[]', 0, '2024-08-23 23:48:06', '2024-08-23 23:48:06', '2025-08-24 05:18:06'),
('e5761267ba530e607703d47d71b5afcf9621c6aa5a83b79461ad2d34a7d46191bc759999a7dd4ac6', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 04:33:21', '2024-08-24 04:33:21', '2025-08-24 10:03:21'),
('f74afa3f52105dd886160c81070ee3106b0f88c0a2d489acc091b414ee37515dbec57f5b92762cda', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 04:40:14', '2024-08-24 04:40:14', '2025-08-24 10:10:14'),
('ebbd820bf6e1d57dbe58128d98b8fcbb0b8bafa648c941ba3e80a75f22f85eef1aee3d305ff20c97', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 04:40:47', '2024-08-24 04:40:47', '2025-08-24 10:10:47'),
('f16d8dbcf17b5b5cef7e53f1052d024ba82c248534e092c4db002ee238cbbc660b5d0365d3c37957', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 04:40:49', '2024-08-24 04:40:49', '2025-08-24 10:10:49'),
('7d10b2a5994d494e8c213ee82f47ee8f2cae8afa21b2eb85b1bf1c66b46ad3d14e7f673d10c99b0e', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 04:41:42', '2024-08-24 04:41:42', '2025-08-24 10:11:42'),
('f09d309521f2a13cb7f94bdfd176946194df2ea597f36eae48a13a883f2b9d79c396f34a2dfe71a4', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:26:53', '2024-08-24 05:26:53', '2025-08-24 10:56:53'),
('dc6f709a23bcaa5f24e58a8a3c5f8671211ea764460e23966ece27a93a7ebfcaba1bf6e43dfb0460', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:27:46', '2024-08-24 05:27:46', '2025-08-24 10:57:46'),
('a625442c3a30d01b1b9a50be4d985fb570ea73059962ec65e6f7bebe27b71c903fa333f1861bd409', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:28:46', '2024-08-24 05:28:46', '2025-08-24 10:58:46'),
('1db33af3c86d0f40e14f75f28430a29bd3aa6749a119920093fd8ba994807b90037b3a582e7fdc97', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:29:46', '2024-08-24 05:29:46', '2025-08-24 10:59:46'),
('eb281d4f9e33d6e9302a0dc0c536e019be9f2c8517e9afb1089d1984d766fbbf31c1cab8e860bea5', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:30:15', '2024-08-24 05:30:15', '2025-08-24 11:00:15'),
('ad5ff433308af170fd3a464e9b6ccc544c157a777519a47c7fe83a61822024163e2a191f5a0f0d2c', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:32:43', '2024-08-24 05:32:43', '2025-08-24 11:02:43'),
('106b53008c967807f851c3fd4a4cf43aeca769cd67f787771df010b7b9ae43f57100adbd110800f0', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:43:34', '2024-08-24 05:43:34', '2025-08-24 11:13:34'),
('d89a88f974a2c612a6863a40f7e36ef6fc9c06f7222163a159112b25be3523d02d1742ca02265389', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:43:42', '2024-08-24 05:43:42', '2025-08-24 11:13:42'),
('7e46e8c23bd25f48b73ebebb9189288e58dc39dff9853bc15b2c6da208f947281a79543d45c22f20', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 05:44:03', '2024-08-24 05:44:03', '2025-08-24 11:14:03'),
('6361e54a2298bf7bba1b05af5118a1c991004d68468a52dc3818178238426f1b6c7c10b0f8ac6df7', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 05:44:26', '2024-08-24 05:44:26', '2025-08-24 11:14:26'),
('1497a483d94d38bfb082fd3c239e570e3f5e4f750c36de5d551ef6a147964a3813efa5f9c2f0f5a7', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:44:41', '2024-08-24 05:44:41', '2025-08-24 11:14:41'),
('7c59c62fc942c93e9525f03d59636123148989cc9d6c5dba491fb23db93661e6fca354210a97ddea', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 05:44:46', '2024-08-24 05:44:46', '2025-08-24 11:14:46'),
('344119ad503c7b4800ee915ba279d60de34cf36f04767d532f9d079ab337146122091ff156bd4e75', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:44:54', '2024-08-24 05:44:54', '2025-08-24 11:14:54'),
('5422734f47467bac221c236d9896679c88762c375154447b2b0302478028b5fe868ee6ecbe5675c0', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:45:54', '2024-08-24 05:45:54', '2025-08-24 11:15:54'),
('63abe39d5225fb4b9760845935df2294f48f3073538a1a3783aa424e9708fb574ae9105d03688aa2', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 05:46:19', '2024-08-24 05:46:19', '2025-08-24 11:16:19'),
('5d65c0b22a513f86734ca559d2a010b505254469721458ba17198e8fd6bc995a4bec134392bb6f1e', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 05:46:28', '2024-08-24 05:46:28', '2025-08-24 11:16:28'),
('a355483487397d8ca846cf2df773db2a1804e08a43865809e8ea811b8d2ecdb5d51d169e79aee181', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 05:50:14', '2024-08-24 05:50:14', '2025-08-24 11:20:14'),
('d568a69d9938f54f11eb689d05802a4aa06c6c659f16060ccdabd820de042dad2e4ff9a87ef434ef', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 06:06:15', '2024-08-24 06:06:15', '2025-08-24 11:36:15'),
('44fc015effa075dcfe2cd08268734350194923e50f3ec38d0332de248dca19013181a7e157932930', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 06:06:24', '2024-08-24 06:06:24', '2025-08-24 11:36:24'),
('91a3366ab8e3b026dc6b2de855d65d06392cd77d173133f8526bc7c595c208ff949d22463ba77dee', 10, 2, 'mag-srl', '[]', 0, '2024-08-24 07:15:42', '2024-08-24 07:15:42', '2025-08-24 12:45:42'),
('76a8ea6a5af85db9029d23eed19cc427d8bdc22231a344832100da7c9dd891e28cc29afe4f25819e', 10, 2, 'mag-srl', '[]', 0, '2024-08-24 07:21:04', '2024-08-24 07:21:04', '2025-08-24 12:51:04'),
('70eea2428637dd2b6ce13ede5e2cc1b0a90d34dea13d4f52dff17cf546c2b9c04141575531c91a18', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 07:40:59', '2024-08-24 07:40:59', '2025-08-24 13:10:59'),
('c4a5a1adf488b07fd02140584cb7722becd48d959bd527eb47187e17e3380b3fa03c10a5309cbe13', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 08:30:39', '2024-08-24 08:30:39', '2025-08-24 14:00:39'),
('a26904ac7ac2a0df197db193997a96ca6359735416a1d0ae1aa93af28dcc95635bd9098d0f710866', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 08:35:18', '2024-08-24 08:35:18', '2025-08-24 14:05:18'),
('9507478dd9ac5d9ae51df25fa0ea4d2cba56319651c54d762d781f3976f7440f1e3321b635974b9e', 1, 2, 'mag-srl', '[]', 0, '2024-08-24 23:13:06', '2024-08-24 23:13:06', '2025-08-25 04:43:06'),
('2584eb5cf36fa7dee689558ba6b02fdef245cee4eaa33d2f8fbb399bdc3afb428fe4e5245fc249bb', 3, 2, 'mag-srl', '[]', 0, '2024-08-24 23:13:45', '2024-08-24 23:13:45', '2025-08-25 04:43:45'),
('fb91b86aebe12d35cf1659a9eff5a570942c5a9c8d388304babea138bd951afb3d6002d6f4244be6', 3, 2, 'mag-srl', '[]', 0, '2024-08-25 00:05:44', '2024-08-25 00:05:44', '2025-08-25 05:35:44'),
('86ed8134162dc0adb0a14177aca53fa204e71cc7f6a6b0ab995630ae41327be3af86372d0e6ec473', 3, 2, 'mag-srl', '[]', 0, '2024-08-25 00:12:02', '2024-08-25 00:12:02', '2025-08-25 05:42:02'),
('570c921e47d75f150135579fc10023f1d1b4733b2f00fee1a7ee4d4829cca9535269de5de7d5b5be', 3, 2, 'mag-srl', '[]', 0, '2024-08-25 00:14:42', '2024-08-25 00:14:42', '2025-08-25 05:44:42'),
('101272589227cca5d2d202818a87446202ffc0bd3d5025c605e9a8fcf3ce2984a9a3f2afa1b17605', 3, 2, 'mag-srl', '[]', 0, '2024-08-25 00:17:00', '2024-08-25 00:17:00', '2025-08-25 05:47:00'),
('b853c2c1d863ae059b9aabcbdce0e4ad99fa5d837c9e83820f0f7d4d2c7d8f2074995d9895c37722', 1, 2, 'mag-srl', '[]', 1, '2024-08-25 01:09:29', '2024-08-25 01:20:19', '2025-08-25 06:39:29'),
('fcb7beb66f484ad95f52940d66431dd0c3b84381d588529a6f3a102b05d9f9744ef1d3f622cba1ec', 1, 2, 'mag-srl', '[]', 0, '2024-08-25 01:21:13', '2024-08-25 01:21:13', '2025-08-25 06:51:13'),
('90e13ae9213140743edbd0f9c92ecc601f49c1700ea711ededc81956bfde0daaa74eb6b02c520c6b', 3, 2, 'mag-srl', '[]', 0, '2024-08-25 02:44:21', '2024-08-25 02:44:21', '2025-08-25 08:14:21'),
('fe102a323700340a94d90ed40b051b8f4e9745dbf227c526b6128acaf8f1c502e5011cdd04ecafed', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 00:14:05', '2024-08-27 00:14:05', '2025-08-27 05:44:05'),
('3aafe08a2ec66d32073da87a21248b13134b9fbdf78bad2e45ad620e9e0506adb2341e525c8ea879', 3, 2, 'mag-srl', '[]', 0, '2024-08-27 00:16:02', '2024-08-27 00:16:02', '2025-08-27 05:46:02'),
('96819ad27356f8b478a9e6f2134d9d3923401815cacb6542587d31edbbf31d60bb88e66bd4543444', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 00:29:23', '2024-08-27 00:29:23', '2025-08-27 05:59:23'),
('db705e82392cbc342497cda659abaca42b3be68ab150e6ee9351e0a45f037aece10858b6da9f042c', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 00:35:30', '2024-08-27 00:35:30', '2025-08-27 06:05:30'),
('0785f26a61af99f0247b374e1b2ab75e09668e76a14de1736dfdd9472cc558d0b9820bf65a7efeec', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 00:36:55', '2024-08-27 00:36:55', '2025-08-27 06:06:55'),
('3304dc72b0d90afcea1c10a00c9708137db9cd9522241483e35d5b957e909e14d189c07df6e52ce9', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 00:37:28', '2024-08-27 00:37:28', '2025-08-27 06:07:28'),
('0abe7b865575d5c8ce39fb20551f5748ad1d0a20280bfbd8597586dcd3c030c6b73684dbbcaac5a7', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 02:47:40', '2024-08-27 02:47:40', '2025-08-27 08:17:40'),
('197ffbffd6443c147930054a0b17fba238b64ff0621b59d18627b19cf4f08e723fe59d3f81949633', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 02:48:59', '2024-08-27 02:48:59', '2025-08-27 08:18:59'),
('c0c78307a1ddd915dbabf512ebd2783bf7f8e8c32a122fa487665f4c56ffa31c3d8e84d181c87b23', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:49:48', '2024-08-27 05:49:48', '2025-08-27 11:19:48'),
('97c6c35f7979c133cd316f434259b0095bfa2e28e17d2ca4a1b2a45f9b675744919d109725774db7', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:51:51', '2024-08-27 05:51:51', '2025-08-27 11:21:51'),
('2b20cce92f87fa6b14f9f992b2389ba90a837f1218437ed1623402bbd1ba98be3150107274d7898f', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:52:16', '2024-08-27 05:52:16', '2025-08-27 11:22:16'),
('51711ddc979c47d10c20b4c95aef836e464df8ec1b932ce469a342580763a4a1b87e6e0be15947b9', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:52:23', '2024-08-27 05:52:23', '2025-08-27 11:22:23'),
('3f92d1ca6e3ec543fee98261993585d5ed09aa86d217167914bc099124260df48efc2f63b4359937', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:53:09', '2024-08-27 05:53:09', '2025-08-27 11:23:09'),
('7c81476e92e9fdb74b5f7d466f7537ac279386a2ba9d0a54697719710943cf8cabc45071e3b57b23', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:53:34', '2024-08-27 05:53:34', '2025-08-27 11:23:34'),
('85c0ec2adc7cf769ec2d00f988e21492ea9d1c61cad987625e5a08309bcea4b5300a487b6cfc1e1c', 1, 2, 'mag-srl', '[]', 0, '2024-08-27 05:54:39', '2024-08-27 05:54:39', '2025-08-27 11:24:39'),
('94e794da39914ddaf1d5de37495837623e9ae07b7cae0f6babb44bae78e4f08a8d8132d1873a404e', 3, 2, 'mag-srl', '[]', 0, '2024-08-30 07:27:13', '2024-08-30 07:27:14', '2025-08-30 12:57:13'),
('e505b10ff5f67576f1b6e6213abe52cfa1c421fc77e9c18d4438429a02be34ea59544139ee341532', 1, 2, 'mag-srl', '[]', 0, '2024-08-31 01:59:43', '2024-08-31 01:59:43', '2025-08-31 07:29:43'),
('028c9c0b158b331b6df6623a9497ed7c52c1443ba0e946d86af4d8c73f945f431b2021d0aa237b7f', 1, 2, 'mag-srl', '[]', 0, '2024-08-31 03:23:56', '2024-08-31 03:23:56', '2025-08-31 08:53:56'),
('eecb0c08d15dbd6ec17208b20b20166df1f5fadaf955be357fdc2ddd5eb494a80bef19d04dc62c2d', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 01:17:52', '2024-09-03 01:17:52', '2025-09-03 06:47:52'),
('a7ec93301c87f3bf724b2cdb3f428679b9406be61507ab6d91c4bcca149b55784b68337439250039', 3, 2, 'mag-srl', '[]', 0, '2024-09-03 01:40:34', '2024-09-03 01:40:34', '2025-09-03 07:10:34'),
('de5e1cf73b65badb85f134ef7823645a45cec988ea7453ccfc661299e2b7d8e64c05a32c7a52331b', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 02:01:09', '2024-09-03 02:01:09', '2025-09-03 07:31:09'),
('32cecc04ed8d520e85488618dda508504f01b189aac7650f121e6bf3e087133aaab91ec5018c43d6', 3, 2, 'mag-srl', '[]', 0, '2024-09-03 03:33:29', '2024-09-03 03:33:29', '2025-09-03 09:03:29'),
('cef55b13c109350330fa60ac653bdacbb0b45e7863c272460db2921023921948574e3d3a47a879f6', 3, 2, 'mag-srl', '[]', 0, '2024-09-03 03:38:22', '2024-09-03 03:38:22', '2025-09-03 09:08:22'),
('5147bf6d86ec08d61fd90b3e8d16ce01a0b81a0a96ac8b156a6c1a320bd5aab70bce4a921e90d615', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 04:28:31', '2024-09-03 04:28:31', '2025-09-03 09:58:31'),
('237c18a71aeae4589739817f6df2edbf0948d721b1241574299b99201275ce2a7193482a7b44ca42', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 04:46:13', '2024-09-03 04:46:13', '2025-09-03 10:16:13'),
('f3e0927e4cf6ae2ea1f43a53031336b72199f53624ca24ac17220d7d024b0dccd56335b7348fd0b1', 3, 2, 'mag-srl', '[]', 0, '2024-09-03 04:58:31', '2024-09-03 04:58:31', '2025-09-03 10:28:31'),
('cf1effc88768a1f029be93f60fc7ac9d0cfbd004dc0c915ca7f5c9dea8dc3c2c6486fa6c1ed503dc', 3, 2, 'mag-srl', '[]', 0, '2024-09-03 05:04:36', '2024-09-03 05:04:36', '2025-09-03 10:34:36'),
('cdbfab371c7e2594f662fc098f9f91f23b41734e26e9ad5018f7e59167388d28435601e80fdda698', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 05:10:34', '2024-09-03 05:10:34', '2025-09-03 10:40:34'),
('72d688ca1904db1b2544b8b7fc998b6cefae86e9201a77e889b37b3897fc4e12939ab624a771e8f4', 1, 2, 'mag-srl', '[]', 0, '2024-09-03 05:11:04', '2024-09-03 05:11:04', '2025-09-03 10:41:04'),
('7d9b7d95f3862d70329bf1a2ad23c97c4475a77bdb37b95d40b4ab9707c01ac612836521c1200224', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:24:33', '2024-10-22 03:24:33', '2025-10-22 08:54:33'),
('3857f2193219c7453fe9e731eff90d976098184765c4d49616bc64f22fd781d9e67dfe2c4b49bb54', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:29:25', '2024-10-22 03:29:26', '2025-10-22 08:59:25'),
('7648368abe45fb5221c54d6458dc311b539f2b750c2867bdc8de60fbf0d76e6d959d46f6eaf068a8', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:29:42', '2024-10-22 03:29:42', '2025-10-22 08:59:42'),
('8dfacb5f98db48af8748bf847c9821912345044b4dc50c71ba4e0fc7c7d0a1664525fe052d7784fc', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:30:13', '2024-10-22 03:30:13', '2025-10-22 09:00:13'),
('2629d2aef3a64430f7861b4320986aacd6db8de1940eca437d1d56ea08a0cb621af758391a02c19d', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:31:05', '2024-10-22 03:31:05', '2025-10-22 09:01:05'),
('815472f59620497a642238babfb2d56a3a7fff272c0b17e95b11d8f3b916d2c9599c73e5d3cb9673', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:31:32', '2024-10-22 03:31:32', '2025-10-22 09:01:32'),
('2d374a27e4d31a889a91aa027c542a3935f7adb58904e5bcfa690371ceeb5feb67e6982da7562fcf', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:31:42', '2024-10-22 03:31:42', '2025-10-22 09:01:42'),
('fcbb269c6d09cda0f375966eea537e3701ae202b56c959752bb981fbed23bb46dfc1ea250c27416e', 1, 2, 'mag-srl', '[]', 1, '2024-10-22 03:32:11', '2024-10-22 03:58:01', '2025-10-22 09:02:11'),
('e46f737f2d7450c94677c4d01dbb4e14698427f5912ad4efeaaec6edc6b92f9d3f1c5af3908a4ffc', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:39:42', '2024-10-22 03:39:42', '2025-10-22 09:09:42'),
('e4af51411f2d9ffa3a8469d92748dfc2e066c860ecbda93a94fe0cd2d18169fd24fe9175ca6e619c', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 03:40:41', '2024-10-22 03:40:41', '2025-10-22 09:10:41'),
('ad36c9f8263f846fd651d7c98b6934e27abbf41c2a4bbd9cebe9c72c9d2c4ff41464fced94fe3409', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 04:00:08', '2024-10-22 04:00:08', '2025-10-22 09:30:08'),
('b6f5b2ece42c10f29b5c9c349499d843045f5cb678368bffc51b4e982d1dc90d372bcf30668aca78', 1, 2, 'mag-srl', '[]', 0, '2024-10-22 08:24:36', '2024-10-22 08:24:36', '2025-10-22 13:54:36'),
('d7d60440c0bf81708df3c195a8ebb1ea655e838a82115f7ebcdecfbc5dfec04a5bca144bdea447ea', 1, 2, 'mag-srl', '[]', 0, '2024-10-26 00:59:31', '2024-10-26 00:59:32', '2025-10-26 06:29:31'),
('2c73cb5c040068471a8f402568e720ac4a64fa23a4fa5400c861993d9f5acccf8c301e2720044cff', 1, 2, 'mag-srl', '[]', 0, '2024-10-26 00:59:38', '2024-10-26 00:59:38', '2025-10-26 06:29:38'),
('2209ce52197bd7af93103e3705d9c65e1e1e53d1da2eb2f19c4319c21e440c517f6b8cc74caae252', 1, 2, 'mag-srl', '[]', 0, '2024-10-26 01:00:31', '2024-10-26 01:00:31', '2025-10-26 06:30:31'),
('7de3c1c2cba39416c3b8401ab068b6d9d3ff76fa828f714e74019a44fdf4de3ea0315ef9323c68a7', 1, 2, 'mag-srl', '[]', 0, '2024-10-26 03:19:14', '2024-10-26 03:19:14', '2025-10-26 08:49:14'),
('8c8d72da7751fc44f88925a6b30b84d8745fe077629b3e1b11766cf85d69bc8acbf0d4566c1181e5', 1, 2, 'mag-srl', '[]', 0, '2024-10-28 01:09:35', '2024-10-28 01:09:35', '2025-10-28 06:39:35');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `oauth_clients`
--

DROP TABLE IF EXISTS `oauth_clients`;
CREATE TABLE IF NOT EXISTS `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'mag', 'Hv35NV1F7fHkv32SA34LjKGHMpw6dupAdSbmX1MF', NULL, 'http://localhost', 1, 0, 0, '2024-08-22 05:22:31', '2024-08-22 05:22:31'),
(2, NULL, 'Laravel Personal Access Client', 'iqnVarQ5GSXItI03GYPYS8t5B3wAcjBqGLrQk04a', NULL, 'http://localhost', 1, 0, 0, '2024-08-22 05:33:02', '2024-08-22 05:33:02'),
(3, NULL, 'Laravel Password Grant Client', 'aCI8gmUjrx3fZSGGaqf9pq0c5l2YphaZJvzv5zqZ', 'users', 'http://localhost', 0, 1, 0, '2024-08-22 05:33:02', '2024-08-22 05:33:02');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_personal_access_clients`
--

DROP TABLE IF EXISTS `oauth_personal_access_clients`;
CREATE TABLE IF NOT EXISTS `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2024-08-22 05:22:31', '2024-08-22 05:22:31'),
(2, 2, '2024-08-22 05:33:02', '2024-08-22 05:33:02');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_refresh_tokens`
--

DROP TABLE IF EXISTS `oauth_refresh_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
CREATE TABLE IF NOT EXISTS `otps` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email_mobile` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `otp` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `otps_email_index` (`email_mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(125) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `receiver_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wallet_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_id` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_provider` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transaction_type` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `previous_amount` decimal(28,8) NOT NULL,
  `current_amount` decimal(28,8) NOT NULL,
  `total_amount` decimal(28,8) NOT NULL,
  `requested_amount` decimal(28,8) NOT NULL,
  `commission_amount` decimal(28,8) NOT NULL,
  `transaction_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `remarks` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `user_id`, `receiver_id`, `wallet_id`, `invoice_id`, `transaction_id`, `platform_name`, `platform_provider`, `country_id`, `transaction_type`, `image`, `previous_amount`, `current_amount`, `total_amount`, `requested_amount`, `commission_amount`, `transaction_status`, `comments`, `remarks`, `created_at`, `updated_at`) VALUES
(1, '3', '4', '3', 'INV1725354723649DxDHc', 'TRA17253547236679psle', 'Softieons', 'Softieons', '1', 'debit', NULL, 9900.80000000, 9890.80000000, 10.00000000, 10.00000000, 0.00000000, 'success', 'Payment for invoice INV1725354723649DxDHc', 'wallet_to_wallet_transaction', '2024-09-03 03:42:04', '2024-09-03 03:42:04'),
(2, '3', '4', '4', 'INV1725354723649DxDHc', 'TRA17253547236679psle', 'Softieons', 'Softieons', '1', 'credit', NULL, 10058.92000000, 10068.92000000, 10.00000000, 10.00000000, 0.00000000, 'success', 'Payment for invoice INV1725354723649DxDHc', 'wallet_to_wallet_transaction', '2024-09-03 03:42:04', '2024-09-03 03:42:04'),
(3, '3', '4', '3', 'INV1725355332286CmN7h', 'TRA1725355332286wIrhj', 'Softieons', 'Softieons', '1', 'debit', NULL, 9890.80000000, 9885.25000000, 5.55000000, 5.55000000, 0.00000000, 'success', 'Payment for invoice INV1725355332286CmN7h', 'wallet_to_wallet_transaction', '2024-09-03 03:52:13', '2024-09-03 03:52:13'),
(4, '3', '4', '4', 'INV1725355332286CmN7h', 'TRA1725355332286wIrhj', 'Softieons', 'Softieons', '1', 'credit', NULL, 10068.92000000, 10074.47000000, 5.55000000, 5.55000000, 0.00000000, 'success', 'Payment for invoice INV1725355332286CmN7h', 'wallet_to_wallet_transaction', '2024-09-03 03:52:13', '2024-09-03 03:52:13'),
(5, '3', '4', '3', 'INV1725359856882hY0Kc', 'TRA1725359856888rAgY0', 'Softieons', 'Softieons', '1', 'debit', NULL, 9885.25000000, 9875.25000000, 10.00000000, 10.00000000, 0.00000000, 'success', 'Payment for invoice INV1725359856882hY0Kc', 'wallet_to_wallet_transaction', '2024-09-03 05:06:11', '2024-09-03 05:06:11'),
(6, '3', '4', '4', 'INV1725359856882hY0Kc', 'TRA1725359856888rAgY0', 'Softieons', 'Softieons', '1', 'credit', NULL, 10074.47000000, 10084.47000000, 10.00000000, 10.00000000, 0.00000000, 'success', 'Payment for invoice INV1725359856882hY0Kc', 'wallet_to_wallet_transaction', '2024-09-03 05:06:11', '2024-09-03 05:06:11'),
(7, '3', '4', '3', 'INV1725359890231novBt', 'TRA17253598902328hjv5', 'Softieons', 'Softieons', '1', 'debit', NULL, 9875.25000000, 9775.25000000, 100.00000000, 100.00000000, 0.00000000, 'success', 'Payment for invoice INV1725359890231novBt', 'wallet_to_wallet_transaction', '2024-09-03 05:06:45', '2024-09-03 05:06:45'),
(8, '3', '4', '4', 'INV1725359890231novBt', 'TRA17253598902328hjv5', 'Softieons', 'Softieons', '1', 'credit', NULL, 10084.47000000, 10184.47000000, 100.00000000, 100.00000000, 0.00000000, 'success', 'Payment for invoice INV1725359890231novBt', 'wallet_to_wallet_transaction', '2024-09-03 05:06:45', '2024-09-03 05:06:45');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_limits`
--

DROP TABLE IF EXISTS `transaction_limits`;
CREATE TABLE IF NOT EXISTS `transaction_limits` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `daily_max_limit` decimal(28,8) NOT NULL,
  `monthly_max_limit` decimal(28,8) NOT NULL,
  `max_amount_limit` decimal(28,8) NOT NULL,
  `min_amount_limit` decimal(28,8) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `transaction_limits`
--

INSERT INTO `transaction_limits` (`id`, `role_id`, `slug`, `daily_max_limit`, `monthly_max_limit`, `max_amount_limit`, `min_amount_limit`, `created_at`, `updated_at`) VALUES
(1, '1', 'add_money', 7000.00000000, 70000.00000000, 1000.00000000, 1.00000000, '2024-08-23 00:04:06', '2024-08-27 04:34:58'),
(2, '2', 'add_money', 4.00000000, 120.00000000, 2000.00000000, 1.00000000, '2024-08-23 00:04:06', '2024-08-23 00:04:06'),
(3, '3', 'add_money', 6.00000000, 180.00000000, 5000.00000000, 1.00000000, '2024-08-23 00:34:53', '2024-08-23 00:34:53'),
(4, '1', 'wallet_to_wallet_transaction', 8000.00000000, 80000.00000000, 1100.00000000, 1.00000000, '2024-08-23 00:34:53', '2024-08-27 04:34:58'),
(5, '2', 'wallet_to_wallet_transaction', 4.00000000, 120.00000000, 2000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32'),
(6, '3', 'wallet_to_wallet_transaction', 6.00000000, 180.00000000, 5000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32'),
(7, '1', 'direct_company_transfer', 2.00000000, 60.00000000, 1000.00000000, 1.00000000, '2024-08-23 00:04:06', '2024-08-23 00:04:06'),
(8, '2', 'direct_company_transfer', 4.00000000, 120.00000000, 2000.00000000, 1.00000000, '2024-08-23 00:04:06', '2024-08-23 00:04:06'),
(9, '3', 'direct_company_transfer', 6.00000000, 180.00000000, 5000.00000000, 1.00000000, '2024-08-23 00:34:53', '2024-08-23 00:34:53'),
(10, '1', 'mobile_money_transfer', 2.00000000, 60.00000000, 1000.00000000, 1.00000000, '2024-08-23 00:34:53', '2024-08-23 00:34:53'),
(11, '2', 'mobile_money_transfer', 4.00000000, 120.00000000, 2000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32'),
(12, '3', 'mobile_money_transfer', 6.00000000, 180.00000000, 5000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32'),
(13, '1', 'direct_bank_transfer', 2.00000000, 60.00000000, 1000.00000000, 1.00000000, '2024-08-23 00:34:53', '2024-08-23 00:34:53'),
(14, '2', 'direct_bank_transfer', 4.00000000, 120.00000000, 2000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32'),
(15, '3', 'direct_bank_transfer', 6.00000000, 180.00000000, 5000.00000000, 1.00000000, '2024-08-23 00:36:32', '2024-08-23 00:36:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_role_id` bigint(20) UNSIGNED NOT NULL DEFAULT '1',
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_id` bigint(20) UNSIGNED NOT NULL,
  `mobile_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `formatted_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referalcode` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
  `is_company` tinyint(1) NOT NULL DEFAULT '0',
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_email_verify` int(11) NOT NULL DEFAULT '0',
  `is_mobile_verify` tinyint(1) NOT NULL DEFAULT '0',
  `is_kyc_verify` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `balance` decimal(25,10) NOT NULL DEFAULT '0.0000000000',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_user_role_id_index` (`user_role_id`),
  KEY `users_country_id_index` (`country_id`),
  KEY `users_is_company_index` (`is_company`),
  KEY `users_status_index` (`status`),
  KEY `users_role_index` (`role`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_role_id`, `first_name`, `last_name`, `email`, `password`, `country_id`, `mobile_number`, `formatted_number`, `referalcode`, `fcm_token`, `is_company`, `verification_token`, `is_email_verify`, `is_mobile_verify`, `is_kyc_verify`, `status`, `role`, `balance`, `remember_token`, `profile_image`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Dinesh', 'Dinesh', 'dinesh.softieons@gmail.com', '$2y$12$uIvBXRWkj31Vx.nNaT7uc.VAIeYCVHdo/P5rt/Efj1f9zgDjlUAnC', 1, '7507642090', '+917507642010', 'ref123', NULL, 1, NULL, 1, 0, 0, 1, 'admin', 0.0000000000, NULL, 'profile_images/1/671dd972881b0_1730009458.jpeg', NULL, '2024-10-22 07:22:03', '2024-10-27 00:40:58'),
(3, 1, 'Nitesh', 'Kumar', 'nitesh.softieons@gmail.com', '$2y$12$AGmUmi0BKTN70xuK7DJOPeTVQC3u3JQOlm5cC60fY8C/hNzJ3qTRu', 1, '7874449936', '+917874449936', 'ref123', NULL, 1, NULL, 1, 0, 0, 1, 'user', 0.0000000000, NULL, NULL, NULL, '2024-10-23 04:02:26', '2024-10-23 04:10:45');

-- --------------------------------------------------------

--
-- Table structure for table `user_kycs`
--

DROP TABLE IF EXISTS `user_kycs`;
CREATE TABLE IF NOT EXISTS `user_kycs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `video` text COLLATE utf8mb4_unicode_ci,
  `document` json DEFAULT NULL,
  `verification_status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identification_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verification_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `meta_response` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_kycs_user_id_index` (`user_id`),
  KEY `user_kycs_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_kycs`
--

INSERT INTO `user_kycs` (`id`, `user_id`, `email`, `video`, `document`, `verification_status`, `identification_id`, `verification_id`, `meta_response`, `created_at`, `updated_at`) VALUES
(1, 1, 'dinesh.softieons@gmail.com', '/storage/kyc-videos/1/671c8cf9d4d4f.mp4', '[\"/storage/kyc-documents/1/671c8cf9f25ad.jpg\"]', 'verified', '6707cc7143c554001d80e133', '6707cc7143c554001d80e135', '{\"identityId\": \"66deed0b6f9de1001d0d6d0a\", \"verificationId\": \"66deed0b6f9de1001d0d6d0b\"}', '2024-10-23 09:16:15', '2024-10-26 01:02:25');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` int(11) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_roles_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `role_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Silver', 1, '2024-10-22 13:10:23', '2024-10-22 13:10:23'),
(2, 'Gold', 1, '2024-10-22 13:10:46', '2024-10-22 13:10:48'),
(3, 'Platinum', 1, '2024-10-22 13:10:46', '2024-10-22 13:10:48');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `company_details`
--
ALTER TABLE `company_details`
  ADD CONSTRAINT `company_details_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_kycs`
--
ALTER TABLE `user_kycs`
  ADD CONSTRAINT `user_kycs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
