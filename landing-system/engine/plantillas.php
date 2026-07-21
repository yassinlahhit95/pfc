<?php
// ══════════════════════════════════════════════════════════════════════
// PLANTILLAS PREDEFINIDAS DE LA LANDING
// ══════════════════════════════════════════════════════════════════════
// Cada plantilla vive en landing-system/plantillas/<slug>.php y devuelve:
//   slug, nombre, descripcion, thumbnail, colorAcento,
//   secciones => [ ['tipo' => ..., 'contenido' => [...sobrescribe el defecto...]], ... ]
//
// Los slugs se detectan automáticamente (landing_plantillas_slugs() hace un
// glob() de esta carpeta) — NO hay que registrar el slug en ningún sitio.
// Para añadir una nueva plantilla, lo más rápido es:
//   php landing-system/herramientas/nueva_plantilla.php <slug> "<Nombre>" <colorAcento>
// que genera de una vez el archivo de esta carpeta, su tema en temas/tema-<slug>.css
// y un thumbnail placeholder en public/imagenes/landing/. Ver el README de
// landing-system/ para hacerlo a mano paso a paso.

require_once __DIR__ . '/secciones.php';

function landing_plantillas_slugs(): array {
    // Auto-discovery: carga automáticamente todos los .php de landing-system/plantillas/
    // que no empiecen por _ (los que empiezan por _ son archivos de ejemplo/borrador)
    $archivos = glob(__DIR__ . '/../plantillas/[!_]*.php') ?: [];
    return array_map(fn($f) => basename($f, '.php'), $archivos);
}

function landing_plantillas(): array {
    static $plantillas = null;
    if ($plantillas !== null) return $plantillas;

    $plantillas = [];
    foreach (landing_plantillas_slugs() as $slug) {
        $archivo = __DIR__ . '/../plantillas/' . $slug . '.php';
        if (file_exists($archivo)) {
            $plantillas[$slug] = require $archivo;
        }
    }
    return $plantillas;
}

// Devuelve una plantilla por slug o null si no existe.
function landing_obtener_plantilla(string $slug): ?array {
    return landing_plantillas()[$slug] ?? null;
}

// Construye las secciones de una plantilla mezclando el contenido por defecto
// del tipo con las sobrescrituras del preset. Devuelve filas listas para insertar.
function landing_secciones_de_plantilla(string $slug): array {
    $plantilla = landing_obtener_plantilla($slug);
    if ($plantilla === null) return [];

    $tipos = landing_tipos();
    $filas = [];
    foreach ($plantilla['secciones'] as $seccion) {
        $tipo = $seccion['tipo'];
        if (!isset($tipos[$tipo])) continue;
        $contenido = array_merge($tipos[$tipo]['defecto'], $seccion['contenido'] ?? []);
        $filas[] = ['tipo' => $tipo, 'contenido' => $contenido];
    }
    return $filas;
}
