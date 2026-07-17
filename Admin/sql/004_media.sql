-- ============================================================
--  UR KAMINUZA - Media support for articles.
--  An article can be plain text, OR carry a video/audio that is
--  either an embedded link (YouTube/Vimeo/SoundCloud/stream) or a
--  self-hosted uploaded file.
-- ============================================================
USE `kaminuza2`;

ALTER TABLE `articles`
  ADD COLUMN `media_type` ENUM('none','video','audio') NOT NULL DEFAULT 'none' AFTER `featured_image`,
  ADD COLUMN `media_url`  VARCHAR(500) DEFAULT NULL AFTER `media_type`,   -- embed link
  ADD COLUMN `media_file` VARCHAR(255) DEFAULT NULL AFTER `media_url`;    -- uploaded file path
