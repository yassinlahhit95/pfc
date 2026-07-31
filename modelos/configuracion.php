<?php
require_once __DIR__ . '/conectar.php';

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerConfiguracionCentro() {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT * FROM configuracion_centro WHERE idConfig = 1");
    $cfg = mysqli_fetch_assoc($res);
    return $cfg ?: [
        'nombreCentro'            => 'Centro de Formación Profesional',
        'codigoCentro'            => '', 'nifCifCentro' => '', 'direccionCentro' => '', 'ciudadCentro' => '',
        'cpCentro'                => '', 'telefonoCentro'  => '', 'emailCentro'  => '',
        'cursoEscolar'            => date('Y') . '-' . (date('Y') + 1),
        'logoCentro'              => '', 'logoGobierno1'   => '', 'logoGobierno2' => '',
        'colorAcento'             => '',
        'textoLegal'              => '', 'nombreDirectorFirmante' => '',
        'feature_prematricula'    => 1,
        'feature_chat'            => 1,
        'feature_inventario'      => 1,
        'feature_subida_tfg'      => 1,
        'feature_anuncios'        => 1,
        'feature_eventos'         => 1,
        'feature_retos'           => 1,
        'feature_mensajes'        => 1,
        'feature_pagos'           => 1,
        'feature_gastos'          => 1,
        'feature_informes'        => 1,
        'feature_horario'         => 1,
        'feature_geoblock_admin'  => 1,
        'feature_ra_ce'           => 0,
        'feature_fp_dual'         => 0,
        'feature_landing'         => 1,
        'feature_fct'             => 1,
        'feature_modulos'         => 1,
        'prematricula_filtrar_niveles' => 0,
    ];
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function guardarConfiguracionCentro($d) {
    $con = obtenerConexion();
    $res = mysqli_query($con, "SELECT idConfig FROM configuracion_centro WHERE idConfig = 1");
    $sql = mysqli_num_rows($res) === 0
        ? "INSERT INTO configuracion_centro (nombreCentro,codigoCentro,nifCifCentro,direccionCentro,ciudadCentro,cpCentro,telefonoCentro,emailCentro,cursoEscolar,colorAcento,textoLegal,nombreDirectorFirmante,idConfig) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1)"
        : "UPDATE configuracion_centro SET nombreCentro=?,codigoCentro=?,nifCifCentro=?,direccionCentro=?,ciudadCentro=?,cpCentro=?,telefonoCentro=?,emailCentro=?,cursoEscolar=?,colorAcento=?,textoLegal=?,nombreDirectorFirmante=? WHERE idConfig=1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssssss',
        $d['nombreCentro'], $d['codigoCentro'], $d['nifCifCentro'], $d['direccionCentro'],
        $d['ciudadCentro'], $d['cpCentro'], $d['telefonoCentro'],
        $d['emailCentro'], $d['cursoEscolar'], $d['colorAcento'],
        $d['textoLegal'], $d['nombreDirectorFirmante']);
    $ok = mysqli_stmt_execute($stmt);
    if ($ok && class_exists('FeatureGuard')) {
        FeatureGuard::clearCache();
    }
    return $ok;
}

// Solo acepta columnas de feature válidas para evitar inyección de nombre de columna.
// Devuelve true en éxito, o un string con el error MySQL en fallo.
// Permite prematricula_filtrar_niveles además de los feature_ normales.
function actualizarFeatureToggle($feature, $estado) {
    $con = obtenerConexion();
    $featuresValidas = [
        'feature_prematricula', 'feature_chat', 'feature_inventario', 'feature_subida_tfg',
        'feature_anuncios', 'feature_eventos', 'feature_retos', 'feature_mensajes',
        'feature_pagos', 'feature_gastos', 'feature_informes', 'feature_horario',
        'feature_geoblock_admin', 'feature_ra_ce', 'feature_fp_dual', 'feature_landing',
        'feature_academico_config', 'feature_fct', 'feature_modulos',
        'prematricula_filtrar_niveles'
    ];
    if (!in_array($feature, $featuresValidas)) return 'Funcionalidad no reconocida.';
    $sql  = "UPDATE configuracion_centro SET $feature = ? WHERE idConfig = 1";
    $stmt = mysqli_prepare($con, $sql);
    if ($stmt === false) {
        return 'Error al preparar la consulta SQL: ' . mysqli_error($con)
             . ' — Es posible que la columna "' . $feature . '" no exista en la base de datos. '
             . 'Actualiza el esquema de configuracion_centro (ver noDeploy/database.sql) en producción.';
    }
    $val = ($estado == 1) ? 1 : 0;
    mysqli_stmt_bind_param($stmt, 'i', $val);
    if (!mysqli_stmt_execute($stmt)) {
        return 'Error al ejecutar la consulta SQL: ' . mysqli_stmt_error($stmt);
    }
    return true;
}

// Solo acepta columnas de logo válidas para evitar inyección de nombre de columna.
function actualizarLogoCentro($campo, $ruta) {
    $con = obtenerConexion();
    if (!in_array($campo, ['logoCentro', 'logoGobierno1', 'logoGobierno2'])) return false;
    $sql  = "UPDATE configuracion_centro SET $campo = ? WHERE idConfig = 1";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, 's', $ruta);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Convierte un logo de disco (o R2 si ya no está en local) a Data URI para incrustar en PDF sin rutas absolutas.
function logoParaPdf($ruta) {
    if (empty($ruta)) return '';
    $nombre = basename($ruta);
    $path = __DIR__ . '/../public/uploads/configuracion/' . $nombre;
    if (file_exists($path)) {
        $mime  = mime_content_type($path) ?: 'image/png';
        $bytes = file_get_contents($path);
    } else {
        require_once __DIR__ . '/../include/R2Client.php';
        $bytes = R2Client::fetchPublicBytes('configuracion/' . $nombre);
        if ($bytes === '') return '';
        $ext = strtolower(pathinfo($nombre, PATHINFO_EXTENSION));
        $mime = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'][$ext] ?? 'image/png';
    }
    return 'data:' . $mime . ';base64,' . base64_encode($bytes);
}
