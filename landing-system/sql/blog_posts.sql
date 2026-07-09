-- ══════════════════════════════════════════════════════════════════════
-- BLOG DEL CENTRO — tabla blog_posts
-- El modelo (modelos/blog.php) la crea automáticamente si no existe,
-- este archivo queda como referencia / para creación manual en producción.
-- ══════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS blog_posts (
    idPost INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    slug VARCHAR(220) NOT NULL UNIQUE,
    resumen VARCHAR(500) NOT NULL DEFAULT '',
    contenido MEDIUMTEXT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT '',
    categoria VARCHAR(80) NOT NULL DEFAULT '',
    autor VARCHAR(120) NOT NULL DEFAULT '',
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    fechaPublicacion DATETIME NULL,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_publicado (publicado, fechaPublicacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
