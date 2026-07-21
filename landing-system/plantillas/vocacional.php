<?php
// Plantilla "Vocacional" — campaña FP audaz: hero oscuro a pantalla completa,
// acento naranja intenso, titulares grandes. Pensada para captación de alumnado.
return [
    'slug'        => 'vocacional',
    'nombre'      => 'Vocacional',
    'descripcion' => 'Diseño audaz de campaña: hero oscuro a pantalla completa, naranja intenso y titulares grandes. Enfocado a captar matrícula.',
    'thumbnail'   => 'plantilla-vocacional.svg',
    'colorAcento' => '#ea580c',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'variante'  => 'fondo',
            'eyebrow'   => 'Matrícula abierta',
            'titulo'    => 'Fórmate para el empleo real',
            'subtitulo' => 'Elige un ciclo con futuro, aprende haciendo y sal con experiencia en empresa desde el primer día.',
            'botonTexto'=> 'Reserva tu plaza', 'botonUrl' => '#prematricula_cta',
        ]],
        ['tipo' => 'prematricula_cta', 'contenido' => ['variante' => 'banner']],
        ['tipo' => 'oferta_formativa', 'contenido' => [
            'titulo' => 'Elige tu ciclo', 'botonTexto' => 'Quiero este ciclo',
        ]],
        ['tipo' => 'becas_financiacion', 'contenido' => [
            'titulo' => '¿Y si no puedo pagarlo todo de golpe?',
            'subtitulo' => 'Fracciona tu matrícula o infórmate de las becas disponibles — no dejes que el precio te frene.',
            'items' => [
                ['icono' => 'fa-piggy-bank', 'titulo' => 'Fracciona en cuotas', 'texto' => 'Sin intereses, adaptado a tu bolsillo. Empieza ya y paga mes a mes.'],
                ['icono' => 'fa-money-bill-wave', 'titulo' => 'Becas al momento', 'texto' => 'Te decimos en la primera visita si tu perfil encaja con alguna beca disponible.'],
                ['icono' => 'fa-star', 'titulo' => 'Descuento por matrícula anticipada', 'texto' => 'Reserva tu plaza ya y ahorra en el importe total del ciclo.'],
            ],
            'botonTexto' => 'Quiero que me llamen',
        ]],
        ['tipo' => 'empresas', 'contenido' => ['variante' => 'marquee']],
        ['tipo' => 'testimonios', 'contenido' => ['variante' => 'tarjetas']],
        ['tipo' => 'equipo_docente', 'contenido' => ['variante' => 'carrusel']],
        ['tipo' => 'cifras', 'contenido' => ['variante' => 'minimalista']],
        ['tipo' => 'faq', 'contenido' => ['variante' => 'acordeon']],
        ['tipo' => 'contacto', 'contenido' => ['modoVisualizacion' => 'separado']],
    ],
];
