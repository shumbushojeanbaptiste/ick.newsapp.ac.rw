-- ============================================================
--  UR KAMINUZA - Fix double-encoded French/Kinyarwanda category names.
--  The 018/019 translation files were originally imported with the wrong
--  client charset, which double-encoded accents (é -> ├®, É -> ├ë, ' -> ÔÇÖ).
--  This re-applies the correct UTF-8 values.
--
--  IMPORTANT: run this file with the utf8mb4 client charset, e.g.
--      mysql -u <user> -p --default-character-set=utf8mb4 KAMINUZA < 023_fix_translation_encoding.sql
--  (Re-running 018 + 019 with that flag has the same effect; this file just
--   bundles the corrected values so a single import fixes production.)
-- ============================================================
USE `kaminuza2`;

-- Top-level sections
UPDATE categories SET name_fr='Actualités'               WHERE slug='news';
UPDATE categories SET name_fr='Reportages'               WHERE slug='features';
UPDATE categories SET name_fr='Opinion'                  WHERE slug='opinion';
UPDATE categories SET name_fr='Politique'                WHERE slug='politics';
UPDATE categories SET name_fr='Environnement'            WHERE slug='environment';
UPDATE categories SET name_fr='Affaires sociales'        WHERE slug='social-affairs';
UPDATE categories SET name_fr='Recherche & Innovation'   WHERE slug='research-innovation';
UPDATE categories SET name_fr='Arts & Culture'           WHERE slug='arts-culture';
UPDATE categories SET name_fr='Sports & Divertissement'  WHERE slug='sports-entertainment';
UPDATE categories SET name_fr='Santé & Mode de vie'      WHERE slug='health-lifestyle';
UPDATE categories SET name_fr='Carrières'                WHERE slug='career-hub';
UPDATE categories SET name_fr='Opportunités'             WHERE slug='opportunities';
UPDATE categories SET name_fr='Annonces'                 WHERE slug='announcements';
UPDATE categories SET name_fr='Multimédia'               WHERE slug='multimedia';
UPDATE categories SET name_fr='Photos'                   WHERE slug='pictorial';

-- News
UPDATE categories SET name_fr='Local'                    WHERE slug='news-local';
UPDATE categories SET name_fr='Régional'                 WHERE slug='news-regional';
UPDATE categories SET name_fr='International'            WHERE slug='news-international';
UPDATE categories SET name_fr='Section UR'               WHERE slug='news-local-ur';

-- Features
UPDATE categories SET name_fr='Reportages approfondis'   WHERE slug='features-indepth';
UPDATE categories SET name_fr='Enquêtes'                 WHERE slug='features-investigative';
UPDATE categories SET name_fr='Récits longs'             WHERE slug='features-longform';
UPDATE categories SET name_fr='Reportages photo'         WHERE slug='features-photo-essays';

-- Opinion
UPDATE categories SET name_fr='Chroniques'               WHERE slug='opinion-columns';
UPDATE categories SET name_fr='Éditoriaux'               WHERE slug='opinion-editorials';
UPDATE categories SET name_fr='Courrier des lecteurs'    WHERE slug='opinion-letters';
UPDATE categories SET name_fr='Voix étudiantes'          WHERE slug='opinion-student-voices';

-- Politics
UPDATE categories SET name_fr='Élections'                WHERE slug='politics-elections';
UPDATE categories SET name_fr='Gouvernance'              WHERE slug='politics-governance';
UPDATE categories SET name_fr='Politique & législation'  WHERE slug='politics-policy';
UPDATE categories SET name_fr='Gouvernance étudiante'    WHERE slug='politics-student-gov';

-- Environment
UPDATE categories SET name_fr='Changement climatique'    WHERE slug='env-climate';
UPDATE categories SET name_fr='Conservation'             WHERE slug='env-conservation';
UPDATE categories SET name_fr='Campus vert'              WHERE slug='env-green-campus';
UPDATE categories SET name_fr='Politique environnementale' WHERE slug='env-policy';

-- Social Affairs
UPDATE categories SET name_fr='Questions communautaires' WHERE slug='social-community';
UPDATE categories SET name_fr='Genre & inclusion'        WHERE slug='social-gender';
UPDATE categories SET name_fr='Droits humains'           WHERE slug='social-human-rights';
UPDATE categories SET name_fr='Pauvreté & développement' WHERE slug='social-poverty';

-- Research & Innovation
UPDATE categories SET name_fr='Recherche académique'     WHERE slug='research-academic';
UPDATE categories SET name_fr='Pôle d’innovation'        WHERE slug='research-innovation-hub';
UPDATE categories SET name_fr='Projets étudiants'        WHERE slug='research-student-projects';
UPDATE categories SET name_fr='Technologie & science'    WHERE slug='research-tech-science';

-- Arts & Culture
UPDATE categories SET name_fr='Patrimoine culturel'      WHERE slug='arts-heritage';
UPDATE categories SET name_fr='Littérature & poésie'     WHERE slug='arts-literature';
UPDATE categories SET name_fr='Musique & spectacle'      WHERE slug='arts-music';
UPDATE categories SET name_fr='Arts visuels'             WHERE slug='arts-visual';

-- Sports & Entertainment
UPDATE categories SET name_fr='Sport universitaire'      WHERE slug='sports-campus';
UPDATE categories SET name_fr='Divertissement'           WHERE slug='sports-entertainment-news';
UPDATE categories SET name_fr='Sport national'           WHERE slug='sports-national';
UPDATE categories SET name_fr='Résultats & calendrier'   WHERE slug='sports-results-fixtures';

-- Health & Lifestyle
UPDATE categories SET name_fr='Conseils de vie'          WHERE slug='health-lifestyle-tips';
UPDATE categories SET name_fr='Santé mentale'            WHERE slug='health-mental';
UPDATE categories SET name_fr='Nutrition & forme'        WHERE slug='health-nutrition';
UPDATE categories SET name_fr='Bien-être sur le campus'  WHERE slug='health-wellbeing';

-- Career Hub
UPDATE categories SET name_fr='Conseils de carrière'     WHERE slug='career-advice';
UPDATE categories SET name_fr='Profils d’anciens'        WHERE slug='career-alumni';
UPDATE categories SET name_fr='Stages'                   WHERE slug='career-internships';
UPDATE categories SET name_fr='Offres d’emploi'          WHERE slug='career-jobs';

-- Opportunities
UPDATE categories SET name_fr='Concours'                 WHERE slug='opps-competitions';
UPDATE categories SET name_fr='Programmes d’échange'     WHERE slug='opps-exchange';
UPDATE categories SET name_fr='Bourses & subventions'    WHERE slug='opps-grants';
UPDATE categories SET name_fr='Bourses d’études'         WHERE slug='opps-scholarships';

-- Announcements
UPDATE categories SET name_fr='Avis communautaires'      WHERE slug='announce-community';
UPDATE categories SET name_fr='Échéances académiques'    WHERE slug='announce-deadlines';
UPDATE categories SET name_fr='Calendrier des événements' WHERE slug='announce-events';
UPDATE categories SET name_fr='Avis de l’université'     WHERE slug='announce-university';

-- Multimedia
UPDATE categories SET name_fr='Documentaires audio'      WHERE slug='mm-audio-docs';
UPDATE categories SET name_fr='Podcasts'                 WHERE slug='mm-podcast';
UPDATE categories SET name_fr='Reportages vidéo'         WHERE slug='mm-video';
UPDATE categories SET name_fr='Épisodes vodcast'         WHERE slug='mm-vodcast';

-- Pictorial
UPDATE categories SET name_fr='La vie sur le campus'     WHERE slug='pic-campus-life';
UPDATE categories SET name_fr='Galerie d’événements'     WHERE slug='pic-events';
UPDATE categories SET name_fr='Photo de la semaine'      WHERE slug='pic-photo-week';
UPDATE categories SET name_fr='Photographie étudiante'   WHERE slug='pic-student';

-- Kinyarwanda names that use the ’ apostrophe (were also affected)
UPDATE categories SET name_rw='Ubuhanzi n’umuco'         WHERE slug='arts-culture';
UPDATE categories SET name_rw='Siporo n’imyidagaduro'    WHERE slug='sports-entertainment';
UPDATE categories SET name_rw='Ubuzima n’imibereho'      WHERE slug='health-lifestyle';
UPDATE categories SET name_rw='Amashusho n’amajwi'       WHERE slug='multimedia';
UPDATE categories SET name_rw='Imirire n’imyitozo'       WHERE slug='health-nutrition';
