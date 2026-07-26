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
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Pragma: no-cache");
    header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
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
    $contenido = json_decode($s['contenido'] ?? '{}', true) ?: [];
    $navVisible = $contenido['navVisible'] ?? (!empty($tipos[$s['tipo']]['menu']) ? 'si' : 'no');
    $navTexto   = $contenido['navTexto'] ?? ($tipos[$s['tipo']]['menu'] ?? '');

    if ($navVisible === 'si' && !empty($navTexto) && !isset($menuAnclas[$s['tipo']])) {
        $esSeparado = ($s['tipo'] === 'contacto' && ($contenido['modoVisualizacion'] ?? 'integrado') === 'separado');
        $menuAnclas[$s['tipo']] = [
            'texto' => $navTexto,
            'separado' => $esSeparado
        ];
        // La sección de noticias enlaza al blog completo, no a un ancla
        if ($s['tipo'] === 'noticias') $menuAnclas[$s['tipo']]['url'] = '/vistas/blog.php';
    }
}

include __DIR__ . '/vistas/landing/_head.php';
include __DIR__ . '/vistas/landing/_nav.php';

foreach ($secciones as $s) {
    if (!isset($tipos[$s['tipo']])) continue;
    $contenido = json_decode($s['contenido'] ?? '{}', true) ?: [];
    
    // Si la sección contacto está en modo separado, no la renderizamos aquí
    if ($s['tipo'] === 'contacto' && ($contenido['modoVisualizacion'] ?? 'integrado') === 'separado') {
        continue;
    }
    
    // Generar estilos en línea personalizados para la sección
    $styleStr = '';
    if (!empty($contenido['estilo_fondo']))  $styleStr .= 'background-color:' . Security::escapeHtml($contenido['estilo_fondo']) . ' !important; ';
    if (!empty($contenido['estilo_texto']))  $styleStr .= 'color:' . Security::escapeHtml($contenido['estilo_texto']) . ' !important; ';
    if (!empty($contenido['estilo_fuente'])) $styleStr .= 'font-family:' . Security::escapeHtml($contenido['estilo_fuente']) . ' !important; ';
    if (!empty($contenido['estilo_tamano'])) $styleStr .= 'font-size:' . Security::escapeHtml($contenido['estilo_tamano']) . ' !important; ';
    
    $styleInline = '';
    if ($styleStr !== '') {
        $styleInline .= ' style="' . $styleStr . '"';
    }
    if ($preview) {
        $styleInline .= ' data-lb-id="' . Security::escapeHtml($s['idSeccion']) . '"';
    }

    include __DIR__ . '/vistas/landing/secciones/' . $s['tipo'] . '.php';
}

include __DIR__ . '/vistas/landing/_footer.php';

if ($preview) {
    // ?v=filemtime, igual que builder.php ya hace para landing-builder.js/css:
    // sin esto el navegador puede seguir sirviendo una copia en caché de este
    // script indefinidamente, incluso después de desplegar una versión nueva,
    // porque el HTML del iframe se pide con cache:'no-store' pero eso no
    // fuerza a repetir la petición de un <script src> anidado con la misma URL.
    echo '<script src="public/js/features/builder-preview.js?v=' . filemtime(__DIR__ . '/public/js/features/builder-preview.js') . '"></script>';
}
