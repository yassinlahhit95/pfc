<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Cache.php';

// ══════════════════════════════════════════════════════════════════════
// CONFIGURACIÓN DE LA LANDING (fila única, idLanding = 1)
// ══════════════════════════════════════════════════════════════════════

function obtenerLandingConfig() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM landing_config WHERE idLanding = 1");
    $cfg = $res ? mysqli_fetch_assoc($res) : null;
    if ($cfg) return $cfg;

    mysqli_query($con, "INSERT IGNORE INTO landing_config (idLanding) VALUES (1)");
    return [
        'idLanding' => 1, 'plantilla' => 'institucional', 'ajustes' => null,
        'plantilla_pub' => null, 'ajustes_pub' => null, 'publicadoEn' => null,
    ];
}

function guardarAjustesLanding($plantilla, $ajustesJson) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE landing_config SET plantilla = ?, ajustes = ? WHERE idLanding = 1");
    mysqli_stmt_bind_param($stmt, 'ss', $plantilla, $ajustesJson);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// SECCIONES
// ══════════════════════════════════════════════════════════════════════

function listarSeccionesLanding($version, $soloVisibles = false) {
    if (!in_array($version, ['draft', 'live'], true)) return [];

    $fetch = function () use ($version, $soloVisibles) {
        $con = obtenerConexion();
        $sql = "SELECT * FROM landing_secciones WHERE version = ?"
             . ($soloVisibles ? " AND visible = 1" : "")
             . " ORDER BY orden ASC, idSeccion ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, 's', $version);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $lista = [];
        while ($fila = mysqli_fetch_assoc($res)) $lista[] = $fila;
        return $lista;
    };

    // El borrador cambia constantemente mientras se edita: nunca se cachea.
    // La versión publicada solo cambia al pulsar "Publicar", así que la
    // cacheamos: es la que sirven index.php/blog.php/contacto.php en cada
    // visita pública anónima.
    if ($version === 'live') {
        return Cache::remember('landing_secciones_live_' . ($soloVisibles ? 1 : 0), 300, $fetch);
    }
    return $fetch();
}

function obtenerSeccionPorId($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "SELECT * FROM landing_secciones WHERE idSeccion = ? AND version = 'draft'");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($res) ?: null;
}

function insertarSeccionLanding($tipo, $contenidoJson) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "INSERT INTO landing_secciones (version, tipo, orden, visible, contenido)
         SELECT 'draft', ?, COALESCE(MAX(orden), 0) + 1, 1, ?
         FROM landing_secciones AS s WHERE s.version = 'draft'");
    mysqli_stmt_bind_param($stmt, 'ss', $tipo, $contenidoJson);
    if (!mysqli_stmt_execute($stmt)) return 0;
    return mysqli_insert_id($con);
}

function actualizarContenidoSeccion($id, $contenidoJson) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE landing_secciones SET contenido = ? WHERE idSeccion = ? AND version = 'draft'");
    mysqli_stmt_bind_param($stmt, 'si', $contenidoJson, $id);
    return mysqli_stmt_execute($stmt);
}

// Recibe los ids del borrador en el orden deseado y persiste orden = posición.
function actualizarOrdenSecciones(array $ids) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE landing_secciones SET orden = ? WHERE idSeccion = ? AND version = 'draft'");
    foreach (array_values($ids) as $i => $id) {
        $orden = $i + 1;
        $id    = (int)$id;
        mysqli_stmt_bind_param($stmt, 'ii', $orden, $id);
        if (!mysqli_stmt_execute($stmt)) return false;
    }
    return true;
}

function actualizarVisibleSeccion($id, $visible) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "UPDATE landing_secciones SET visible = ? WHERE idSeccion = ? AND version = 'draft'");
    $visible = $visible ? 1 : 0;
    mysqli_stmt_bind_param($stmt, 'ii', $visible, $id);
    return mysqli_stmt_execute($stmt);
}

function borrarSeccionLanding($id) {
    $con  = obtenerConexion();
    $stmt = mysqli_prepare($con, "DELETE FROM landing_secciones WHERE idSeccion = ? AND version = 'draft'");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    return mysqli_stmt_execute($stmt) && mysqli_stmt_affected_rows($stmt) > 0;
}

// ══════════════════════════════════════════════════════════════════════
// PLANTILLAS Y PUBLICACIÓN
// ══════════════════════════════════════════════════════════════════════

// Reemplaza el borrador completo por las secciones de una plantilla.
// $secciones = [ ['tipo' => ..., 'contenido' => array], ... ] ya saneadas.
function reemplazarBorradorLanding($plantilla, array $secciones) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        mysqli_query($con, "DELETE FROM landing_secciones WHERE version = 'draft'");

        $stmt = mysqli_prepare($con,
            "INSERT INTO landing_secciones (version, tipo, orden, visible, contenido) VALUES ('draft', ?, ?, 1, ?)");
        foreach (array_values($secciones) as $i => $seccion) {
            $orden = $i + 1;
            $json  = json_encode($seccion['contenido'], JSON_UNESCAPED_UNICODE);
            mysqli_stmt_bind_param($stmt, 'sis', $seccion['tipo'], $orden, $json);
            if (!mysqli_stmt_execute($stmt)) throw new Exception(mysqli_stmt_error($stmt));
        }

        $stmt2 = mysqli_prepare($con, "UPDATE landing_config SET plantilla = ? WHERE idLanding = 1");
        mysqli_stmt_bind_param($stmt2, 's', $plantilla);
        if (!mysqli_stmt_execute($stmt2)) throw new Exception(mysqli_stmt_error($stmt2));

        mysqli_commit($con);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($con);
        return false;
    }
}

// Publica el borrador: copia draft → live y sella plantilla/ajustes publicados.
function publicarLanding() {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        mysqli_query($con, "DELETE FROM landing_secciones WHERE version = 'live'");
        $ok = mysqli_query($con,
            "INSERT INTO landing_secciones (version, tipo, orden, visible, contenido)
             SELECT 'live', tipo, orden, visible, contenido
             FROM landing_secciones WHERE version = 'draft'");
        if (!$ok) throw new Exception(mysqli_error($con));

        $ok = mysqli_query($con,
            "UPDATE landing_config
             SET plantilla_pub = plantilla, ajustes_pub = ajustes, publicadoEn = NOW()
             WHERE idLanding = 1");
        if (!$ok) throw new Exception(mysqli_error($con));

        mysqli_commit($con);
        Cache::forget('landing_secciones_live_1');
        Cache::forget('landing_secciones_live_0');
        return true;
    } catch (Exception $e) {
        mysqli_rollback($con);
        return false;
    }
}

// Descarta el borrador: copia live → draft. Solo si ya hay una versión publicada.
function descartarBorradorLanding() {
    $con = obtenerConexion();
    $cfg = obtenerLandingConfig();
    if (empty($cfg['publicadoEn'])) return false;

    mysqli_begin_transaction($con);
    try {
        mysqli_query($con, "DELETE FROM landing_secciones WHERE version = 'draft'");
        $ok = mysqli_query($con,
            "INSERT INTO landing_secciones (version, tipo, orden, visible, contenido)
             SELECT 'draft', tipo, orden, visible, contenido
             FROM landing_secciones WHERE version = 'live'");
        if (!$ok) throw new Exception(mysqli_error($con));

        $ok = mysqli_query($con,
            "UPDATE landing_config
             SET plantilla = COALESCE(plantilla_pub, plantilla), ajustes = ajustes_pub
             WHERE idLanding = 1");
        if (!$ok) throw new Exception(mysqli_error($con));

        mysqli_commit($con);
        return true;
    } catch (Exception $e) {
        mysqli_rollback($con);
        return false;
    }
}
