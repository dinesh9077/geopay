-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 22, 2024 at 12:49 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `magsrl`
--

-- --------------------------------------------------------

--
-- Table structure for table `company_details`
--

DROP TABLE IF EXISTS `company_details`;
CREATE TABLE IF NOT EXISTS `company_details` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `company_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `business_licence` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `tin` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vat` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `company_address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `postcode` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank_code` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `company_details`
--

INSERT INTO `company_details` (`id`, `user_id`, `company_name`, `business_licence`, `tin`, `vat`, `company_address`, `postcode`, `bank_name`, `account_number`, `bank_code`, `created_at`, `updated_at`) VALUES
(1, 1, 'Softieons', 'test214soft', '147asd15', '3f1541dfe1', '5th floor Abhinandan Royal, Bhatar road, Althan, Surat', '395007', 'IDFC Bank', '21234567890123', 'IDFC2024', '2024-08-22 04:53:36', '2024-08-22 04:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isdcode` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_flag` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=235 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `isdcode`, `country_flag`, `created_at`, `updated_at`) VALUES
(1, 'Afghanistan ', '+93', 'afghanistan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(2, 'Albania ', '+355', 'albania.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(3, 'Algeria ', '+213', 'algeria.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(4, 'American Samoa', '+1684', 'american-samoa.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(5, 'Andorra, Principality of ', '+376', 'andorra.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(6, 'Angola', '+244', 'angola.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(7, 'Anguilla ', '+1264', 'anguilla.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(8, 'Antarctica', '+672', 'Antarctic_Treaty.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(9, 'Antigua and Barbuda', '+1-268', 'antigua-and-barbuda.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(10, 'Argentina ', '+54', 'argentina.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(11, 'Armenia', '+374', 'armenia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(12, 'Aruba', '+297', 'aruba.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(13, 'Australia', '+61', 'australia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(14, 'Austria', '+43', 'austria.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(15, 'Azerbaijan or Azerbaidjan (Former Azerbaijan Soviet Socialist Republic)', '+994', 'azerbaijan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(16, 'Bahamas, Commonwealth of The', '+1-242', 'bahamas.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(17, 'Bahrain, Kingdom of (Former Dilmun)', '+973', 'bahrain.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(18, 'Bangladesh (Former East Pakistan)', '+880', 'bangladesh.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(19, 'Barbados ', '+1246', 'barbados.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(20, 'Belarus (Former Belorussian [Byelorussian] Soviet Socialist Republic)', '+375', 'belarus.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(21, 'Belgium ', '+32', 'belgium.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(22, 'Belize (Former British Honduras)', '+501', 'belize.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(23, 'Benin (Former Dahomey)', '+229', 'benin.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(24, 'Bermuda ', '+1-441', 'bermuda.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(25, 'Bhutan, Kingdom of', '+975', 'bhutan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(26, 'Bolivia ', '+591', 'bolivia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(27, 'Bosnia and Herzegovina ', '+387', 'bosnia-and-herzegovina.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(28, 'Botswana (Former Bechuanaland)', '+267', 'botswana.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(29, 'Bouvet Island (Territory of Norway)', '+47', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(30, 'Brazil ', '+55', 'brazil.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(31, 'British Indian Ocean Territory (BIOT)', '+246', 'british-indian-ocean-territory.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(32, 'Brunei (Negara Brunei Darussalam) ', '+673', 'brunei.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(33, 'Bulgaria ', '+359', 'bulgaria.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(34, 'Burkina Faso (Former Upper Volta)', '+226', 'burkina-faso.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(35, 'Burundi (Former Urundi)', '+257', 'burundi.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(36, 'Cambodia, Kingdom of (Former Khmer Republic, Kampuchea Republic)', '+855', 'cambodia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(37, 'Cameroon (Former French Cameroon)', '+237', 'cameroon.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(38, 'Canada ', '+1', 'canada.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(39, 'Cape Verde ', '+238', 'cape-verde.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(40, 'Cayman Islands ', '+1-345', 'cayman-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(41, 'Central African Republic ', '+236', 'central-african-republic.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(42, 'Chad ', '+235', 'chad.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(43, 'Chile ', '+56', 'chile.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(44, 'China ', '+86', 'china.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(45, 'Christmas Island ', '+53', 'christmas-island.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(46, 'Cocos (Keeling) Islands ', '+61', 'cocos-island.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(47, 'Colombia ', '+57', 'colombia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(48, 'Comoros, Union of the ', '+269', 'comoros.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(49, 'Congo, Democratic Republic of the (Former Zaire) ', '+243', 'congo.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(50, 'Congo, Republic of the', '+242', 'Republic_of_the_Congo.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(51, 'Cook Islands (Former Harvey Islands)', '+682', 'cook-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(52, 'Costa Rica ', '+506', 'costa-rica.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(53, 'Cote D\'Ivoire (Former Ivory Coast) ', '+225', 'cote_d.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(54, 'Croatia (Hrvatska) ', '+385', 'croatia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(55, 'Cuba ', '+53', 'cuba.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(56, 'Cyprus ', '+357', 'cyprus.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(57, 'Czech Republic', '+420', 'czech-republic.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(58, 'Denmark ', '+45', 'denmark.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(59, 'Djibouti (Former French Territory of the Afars and Issas, French Somaliland)', '+253', 'djibouti.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(60, 'Dominica ', '+1-767', 'dominica.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(61, 'Dominican Republic ', '+1809', 'dominican-republic.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(62, 'East Timor (Former Portuguese Timor)', '+670', 'east-timor.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(63, 'Ecuador ', '+593 ', 'ecuador.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(64, 'Egypt (Former United Arab Republic - with Syria)', '+20', 'egypt.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(65, 'El Salvador ', '+503', 'El_Salvador.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(66, 'Equatorial Guinea (Former Spanish Guinea)', '+240', 'equatorial-guinea.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(67, 'Eritrea (Former Eritrea Autonomous Region in Ethiopia)', '+291', 'eritrea.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(68, 'Estonia (Former Estonian Soviet Socialist Republic)', '+372', 'estonia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(69, 'Ethiopia (Former Abyssinia, Italian East Africa)', '+251', 'ethiopia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(70, 'Falkland Islands (Islas Malvinas) ', '+500', 'falkland-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(71, 'Faroe Islands ', '+298', 'faroe-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(72, 'Fiji ', '+679', 'fiji.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(73, 'Finland ', '+358', 'finland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(74, 'France ', '+33', 'france.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(75, 'French Guiana or French Guyana ', '+594', 'French_Guiana.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(76, 'French Polynesia (Former French Colony of Oceania)', '+689', 'french-polynesia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(77, 'Gabon (Gabonese Republic)', '+241', 'gabon.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(78, 'Gambia, The ', '+220', 'gambia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(79, 'Georgia (Former Georgian Soviet Socialist Republic)', '+995', 'georgia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(80, 'Germany ', '+49', 'germany.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(81, 'Ghana (Former Gold Coast)', '+233', 'ghana.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(82, 'Gibraltar ', '+350', 'gibraltar.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(83, 'Greece ', '+30', 'greece.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(84, 'Greenland ', '+299', 'greenland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(85, 'Grenada ', '+1-473', 'grenada.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(86, 'Guadeloupe', '+590', 'GuadeloupeFlag.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(87, 'Guam', '+1-671', 'guam.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(88, 'Guatemala ', '+502', 'guatemala.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(89, 'Guinea (Former French Guinea)', '+224', 'guinea.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(90, 'Guinea-Bissau (Former Portuguese Guinea)', '+245', 'Guinea-Bissau.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(91, 'Guyana (Former British Guiana)', '+592', 'guyana.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(92, 'Haiti ', '+509', 'haiti.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(93, 'Holy See (Vatican City State)', '+379', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(94, 'Honduras ', '+504', 'honduras.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(95, 'Hong Kong ', '+852', 'hong-kong.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(96, 'Hungary ', '+36', 'hungary.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(97, 'Iceland ', '+354', 'iceland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(98, 'India ', '+91', 'india.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(99, 'Iran, Islamic Republic of', '+98', 'iran.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(100, 'Iraq ', '+964', 'iraq.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(101, 'Ireland ', '+353', 'ireland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(102, 'Israel ', '+972', 'israel.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(103, 'Italy ', '+39', 'italy.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(104, 'Jamaica ', '+1-876', 'jamaica.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(105, 'Japan ', '+81', 'japan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(106, 'Jordan (Former Transjordan)', '+962', 'jordan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(107, 'Kazakstan or Kazakhstan (Former Kazakh Soviet Socialist Republic)', '+7', 'kazakhstan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(108, 'Kiribati (Pronounced keer-ree-bahss) (Former Gilbert Islands)', '+686', 'kiribati.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(109, 'Korea, Democratic People\'s Republic of (North Korea)', '+850', 'korea_democratic.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(110, 'Korea, Republic of (South Korea) ', '+82', 'Republic_of_S_Korea.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(111, 'Kuwait ', '+965', 'kuwait.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(112, 'Kyrgyzstan (Kyrgyz Republic) (Former Kirghiz Soviet Socialist Republic)', '+996', 'kyrgyzstan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(113, 'Lao People\'s Democratic Republic (Laos)', '+856', 'laos.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(114, 'Latvia (Former Latvian Soviet Socialist Republic)', '+371', 'latvia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(115, 'Lebanon ', '+961', 'lebanon.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(116, 'Lesotho (Former Basutoland)', '+266', 'lesotho.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(117, 'Liberia ', '+231', 'liberia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(118, 'Libya (Libyan Arab Jamahiriya)', '+218', 'libya.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(119, 'Liechtenstein ', '+423', 'liechtenstein.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(120, 'Lithuania (Former Lithuanian Soviet Socialist Republic)', '+370', 'lithuania.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(121, 'Luxembourg ', '+352', 'luxembourg.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(122, 'Macau ', '+853', 'macao.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(123, 'Macedonia, The Former Yugoslav Republic of', '+389', 'Macedonia.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(124, 'Madagascar (Former Malagasy Republic)', '+261', 'madagascar.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(125, 'Malawi (Former British Central African Protectorate, Nyasaland)', '+265', 'malawi.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(126, 'Malaysia ', '+60', 'malaysia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(127, 'Maldives ', '+960', 'maldives.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(128, 'Mali (Former French Sudan and Sudanese Republic) ', '+223', 'mali.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(129, 'Malta ', '+356', 'malta.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(130, 'Marshall Islands (Former Marshall Islands District - Trust Territory of the Pacific Islands)', '+692', 'marshall-island.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(131, 'Martinique (French) ', '+596', 'martinique.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(132, 'Mauritania ', '+222', 'mauritania.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(133, 'Mauritius ', '+230', 'mauritius.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(134, 'Mayotte (Territorial Collectivity of Mayotte)', '+269', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(135, 'Mexico ', '+52', 'mexico.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(136, 'Micronesia, Federated States of (Former Ponape, Truk, and Yap Districts - Trust Territory of the Pacific Islands)', '+691', 'micronesia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(137, 'Moldova, Republic of', '+373', 'moldova.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(138, 'Monaco, Principality of', '+377', 'monaco.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(139, 'Mongolia (Former Outer Mongolia)', '+976', 'mongolia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(140, 'Montserrat ', '+1-664', 'montserrat.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(141, 'Morocco ', '+212', 'morocco.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(142, 'Mozambique (Former Portuguese East Africa)', '+258', 'mozambique.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(143, 'Myanmar, Union of (Former Burma)', '+95', 'myanmar.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(144, 'Namibia (Former German Southwest Africa, South-West Africa)', '+264', 'namibia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(145, 'Nauru (Former Pleasant Island)', '+674', 'nauru.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(146, 'Nepal ', '+977', 'nepal.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(147, 'Netherlands ', '+31', 'netherlands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(148, 'Netherlands Antilles (Former Curacao and Dependencies)', '+599', 'neatherlans-Antilles.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(149, 'New Caledonia ', '+687', 'NewCaledonia.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(150, 'New Zealand (Aotearoa) ', '+64', 'new-zealand.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(151, 'Nicaragua ', '+505', 'nicaragua.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(152, 'Niger ', '+227', 'niger.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(153, 'Nigeria ', '+234', 'nigeria.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(154, 'Niue (Former Savage Island)', '+683', 'niue.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(155, 'Norfolk Island ', '+672', 'norfolk-island.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(156, 'Northern Mariana Islands (Former Mariana Islands District - Trust Territory of the Pacific Islands)', '+1-670', 'northern-marianas-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(157, 'Norway ', '+47', 'norway.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(158, 'Oman, Sultanate of (Former Muscat and Oman)', '+968', 'oman.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(159, 'Pakistan (Former West Pakistan)', '+92', 'pakistan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(160, 'Palau (Former Palau District - Trust Terriroty of the Pacific Islands)', '+680', 'palau.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(161, 'Palestinian State (Proposed)', '+970', 'palestine.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(162, 'Panama ', '+507', 'panama.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(163, 'Papua New Guinea (Former Territory of Papua and New Guinea)', '+675', 'papua-new-guinea.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(164, 'Paraguay ', '+595', 'paraguay.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(165, 'Peru ', '+51', 'peru.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(166, 'Philippines ', '+63', 'philippines.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(167, 'Pitcairn Island', '+64', 'pitcairn-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(168, 'Poland ', '+48', 'Poland.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(169, 'Portugal ', '+351', 'portugal.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(170, 'Puerto Rico ', '+1939', 'puerto-rico.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(171, 'Qatar, State of ', '+974 ', 'qatar.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(172, 'Reunion (French) (Former Bourbon Island)', '+262', 'ReunionFrench.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(173, 'Romania ', '+40', 'romania.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(174, 'Russian Federation ', '+7', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(175, 'Rwanda (Rwandese Republic) (Former Ruanda)', '+250', 'rwanda.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(176, 'Saint Helena ', '+290', 'saint-helena.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(177, 'Saint Kitts and Nevis (Former Federation of Saint Christopher and Nevis)', '+1-869', 'saint-kitts-and-nevis.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(178, 'Saint Lucia ', '+1-758', 'st-lucia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(179, 'Saint Pierre and Miquelon ', '+508', 'Saint-Pierre_and_Miquelon.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(180, 'Saint Vincent and the Grenadines ', '+1-784', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(181, 'Samoa (Former Western Samoa)', '+685', 'samoa.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(182, 'San Marino ', '+378', 'san-marino.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(183, 'Sao Tome and Principe ', '+239', 'sao-tome-and-principe.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(184, 'Saudi Arabia ', '+966', 'saudi-arabia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(185, 'Serbia, Republic of', '+381', 'serbia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(186, 'Senegal ', '+221', 'senegal.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(187, 'Seychelles ', '+248', 'seychelles.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(188, 'Sierra Leone ', '+232', 'sierra-leone.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(189, 'Singapore ', '+65', 'singapore.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(190, 'Slovakia', '+421', 'slovakia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(191, 'Slovenia ', '+386', 'slovenia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(192, 'Solomon Islands (Former British Solomon Islands)', '+677', 'solomon-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(193, 'Somalia (Former Somali Republic, Somali Democratic Republic) ', '+252', 'somalia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(194, 'South Africa (Former Union of South Africa)', '+27', 'south-africa.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(195, 'Spain ', '+34', 'spain.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(196, 'Sri Lanka (Former Serendib, Ceylon) ', '+94', 'sri-lanka.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(197, 'Sudan (Former Anglo-Egyptian Sudan) ', '+249', 'sudan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(198, 'Suriname (Former Netherlands Guiana, Dutch Guiana)', '+597', 'suriname.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(199, 'Svalbard (Spitzbergen) and Jan Mayen Islands ', '+47', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(200, 'Swaziland, Kingdom of ', '+268', 'switzerland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(201, 'Sweden ', '+46', 'sweden.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(202, 'Switzerland ', '+41', 'switzerland.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(203, 'Syria (Syrian Arab Republic) (Former United Arab Republic - with Egypt)', '+963', 'syria.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(204, 'Taiwan (Former Formosa)', '+886', 'taiwan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(205, 'Tajikistan (Former Tajik Soviet Socialist Republic)', '+992', 'tajikistan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(206, 'Tanzania, United Republic of (Former United Republic of Tanganyika and Zanzibar)', '+255', 'tanzania.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(207, 'Thailand (Former Siam)', '+66', 'thailand.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(208, 'Tokelau ', '+690', 'tokelau.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(209, 'Tonga, Kingdom of (Former Friendly Islands)', '+676', 'tonga.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(210, 'Trinidad and Tobago ', '+1868', 'trinidad-and-tobago.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(211, 'Tunisia ', '+216', 'tunisia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(212, 'Turkey ', '+90', 'turkey.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(213, 'Turkmenistan (Former Turkmen Soviet Socialist Republic)', '+993', 'turkmenistan.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(214, 'Turks and Caicos Islands ', '+1-649', 'turks-and-caicos.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(215, 'Tuvalu (Former Ellice Islands)', '+688', 'tuvalu.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(216, 'Uganda, Republic of', '+256', 'uganda.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(217, 'United Arab Emirates (UAE) (Former Trucial Oman, Trucial States)', '+971', 'united-arab-emirates.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(218, 'United Kingdom (Great Britain / UK)', '+44', 'united-kingdom.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(219, 'United States ', '+1', 'united-states-of-america.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(220, 'Uruguay, Oriental Republic of (Former Banda Oriental, Cisplatine Province)', '+598', 'uruguay.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(221, 'Vanuatu (Former New Hebrides)', '+678', 'vanuatu.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(222, 'Vatican City State (Holy See)', '+418', 'vatican-city.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(223, 'Venezuela ', '+58', 'venezuela.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(224, 'Vietnam ', '+84', 'vietnam.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(225, 'Virgin Islands, British ', '+1-284', 'virgin-islands.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(226, 'Virgin Islands, United States (Former Danish West Indies) ', '+1-340', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(227, 'Wallis and Futuna Islands ', '+681', 'Wallis_and_Futuna.jpg', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(228, 'Western Sahara (Former Spanish Sahara)', '+212', 'western-sahara.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(229, 'Yemen ', '+967', 'yemen.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(230, 'Yugoslavia ', '+38', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(231, 'Zaire (Former Congo Free State, Belgian Congo, Congo/Leopoldville, Congo/Kinshasa, Zaire) Now CD - Congo, Democratic Republic of the ', '+243', 'img', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(232, 'Zambia, Republic of (Former Northern Rhodesia) ', '+260', 'zambia.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(233, 'Zimbabwe, Republic of (Former Southern Rhodesia, Rhodesia) ', '+263', 'zimbabwe.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25'),
(234, 'Togo', '+228', 'togo.png', '2024-08-22 12:04:25', '2024-08-22 12:04:25');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(4, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(5, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(6, '2016_06_01_000004_create_oauth_clients_table', 1),
(7, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(8, '2019_08_19_000000_create_failed_jobs_table', 1),
(9, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(10, '2024_08_22_092750_create_company_details_table', 1),
(11, '2024_08_22_111619_create_countries_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `oauth_access_tokens`
--

DROP TABLE IF EXISTS `oauth_access_tokens`;
CREATE TABLE IF NOT EXISTS `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
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
('ff07b0e702c7fda1dbc1236970730012fb32bfb53ee134a831ffe35edd9542de5234038d0e6607f9', 1, 2, 'mag-srl', '[]', 0, '2024-08-22 05:36:55', '2024-08-22 05:36:55', '2025-08-22 11:06:55');

-- --------------------------------------------------------

--
-- Table structure for table `oauth_auth_codes`
--

DROP TABLE IF EXISTS `oauth_auth_codes`;
CREATE TABLE IF NOT EXISTS `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `client_id` bigint UNSIGNED NOT NULL,
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
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED DEFAULT NULL,
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
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `client_id` bigint UNSIGNED NOT NULL,
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
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
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
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_email_verify` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `country_id` int NOT NULL,
  `mobile_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `referalcode` text COLLATE utf8mb4_unicode_ci,
  `fcm_token` text COLLATE utf8mb4_unicode_ci,
  `is_company` tinyint(1) NOT NULL DEFAULT '0',
  `is_mobile_verify` tinyint(1) NOT NULL DEFAULT '0',
  `is_kyc_verify` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `is_email_verify`, `country_id`, `mobile_number`, `referalcode`, `fcm_token`, `is_company`, `is_mobile_verify`, `is_kyc_verify`, `status`, `role`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Rajan', 'Softieons', 'rajan.softieons@gmail.com', '0', 1, '7874449936', 'ref123', NULL, 1, 0, 0, 1, 'user', '$2y$12$2H.Kt2buXJARxERqpQIxku4fkDp7Eabl8q1VmOIBERJ7VBX2JguBS', NULL, '2024-08-22 04:53:36', '2024-08-22 04:53:36'),
(2, 'Ankit', 'Softieons', 'ankit.softieons@gmail.com', '0', 1, '987654321', 'ref123', NULL, 0, 0, 0, 1, 'user', '$2y$12$OkS84qbQzzKcPMaqchcv9uGYALTPcbZyes0jpkU4LsXJkhRKzOMC2', NULL, '2024-08-22 04:56:02', '2024-08-22 04:56:02');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
