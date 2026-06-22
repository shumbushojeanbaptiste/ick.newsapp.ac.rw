USE `KAMINUZA`;
-- Add Kinyarwanda + French versions to several existing sample articles, and
-- a few native rw/fr ones, so the language switcher shows real content.
-- (Idempotent: UPDATEs are safe to re-run.)

-- Add rw + fr to existing English samples (so they appear in all 3 versions).
UPDATE articles SET
  title_rw='Kaminda za kaminuza zagura serivisi', excerpt_rw='Abanyeshuri bishimiye amasaha menshi n''ahantu hashya.', body_rw='<p>Serivisi ya bisi ya kaminuza yongereye inzira nshya n''amasaha y''umugoroba, byoroshya urugendo rw''abanyeshuri benshi.</p>',
  title_fr='Le service de navette du campus s''étend', excerpt_fr='Les étudiants saluent des horaires élargis et de nouveaux arrêts.', body_fr='<p>Le service de navette du campus a ajouté de nouveaux itinéraires et des horaires prolongés en soirée.</p>'
  WHERE slug='sample-n1';

UPDATE articles SET
  title_rw='Inteko y''abanyeshuri yatangaje imiterere nshya', excerpt_rw='Ivugurura rigamije guha buri shami ijwi rikomeye.', body_rw='<p>Inteko y''abanyeshuri yatangaje uburyo bushya bwo kuyobora bugamije guha buri shami ijwi mu myanzuro.</p>',
  title_fr='Le guilde étudiante dévoile sa nouvelle structure', excerpt_fr='Les réformes visent à renforcer la représentation des facultés.', body_fr='<p>La guilde étudiante a annoncé un modèle de direction restructuré.</p>'
  WHERE slug='sample-p1';

UPDATE articles SET
  title_rw='Itsinda rya kaminuza ryegukanye zahabu', excerpt_rw='Itsinda rya kaminuza ryaje ku isonga mu mikino y''amashuri makuru.', body_rw='<p>Abasiganwa ba kaminuza bagize imikino myiza mu marushanwa y''igihugu, baza ku isonga ku rwego rw''imikino.</p>',
  title_fr='Les athlètes de l''UR rapportent l''or', excerpt_fr='L''équipe universitaire en tête du tableau des médailles.', body_fr='<p>Les athlètes de l''Université du Rwanda ont brillé aux jeux inter-universitaires nationaux.</p>'
  WHERE slug='sample-athletics-gold';

UPDATE articles SET
  title_rw='Ubushakashatsi bwa UR bwatsindiye inkunga', excerpt_rw='Inkunga y''imyaka myinshi izatera inkunga ubushakashatsi ku bw''ikirere.', body_rw='<p>Itsinda ry''ishami ry''ubuhinzi ryabonye inkunga y''ubushakashatsi ku binyabuzima bihangana n''imihindagurikire y''ikirere.</p>',
  title_fr='Des chercheurs de l''UR remportent une subvention', excerpt_fr='Une subvention pluriannuelle financera la recherche agricole.', body_fr='<p>Une équipe du Collège d''agriculture a obtenu une subvention de recherche continentale.</p>'
  WHERE slug='sample-research-grant';

UPDATE articles SET
  title_rw='Isomero rikuru ryongereye amasaha', excerpt_rw='Amasaha yongerewe atangira mu cyumweru gitaha.', body_rw='<p>Isomero rikuru rizakora amasaha yongerewe mu gihe cy''ibizamini.</p>',
  title_fr='La bibliothèque prolonge ses horaires', excerpt_fr='Les horaires prolongés prennent effet la semaine prochaine.', body_fr='<p>La bibliothèque fonctionnera avec des horaires prolongés pendant la période d''examens.</p>'
  WHERE slug='sample-ann-4';

UPDATE articles SET
  title_rw='Inama ku bijyanye n''imihindagurikire y''ikirere', excerpt_rw='Abanyeshuri n''impuguke baganiriye ku ngamba.', body_rw='<p>Inama yo ku rwego rwa kaminuza ku mihindagurikire y''ikirere yakuruye abantu benshi.</p>',
  title_fr='Un forum sur le campus aborde le climat', excerpt_fr='Étudiants et experts ont débattu de mesures concrètes.', body_fr='<p>Un forum sur le campus consacré à l''action climatique a attiré un large public.</p>'
  WHERE slug='sample-climate-forum';
