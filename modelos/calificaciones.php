<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/modulos.php";
require_once __DIR__ . "/estudiantes.php";
require_once __DIR__ . "/retos.php";
require_once __DIR__ . "/tfg.php";
require_once __DIR__ . "/academico_config.php";
require_once __DIR__ . "/motor_calificaciones.php";
require_once __DIR__ . "/fct.php";
require_once __DIR__ . "/ra_ce.php";

// ══════════════════════════════════════════════════════════════════════
// MOTOR DE NOTAS — reglas compartidas entre listarResultadosFinalesCiclo()
// y obtenerResultadosFinalesEstudiante() (antes duplicadas literalmente en
// ambas). Cuando feature_academico_config está activo, delega en
// calcularNotaModuloConfigurable(); si no, reproduce exactamente la fórmula
// original (0.75 examen / 0.25 reto, aprobado = 5) para no cambiar nada en
// centros que no hayan configurado el motor nuevo.
// ══════════════════════════════════════════════════════════════════════

// Detalle de un módulo (nota, estado) para un estudiante. $notas es la fila
// de calificaciones_modulos (o null); $mediaRetosBruta es el AVG ya calculado
// de calificaciones_retos para ese módulo (o null). Incluye claves internas
// (_mediaExamenes/_mediaRetos/_tieneNota) que usa _resumenGlobalEstudiante()
// para el promedio global — no se exponen en el detalle público final.
function _detalleModulo($idModulo, $nombreModulo, $cursoAnio, ?array $notas, $mediaRetosBruta, bool $motorActivo, ?int $idConfigActivo, int $idEstudiante): array {
    if ($motorActivo && $idConfigActivo) {
        $notaConfigurable = calcularNotaModuloConfigurable($idEstudiante, (int)$idModulo, $idConfigActivo);
        $tieneNota = $notaConfigurable['nota_final'] !== '-';
        return [
            'idModulo' => $idModulo,
            'nombreModulo' => $nombreModulo,
            'cursoAnio' => $cursoAnio,
            'media_retos' => round((float)$notaConfigurable['media_retos'], 2),
            'estado' => $notaConfigurable['estado'],
            'media_notas' => $tieneNota ? round((float)$notaConfigurable['media_notas'], 2) : '-',
            'nota_final' => $tieneNota ? round((float)$notaConfigurable['nota_final'], 2) : '-',
            '_mediaExamenes' => $tieneNota ? (float)$notaConfigurable['media_notas'] : 0.0,
            '_mediaRetos' => (float)$notaConfigurable['media_retos'],
            '_notaFinalNum' => $tieneNota ? (float)$notaConfigurable['nota_final'] : 0.0,
            '_tieneNota' => $tieneNota,
        ];
    }

    // RA/CE "simple": si el módulo tiene Resultados de Aprendizaje definidos
    // y feature_ra_ce está activo, esa es la forma de calificar que exige la
    // normativa española (LOMLOE) — cuenta para la nota final SIN necesitar
    // configurar el asistente académico completo. Si no hay ninguna nota de
    // RA/CE todavía para este alumno, cae al camino heredado de abajo (para
    // no mostrar "Pendiente" en un módulo que sí tiene notas de examen).
    if (FeatureGuard::check('feature_ra_ce')) {
        $ras = listarRAPorModulo($idModulo);
        if (!empty($ras)) {
            $race = calcularMediaPonderadaRA($idEstudiante, $idModulo, $ras);
            if ($race['huboNota']) {
                $notaFinalRedondeada = round($race['media']);
                return [
                    'idModulo' => $idModulo,
                    'nombreModulo' => $nombreModulo,
                    'cursoAnio' => $cursoAnio,
                    'media_retos' => 0.0,
                    'estado' => $notaFinalRedondeada >= 5 ? 'Aprobado' : 'Suspenso',
                    'media_notas' => round($race['media'], 2),
                    'nota_final' => $notaFinalRedondeada,
                    '_mediaExamenes' => 0.0,
                    '_mediaRetos' => 0.0,
                    '_notaFinalNum' => $race['media'],
                    '_tieneNota' => true,
                ];
            }
        }
    }

    $nota1ev    = isset($notas['nota_1ev'])    ? floatval($notas['nota_1ev'])    : null;
    $nota1final = isset($notas['nota_1final']) ? floatval($notas['nota_1final']) : null;
    $nota2ev    = isset($notas['nota_2ev'])    ? floatval($notas['nota_2ev'])    : null;
    $nota2final = isset($notas['nota_2final']) ? floatval($notas['nota_2final']) : null;

    $notaDefinitiva1 = calcularNotaDefinitiva($nota1ev, $nota1final);
    $notaDefinitiva2 = calcularNotaDefinitiva($nota2ev, $nota2final);

    $sumaEvaluaciones = 0;
    $evaluacionesConNota = 0;
    if ($notaDefinitiva1 !== null) { $sumaEvaluaciones += $notaDefinitiva1; $evaluacionesConNota++; }
    if ($notaDefinitiva2 !== null) { $sumaEvaluaciones += $notaDefinitiva2; $evaluacionesConNota++; }

    $mediaExamenes = $evaluacionesConNota > 0 ? $sumaEvaluaciones / $evaluacionesConNota : 0;
    $mediaRetos = floatval($mediaRetosBruta ?? 0);
    $notaFinal = ($mediaExamenes * 0.75) + ($mediaRetos * 0.25);
    // La nota final de un módulo se expresa en un número entero de 1 a 10
    // (normativa de evaluación de FP de las comunidades autónomas) — se
    // redondea aquí, antes de decidir el estado, para que "Aprobado" siempre
    // coincida con la nota que se muestra (p.ej. una media de 4.6 redondea a
    // 5 y aprueba; comparar el estado contra el valor sin redondear daría
    // "Suspenso" con un 5 en pantalla).
    $notaFinalRedondeada = round($notaFinal);

    if ($evaluacionesConNota == 0) {
        $estado = "Pendiente";
    } elseif ($notaFinalRedondeada >= 5) {
        $estado = "Aprobado";
    } else {
        $estado = "Suspenso";
    }

    return [
        'idModulo' => $idModulo,
        'nombreModulo' => $nombreModulo,
        'cursoAnio' => $cursoAnio,
        'media_retos' => round($mediaRetos, 2),
        'estado' => $estado,
        'media_notas' => $evaluacionesConNota > 0 ? round($mediaExamenes, 2) : "-",
        'nota_final' => $evaluacionesConNota > 0 ? $notaFinalRedondeada : "-",
        '_mediaExamenes' => $mediaExamenes,
        '_mediaRetos' => $mediaRetos,
        '_notaFinalNum' => $evaluacionesConNota > 0 ? $notaFinal : 0.0,
        '_tieneNota' => $evaluacionesConNota > 0,
    ];
}

// Resumen global (promedio, estado_global) a partir de los detalles de
// módulo ya calculados (con sus claves internas _mediaExamenes/_mediaRetos/
// _tieneNota) más la nota de TFG.
function _resumenGlobalEstudiante(array $detallesModulos, $notaTfgBruta, bool $motorActivo, ?int $idConfigActivo, int $idEstudiante = 0, int $idCiclo = 0): array {
    $sumaModulos = 0.0; $sumaRetos = 0.0; $sumaNotaFinal = 0.0; $modulosConNotas = 0; $tieneSuspensos = false;
    $totalModulos = count($detallesModulos);

    foreach ($detallesModulos as $detalle) {
        if ($detalle['estado'] === 'Suspenso') $tieneSuspensos = true;
        if ($detalle['_tieneNota']) {
            $sumaModulos += $detalle['_mediaExamenes'];
            $sumaRetos += $detalle['_mediaRetos'];
            $sumaNotaFinal += $detalle['_notaFinalNum'];
            $modulosConNotas++;
        }
    }

    if ($modulosConNotas === 0) {
        return [
            'media_modulos' => "-", 'media_retos' => "-", 'promedio_global' => "-",
            'calculo_completo' => false, 'tiene_suspensos' => $tieneSuspensos, 'estado_global' => 'PENDIENTE',
        ];
    }

    $mediaModulos = $sumaModulos / $modulosConNotas;
    $mediaRetosGlobal = $sumaRetos / $modulosConNotas;

    // Se promedia nota_final de cada módulo directamente (ya viene resuelta
    // con lo que corresponda a cada módulo: motor configurable, RA/CE simple,
    // o la fórmula heredada 75/25). Reconstruir a partir de mediaExamenes/
    // mediaRetos daría el mismo resultado SOLO si todos los módulos usan la
    // fórmula heredada — en cuanto uno se calcula por RA/CE (mediaExamenes y
    // mediaRetos van a 0 ahí) la reconstrucción lo dejaría fuera de la media
    // global por completo.
    $promedioGlobal = $sumaNotaFinal / $modulosConNotas;

    $notaAprobado = 5.0;
    $notaMinimaTfg = 5.0;
    $pesoTfg = 1.0;
    $configFCT = null;
    if ($motorActivo && $idConfigActivo) {
        $politica = obtenerPoliticaCalificacion($idConfigActivo);
        if ($politica) {
            $notaAprobado = (float)$politica['notaAprobado'];
            $pesoTfg = (float)$politica['pesoTfgEnMedia'];
        }
        $configTFG = obtenerConfigTFG($idConfigActivo);
        if ($configTFG) {
            $notaMinimaTfg = (float)$configTFG['notaMinima'];
            $pesoTfg = (float)$configTFG['pesoEnMedia']; // pesoEnMedia vive en tfg_config, no en grading_policies
        }
        $configFCT = obtenerConfigFCT($idConfigActivo);
    }

    $notaTfgNum = is_numeric($notaTfgBruta) ? (float)$notaTfgBruta : null;
    if ($notaTfgNum !== null) {
        $promedioGlobal = ($promedioGlobal * $modulosConNotas + $notaTfgNum * $pesoTfg) / ($modulosConNotas + $pesoTfg);
        if ($notaTfgNum < $notaMinimaTfg) $tieneSuspensos = true;
    }

    // FCT (Formación en Centros de Trabajo): es por ciclo, no por módulo, así
    // que se incorpora aquí (igual que el TFG) y no en _detalleModulo().
    if (!empty($configFCT['habilitado']) && $idEstudiante && $idCiclo) {
        $fct = obtenerNotaFCTEscala10($idEstudiante, $idCiclo, $configFCT['metodoEvaluacion'] ?? 'ambos');
        if ($fct['huboNota']) {
            $pesoFct = (float)($configFCT['pesoEnMedia'] ?? 0);
            if ($pesoFct > 0) {
                $promedioGlobal = ($promedioGlobal * $modulosConNotas + $fct['media'] * $pesoFct) / ($modulosConNotas + $pesoFct);
            }
            if (!empty($configFCT['requiereAprobarParaTitular']) && $fct['media'] < $notaAprobado) {
                $tieneSuspensos = true;
            }
        }
    }

    $calculoCompleto = ($modulosConNotas === $totalModulos);
    $estadoGlobal = 'PENDIENTE';
    if ($tieneSuspensos) {
        $estadoGlobal = 'SUSPENSO';
    } elseif ($calculoCompleto && $promedioGlobal >= $notaAprobado) {
        $estadoGlobal = 'APROBADO';
    }

    return [
        'media_modulos' => round($mediaModulos, 2),
        'media_retos' => round($mediaRetosGlobal, 2),
        'promedio_global' => round($promedioGlobal, 2),
        'calculo_completo' => $calculoCompleto,
        'tiene_suspensos' => $tieneSuspensos,
        'estado_global' => $estadoGlobal,
    ];
}

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function obtenerNotasModulo($idEstudiante, $idModulo)
{
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $notas = mysqli_fetch_assoc($resultado);
    return $notas;
}

function obtenerCalificacionPorId($idCalificacion)
{
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    return $datos;
}

function listarCalificacionesPorEstudiante($idEstudiante)
{
    $con = obtenerConexion();
    $sql = "SELECT cm.*, m.nombreModulo
            FROM calificaciones_modulos cm
            JOIN modulos m ON cm.idModulo = m.idModulo
            WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarCalificacionesPorModulo($idModulo)
{
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final, cm.observaciones
            FROM modulos mo
            JOIN estudiantes e ON e.idCiclo = mo.idCiclo
            LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND cm.idModulo = mo.idModulo
            WHERE mo.idModulo = ?
            ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES / ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $val1ev, $val1final, $val2ev, $val2final, $observaciones)
{
    $especiales = ['NP', 'EX', 'CO'];
    $parse = function($v) use ($especiales) {
        $v = strtoupper(trim((string)$v));
        if ($v === '')                    return ['nota' => null, 'estado' => null];
        if (in_array($v, $especiales))    return ['nota' => null, 'estado' => $v];
        return ['nota' => (float)$v, 'estado' => null];
    };

    $p1ev    = $parse($val1ev);
    $p1final = $parse($val1final);
    $p2ev    = $parse($val2ev);
    $p2final = $parse($val2final);

    $nota1ev    = $p1ev['nota'];    $est1ev    = $p1ev['estado'];
    $nota1final = $p1final['nota']; $est1final = $p1final['estado'];
    $nota2ev    = $p2ev['nota'];    $est2ev    = $p2ev['estado'];
    $nota2final = $p2final['nota']; $est2final = $p2final['estado'];

    $con = obtenerConexion();

    $sql1 = "INSERT INTO calificaciones_modulos
                 (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final,
                  estado_1ev, estado_1final, estado_2ev, estado_2final, observaciones)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE
                 nota_1ev    = VALUES(nota_1ev),
                 nota_1final = VALUES(nota_1final),
                 nota_2ev    = VALUES(nota_2ev),
                 nota_2final = VALUES(nota_2final),
                 estado_1ev    = VALUES(estado_1ev),
                 estado_1final = VALUES(estado_1final),
                 estado_2ev    = VALUES(estado_2ev),
                 estado_2final = VALUES(estado_2final),
                 observaciones = VALUES(observaciones)";
    $stmt = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt, "iiddddsssss",
        $idEstudiante, $idModulo,
        $nota1ev, $nota1final, $nota2ev, $nota2final,
        $est1ev, $est1final, $est2ev, $est2final,
        $observaciones);
    $exito = mysqli_stmt_execute($stmt);

    // Mantiene calificaciones_periodo al día con lo que se acaba de guardar
    // en las 4 columnas fijas, para que el motor configurable (si está
    // activo) vea la nota sin necesitar una vista de entrada nueva.
    if ($exito && motorAcademicoActivo()) {
        $configActiva = obtenerConfigAcademicaActiva();
        if ($configActiva) {
            sincronizarCalificacionPeriodo(
                (int)$idEstudiante, (int)$idModulo, (int)$configActiva['idConfig'],
                $nota1ev, $est1ev, $nota1final, $est1final, $nota2ev, $est2ev, $nota2final, $est2final
            );
        }
    }

    return $exito;
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function eliminarCalificacion($idCalificacion)
{
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    $exito = mysqli_stmt_execute($stmt);
    return $exito;
}

// ══════════════════════════════════════════════════════════════════════
// RESULTADOS FINALES
// ══════════════════════════════════════════════════════════════════════

function listarResultadosFinalesCiclo($idCiclo)
{
    $con = obtenerConexion();

    // 1. Obtener estudiantes
    $sqlEstudiantes = "SELECT e.*, c.nombreCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.idCiclo = ? AND e.eliminado = 0";
    $stmtE = mysqli_prepare($con, $sqlEstudiantes);
    mysqli_stmt_bind_param($stmtE, "i", $idCiclo);
    mysqli_stmt_execute($stmtE);
    $resE = mysqli_stmt_get_result($stmtE);
    $estudiantes = [];
    $estudianteIds = [];
    while ($fila = mysqli_fetch_assoc($resE)) {
        $estudiantes[$fila['idEstudiante']] = $fila;
        $estudianteIds[] = $fila['idEstudiante'];
    }

    if (empty($estudianteIds)) return [];

    // 2. Obtener módulos
    $sqlModulos = "SELECT * FROM modulos WHERE idCiclo = ?";
    $stmtM = mysqli_prepare($con, $sqlModulos);
    mysqli_stmt_bind_param($stmtM, "i", $idCiclo);
    mysqli_stmt_execute($stmtM);
    $resM = mysqli_stmt_get_result($stmtM);
    $modulos = [];
    $moduloIds = [];
    while ($fila = mysqli_fetch_assoc($resM)) {
        $modulos[$fila['idModulo']] = $fila;
        $moduloIds[] = $fila['idModulo'];
    }

    if (empty($moduloIds)) return [];

    // 3. Obtener todas las calificaciones de módulos para estos estudiantes/módulos
    $phEst = implode(',', array_fill(0, count($estudianteIds), '?'));
    $phMod = implode(',', array_fill(0, count($moduloIds), '?'));
    $tEst  = str_repeat('i', count($estudianteIds));
    $tMod  = str_repeat('i', count($moduloIds));

    $stmtG = mysqli_prepare($con, "SELECT * FROM calificaciones_modulos WHERE idEstudiante IN ($phEst) AND idModulo IN ($phMod)");
    mysqli_stmt_bind_param($stmtG, $tEst . $tMod, ...[...$estudianteIds, ...$moduloIds]);
    mysqli_stmt_execute($stmtG);
    $resG = mysqli_stmt_get_result($stmtG);
    $allGrades = [];
    while ($fila = mysqli_fetch_assoc($resG)) {
        $allGrades[$fila['idEstudiante']][$fila['idModulo']] = $fila;
    }

    // 4. Obtener promedios de retos
    $stmtR = mysqli_prepare($con,
        "SELECT cr.idEstudiante, mr.idModulo, AVG(cr.nota) AS promedio
         FROM calificaciones_retos cr
         JOIN modulo_reto mr ON cr.idReto = mr.idReto
         WHERE mr.idModulo IN ($phMod) AND cr.idEstudiante IN ($phEst)
         GROUP BY cr.idEstudiante, mr.idModulo"
    );
    mysqli_stmt_bind_param($stmtR, $tMod . $tEst, ...[...$moduloIds, ...$estudianteIds]);
    mysqli_stmt_execute($stmtR);
    $resR = mysqli_stmt_get_result($stmtR);
    $allRetos = [];
    while ($fila = mysqli_fetch_assoc($resR)) {
        $allRetos[$fila['idEstudiante']][$fila['idModulo']] = $fila['promedio'];
    }

    // 5. Obtener calificaciones de TFG
    $stmtT = mysqli_prepare($con, "SELECT * FROM calificaciones_tfg WHERE idEstudiante IN ($phEst)");
    mysqli_stmt_bind_param($stmtT, $tEst, ...$estudianteIds);
    mysqli_stmt_execute($stmtT);
    $resT = mysqli_stmt_get_result($stmtT);
    $allTFG = [];
    while ($fila = mysqli_fetch_assoc($resT)) {
        $allTFG[$fila['idEstudiante']] = $fila;
    }

    $motorActivo = motorAcademicoActivo();
    $idConfigActivo = $motorActivo ? (obtenerConfigAcademicaActiva()['idConfig'] ?? null) : null;

    $resultados = [];
    foreach ($estudiantes as $idEstudiante => $datosEstudiante) {
        $notaTfgBruta = $allTFG[$idEstudiante]['nota'] ?? null;
        $resumen = [
            'idEstudiante' => $idEstudiante,
            'nombreEstudiante' => $datosEstudiante['nombreEstudiante'],
            'nombreCiclo' => $datosEstudiante['nombreCiclo'],
            'anioEstudio' => $datosEstudiante['curso'] ?? '-',
            'detalles_modulos' => [],
            'nota_tfg' => $notaTfgBruta,
            'obs_tfg' => $allTFG[$idEstudiante]['observaciones'] ?? '',
        ];

        $detallesInternos = [];
        foreach ($modulos as $idModulo => $modulo) {
            $detallesInternos[] = _detalleModulo(
                $idModulo, $modulo['nombreModulo'], $modulo['cursoAnio'],
                $allGrades[$idEstudiante][$idModulo] ?? null,
                $allRetos[$idEstudiante][$idModulo] ?? null,
                $motorActivo, $idConfigActivo, (int)$idEstudiante
            );
        }

        $global = _resumenGlobalEstudiante($detallesInternos, $notaTfgBruta, $motorActivo, $idConfigActivo, (int)$idEstudiante, (int)$idCiclo);
        $resumen = array_merge($resumen, $global);

        // El detalle público no expone las claves internas (_mediaExamenes/_mediaRetos/_tieneNota)
        $resumen['detalles_modulos'] = array_map(function ($detalle) {
            unset($detalle['_mediaExamenes'], $detalle['_mediaRetos'], $detalle['_tieneNota']);
            return $detalle;
        }, $detallesInternos);

        $resultados[] = $resumen;
    }

    return $resultados;
}

function obtenerResultadosFinalesEstudiante($idEstudiante, $listaModulos = null)
{
    $datosEstudiante = obtenerEstudiantePorId($idEstudiante);

    if ($listaModulos === null) {
        $listaModulos = listarModulosPorCiclo($datosEstudiante['idCiclo']);
    }

    $resumen = [];
    $resumen['idEstudiante'] = $idEstudiante;
    $resumen['nombreEstudiante'] = $datosEstudiante['nombreEstudiante'];
    $resumen['nombreCiclo'] = $datosEstudiante['nombreCiclo'];

    $calificacionTFG = obtenerCalificacionTFG($idEstudiante);
    $notaTfgBruta = $calificacionTFG['nota'] ?? null;
    $resumen['nota_tfg'] = $notaTfgBruta;
    $resumen['obs_tfg'] = $calificacionTFG['observaciones'] ?? '';

    // Batch de notas y medias de retos para todos los módulos del estudiante
    // en 2 consultas, en vez de 2 por módulo dentro del bucle.
    $notasPorModulo = [];
    $retosPorModulo = [];
    $moduloIds = array_column($listaModulos, 'idModulo');
    if (!empty($moduloIds)) {
        $con = obtenerConexion();
        $phMod = implode(',', array_fill(0, count($moduloIds), '?'));
        $tMod  = str_repeat('i', count($moduloIds));

        $stmtN = mysqli_prepare($con, "SELECT * FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo IN ($phMod)");
        mysqli_stmt_bind_param($stmtN, 'i' . $tMod, $idEstudiante, ...$moduloIds);
        mysqli_stmt_execute($stmtN);
        $resN = mysqli_stmt_get_result($stmtN);
        while ($fila = mysqli_fetch_assoc($resN)) {
            $notasPorModulo[$fila['idModulo']] = $fila;
        }

        $stmtR = mysqli_prepare($con,
            "SELECT mr.idModulo, AVG(cr.nota) AS promedio
             FROM calificaciones_retos cr
             JOIN modulo_reto mr ON cr.idReto = mr.idReto
             WHERE mr.idModulo IN ($phMod) AND cr.idEstudiante = ?
             GROUP BY mr.idModulo");
        mysqli_stmt_bind_param($stmtR, $tMod . 'i', ...[...$moduloIds, $idEstudiante]);
        mysqli_stmt_execute($stmtR);
        $resR = mysqli_stmt_get_result($stmtR);
        while ($fila = mysqli_fetch_assoc($resR)) {
            $retosPorModulo[$fila['idModulo']] = $fila['promedio'];
        }
    }

    $motorActivo = motorAcademicoActivo();
    $idConfigActivo = $motorActivo ? (obtenerConfigAcademicaActiva()['idConfig'] ?? null) : null;

    $detallesInternos = [];
    foreach ($listaModulos as $modulo) {
        $idModuloActual = $modulo['idModulo'];
        $detallesInternos[] = _detalleModulo(
            $idModuloActual, $modulo['nombreModulo'], $modulo['cursoAnio'] ?? 1,
            $notasPorModulo[$idModuloActual] ?? null,
            $retosPorModulo[$idModuloActual] ?? null,
            $motorActivo, $idConfigActivo, (int)$idEstudiante
        );
    }

    $global = _resumenGlobalEstudiante($detallesInternos, $notaTfgBruta, $motorActivo, $idConfigActivo, (int)$idEstudiante, (int)($datosEstudiante['idCiclo'] ?? 0));
    $resumen = array_merge($resumen, $global);

    $resumen['detalles_modulos'] = array_map(function ($detalle) {
        unset($detalle['_mediaExamenes'], $detalle['_mediaRetos'], $detalle['_tieneNota']);
        return $detalle;
    }, $detallesInternos);

    return $resumen;
}

// ══════════════════════════════════════════════════════════════════════
// BOLETINES
// ══════════════════════════════════════════════════════════════════════

function guardarBoletinLog($serial, $idEstudiante, $idCiclo, $nombreEstudiante, $nombreCiclo, $cursoEscolar)
{
    $con = obtenerConexion();
    $sql = "INSERT INTO boletines_log (serial, idEstudiante, idCiclo, nombreEstudiante, nombreCiclo, cursoEscolar)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE fechaGeneracion = CURRENT_TIMESTAMP";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "siisss", $serial, $idEstudiante, $idCiclo, $nombreEstudiante, $nombreCiclo, $cursoEscolar);
    return mysqli_stmt_execute($stmt);
}

function verificarBoletinPorSerial($serial, $ip = '')
{
    $con = obtenerConexion();

    $stmt = mysqli_prepare($con, "SELECT * FROM boletines_log WHERE serial = ?");
    mysqli_stmt_bind_param($stmt, "s", $serial);
    mysqli_stmt_execute($stmt);
    $doc = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    // Actualiza el contador de escaneos del documento encontrado
    if ($doc) {
        $upd = mysqli_prepare($con,
            "UPDATE boletines_log SET scan_count = scan_count + 1, last_scan_at = NOW(), last_scan_ip = ? WHERE serial = ?");
        mysqli_stmt_bind_param($upd, "ss", $ip, $serial);
        mysqli_stmt_execute($upd);
    }

    // Registra siempre el intento para auditoría y control de acceso
    $log = mysqli_prepare($con,
        "INSERT INTO verificaciones_log (serial_buscado, ip, resultado) VALUES (?, ?, ?)");
    $found = $doc ? 1 : 0;
    mysqli_stmt_bind_param($log, "ssi", $serial, $ip, $found);
    mysqli_stmt_execute($log);

    return $doc;
}

function contarIntentosVerificacion($ip, $minutos = 60)
{
    $con = obtenerConexion();
    $stmt = mysqli_prepare($con,
        "SELECT COUNT(*) FROM verificaciones_log WHERE ip = ? AND created_at >= NOW() - INTERVAL ? MINUTE");
    mysqli_stmt_bind_param($stmt, "si", $ip, $minutos);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
    return (int)$total;
}

function generarDatosBoletinCiclo($idCiclo) {
    $con = obtenerConexion();

    $stmt = mysqli_prepare($con,
        "SELECT e.*, c.nombreCiclo, c.abreviaturaCiclo, n.nombreNivel
         FROM estudiantes e
         JOIN ciclos c ON e.idCiclo = c.idCiclo
         JOIN niveles n ON c.idNivel = n.idNivel
         WHERE e.idCiclo = ? AND e.eliminado = 0 ORDER BY e.nombreEstudiante ASC");
    mysqli_stmt_bind_param($stmt, 'i', $idCiclo);
    mysqli_stmt_execute($stmt);
    $resEst = mysqli_stmt_get_result($stmt);
    $estudiantes = [];
    while ($fila = mysqli_fetch_assoc($resEst)) {
        $estudiantes[] = $fila;
    }
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($con, "SELECT * FROM modulos WHERE idCiclo = ? ORDER BY nombreModulo ASC");
    mysqli_stmt_bind_param($stmt, 'i', $idCiclo);
    mysqli_stmt_execute($stmt);
    $resMod = mysqli_stmt_get_result($stmt);
    $modulos = [];
    while ($fila = mysqli_fetch_assoc($resMod)) {
        $modulos[] = $fila;
    }
    mysqli_stmt_close($stmt);

    if (empty($modulos) || empty($estudiantes)) {
        return ['estudiantes' => $estudiantes, 'modulos' => $modulos];
    }

    $modIds  = implode(',', array_map('intval', array_column($modulos, 'idModulo')));
    $resG    = mysqli_query($con, "SELECT * FROM calificaciones_modulos WHERE idModulo IN ($modIds)");
    $gradeMap = [];
    while ($row = mysqli_fetch_assoc($resG)) {
        $gradeMap[$row['idEstudiante']][$row['idModulo']] = $row;
    }

    $estIds = implode(',', array_map('intval', array_column($estudiantes, 'idEstudiante')));
    $resTFG = mysqli_query($con, "SELECT idEstudiante, nota FROM calificaciones_tfg WHERE idEstudiante IN ($estIds)");
    $tfgMap = [];
    while ($row = mysqli_fetch_assoc($resTFG)) {
        $tfgMap[$row['idEstudiante']] = $row['nota'];
    }

    // Nota/estado ya resueltos (recuperación, pesos, aprobado) por el mismo
    // motor que usa el resto de la app — el boletín ya NO recalcula por su
    // cuenta (antes tenía su propia lógica independiente en la plantilla,
    // que podía divergir: usaba "última evaluación no nula" en vez de
    // calcularNotaDefinitiva(), y una media simple sin el peso de retos).
    $resultadosPorEstudiante = [];
    foreach (listarResultadosFinalesCiclo($idCiclo) as $resultadoEstudiante) {
        $resultadosPorEstudiante[$resultadoEstudiante['idEstudiante']] = $resultadoEstudiante;
    }

    // Método de evaluación de FCT: el del motor configurable si está activo,
    // 'ambos' (nota si existe, si no apto/no apto) por defecto — igual que
    // en cualquier otro sitio que consulta obtenerNotaFCTEscala10() sin
    // depender de que el centro haya configurado el asistente académico.
    $metodoFCT = 'ambos';
    if (motorAcademicoActivo()) {
        $configActivaFCT = obtenerConfigAcademicaActiva();
        if ($configActivaFCT) {
            $internshipCfg = obtenerConfigFCT((int)$configActivaFCT['idConfig']);
            if ($internshipCfg) $metodoFCT = $internshipCfg['metodoEvaluacion'] ?? 'ambos';
        }
    }

    foreach ($estudiantes as &$est) {
        $est['nota_tfg'] = $tfgMap[$est['idEstudiante']] ?? null;
        $resultado = $resultadosPorEstudiante[$est['idEstudiante']] ?? null;
        $est['promedio_global'] = $resultado['promedio_global'] ?? '-';
        $est['estado_global']   = $resultado['estado_global'] ?? 'PENDIENTE';
        $detallesPorModulo = [];
        foreach ($resultado['detalles_modulos'] ?? [] as $detalleModulo) {
            $detallesPorModulo[$detalleModulo['idModulo']] = $detalleModulo;
        }

        $fct = obtenerNotaFCTEscala10((int)$est['idEstudiante'], (int)$idCiclo, $metodoFCT);
        $est['nota_fct'] = $fct['huboNota'] ? $fct['media'] : null;

        $est['modulos'] = [];
        foreach ($modulos as $mod) {
            $est['modulos'][] = [
                'idModulo'     => $mod['idModulo'],
                'nombreModulo' => $mod['nombreModulo'],
                'codigoModulo' => $mod['codigoModulo'] ?? null,
                'cursoAnio'    => $mod['cursoAnio'] ?? 1,
                'notas'        => $gradeMap[$est['idEstudiante']][$mod['idModulo']] ?? null,
                'nota_final'   => $detallesPorModulo[$mod['idModulo']]['nota_final'] ?? '-',
                'estado'       => $detallesPorModulo[$mod['idModulo']]['estado'] ?? 'Pendiente',
            ];
        }
    }

    return ['estudiantes' => $estudiantes, 'modulos' => $modulos];
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

// Devuelve la nota de recuperación si supera a la base; de lo contrario la base.
function calcularNotaDefinitiva($notaBase, $notaRecuperacion)
{
    if ($notaBase === null) return null;
    if ($notaRecuperacion !== null && $notaRecuperacion > $notaBase) {
        return $notaRecuperacion;
    }
    return $notaBase;
}
