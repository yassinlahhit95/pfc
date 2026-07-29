<?php
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../modelos/landing.php';
require_once __DIR__ . '/../include/landing/secciones.php';
require_once __DIR__ . '/../include/landing/plantillas.php';
// ══════════════════════════════════════════════════════════════════════
// PÁGINA DE CONTACTO SEPARADA
// ══════════════════════════════════════════════════════════════════════
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

foreach ($secciones as $seccion) {
    if (!isset($tipos[$seccion['tipo']])) continue;
    $contenido = json_decode($seccion['contenido'] ?? '{}', true) ?: [];

    // Si es la sección de contacto, guardamos sus datos
    if ($seccion['tipo'] === 'contacto') {
        $contactoData = $contenido;
    }

    $navVisible = $contenido['navVisible'] ?? (!empty($tipos[$seccion['tipo']]['menu']) ? 'si' : 'no');
    $navTexto   = $contenido['navTexto'] ?? ($tipos[$seccion['tipo']]['menu'] ?? '');

    if ($navVisible === 'si' && !empty($navTexto) && !isset($menuAnclas[$seccion['tipo']])) {
        $esSeparado = ($seccion['tipo'] === 'contacto' && ($contenido['modoVisualizacion'] ?? 'integrado') === 'separado');
        $menuAnclas[$seccion['tipo']] = [
            'texto' => $navTexto,
            'separado' => $esSeparado
        ];
        // La sección de noticias enlaza al blog completo, no a un ancla
        if ($seccion['tipo'] === 'noticias') $menuAnclas[$seccion['tipo']]['url'] = '/vistas/blog.php';
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
