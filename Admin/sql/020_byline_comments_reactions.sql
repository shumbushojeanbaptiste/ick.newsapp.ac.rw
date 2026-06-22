-- ============================================================
--  UR KAMINUZA - Byline + reader comments + like/dislike reactions.
--   1. articles.byline  : optional custom credit ("By Jean / The Kaminuza Star")
--   2. article_comments  : reader comments, moderated (pending until approved)
--   3. article_reactions : one like/dislike per visitor token, counted instantly
--  Idempotent column add + CREATE TABLE IF NOT EXISTS.
-- ============================================================
USE `KAMINUZA`;

-- 1) Optional byline on articles (falls back to the author account name if empty).
SET @has_byline := (SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema='KAMINUZA' AND table_name='articles' AND column_name='byline');
SET @sql := IF(@has_byline=0,
    'ALTER TABLE `articles` ADD COLUMN `byline` VARCHAR(150) DEFAULT NULL AFTER `author_id`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2) Reader comments (moderated).
CREATE TABLE IF NOT EXISTS `article_comments` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id` INT UNSIGNED NOT NULL,
    `author_name` VARCHAR(100) NOT NULL,
    `author_email` VARCHAR(150) DEFAULT NULL,           -- private, admin only
    `body`       TEXT NOT NULL,
    `status`     ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
    `ip`         VARBINARY(16) DEFAULT NULL,             -- for abuse triage
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_comment_article` (`article_id`, `status`),
    CONSTRAINT `fk_comment_article` FOREIGN KEY (`article_id`)
        REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3) Like / dislike reactions. One row per visitor token per article (toggleable).
CREATE TABLE IF NOT EXISTS `article_reactions` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `article_id` INT UNSIGNED NOT NULL,
    `visitor`    CHAR(32) NOT NULL,                      -- random per-browser token
    `reaction`   ENUM('like','dislike') NOT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_reaction` (`article_id`, `visitor`),  -- one vote per visitor
    CONSTRAINT `fk_reaction_article` FOREIGN KEY (`article_id`)
        REFERENCES `articles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
