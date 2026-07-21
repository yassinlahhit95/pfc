<?php
// ══════════════════════════════════════════════════════════════════════
// GENERADOR DE PLANTILLAS — landing-system/herramientas/nueva_plantilla.php
// ══════════════════════════════════════════════════════════════════════
// Crea de una sola vez los 3 archivos que necesita una plantilla nueva,
// ya enlazados entre sí por el mismo slug:
//   - landing-system/plantillas/<slug>.php      (estructura + contenido)
//   - landing-system/temas/tema-<slug>.css      (identidad visual)
//   - public/imagenes/landing/plantilla-<slug>.svg  (thumbnail placeholder)
//
// Solo por línea de comandos — nunca se sirve por navegador (el .htaccess
// de landing-system/ ya bloquea el acceso web directo a cualquier .php de
// esta carpeta, pero se comprueba aquí también por si se copia fuera de ella).
//
// Uso:
//   php landing-system/herramientas/nueva_plantilla.php <slug> "<Nombre>" [colorAcento]
// Ejemplo:
//   php landing-system/herramientas/nueva_plantilla.php moderna "Moderna" "#6d28d9"

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Esta herramienta solo se puede ejecutar por línea de comandos.\n");
}

$slug   = $argv[1] ?? null;
$nombre = $argv[2] ?? null;
$color  = $argv[3] ?? '#4f46e5';

if (!$slug || !$nombre) {
    fwrite(STDERR, "Uso: php nueva_plantilla.php <slug> \"<Nombre>\" [colorAcento]\n");
    fwrite(STDERR, "Ejemplo: php nueva_plantilla.php moderna \"Moderna\" \"#6d28d9\"\n");
    exit(1);
}
if (!preg_match('/^[a-z][a-z0-9_-]*$/', $slug)) {
    fwrite(STDERR, "El slug solo puede tener minúsculas, números, guiones y guion bajo, y empezar por una letra.\n");
    exit(1);
}
if (!preg_match('/^#[0-9a-fA-F]{6}$/', $color)) {
    fwrite(STDERR, "El color de acento debe ser un hexadecimal de 6 dígitos, ej: #4f46e5\n");
    exit(1);
}

$raizLanding = __DIR__ . '/..';
$raizProyecto = __DIR__ . '/../..';

$rutaPlantilla = "$raizLanding/plantillas/$slug.php";
$rutaTema      = "$raizLanding/temas/tema-$slug.css";
$rutaThumbnail = "$raizProyecto/public/imagenes/landing/plantilla-$slug.svg";

foreach ([$rutaPlantilla, $rutaTema, $rutaThumbnail] as $ruta) {
    if (file_exists($ruta)) {
        fwrite(STDERR, "Ya existe: $ruta\nElige otro slug o borra ese archivo antes de repetir.\n");
        exit(1);
    }
}
if (!is_dir(dirname($rutaThumbnail))) {
    fwrite(STDERR, "No existe la carpeta " . dirname($rutaThumbnail) . " — revisa que public/imagenes/landing/ exista.\n");
    exit(1);
}

$nombreEscapado = addslashes($nombre);

// ── 1. plantillas/<slug>.php ─────────────────────────────────────────────
$plantillaPhp = <<<PHP
<?php
// Plantilla "{$nombre}" — generada con herramientas/nueva_plantilla.php.
// Ajusta secciones, colorAcento y textos por defecto según tu diseño.
// Tipos de sección disponibles: ver landing-system/engine/secciones.php
return [
    'slug'        => '{$slug}',
    'nombre'      => '{$nombreEscapado}',
    'descripcion' => 'Descripción breve de la plantilla «{$nombreEscapado}».',
    'thumbnail'   => 'plantilla-{$slug}.svg',
    'colorAcento' => '{$color}',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'titulo' => 'Tu futuro empieza aquí',
        ]],
        ['tipo' => 'cifras'],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'contacto'],
    ],
];

PHP;
file_put_contents($rutaPlantilla, $plantillaPhp);

// ── 2. temas/tema-<slug>.css ──────────────────────────────────────────────
$temaCss = <<<CSS
/* ══════════════════════════════════════════════════════════════════════
   TEMA "{$nombre}" — generado con herramientas/nueva_plantilla.php.
   Aquí solo se sobrescriben las variables --lp-* y detalles puntuales;
   la estructura compartida vive en base.css. Consulta los otros temas
   (tema-clasico.css, tema-vocacional.css, tema-universidad.css...) para
   ver qué se suele personalizar: tipografía de titulares, radios, sombras,
   hero, tarjetas y botones.
   ══════════════════════════════════════════════════════════════════════ */

.tema-{$slug} {
  /* --lp-fuente-titulos: 'Sora', var(--lp-fuente); */
  /* --lp-radio: 24px; */
  /* --lp-radio-boton: 14px; */
  /* --lp-footer-fondo: #0b1220; */
}

CSS;
file_put_contents($rutaTema, $temaCss);

// ── 3. Thumbnail placeholder (public/imagenes/landing/) ──────────────────
$thumbSvg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 280" width="400" height="280">
  <rect width="400" height="280" fill="#f1f5f9"/>
  <rect x="0" y="0" width="400" height="40" fill="{$color}"/>
  <text x="20" y="150" font-family="sans-serif" font-size="16" fill="#64748b">Miniatura pendiente: {$nombreEscapado}</text>
</svg>

SVG;
file_put_contents($rutaThumbnail, $thumbSvg);

echo "Plantilla '{$slug}' creada correctamente:\n";
echo "  - " . realpath($rutaPlantilla) . "\n";
echo "  - " . realpath($rutaTema) . "\n";
echo "  - " . realpath($rutaThumbnail) . "  (sustituye este placeholder por el diseño real)\n";
echo "\nSe detecta automáticamente en el selector de plantillas del panel de administración.\n";
