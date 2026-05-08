<?php
require_once __DIR__ . "/conectar.php";

function obtenerNotasModulo($idEstudiante, $idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosCalificaciones = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosCalificaciones;
}

function listarCalificacionesGeneral() {
    $con = obtenerConexion();
    $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
            FROM calificaciones_modulos cm
            JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
            JOIN modulos m ON cm.idModulo = m.idModulo
            ORDER BY e.idEstudiante ASC";

    $resultado = mysqli_query($con, $sql);
    $listaCalificaciones = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaCalificaciones[] = $fila;
    }
    mysqli_close($con);
    return $listaCalificaciones;
}

function obtenerCalificacionPorId($idCalificacion) {
    if (empty($idCalificacion) || !is_numeric($idCalificacion)) {
        return null;
    }
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    $idCalificacion = (int)$idCalificacion;
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datosCalificacion = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosCalificacion;
}

function eliminarCalificacion($idCalificacion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    $resultado = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $resultado;
}

function listarCalificacionesPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT cm.*, m.nombreModulo FROM calificaciones_modulos cm JOIN modulos m ON cm.idModulo = m.idModulo WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaEstudiante = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaEstudiante[] = $fila;
    }
    mysqli_close($con);
    return $listaEstudiante;
}

function listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo = 0, $idModulo = 0) {
    $con = obtenerConexion();

    // Solo permitimos visualizar las calificaciones de los ciclos/módulos que el profesor imparte
    $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
            FROM calificaciones_modulos cm
            JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
            JOIN modulos m ON cm.idModulo = m.idModulo
            WHERE m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)";

    $types = "i";
    $params = [$idProfesor];

    if ($idCiclo > 0) {
        $sql .= " AND m.idCiclo = ?";
        $types .= "i";
        $params[] = $idCiclo;
    }
    if ($idModulo > 0) {
        $sql .= " AND m.idModulo = ?";
        $types .= "i";
        $params[] = $idModulo;
    }

    $sql .= " ORDER BY e.nombreEstudiante ASC";

    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $listaFiltrada = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $listaFiltrada[] = $fila;
    }
    mysqli_close($con);
    return $listaFiltrada;
}

function actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones) {
    $con = obtenerConexion();

    $sqlBuscar = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmtBuscar = mysqli_prepare($con, $sqlBuscar);
    mysqli_stmt_bind_param($stmtBuscar, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmtBuscar);
    $resultado = mysqli_stmt_get_result($stmtBuscar);

    if (mysqli_num_rows($resultado) > 0) {
        $sqlGuardar = "UPDATE calificaciones_modulos SET nota_1ev=?, nota_1final=?, nota_2ev=?, nota_2final=?, observaciones=? WHERE idEstudiante=? AND idModulo=?";
        $stmtGuardar = mysqli_prepare($con, $sqlGuardar);
        mysqli_stmt_bind_param($stmtGuardar, "sssssii", $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones, $idEstudiante, $idModulo);
    } else {
        $sqlGuardar = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtGuardar = mysqli_prepare($con, $sqlGuardar);
        mysqli_stmt_bind_param($stmtGuardar, "iisssss", $idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones);
    }

    $ok = mysqli_stmt_execute($stmtGuardar);
    mysqli_close($con);
    return $ok;
}

function listarCalificacionesPorModulo($idModulo) {
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
    mysqli_close($con);
    return $lista;
}

// --- LÓGICA DE NEGOCIO PARA RESULTADOS FINALES (75% Módulos / 25% Retos) ---

function obtenerResultadosFinalesCiclo($idCiclo) {
    require_once __DIR__ . "/modulos.php";
    require_once __DIR__ . "/estudiantes.php";
    require_once __DIR__ . "/retos.php";

    $listaEstudiantes = listarEstudiantesPorCiclo($idCiclo);
    $listaModulos = obtenerModulosPorCiclo($idCiclo);
    $listaResultados = [];

    foreach ($listaEstudiantes as $estudiante) {
        $listaResultados[] = obtenerResultadosFinalesEstudiante($estudiante['idEstudiante'], $listaModulos);
    }
    return $listaResultados;
}

function obtenerResultadosFinalesEstudiante($idEstudiante, $listaModulos = null) {
    require_once __DIR__ . "/modulos.php";
    require_once __DIR__ . "/retos.php";
    require_once __DIR__ . "/estudiantes.php";

    if ($listaModulos === null) {
        $datosEst = obtenerEstudiantePorId($idEstudiante);
        $listaModulos = obtenerModulosPorCiclo($datosEst['idCiclo']);
    } else {
        $datosEst = obtenerEstudiantePorId($idEstudiante);
    }

    $resumenEstudiante = [
        'idEstudiante' => $idEstudiante,
        'nombreEstudiante' => strtoupper($datosEst['nombreEstudiante']),
        'nombreCiclo' => $datosEst['nombreCiclo'],
        'detalles_modulos' => [],
        'promedio_global' => 0,
        'estado_global' => 'PENDIENTE',
        'tiene_suspensos' => false
    ];

    $sumaFinalAcumulada = 0;
    $contadorModulosConNota = 0;
    $totalModulosCiclo = count($listaModulos);

    foreach ($listaModulos as $modulo) {
        $idModuloActual = $modulo['idModulo'];

        // 1. Media de Módulo (Peso 75%)
        $datosNotas = obtenerNotasModulo($idEstudiante, $idModuloActual);
        $camposNotas = ['nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final'];
        $sumaNotasModulo = 0;
        $cantidadNotasValidas = 0;

        if ($datosNotas) {
            foreach ($camposNotas as $campo) {
                if (isset($datosNotas[$campo]) && is_numeric($datosNotas[$campo]) && !empty($datosNotas[$campo])) {
                    $sumaNotasModulo += (float)$datosNotas[$campo];
                    $cantidadNotasValidas++;
                }
            }
        }

        $mediaNotasModulo = ($cantidadNotasValidas > 0) ? $sumaNotasModulo / $cantidadNotasValidas : 0;

        // 2. Media de Retos (Peso 25%)
        $mapaMediasRetos = listarCalificacionesRetoPorModulo($idModuloActual);
        $mediaRetosModulo = (float)($mapaMediasRetos[$idEstudiante] ?? 0);

        // 3. Cálculo de Nota Final del Módulo
        $notaFinalModulo = ($mediaNotasModulo * 0.75) + ($mediaRetosModulo * 0.25);

        if ($cantidadNotasValidas === 0) {
            $estadoModulo = "Pendiente";
        } elseif ($notaFinalModulo >= 5) {
            $estadoModulo = "Aprobado";
        } else {
            $estadoModulo = "Suspenso";
            $resumenEstudiante['tiene_suspensos'] = true;
        }

        $resumenEstudiante['detalles_modulos'][] = [
            'idModulo' => $idModuloActual,
            'nombreModulo' => $modulo['nombreModulo'],
            'media_notas' => round($mediaNotasModulo, 2),
            'media_retos' => round($mediaRetosModulo, 2),
            'nota_final' => round($notaFinalModulo, 2),
            'estado' => $estadoModulo
        ];

        if ($cantidadNotasValidas > 0) {
            $sumaFinalAcumulada += $notaFinalModulo;
            $contadorModulosConNota++;
        }
    }

    // El promedio global solo se calcula si se han cursado y evaluado TODOS los módulos del ciclo
    if ($contadorModulosConNota === $totalModulosCiclo && $totalModulosCiclo > 0) {
        $promedioGlobal = $sumaFinalAcumulada / $contadorModulosConNota;
        $resumenEstudiante['promedio_global'] = round($promedioGlobal, 2);

        if ($promedioGlobal >= 5 && !$resumenEstudiante['tiene_suspensos']) {
            $resumenEstudiante['estado_global'] = 'APROBADO';
        } else {
            $resumenEstudiante['estado_global'] = 'SUSPENSO';
        }
        $resumenEstudiante['calculo_completo'] = true;
    } else {
        $resumenEstudiante['promedio_global'] = "-";
        $resumenEstudiante['estado_global'] = 'PENDIENTE (Incompleto)';
        $resumenEstudiante['calculo_completo'] = false;
    }

    return $resumenEstudiante;
}
?>
