-- Adminer 5.0.5 MySQL 5.5.5-10.6.20-MariaDB-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `access_keys`;
CREATE TABLE `access_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(255) NOT NULL DEFAULT '',
  `access_key` varchar(255) NOT NULL DEFAULT '',
  `access_secret` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;


DROP TABLE IF EXISTS `all_articles`;
CREATE TABLE `all_articles` (
  `article_id` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  UNIQUE KEY `article_id` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `all_articles_titles`;
CREATE TABLE `all_articles_titles` (`qid` varchar(120), `title` varchar(255), `category` varchar(255));


DROP TABLE IF EXISTS `all_exists`;
CREATE TABLE `all_exists` (
  `article_id` varchar(255) NOT NULL,
  `code` varchar(25) NOT NULL,
  PRIMARY KEY (`article_id`,`code`),
  UNIQUE KEY `article_id_code` (`article_id`,`code`),
  CONSTRAINT `all_exists_ibfk_1` FOREIGN KEY (`article_id`) REFERENCES `all_articles` (`article_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `all_qids`;
CREATE TABLE `all_qids` (
  `qid` varchar(255) NOT NULL,
  `category` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`qid`),
  UNIQUE KEY `qid` (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `all_qidsexists`;
CREATE TABLE `all_qidsexists` (
  `qid` varchar(255) NOT NULL,
  `code` varchar(25) NOT NULL,
  PRIMARY KEY (`qid`,`code`),
  UNIQUE KEY `qid_code` (`qid`,`code`),
  CONSTRAINT `all_qidsexists_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `all_qids` (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `all_qids_titles`;
CREATE TABLE `all_qids_titles` (`qid` varchar(255), `title` varchar(120), `category` varchar(255));


DROP TABLE IF EXISTS `assessments`;
CREATE TABLE `assessments` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `importance` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `idx_assessments_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


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


DROP TABLE IF EXISTS `coordinator`;
CREATE TABLE `coordinator` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `enwiki_pageviews`;
CREATE TABLE `enwiki_pageviews` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `en_views` int(6) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `idx_enwiki_pageviews_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `full_translators`;
CREATE TABLE `full_translators` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `in_process`;
CREATE TABLE `in_process` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `lang` varchar(30) NOT NULL,
  `cat` varchar(255) DEFAULT 'RTT',
  `translate_type` varchar(20) DEFAULT 'lead',
  `word` int(6) DEFAULT 0,
  `add_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `keys_new`;
CREATE TABLE `keys_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `u_n` varchar(255) NOT NULL DEFAULT '',
  `a_k` varchar(255) NOT NULL DEFAULT '',
  `a_s` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_name` (`u_n`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;


DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `word` int(6) DEFAULT NULL,
  `translate_type` varchar(20) DEFAULT NULL,
  `cat` varchar(120) DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `user` varchar(120) DEFAULT NULL,
  `target` varchar(120) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `pupdate` varchar(120) DEFAULT NULL,
  `add_date` date NOT NULL DEFAULT curdate(),
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `pages_users`;
CREATE TABLE `pages_users` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `word` int(6) DEFAULT NULL,
  `translate_type` varchar(20) DEFAULT NULL,
  `cat` varchar(20) DEFAULT NULL,
  `lang` varchar(30) DEFAULT NULL,
  `user` varchar(120) DEFAULT NULL,
  `target` varchar(120) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `pupdate` varchar(120) DEFAULT NULL,
  `add_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `deleted` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `pages_users_to_main`;
CREATE TABLE `pages_users_to_main` (
  `id` int(6) unsigned NOT NULL,
  `new_target` varchar(255) NOT NULL DEFAULT '',
  `new_user` varchar(255) NOT NULL DEFAULT '',
  KEY `id` (`id`),
  CONSTRAINT `pages_users_to_main_ibfk_1` FOREIGN KEY (`id`) REFERENCES `pages_users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `g_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `g_title` varchar(120) NOT NULL,
  PRIMARY KEY (`g_id`),
  UNIQUE KEY `g_title` (`g_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `qids`;
CREATE TABLE `qids` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `qid` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_qid` (`title`,`qid`),
  KEY `idx_qids_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `qids_others`;
CREATE TABLE `qids_others` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(120) NOT NULL,
  `qid` varchar(120) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title_qid` (`title`,`qid`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `refs_counts`;
CREATE TABLE `refs_counts` (
  `r_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `r_title` varchar(120) NOT NULL,
  `r_lead_refs` int(6) DEFAULT NULL,
  `r_all_refs` int(6) DEFAULT NULL,
  PRIMARY KEY (`r_id`),
  UNIQUE KEY `r_title` (`r_title`),
  KEY `idx_refs_counts_r_title` (`r_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) NOT NULL,
  `displayed` varchar(500) NOT NULL,
  `Type` varchar(500) NOT NULL DEFAULT 'check',
  `value` int(1) NOT NULL DEFAULT 0,
  `ignored` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`),
  KEY `idx_title` (`title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `titles_infos`;
CREATE TABLE `titles_infos` (`title` varchar(120), `importance` varchar(120), `r_lead_refs` int(6), `r_all_refs` int(6), `en_views` int(6), `w_lead_words` int(6), `w_all_words` int(6), `qid` varchar(120));


DROP TABLE IF EXISTS `translate_type`;
CREATE TABLE `translate_type` (
  `tt_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `tt_title` varchar(120) NOT NULL,
  `tt_lead` int(11) NOT NULL DEFAULT 1,
  `tt_full` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`tt_id`),
  UNIQUE KEY `tt_title` (`tt_title`),
  KEY `idx_tt_title` (`tt_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL DEFAULT '',
  `wiki` varchar(255) NOT NULL DEFAULT '',
  `user_group` varchar(120) NOT NULL DEFAULT '',
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `users_no_inprocess`;
CREATE TABLE `users_no_inprocess` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `views_new`;
CREATE TABLE `views_new` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `target` varchar(120) NOT NULL,
  `lang` varchar(30) NOT NULL,
  `year` int(4) NOT NULL,
  `views` int(20) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `target_lang_year` (`target`,`lang`,`year`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `views_new_all`;
CREATE TABLE `views_new_all` (`target` varchar(120), `lang` varchar(30), `views` decimal(41,0));


DROP TABLE IF EXISTS `wddone`;
CREATE TABLE `wddone` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `mdtitle` varchar(120) NOT NULL,
  `target` varchar(120) NOT NULL,
  `lang` varchar(30) NOT NULL,
  `user` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `words`;
CREATE TABLE `words` (
  `w_id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `w_title` varchar(120) NOT NULL,
  `w_lead_words` int(6) DEFAULT NULL,
  `w_all_words` int(6) DEFAULT NULL,
  PRIMARY KEY (`w_id`),
  UNIQUE KEY `w_title` (`w_title`),
  KEY `idx_words_w_title` (`w_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `__all_articles_view`;
CREATE TABLE `__all_articles_view` (`article_id` varchar(255), `category` varchar(255), `qid` varchar(120));


DROP TABLE IF EXISTS `all_articles_titles`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `all_articles_titles` AS select `q`.`qid` AS `qid`,`aa`.`article_id` AS `title`,`aa`.`category` AS `category` from (`all_articles` `aa` left join `qids` `q` on(`aa`.`article_id` = `q`.`title`));

DROP TABLE IF EXISTS `all_qids_titles`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `all_qids_titles` AS select `qq`.`qid` AS `qid`,`q`.`title` AS `title`,`aa`.`category` AS `category` from ((`all_qids` `qq` left join `qids` `q` on(`qq`.`qid` = `q`.`qid`)) left join `all_articles` `aa` on(`aa`.`article_id` = `q`.`title`));

DROP TABLE IF EXISTS `titles_infos`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `titles_infos` AS select `ase`.`title` AS `title`,`ase`.`importance` AS `importance`,`rc`.`r_lead_refs` AS `r_lead_refs`,`rc`.`r_all_refs` AS `r_all_refs`,`ep`.`en_views` AS `en_views`,`w`.`w_lead_words` AS `w_lead_words`,`w`.`w_all_words` AS `w_all_words`,`q`.`qid` AS `qid` from ((((`assessments` `ase` left join `enwiki_pageviews` `ep` on(`ase`.`title` = `ep`.`title`)) left join `qids` `q` on(`q`.`title` = `ase`.`title`)) left join `refs_counts` `rc` on(`rc`.`r_title` = `ase`.`title`)) left join `words` `w` on(`w`.`w_title` = `ase`.`title`));

DROP TABLE IF EXISTS `views_new_all`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `views_new_all` AS select `v`.`target` AS `target`,`v`.`lang` AS `lang`,sum(`v`.`views`) AS `views` from `views_new` `v` group by `v`.`target`,`v`.`lang`;

DROP TABLE IF EXISTS `__all_articles_view`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `__all_articles_view` AS select distinct `a`.`article_id` AS `article_id`,`a`.`category` AS `category`,`q`.`qid` AS `qid` from (`all_articles` `a` join `qids` `q` on(`a`.`article_id` = `q`.`title`));

-- 2025-04-24 22:54:52 UTC