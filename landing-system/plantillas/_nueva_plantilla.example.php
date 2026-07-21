<?php
// ════════════════════════════════════════════════════════════════════════
// PLANTILLA DE EJEMPLO — landing-system/plantillas/_nueva_plantilla.example.php
// ════════════════════════════════════════════════════════════════════════
// Copia este archivo, renómbralo (ej: moderna.php) y rellena los valores.
// El slug se detecta automáticamente (no hay que registrarlo en ningún sitio)
// — pero una plantilla completa necesita, además de este archivo, DOS cosas
// más que este ejemplo no genera por ti:
//   1. landing-system/temas/tema-moderna.css   (identidad visual — sin esto
//      la plantilla funciona pero se ve con el aspecto genérico de base.css)
//   2. public/imagenes/landing/plantilla-moderna.svg  (thumbnail del selector
//      — OJO: es esta carpeta, NO landing-system/assets/imagenes/)
// Si prefieres generarlos los tres de una vez, usa en su lugar:
//   php landing-system/herramientas/nueva_plantilla.php moderna "Moderna" "#6d28d9"
//
// SECCIONES DISPONIBLES (tipos válidos):
//   hero, hero_slider, cta_secundario, galeria, noticias, video_presentacion,
//   cifras, porque_elegirnos, equipo_docente, oferta_formativa, becas_financiacion,
//   empresas, instalaciones, fp_dual, testimonios, prematricula_cta, faq, contacto
//
// Los campos de 'contenido' son OPCIONALES — si no se especifican, se
// usan los valores por defecto definidos en engine/secciones.php.
// ════════════════════════════════════════════════════════════════════════

return [
    'slug'        => 'moderna',                          // ← mismo nombre que el archivo
    'nombre'      => 'Moderna',                          // ← nombre visible en el selector
    'descripcion' => 'Descripción breve de la plantilla.',
    'thumbnail'   => 'plantilla-moderna.svg',            // ← archivo en public/imagenes/landing/
    'colorAcento' => '#6d28d9',                          // ← color hex principal

    'secciones' => [
        // Hero con imagen de fondo y botón CTA
        ['tipo' => 'hero', 'contenido' => [
            'variante'    => 'fondo',
            'eyebrow'     => 'Formación para el mundo real',
            'titulo'      => 'Tu futuro empieza aquí',
            'subtitulo'   => 'Ciclos formativos de calidad, prácticas garantizadas y salida laboral real.',
            'botonTexto'  => 'Ver ciclos formativos',
            'botonUrl'    => '#oferta_formativa',
        ]],

        // Secciones sin personalización (usan contenido por defecto):
        ['tipo' => 'cifras'],
        ['tipo' => 'porque_elegirnos'],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'testimonios'],
        ['tipo' => 'contacto'],
    ],
];
