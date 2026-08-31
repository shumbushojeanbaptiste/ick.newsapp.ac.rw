-- ---------------------------------------------------------------------------
-- Rename the station to "ICK Radio".
-- ICK is replacing the third-party station (Radio Maria / Salus Radio) with its
-- own online radio, which is announced on the site but not broadcasting yet.
-- Safe to run more than once.
-- ---------------------------------------------------------------------------

UPDATE radio_streams
   SET name = 'ICK Radio'
 WHERE name IN ('Radio Maria', 'Salus Radio', 'Radio Salus');

-- The site shows the station name from Settings first (falling back to the
-- first active stream, then to "ICK Radio"), so pin it explicitly here too.
INSERT INTO settings (setting_key, setting_value)
VALUES ('radio_name', 'ICK Radio')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);
