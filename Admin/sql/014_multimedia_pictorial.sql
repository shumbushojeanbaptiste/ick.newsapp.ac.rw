-- ============================================================
--  UR KAMINUZA - Sample Multimedia & Pictorial articles.
--  Fills the homepage gallery carousel (podcasts, vodcasts, video
--  reports, audio docs, photo essays) so it shows on-topic content.
--  Re-runnable; clear with: DELETE FROM articles WHERE slug LIKE 'sample-%';
-- ============================================================
USE `KAMINUZA`;

DROP PROCEDURE IF EXISTS `kz_add_article`;
DELIMITER //
CREATE PROCEDURE `kz_add_article`(
    IN p_slug VARCHAR(200), IN p_cat_slug VARCHAR(120), IN p_title VARCHAR(255),
    IN p_excerpt VARCHAR(500), IN p_body MEDIUMTEXT, IN p_image VARCHAR(255),
    IN p_media_type VARCHAR(10), IN p_media_url VARCHAR(500),
    IN p_featured TINYINT, IN p_breaking TINYINT, IN p_pick TINYINT)
BEGIN
    DECLARE v_cat INT UNSIGNED DEFAULT NULL;
    DECLARE v_author INT UNSIGNED;
    SELECT id INTO v_cat FROM `categories` WHERE `slug` = p_cat_slug LIMIT 1;
    SELECT id INTO v_author FROM `users` ORDER BY id LIMIT 1;
    IF (SELECT COUNT(*) FROM `articles` WHERE `slug` = p_slug) = 0 THEN
        INSERT INTO `articles`
            (category_id, author_id, slug, primary_lang, title_en, excerpt_en, body_en,
             featured_image, media_type, media_url, status, published_at,
             is_featured, is_breaking, is_editor_pick)
        VALUES
            (v_cat, v_author, p_slug, 'en', p_title, p_excerpt, p_body,
             p_image, p_media_type, p_media_url, 'published', NOW(),
             p_featured, p_breaking, p_pick);
    END IF;
END //
DELIMITER ;

SET @i1 := 'uploads/articles/sample-index_slider-image01.jpg';
SET @i2 := 'uploads/articles/sample-index_slider-image02.jpg';
SET @i3 := 'uploads/articles/sample-index_slider-image03.jpg';
SET @i4 := 'uploads/articles/sample-index_slider-image04.jpg';
SET @i5 := 'uploads/articles/sample-index_slider-image05.jpg';
SET @i6 := 'uploads/articles/sample-index_slider-image06.jpg';
SET @i7 := 'uploads/articles/sample-index_slider-image07.jpg';
SET @i8 := 'uploads/articles/sample-index_slider-image08.jpg';
SET @ia := 'uploads/articles/sample-art-entertainment_slider-large-image01.jpg';

-- ---- VIDEO REPORTS (mm-video) ----
CALL kz_add_article('sample-mm-v1','mm-video','Video: Inside the new science library','A walkthrough of the renovated library and its new digital archive.','<p>Take a tour of the newly renovated science library in this video report.</p>',@i1,'video','https://vimeo.com/video/76979871',0,0,1);
CALL kz_add_article('sample-mm-v2','mm-video','Video report: Campus innovation showcase','Students demonstrate their projects at the annual innovation showcase.','<p>Highlights from the annual student innovation showcase.</p>',@i3,'video','https://www.youtube.com/watch?v=scbeQEKWLPY',0,0,0);
CALL kz_add_article('sample-mm-v3','mm-video','Video: Graduation day in pictures and motion','Relive the emotion of graduation day.','<p>A cinematic recap of this year''s graduation ceremony.</p>',@i5,'video','https://vimeo.com/video/66388105',0,0,0);

-- ---- VODCAST EPISODES (mm-vodcast) ----
CALL kz_add_article('sample-mm-vo1','mm-vodcast','Vodcast: Student leaders in conversation','This week''s vodcast sits down with the new guild leadership.','<p>Watch this week''s vodcast episode with student leaders.</p>',@i4,'video','https://vimeo.com/video/97744717',0,0,0);
CALL kz_add_article('sample-mm-vo2','mm-vodcast','Vodcast: Research that matters','Researchers explain their work in plain language.','<p>An accessible vodcast on UR research projects.</p>',@i6,'video','https://vimeo.com/video/76979871',0,0,0);

-- ---- PODCAST EPISODES (mm-podcast) ----
CALL kz_add_article('sample-mm-p1','mm-podcast','Podcast: Balancing study and wellbeing','Students and counsellors discuss staying healthy during exams.','<p>Listen to this episode on study-life balance.</p>',@i2,'none',NULL,0,0,0);
CALL kz_add_article('sample-mm-p2','mm-podcast','Podcast: Voices from campus','A round-up of student opinions on campus life.','<p>This episode features a range of student voices.</p>',@i7,'none',NULL,0,0,0);

-- ---- AUDIO DOCUMENTARIES (mm-audio-docs) ----
CALL kz_add_article('sample-mm-a1','mm-audio-docs','Audio doc: The history of our campus','An audio journey through the university''s history.','<p>An audio documentary tracing the university''s story.</p>',@i8,'none',NULL,0,0,0);

-- ---- PHOTO ESSAYS / PICTORIAL ----
CALL kz_add_article('sample-pic-1','features-photo-essays','Photo essay: A day on campus','In pictures: from sunrise lectures to evening study.','<p>A photo essay capturing a full day of campus life.</p>',@ia,'none',NULL,0,0,1);
CALL kz_add_article('sample-pic-2','pic-campus-life','Life at the campus: green spaces','Photographs of the university''s gardens and green areas.','<p>A pictorial of campus green spaces.</p>',@i1,'none',NULL,0,0,0);
CALL kz_add_article('sample-pic-3','pic-events','Events gallery: graduation 2026','The best photographs from graduation day.','<p>Photo gallery from the 2026 graduation ceremony.</p>',@i5,'none',NULL,0,0,0);
CALL kz_add_article('sample-pic-4','pic-photo-week','Photo of the week: morning mist over campus','This week''s standout student photograph.','<p>Our photo of the week, by a student photographer.</p>',@i3,'none',NULL,0,0,0);

DROP PROCEDURE IF EXISTS `kz_add_article`;
