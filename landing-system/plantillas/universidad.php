<?php
// Plantilla Universidad (Basada en estilo Deusto)

return [
    'slug' => 'universidad',
    'nombre' => 'Universidad',
    'descripcion' => 'Diseño corporativo y académico. Uso de mayúsculas, bordes rectos y sombras sólidas.',
    'thumbnail' => 'plantilla-universidad.svg', 
    'colorAcento' => '#003c8f', // Azul universidad clásico
    
    'secciones' => [
        [
            'tipo' => 'hero',
            'contenido' => [
                'variante' => 'fondo',
                'eyebrow' => 'Tu futuro profesional',
                'titulo' => 'FORMAMOS PERSONAS PARA TRANSFORMAR EL MUNDO',
                'subtitulo' => 'Descubre una formación práctica y especializada que te preparará para los retos del mañana.',
                'videoFondo' => 'https://www.w3schools.com/html/mov_bbb.mp4', // Placeholder video
                'imagen' => '',
                'botonTexto' => 'NUESTRA OFERTA',
                'botonUrl' => '#oferta_formativa',
                'boton2Texto' => 'SOLICITAR INFO',
                'boton2Url' => '#contacto',
            ]
        ],
        [
            'tipo' => 'cifras',
            'contenido' => [
                'items' => [
                    ['numero' => '365', 'sufijo' => ' DIAS', 'etiqueta' => 'DE INNOVACIÓN'],
                    ['numero' => '12', 'sufijo' => 'K', 'etiqueta' => 'ALUMNOS ACTIVOS'],
                    ['numero' => '4', 'sufijo' => 'M€', 'etiqueta' => 'EN BECAS']
                ]
            ]
        ],
        [
            'tipo' => 'porque_elegirnos',
            'contenido' => [
                'titulo' => 'FORMACIÓN Y VALORES',
                'subtitulo' => 'Educamos en la excelencia académica y humana.',
                'items' => [
                    ['icono' => 'fa-graduation-cap', 'titulo' => 'EXCELENCIA', 'texto' => 'Calidad educativa reconocida y adaptada al mercado profesional actual.'],
                    ['icono' => 'fa-users', 'titulo' => 'PERSONAS', 'texto' => 'Acompañamiento personalizado en tu desarrollo integral.'],
                    ['icono' => 'fa-briefcase', 'titulo' => 'EMPLEABILIDAD', 'texto' => 'Estrecha relación con el tejido empresarial y prácticas garantizadas.']
                ]
            ]
        ],
        [
            'tipo' => 'oferta_formativa',
            'contenido' => [
                'titulo' => 'NUESTRA OFERTA DE ESTUDIOS',
                'subtitulo' => 'Programas de formación profesional diseñados para la especialización técnica.',
                'botonTexto' => 'VER DETALLES',
                'mostrarPrecio' => 'no'
            ]
        ],
        [
            'tipo' => 'instalaciones',
            'contenido' => [
                'titulo' => 'NUESTROS CAMPUS',
                'subtitulo' => 'Entornos de aprendizaje equipados con la última tecnología.',
                'items' => []
            ]
        ],
        [
            'tipo' => 'empresas',
            'contenido' => [
                'titulo' => 'CONECTADOS CON EL MUNDO EMPRESARIAL',
                'texto' => 'Trabajamos con líderes del sector para asegurar tu inserción laboral.',
                'items' => []
            ]
        ],
        [
            'tipo' => 'contacto',
            'contenido' => [
                'modoVisualizacion' => 'integrado',
                'titulo' => '¿QUIERES SABER MÁS?',
                'texto' => 'Contacta con nuestro equipo de admisiones y resuelve todas tus dudas.',
                'mostrarFormulario' => 'si',
                'mostrarMapa' => 'no'
            ]
        ]
    ]
];
