-- ============================================================
--  UR KAMINUZA - FULL category name translations (rw / fr).
--  Completes 018 by translating EVERY active sub-category, so no badge or
--  menu item falls back to English on the rw/fr versions of the site.
--  Idempotent: UPDATE by slug.
-- ============================================================
USE `kaminuza2`;

-- Top-level (re-affirm from 018)
UPDATE categories SET name_rw='Amakuru',                  name_fr='Actualités'              WHERE slug='news';
UPDATE categories SET name_rw='Inyandiko zihariye',       name_fr='Reportages'             WHERE slug='features';
UPDATE categories SET name_rw='Ibitekerezo',             name_fr='Opinion'                WHERE slug='opinion';
UPDATE categories SET name_rw='Politiki',                name_fr='Politique'              WHERE slug='politics';
UPDATE categories SET name_rw='Ibidukikije',             name_fr='Environnement'          WHERE slug='environment';
UPDATE categories SET name_rw='Imibereho',               name_fr='Affaires sociales'      WHERE slug='social-affairs';
UPDATE categories SET name_rw='Ubushakashatsi',          name_fr='Recherche & Innovation' WHERE slug='research-innovation';
UPDATE categories SET name_rw='Ubuhanzi n’umuco',        name_fr='Arts & Culture'         WHERE slug='arts-culture';
UPDATE categories SET name_rw='Siporo n’imyidagaduro',   name_fr='Sports & Divertissement' WHERE slug='sports-entertainment';
UPDATE categories SET name_rw='Ubuzima n’imibereho',     name_fr='Santé & Mode de vie'    WHERE slug='health-lifestyle';
UPDATE categories SET name_rw='Akazi',                   name_fr='Carrières'              WHERE slug='career-hub';
UPDATE categories SET name_rw='Amahirwe',                name_fr='Opportunités'           WHERE slug='opportunities';
UPDATE categories SET name_rw='Amatangazo',              name_fr='Annonces'               WHERE slug='announcements';
UPDATE categories SET name_rw='Amashusho n’amajwi',      name_fr='Multimédia'             WHERE slug='multimedia';
UPDATE categories SET name_rw='Amafoto',                 name_fr='Photos'                 WHERE slug='pictorial';
UPDATE categories SET name_rw='Amazone',                 name_fr='Amazone'                WHERE slug='amazone';

-- News
UPDATE categories SET name_rw='Mu gihugu',               name_fr='Local'                  WHERE slug='news-local';
UPDATE categories SET name_rw='Mu karere',               name_fr='Régional'               WHERE slug='news-regional';
UPDATE categories SET name_rw='Mpuzamahanga',            name_fr='International'           WHERE slug='news-international';
UPDATE categories SET name_rw='Igice cya UR',            name_fr='Section UR'             WHERE slug='news-local-ur';

-- Features
UPDATE categories SET name_rw='Raporo zimbitse',         name_fr='Reportages approfondis' WHERE slug='features-indepth';
UPDATE categories SET name_rw='Iperereza',               name_fr='Enquêtes'               WHERE slug='features-investigative';
UPDATE categories SET name_rw='Inkuru ndende',           name_fr='Récits longs'           WHERE slug='features-longform';
UPDATE categories SET name_rw='Inkuru z’amafoto',        name_fr='Reportages photo'       WHERE slug='features-photo-essays';

-- Opinion
UPDATE categories SET name_rw='Inkingi',                 name_fr='Chroniques'             WHERE slug='opinion-columns';
UPDATE categories SET name_rw='Ibitekerezo by’ikinyamakuru', name_fr='Éditoriaux'         WHERE slug='opinion-editorials';
UPDATE categories SET name_rw='Amabaruwa ku muyobozi',   name_fr='Courrier des lecteurs'  WHERE slug='opinion-letters';
UPDATE categories SET name_rw='Amajwi y’abanyeshuri',    name_fr='Voix étudiantes'        WHERE slug='opinion-student-voices';

-- Politics
UPDATE categories SET name_rw='Amatora',                 name_fr='Élections'              WHERE slug='politics-elections';
UPDATE categories SET name_rw='Ubuyobozi',              name_fr='Gouvernance'            WHERE slug='politics-governance';
UPDATE categories SET name_rw='Politiki n’amategeko',    name_fr='Politique & législation' WHERE slug='politics-policy';
UPDATE categories SET name_rw='Ubuyobozi bw’abanyeshuri', name_fr='Gouvernance étudiante' WHERE slug='politics-student-gov';

-- Environment
UPDATE categories SET name_rw='Imihindagurikire y’ikirere', name_fr='Changement climatique' WHERE slug='env-climate';
UPDATE categories SET name_rw='Kubungabunga ibidukikije', name_fr='Conservation'          WHERE slug='env-conservation';
UPDATE categories SET name_rw='Kaminuza icyatsi',       name_fr='Campus vert'            WHERE slug='env-green-campus';
UPDATE categories SET name_rw='Politiki y’ibidukikije',  name_fr='Politique environnementale' WHERE slug='env-policy';

-- Social Affairs
UPDATE categories SET name_rw='Ibibazo by’abaturage',    name_fr='Questions communautaires' WHERE slug='social-community';
UPDATE categories SET name_rw='Uburinganire',           name_fr='Genre & inclusion'      WHERE slug='social-gender';
UPDATE categories SET name_rw='Uburenganzira bwa muntu', name_fr='Droits humains'         WHERE slug='social-human-rights';
UPDATE categories SET name_rw='Ubukene n’iterambere',    name_fr='Pauvreté & développement' WHERE slug='social-poverty';

-- Research & Innovation
UPDATE categories SET name_rw='Ubushakashatsi bw’amasomo', name_fr='Recherche académique' WHERE slug='research-academic';
UPDATE categories SET name_rw='Icyumba cy’udushya',     name_fr='Pôle d’innovation'      WHERE slug='research-innovation-hub';
UPDATE categories SET name_rw='Imishinga y’abanyeshuri', name_fr='Projets étudiants'      WHERE slug='research-student-projects';
UPDATE categories SET name_rw='Ikoranabuhanga n’ubumenyi', name_fr='Technologie & science' WHERE slug='research-tech-science';

-- Arts & Culture
UPDATE categories SET name_rw='Umurage ndangamuco',     name_fr='Patrimoine culturel'    WHERE slug='arts-heritage';
UPDATE categories SET name_rw='Ubuvanganzo n’ivanga',    name_fr='Littérature & poésie'   WHERE slug='arts-literature';
UPDATE categories SET name_rw='Umuziki n’imikino ngaragu', name_fr='Musique & spectacle'  WHERE slug='arts-music';
UPDATE categories SET name_rw='Ubuhanzi bugaragara',    name_fr='Arts visuels'           WHERE slug='arts-visual';

-- Sports & Entertainment
UPDATE categories SET name_rw='Siporo za kaminuza',     name_fr='Sport universitaire'    WHERE slug='sports-campus';
UPDATE categories SET name_rw='Amakuru y’imyidagaduro',  name_fr='Divertissement'         WHERE slug='sports-entertainment-news';
UPDATE categories SET name_rw='Siporo z’igihugu',        name_fr='Sport national'         WHERE slug='sports-national';
UPDATE categories SET name_rw='Ibyavuye mu mikino',     name_fr='Résultats & calendrier' WHERE slug='sports-results-fixtures';

-- Health & Lifestyle
UPDATE categories SET name_rw='Inama z’imibereho',       name_fr='Conseils de vie'        WHERE slug='health-lifestyle-tips';
UPDATE categories SET name_rw='Ubuzima bwo mu mutwe',    name_fr='Santé mentale'          WHERE slug='health-mental';
UPDATE categories SET name_rw='Imirire n’imyitozo',      name_fr='Nutrition & forme'      WHERE slug='health-nutrition';
UPDATE categories SET name_rw='Ubuzima muri kaminuza',   name_fr='Bien-être sur le campus' WHERE slug='health-wellbeing';

-- Career Hub
UPDATE categories SET name_rw='Inama z’umwuga',          name_fr='Conseils de carrière'   WHERE slug='career-advice';
UPDATE categories SET name_rw='Abahoze ari abanyeshuri', name_fr='Profils d’anciens'      WHERE slug='career-alumni';
UPDATE categories SET name_rw='Imyitozo y’akazi',        name_fr='Stages'                 WHERE slug='career-internships';
UPDATE categories SET name_rw='Akazi gatangwa',          name_fr='Offres d’emploi'        WHERE slug='career-jobs';

-- Opportunities
UPDATE categories SET name_rw='Amarushanwa',            name_fr='Concours'               WHERE slug='opps-competitions';
UPDATE categories SET name_rw='Gahunda z’ihererekanya',  name_fr='Programmes d’échange'   WHERE slug='opps-exchange';
UPDATE categories SET name_rw='Inkunga n’ubufatanye',    name_fr='Bourses & subventions'  WHERE slug='opps-grants';
UPDATE categories SET name_rw='Buruse',                  name_fr='Bourses d’études'       WHERE slug='opps-scholarships';

-- Announcements
UPDATE categories SET name_rw='Amatangazo y’abaturage',  name_fr='Avis communautaires'    WHERE slug='announce-community';
UPDATE categories SET name_rw='Ibihe ntarengwa by’amasomo', name_fr='Échéances académiques' WHERE slug='announce-deadlines';
UPDATE categories SET name_rw='Kalindari y’ibikorwa',    name_fr='Calendrier des événements' WHERE slug='announce-events';
UPDATE categories SET name_rw='Amatangazo ya kaminuza',  name_fr='Avis de l’université'   WHERE slug='announce-university';

-- Multimedia
UPDATE categories SET name_rw='Inyandiko z’amajwi',      name_fr='Documentaires audio'    WHERE slug='mm-audio-docs';
UPDATE categories SET name_rw='Podcast',                name_fr='Podcasts'               WHERE slug='mm-podcast';
UPDATE categories SET name_rw='Amashusho',              name_fr='Reportages vidéo'       WHERE slug='mm-video';
UPDATE categories SET name_rw='Vodcast',                name_fr='Épisodes vodcast'       WHERE slug='mm-vodcast';

-- Pictorial
UPDATE categories SET name_rw='Ubuzima muri kaminuza',   name_fr='La vie sur le campus'   WHERE slug='pic-campus-life';
UPDATE categories SET name_rw='Amafoto y’ibikorwa',      name_fr='Galerie d’événements'   WHERE slug='pic-events';
UPDATE categories SET name_rw='Ifoto y’icyumweru',       name_fr='Photo de la semaine'    WHERE slug='pic-photo-week';
UPDATE categories SET name_rw='Amafoto y’abanyeshuri',   name_fr='Photographie étudiante' WHERE slug='pic-student';
