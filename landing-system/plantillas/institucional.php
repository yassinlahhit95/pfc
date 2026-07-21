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
        ['tipo' => 'cifras', 'contenido' => ['variante' => 'tarjetas']],
        ['tipo' => 'oferta_formativa'],
        ['tipo' => 'porque_elegirnos', 'contenido' => ['variante' => 'tarjetas']],
        ['tipo' => 'equipo_docente'],
        ['tipo' => 'fp_dual', 'contenido' => ['variante' => 'split']],
        ['tipo' => 'instalaciones'],
        // Reutiliza el tipo "empresas" (solo logos + enlace, ya genérico)
        // como franja de reconocimiento oficial — encaja mejor en un centro
        // público que otro bloque de "empresas colaboradoras" duplicado.
        ['tipo' => 'empresas', 'contenido' => [
            'titulo' => 'Reconocimiento oficial',
            'texto'  => 'Centro autorizado para impartir enseñanzas oficiales de Formación Profesional, con validez en toda España y la Unión Europea.',
        ]],
        // Copy pensada para un centro público: la matrícula de FP pública es
        // gratuita o de coste mínimo en la mayoría de comunidades, así que el
        // objetivo aquí es transparencia sobre becas/ayudas — no "financiación
        // en cuotas", que es el enfoque de una academia privada (ver el
        // texto por defecto de esta sección en engine/secciones.php).
        ['tipo' => 'becas_financiacion', 'contenido' => [
            'titulo' => 'Ayudas y transporte',
            'subtitulo' => 'La Formación Profesional pública es gratuita o de coste muy reducido — esto es lo que puede ayudarte además.',
            'items' => [
                ['icono' => 'fa-shield-halved', 'titulo' => 'Matrícula gratuita', 'texto' => 'La FP pública no tiene coste de matrícula en la mayoría de comunidades autónomas.'],
                ['icono' => 'fa-money-bill-wave', 'titulo' => 'Becas del Ministerio', 'texto' => 'Consulta si tu situación económica y académica te da acceso a las becas generales de estudio.'],
                ['icono' => 'fa-piggy-bank', 'titulo' => 'Ayudas de transporte y material', 'texto' => 'Infórmate sobre las ayudas de desplazamiento y material didáctico disponibles en tu comunidad.'],
            ],
            'botonTexto' => 'Consultar en secretaría',
            'notaLegal' => 'Las condiciones de acceso a becas y ayudas dependen de la normativa de cada convocatoria. Consulta con secretaría los requisitos específicos.',
        ]],
        ['tipo' => 'prematricula_cta', 'contenido' => ['variante' => 'centrado']],
        ['tipo' => 'contacto'],
    ],
];
