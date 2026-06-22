USE `KAMINUZA`;
DROP PROCEDURE IF EXISTS `kz_add_article`;
DELIMITER //
CREATE PROCEDURE `kz_add_article`(IN p_slug VARCHAR(200), IN p_cat_slug VARCHAR(120), IN p_title VARCHAR(255), IN p_excerpt VARCHAR(500), IN p_body MEDIUMTEXT, IN p_image VARCHAR(255))
BEGIN
  DECLARE v_cat INT UNSIGNED DEFAULT NULL; DECLARE v_author INT UNSIGNED;
  SELECT id INTO v_cat FROM `categories` WHERE `slug`=p_cat_slug LIMIT 1;
  SELECT id INTO v_author FROM `users` ORDER BY id LIMIT 1;
  IF (SELECT COUNT(*) FROM `articles` WHERE `slug`=p_slug)=0 THEN
    INSERT INTO `articles`(category_id,author_id,slug,primary_lang,title_en,excerpt_en,body_en,featured_image,media_type,status,published_at)
    VALUES(v_cat,v_author,p_slug,'en',p_title,p_excerpt,p_body,p_image,'none','published',NOW());
  END IF;
END //
DELIMITER ;
SET @i1:='uploads/articles/sample-index_slider-image01.jpg';
SET @i2:='uploads/articles/sample-index_slider-image02.jpg';
SET @i3:='uploads/articles/sample-index_slider-image03.jpg';
SET @i4:='uploads/articles/sample-index_slider-image04.jpg';
SET @i5:='uploads/articles/sample-index_slider-image05.jpg';
SET @i6:='uploads/articles/sample-index_slider-image06.jpg';
CALL kz_add_article('sample-opp-1','opps-scholarships','Scholarship: Mastercard Foundation Scholars Program now open','Full scholarships available for undergraduate and postgraduate study.','<p>The Mastercard Foundation Scholars Program is accepting applications for full scholarships covering tuition, accommodation and living costs.</p>',@i1);
CALL kz_add_article('sample-opp-2','opps-grants','Grant: Research seed funding for early-career staff','Up to RWF 5M available for promising research proposals.','<p>Early-career academic staff can apply for research seed grants. Proposals are due at the end of the month.</p>',@i2);
CALL kz_add_article('sample-opp-3','opps-exchange','Exchange: Semester abroad in East Africa','Applications open for the regional student exchange programme.','<p>Spend a semester at a partner university in the region. Applications are now open.</p>',@i3);
CALL kz_add_article('sample-opp-4','career-internships','Internship: Summer placements with industry partners','Paid internships across engineering, ICT and business.','<p>A range of paid summer internships with industry partners is available to current students.</p>',@i4);
CALL kz_add_article('sample-opp-5','career-jobs','Job: Graduate trainee positions announced','Several graduate trainee roles open to recent alumni.','<p>Partner organisations have announced graduate trainee positions for recent graduates.</p>',@i5);
CALL kz_add_article('sample-opp-6','opps-competitions','Competition: National student innovation challenge','Form a team and pitch your idea for cash prizes.','<p>The national student innovation challenge is back, with cash prizes and mentorship for winning teams.</p>',@i6);
DROP PROCEDURE IF EXISTS `kz_add_article`;
