<?php
// ══════════════════════════════════════════════════════════════════════
// LANDING PÚBLICA DEL CENTRO — renderizada desde el constructor de landing
// ══════════════════════════════════════════════════════════════════════
// Renderiza las secciones publicadas (version='live') según la plantilla
// elegida. Con ?preview=1 (solo sesión de admin) renderiza el borrador.
require_once __DIR__ . '/include/Security.php';
require_once __DIR__ . '/include/FeatureGuard.php';
require_once __DIR__ . '/modelos/configuracion.php';
require_once __DIR__ . '/modelos/landing.php';
require_once __DIR__ . '/include/landing/secciones.php';
require_once __DIR__ . '/include/landing/plantillas.php';

$preview = isset($_GET['preview']);
if ($preview) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['idAdmin'])) {
        http_response_code(403);
        echo '<h1>403 — Acceso restringido</h1><p>La previsualización requiere sesión de administrador.</p>';
        exit;
    }
    header('X-Robots-Tag: noindex, nofollow');
    header('X-Frame-Options: SAMEORIGIN');
    header("Content-Security-Policy: frame-ancestors 'self'");
}

$cfg = obtenerConfiguracionCentro();

// Flag desactivado (o instancia suspendida) → página mínima de fallback
if (!FeatureGuard::check('feature_landing')) {
    include __DIR__ . '/vistas/landing/fallback.php';
    exit;
}

$landing = obtenerLandingConfig();

// Nunca publicado → fallback (salvo en previsualización del borrador)
if (!$preview && empty($landing['publicadoEn'])) {
    include __DIR__ . '/vistas/landing/fallback.php';
    exit;
}

$temaRaw   = $preview ? $landing['plantilla'] : $landing['plantilla_pub'];
$tema      = in_array($temaRaw, landing_plantillas_slugs(), true) ? $temaRaw : 'institucional';
$ajustes   = json_decode(($preview ? $landing['ajustes'] : $landing['ajustes_pub']) ?? '', true) ?: [];
$secciones = listarSeccionesLanding($preview ? 'draft' : 'live', true);
$tipos     = landing_tipos();

// Anclas del menú: secciones visibles cuyo tipo declara etiqueta de menú
$menuAnclas = [];
foreach ($secciones as $s) {
    if (!empty($tipos[$s['tipo']]['menu']) && !isset($menuAnclas[$s['tipo']])) {
        $menuAnclas[$s['tipo']] = $tipos[$s['tipo']]['menu'];
    }
}

include __DIR__ . '/vistas/landing/_head.php';
include __DIR__ . '/vistas/landing/_nav.php';

foreach ($secciones as $s) {
    if (!isset($tipos[$s['tipo']])) continue;
    $contenido = json_decode($s['contenido'] ?? '{}', true) ?: [];
    include __DIR__ . '/vistas/landing/secciones/' . $s['tipo'] . '.php';
}

include __DIR__ . '/vistas/landing/_footer.php';
