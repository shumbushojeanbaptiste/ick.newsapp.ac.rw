-- ============================================================
--  UR KAMINUZA - Additional sample articles for a fuller homepage.
--
--  ~3 realistic UR-flavoured articles per main section, filed under
--  leaf categories, so the homepage grids/sidebars show VARIED content
--  (not the same few stories repeated). Reuses the kz_add_article proc.
--
--  SAFE TO RE-RUN: inserts only if the slug doesn't already exist.
--  Clear all sample data with:  DELETE FROM articles WHERE slug LIKE 'sample-%';
-- ============================================================
USE `KAMINUZA`;

DROP PROCEDURE IF EXISTS `kz_add_article`;
DELIMITER //
CREATE PROCEDURE `kz_add_article`(
    IN p_slug       VARCHAR(200),
    IN p_cat_slug   VARCHAR(120),
    IN p_title      VARCHAR(255),
    IN p_excerpt    VARCHAR(500),
    IN p_body       MEDIUMTEXT,
    IN p_image      VARCHAR(255),
    IN p_media_type VARCHAR(10),
    IN p_media_url  VARCHAR(500),
    IN p_featured   TINYINT,
    IN p_breaking   TINYINT,
    IN p_pick       TINYINT
)
BEGIN
    DECLARE v_cat INT UNSIGNED DEFAULT NULL;
    DECLARE v_author INT UNSIGNED;
    SELECT id INTO v_cat FROM `categories` WHERE `slug` = p_cat_slug LIMIT 1;
    SELECT id INTO v_author FROM `users` ORDER BY id LIMIT 1;
    IF (SELECT COUNT(*) FROM `articles` WHERE `slug` = p_slug) = 0 THEN
        INSERT INTO `articles`
            (category_id, author_id, slug, primary_lang,
             title_en, excerpt_en, body_en, featured_image,
             media_type, media_url,
             status, published_at, is_featured, is_breaking, is_editor_pick)
        VALUES
            (v_cat, v_author, p_slug, 'en',
             p_title, p_excerpt, p_body, p_image,
             p_media_type, p_media_url,
             'published', NOW(), p_featured, p_breaking, p_pick);
    END IF;
END //
DELIMITER ;

-- Shorthand: image paths cycle through the bundled samples.
SET @i1 := 'uploads/articles/sample-index_slider-image01.jpg';
SET @i2 := 'uploads/articles/sample-index_slider-image02.jpg';
SET @i3 := 'uploads/articles/sample-index_slider-image03.jpg';
SET @i4 := 'uploads/articles/sample-index_slider-image04.jpg';
SET @i5 := 'uploads/articles/sample-index_slider-image05.jpg';
SET @i6 := 'uploads/articles/sample-index_slider-image06.jpg';
SET @i7 := 'uploads/articles/sample-index_slider-image07.jpg';
SET @i8 := 'uploads/articles/sample-index_slider-image08.jpg';
SET @ih := 'uploads/articles/sample-health-home_800x400-image01.jpg';
SET @ia := 'uploads/articles/sample-art-entertainment_slider-large-image01.jpg';
SET @iw := 'uploads/articles/sample-africa_slider-image01.jpg';

-- ---- NEWS / Local & International ----
CALL kz_add_article('sample-n1','news-local','Campus shuttle service expands to new routes','Students welcome longer hours and additional stops across the city.','<p>The campus shuttle service has added new routes and extended evening hours, easing commutes for thousands of students living off campus.</p>',@i1,'none',NULL,0,0,0);
CALL kz_add_article('sample-n2','news-local','New cafeteria opens at the science campus','Healthier menus and more seating greet students this semester.','<p>A newly built cafeteria has opened at the science campus, offering healthier meal options and significantly more seating during peak hours.</p>',@i2,'none',NULL,0,0,0);
CALL kz_add_article('sample-n3','news-international','UR joins global academic exchange network','The partnership opens new study-abroad options for students.','<p>The University of Rwanda has joined an international exchange network, expanding study-abroad and joint-degree opportunities for its students.</p>',@iw,'none',NULL,0,0,0);

-- ---- POLITICS ----
CALL kz_add_article('sample-p1','politics-governance','Student guild unveils new leadership structure','Reforms aim to give faculties stronger representation.','<p>The student guild has announced a restructured leadership model designed to give each faculty a clearer voice in campus decision-making.</p>',@i3,'none',NULL,0,0,0);
CALL kz_add_article('sample-p2','politics-policy','New attendance policy takes effect next term','Administration outlines the changes and the reasoning behind them.','<p>A revised attendance policy will take effect next term, with the administration emphasising flexibility for students balancing work and study.</p>',@i4,'none',NULL,0,0,0);
CALL kz_add_article('sample-p3','politics-elections','Record turnout expected for guild elections','Awareness campaigns have boosted student interest.','<p>Organisers expect record participation in the upcoming guild elections following a wave of student-led awareness campaigns.</p>',@i5,'none',NULL,0,1,0);

-- ---- SPORTS ----
CALL kz_add_article('sample-s1','sports-campus','Volleyball team clinches regional title','A dramatic final sealed the championship for UR.','<p>The university volleyball team claimed the regional title after a thrilling five-set final that kept fans on the edge of their seats.</p>',@i6,'none',NULL,0,0,1);
CALL kz_add_article('sample-s2','sports-results-fixtures','Weekend fixtures: football and basketball','Full schedule for this weekend''s inter-faculty matches.','<p>This weekend features a packed schedule of inter-faculty football and basketball matches across the main sports grounds.</p>',@i7,'none',NULL,0,0,0);
CALL kz_add_article('sample-s3','sports-entertainment-news','Campus marathon raises funds for charity','Hundreds of runners took part in the annual event.','<p>Hundreds of students and staff joined the annual campus marathon, raising funds for local community health projects.</p>',@i8,'none',NULL,0,0,0);

-- ---- HEALTH & LIFESTYLE ----
CALL kz_add_article('sample-h1','health-wellbeing','Free health screenings offered on campus','The wellbeing centre partners with a local hospital.','<p>The campus wellbeing centre is offering free health screenings this month in partnership with a local referral hospital.</p>',@ih,'none',NULL,0,0,0);
CALL kz_add_article('sample-h2','health-nutrition','Nutrition workshop helps students eat well on a budget','Practical tips for healthy, affordable meals.','<p>A popular nutrition workshop showed students how to prepare healthy, affordable meals using locally available ingredients.</p>',@i1,'none',NULL,0,0,0);
CALL kz_add_article('sample-h3','health-mental','Exam-season support: managing stress','Counsellors share strategies ahead of finals.','<p>As exam season approaches, campus counsellors are sharing practical strategies to help students manage stress and stay focused.</p>',@i2,'none',NULL,0,0,1);

-- ---- RESEARCH & INNOVATION ----
CALL kz_add_article('sample-r1','research-academic','UR study maps local water quality','Findings will guide community health interventions.','<p>A new University of Rwanda study has mapped water quality across several districts, providing data to guide community health interventions.</p>',@i3,'none',NULL,0,0,0);
CALL kz_add_article('sample-r2','research-innovation-hub','Student startup wins national pitch contest','Their app connects farmers directly to markets.','<p>A student-founded startup won a national pitch competition with an app that connects smallholder farmers directly to urban markets.</p>',@i4,'none',NULL,0,0,1);
CALL kz_add_article('sample-r3','research-student-projects','Engineering students build low-cost water filter','The prototype could help rural households.','<p>Engineering students have built a low-cost water filtration prototype aimed at improving access to clean water in rural households.</p>',@i5,'none',NULL,0,0,0);

-- ---- ENVIRONMENT ----
CALL kz_add_article('sample-e1','env-conservation','Campus wetland restoration project begins','Volunteers plant native species to revive the habitat.','<p>A wetland restoration project has begun on campus, with volunteers planting native species to revive the local habitat and biodiversity.</p>',@i6,'none',NULL,0,0,0);
CALL kz_add_article('sample-e2','env-policy','University adopts new waste-reduction policy','Single-use plastics to be phased out across campuses.','<p>The university has adopted a waste-reduction policy that will phase out single-use plastics across all campuses over the next year.</p>',@i7,'none',NULL,0,0,0);

-- ---- ARTS & CULTURE ----
CALL kz_add_article('sample-a1','arts-heritage','Exhibition celebrates Rwandan cultural heritage','Student curators showcase traditional crafts and art.','<p>A new exhibition curated by students celebrates Rwandan cultural heritage through traditional crafts, photography and storytelling.</p>',@ia,'none',NULL,0,0,0);
CALL kz_add_article('sample-a2','arts-literature','Poetry night gives student writers a stage','An evening of original work drew a full house.','<p>The campus poetry night gave student writers a stage to share original work, drawing a full and appreciative audience.</p>',@i8,'none',NULL,0,0,0);

-- ---- OPINION ----
CALL kz_add_article('sample-o1','opinion-columns','Column: The value of campus volunteering','Why giving time shapes both community and character.','<p>Volunteering on and off campus builds skills, strengthens community ties and shapes character in ways the classroom alone cannot.</p>',@i1,'none',NULL,0,0,0);
CALL kz_add_article('sample-o2','opinion-student-voices','Student voices: improving library access','A call for longer hours during exam periods.','<p>Students make the case for extended library hours during exam periods, arguing it would meaningfully improve study outcomes.</p>',@i2,'none',NULL,0,0,0);

-- ---- FEATURES ----
CALL kz_add_article('sample-f1','features-indepth','In depth: a day in the life of a lab researcher','Following the routine behind groundbreaking work.','<p>We follow a UR lab researcher through a full day, revealing the patience and teamwork behind groundbreaking scientific work.</p>',@i3,'none',NULL,0,0,1);
CALL kz_add_article('sample-f2','features-investigative','Investigation: where campus waste really goes','Tracing the journey of everyday rubbish.','<p>An investigative feature traces the journey of everyday campus waste, raising questions about recycling and accountability.</p>',@i4,'none',NULL,0,0,0);

-- ---- MULTIMEDIA ----
CALL kz_add_article('sample-m1','mm-video','Video: highlights from graduation day','Relive the best moments of this year''s ceremony.','<p>Relive the best moments from this year''s graduation ceremony in our highlights video.</p>',@i5,'video','https://vimeo.com/video/76979871',0,0,0);
CALL kz_add_article('sample-m2','mm-podcast','Podcast: students discuss campus life','The latest episode tackles study-life balance.','<p>In the latest podcast episode, students discuss study-life balance, wellbeing and making the most of campus life.</p>',@i6,'none',NULL,0,0,0);

DROP PROCEDURE IF EXISTS `kz_add_article`;
