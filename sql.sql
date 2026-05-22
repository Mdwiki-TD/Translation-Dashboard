-- Adminer 5.3.0 MySQL 8.0.42 dump
SET
    NAMES utf8;

SET
    time_zone = '+00:00';

SET
    foreign_key_checks = 0;

SET
    sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `s54732__mdwikiz`;

SET
    NAMES utf8mb4;

CREATE TABLE
    `access_keys` (
        `id` int NOT NULL AUTO_INCREMENT,
        `user_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `user_name_hash` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `access_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `access_secret` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `user_name` (`user_name`),
        KEY `user_name_hash` (`user_name_hash`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `all_articles` (
        `id` int NOT NULL AUTO_INCREMENT,
        `article_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `article_id` (`article_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `all_qids_exists` (
        `id` int NOT NULL AUTO_INCREMENT,
        `qid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `code` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
        `target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `qid_code` (`qid`, `code`),
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `assessments` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `importance` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `title` (`title`),
        KEY `idx_assessments_title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `categories` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `category` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `category2` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `display` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `campaign` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `depth` int DEFAULT '0',
        `is_default` int NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `category` (`category`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `category_members` (
        `id` int NOT NULL AUTO_INCREMENT,
        `category` varchar(120) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `article_id` varchar(255) CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `category_article_id` (`category`, `article_id`),
            KEY `article_id` (`article_id`),
            CONSTRAINT `category_members_ibfk_1` FOREIGN KEY (`category`) REFERENCES `categories` (`category`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `coordinators` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `username` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `is_active` int NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `username` (`username`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `enwiki_pageviews` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `en_views` int DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `title` (`title`),
        KEY `idx_enwiki_pageviews_title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `full_translators` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `user` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `is_active` int NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `user` (`user`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `in_process` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `lang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
        `cat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'RTT',
        `translate_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'lead',
        `word` int DEFAULT '0',
        `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `langs` (
        `lang_id` int NOT NULL AUTO_INCREMENT,
        `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
        `autonym` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
        `name` varchar(70) COLLATE utf8mb4_unicode_ci NOT NULL,
        `redirects` longtext COLLATE utf8mb4_unicode_ci,
        PRIMARY KEY (`lang_id`),
        CONSTRAINT `langs_chk_1` CHECK (json_valid (`redirects`))
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `language_settings` (
        `id` int NOT NULL AUTO_INCREMENT,
        `lang_code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `move_dots` tinyint DEFAULT '0',
        `expend` tinyint DEFAULT '0',
        `add_en_lang` tinyint DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `lang_code` (`lang_code`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `logins` (
        `id` int NOT NULL AUTO_INCREMENT,
        `site` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `result` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
        `action` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `count` int NOT NULL DEFAULT '1',
        `first` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `last` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE KEY `site_username_result_action` (`site`, `username`, `result`, `action`),
        KEY `idx_site_user` (`site`, `username`),
        KEY `idx_timestamp` (`first`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `mdwiki_revids` (
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `revid` int NOT NULL,
        PRIMARY KEY (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `pages` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `word` int DEFAULT NULL,
        `translate_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `cat` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `lang` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `user` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `target` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `date` date DEFAULT NULL,
        `pupdate` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `deleted` int DEFAULT '0',
        `mdwiki_revid` int DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_title` (`title`),
        KEY `target` (`target`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `pages_users` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `word` int DEFAULT NULL,
        `translate_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `cat` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `lang` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `user` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `target` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `date` date DEFAULT NULL,
        `pupdate` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        `add_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `deleted` int DEFAULT '0',
        `mdwiki_revid` int DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_title` (`title`),
        KEY `target` (`target`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `pages_users_to_main` (
        `id` int unsigned NOT NULL,
        `new_target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `new_user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `new_qid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        KEY `id` (`id`),
        CONSTRAINT `pages_users_to_main_ibfk_1` FOREIGN KEY (`id`) REFERENCES `pages_users` (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `projects` (
        `g_id` int unsigned NOT NULL AUTO_INCREMENT,
        `g_title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        PRIMARY KEY (`g_id`),
        UNIQUE KEY `g_title` (`g_title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `publish_reports` (
        `id` int NOT NULL AUTO_INCREMENT,
        `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `user` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `sourcetitle` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `result` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
        PRIMARY KEY (`id`),
        CONSTRAINT `publish_reports_chk_1` CHECK (json_valid (`data`))
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `qids` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `qid` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `title_qid` (`title`, `qid`),
        KEY `idx_qids_title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `qids_others` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `qid` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `title_qid` (`title`, `qid`),
        KEY `idx_title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `refs_counts` (
        `r_id` int unsigned NOT NULL AUTO_INCREMENT,
        `r_title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `r_lead_refs` int DEFAULT NULL,
        `r_all_refs` int DEFAULT NULL,
        PRIMARY KEY (`r_id`),
        UNIQUE KEY `r_title` (`r_title`),
        KEY `idx_refs_counts_r_title` (`r_title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `settings` (
        `id` int NOT NULL AUTO_INCREMENT,
        `title` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
        `displayed` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
        `Type` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'check',
        `value` int NOT NULL DEFAULT '0',
        `ignored` int NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `title` (`title`),
        KEY `idx_title` (`title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `translate_type` (
        `tt_id` int unsigned NOT NULL AUTO_INCREMENT,
        `tt_title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `tt_lead` int NOT NULL DEFAULT '1',
        `tt_full` int NOT NULL DEFAULT '0',
        PRIMARY KEY (`tt_id`),
        UNIQUE KEY `tt_title` (`tt_title`),
        KEY `idx_tt_title` (`tt_title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `users` (
        `user_id` int NOT NULL AUTO_INCREMENT,
        `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
        `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `wiki` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `user_group` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Uncategorized',
        `reg_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`user_id`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `users_no_inprocess` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `user` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `is_active` int NOT NULL DEFAULT '1',
        PRIMARY KEY (`id`),
        UNIQUE KEY `user` (`user`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `views_new` (
        `id` int unsigned NOT NULL AUTO_INCREMENT,
        `target` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `lang` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
        `year` int NOT NULL,
        `views` int DEFAULT '0',
        PRIMARY KEY (`id`),
        UNIQUE KEY `target_lang_year` (`target`, `lang`, `year`),
        KEY `target` (`target`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE
    `views_new_all` (
        `target` varchar(120),
        `lang` varchar(30),
        `views` decimal(41, 0)
    );

CREATE TABLE
    `words` (
        `w_id` int unsigned NOT NULL AUTO_INCREMENT,
        `w_title` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
        `w_lead_words` int DEFAULT NULL,
        `w_all_words` int DEFAULT NULL,
        PRIMARY KEY (`w_id`),
        UNIQUE KEY `w_title` (`w_title`),
        KEY `idx_words_w_title` (`w_title`)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

DROP TABLE IF EXISTS views_new_all;

CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW views_new_all AS
select
    v.target AS target,
    v.lang AS lang,
    sumv.views AS views
from
    views_new v
group by
    v.target,
    v.lang;

-- 2026-05-21 02:23:09 UTC
