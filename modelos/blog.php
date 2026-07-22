<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// BLOG / NOTICIAS DEL CENTRO — modelos/blog.php
// Tabla: blog_posts (ver noDeploy/migrations/001_blog_posts.sql)
// ══════════════════════════════════════════════════════════════════════

// La tabla blog_posts se crea una vez vía noDeploy/database.sql (no en cada
// request: antes se comprobaba con CREATE TABLE IF NOT EXISTS en cada
// llamada, una sentencia DDL costosa e innecesaria después de la primera vez).

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS PÚBLICAS (solo posts publicados)
// ══════════════════════════════════════════════════════════════════════

function listarPostsPublicados($limite = 9, $offset = 0, $categoria = '') {
    $con = obtenerConexion();
    // Se compara contra la hora de PHP (no NOW() de MySQL): evita depender de que
    // el servidor de MySQL tenga la misma zona horaria configurada que PHP.
    $ahora = date('Y-m-d H:i:s');
    $sql = "SELECT * FROM blog_posts
            WHERE publicado = 1 AND fechaPublicacion <= ?"
         . ($categoria !== '' ? " AND categoria = ?" : "")
         . " ORDER BY destacado DESC, fechaPublicacion DESC LIMIT ? OFFSET ?";
    $stmt = mysqli_prepare($con, $sql);
    $limite = (int)$limite;
    $offset = (int)$offset;
    if ($categoria !== '') {
        mysqli_stmt_bind_param($stmt, "ssii", $ahora, $categoria, $limite, $offset);
    } else {
        mysqli_stmt_bind_param($stmt, "sii", $ahora, $limite, $offset);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function contarPostsPublicados($categoria = '') {
    $con = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $sql = "SELECT COUNT(*) AS total FROM blog_posts
            WHERE publicado = 1 AND fechaPublicacion <= ?"
         . ($categoria !== '' ? " AND categoria = ?" : "");
    $stmt = mysqli_prepare($con, $sql);
    if ($categoria !== '') {
        mysqli_stmt_bind_param($stmt, "ss", $ahora, $categoria);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $ahora);
    }
    mysqli_stmt_execute($stmt);
    $fila = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    return (int)($fila['total'] ?? 0);
}

function obtenerPostPorSlug($slug) {
    $con = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($con,
        "SELECT * FROM blog_posts WHERE slug = ? AND publicado = 1 AND fechaPublicacion <= ?");
    mysqli_stmt_bind_param($stmt, "ss", $slug, $ahora);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Otros posts publicados para el bloque "seguir leyendo" del detalle.
function listarPostsRelacionados($idPost, $limite = 3) {
    $con = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($con,
        "SELECT * FROM blog_posts
         WHERE publicado = 1 AND fechaPublicacion <= ? AND idPost != ?
         ORDER BY fechaPublicacion DESC LIMIT ?");
    $idPost = (int)$idPost;
    $limite = (int)$limite;
    mysqli_stmt_bind_param($stmt, "sii", $ahora, $idPost, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

// Categorías con al menos un post publicado (para los filtros del blog).
function listarCategoriasBlog() {
    $con = obtenerConexion();
    $ahora = date('Y-m-d H:i:s');
    $stmt = mysqli_prepare($con,
        "SELECT categoria, COUNT(*) AS total FROM blog_posts
         WHERE publicado = 1 AND fechaPublicacion <= ? AND categoria != ''
         GROUP BY categoria ORDER BY categoria ASC");
    mysqli_stmt_bind_param($stmt, "s", $ahora);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// GESTIÓN (admin)
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosPosts() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM blog_posts ORDER BY idPost DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function obtenerPostPorId($idPost) {
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
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM blog_posts WHERE idPost = ?");
    $idPost = (int)$idPost;
    mysqli_stmt_bind_param($stmt, "i", $idPost);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}
