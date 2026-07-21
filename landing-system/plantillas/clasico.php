<?php
// Plantilla "Clásico académico" — IES tradicional: serifa en titulares,
// verde botella, bordes finos y maquetación sobria centrada.
return [
    'slug'        => 'clasico',
    'nombre'      => 'Clásico académico',
    'descripcion' => 'Estilo académico tradicional: tipografía con serifa, verde botella y maquetación sobria. Transmite trayectoria y seriedad.',
    'thumbnail'   => 'plantilla-clasico.svg',
    'colorAcento' => '#0d4732',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'variante'  => 'minimal',
            'eyebrow'   => 'Desde 1985 formando profesionales',
            'titulo'    => 'Excelencia académica y compromiso con el empleo',
            'subtitulo' => 'Un centro con trayectoria, profesorado estable y resultados contrastados en inserción laboral.',
        ]],
        ['tipo' => 'porque_elegirnos', 'contenido' => ['variante' => 'grid']],
        ['tipo' => 'equipo_docente', 'contenido' => [
            'titulo' => 'Un claustro con trayectoria',
            'subtitulo' => 'Profesorado estable que conoce el sector y acompaña a cada alumno.',
        ]],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'instalaciones'],
        ['tipo' => 'fp_dual', 'contenido' => ['variante' => 'split']],
        ['tipo' => 'testimonios', 'contenido' => ['variante' => 'grid']],
        ['tipo' => 'faq', 'contenido' => ['variante' => 'lista']],
        // Reutiliza el tipo "empresas" como franja de trayectoria/reconocimiento
        // — encaja con el posicionamiento "Desde 1985" del hero de esta plantilla.
        ['tipo' => 'empresas', 'contenido' => [
            'titulo' => 'Trayectoria y reconocimiento',
            'texto'  => 'Décadas de historia avalan nuestros títulos oficiales, reconocidos en toda España y la Unión Europea.',
        ]],
        // Copy pensada para un centro con trayectoria: el mérito académico y la
        // continuidad familiar encajan mejor que "cuotas mensuales" (enfoque de
        // academia joven — ver el texto por defecto en engine/secciones.php).
        ['tipo' => 'becas_financiacion', 'contenido' => [
            'titulo' => 'Becas y ayudas al mérito',
            'subtitulo' => 'Reconocemos la trayectoria académica y familiar de quienes nos eligen.',
            'items' => [
                ['icono' => 'fa-award', 'titulo' => 'Becas por expediente', 'texto' => 'Descuentos para el alumnado con mejor expediente académico de su promoción.'],
                ['icono' => 'fa-shield-halved', 'titulo' => 'Continuidad familiar', 'texto' => 'Condiciones especiales para hermanos o segundas generaciones de antiguos alumnos.'],
                ['icono' => 'fa-money-bill-wave', 'titulo' => 'Becas y ayudas oficiales', 'texto' => 'Te ayudamos a tramitar las becas del Ministerio y de tu comunidad autónoma.'],
            ],
            'botonTexto' => 'Consultar condiciones',
            'notaLegal' => 'Las condiciones de becas y ayudas dependen de la normativa vigente y de estudio previo. Consulta con secretaría los requisitos de tu ciclo.',
        ]],
        ['tipo' => 'prematricula_cta', 'contenido' => ['variante' => 'centrado']],
        ['tipo' => 'contacto'],
    ],
];
