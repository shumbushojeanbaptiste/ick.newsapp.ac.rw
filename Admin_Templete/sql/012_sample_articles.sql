-- ============================================================
--  UR KAMINUZA - Sample published articles for the public homepage.
--
--  Realistic University-of-Rwanda-flavoured placeholder content, filed
--  under the website-skeleton categories, with homepage flags set so the
--  Breaking ticker / Featured slider / Editor's picks all have content.
--
--  SAFE TO RE-RUN: each row inserts only if its slug doesn't already exist.
--  To clear sample data later:  DELETE FROM articles WHERE slug LIKE 'sample-%';
-- ============================================================
USE `KAMINUZA`;

-- Helper: insert a published article by (slug, category slug, flags, en text).
DROP PROCEDURE IF EXISTS `kz_add_article`;
DELIMITER //
CREATE PROCEDURE `kz_add_article`(
    IN p_slug       VARCHAR(200),
    IN p_cat_slug   VARCHAR(120),
    IN p_title      VARCHAR(255),
    IN p_excerpt    VARCHAR(500),
    IN p_body       MEDIUMTEXT,
    IN p_image      VARCHAR(255),   -- featured image path (theme asset or upload)
    IN p_media_type VARCHAR(10),    -- 'none' | 'video' | 'audio'
    IN p_media_url  VARCHAR(500),   -- embed URL when media_type <> none
    IN p_featured   TINYINT,
    IN p_breaking   TINYINT,
    IN p_pick       TINYINT
)
BEGIN
    DECLARE v_cat INT UNSIGNED DEFAULT NULL;
    DECLARE v_author INT UNSIGNED;
    SELECT id INTO v_cat FROM `categories` WHERE `slug` = p_cat_slug LIMIT 1;
    SELECT id INTO v_author FROM `users` ORDER BY id LIMIT 1;   -- first admin/user
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

-- ---- FEATURED (slider) ----
CALL kz_add_article('sample-ur-graduation', 'news-local-ur',
  'University of Rwanda celebrates record graduating class',
  'Over 9,000 students received degrees at this year''s ceremony, the largest in UR history.',
  '<p>The University of Rwanda held its annual graduation ceremony this week, conferring degrees on a record number of students across its six colleges. Officials highlighted growth in postgraduate and research programmes.</p><p>The Vice-Chancellor congratulated graduates and urged them to contribute to national development.</p>',
  'uploads/articles/sample-index_slider-image01.jpg', 'none', NULL, 1, 0, 1);

CALL kz_add_article('sample-research-grant', 'research-academic',
  'UR researchers win continental grant for climate study',
  'A multi-year grant will fund research into climate-resilient agriculture across the region.',
  '<p>A team from the College of Agriculture has secured a competitive continental research grant to study climate-resilient crops. The project will run in partnership with several East African universities.</p>',
  'uploads/articles/sample-index_slider-image02.jpg', 'none', NULL, 1, 0, 0);

CALL kz_add_article('sample-innovation-week', 'research-tech-science',
  'Innovation Week showcases student-built solutions',
  'From mobile health apps to solar projects, students presented their work to industry partners.',
  '<p>UR''s annual Innovation Week brought together students, faculty and industry partners. Dozens of projects were exhibited, ranging from health technology to renewable energy.</p>',
  'uploads/articles/sample-index_slider-image03.jpg', 'video', 'https://vimeo.com/video/66388105', 1, 0, 1);

CALL kz_add_article('sample-library-upgrade', 'news-local',
  'Main library reopens after major renovation',
  'Modern study halls, faster Wi-Fi and a new digital archive welcome students back.',
  '<p>After months of renovation, the main university library has reopened with expanded study halls, upgraded internet and a new digital archive that makes thousands of journals available online.</p>',
  'uploads/articles/sample-index_slider-image04.jpg', 'none', NULL, 1, 0, 0);

CALL kz_add_article('sample-women-stem', 'research-tech-science',
  'Women in STEM initiative draws record applicants',
  'A mentorship programme connects female students with researchers and industry leaders.',
  '<p>The university''s Women in STEM initiative has attracted a record number of applicants this year, pairing female students with mentors across research and industry to close the gender gap in science.</p>',
  'uploads/articles/sample-index_slider-image05.jpg', 'none', NULL, 1, 0, 0);

CALL kz_add_article('sample-athletics-gold', 'sports-campus',
  'UR athletes bring home gold from national games',
  'The university team topped the medal table at this year''s inter-university championship.',
  '<p>University of Rwanda athletes delivered a standout performance at the national inter-university games, finishing top of the medal table with several gold medals in track and field.</p>',
  'uploads/articles/sample-index_slider-image06.jpg', 'none', NULL, 1, 0, 1);

CALL kz_add_article('sample-green-campus', 'env-green-campus',
  'Green campus drive plants 5,000 trees',
  'Students and staff joined a campus-wide effort to expand green spaces.',
  '<p>A green campus initiative saw students and staff plant over 5,000 trees this month, part of a broader sustainability drive to expand green spaces and cut the university''s carbon footprint.</p>',
  'uploads/articles/sample-index_slider-image07.jpg', 'none', NULL, 1, 0, 0);

CALL kz_add_article('sample-arts-festival', 'arts-music',
  'Annual arts festival lights up the campus',
  'Music, dance and visual art from across the colleges drew large crowds.',
  '<p>The annual campus arts festival brought together musicians, dancers and visual artists from across the colleges for a week of performances and exhibitions that drew large, enthusiastic crowds.</p>',
  'uploads/articles/sample-index_slider-image08.jpg', 'none', NULL, 1, 0, 0);

-- ---- BREAKING (ticker) ----
CALL kz_add_article('sample-senate-vote', 'politics-governance',
  'University senate approves new academic calendar',
  'The revised calendar takes effect next semester.',
  '<p>The university senate has approved a revised academic calendar following months of consultation. The changes aim to align teaching periods across campuses.</p>',
  'uploads/articles/sample-index_slider-image04.jpg', 'none', NULL, 0, 1, 0);

CALL kz_add_article('sample-elections-date', 'politics-elections',
  'Student guild elections set for next month',
  'Candidates can begin registering this week.',
  '<p>The student guild has announced dates for the upcoming elections. Registration of candidates opens this week and campaigns will follow.</p>',
  'uploads/articles/sample-index_slider-image05.jpg', 'none', NULL, 0, 1, 0);

CALL kz_add_article('sample-campus-derby', 'sports-campus',
  'Inter-college sports derby returns this weekend',
  'Teams from all colleges compete for the campus trophy.',
  '<p>The inter-college sports derby returns this weekend, with football, basketball and athletics events scheduled across the main campus grounds.</p>',
  'uploads/articles/sample-index_slider-image06.jpg', 'none', NULL, 0, 1, 0);

CALL kz_add_article('sample-climate-forum', 'env-climate',
  'Campus forum tackles climate action',
  'Students and experts debated practical steps for a greener campus.',
  '<p>A campus forum on climate action drew a large audience this week. Panellists discussed waste reduction, tree planting and energy efficiency initiatives.</p>',
  'uploads/articles/sample-index_slider-image07.jpg', 'none', NULL, 0, 1, 0);

-- ---- EDITOR'S PICKS + general category fill ----
CALL kz_add_article('sample-editorial-quality', 'opinion-editorials',
  'Editorial: Why student journalism matters',
  'A free and rigorous student press strengthens the whole university community.',
  '<p>Student journalism is more than practice for a future career. It holds institutions accountable, amplifies student voices and builds a culture of informed debate.</p>',
  'uploads/articles/sample-index_slider-image08.jpg', 'none', NULL, 0, 0, 1);

CALL kz_add_article('sample-mental-health', 'health-mental',
  'Supporting student mental health on campus',
  'New peer-support groups aim to reduce stigma and offer help.',
  '<p>Campus wellbeing services have launched new peer-support groups focused on student mental health, aiming to reduce stigma and connect students with resources.</p>',
  'uploads/articles/sample-health-home_800x400-image01.jpg', 'none', NULL, 0, 0, 1);

CALL kz_add_article('sample-music-night', 'arts-music',
  'Campus music night celebrates local talent',
  'Student performers filled the auditorium for an evening of live music.',
  '<p>The campus auditorium hosted a vibrant music night featuring student bands and soloists, celebrating the rich musical talent within the university.</p>',
  'uploads/articles/sample-art-entertainment_slider-large-image01.jpg', 'none', NULL, 0, 0, 0);

CALL kz_add_article('sample-regional-partnership', 'news-regional',
  'UR signs partnership with regional universities',
  'The agreement expands student exchange and joint research.',
  '<p>The University of Rwanda has signed a partnership agreement with several regional universities, expanding opportunities for student exchange and collaborative research.</p>',
  'uploads/articles/sample-africa_slider-image01.jpg', 'none', NULL, 0, 0, 0);

CALL kz_add_article('sample-local-library', 'news-local',
  'New study spaces open at the main library',
  'Extended hours and additional seating welcome students ahead of exams.',
  '<p>The main library has opened new study spaces with extended opening hours, providing additional quiet seating for students preparing for end-of-semester exams.</p>',
  'uploads/articles/sample-index_slider-image02.jpg', 'none', NULL, 0, 0, 0);

DROP PROCEDURE IF EXISTS `kz_add_article`;

-- Summary of what to expect on the homepage:
--   Featured slider : sample-ur-graduation, sample-research-grant, sample-innovation-week
--   Breaking ticker : sample-senate-vote, sample-elections-date, sample-campus-derby, sample-climate-forum
--   Editor's picks  : sample-ur-graduation, sample-innovation-week, sample-editorial-quality, sample-mental-health
