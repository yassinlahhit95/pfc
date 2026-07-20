<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CATÁLOGO DE CICLOS (landing) — modelos/landingCiclos.php
// Tabla: landing_ciclos (ver landing-system/sql/landing_ciclos.sql).
// Contenido de marketing gestionado desde admin/secretaría; independiente
// de la tabla académica `ciclos` (gestión de alumnos/notas/profesores).
// ══════════════════════════════════════════════════════════════════════

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS PÚBLICAS (solo fichas publicadas)
// ══════════════════════════════════════════════════════════════════════

function listarCiclosLandingPublicados($limite = 9, $offset = 0) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM landing_ciclos
         WHERE publicado = 1
         ORDER BY destacado DESC, orden ASC, idLandingCiclo ASC LIMIT ? OFFSET ?");
    $limite = (int)$limite;
    $offset = (int)$offset;
    mysqli_stmt_bind_param($stmt, "ii", $limite, $offset);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function contarCiclosLandingPublicados() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT COUNT(*) AS total FROM landing_ciclos WHERE publicado = 1");
    $fila = mysqli_fetch_assoc($res);
    return (int)($fila['total'] ?? 0);
}

// Solo devuelve fichas publicadas: evita que una ficha en borrador sea
// alcanzable adivinando/probando su slug (IDOR).
function obtenerCicloLandingPorSlug($slug) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM landing_ciclos WHERE slug = ? AND publicado = 1");
    mysqli_stmt_bind_param($stmt, "s", $slug);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Otros ciclos publicados para el bloque "otros ciclos" de la ficha de detalle.
function listarCiclosLandingRelacionados($idLandingCiclo, $limite = 3) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT * FROM landing_ciclos
         WHERE publicado = 1 AND idLandingCiclo != ?
         ORDER BY destacado DESC, orden ASC LIMIT ?");
    $idLandingCiclo = (int)$idLandingCiclo;
    $limite = (int)$limite;
    mysqli_stmt_bind_param($stmt, "ii", $idLandingCiclo, $limite);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// GESTIÓN (admin / secretaría)
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosCiclosLanding() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM landing_ciclos ORDER BY orden ASC, idLandingCiclo DESC");
    $lista = [];
    while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
    return $lista;
}

function obtenerCicloLandingPorId($idLandingCiclo) {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM landing_ciclos WHERE idLandingCiclo = ?");
    $idLandingCiclo = (int)$idLandingCiclo;
    mysqli_stmt_bind_param($stmt, "i", $idLandingCiclo);
    mysqli_stmt_execute($stmt);
    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
}

// Slug URL-safe único a partir del título ("Desarrollo de Aplicaciones
// Multiplataforma" → desarrollo-de-aplicaciones-multiplataforma).
function generarSlugCiclo($titulo, $idExcluir = 0) {
    $slug = mb_strtolower(trim($titulo));
    $slug = strtr($slug, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n','ç'=>'c']);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim(mb_substr($slug, 0, 160), '-') ?: 'ciclo';

    $con  = obtenerConexion();
    $base = $slug;
    $n    = 1;
    while (true) {
        $stmt = mysqli_prepare($con, "SELECT idLandingCiclo FROM landing_ciclos WHERE slug = ? AND idLandingCiclo != ?");
        $idExcluir = (int)$idExcluir;
        mysqli_stmt_bind_param($stmt, "si", $slug, $idExcluir);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) === 0) return $slug;
        $slug = $base . '-' . (++$n);
    }
}

function insertarCicloLanding($titulo, $slug, $etiqueta, $resumen, $descripcion, $imagen, $precio, $duracion, $modalidad, $publicado, $destacado, $orden) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO landing_ciclos (titulo, slug, etiqueta, resumen, descripcion, imagen, precio, duracion, modalidad, publicado, destacado, orden)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $publicado = $publicado ? 1 : 0;
    $destacado = $destacado ? 1 : 0;
    $orden     = (int)$orden;
    mysqli_stmt_bind_param($stmt, "sssssssssiii",
        $titulo, $slug, $etiqueta, $resumen, $descripcion, $imagen, $precio, $duracion, $modalidad, $publicado, $destacado, $orden);
    if (!mysqli_stmt_execute($stmt)) return 0;
    return mysqli_insert_id($con);
}

function actualizarCicloLanding($idLandingCiclo, $titulo, $slug, $etiqueta, $resumen, $descripcion, $imagen, $precio, $duracion, $modalidad, $publicado, $destacado, $orden) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "UPDATE landing_ciclos SET titulo = ?, slug = ?, etiqueta = ?, resumen = ?, descripcion = ?, imagen = ?,
                precio = ?, duracion = ?, modalidad = ?, publicado = ?, destacado = ?, orden = ?
         WHERE idLandingCiclo = ?");
    $idLandingCiclo = (int)$idLandingCiclo;
    $publicado = $publicado ? 1 : 0;
    $destacado = $destacado ? 1 : 0;
    $orden     = (int)$orden;
    mysqli_stmt_bind_param($stmt, "sssssssssiiii",
        $titulo, $slug, $etiqueta, $resumen, $descripcion, $imagen, $precio, $duracion, $modalidad, $publicado, $destacado, $orden, $idLandingCiclo);
    return mysqli_stmt_execute($stmt);
}

function borrarCicloLanding($idLandingCiclo) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM landing_ciclos WHERE idLandingCiclo = ?");
    $idLandingCiclo = (int)$idLandingCiclo;
    mysqli_stmt_bind_param($stmt, "i", $idLandingCiclo);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}
