-- Adminer 5.0.5 MySQL 5.5.5-10.6.20-MariaDB-log dump
-- https://mdwiki.toolforge.org/api.php?get=missing
-- https://mdwiki.toolforge.org/api.php?get=missing_by_qids

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `qids`;
-- CREATE VIEW mdwiki_new.qids1 AS SELECT * FROM mdwiki.qids;

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


DROP TABLE IF EXISTS `all_qids_exists`;
CREATE TABLE `all_qids_exists` (
  `qid` varchar(255) NOT NULL,
  `code` varchar(25) NOT NULL,
  PRIMARY KEY (`qid`,`code`),
  UNIQUE KEY `qid_code` (`qid`,`code`),
  CONSTRAINT `all_qids_exists_ibfk_1` FOREIGN KEY (`qid`) REFERENCES `all_qids` (`qid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP VIEW IF EXISTS `all_qids_titles`;
CREATE TABLE `all_qids_titles` (`qid` varchar(255), `title` varchar(120), `category` varchar(255));


DROP TABLE IF EXISTS `all_articles_titles`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `all_articles_titles` AS select `q`.`qid` AS `qid`,`aa`.`article_id` AS `title`,`aa`.`category` AS `category` from (`all_articles` `aa` left join `qids` `q` on(`aa`.`article_id` = `q`.`title`));

DROP TABLE IF EXISTS `all_qids_titles`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `all_qids_titles` AS select `qq`.`qid` AS `qid`,`q`.`title` AS `title`,`aa`.`category` AS `category` from ((`all_qids` `qq` left join `qids` `q` on(`qq`.`qid` = `q`.`qid`)) left join `all_articles` `aa` on(`aa`.`article_id` = `q`.`title`));

-- 2025-04-25 22:12:51 UTC
