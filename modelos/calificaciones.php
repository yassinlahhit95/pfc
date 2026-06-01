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
    mysqli_close($con);
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
    mysqli_close($con);
    return $datos;
}

function eliminarCalificacion($idCalificacion)
{
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    $exito = mysqli_stmt_execute($stmt);
    mysqli_close($con);
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
    mysqli_close($con);
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
    mysqli_close($con);
    return $lista;
}

function actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones)
{
    if ($nota1ev === '')   $nota1ev   = null;
    if ($nota1final === '') $nota1final = null;
    if ($nota2ev === '')   $nota2ev   = null;
    if ($nota2final === '') $nota2final = null;

    $con = obtenerConexion();

    $sql1 = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmt = mysqli_prepare($con, $sql1);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        $sql1 = "UPDATE calificaciones_modulos SET nota_1ev=?, nota_1final=?, nota_2ev=?, nota_2final=?, observaciones=? WHERE idEstudiante=? AND idModulo=?";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "ddddsii", $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones, $idEstudiante, $idModulo);
    } else {
        $sql1 = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($con, $sql1);
        mysqli_stmt_bind_param($stmt, "iidddds", $idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones);
    }

    $exito = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $exito;
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
    mysqli_close($con);
    return $lista;
}

function listarResultadosFinalesCiclo($idCiclo)
{
    $listaEstudiantes = listarEstudiantesPorCiclo($idCiclo);
    $listaModulos = listarModulosPorCiclo($idCiclo);
    $listaResultados = [];
    foreach ($listaEstudiantes as $estudiante) {
        $listaResultados[] = obtenerResultadosFinalesEstudiante($estudiante['idEstudiante'], $listaModulos);
    }
    return $listaResultados;
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
