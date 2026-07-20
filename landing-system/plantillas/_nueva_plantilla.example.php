<?php
// ════════════════════════════════════════════════════════════════════════
// PLANTILLA DE EJEMPLO — landing-system/plantillas/_nueva_plantilla.example.php
// ════════════════════════════════════════════════════════════════════════
// Copia este archivo, renómbralo (ej: moderna.php) y rellena los valores.
// Luego añade el slug a landing_plantillas_slugs() en engine/plantillas.php.
//
// SECCIONES DISPONIBLES (tipos válidos):
//   hero, hero_slider, cta_secundario, galeria, noticias, video_presentacion,
//   cifras, porque_elegirnos, oferta_formativa, empresas, instalaciones,
//   fp_dual, testimonios, prematricula_cta, faq, contacto
//
// Los campos de 'contenido' son OPCIONALES — si no se especifican, se
// usan los valores por defecto definidos en engine/secciones.php.
// ════════════════════════════════════════════════════════════════════════

return [
    'slug'        => 'moderna',                          // ← mismo nombre que el archivo
    'nombre'      => 'Moderna',                          // ← nombre visible en el selector
    'descripcion' => 'Descripción breve de la plantilla.',
    'thumbnail'   => 'plantilla-moderna.svg',            // ← archivo en assets/imagenes/
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
