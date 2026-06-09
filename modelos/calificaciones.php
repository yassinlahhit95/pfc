<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/modulos.php";
require_once __DIR__ . "/estudiantes.php";
require_once __DIR__ . "/retos.php";
require_once __DIR__ . "/tfg.php";

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

function eliminarCalificacion($idCalificacion)
{
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    $exito = mysqli_stmt_execute($stmt);
    return $exito;
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

function listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo = 0, $idModulo = 0)
{
    $con = obtenerConexion();
    $select = "SELECT e.idEstudiante, e.nombreEstudiante, m.idModulo, m.nombreModulo,
                      cm.idCalificacion, cm.nota_1ev, cm.nota_1final, cm.nota_2ev, cm.nota_2final";

    if ($idModulo > 0) {
        $sql1 = "$select
                FROM modulos m
                JOIN modulo_profesor pm ON m.idModulo = pm.idModulo AND pm.idProfesor = ?
                JOIN estudiantes e ON m.idCiclo = e.idCiclo
                LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND m.idModulo = cm.idModulo
                WHERE m.idModulo = ?
                ORDER BY e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idModulo);

    } elseif ($idCiclo > 0) {
        $sql1 = "$select
                FROM modulos m
                JOIN ciclo_profesor cp ON m.idCiclo = cp.idCiclo AND cp.idProfesor = ?
                JOIN estudiantes e ON m.idCiclo = e.idCiclo
                LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND m.idModulo = cm.idModulo
                WHERE m.idCiclo = ?
                ORDER BY m.nombreModulo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idCiclo);

    } else {
        $sql1 = "$select
                FROM modulos m
                JOIN estudiantes e ON m.idCiclo = e.idCiclo
                LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND m.idModulo = cm.idModulo
                WHERE m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                   OR m.idModulo IN (SELECT idModulo FROM modulo_profesor WHERE idProfesor = ?)
                ORDER BY m.nombreModulo ASC, e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

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

    $sql1 = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmt = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $sql1 = "UPDATE calificaciones_modulos SET nota_1ev=?, nota_1final=?, nota_2ev=?, nota_2final=?, estado_1ev=?, estado_1final=?, estado_2ev=?, estado_2final=?, observaciones=? WHERE idEstudiante=? AND idModulo=?";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "ddddsssssii", $nota1ev, $nota1final, $nota2ev, $nota2final, $est1ev, $est1final, $est2ev, $est2final, $observaciones, $idEstudiante, $idModulo);
    } else {
        $sql1 = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, estado_1ev, estado_1final, estado_2ev, estado_2final, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "iiddddsssss", $idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $est1ev, $est1final, $est2ev, $est2final, $observaciones);
    }

    $exito = mysqli_stmt_execute($stmt);
    return $exito;
}

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

    // Update scan counter on the found document
    if ($doc) {
        $upd = mysqli_prepare($con,
            "UPDATE boletines_log SET scan_count = scan_count + 1, last_scan_at = NOW(), last_scan_ip = ? WHERE serial = ?");
        mysqli_stmt_bind_param($upd, "ss", $ip, $serial);
        mysqli_stmt_execute($upd);
    }

    // Always log the attempt for rate-limiting and audit
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

function listarCalificacionesPorModulo($idModulo)
{
    $con = obtenerConexion();
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, cm.nota_1ev AS calificacion, cm.observaciones
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

function listarResultadosFinalesCiclo($idCiclo)
{
    $con = obtenerConexion();

    // 1. Obtener estudiantes
    $sqlEstudiantes = "SELECT e.*, c.nombreCiclo FROM estudiantes e JOIN ciclos c ON e.idCiclo = c.idCiclo WHERE e.idCiclo = ?";
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
    $idsStr = implode(',', array_map('intval', $estudianteIds));
    $modIdsStr = implode(',', array_map('intval', $moduloIds));
    $sqlGrades = "SELECT * FROM calificaciones_modulos WHERE idEstudiante IN ($idsStr) AND idModulo IN ($modIdsStr)";
    $resG = mysqli_query($con, $sqlGrades);
    $allGrades = [];
    while ($fila = mysqli_fetch_assoc($resG)) {
        $allGrades[$fila['idEstudiante']][$fila['idModulo']] = $fila;
    }

    // 4. Obtener promedios de retos
    $sqlRetos = "SELECT cr.idEstudiante, mr.idModulo, AVG(cr.nota) AS promedio
                 FROM calificaciones_retos cr
                 JOIN modulo_reto mr ON cr.idReto = mr.idReto
                 WHERE mr.idModulo IN ($modIdsStr) AND cr.idEstudiante IN ($idsStr)
                 GROUP BY cr.idEstudiante, mr.idModulo";
    $resR = mysqli_query($con, $sqlRetos);
    $allRetos = [];
    while ($fila = mysqli_fetch_assoc($resR)) {
        $allRetos[$fila['idEstudiante']][$fila['idModulo']] = $fila['promedio'];
    }

    // 5. Obtener calificaciones de TFG
    $sqlTFG = "SELECT * FROM calificaciones_tfg WHERE idEstudiante IN ($idsStr)";
    $resT = mysqli_query($con, $sqlTFG);
    $allTFG = [];
    while ($fila = mysqli_fetch_assoc($resT)) {
        $allTFG[$fila['idEstudiante']] = $fila;
    }

    $resultados = [];
    foreach ($estudiantes as $idEstudiante => $datosEstudiante) {
        $resumen = [
            'idEstudiante' => $idEstudiante,
            'nombreEstudiante' => $datosEstudiante['nombreEstudiante'],
            'nombreCiclo' => $datosEstudiante['nombreCiclo'],
            'detalles_modulos' => [],
            'estado_global' => 'PENDIENTE',
            'tiene_suspensos' => false,
            'nota_tfg' => $allTFG[$idEstudiante]['nota'] ?? null,
            'obs_tfg' => $allTFG[$idEstudiante]['observaciones'] ?? '',
        ];

        $sumaModulos = 0;
        $sumaRetos = 0;
        $modulosConNotas = 0;
        $totalModulos = count($modulos);

        foreach ($modulos as $idModulo => $modulo) {
            $notas = $allGrades[$idEstudiante][$idModulo] ?? null;
            
            $nota1ev = isset($notas['nota_1ev']) ? floatval($notas['nota_1ev']) : null;
            $nota1final = isset($notas['nota_1final']) ? floatval($notas['nota_1final']) : null;
            $nota2ev = isset($notas['nota_2ev']) ? floatval($notas['nota_2ev']) : null;
            $nota2final = isset($notas['nota_2final']) ? floatval($notas['nota_2final']) : null;

            $notaDefinitiva1 = calcularNotaDefinitiva($nota1ev, $nota1final);
            $notaDefinitiva2 = calcularNotaDefinitiva($nota2ev, $nota2final);

            $sumaEvaluaciones = 0;
            $evaluacionesConNota = 0;

            if ($notaDefinitiva1 !== null) { $sumaEvaluaciones += $notaDefinitiva1; $evaluacionesConNota++; }
            if ($notaDefinitiva2 !== null) { $sumaEvaluaciones += $notaDefinitiva2; $evaluacionesConNota++; }

            $mediaExamenes = $evaluacionesConNota > 0 ? $sumaEvaluaciones / $evaluacionesConNota : 0;
            $mediaRetos = floatval($allRetos[$idEstudiante][$idModulo] ?? 0);
            $notaFinal = ($mediaExamenes * 0.75) + ($mediaRetos * 0.25);

            if ($evaluacionesConNota == 0) {
                $estado = "Pendiente";
            } elseif ($notaFinal >= 5) {
                $estado = "Aprobado";
            } else {
                $estado = "Suspenso";
                $resumen['tiene_suspensos'] = true;
            }

            $detalle = [
                'idModulo' => $idModulo,
                'nombreModulo' => $modulo['nombreModulo'],
                'media_retos' => round($mediaRetos, 2),
                'estado' => $estado,
                'media_notas' => $evaluacionesConNota > 0 ? round($mediaExamenes, 2) : "-",
                'nota_final' => $evaluacionesConNota > 0 ? round($notaFinal, 2) : "-",
            ];
            $resumen['detalles_modulos'][] = $detalle;

            if ($evaluacionesConNota > 0) {
                $sumaModulos += $mediaExamenes;
                $sumaRetos += $mediaRetos;
                $modulosConNotas++;
            }
        }

        if ($modulosConNotas > 0) {
            $mediaModulos = $sumaModulos / $modulosConNotas;
            $mediaRetosGlobal = $sumaRetos / $modulosConNotas;
            $promedioGlobal = ($mediaModulos * 0.75) + ($mediaRetosGlobal * 0.25);

            $resumen['media_modulos'] = round($mediaModulos, 2);
            $resumen['media_retos'] = round($mediaRetosGlobal, 2);
            $resumen['promedio_global'] = round($promedioGlobal, 2);
            $resumen['calculo_completo'] = ($modulosConNotas == $totalModulos);

            if ($resumen['tiene_suspensos']) {
                $resumen['estado_global'] = 'SUSPENSO';
            } elseif ($resumen['calculo_completo'] && $promedioGlobal >= 5) {
                $resumen['estado_global'] = 'APROBADO';
            }
        } else {
            $resumen['media_modulos'] = "-";
            $resumen['media_retos'] = "-";
            $resumen['promedio_global'] = "-";
            $resumen['calculo_completo'] = false;
        }
        $resultados[] = $resumen;
    }

    return $resultados;
}

function calcularNotaDefinitiva($notaBase, $notaRecuperacion)
{
    if ($notaBase === null) return null;
    if ($notaRecuperacion !== null && $notaRecuperacion > $notaBase) {
        return $notaRecuperacion;
    }
    return $notaBase;
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
    $resumen['detalles_modulos'] = [];
    $resumen['estado_global'] = 'PENDIENTE';
    $resumen['tiene_suspensos'] = false;

    $calificacionTFG = obtenerCalificacionTFG($idEstudiante);
    $resumen['nota_tfg'] = null;
    $resumen['obs_tfg'] = '';
    if ($calificacionTFG) {
        $resumen['nota_tfg'] = $calificacionTFG['nota'];
        $resumen['obs_tfg'] = $calificacionTFG['observaciones'];
    }

    $sumaModulos = 0;
    $sumaRetos = 0;
    $modulosConNotas = 0;
    $totalModulos = count($listaModulos);

    foreach ($listaModulos as $modulo) {
        $idModuloActual = $modulo['idModulo'];
        $notas = obtenerNotasModulo($idEstudiante, $idModuloActual);

        $nota1ev    = null;
        $nota1final = null;
        $nota2ev    = null;
        $nota2final = null;

        if ($notas) {
            if ($notas['nota_1ev'] !== null) {
                $nota1ev    = floatval($notas['nota_1ev']);
            }
            if ($notas['nota_1final'] !== null) {
                $nota1final = floatval($notas['nota_1final']);
            }
            if ($notas['nota_2ev'] !== null) {
                $nota2ev    = floatval($notas['nota_2ev']);
            }
            if ($notas['nota_2final'] !== null) {
                $nota2final = floatval($notas['nota_2final']);
            }
        }

        $notaDefinitiva1 = calcularNotaDefinitiva($nota1ev, $nota1final);
        $notaDefinitiva2 = calcularNotaDefinitiva($nota2ev, $nota2final);

        $sumaEvaluaciones = 0;
        $evaluacionesConNota = 0;

        if ($notaDefinitiva1 !== null) {
            $sumaEvaluaciones += $notaDefinitiva1;
            $evaluacionesConNota++;
        }
        if ($notaDefinitiva2 !== null) {
            $sumaEvaluaciones += $notaDefinitiva2;
            $evaluacionesConNota++;
        }

        $mediaExamenes = 0;
        if ($evaluacionesConNota > 0) {
            $mediaExamenes = $sumaEvaluaciones / $evaluacionesConNota;
        }

        $calificacionesRetos = listarCalificacionesRetoPorModulo($idModuloActual);
        $mediaRetos = 0;
        if (isset($calificacionesRetos[$idEstudiante])) {
            $mediaRetos = floatval($calificacionesRetos[$idEstudiante]);
        }

        $notaFinal = ($mediaExamenes * 0.75) + ($mediaRetos * 0.25);

        if ($evaluacionesConNota == 0) {
            $estado = "Pendiente";
        } elseif ($notaFinal >= 5) {
            $estado = "Aprobado";
        } else {
            $estado = "Suspenso";
            $resumen['tiene_suspensos'] = true;
        }

        $detalle = [];
        $detalle['idModulo'] = $idModuloActual;
        $detalle['nombreModulo'] = $modulo['nombreModulo'];
        $detalle['media_retos'] = round($mediaRetos, 2);
        $detalle['estado'] = $estado;

        if ($evaluacionesConNota > 0) {
            $detalle['media_notas'] = round($mediaExamenes, 2);
            $detalle['nota_final'] = round($notaFinal, 2);
        } else {
            $detalle['media_notas'] = "-";
            $detalle['nota_final'] = "-";
        }

        $resumen['detalles_modulos'][] = $detalle;

        if ($evaluacionesConNota > 0) {
            $sumaModulos += $mediaExamenes;
            $sumaRetos += $mediaRetos;
            $modulosConNotas++;
        }
    }

    if ($modulosConNotas > 0) {
        $mediaModulos = $sumaModulos / $modulosConNotas;
        $mediaRetosGlobal = $sumaRetos / $modulosConNotas;
        $promedioGlobal = ($mediaModulos * 0.75) + ($mediaRetosGlobal * 0.25);

        $resumen['media_modulos'] = round($mediaModulos, 2);
        $resumen['media_retos'] = round($mediaRetosGlobal, 2);
        $resumen['promedio_global'] = round($promedioGlobal, 2);
        $resumen['calculo_completo'] = ($modulosConNotas == $totalModulos);

        if ($resumen['tiene_suspensos']) {
            $resumen['estado_global'] = 'SUSPENSO';
        } elseif ($resumen['calculo_completo'] && $promedioGlobal >= 5) {
            $resumen['estado_global'] = 'APROBADO';
        }
    } else {
        $resumen['media_modulos'] = "-";
        $resumen['media_retos'] = "-";
        $resumen['promedio_global'] = "-";
        $resumen['calculo_completo'] = false;
    }

    return $resumen;
}

function generarDatosBoletinCiclo($idCiclo) {
    $con = obtenerConexion();

    $stmt = mysqli_prepare($con,
        "SELECT e.*, c.nombreCiclo, c.abreviaturaCiclo, n.nombreNivel
         FROM estudiantes e
         JOIN ciclos c ON e.idCiclo = c.idCiclo
         JOIN niveles n ON c.idNivel = n.idNivel
         WHERE e.idCiclo = ? ORDER BY e.nombreEstudiante ASC");
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

    foreach ($estudiantes as &$est) {
        $est['nota_tfg'] = $tfgMap[$est['idEstudiante']] ?? null;
        $est['modulos'] = [];
        foreach ($modulos as $mod) {
            $est['modulos'][] = [
                'idModulo'     => $mod['idModulo'],
                'nombreModulo' => $mod['nombreModulo'],
                'notas'        => $gradeMap[$est['idEstudiante']][$mod['idModulo']] ?? null,
            ];
        }
    }

    return ['estudiantes' => $estudiantes, 'modulos' => $modulos];
}
