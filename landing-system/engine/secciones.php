<?php
// ══════════════════════════════════════════════════════════════════════
// CATÁLOGO DE TIPOS DE SECCIÓN DE LA LANDING
// ══════════════════════════════════════════════════════════════════════
// Única fuente de verdad para:
//   - el constructor (genera los formularios desde este schema, vía json_encode)
//   - el saneado server-side (landing_sanear_contenido)
//   - el contenido por defecto al añadir una sección
//
// Tipos de campo: text | textarea | select | imagen | color | lista
//   lista → 'subcampos' (schema anidado, un nivel) + 'max' (nº máximo de items)
//   imagen → guarda solo el nombre de archivo dentro de public/uploads/landing/
//
// El escapado NO se hace aquí: se hace al renderizar con Security::escapeHtml().

// URL pública de una imagen subida al constructor ('' si no hay imagen)
function landing_img_url($nombre): string {
    $nombre = trim((string)$nombre);
    if ($nombre === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $nombre) || str_starts_with($nombre, '/')) {
        return $nombre;
    }
    return '/public/uploads/landing/' . basename($nombre);
}

// Valida un enlace admin-configurable antes de usarlo en un atributo href:
// solo se permiten anclas internas (#...), rutas relativas (/...) o URLs
// http(s) absolutas. Cualquier otro esquema (javascript:, data:, vbscript:...)
// se descarta y se usa $fallback — así una sección nunca puede convertirse
// en un vector de XSS a través de un campo de enlace.
function landing_url_segura($url, string $fallback = '#'): string {
    $url = trim((string)($url ?? ''));
    if ($url === '') return $fallback;
    if ($url[0] === '#') return $url;
    if (preg_match('#^https?://#i', $url)) return $url;
    // Ruta relativa, pero no protocolo-relativa ("//dominio-externo.com")
    if ($url[0] === '/' && (!isset($url[1]) || $url[1] !== '/')) return $url;
    return $fallback;
}

// Iconos Font Awesome permitidos en campos de tipo select 'icono'
function landing_iconos_permitidos(): array {
    return [
        'fa-graduation-cap' => 'Birrete',
        'fa-briefcase'      => 'Maletín',
        'fa-users'          => 'Personas',
        'fa-laptop-code'    => 'Portátil',
        'fa-building'       => 'Edificio',
        'fa-handshake'      => 'Apretón de manos',
        'fa-award'          => 'Premio',
        'fa-chalkboard-teacher' => 'Profesor',
        'fa-microscope'     => 'Microscopio',
        'fa-tools'          => 'Herramientas',
        'fa-heart'          => 'Corazón',
        'fa-globe'          => 'Globo',
        'fa-rocket'         => 'Cohete',
        'fa-shield-halved'  => 'Escudo',
        'fa-star'           => 'Estrella',
    ];
}

function landing_tipos(): array {
    static $tipos = null;
    if ($tipos !== null) return $tipos;

    $iconos = landing_iconos_permitidos();

    $tipos = [

        'hero' => [
            'nombre' => 'Portada (Hero)',
            'descripcion' => 'La primera pantalla de la web: titular grande, imagen o vídeo de fondo y botones principales.',
            'icono'  => 'fa-image',
            'menu'   => null,
            'campos' => [
                'variante'   => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                 'opciones' => ['fondo' => 'Imagen de fondo', 'split' => 'Texto + imagen', 'minimal' => 'Minimalista', 'promo' => 'Campaña con cinta promocional']],
                'eyebrow'    => ['tipo' => 'text',     'etiqueta' => 'Texto superior (pequeño)', 'max' => 80],
                'titulo'     => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo'  => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo', 'max' => 300],
                'imagen'     => ['tipo' => 'imagen',   'etiqueta' => 'Imagen (lateral para split/campaña)'],
                'videoFondo' => ['tipo' => 'video',    'etiqueta' => 'Vídeo de fondo (MP4, opcional)'],
                'fondoParallax'=>['tipo' => 'imagen',  'etiqueta' => 'Imagen de fondo Parallax (si no hay vídeo)'],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón principal', 'max' => 40],
                'botonUrl'   => ['tipo' => 'url',      'etiqueta' => 'Enlace del botón principal', 'max' => 255],
                'boton2Texto'=> ['tipo' => 'text',     'etiqueta' => 'Texto del botón secundario', 'max' => 40],
                'boton2Url'  => ['tipo' => 'url',      'etiqueta' => 'Enlace del botón secundario', 'max' => 255],
                'promoTexto' => ['tipo' => 'text',     'etiqueta' => 'Cinta promocional (solo estilo «Campaña»; ej: financiación, descuento)', 'max' => 140],
                'promoUrl'   => ['tipo' => 'url',      'etiqueta' => 'Enlace de la cinta promocional (opcional)', 'max' => 255],
            ],
            'defecto' => [
                'variante' => 'fondo', 'eyebrow' => 'Formación Profesional',
                'titulo' => 'Tu futuro profesional empieza aquí',
                'subtitulo' => 'Formación Profesional oficial con prácticas en empresas líderes del sector.',
                'imagen' => '', 'videoFondo' => '', 'fondoParallax' => '', 'botonTexto' => 'Ver ciclos', 'botonUrl' => '#oferta_formativa',
                'boton2Texto' => 'Contacto', 'boton2Url' => '#contacto',
                'promoTexto' => '', 'promoUrl' => '',
            ],
        ],

        'cta_secundario' => [
            'nombre' => 'CTA / Banner secundario',
            'descripcion' => 'Franja destacada con un mensaje corto y un botón, para anunciar algo puntual (jornada de puertas abiertas, plazo, etc.).',
            'icono'  => 'fa-bullhorn',
            'menu'   => null,
            'campos' => [
                'titulo'     => ['tipo' => 'text', 'etiqueta' => 'Texto del anuncio', 'max' => 120, 'requerido' => true],
                'botonTexto' => ['tipo' => 'text', 'etiqueta' => 'Texto del botón', 'max' => 40],
                'botonUrl'   => ['tipo' => 'url',  'etiqueta' => 'Enlace', 'max' => 255],
            ],
            'defecto' => [
                'titulo' => '¡Jornada de puertas abiertas el próximo viernes!',
                'botonTexto' => 'Inscribirse', 'botonUrl' => '#contacto'
            ],
        ],

        'galeria' => [
            'nombre' => 'Galería Masonry',
            'descripcion' => 'Cuadrícula de fotos del centro con efecto lightbox (ampliar) al hacer clic.',
            'icono'  => 'fa-images',
            'menu'   => 'Galería',
            'campos' => [
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo', 'max' => 300],
                'items'     => ['tipo' => 'lista', 'etiqueta' => 'Fotos', 'max' => 12, 'subcampos' => [
                    'imagen' => ['tipo' => 'imagen', 'etiqueta' => 'Foto', 'requerido' => true],
                    'texto'  => ['tipo' => 'text', 'etiqueta' => 'Pie de foto', 'max' => 80],
                ]],
            ],
            'defecto' => [
                'titulo' => 'Nuestro día a día', 'subtitulo' => 'Echa un vistazo a la vida en el centro.', 'items' => []
            ],
        ],

        'noticias' => [
            'nombre' => 'Últimas Noticias (Blog)',
            'descripcion' => 'Muestra las últimas entradas publicadas del blog, con enlace para ver todas.',
            'icono'  => 'fa-newspaper',
            'menu'   => 'Blog',
            'campos' => [
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo', 'max' => 300],
                'numPosts'  => ['tipo' => 'select',   'etiqueta' => 'Nº de entradas a mostrar',
                                'opciones' => ['n3' => '3 entradas', 'n6' => '6 entradas']],
                'botonTexto'=> ['tipo' => 'text', 'etiqueta' => 'Texto del botón "Ver todas"', 'max' => 40],
            ],
            'defecto' => [
                'titulo' => 'Actualidad', 'subtitulo' => 'Mantente informado de las últimas novedades del centro.',
                'numPosts' => 'n3', 'botonTexto' => 'Ver todas las noticias'
            ],
        ],

        'video_presentacion' => [
            'nombre' => 'Vídeo de Presentación',
            'descripcion' => 'Bloque con un vídeo (MP4 o URL) junto a un texto de presentación del centro.',
            'icono'  => 'fa-circle-play',
            'menu'   => 'Vídeo',
            'campos' => [
                'variante'   => ['tipo' => 'select',   'etiqueta' => 'Estilo visual',
                                 'opciones' => ['split' => 'Texto + Vídeo a un lado', 'centrado' => 'Centrado (Ancho completo)']],
                'orientacion'=> ['tipo' => 'select',   'etiqueta' => 'Alineación de vídeo (para estilo split)',
                                 'opciones' => ['derecha' => 'Vídeo a la derecha', 'izquierda' => 'Vídeo a la izquierda']],
                'eyebrow'    => ['tipo' => 'text',     'etiqueta' => 'Texto superior (pequeño)', 'max' => 80],
                'titulo'     => ['tipo' => 'text',     'etiqueta' => 'Título principal', 'max' => 120, 'requerido' => true],
                'subtitulo'  => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo o descripción corta', 'max' => 300],
                'parrafo'    => ['tipo' => 'textarea', 'etiqueta' => 'Párrafo detallado', 'max' => 1000],
                'videoUrl'   => ['tipo' => 'video',    'etiqueta' => 'Vídeo (MP4, subido o URL externa)', 'requerido' => true],
                'posterUrl'  => ['tipo' => 'imagen',   'etiqueta' => 'Imagen miniatura (Poster, opcional)'],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón', 'max' => 40],
                'botonUrl'   => ['tipo' => 'url',      'etiqueta' => 'Enlace del botón', 'max' => 255],
            ],
            'defecto' => [
                'variante' => 'split', 'orientacion' => 'derecha', 'eyebrow' => 'Conoce nuestro centro',
                'titulo' => 'Una experiencia educativa única',
                'subtitulo' => 'Descubre en este vídeo cómo preparamos a nuestros estudiantes para los retos profesionales del futuro.',
                'parrafo' => 'Nuestras instalaciones cuentan con tecnología de vanguardia y espacios diseñados para el aprendizaje práctico y colaborativo.',
                'videoUrl' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'posterUrl' => '', 'botonTexto' => 'Visitar instalaciones', 'botonUrl' => '#instalaciones',
            ],
        ],

        'hero_slider' => [
            'nombre' => 'Hero Slider',
            'descripcion' => 'Como la Portada, pero con varias diapositivas que rotan automáticamente.',
            'icono'  => 'fa-images',
            'menu'   => null,
            'campos' => [
                'eyebrow'    => ['tipo' => 'text',     'etiqueta' => 'Texto superior general', 'max' => 80],
                'slides'     => ['tipo' => 'lista', 'etiqueta' => 'Diapositivas', 'max' => 5, 'subcampos' => [
                    'imagen'    => ['tipo' => 'imagen',   'etiqueta' => 'Imagen de fondo', 'requerido' => true],
                    'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                    'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo', 'max' => 300],
                ]],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón principal', 'max' => 40],
                'botonUrl'   => ['tipo' => 'url',      'etiqueta' => 'Enlace del botón principal', 'max' => 255],
                'autoplay'   => ['tipo' => 'select',   'etiqueta' => 'Autoplay', 'opciones' => ['si' => 'Sí', 'no' => 'No']],
            ],
            'defecto' => [
                'eyebrow' => 'Centro de Excelencia',
                'slides' => [
                    ['imagen' => '', 'titulo' => 'Innovación y Tecnología', 'subtitulo' => 'Aprende con las mejores herramientas del mercado.'],
                    ['imagen' => '', 'titulo' => 'Prácticas en Empresas', 'subtitulo' => 'Accede a nuestro programa de prácticas exclusivas.'],
                ],
                'botonTexto' => 'Más información', 'botonUrl' => '#oferta_formativa',
                'autoplay' => 'si',
            ],
        ],

        'oferta_formativa' => [
            'nombre' => 'Oferta formativa',
            'descripcion' => 'Tarjetas de tus ciclos o programas: foto, título, descripción y precio, personalizables una a una.',
            'icono'  => 'fa-graduation-cap',
            'menu'   => 'Ciclos',
            'campos' => [
                'variante'      => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                    'opciones' => ['grid' => 'Cuadrícula de tarjetas', 'lista' => 'Vista de lista compacta']],
                'columnas'      => ['tipo' => 'select',   'etiqueta' => 'Tarjetas por fila (cuadrícula)',
                                    'opciones' => ['cols-4' => '4 por fila', 'cols-3' => '3 por fila']],
                'titulo'        => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo'     => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'botonTexto'    => ['tipo' => 'text',     'etiqueta' => 'Texto del botón por tarjeta', 'max' => 40],
                'items'         => ['tipo' => 'lista', 'etiqueta' => 'Ciclos / programas', 'max' => 12, 'subcampos' => [
                    'imagen'  => ['tipo' => 'imagen',   'etiqueta' => 'Foto'],
                    'etiqueta'=> ['tipo' => 'text',     'etiqueta' => 'Etiqueta (ej: Grado Medio)', 'max' => 60],
                    'titulo'  => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                    'texto'   => ['tipo' => 'textarea', 'etiqueta' => 'Descripción', 'max' => 300],
                    'precio'  => ['tipo' => 'text',     'etiqueta' => 'Precio (ej: 1.200 € /curso, o vacío para ocultarlo)', 'max' => 60],
                    'botonUrl'=> ['tipo' => 'url',      'etiqueta' => 'Enlace del botón (opcional, si no se rellena usa el general)', 'max' => 255],
                    'cicloSlug'=>['tipo' => 'text',     'etiqueta' => 'Enlazar a ficha del catálogo de ciclos (slug, opcional — si se rellena, tiene prioridad sobre el enlace del botón)', 'max' => 180],
                ]],
            ],
            'defecto' => [
                'variante' => 'grid', 'columnas' => 'cols-4', 'titulo' => 'Nuestra oferta formativa',
                'subtitulo' => 'Ciclos formativos oficiales adaptados a las profesiones con más demanda.',
                'botonTexto' => 'Solicitar plaza', 'items' => [],
            ],
        ],

        'prematricula_cta' => [
            'nombre' => 'Admisión (Pre-matrícula)',
            'descripcion' => 'Llamada a la acción para que los visitantes empiecen su pre-matrícula.',
            'icono'  => 'fa-user-plus',
            'menu'   => 'Admisión',
            'campos' => [
                'variante'   => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                 'opciones' => ['centrado' => 'Caja centrada', 'banner' => 'Banner ancho completo']],
                'titulo'     => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'      => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 400],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón', 'max' => 40],
                'notaPlazo'  => ['tipo' => 'text',     'etiqueta' => 'Nota de plazo (opcional)', 'max' => 120],
            ],
            'defecto' => [
                'variante' => 'centrado',
                'titulo' => 'Reserva tu plaza para el próximo curso',
                'texto' => 'Realiza tu pre-matrícula online en menos de 10 minutos. Nuestro equipo revisará tu solicitud y te contactará con los siguientes pasos.',
                'botonTexto' => 'Iniciar pre-matrícula', 'notaPlazo' => 'Plazo abierto — plazas limitadas',
            ],
        ],

        'porque_elegirnos' => [
            'nombre' => 'Por qué elegirnos',
            'descripcion' => 'Lista de puntos fuertes del centro, cada uno con icono, título y texto breve.',
            'icono'  => 'fa-circle-check',
            'menu'   => 'El centro',
            'campos' => [
                'variante'  => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                'opciones' => ['grid' => 'Cuadrícula', 'tarjetas' => 'Tarjetas estructuradas', 'lateral' => 'Lista lateral']],
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'items'     => ['tipo' => 'lista', 'etiqueta' => 'Ventajas', 'max' => 6, 'subcampos' => [
                    'icono'  => ['tipo' => 'select',   'etiqueta' => 'Icono', 'opciones' => $iconos],
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80, 'requerido' => true],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 250],
                ]],
            ],
            'defecto' => [
                'variante' => 'grid', 'titulo' => '¿Por qué estudiar con nosotros?', 'subtitulo' => '',
                'items' => [
                    ['icono' => 'fa-briefcase', 'titulo' => 'Prácticas garantizadas', 'texto' => 'Convenios con empresas del sector para que hagas prácticas reales desde el primer curso.'],
                    ['icono' => 'fa-chalkboard-teacher', 'titulo' => 'Profesorado experto', 'texto' => 'Docentes con experiencia profesional activa en su especialidad.'],
                    ['icono' => 'fa-award', 'titulo' => 'Titulación oficial', 'texto' => 'Títulos oficiales de Formación Profesional válidos en toda España y la UE.'],
                ],
            ],
        ],

        'cifras' => [
            'nombre' => 'Cifras clave',
            'descripcion' => 'Números destacados con contador animado (años de experiencia, alumnos, ciclos...).',
            'icono'  => 'fa-chart-simple',
            'menu'   => null,
            'campos' => [
                'variante' => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                               'opciones' => ['horizontal' => 'Lista horizontal', 'tarjetas' => 'Tarjetas', 'minimalista' => 'Minimalista']],
                'items' => ['tipo' => 'lista', 'etiqueta' => 'Cifras', 'max' => 4, 'subcampos' => [
                    'numero'   => ['tipo' => 'text', 'etiqueta' => 'Número', 'max' => 10, 'requerido' => true],
                    'sufijo'   => ['tipo' => 'text', 'etiqueta' => 'Sufijo (%, +, …)', 'max' => 5],
                    'etiqueta' => ['tipo' => 'text', 'etiqueta' => 'Etiqueta', 'max' => 60, 'requerido' => true],
                ]],
            ],
            'defecto' => [
                'variante' => 'horizontal',
                'items' => [
                    ['numero' => '95', 'sufijo' => '%', 'etiqueta' => 'Inserción laboral'],
                    ['numero' => '30', 'sufijo' => '+', 'etiqueta' => 'Empresas colaboradoras'],
                    ['numero' => '20', 'sufijo' => '+', 'etiqueta' => 'Años de experiencia'],
                    ['numero' => '500', 'sufijo' => '+', 'etiqueta' => 'Alumnos titulados'],
                ],
            ],
        ],

        'fp_dual' => [
            'nombre' => 'FP Dual',
            'descripcion' => 'Bloque dedicado a explicar la formación dual, con imagen y lista de ventajas.',
            'icono'  => 'fa-building',
            'menu'   => 'FP Dual',
            'campos' => [
                'variante' => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                               'opciones' => ['split' => 'Texto izq, Imagen der', 'reverso' => 'Imagen izq, Texto der']],
                'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 500],
                'imagen' => ['tipo' => 'imagen',   'etiqueta' => 'Imagen'],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Ventajas', 'max' => 4, 'subcampos' => [
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80, 'requerido' => true],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 200],
                ]],
            ],
            'defecto' => [
                'variante' => 'split',
                'titulo' => 'Formación Profesional Dual',
                'texto' => 'Estudia y trabaja a la vez: la FP Dual combina la formación en el aula con estancias remuneradas en empresas colaboradoras.',
                'imagen' => '',
                'items' => [
                    ['titulo' => 'Experiencia real', 'texto' => 'Aprende trabajando en empresas del sector desde el primer año.'],
                    ['titulo' => 'Remuneración', 'texto' => 'Recibe una compensación económica durante tu estancia en la empresa.'],
                ],
            ],
        ],

        'empresas' => [
            'nombre' => 'Empresas colaboradoras',
            'descripcion' => 'Carrusel o cuadrícula con los logotipos de las empresas colaboradoras.',
            'icono'  => 'fa-handshake',
            'menu'   => 'Empresas',
            'campos' => [
                'variante' => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                               'opciones' => ['grid' => 'Cuadrícula estática', 'marquee' => 'Carrusel animado infinito']],
                'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 300],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Empresas', 'max' => 12, 'subcampos' => [
                    'nombre' => ['tipo' => 'text',   'etiqueta' => 'Nombre', 'max' => 80, 'requerido' => true],
                    'logo'   => ['tipo' => 'imagen', 'etiqueta' => 'Logo'],
                    'url'    => ['tipo' => 'url',    'etiqueta' => 'Web (opcional)', 'max' => 255],
                ]],
            ],
            'defecto' => [
                'variante' => 'grid', 'titulo' => 'Empresas que confían en nosotros',
                'texto' => 'Nuestros alumnos realizan prácticas y encuentran empleo en empresas líderes.',
                'items' => [],
            ],
        ],

        'instalaciones' => [
            'nombre' => 'Instalaciones',
            'descripcion' => 'Galería de fotos de las instalaciones del centro, cada una con su título.',
            'icono'  => 'fa-school',
            'menu'   => 'Instalaciones',
            'campos' => [
                'variante'  => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                'opciones' => ['grid' => 'Cuadrícula estándar', 'mosaico' => 'Mosaico asimétrico']],
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'items'     => ['tipo' => 'lista', 'etiqueta' => 'Espacios', 'max' => 9, 'subcampos' => [
                    'imagen' => ['tipo' => 'imagen',   'etiqueta' => 'Foto', 'requerido' => true],
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Descripción', 'max' => 200],
                ]],
            ],
            'defecto' => [
                'variante' => 'grid', 'titulo' => 'Nuestras instalaciones',
                'subtitulo' => 'Espacios y equipamiento profesional para aprender con las mismas herramientas que usarás en tu trabajo.',
                'items' => [],
            ],
        ],

        'testimonios' => [
            'nombre' => 'Testimonios',
            'descripcion' => 'Opiniones de alumnos o familias, con nombre, ciclo y foto opcional.',
            'icono'  => 'fa-quote-left',
            'menu'   => 'Testimonios',
            'campos' => [
                'variante' => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                               'opciones' => ['tarjetas' => 'Cuadrícula de tarjetas', 'carrusel' => 'Carrusel horizontal animado']],
                'titulo' => ['tipo' => 'text', 'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Testimonios', 'max' => 8, 'subcampos' => [
                    'nombre' => ['tipo' => 'text',     'etiqueta' => 'Nombre', 'max' => 80, 'requerido' => true],
                    'rol'    => ['tipo' => 'text',     'etiqueta' => 'Ciclo / promoción', 'max' => 80],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Testimonio', 'max' => 400, 'requerido' => true],
                    'foto'   => ['tipo' => 'imagen',   'etiqueta' => 'Foto'],
                ]],
            ],
            'defecto' => [
                'variante' => 'tarjetas',
                'titulo' => 'Lo que dicen nuestros alumnos',
                'items' => [
                    ['nombre' => 'Laura G.', 'rol' => 'DAM · Promoción 2024', 'texto' => 'Gracias a las prácticas conseguí contrato en la misma empresa antes de terminar el ciclo.', 'foto' => ''],
                    ['nombre' => 'Carlos M.', 'rol' => 'ASIR · Promoción 2023', 'texto' => 'El profesorado te acompaña de verdad. Salí con un portfolio que me abrió muchas puertas.', 'foto' => ''],
                ],
            ],
        ],

        'faq' => [
            'nombre' => 'Preguntas frecuentes',
            'descripcion' => 'Lista de preguntas y respuestas, en formato lista, acordeón o cuadrícula.',
            'icono'  => 'fa-circle-question',
            'menu'   => null,
            'campos' => [
                'variante' => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                               'opciones' => ['lista' => 'Lista normal', 'acordeon' => 'Acordeón interactivo', 'grid' => 'Cuadrícula 2 columnas']],
                'titulo' => ['tipo' => 'text', 'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Preguntas', 'max' => 10, 'subcampos' => [
                    'pregunta'  => ['tipo' => 'text',     'etiqueta' => 'Pregunta', 'max' => 150, 'requerido' => true],
                    'respuesta' => ['tipo' => 'textarea', 'etiqueta' => 'Respuesta', 'max' => 600, 'requerido' => true],
                ]],
            ],
            'defecto' => [
                'variante' => 'lista',
                'titulo' => 'Preguntas frecuentes',
                'items' => [
                    ['pregunta' => '¿Qué requisitos necesito para matricularme?', 'respuesta' => 'Depende del ciclo: para grado medio, el título de ESO o equivalente; para grado superior, Bachillerato, un grado medio o prueba de acceso.'],
                    ['pregunta' => '¿Las prácticas en empresa están garantizadas?', 'respuesta' => 'Sí. Todos los ciclos incluyen formación en centros de trabajo con empresas colaboradoras del centro.'],
                ],
            ],
        ],

        'contacto' => [
            'nombre' => 'Contacto',
            'descripcion' => 'Formulario de contacto, datos del centro, horario y mapa.',
            'icono'  => 'fa-envelope',
            'menu'   => 'Contacto',
            'campos' => [
                'modoVisualizacion'=> ['tipo' => 'select',   'etiqueta' => 'Modo de visualización',
                                       'opciones' => ['integrado' => 'Integrado en la landing', 'separado' => 'Página separada']],
                'titulo'           => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'            => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 300],
                'mostrarFormulario'=> ['tipo' => 'select',   'etiqueta' => 'Mostrar formulario',
                                       'opciones' => ['si' => 'Sí', 'no' => 'No']],
                'textoHorario'     => ['tipo' => 'textarea', 'etiqueta' => 'Horario de atención', 'max' => 300],
                'mostrarMapa'      => ['tipo' => 'select',   'etiqueta' => 'Mostrar mapa', 'opciones' => ['si' => 'Sí', 'no' => 'No']],
                'iframeMapa'       => ['tipo' => 'html', 'etiqueta' => 'Código de inserción (Ve a Google Maps -> Compartir -> "Insertar un mapa" y copia el HTML)', 'max' => 1000],
            ],
            'defecto' => [
                'modoVisualizacion' => 'integrado',
                'titulo' => '¿Hablamos?',
                'texto' => 'Resolvemos tus dudas sobre ciclos, admisión, becas y convalidaciones.',
                'mostrarFormulario' => 'si',
                'textoHorario' => "Lunes a viernes: 9:00 – 14:00\nSecretaría: 9:00 – 13:00",
                'mostrarMapa' => 'no',
                'iframeMapa' => '',
            ],
        ],
    ];

    foreach ($tipos as $clave => &$tipo) {
        $tipo['campos'] = array_merge([
            'navVisible' => ['tipo' => 'select', 'etiqueta' => 'Mostrar en menú de navegación superior', 'opciones' => ['si' => 'Sí', 'no' => 'No']],
            'navTexto'   => ['tipo' => 'text', 'etiqueta' => 'Texto del enlace en el menú', 'max' => 30]
        ], $tipo['campos'], [
            'estilo_fondo'  => ['tipo' => 'color',  'etiqueta' => 'Color de fondo personalizado'],
            'estilo_texto'  => ['tipo' => 'color',  'etiqueta' => 'Color de texto personalizado'],
            'estilo_fuente' => ['tipo' => 'select', 'etiqueta' => 'Familia tipográfica', 
                                'opciones' => ['' => 'Por defecto (Tema)', 'system-ui, sans-serif' => 'System Sans-serif', 'Georgia, serif' => 'Serif clásica', 'monospace' => 'Monospace', '"Comic Sans MS", cursive' => 'Cursive', 'Arial, sans-serif' => 'Arial']],
            'estilo_tamano' => ['tipo' => 'select', 'etiqueta' => 'Tamaño de fuente', 
                                'opciones' => ['' => 'Normal', '0.9em' => 'Pequeño', '1.15em' => 'Grande', '1.3em' => 'Muy grande']]
        ]);
        
        $tipo['defecto']['navVisible'] = !empty($tipo['menu']) ? 'si' : 'no';
        $tipo['defecto']['navTexto']   = $tipo['menu'] ?? '';
        $tipo['defecto']['estilo_fondo']  = '';
        $tipo['defecto']['estilo_texto']  = '';
        $tipo['defecto']['estilo_fuente'] = '';
        $tipo['defecto']['estilo_tamano'] = '';
    }
    unset($tipo);

    return $tipos;
}

// ══════════════════════════════════════════════════════════════════════
// SANEADO SERVER-SIDE
// ══════════════════════════════════════════════════════════════════════

// Sanea el contenido de una sección contra su schema.
// Devuelve el array saneado, o un string de error si falta un campo requerido
// de primer nivel o el tipo no existe.
function landing_sanear_contenido(string $tipo, array $datos) {
    $tipos = landing_tipos();
    if (!isset($tipos[$tipo])) return 'Tipo de sección no reconocido.';

    $limpio = _landing_sanear_campos($tipos[$tipo]['campos'], $datos);

    foreach ($tipos[$tipo]['campos'] as $clave => $def) {
        if (!empty($def['requerido']) && $def['tipo'] !== 'lista' && trim((string)($limpio[$clave] ?? '')) === '') {
            return 'El campo «' . $def['etiqueta'] . '» es obligatorio.';
        }
    }
    return $limpio;
}

// Recorre un schema de campos y devuelve solo las claves conocidas, saneadas.
function _landing_sanear_campos(array $schema, array $datos): array {
    $limpio = [];
    foreach ($schema as $clave => $def) {
        $valor = $datos[$clave] ?? null;

        switch ($def['tipo']) {
            case 'text':
                $v = trim(strip_tags((string)($valor ?? '')));
                $limpio[$clave] = mb_substr($v, 0, $def['max'] ?? 255);
                break;

            case 'textarea':
                // Conserva saltos de línea; el render usa nl2br(escapeHtml()).
                $v = trim(strip_tags((string)($valor ?? '')));
                $limpio[$clave] = mb_substr($v, 0, $def['max'] ?? 1000);
                break;

            case 'html':
                // Código embed de mapa: solo se permite un <iframe> de Google
                // Maps, reconstruido desde cero con atributos seguros (nunca se
                // copian atributos del HTML pegado por el admin, así que no hay
                // forma de colar onload/srcdoc/etc.)
                $limpio[$clave] = _landing_sanear_iframe_mapa((string)($valor ?? ''));
                break;

            case 'url':
                $v = landing_url_segura(trim((string)($valor ?? '')), '');
                $limpio[$clave] = $v !== '' ? mb_substr($v, 0, $def['max'] ?? 255) : '';
                break;

            case 'select':
                $opciones = array_keys($def['opciones'] ?? []);
                $limpio[$clave] = in_array($valor, $opciones, true) ? $valor : ($opciones[0] ?? '');
                break;

            case 'imagen':
            case 'video':
                $valorStr = trim((string)($valor ?? ''));
                if ($valorStr === '') {
                    $limpio[$clave] = '';
                } elseif (preg_match('#^https?://#i', $valorStr) || str_starts_with($valorStr, '/')) {
                    $limpio[$clave] = $valorStr;
                } else {
                    $nombre = basename($valorStr);
                    if ($nombre !== '' && file_exists(__DIR__ . '/../../public/uploads/landing/' . $nombre)) {
                        $limpio[$clave] = $nombre;
                    } else {
                        $limpio[$clave] = '';
                    }
                }
                break;

            case 'color':
                $v = trim((string)($valor ?? ''));
                $limpio[$clave] = preg_match('/^#[0-9a-f]{6}$/i', $v) ? strtolower($v) : '';
                break;

            case 'lista':
                $items = is_array($valor) ? array_slice(array_values($valor), 0, $def['max'] ?? 10) : [];
                $limpiosItems = [];
                foreach ($items as $item) {
                    if (!is_array($item)) continue;
                    $itemLimpio = _landing_sanear_campos($def['subcampos'], $item);
                    // Descarta items a los que les falte algún subcampo requerido
                    $valido = true;
                    foreach ($def['subcampos'] as $subClave => $subDef) {
                        if (!empty($subDef['requerido']) && trim((string)($itemLimpio[$subClave] ?? '')) === '') {
                            $valido = false;
                            break;
                        }
                    }
                    if ($valido) $limpiosItems[] = $itemLimpio;
                }
                $limpio[$clave] = $limpiosItems;
                break;
        }
    }
    return $limpio;
}

// Solo permite un <iframe> de Google Maps ("Insertar un mapa" -> HTML) con
// atributos seguros. Se reconstruye el tag desde cero (src validado, width
// y height acotados) en vez de copiar los atributos del HTML pegado por el
// admin, así onload/onerror/srcdoc/class/id/etc. nunca pasan, sea cual sea
// el contenido pegado.
function _landing_sanear_iframe_mapa(string $html): string {
    if (!preg_match('/<iframe\b[^>]*\bsrc=["\']([^"\']+)["\'][^>]*>/i', $html, $m)) {
        return '';
    }
    $src = html_entity_decode($m[1], ENT_QUOTES);
    if (!preg_match('#^https://www\.google\.com/maps/embed[?/]#i', $src)) {
        return '';
    }
    $width  = 600;
    $height = 450;
    if (preg_match('/\bwidth=["\']?(\d+)/i', $m[0], $wm))  $width  = min((int)$wm[1], 2000);
    if (preg_match('/\bheight=["\']?(\d+)/i', $m[0], $hm)) $height = min((int)$hm[1], 2000);

    return '<iframe src="' . htmlspecialchars($src, ENT_QUOTES)
         . '" width="' . $width . '" height="' . $height
         . '" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>';
}
