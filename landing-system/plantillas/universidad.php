<?php
// Plantilla "Universidad" — corporativo académico: mayúsculas (vía CSS),
// bordes rectos y sombras sólidas offset. Solo define estructura y copy
// genérico; el contenido real (cifras, testimonios, FAQ…) se rellena desde
// el panel. Las mayúsculas las aplica el tema (text-transform), no el texto.
return [
    'slug'        => 'universidad',
    'nombre'      => 'Universidad',
    'descripcion' => 'Diseño corporativo y académico: bordes rectos, sombras sólidas y titulares en mayúsculas. Transmite solidez institucional.',
    'thumbnail'   => 'plantilla-universidad.svg',
    'colorAcento' => '#003c8f', // Azul universidad clásico

    'secciones' => [
        ['tipo' => 'hero', 'contenido' => [
            'variante'    => 'fondo',
            'eyebrow'     => 'Tu futuro profesional',
            'titulo'      => 'Formamos personas para transformar el mundo',
            'subtitulo'   => 'Una formación práctica y especializada que te prepara para los retos del mañana.',
            'botonTexto'  => 'Nuestra oferta',
            'botonUrl'    => '#oferta_formativa',
            'boton2Texto' => 'Solicitar información',
            'boton2Url'   => '#contacto',
        ]],
        ['tipo' => 'cifras', 'contenido' => ['variante' => 'tarjetas']],
        ['tipo' => 'porque_elegirnos', 'contenido' => [
            'titulo'    => 'Formación y valores',
            'subtitulo' => 'Educamos en la excelencia académica y humana.',
        ]],
        ['tipo' => 'oferta_formativa', 'contenido' => [
            'titulo'        => 'Nuestra oferta de estudios',
            'subtitulo'     => 'Programas de formación profesional para la especialización técnica.',
            'botonTexto'    => 'Ver detalles',
        ]],
        ['tipo' => 'fp_dual', 'contenido' => [
            'variante' => 'reverso',
            'titulo'   => 'Formación profesional dual',
            'texto'    => 'Estudia y trabaja a la vez: combina la formación teórica con estancias remuneradas en empresas.',
        ]],
        ['tipo' => 'instalaciones', 'contenido' => [
            'titulo'    => 'Nuestros campus',
            'subtitulo' => 'Entornos de aprendizaje equipados con la última tecnología.',
        ]],
        ['tipo' => 'testimonios', 'contenido' => [
            'variante' => 'carrusel',
            'titulo'   => 'Alumnos destacados',
        ]],
        ['tipo' => 'faq', 'contenido' => [
            'variante' => 'acordeon',
            'titulo'   => 'Preguntas frecuentes',
        ]],
        ['tipo' => 'empresas', 'contenido' => [
            'titulo' => 'Conectados con el mundo empresarial',
            'texto'  => 'Trabajamos con líderes del sector para asegurar tu inserción laboral.',
        ]],
        ['tipo' => 'prematricula_cta', 'contenido' => [
            'variante'   => 'banner',
            'titulo'     => 'Reserva tu plaza ahora',
            'texto'      => 'Inicia tu proceso de admisión online de forma rápida y sencilla.',
            'botonTexto' => 'Iniciar admisión',
            'notaPlazo'  => 'Últimas plazas disponibles',
        ]],
        ['tipo' => 'contacto', 'contenido' => [
            'modoVisualizacion' => 'integrado',
            'titulo'            => '¿Quieres saber más?',
            'texto'             => 'Contacta con nuestro equipo de admisiones y resuelve todas tus dudas.',
            'mostrarFormulario' => 'si',
            'mostrarMapa'       => 'no',
        ]],
    ],
];
