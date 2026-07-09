<?php
// ══════════════════════════════════════════════════════════════════════
// PÁGINA DE CONTACTO SEPARADA
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../modelos/landing.php';
require_once __DIR__ . '/../include/landing/secciones.php';
require_once __DIR__ . '/../include/landing/plantillas.php';

$cfg = obtenerConfiguracionCentro();

// Si la landing está desactivada, mostramos fallback
if (!FeatureGuard::check('feature_landing')) {
    include __DIR__ . '/landing/fallback.php';
    exit;
}

$landing = obtenerLandingConfig();
$tema    = $landing['plantilla_pub'] ?: 'institucional';
$ajustes = json_decode($landing['ajustes_pub'] ?? '', true) ?: [];

// Construir el menú de navegación basado en la configuración guardada
$secciones = listarSeccionesLanding('live', true);
$tipos     = landing_tipos();
$menuAnclas = [];
$contactoData = [];

foreach ($secciones as $s) {
    if (!isset($tipos[$s['tipo']])) continue;
    $contenido = json_decode($s['contenido'] ?? '{}', true) ?: [];
    
    // Si es la sección de contacto, guardamos sus datos
    if ($s['tipo'] === 'contacto') {
        $contactoData = $contenido;
    }

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

// Sobrescribimos el título SEO para la página de contacto
$ajustes['tituloSeo'] = "Contacto — " . ($ajustes['tituloSeo'] ?: $cfg['nombreCentro']);

include __DIR__ . '/landing/_head.php';
include __DIR__ . '/landing/_nav.php';

// Renderizamos la sección de contacto usando los datos de la BD o los de defecto
$contenido = $contactoData ?: ($tipos['contacto']['defecto'] ?? []);

// Para asegurar que la sección tenga el padding adecuado en una página suelta
echo '<div style="padding-top: 64px; padding-bottom: 64px;">';
include __DIR__ . '/landing/secciones/contacto.php';
echo '</div>';

include __DIR__ . '/landing/_footer.php';
