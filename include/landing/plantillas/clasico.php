<?php
// Plantilla "Clásico académico" — IES tradicional: serifa en titulares,
// verde botella, bordes finos y maquetación sobria centrada.
return [
    'slug'        => 'clasico',
    'nombre'      => 'Clásico académico',
    'descripcion' => 'Estilo académico tradicional: tipografía con serifa, verde botella y maquetación sobria. Transmite trayectoria y seriedad.',
    'thumbnail'   => 'plantilla-clasico.svg',
    'colorAcento' => '#166534',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'variante'  => 'minimal',
            'eyebrow'   => 'Desde 1985 formando profesionales',
            'titulo'    => 'Excelencia académica y compromiso con el empleo',
            'subtitulo' => 'Un centro con trayectoria, profesorado estable y resultados contrastados en inserción laboral.',
        ]],
        ['tipo' => 'porque_elegirnos'],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'instalaciones'],
        ['tipo' => 'fp_dual'],
        ['tipo' => 'testimonios'],
        ['tipo' => 'faq'],
        ['tipo' => 'contacto'],
    ],
];
