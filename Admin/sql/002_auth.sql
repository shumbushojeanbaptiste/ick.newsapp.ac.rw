-- ============================================================
--  UR KAMINUZA - Auth additions: password resets, remember-me,
--  and one seed user per role.
-- ============================================================
USE `KAMINUZA`;

-- ------------------------------------------------------------
-- Password reset tokens
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `password_resets` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64)     NOT NULL,                  -- sha256 of the raw token
  `expires_at` DATETIME     NOT NULL,
  `used`       TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pr_token` (`token_hash`),
  KEY `fk_pr_user` (`user_id`),
  CONSTRAINT `fk_pr_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- Remember-me persistent login tokens
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `remember_tokens` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`     INT UNSIGNED NOT NULL,
  `selector`    CHAR(32)     NOT NULL,                 -- public lookup id
  `token_hash`  CHAR(64)     NOT NULL,                 -- sha256 of validator
  `expires_at`  DATETIME     NOT NULL,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rt_selector` (`selector`),
  KEY `fk_rt_user` (`user_id`),
  CONSTRAINT `fk_rt_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- One seed user per role (password for all: Password@123)
-- ------------------------------------------------------------
INSERT INTO `users` (`role_id`, `name`, `email`, `password`)
SELECT r.id, x.name, x.email,
       '$2y$10$EmLNX6U.pRoJSDTTDyLKteMVdwZlVxDeNPk4wGJMX3.8uzgAV4rc.'
FROM (
    SELECT 'chief_editor' AS role, 'Chief Editor' AS name, 'chief@kaminuza.rw'      AS email UNION ALL
    SELECT 'reporter',     'Reporter User',  'reporter@kaminuza.rw'    UNION ALL
    SELECT 'contributor',  'Contributor User','contributor@kaminuza.rw' UNION ALL
    SELECT 'viewer',       'Viewer User',    'viewer@kaminuza.rw'
) x
JOIN `roles` r ON r.name = x.role
WHERE NOT EXISTS (SELECT 1 FROM `users` u WHERE u.email = x.email);
