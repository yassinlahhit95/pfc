<?php
require_once __DIR__ . "/conectar.php";

// Obtener las notas de un alumno en un módulo específico
function obtenerNotasModulo($idEstudiante, $idModulo) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    $datosCalificaciones = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosCalificaciones;
}

// Listar todas las calificaciones registradas (Uso para Administradores)
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

// Obtener una calificación específica por su ID único
function obtenerCalificacionPorId($idCalificacion) {
    if (empty($idCalificacion) || !is_numeric($idCalificacion)) {
        return null;
    }
    $con = obtenerConexion();
    $sql = "SELECT * FROM calificaciones_modulos WHERE idCalificacion = " . (int)$idCalificacion;
    $resultado = mysqli_query($con, $sql);
    $datosCalificacion = mysqli_fetch_assoc($resultado);
    mysqli_close($con);
    return $datosCalificacion;
}

// Eliminar un registro de calificación por su ID
function eliminarCalificacion($idCalificacion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = $idCalificacion";
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Obtener el historial completo de notas de un estudiante (todos sus módulos)
function listarCalificacionesPorEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT cm.*, m.nombreModulo 
            FROM calificaciones_modulos cm 
            JOIN modulos m ON cm.idModulo = m.idModulo 
            WHERE idEstudiante = $idEstudiante";
            
    $resultado = mysqli_query($con, $sql);
    $listaEstudiante = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaEstudiante[] = $fila; 
    }
    mysqli_close($con);
    return $listaEstudiante;
}

// Listar calificaciones para profesores con filtros opcionales (Ciclo y Módulo)
function listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo = 0, $idModulo = 0) {
    $con = obtenerConexion();
    
    // Solo permitimos ver notas de los ciclos/módulos que el profesor imparte
    $clausulaWhere = "WHERE m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor)";
    
    if ($idCiclo > 0) { 
        $clausulaWhere .= " AND m.idCiclo = $idCiclo"; 
    }
    if ($idModulo > 0) { 
        $clausulaWhere .= " AND m.idModulo = $idModulo"; 
    }
    
    $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo 
            FROM calificaciones_modulos cm 
            JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante 
            JOIN modulos m ON cm.idModulo = m.idModulo 
            $clausulaWhere 
            ORDER BY e.nombreEstudiante ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaFiltrada = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaFiltrada[] = $fila; 
    }
    mysqli_close($con);
    return $listaFiltrada;
}

// Guardar una nueva calificación o actualizarla si ya existe (Upsert)
function actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones) {
    $con = obtenerConexion();
    
    $sql = "SELECT idCalificacion FROM calificaciones_modulos 
                 WHERE idEstudiante = $idEstudiante AND idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    
    if(mysqli_num_rows($resultado) > 0) {
        // Actualizamos registro existente
        $sql = "UPDATE calificaciones_modulos 
                SET nota_1ev='$nota1ev', nota_1final='$nota1final', 
                    nota_2ev='$nota2ev', nota_2final='$nota2final', observaciones='$observaciones' 
                WHERE idEstudiante=$idEstudiante AND idModulo=$idModulo";
    } else {
        // Creamos nuevo registro
        $sql = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) 
                VALUES ($idEstudiante, $idModulo, '$nota1ev', '$nota1final', '$nota2ev', '$nota2final', '$observaciones')";
    }
    
    $resultado = mysqli_query($con, $sql);
    mysqli_close($con);
    return $resultado;
}

// Listar datos para el formulario de calificar un módulo completo (todos los alumnos del ciclo)
function listarCalificacionesPorModulo($idModulo) {
    $con = obtenerConexion();
    
    // Obtenemos primero el ciclo al que pertenece el módulo
    $sql = "SELECT idCiclo FROM modulos WHERE idModulo = $idModulo";
    $resultado = mysqli_query($con, $sql);
    $datosModulo = mysqli_fetch_assoc($resultado);
    $idCiclo = (int)($datosModulo['idCiclo'] ?? 0);
    
    // Traemos todos los alumnos del ciclo y su nota actual si la tienen
    $sql = "SELECT e.idEstudiante, e.nombreEstudiante, cm.nota_1ev as calificacion, cm.observaciones 
            FROM estudiantes e 
            LEFT JOIN calificaciones_modulos cm ON e.idEstudiante = cm.idEstudiante AND cm.idModulo = $idModulo 
            WHERE e.idCiclo = $idCiclo 
            ORDER BY e.nombreEstudiante ASC";
            
    $resultado = mysqli_query($con, $sql);
    $listaModulo = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $listaModulo[] = $fila; 
    }
    mysqli_close($con);
    return $listaModulo;
}

// --- LÓGICA DE NEGOCIO PARA RESULTADOS FINALES (75% Módulos / 25% Retos) ---

/**
 * Calcula los resultados académicos globales para todos los estudiantes de un ciclo.
 */
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

/**
 * Calcula los resultados finales de un estudiante aplicando los pesos (75% módulos, 25% retos).
 */
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
        $mediaRetosModulo = isset($mapaMediasRetos[$idEstudiante]) ? (float)$mapaMediasRetos[$idEstudiante] : 0;

        // 3. Cálculo de Nota Final del Módulo
        $notaFinalModulo = ($mediaNotasModulo * 0.75) + ($mediaRetosModulo * 0.25);
        
        // Determinar estado del módulo
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

