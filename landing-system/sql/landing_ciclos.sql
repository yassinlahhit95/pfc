-- ══════════════════════════════════════════════════════════════════════
-- CATÁLOGO DE CICLOS (landing) — tabla landing_ciclos
-- Fichas públicas de ciclos formativos (marketing), independiente de la
-- tabla académica `ciclos`. Se crea vía migrate_db.php (sección 13);
-- este archivo queda como referencia / para creación manual en producción.
-- ══════════════════════════════════════════════════════════════════════

CREATE TABLE IF NOT EXISTS landing_ciclos (
    idLandingCiclo INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    slug VARCHAR(180) NOT NULL UNIQUE,
    etiqueta VARCHAR(60) NOT NULL DEFAULT '',
    resumen VARCHAR(300) NOT NULL DEFAULT '',
    descripcion MEDIUMTEXT NULL,
    imagen VARCHAR(255) NOT NULL DEFAULT '',
    precio VARCHAR(60) NOT NULL DEFAULT '',
    duracion VARCHAR(60) NOT NULL DEFAULT '',
    modalidad VARCHAR(60) NOT NULL DEFAULT '',
    publicado TINYINT(1) NOT NULL DEFAULT 0,
    destacado TINYINT(1) NOT NULL DEFAULT 0,
    orden INT NOT NULL DEFAULT 0,
    creadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizadoEn TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_publicado (publicado, orden)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
