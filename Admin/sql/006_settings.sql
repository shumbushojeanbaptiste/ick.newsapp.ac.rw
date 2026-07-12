-- ============================================================
--  UR KAMINUZA - Site settings (key/value store).
--  Holds company info, contact, social media links, logo, etc.
-- ============================================================
USE `kaminuza2`;

CREATE TABLE IF NOT EXISTS `settings` (
  `setting_key`   VARCHAR(60) NOT NULL,
  `setting_value` TEXT        DEFAULT NULL,
  `updated_at`    TIMESTAMP   NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed default keys (values can be edited from the admin Settings page).
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
  ('company_name',   'UR KAMINUZA'),
  ('tagline',        'University of Rwanda News'),
  ('about',          ''),
  ('contact_email',  'info@kaminuza.rw'),
  ('contact_phone',  ''),
  ('address',        ''),
  ('logo',           ''),
  ('facebook',       ''),
  ('twitter',        ''),
  ('instagram',      ''),
  ('youtube',        ''),
  ('linkedin',       '')
ON DUPLICATE KEY UPDATE `setting_key` = `setting_key`;
