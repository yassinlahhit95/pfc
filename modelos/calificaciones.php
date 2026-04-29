<?php
require_once("conectar.php");

// Ver notas de un alumno en un modulo
function obtenerNotasModulo($idEst, $idMod) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEst AND idModulo = $idMod";
    $resultado = mysqli_query($db, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $datos;
}

// Sacar todas las notas para admin
function listarCalificacionesGeneral() {
    $db = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo ORDER BY estudiantes.idEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Sacar nota por ID
function obtenerCalificacionPorId($id) {
    if (empty($id) || !is_numeric($id)) {
        return null;
    }
    $db = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = " . (int)$id;
    $resultado = mysqli_query($db, $sql);
    $datos = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $datos;
}

// Borrar nota
function eliminarCalificacion($id) {
    $db = obtenerConexion();
    $resultado = mysqli_query($db, "DELETE FROM calificaciones_modulos WHERE idCalificacion = $id");
    mysqli_close($db);
    return $resultado;
}

// Notas de un alumno
function listarCalificacionesPorEstudiante($idEst) {
    $db = obtenerConexion();
    $sql = "SELECT calificaciones_modulos.*, modulos.nombreModulo FROM calificaciones_modulos JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo WHERE idEstudiante = $idEst";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Notas para profes con filtro
function listarCalificacionesPorProfesorFiltrado($idProf, $idCiclo = 0, $idMod = 0) {
    $db = obtenerConexion();
    $where = "WHERE modulos.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProf)";
    if ($idCiclo > 0) { $where = $where . " AND modulos.idCiclo = $idCiclo"; }
    if ($idMod > 0) { $where = $where . " AND modulos.idModulo = $idMod"; }
    
    $sql = "SELECT calificaciones_modulos.*, estudiantes.nombreEstudiante, modulos.nombreModulo FROM calificaciones_modulos JOIN estudiantes ON calificaciones_modulos.idEstudiante = estudiantes.idEstudiante JOIN modulos ON calificaciones_modulos.idModulo = modulos.idModulo $where ORDER BY estudiantes.nombreEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}

// Guardar o actualizar
function actualizarOCrearNotaCompleta($idEst, $idMod, $n1, $n1f, $n2, $n2f, $obs) {
    $db = obtenerConexion();
    $sqlCheck = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = $idEst AND idModulo = $idMod";
    $resCheck = mysqli_query($db, $sqlCheck);
    
    if(mysqli_num_rows($resCheck) > 0) {
        $sql = "UPDATE calificaciones_modulos SET nota_1ev='$n1', nota_1final='$n1f', nota_2ev='$n2', nota_2final='$n2f', observaciones='$obs' WHERE idEstudiante=$idEst AND idModulo=$idMod";
    } else {
        $sql = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) VALUES ($idEst, $idMod, '$n1', '$n1f', '$n2', '$n2f', '$obs')";
    }
    
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Lista de un modulo para el formulario masivo
function listarCalificacionesPorModulo($idMod) {
    $db = obtenerConexion();
    $resMod = mysqli_query($db, "SELECT idCiclo FROM modulos WHERE idModulo = $idMod");
    $datosMod = mysqli_fetch_assoc($resMod);
    $idCiclo = isset($datosMod['idCiclo']) ? $datosMod['idCiclo'] : 0;
    
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, cm.nota_1ev as calificacion, cm.observaciones FROM estudiantes e LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND cm.idModulo = $idMod WHERE e.idCiclo = $idCiclo ORDER BY e.nombreEstudiante ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { $lista[] = $fila; }
    mysqli_close($db);
    return $lista;
}
// --- LÓGICA DE NEGOCIO PARA RESULTADOS FINALES (75% Módulos / 25% Retos) ---

/**
 * Calcula los resultados finales para todos los estudiantes de un ciclo.
 */
function obtenerResultadosFinalesCiclo($idCiclo) {
    require_once("modulos.php");
    require_once("estudiantes.php");
    require_once("retos.php");

    $estudiantes = listarEstudiantesPorCiclo($idCiclo);
    $modulos = obtenerModulosPorCiclo($idCiclo);
    $resultados = [];

    foreach ($estudiantes as $est) {
        $resultados[] = obtenerResultadosFinalesEstudiante($est['idEstudiante'], $modulos);
    }
    return $resultados;
}

/**
 * Calcula los resultados finales para un estudiante específico.
 */
function obtenerResultadosFinalesEstudiante($idEst, $modulos = null) {
    require_once("modulos.php");
    require_once("retos.php");
    require_once("estudiantes.php");

    if ($modulos === null) {
        $est = obtenerEstudiantePorId($idEst);
        $modulos = obtenerModulosPorCiclo($est['idCiclo']);
    } else {
        $est = obtenerEstudiantePorId($idEst);
    }

    $datos_estudiante = [
        'idEstudiante' => $idEst,
        'nombreEstudiante' => strtoupper($est['nombreEstudiante']),
        'nombreCiclo' => $est['nombreCiclo'],
        'detalles_modulos' => [],
        'promedio_global' => 0,
        'estado_global' => 'PENDIENTE',
        'tiene_suspensos' => false
    ];

    $suma_final_acumulada = 0;
    $modulos_con_nota = 0;
    $total_modulos_ciclo = count($modulos);

    foreach ($modulos as $mod) {
        $idMod = $mod['idModulo'];
        
        // 1. Media de Módulo (75%)
        $notas = obtenerNotasModulo($idEst, $idMod);
        $campos = ['nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final'];
        $suma_m = 0; $cont_m = 0;
        
        if ($notas) {
            foreach ($campos as $c) {
                // Si el campo existe y no es una cadena vacía ni nulo, lo contamos (incluyendo el 0.00)
                if (isset($notas[$c]) && is_numeric($notas[$c]) && !empty($notas[$c])) {
                    $suma_m += (float)$notas[$c];
                    $cont_m++;
                }
            }
        }
        
        $media_m = ($cont_m > 0) ? $suma_m / $cont_m : 0;

        // 2. Media de Retos (25%)
        $medias_retos = listarCalificacionesRetoPorModulo($idMod);
        $media_r = isset($medias_retos[$idEst]) ? (float)$medias_retos[$idEst] : 0;

        // 3. Nota Final Módulo
        $nota_f = ($media_m * 0.75) + ($media_r * 0.25);
        
        // Definición de Estado
        if (empty($cont_m)) { 
            $estado_m = "Pendiente"; 
        } elseif ($nota_f >= 5) { 
            $estado_m = "Aprobado"; 
        } else { 
            $estado_m = "Suspenso";
            $datos_estudiante['tiene_suspensos'] = true; 
        }

        $datos_estudiante['detalles_modulos'][] = [
            'idModulo' => $idMod,
            'nombreModulo' => $mod['nombreModulo'],
            'media_notas' => round($media_m, 2),
            'media_retos' => round($media_r, 2),
            'nota_final' => round($nota_f, 2),
            'estado' => $estado_m
        ];

        if ($cont_m > 0) {
            $suma_final_acumulada += $nota_f;
            $modulos_con_nota++;
        }
    }

    // Solo calculamos el promedio global y estado si TODOS los módulos tienen al menos una nota
    if ($modulos_con_nota === $total_modulos_ciclo && $total_modulos_ciclo > 0) {
        $promedio = $suma_final_acumulada / $modulos_con_nota;
        $datos_estudiante['promedio_global'] = round($promedio, 2);
        
        if ($promedio >= 5 && !$datos_estudiante['tiene_suspensos']) {
            $datos_estudiante['estado_global'] = 'APROBADO';
        } else {
            $datos_estudiante['estado_global'] = 'SUSPENSO';
        }
        $datos_estudiante['calculo_completo'] = true;
    } else {
        $datos_estudiante['promedio_global'] = "-";
        $datos_estudiante['estado_global'] = 'PENDIENTE (Incompleto)';
        $datos_estudiante['calculo_completo'] = false;
    }

    return $datos_estudiante;
}
?>