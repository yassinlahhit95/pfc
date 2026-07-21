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
        ['tipo' => 'equipo_docente', 'contenido' => [
            'titulo'    => 'Nuestro claustro',
            'subtitulo' => 'Profesionales en activo al frente de cada módulo.',
        ]],
        ['tipo' => 'testimonios', 'contenido' => [
            'variante' => 'carrusel',
            'titulo'   => 'Alumnos destacados',
        ]],
        ['tipo' => 'faq', 'contenido' => [
            'variante' => 'acordeon',
            'titulo'   => 'Preguntas frecuentes',
        ]],
        // Nota: no se duplica el tipo "empresas" para separar "socios de FCT"
        // de "acreditaciones oficiales" — secciones/empresas.php usa un id
        // HTML fijo (id="empresas"), así que una segunda instancia del mismo
        // tipo generaría un id duplicado (HTML inválido y el ancla del menú
        // solo llegaría a la primera). Se combina el mensaje en una sola
        // sección en su lugar.
        ['tipo' => 'empresas', 'contenido' => [
            'titulo' => 'Conectados con el mundo empresarial',
            'texto'  => 'Trabajamos con líderes del sector para asegurar tu inserción laboral — con titulaciones oficiales reconocidas por la administración educativa, con validez en toda España y la Unión Europea.',
        ]],
        // Copy con tono de prestigio corporativo, no de "academia económica"
        // (ver el texto por defecto de esta sección en engine/secciones.php).
        ['tipo' => 'becas_financiacion', 'contenido' => [
            'titulo'    => 'Invierte en tu futuro con flexibilidad',
            'subtitulo' => 'Estudiar aquí es una decisión seria — por eso ofrecemos condiciones de pago igual de serias.',
            'items' => [
                ['icono' => 'fa-piggy-bank', 'titulo' => 'Financiación a medida', 'texto' => 'Planes de pago flexibles adaptados a tu situación, sin sorpresas.'],
                ['icono' => 'fa-shield-halved', 'titulo' => 'Becas de excelencia', 'texto' => 'Reconocemos el talento y el esfuerzo con becas propias del centro.'],
                ['icono' => 'fa-money-bill-wave', 'titulo' => 'Becas y ayudas oficiales', 'texto' => 'Te asesoramos sobre las convocatorias públicas a las que puedas optar.'],
            ],
            'botonTexto' => 'Solicitar información',
            'notaLegal' => 'Las condiciones de becas y financiación están sujetas a estudio previo y a la normativa de cada convocatoria. Consulta con admisiones.',
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
