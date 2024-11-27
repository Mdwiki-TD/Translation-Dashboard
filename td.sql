-- Adminer 4.8.1 MySQL 5.5.5-10.4.29-MariaDB-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `access_keys`;
CREATE TABLE `access_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL,
  `access_key` varchar(255) NOT NULL,
  `access_secret` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

INSERT INTO `accounts` (`id`, `username`, `password`, `email`) VALUES
(1,	'test',	'$2y$10$SfhYIDtn.iOuCW7zfoFLuuZHX6lja4lF4XA4JqNmpiH/.P3zB8JCa',	'test@test.com');

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(120) NOT NULL,
  `category2` varchar(120) NOT NULL DEFAULT '',
  `display` varchar(120) NOT NULL DEFAULT '',
  `campaign` varchar(120) NOT NULL DEFAULT '',
  `depth` int(2) DEFAULT NULL,
  `def` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `category`, `category2`, `display`, `campaign`, `depth`, `def`) VALUES
(1,	'RTT',	'',	'Main',	'Main',	1,	1),
(2,	'RTTCovid',	'',	'COVID',	'COVID',	0,	0),
(5,	'RTTHearing',	'',	'Hearing',	'Hearing',	0,	0),
(6,	'RTTOSH',	'',	'Occupational Health',	'Occupational Health',	0,	0),
(8,	'World Health Organization essential medicines',	'',	'Essential Medicines',	'Essential Medicines',	0,	0),
(9,	'WHRTT',	'',	'Women Health',	'Women Health',	0,	0),
(10,	'RTTILAE',	'',	'Epilepsy',	'Epilepsy',	0,	0),
(14,	'RTTILAE',	'WHRTT',	'',	'test',	0,	0);

DROP TABLE IF EXISTS `coordinator`;
CREATE TABLE `coordinator` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `coordinator` (`id`, `user`) VALUES
(1,	'Doc James'),
(2,	'Mr. Ibrahem')

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `word` int(6) DEFAULT NULL,
  `translate_type` varchar(20) DEFAULT NULL,
  `cat` varchar(120) DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `user` varchar(120) DEFAULT NULL,
  `pupdate` varchar(120) DEFAULT NULL,
  `target` varchar(120) DEFAULT NULL,
  `add_date` date DEFAULT NULL,
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `pages_users`;
CREATE TABLE `pages_users` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `user` varchar(120) DEFAULT NULL,
  `pupdate` varchar(120) DEFAULT NULL,
  `target` varchar(120) DEFAULT NULL,
  `add_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `pages_users` (`id`, `title`, `lang`, `user`, `pupdate`, `target`, `add_date`) VALUES

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `g_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(120) NOT NULL,
  PRIMARY KEY (`g_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`g_id`, `g_title`) VALUES
(4,	'ProZ'),
(5,	'Wiki'),
(6,	'Benevity'),
(7,	'Shani'),
(9,	'Hearing'),
(10,	'McMaster'),
(12,	'TWB');

DROP TABLE IF EXISTS `qids`;
CREATE TABLE `qids` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `qid` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `qids_others`;
CREATE TABLE `qids_others` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `qid` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `displayed` text NOT NULL,
  `Type` text NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `title`, `displayed`, `Type`, `value`) VALUES
(1,	'allow_type_of_translate',	'Allow whole translate',	'check',	'0'),
(2,	'translation_button_in_progress_table',	'Display translation button in progress table?',	'check',	'1'),
(3,	'fix_ref_in_text',	'Expand references in enwiki',	'check',	'0'),
(4,	'use_medwiki',	'Use Medwiki to translate?',	'check',	'1');

DROP TABLE IF EXISTS `translate_type`;
CREATE TABLE `translate_type` (
  `tt_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `tt_title` varchar(120) NOT NULL,
  `tt_lead` int(11) NOT NULL DEFAULT 1,
  `tt_full` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `wiki` varchar(255) NOT NULL,
  `user_group` varchar(120) NOT NULL,
  `reg_date` date NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`user_id`, `username`, `email`, `wiki`, `user_group`, `reg_date`) VALUES
(8,	'Mr. Ibrahem',	'noour.net@live.com',	'ar',	'Wiki',	'0000-00-00'),

DROP TABLE IF EXISTS `views`;
CREATE TABLE `views` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(120) NOT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `countall` int(6) DEFAULT NULL,
  `count2021` int(6) DEFAULT NULL,
  `count2022` int(6) DEFAULT NULL,
  `count2023` int(6) DEFAULT NULL,
  `count2024` int(6) DEFAULT 0,
  `count2025` int(6) DEFAULT 0,
  `count2026` int(6) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `wddone`;
CREATE TABLE `wddone` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mdtitle` varchar(120) NOT NULL,
  `target` varchar(120) NOT NULL,
  `lang` varchar(30) NOT NULL,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `words`;
CREATE TABLE `words` (
  `w_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `w_title` varchar(120) NOT NULL,
  `w_lead_words` int(6) DEFAULT NULL,
  `w_all_words` int(6) DEFAULT NULL,
  PRIMARY KEY (`w_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2024-09-18 22:10:03
