<?php
// ══════════════════════════════════════════════════════════════════════
// MOTOR DE CÁLCULO DE NOTAS — configurable, sustituye las reglas
// hardcodeadas de modelos/calificaciones.php cuando feature_academico_config
// está activo. Ver modelos/academico_config.php para el acceso a la
// configuración y migrate_db.php sección 12 para el esquema.
// ══════════════════════════════════════════════════════════════════════
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/academico_config.php";
require_once __DIR__ . "/retos.php";
require_once __DIR__ . "/ra_ce.php";

// Los 2 primeros períodos ordinarios (orden ASC) de una config, con su
// recuperación emparejada — la forma que reproduce las 4 columnas fijas
// nota_1ev/1final/2ev/2final. Si el centro tiene menos de 2 períodos
// ordinarios (config aún sin terminar de configurar) devuelve menos de 2.
function _motorSlotsLegado(int $idConfig): array {
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT idPeriodo FROM academic_periods WHERE idConfig = ? AND tipo = 'evaluacion' ORDER BY orden ASC LIMIT 2");
    mysqli_stmt_bind_param($stmt, "i", $idConfig);
    mysqli_stmt_execute($stmt);
    $ordinarios = [];
    $res = mysqli_stmt_get_result($stmt);
    while ($fila = mysqli_fetch_assoc($res)) $ordinarios[] = (int)$fila['idPeriodo'];
    if (empty($ordinarios)) return [];

    $stmt2 = mysqli_prepare($con, "SELECT idPeriodo, idPeriodoRecuperaDe FROM academic_periods WHERE idConfig = ? AND tipo = 'recuperacion'");
    mysqli_stmt_bind_param($stmt2, "i", $idConfig);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    $recuperaDe = [];
    while ($fila = mysqli_fetch_assoc($res2)) {
        if ($fila['idPeriodoRecuperaDe']) $recuperaDe[(int)$fila['idPeriodoRecuperaDe']] = (int)$fila['idPeriodo'];
    }

    $slots = [];
    if (isset($ordinarios[0])) $slots['1ev'] = $ordinarios[0];
    if (isset($recuperaDe[$ordinarios[0] ?? 0])) $slots['1final'] = $recuperaDe[$ordinarios[0]];
    if (isset($ordinarios[1])) $slots['2ev'] = $ordinarios[1];
    if (isset($recuperaDe[$ordinarios[1] ?? 0])) $slots['2final'] = $recuperaDe[$ordinarios[1]];
    return $slots;
}

// Sincroniza calificaciones_modulos (nota_1ev/1final/2ev/2final, tal como las
// escribe el formulario de notas de siempre) hacia calificaciones_periodo,
// para que el motor configurable vea los datos sin duplicar el formulario de
// entrada. Solo cubre los 2 primeros períodos ordinarios + su recuperación —
// si un centro añade un 3er período, ya necesita introducir esa nota desde
// una vista basada en períodos (fuera del alcance de este puente).
function sincronizarCalificacionPeriodo(
    int $idEstudiante, int $idModulo, int $idConfig,
    ?float $nota1ev, ?string $est1ev, ?float $nota1final, ?string $est1final,
    ?float $nota2ev, ?string $est2ev, ?float $nota2final, ?string $est2final
): void {
    $idTipoExamen = dbFetchOne(
        "SELECT idTipo FROM assessment_types WHERE idConfig = ? AND origen = 'examen' ORDER BY orden LIMIT 1",
        "i", $idConfig
    )['idTipo'] ?? null;
    if (!$idTipoExamen) return;

    $slots = _motorSlotsLegado($idConfig);
    if (empty($slots)) return;

    $valores = [
        '1ev'    => [$nota1ev, $est1ev],
        '1final' => [$nota1final, $est1final],
        '2ev'    => [$nota2ev, $est2ev],
        '2final' => [$nota2final, $est2final],
    ];

    $con = obtenerConexion();
    foreach ($slots as $slot => $idPeriodo) {
        [$nota, $estado] = $valores[$slot];
        $stmt = mysqli_prepare($con, "INSERT INTO calificaciones_periodo
                (idEstudiante, idModulo, idPeriodo, idTipo, nota, estado)
                VALUES (?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE nota = VALUES(nota), estado = VALUES(estado)");
        mysqli_stmt_bind_param($stmt, "iiiids", $idEstudiante, $idModulo, $idPeriodo, $idTipoExamen, $nota, $estado);
        mysqli_stmt_execute($stmt);
    }
}

// Nota de RA/CE de un módulo para un estudiante, ponderada por el
// porcentaje de cada RA (resultados_aprendizaje.porcentaje) — solo cuentan
// los RA que el centro haya enlazado a este tipo de evaluación
// (resultados_aprendizaje.idTipo); un RA sin enlazar sigue siendo pura
// documentación sin efecto en la nota, igual que antes de esta integración.
function _motorMediaRACE(int $idEstudiante, int $idModulo, int $idTipo): array {
    $ras = array_filter(listarRAPorModulo($idModulo), fn($ra) => (int)($ra['idTipo'] ?? 0) === $idTipo);
    return calcularMediaPonderadaRA($idEstudiante, $idModulo, $ras);
}

// Misma regla que calcularNotaDefinitiva() en calificaciones.php: una
// recuperación solo sustituye a la nota ordinaria si es estrictamente mayor.
function _motorResolverRecuperacion(?float $ordinaria, ?float $recuperacion): ?float {
    if ($ordinaria === null) return $recuperacion;
    if ($recuperacion !== null && $recuperacion > $ordinaria) return $recuperacion;
    return $ordinaria;
}

// Para un tipo de evaluación cuyo origen='examen' (o futuros orígenes basados
// en calificaciones_periodo): resuelve cada período ordinario frente a su
// recuperación emparejada (idPeriodoRecuperaDe) y devuelve la media de los
// valores resueltos no nulos, más si hubo al menos una nota real.
function _motorMediaPorPeriodos(int $idEstudiante, int $idModulo, int $idTipo, int $idConfig): array {
    $con = obtenerConexion();

    $stmt = mysqli_prepare($con, "SELECT idPeriodo, tipo, idPeriodoRecuperaDe FROM academic_periods WHERE idConfig = ? ORDER BY orden");
    mysqli_stmt_bind_param($stmt, "i", $idConfig);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $periodos = [];
    $recuperacionDe = []; // idPeriodoOrdinario => idPeriodoRecuperacion
    while ($fila = mysqli_fetch_assoc($res)) {
        $periodos[$fila['idPeriodo']] = $fila;
        if ($fila['tipo'] === 'recuperacion' && $fila['idPeriodoRecuperaDe']) {
            $recuperacionDe[(int)$fila['idPeriodoRecuperaDe']] = (int)$fila['idPeriodo'];
        }
    }

    $stmt2 = mysqli_prepare($con, "SELECT idPeriodo, nota FROM calificaciones_periodo WHERE idEstudiante = ? AND idModulo = ? AND idTipo = ?");
    mysqli_stmt_bind_param($stmt2, "iii", $idEstudiante, $idModulo, $idTipo);
    mysqli_stmt_execute($stmt2);
    $res2 = mysqli_stmt_get_result($stmt2);
    $notasPorPeriodo = [];
    while ($fila = mysqli_fetch_assoc($res2)) {
        $notasPorPeriodo[(int)$fila['idPeriodo']] = $fila['nota'] !== null ? (float)$fila['nota'] : null;
    }

    $resueltos = [];
    foreach ($periodos as $idPeriodo => $p) {
        if ($p['tipo'] === 'recuperacion') continue; // se procesan junto a su ordinario
        $ordinaria = $notasPorPeriodo[$idPeriodo] ?? null;
        $idRecuperacion = $recuperacionDe[$idPeriodo] ?? null;
        $recuperacion = $idRecuperacion ? ($notasPorPeriodo[$idRecuperacion] ?? null) : null;
        $final = _motorResolverRecuperacion($ordinaria, $recuperacion);
        if ($final !== null) $resueltos[] = $final;
    }

    if (empty($resueltos)) return ['media' => 0.0, 'huboNota' => false];
    return ['media' => array_sum($resueltos) / count($resueltos), 'huboNota' => true];
}

// Nota media de retos de un módulo para un estudiante — reutiliza el sistema
// de retos existente (calificaciones_retos/modulo_reto) sin cambios: los
// retos nunca estuvieron ligados a evaluaciones/períodos en este proyecto,
// así que no se fuerzan a calificaciones_periodo.
function _motorMediaRetos(int $idEstudiante, int $idModulo): float {
    $medias = listarCalificacionesRetoPorModulo($idModulo);
    return isset($medias[$idEstudiante]) ? (float)$medias[$idEstudiante] : 0.0;
}

// Calcula la nota final de un módulo para un estudiante usando la
// configuración académica activa (tipos de evaluación + sus pesos +
// política de calificación), en vez de la fórmula 0.75/0.25 hardcodeada.
// Devuelve el mismo tipo de estructura que necesita obtenerResultadosFinalesEstudiante().
function calcularNotaModuloConfigurable(int $idEstudiante, int $idModulo, int $idConfig): array {
    $tipos = listarTiposEvaluacion($idConfig);
    $politica = obtenerPoliticaCalificacion($idConfig) ?? ['decimales' => 2, 'notaAprobado' => 5.00];

    $sumaPonderada = 0.0;
    $sumaPesos = 0.0;
    $tieneNotaObligatoria = false;
    $detalle = [];
    // Acumulados por origen (no por nombre): el nombre del tipo lo puede
    // renombrar el centro desde el wizard (guardarTipoEvaluacion), así que
    // buscar $detalle['Examen']/$detalle['Reto'] literal se rompía en
    // silencio (volvía a 0.0) en cuanto alguien renombraba esos tipos.
    $sumaExamen = 0.0; $pesosExamen = 0.0;
    $sumaReto = 0.0; $pesosReto = 0.0;

    foreach ($tipos as $tipo) {
        if (!$tipo['incluirEnMedia']) continue;
        $peso = (float)$tipo['peso'];

        if ($tipo['origen'] === 'examen') {
            $mediaTipo = _motorMediaPorPeriodos($idEstudiante, $idModulo, (int)$tipo['idTipo'], $idConfig);
            $media = $mediaTipo['media'];
            $huboNota = $mediaTipo['huboNota'];
            if ($huboNota) { $sumaExamen += $media * $peso; $pesosExamen += $peso; }
        } elseif ($tipo['origen'] === 'reto') {
            $media = _motorMediaRetos($idEstudiante, $idModulo);
            $huboNota = true; // un reto sin nota no bloquea el estado del módulo (igual que hoy)
            $sumaReto += $media * $peso; $pesosReto += $peso;
        } elseif ($tipo['origen'] === 'ra_ce') {
            $mediaTipo = _motorMediaRACE($idEstudiante, $idModulo, (int)$tipo['idTipo']);
            $media = $mediaTipo['media'];
            $huboNota = $mediaTipo['huboNota'];
        } else {
            // fct/tfg/otro: se integran en M7/M8. Hasta entonces no aportan.
            $media = 0.0;
            $huboNota = true;
        }

        if (!empty($tipo['obligatorio']) && $huboNota) $tieneNotaObligatoria = true;
        $sumaPonderada += $media * $peso;
        $sumaPesos += $peso;
        $detalle[$tipo['nombre']] = round($media, 2);
    }

    $notaFinal = $sumaPesos > 0 ? $sumaPonderada / $sumaPesos : 0.0;
    $notaFinal = round($notaFinal, (int)$politica['decimales']);

    $estado = !$tieneNotaObligatoria ? 'Pendiente'
        : ($notaFinal >= (float)$politica['notaAprobado'] ? 'Aprobado' : 'Suspenso');

    return [
        'nota_final' => $tieneNotaObligatoria ? $notaFinal : '-',
        'estado'     => $estado,
        'media_retos'=> $pesosReto > 0 ? round($sumaReto / $pesosReto, 2) : 0.0,
        // OJO: media_notas es la media de los tipos con origen='examen' en
        // solitario, NO nota_final (que ya lleva la ponderación de todos los
        // tipos). Si se devolviera nota_final aquí, cualquier código que
        // reconstruya una media a partir de media_notas/media_retos contaría
        // el peso de los demás tipos dos veces (se detectó así: promedio_global
        // salía mal en cuanto se combinaban varios módulos + FCT).
        'media_notas'=> $tieneNotaObligatoria && $pesosExamen > 0 ? round($sumaExamen / $pesosExamen, 2) : ($tieneNotaObligatoria ? 0.0 : '-'),
        'detalle'    => $detalle,
    ];
}
