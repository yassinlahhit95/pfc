<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// BLOG / NOTICIAS DEL CENTRO — modelos/blog.php
// Tabla: blog_posts (ver landing-system/sql/blog_posts.sql)
// ══════════════════════════════════════════════════════════════════════

// Crea la tabla si no existe (despliegue manual por FTP, sin migraciones).
function blogAsegurarTabla() {
    static $hecho = false;
    if ($hecho) return;
    $hecho = true;
    $con = obtenerConexion();
    mysqli_query($con, "CREATE TABLE IF NOT EXISTS blog_posts (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS PÚBLICAS (solo posts publicados)
// ══════════════════════════════════════════════════════════════════════

function listarPostsPublicados($limite = 9, $offset = 0, $categoria = '') {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $sql = "SELECT * FROM blog_posts
            WHERE publicado = 1 AND fechaPublicacion <= NOW()"
         . ($categoria !== '' ? " AND categoria = ?" : "")
         . " ORDER BY destacado DESC, fechaPublicacion DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    $limite = (int)$limite;
    $offset = (int)$offset;
    if ($categoria !== '') {
        mysqli_stmt_bind_param($stmt, "sii", $categoria, $limite, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, "ii", $limite, $offset);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function contarPostsPublicados($categoria = '') {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) AS total FROM blog_posts
            WHERE publicado = 1 AND fechaPublicacion <= NOW()"
         . ($categoria !== '' ? " AND categoria = ?" : "");
    $stmt = mysqli_prepare($con, $sql);
    if ($categoria !== '') mysqli_stmt_bind_param($stmt, "s", $categoria);
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (int)($fila['total'] ?? 0);
}

function obtenerPostPorSlug($slug) {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM blog_posts WHERE slug = ? AND publicado = 1 AND fechaPublicacion <= NOW()");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Otros posts publicados para el bloque "seguir leyendo" del detalle.
function listarPostsRelacionados($idPost, $limite = 3) {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM blog_posts
         WHERE publicado = 1 AND fechaPublicacion <= NOW() AND idPost != ?
         ORDER BY fechaPublicacion DESC LIMIT ?");
    $idPost = (int)$idPost;
    $limite = (int)$limite;
    mysqli_stmt_bind_param($stmt, "ii", $idPost, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

// Categorías con al menos un post publicado (para los filtros del blog).
function listarCategoriasBlog() {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $res = mysqli_query($con,
        "SELECT categoria, COUNT(*) AS total FROM blog_posts
         WHERE publicado = 1 AND fechaPublicacion <= NOW() AND categoria != ''
         GROUP BY categoria ORDER BY categoria ASC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// GESTIÓN (admin)
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPosts() {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM blog_posts ORDER BY idPost DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function obtenerPostPorId($idPost) {
    blogAsegurarTabla();
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM blog_posts WHERE idPost = ?");
    $idPost = (int)$idPost;
    mysqli_stmt_bind_param($stmt, "i", $idPost);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Slug URL-safe único a partir del título ("FP Dual 2026" → fp-dual-2026).
function generarSlugBlog($titulo, $idExcluir = 0) {
    $slug = mb_strtolower(trim($titulo));
    $slug = strtr($slug, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n','ç'=>'c']);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim(mb_substr($slug, 0, 200), '-') ?: 'post';

    $con  = obtenerConexion();
    $base = $slug;
    $n    = 1;
    while (true) {
        $stmt = mysqli_prepare($con, "SELECT idPost FROM blog_posts WHERE slug = ? AND idPost != ?");
        $idExcluir = (int)$idExcluir;
        mysqli_stmt_bind_param($stmt, "si", $slug, $idExcluir);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 0) return $slug;
        $slug = $base . '-' . (++$n);
    }
}

function insertarPost($titulo, $slug, $resumen, $contenido, $imagen, $categoria, $autor, $publicado, $destacado, $fechaPublicacion) {
    blogAsegurarTabla();
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO blog_posts (titulo, slug, resumen, contenido, imagen, categoria, autor, publicado, destacado, fechaPublicacion)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $publicado = $publicado ? 1 : 0;
    $destacado = $destacado ? 1 : 0;
    mysqli_stmt_bind_param($stmt, "sssssssiis",
        $titulo, $slug, $resumen, $contenido, $imagen, $categoria, $autor, $publicado, $destacado, $fechaPublicacion);
    if (!mysqli_stmt_execute($stmt)) return 0;
    return mysqli_insert_id($con);
}

function actualizarPost($idPost, $titulo, $slug, $resumen, $contenido, $imagen, $categoria, $autor, $publicado, $destacado, $fechaPublicacion) {
    blogAsegurarTabla();
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE blog_posts SET titulo = ?, slug = ?, resumen = ?, contenido = ?, imagen = ?,
                categoria = ?, autor = ?, publicado = ?, destacado = ?, fechaPublicacion = ?
         WHERE idPost = ?");
    $idPost    = (int)$idPost;
    $publicado = $publicado ? 1 : 0;
    $destacado = $destacado ? 1 : 0;
    mysqli_stmt_bind_param($stmt, "sssssssiisi",
        $titulo, $slug, $resumen, $contenido, $imagen, $categoria, $autor, $publicado, $destacado, $fechaPublicacion, $idPost);
    return mysqli_stmt_execute($stmt);
}

function borrarPost($idPost) {
    blogAsegurarTabla();
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM blog_posts WHERE idPost = ?");
    $idPost = (int)$idPost;
    mysqli_stmt_bind_param($stmt, "i", $idPost);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}
