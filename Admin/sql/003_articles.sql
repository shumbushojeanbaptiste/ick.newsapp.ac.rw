-- ============================================================
--  UR KAMINUZA - Article additions for the hybrid multilingual model.
--  - primary_lang : the article's main language (used when filtering
--                   "show only French/English/Kinyarwanda").
--  - excerpt_*    : optional short summary per language for cards.
-- ============================================================
USE `kaminuza2`;

-- Primary language of the article (rw / fr / en).
ALTER TABLE `articles`
  ADD COLUMN `primary_lang` ENUM('rw','fr','en') NOT NULL DEFAULT 'en' AFTER `slug`;

-- Optional short summaries shown on listing cards.
ALTER TABLE `articles`
  ADD COLUMN `excerpt_rw` VARCHAR(500) DEFAULT NULL AFTER `body_rw`,
  ADD COLUMN `excerpt_fr` VARCHAR(500) DEFAULT NULL AFTER `body_fr`,
  ADD COLUMN `excerpt_en` VARCHAR(500) DEFAULT NULL AFTER `body_en`;
