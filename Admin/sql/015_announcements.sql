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
CALL kz_add_article('sample-ann-1','announce-university','University notice: revised semester calendar published','The academic office has released the updated calendar for the coming semester.','<p>The academic office has published the revised semester calendar. Students should review key dates.</p>',@i1);
CALL kz_add_article('sample-ann-2','announce-deadlines','Deadline: scholarship applications close Friday','Students are reminded to submit scholarship applications before the deadline.','<p>Scholarship applications close this Friday. Late submissions will not be accepted.</p>',@i2);
CALL kz_add_article('sample-ann-3','announce-events','Upcoming: open day for prospective students','The university will host an open day next month for prospective students and families.','<p>Join us for the annual open day featuring campus tours and faculty sessions.</p>',@i3);
CALL kz_add_article('sample-ann-4','announce-university','Library hours extended during exam period','Extended opening hours take effect from next week.','<p>The library will operate extended hours throughout the examination period.</p>',@i4);
CALL kz_add_article('sample-ann-5','announce-community','Community notice: campus health drive','A free health screening drive is open to all students and staff.','<p>The wellbeing centre is running a free health screening drive this month.</p>',@i5);
DROP PROCEDURE IF EXISTS `kz_add_article`;
