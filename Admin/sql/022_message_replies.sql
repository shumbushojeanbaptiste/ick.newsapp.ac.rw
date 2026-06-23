-- ============================================================
--  UR KAMINUZA - Contact message replies.
--  Records the reply text sent back to a sender, and when. Idempotent.
-- ============================================================
USE `KAMINUZA`;

SET @has := (SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema='KAMINUZA' AND table_name='contact_messages' AND column_name='reply_body');
SET @sql := IF(@has=0,
    'ALTER TABLE `contact_messages`
        ADD COLUMN `reply_body` TEXT DEFAULT NULL AFTER `body`,
        ADD COLUMN `replied_at` TIMESTAMP NULL DEFAULT NULL AFTER `reply_body`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
