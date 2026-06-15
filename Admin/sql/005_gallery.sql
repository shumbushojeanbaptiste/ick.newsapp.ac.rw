-- ============================================================
--  UR KAMINUZA - Article image gallery.
--  Additional images per article (beyond the single featured image),
--  shown as a grid/slider on the public post page.
-- ============================================================
USE `KAMINUZA`;

CREATE TABLE IF NOT EXISTS `article_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `article_id` INT UNSIGNED NOT NULL,
  `path`       VARCHAR(255) NOT NULL,
  `caption`    VARCHAR(255) DEFAULT NULL,
  `sort_order` INT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_imgs_article` (`article_id`),
  KEY `idx_imgs_sort` (`article_id`, `sort_order`),
  CONSTRAINT `fk_imgs_article` FOREIGN KEY (`article_id`)
      REFERENCES `articles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
