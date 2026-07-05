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
    $nombre = basename(trim((string)$nombre));
    return $nombre === '' ? '' : '/public/uploads/landing/' . $nombre;
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
            'icono'  => 'fa-image',
            'menu'   => null,
            'campos' => [
                'variante'   => ['tipo' => 'select',   'etiqueta' => 'Estilo',
                                 'opciones' => ['fondo' => 'Imagen de fondo', 'split' => 'Texto + imagen', 'minimal' => 'Minimalista']],
                'eyebrow'    => ['tipo' => 'text',     'etiqueta' => 'Texto superior (pequeño)', 'max' => 80],
                'titulo'     => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo'  => ['tipo' => 'textarea', 'etiqueta' => 'Subtítulo', 'max' => 300],
                'imagen'     => ['tipo' => 'imagen',   'etiqueta' => 'Imagen'],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón principal', 'max' => 40],
                'botonUrl'   => ['tipo' => 'text',     'etiqueta' => 'Enlace del botón principal', 'max' => 255],
                'boton2Texto'=> ['tipo' => 'text',     'etiqueta' => 'Texto del botón secundario', 'max' => 40],
                'boton2Url'  => ['tipo' => 'text',     'etiqueta' => 'Enlace del botón secundario', 'max' => 255],
            ],
            'defecto' => [
                'variante' => 'fondo', 'eyebrow' => 'Formación Profesional',
                'titulo' => 'Tu futuro profesional empieza aquí',
                'subtitulo' => 'Formación Profesional oficial con prácticas en empresas líderes del sector.',
                'imagen' => '', 'botonTexto' => 'Ver ciclos', 'botonUrl' => '#oferta_formativa',
                'boton2Texto' => 'Contacto', 'boton2Url' => '#contacto',
            ],
        ],

        'oferta_formativa' => [
            'nombre' => 'Oferta formativa',
            'icono'  => 'fa-graduation-cap',
            'menu'   => 'Ciclos',
            'campos' => [
                'titulo'        => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo'     => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'mostrarPrecio' => ['tipo' => 'select',   'etiqueta' => 'Mostrar precio',
                                    'opciones' => ['no' => 'No', 'si' => 'Sí']],
                'botonTexto'    => ['tipo' => 'text',     'etiqueta' => 'Texto del botón por ciclo', 'max' => 40],
            ],
            'defecto' => [
                'titulo' => 'Nuestra oferta formativa',
                'subtitulo' => 'Ciclos formativos oficiales adaptados a las profesiones con más demanda.',
                'mostrarPrecio' => 'no', 'botonTexto' => 'Solicitar plaza',
            ],
        ],

        'prematricula_cta' => [
            'nombre' => 'Admisión (Pre-matrícula)',
            'icono'  => 'fa-user-plus',
            'menu'   => 'Admisión',
            'campos' => [
                'titulo'     => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'      => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 400],
                'botonTexto' => ['tipo' => 'text',     'etiqueta' => 'Texto del botón', 'max' => 40],
                'notaPlazo'  => ['tipo' => 'text',     'etiqueta' => 'Nota de plazo (opcional)', 'max' => 120],
            ],
            'defecto' => [
                'titulo' => 'Reserva tu plaza para el próximo curso',
                'texto' => 'Realiza tu pre-matrícula online en menos de 10 minutos. Nuestro equipo revisará tu solicitud y te contactará con los siguientes pasos.',
                'botonTexto' => 'Iniciar pre-matrícula', 'notaPlazo' => 'Plazo abierto — plazas limitadas',
            ],
        ],

        'porque_elegirnos' => [
            'nombre' => 'Por qué elegirnos',
            'icono'  => 'fa-circle-check',
            'menu'   => 'El centro',
            'campos' => [
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'items'     => ['tipo' => 'lista', 'etiqueta' => 'Ventajas', 'max' => 6, 'subcampos' => [
                    'icono'  => ['tipo' => 'select',   'etiqueta' => 'Icono', 'opciones' => $iconos],
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80, 'requerido' => true],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 250],
                ]],
            ],
            'defecto' => [
                'titulo' => '¿Por qué estudiar con nosotros?', 'subtitulo' => '',
                'items' => [
                    ['icono' => 'fa-briefcase', 'titulo' => 'Prácticas garantizadas', 'texto' => 'Convenios con empresas del sector para que hagas prácticas reales desde el primer curso.'],
                    ['icono' => 'fa-chalkboard-teacher', 'titulo' => 'Profesorado experto', 'texto' => 'Docentes con experiencia profesional activa en su especialidad.'],
                    ['icono' => 'fa-award', 'titulo' => 'Titulación oficial', 'texto' => 'Títulos oficiales de Formación Profesional válidos en toda España y la UE.'],
                ],
            ],
        ],

        'cifras' => [
            'nombre' => 'Cifras clave',
            'icono'  => 'fa-chart-simple',
            'menu'   => null,
            'campos' => [
                'items' => ['tipo' => 'lista', 'etiqueta' => 'Cifras', 'max' => 4, 'subcampos' => [
                    'numero'   => ['tipo' => 'text', 'etiqueta' => 'Número', 'max' => 10, 'requerido' => true],
                    'sufijo'   => ['tipo' => 'text', 'etiqueta' => 'Sufijo (%, +, …)', 'max' => 5],
                    'etiqueta' => ['tipo' => 'text', 'etiqueta' => 'Etiqueta', 'max' => 60, 'requerido' => true],
                ]],
            ],
            'defecto' => [
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
            'icono'  => 'fa-building',
            'menu'   => 'FP Dual',
            'campos' => [
                'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 500],
                'imagen' => ['tipo' => 'imagen',   'etiqueta' => 'Imagen'],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Ventajas', 'max' => 4, 'subcampos' => [
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80, 'requerido' => true],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 200],
                ]],
            ],
            'defecto' => [
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
            'icono'  => 'fa-handshake',
            'menu'   => 'Empresas',
            'campos' => [
                'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 300],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Empresas', 'max' => 12, 'subcampos' => [
                    'nombre' => ['tipo' => 'text',   'etiqueta' => 'Nombre', 'max' => 80, 'requerido' => true],
                    'logo'   => ['tipo' => 'imagen', 'etiqueta' => 'Logo'],
                    'url'    => ['tipo' => 'text',   'etiqueta' => 'Web (opcional)', 'max' => 255],
                ]],
            ],
            'defecto' => [
                'titulo' => 'Empresas que confían en nosotros',
                'texto' => 'Nuestros alumnos realizan prácticas y encuentran empleo en empresas líderes.',
                'items' => [],
            ],
        ],

        'instalaciones' => [
            'nombre' => 'Instalaciones',
            'icono'  => 'fa-school',
            'menu'   => 'Instalaciones',
            'campos' => [
                'titulo'    => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'subtitulo' => ['tipo' => 'textarea', 'etiqueta' => 'Introducción', 'max' => 300],
                'items'     => ['tipo' => 'lista', 'etiqueta' => 'Espacios', 'max' => 9, 'subcampos' => [
                    'imagen' => ['tipo' => 'imagen',   'etiqueta' => 'Foto', 'requerido' => true],
                    'titulo' => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 80],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Descripción', 'max' => 200],
                ]],
            ],
            'defecto' => [
                'titulo' => 'Nuestras instalaciones',
                'subtitulo' => 'Espacios y equipamiento profesional para aprender con las mismas herramientas que usarás en tu trabajo.',
                'items' => [],
            ],
        ],

        'testimonios' => [
            'nombre' => 'Testimonios',
            'icono'  => 'fa-quote-left',
            'menu'   => 'Testimonios',
            'campos' => [
                'titulo' => ['tipo' => 'text', 'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Testimonios', 'max' => 8, 'subcampos' => [
                    'nombre' => ['tipo' => 'text',     'etiqueta' => 'Nombre', 'max' => 80, 'requerido' => true],
                    'rol'    => ['tipo' => 'text',     'etiqueta' => 'Ciclo / promoción', 'max' => 80],
                    'texto'  => ['tipo' => 'textarea', 'etiqueta' => 'Testimonio', 'max' => 400, 'requerido' => true],
                    'foto'   => ['tipo' => 'imagen',   'etiqueta' => 'Foto'],
                ]],
            ],
            'defecto' => [
                'titulo' => 'Lo que dicen nuestros alumnos',
                'items' => [
                    ['nombre' => 'Laura G.', 'rol' => 'DAM · Promoción 2024', 'texto' => 'Gracias a las prácticas conseguí contrato en la misma empresa antes de terminar el ciclo.', 'foto' => ''],
                    ['nombre' => 'Carlos M.', 'rol' => 'ASIR · Promoción 2023', 'texto' => 'El profesorado te acompaña de verdad. Salí con un portfolio que me abrió muchas puertas.', 'foto' => ''],
                ],
            ],
        ],

        'faq' => [
            'nombre' => 'Preguntas frecuentes',
            'icono'  => 'fa-circle-question',
            'menu'   => null,
            'campos' => [
                'titulo' => ['tipo' => 'text', 'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'items'  => ['tipo' => 'lista', 'etiqueta' => 'Preguntas', 'max' => 10, 'subcampos' => [
                    'pregunta'  => ['tipo' => 'text',     'etiqueta' => 'Pregunta', 'max' => 150, 'requerido' => true],
                    'respuesta' => ['tipo' => 'textarea', 'etiqueta' => 'Respuesta', 'max' => 600, 'requerido' => true],
                ]],
            ],
            'defecto' => [
                'titulo' => 'Preguntas frecuentes',
                'items' => [
                    ['pregunta' => '¿Qué requisitos necesito para matricularme?', 'respuesta' => 'Depende del ciclo: para grado medio, el título de ESO o equivalente; para grado superior, Bachillerato, un grado medio o prueba de acceso.'],
                    ['pregunta' => '¿Las prácticas en empresa están garantizadas?', 'respuesta' => 'Sí. Todos los ciclos incluyen formación en centros de trabajo con empresas colaboradoras del centro.'],
                ],
            ],
        ],

        'contacto' => [
            'nombre' => 'Contacto',
            'icono'  => 'fa-envelope',
            'menu'   => 'Contacto',
            'campos' => [
                'titulo'           => ['tipo' => 'text',     'etiqueta' => 'Título', 'max' => 120, 'requerido' => true],
                'texto'            => ['tipo' => 'textarea', 'etiqueta' => 'Texto', 'max' => 300],
                'mostrarFormulario'=> ['tipo' => 'select',   'etiqueta' => 'Mostrar formulario',
                                       'opciones' => ['si' => 'Sí', 'no' => 'No']],
                'textoHorario'     => ['tipo' => 'textarea', 'etiqueta' => 'Horario de atención', 'max' => 300],
            ],
            'defecto' => [
                'titulo' => '¿Hablamos?',
                'texto' => 'Resolvemos tus dudas sobre ciclos, admisión, becas y convalidaciones.',
                'mostrarFormulario' => 'si',
                'textoHorario' => "Lunes a viernes: 9:00 – 14:00\nSecretaría: 9:00 – 13:00",
            ],
        ],
    ];

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

            case 'select':
                $opciones = array_keys($def['opciones'] ?? []);
                $limpio[$clave] = in_array($valor, $opciones, true) ? $valor : ($opciones[0] ?? '');
                break;

            case 'imagen':
                $nombre = basename(trim((string)($valor ?? '')));
                if ($nombre !== '' && !file_exists(__DIR__ . '/../../public/uploads/landing/' . $nombre)) {
                    $nombre = '';
                }
                $limpio[$clave] = $nombre;
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
