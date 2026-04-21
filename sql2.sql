-- Adminer 5.3.0 MySQL 8.0.42 dump
-- https://mdwiki.toolforge.org/api.php?get=missing
-- https://mdwiki.toolforge.org/api.php?get=missing_by_qids
SET
    NAMES utf8;

SET
    time_zone = '+00:00';

SET
    foreign_key_checks = 0;

SET
    sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET
    NAMES utf8mb4;

CREATE TABLE all_articles (
        id int NOT NULL AUTO_INCREMENT,
        article_id varchar(255) NOT NULL,
        category varchar(255) DEFAULT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY article_id (article_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE all_exists (
        id int NOT NULL AUTO_INCREMENT,
        article_id varchar(255) NOT NULL,
        code varchar(25) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY article_id_code (article_id, code),
        CONSTRAINT all_exists_ibfk_1 FOREIGN KEY (article_id) REFERENCES all_articles (article_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE all_qids (
        qid varchar(255) NOT NULL,
        category varchar(255) DEFAULT NULL,
        id int NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (id),
        UNIQUE KEY qid (qid)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE all_qids_exists (
        id int NOT NULL AUTO_INCREMENT,
        qid varchar(255) NOT NULL,
        code varchar(25) NOT NULL,
        target varchar(255) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY qid_code (qid, code),
        CONSTRAINT all_qids_exists_ibfk_1 FOREIGN KEY (qid) REFERENCES all_qids (qid)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE publish_reports (
        id int NOT NULL AUTO_INCREMENT,
        date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
        title varchar(255) NOT NULL,
        user varchar(255) NOT NULL,
        lang varchar(255) NOT NULL,
        sourcetitle varchar(255) NOT NULL,
        result varchar(255) NOT NULL,
        data longtext CHARACTER
        SET
            utf8mb4 COLLATE utf8mb4_bin NOT NULL,
            PRIMARY KEY (id),
            CONSTRAINT publish_reports_chk_1 CHECK (json_valid (data))
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE VIEW all_articles_titles AS
select
    q.qid AS qid,
    aa.article_id AS title,
    aa.category AS category
from
    all_articles aa
    left join qids q on aa.article_id = q.title;

CREATE VIEW all_qids_titles AS
select
    qq.qid AS qid,
    q.title AS title,
    aa.category AS category
from
    all_qids qq
    left join qids q on qq.qid = q.qid
    left join all_articles aa on aa.article_id = q.title;

-- 2026-04-21 01:21:15 UTC
