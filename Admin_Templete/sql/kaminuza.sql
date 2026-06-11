-- ============================================================
--  UR KAMINUZA  -  Database schema
--  Engine: MySQL / MariaDB  |  Charset: utf8mb4 (multilingual)
-- ============================================================

CREATE DATABASE IF NOT EXISTS `KAMINUZA`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `KAMINUZA`;

-- ------------------------------------------------------------
-- Roles  (RBAC)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `roles` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(50)  NOT NULL,                 -- machine name (admin, chief_editor, ...)
  `label`       VARCHAR(100) NOT NULL,                 -- human label
  `description` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_roles_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`name`, `label`, `description`) VALUES
  ('admin',        'Admin',        'Full access to everything'),
  ('chief_editor', 'Chief Editor', 'Publish, unpublish and delete any article'),
  ('reporter',     'Reporter',     'Create articles and submit own for review'),
  ('contributor',  'Contributor',  'Create drafts and submit own for review'),
  ('viewer',       'Viewer',       'Read-only access')
ON DUPLICATE KEY UPDATE `label` = VALUES(`label`);

-- ------------------------------------------------------------
-- Users
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_id`    INT UNSIGNED NOT NULL,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(150) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,                  -- password_hash()
  `avatar`     VARCHAR(255) DEFAULT NULL,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Default admin account  ->  email: admin@kaminuza.rw   password: Admin@123
-- (password hash generated with PHP password_hash, bcrypt)
INSERT INTO `users` (`role_id`, `name`, `email`, `password`)
SELECT r.id, 'Administrator', 'admin@kaminuza.rw',
       '$2y$10$3nLFYfb63xRLX3ANL.xX0OWTSzm2umVymXFpnI1wgtT.Ur8WJ/Riu'
FROM `roles` r WHERE r.name = 'admin'
ON DUPLICATE KEY UPDATE `users`.`name` = `users`.`name`;

-- ------------------------------------------------------------
-- Categories
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `categories` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(100) NOT NULL,
  `slug`       VARCHAR(120) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`name`, `slug`) VALUES
  ('Politics',    'politics'),
  ('Education',   'education'),
  ('Sports',      'sports'),
  ('Business',    'business'),
  ('Health',      'health'),
  ('Technology',  'technology'),
  ('Entertainment','entertainment'),
  ('Agriculture', 'agriculture'),
  ('Culture',     'culture')
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- ------------------------------------------------------------
-- Articles  (multilingual: rw / fr / en)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `articles` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`   INT UNSIGNED DEFAULT NULL,
  `author_id`     INT UNSIGNED NOT NULL,
  `slug`          VARCHAR(200) NOT NULL,

  -- Kinyarwanda
  `title_rw`      VARCHAR(255) DEFAULT NULL,
  `body_rw`       MEDIUMTEXT   DEFAULT NULL,
  -- French
  `title_fr`      VARCHAR(255) DEFAULT NULL,
  `body_fr`       MEDIUMTEXT   DEFAULT NULL,
  -- English
  `title_en`      VARCHAR(255) DEFAULT NULL,
  `body_en`       MEDIUMTEXT   DEFAULT NULL,

  `featured_image` VARCHAR(255) DEFAULT NULL,
  `radio_stream_id` INT UNSIGNED DEFAULT NULL,         -- optional embedded radio

  `status`        ENUM('draft','pending','published','unpublished')
                  NOT NULL DEFAULT 'draft',
  `published_at`  TIMESTAMP NULL DEFAULT NULL,
  `created_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_articles_slug` (`slug`),
  KEY `fk_articles_category` (`category_id`),
  KEY `fk_articles_author`   (`author_id`),
  KEY `idx_articles_status`  (`status`),
  CONSTRAINT `fk_articles_category` FOREIGN KEY (`category_id`)
      REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_articles_author` FOREIGN KEY (`author_id`)
      REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Related external links / references  (per article, many)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `article_links` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` INT UNSIGNED NOT NULL,
  `label`      VARCHAR(200) DEFAULT NULL,
  `url`        VARCHAR(500) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_links_article` (`article_id`),
  CONSTRAINT `fk_links_article` FOREIGN KEY (`article_id`)
      REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Radio streams  (e.g. Salus Radio)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `radio_streams` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `stream_url` VARCHAR(500) NOT NULL,
  `is_active`  TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `radio_streams` (`name`, `stream_url`) VALUES
  ('Salus Radio', 'https://salusradio.example/stream')
ON DUPLICATE KEY UPDATE `name` = `name`;

-- Link articles.radio_stream_id now that radio_streams exists
ALTER TABLE `articles`
  ADD CONSTRAINT `fk_articles_radio` FOREIGN KEY (`radio_stream_id`)
      REFERENCES `radio_streams` (`id`) ON DELETE SET NULL;
