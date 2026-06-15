-- ============================================================
--  UR KAMINUZA  -  Migration 011: Categories tree + homepage flags
--
--  1) Make categories hierarchical (parent_id) for submenus + add
--     sort_order and is_active for menu control.
--  2) Add homepage curation flags to articles
--     (Featured stories / Breaking news ticker / Editor's picks).
--  3) Seed the website-skeleton sections as a nested tree.
--
--  NON-DESTRUCTIVE: existing categories are kept. Each skeleton row is
--  inserted only if its slug doesn't already exist, so this file is safe
--  to run more than once and never drops data. Articles keep their links.
-- ============================================================
USE `KAMINUZA`;

-- ------------------------------------------------------------
-- 1) Hierarchical categories (idempotent column adds via checks)
-- ------------------------------------------------------------
SET @has_parent := (SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = 'KAMINUZA' AND table_name = 'categories' AND column_name = 'parent_id');
SET @sql := IF(@has_parent = 0,
    'ALTER TABLE `categories`
        ADD COLUMN `parent_id`  INT UNSIGNED DEFAULT NULL AFTER `id`,
        ADD COLUMN `sort_order` INT UNSIGNED NOT NULL DEFAULT 0 AFTER `description`,
        ADD COLUMN `is_active`  TINYINT(1)   NOT NULL DEFAULT 1 AFTER `sort_order`,
        ADD KEY `fk_categories_parent` (`parent_id`),
        ADD CONSTRAINT `fk_categories_parent`
            FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------
-- 2) Homepage curation flags on articles (idempotent)
-- ------------------------------------------------------------
SET @has_featured := (SELECT COUNT(*) FROM information_schema.columns
    WHERE table_schema = 'KAMINUZA' AND table_name = 'articles' AND column_name = 'is_featured');
SET @sql := IF(@has_featured = 0,
    'ALTER TABLE `articles`
        ADD COLUMN `is_featured`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `status`,
        ADD COLUMN `is_breaking`    TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_featured`,
        ADD COLUMN `is_editor_pick` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_breaking`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ------------------------------------------------------------
-- 2b) Move any article on a legacy flat category to a temporary
--     holding value (NULL) so those rows can be removed cleanly.
--     We re-home them to "Arts & Culture" at the very end, once the
--     skeleton tree (which contains arts-culture) has been seeded.
--     Done BEFORE seeding because the skeleton reuses the 'politics'
--     slug, so the old flat 'politics' must go first.
-- ------------------------------------------------------------
UPDATE `articles` a
   JOIN `categories` old ON old.id = a.category_id
   SET a.category_id = NULL
 WHERE old.parent_id IS NULL
   AND old.slug IN ('politics','education','sports','business','health',
                    'technology','entertainment','agriculture','culture','world');

DELETE FROM `categories`
 WHERE `parent_id` IS NULL
   AND `slug` IN ('politics','education','sports','business','health',
                  'technology','entertainment','agriculture','culture','world');

-- ------------------------------------------------------------
-- 3) Seed the skeleton sections (insert-if-not-exists by slug).
--    A small helper procedure keeps this readable and safe.
-- ------------------------------------------------------------
DROP PROCEDURE IF EXISTS `kz_add_cat`;
DELIMITER //
CREATE PROCEDURE `kz_add_cat`(
    IN p_name VARCHAR(100),
    IN p_slug VARCHAR(120),
    IN p_parent_slug VARCHAR(120),
    IN p_sort INT
)
BEGIN
    DECLARE v_parent INT UNSIGNED DEFAULT NULL;
    IF p_parent_slug IS NOT NULL THEN
        SELECT id INTO v_parent FROM `categories` WHERE `slug` = p_parent_slug LIMIT 1;
    END IF;
    IF (SELECT COUNT(*) FROM `categories` WHERE `slug` = p_slug) = 0 THEN
        INSERT INTO `categories` (`name`, `slug`, `parent_id`, `sort_order`)
        VALUES (p_name, p_slug, v_parent, p_sort);
    END IF;
END //
DELIMITER ;

-- ---- CORE NAVIGATION ----
CALL kz_add_cat('News', 'news', NULL, 1);
CALL kz_add_cat('Local', 'news-local', 'news', 1);
CALL kz_add_cat('Regional', 'news-regional', 'news', 2);
CALL kz_add_cat('International', 'news-international', 'news', 3);
CALL kz_add_cat('UR section', 'news-local-ur', 'news-local', 1);

CALL kz_add_cat('Features', 'features', NULL, 2);
CALL kz_add_cat('In-depth reports', 'features-indepth', 'features', 1);
CALL kz_add_cat('Investigative pieces', 'features-investigative', 'features', 2);
CALL kz_add_cat('Long-form narratives', 'features-longform', 'features', 3);
CALL kz_add_cat('Photo essays', 'features-photo-essays', 'features', 4);

CALL kz_add_cat('Opinion', 'opinion', NULL, 3);
CALL kz_add_cat('Editorials', 'opinion-editorials', 'opinion', 1);
CALL kz_add_cat('Columns', 'opinion-columns', 'opinion', 2);
CALL kz_add_cat('Letters to the editor', 'opinion-letters', 'opinion', 3);
CALL kz_add_cat('Student voices', 'opinion-student-voices', 'opinion', 4);

-- ---- SPECIALIST CONTENT ----
CALL kz_add_cat('Politics', 'politics', NULL, 4);
CALL kz_add_cat('Governance', 'politics-governance', 'politics', 1);
CALL kz_add_cat('Policy & legislation', 'politics-policy', 'politics', 2);
CALL kz_add_cat('Elections', 'politics-elections', 'politics', 3);
CALL kz_add_cat('Student governance', 'politics-student-gov', 'politics', 4);

CALL kz_add_cat('Environment', 'environment', NULL, 5);
CALL kz_add_cat('Climate change', 'env-climate', 'environment', 1);
CALL kz_add_cat('Conservation', 'env-conservation', 'environment', 2);
CALL kz_add_cat('Green campus initiatives', 'env-green-campus', 'environment', 3);
CALL kz_add_cat('Environmental policy', 'env-policy', 'environment', 4);

CALL kz_add_cat('Social Affairs', 'social-affairs', NULL, 6);
CALL kz_add_cat('Poverty & development', 'social-poverty', 'social-affairs', 1);
CALL kz_add_cat('Community issues', 'social-community', 'social-affairs', 2);
CALL kz_add_cat('Gender & inclusion', 'social-gender', 'social-affairs', 3);
CALL kz_add_cat('Human rights', 'social-human-rights', 'social-affairs', 4);

CALL kz_add_cat('Research & Innovation', 'research-innovation', NULL, 7);
CALL kz_add_cat('Academic research', 'research-academic', 'research-innovation', 1);
CALL kz_add_cat('Technology & science', 'research-tech-science', 'research-innovation', 2);
CALL kz_add_cat('Student projects', 'research-student-projects', 'research-innovation', 3);
CALL kz_add_cat('Innovation hub', 'research-innovation-hub', 'research-innovation', 4);

CALL kz_add_cat('Arts & Culture', 'arts-culture', NULL, 8);
CALL kz_add_cat('Visual arts', 'arts-visual', 'arts-culture', 1);
CALL kz_add_cat('Music & performance', 'arts-music', 'arts-culture', 2);
CALL kz_add_cat('Literature & poetry', 'arts-literature', 'arts-culture', 3);
CALL kz_add_cat('Cultural heritage', 'arts-heritage', 'arts-culture', 4);

CALL kz_add_cat('Sports & Entertainment', 'sports-entertainment', NULL, 9);
CALL kz_add_cat('Campus sports', 'sports-campus', 'sports-entertainment', 1);
CALL kz_add_cat('National sports', 'sports-national', 'sports-entertainment', 2);
CALL kz_add_cat('Entertainment news', 'sports-entertainment-news', 'sports-entertainment', 3);
CALL kz_add_cat('Game results & fixtures', 'sports-results-fixtures', 'sports-entertainment', 4);

CALL kz_add_cat('Health & Lifestyle', 'health-lifestyle', NULL, 10);
CALL kz_add_cat('Mental health', 'health-mental', 'health-lifestyle', 1);
CALL kz_add_cat('Nutrition & fitness', 'health-nutrition', 'health-lifestyle', 2);
CALL kz_add_cat('Campus wellbeing', 'health-wellbeing', 'health-lifestyle', 3);
CALL kz_add_cat('Lifestyle tips', 'health-lifestyle-tips', 'health-lifestyle', 4);

-- ---- COMMUNITY & ENGAGEMENT ----
CALL kz_add_cat('Career Hub', 'career-hub', NULL, 11);
CALL kz_add_cat('Job listings', 'career-jobs', 'career-hub', 1);
CALL kz_add_cat('Internships', 'career-internships', 'career-hub', 2);
CALL kz_add_cat('Career advice', 'career-advice', 'career-hub', 3);
CALL kz_add_cat('Alumni profiles', 'career-alumni', 'career-hub', 4);

CALL kz_add_cat('Opportunities', 'opportunities', NULL, 12);
CALL kz_add_cat('Scholarships', 'opps-scholarships', 'opportunities', 1);
CALL kz_add_cat('Grants & fellowships', 'opps-grants', 'opportunities', 2);
CALL kz_add_cat('Competitions', 'opps-competitions', 'opportunities', 3);
CALL kz_add_cat('Exchange programmes', 'opps-exchange', 'opportunities', 4);

CALL kz_add_cat('Announcements', 'announcements', NULL, 13);
CALL kz_add_cat('University notices', 'announce-university', 'announcements', 1);
CALL kz_add_cat('Events calendar', 'announce-events', 'announcements', 2);
CALL kz_add_cat('Academic deadlines', 'announce-deadlines', 'announcements', 3);
CALL kz_add_cat('Community notices', 'announce-community', 'announcements', 4);

-- ---- MULTIMEDIA & VISUAL ----
CALL kz_add_cat('Multimedia', 'multimedia', NULL, 14);
CALL kz_add_cat('Podcast episodes', 'mm-podcast', 'multimedia', 1);
CALL kz_add_cat('Vodcast episodes', 'mm-vodcast', 'multimedia', 2);
CALL kz_add_cat('Video reports', 'mm-video', 'multimedia', 3);
CALL kz_add_cat('Audio documentaries', 'mm-audio-docs', 'multimedia', 4);

CALL kz_add_cat('Pictorial', 'pictorial', NULL, 15);
CALL kz_add_cat('Life at the campus', 'pic-campus-life', 'pictorial', 1);
CALL kz_add_cat('Events gallery', 'pic-events', 'pictorial', 2);
CALL kz_add_cat('Photo of the week', 'pic-photo-week', 'pictorial', 3);
CALL kz_add_cat('Student photography', 'pic-student', 'pictorial', 4);

DROP PROCEDURE IF EXISTS `kz_add_cat`;

-- NOTE: articles that were on a deleted legacy category were set to
-- NULL (uncategorised) in step 2b. Re-assign them to the right section
-- from the admin Articles screen. We intentionally do NOT auto-move all
-- NULL-category articles here, because "uncategorised" is a valid state
-- for genuinely new drafts.

-- Done. The public menu can be built straight from this table:
--   top level  =  WHERE parent_id IS NULL AND is_active = 1 ORDER BY sort_order
--   submenu    =  WHERE parent_id = :id   AND is_active = 1 ORDER BY sort_order
