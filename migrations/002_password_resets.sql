-- Password reset tokens table for all user types
-- Run once against yassjjzw_pfc

CREATE TABLE IF NOT EXISTS `password_resets` (
  `token`         varchar(64)                                    NOT NULL,
  `email`         varchar(255)                                   NOT NULL,
  `tipo_usuario`  enum('admin','profesor','estudiante')          NOT NULL,
  `expires_at`    datetime                                       NOT NULL,
  `usado`         tinyint(1)                                     NOT NULL DEFAULT 0,
  `creado_at`     datetime                                       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`token`),
  KEY `idx_pr_email_tipo` (`email`, `tipo_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
