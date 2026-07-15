<?php
// ══════════════════════════════════════════════════════════════════════
// VERIFICACIÓN: el motor configurable debe reproducir EXACTAMENTE el
// cálculo hardcodeado actual cuando se siembra con la configuración por
// defecto (ver migrate_db.php sección 12). Compara, para cada estudiante,
// la nota de módulo calculada por el código antiguo (modelos/calificaciones.php,
// sin tocar) frente al motor nuevo (modelos/motor_calificaciones.php).
// Solo lectura — no modifica ningún dato. CLI únicamente.
// ══════════════════════════════════════════════════════════════════════
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("Solo se puede ejecutar desde CLI.\n");
}

require_once __DIR__ . '/modelos/calificaciones.php';
require_once __DIR__ . '/modelos/academico_config.php';
require_once __DIR__ . '/modelos/motor_calificaciones.php';

$config = obtenerConfigAcademicaActiva();
if (!$config) {
    exit("No hay ninguna configuración académica sembrada. Ejecuta migrate_db.php primero.\n");
}
$idConfig = (int)$config['idConfig'];

// Estudiantes a comparar: cualquiera con datos en calificaciones_modulos O
// calificaciones_periodo (viejo o nuevo esquema, para no depender de que
// ya se haya migrado nada).
$con = obtenerConexion();
$res = mysqli_query($con, "
    SELECT DISTINCT idEstudiante, idModulo FROM (
        SELECT idEstudiante, idModulo FROM calificaciones_modulos
        UNION
        SELECT idEstudiante, idModulo FROM calificaciones_periodo
    ) t ORDER BY idEstudiante, idModulo");

$total = 0;
$coincide = 0;
$diferencias = [];

while ($fila = mysqli_fetch_assoc($res)) {
    $idEstudiante = (int)$fila['idEstudiante'];
    $idModulo = (int)$fila['idModulo'];
    $total++;

    // ── Cálculo antiguo: usa el código real de calificaciones.php sin tocar ──
    $resumenViejo = obtenerResultadosFinalesEstudiante($idEstudiante);
    $notaVieja = null;
    $estadoViejo = null;
    foreach ($resumenViejo['detalles_modulos'] as $d) {
        if ((int)$d['idModulo'] === $idModulo) {
            $notaVieja = $d['nota_final'];
            $estadoViejo = $d['estado'];
            break;
        }
    }

    // ── Cálculo nuevo: motor configurable ──
    $nuevo = calcularNotaModuloConfigurable($idEstudiante, $idModulo, $idConfig);
    $notaNueva = $nuevo['nota_final'];
    $estadoNuevo = $nuevo['estado'];

    // El código antiguo usa "Aprobado"/"Suspenso"/"Pendiente"; el nuevo motor
    // usa las mismas tres palabras — comparación textual directa.
    $notasIguales = (is_numeric($notaVieja) && is_numeric($notaNueva))
        ? abs((float)$notaVieja - (float)$notaNueva) < 0.005
        : $notaVieja === $notaNueva;
    $estadosIguales = $estadoViejo === $estadoNuevo;

    if ($notasIguales && $estadosIguales) {
        $coincide++;
        echo "[OK]    est=$idEstudiante mod=$idModulo  nota=$notaVieja  estado=$estadoViejo\n";
    } else {
        $diferencias[] = compact('idEstudiante', 'idModulo', 'notaVieja', 'notaNueva', 'estadoViejo', 'estadoNuevo');
        echo "[DIFF]  est=$idEstudiante mod=$idModulo  viejo=(nota=$notaVieja, estado=$estadoViejo)  nuevo=(nota=$notaNueva, estado=$estadoNuevo)\n";
    }
}

echo "\n" . str_repeat('=', 60) . "\n";
echo "$coincide / $total combinaciones estudiante-módulo coinciden.\n";
if ($diferencias) {
    echo count($diferencias) . " diferencia(s) encontrada(s) — revisar antes de continuar con M3.\n";
    exit(1);
}
echo "El motor nuevo reproduce el cálculo antiguo exactamente.\n";
exit(0);
