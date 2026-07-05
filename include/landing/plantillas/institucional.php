<?php
// Plantilla "Institucional" — estilo CIFP moderno: azul corporativo, tarjetas
// con sombra suave, hero split con imagen. Pensada para centros públicos.
return [
    'slug'        => 'institucional',
    'nombre'      => 'Institucional',
    'descripcion' => 'Diseño corporativo moderno en azul, ideal para centros públicos (IES/CIFP). Hero con imagen lateral, tarjetas limpias y aire institucional.',
    'thumbnail'   => 'plantilla-institucional.svg',
    'colorAcento' => '#1d4ed8',
    'secciones'   => [
        ['tipo' => 'hero', 'contenido' => [
            'variante'  => 'split',
            'eyebrow'   => 'Centro Integrado de Formación Profesional',
            'titulo'    => 'Formación oficial que abre puertas',
            'subtitulo' => 'Ciclos formativos de grado medio y superior con prácticas en empresas colaboradoras y profesorado especialista.',
        ]],
        ['tipo' => 'cifras'],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'porque_elegirnos'],
        ['tipo' => 'fp_dual'],
        ['tipo' => 'instalaciones'],
        ['tipo' => 'prematricula_cta'],
        ['tipo' => 'contacto'],
    ],
];
