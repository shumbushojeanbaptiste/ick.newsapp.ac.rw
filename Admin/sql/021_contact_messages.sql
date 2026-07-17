-- ============================================================
--  UR KAMINUZA - Contact form messages.
--  Stores messages submitted from the public contact page so staff can read
--  them in the Admin later. Idempotent (CREATE TABLE IF NOT EXISTS).
-- ============================================================
USE `kaminuza2`;

CREATE TABLE IF NOT EXISTS `contact_messages` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(120) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `subject`    VARCHAR(200) DEFAULT NULL,
    `body`       TEXT NOT NULL,
    `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
    `ip`         VARBINARY(16) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_msg_read` (`is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
