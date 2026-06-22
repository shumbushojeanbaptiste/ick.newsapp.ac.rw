-- ============================================================
--  UR KAMINUZA - Category name translations (rw / fr).
--  Adds name_rw + name_fr so the public menu/section names display in the
--  visitor's chosen language. English stays in the existing `name` column.
--  Idempotent column adds + UPDATEs by slug.
-- ============================================================
USE `KAMINUZA`;

SET @has_rw := (SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema='KAMINUZA' AND table_name='categories' AND column_name='name_rw');
SET @sql := IF(@has_rw=0,
    'ALTER TABLE `categories`
        ADD COLUMN `name_rw` VARCHAR(100) DEFAULT NULL AFTER `name`,
        ADD COLUMN `name_fr` VARCHAR(100) DEFAULT NULL AFTER `name_rw`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Top-level sections (the main menu).
UPDATE categories SET name_rw='Amakuru',                 name_fr='Actualités'            WHERE slug='news';
UPDATE categories SET name_rw='Inyandiko zihariye',      name_fr='Reportages'            WHERE slug='features';
UPDATE categories SET name_rw='Ibitekerezo',             name_fr='Opinion'               WHERE slug='opinion';
UPDATE categories SET name_rw='Politiki',                name_fr='Politique'             WHERE slug='politics';
UPDATE categories SET name_rw='Ibidukikije',             name_fr='Environnement'         WHERE slug='environment';
UPDATE categories SET name_rw='Imibereho',               name_fr='Affaires sociales'     WHERE slug='social-affairs';
UPDATE categories SET name_rw='Ubushakashatsi',          name_fr='Recherche & Innovation' WHERE slug='research-innovation';
UPDATE categories SET name_rw='Ubuhanzi n’umuco',        name_fr='Arts & Culture'        WHERE slug='arts-culture';
UPDATE categories SET name_rw='Siporo n’imyidagaduro',   name_fr='Sports & Divertissement' WHERE slug='sports-entertainment';
UPDATE categories SET name_rw='Ubuzima n’imibereho',     name_fr='Santé & Mode de vie'   WHERE slug='health-lifestyle';
UPDATE categories SET name_rw='Akazi',                   name_fr='Carrières'             WHERE slug='career-hub';
UPDATE categories SET name_rw='Amahirwe',                name_fr='Opportunités'          WHERE slug='opportunities';
UPDATE categories SET name_rw='Amatangazo',              name_fr='Annonces'              WHERE slug='announcements';
UPDATE categories SET name_rw='Amashusho n’amajwi',      name_fr='Multimédia'            WHERE slug='multimedia';
UPDATE categories SET name_rw='Amafoto',                 name_fr='Photos'                WHERE slug='pictorial';

-- A selection of common sub-categories (news + politics + sports + health).
UPDATE categories SET name_rw='Mu gihugu',     name_fr='Local'           WHERE slug='news-local';
UPDATE categories SET name_rw='Mu karere',     name_fr='Régional'        WHERE slug='news-regional';
UPDATE categories SET name_rw='Mpuzamahanga',  name_fr='International'    WHERE slug='news-international';
UPDATE categories SET name_rw='Amatora',       name_fr='Élections'       WHERE slug='politics-elections';
UPDATE categories SET name_rw='Ubuyobozi',     name_fr='Gouvernance'     WHERE slug='politics-governance';
UPDATE categories SET name_rw='Siporo za kaminuza', name_fr='Sport universitaire' WHERE slug='sports-campus';
UPDATE categories SET name_rw='Ubuzima bwo mu mutwe', name_fr='Santé mentale' WHERE slug='health-mental';
UPDATE categories SET name_rw='Imirire n’imyitozo', name_fr='Nutrition & forme' WHERE slug='health-nutrition';
UPDATE categories SET name_rw='Podcast',       name_fr='Podcasts'        WHERE slug='mm-podcast';
UPDATE categories SET name_rw='Amashusho',     name_fr='Reportages vidéo' WHERE slug='mm-video';
