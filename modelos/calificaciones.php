<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/modulos.php";
require_once __DIR__ . "/estudiantes.php";
require_once __DIR__ . "/retos.php";

// Devuelve las notas de un estudiante en un módulo concreto
function obtenerNotasModulo($idEstudiante, $idModulo) {
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

// Devuelve todas las calificaciones con el nombre del estudiante y del módulo
function listarCalificacionesGeneral() {
    $con = obtenerConexion();
    $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
            FROM calificaciones_modulos cm
            JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
            JOIN modulos m ON cm.idModulo = m.idModulo
            ORDER BY e.idEstudiante ASC";

    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($con);
    return $lista;
}

// Devuelve una calificación buscando por su ID
function obtenerCalificacionPorId($idCalificacion) {
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

// Elimina una calificación de la base de datos
function eliminarCalificacion($idCalificacion) {
    $con = obtenerConexion();
    $sql = "DELETE FROM calificaciones_modulos WHERE idCalificacion = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCalificacion);
    $exito = mysqli_stmt_execute($stmt);
    mysqli_close($con);
    return $exito;
}

// Devuelve todas las calificaciones de un estudiante con el nombre de cada módulo
function listarCalificacionesPorEstudiante($idEstudiante) {
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

// Devuelve las calificaciones del profesor con filtros opcionales por ciclo o módulo
function listarCalificacionesPorProfesorFiltrado($idProfesor, $idCiclo = 0, $idModulo = 0) {
    $con = obtenerConexion();

    // Si se filtra por módulo concreto
    if ($idModulo > 0) {
        $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
                FROM calificaciones_modulos cm
                JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
                JOIN modulos m ON cm.idModulo = m.idModulo
                WHERE m.idModulo = ?
                AND (
                    m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                    OR m.idModulo IN (SELECT idModulo FROM profesor_modulo WHERE idProfesor = ?)
                )
                ORDER BY e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $idModulo, $idProfesor, $idProfesor);

    // Si se filtra por ciclo
    } elseif ($idCiclo > 0) {
        $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
                FROM calificaciones_modulos cm
                JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
                JOIN modulos m ON cm.idModulo = m.idModulo
                WHERE m.idCiclo = ?
                AND (
                    m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                    OR m.idModulo IN (SELECT idModulo FROM profesor_modulo WHERE idProfesor = ?)
                )
                ORDER BY e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "iii", $idCiclo, $idProfesor, $idProfesor);

    // Sin filtro, se devuelven todas las calificaciones del profesor
    } else {
        $sql = "SELECT cm.*, e.nombreEstudiante, m.nombreModulo
                FROM calificaciones_modulos cm
                JOIN estudiantes e ON cm.idEstudiante = e.idEstudiante
                JOIN modulos m ON cm.idModulo = m.idModulo
                WHERE (
                    m.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
                    OR m.idModulo IN (SELECT idModulo FROM profesor_modulo WHERE idProfesor = ?)
                )
                ORDER BY e.nombreEstudiante ASC";
        $stmt = mysqli_prepare($con, $sql);
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

// Guarda las notas: si ya existen las actualiza, si no las crea nuevas
function actualizarOCrearNotaCompleta($idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones) {
    $con = obtenerConexion();

    // Primero comprobamos si ya existe una fila para este estudiante y módulo
    $sqlBuscar = "SELECT idCalificacion FROM calificaciones_modulos WHERE idEstudiante = ? AND idModulo = ?";
    $stmt = mysqli_prepare($con, $sqlBuscar);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idModulo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($resultado) > 0) {
        // Si ya existe, actualizamos los datos
        $sql = "UPDATE calificaciones_modulos SET nota_1ev=?, nota_1final=?, nota_2ev=?, nota_2final=?, observaciones=? WHERE idEstudiante=? AND idModulo=?";
        $stmt2 = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt2, "sssssii", $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones, $idEstudiante, $idModulo);
    } else {
        // Si no existe, insertamos una fila nueva
        $sql = "INSERT INTO calificaciones_modulos (idEstudiante, idModulo, nota_1ev, nota_1final, nota_2ev, nota_2final, observaciones) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt2 = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt2, "iisssss", $idEstudiante, $idModulo, $nota1ev, $nota1final, $nota2ev, $nota2final, $observaciones);
    }

    $exito = mysqli_stmt_execute($stmt2);
    mysqli_close($con);
    return $exito;
}

// Devuelve los estudiantes de un módulo con su nota (puede estar vacía si aún no tiene)
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

// Calcula y devuelve el resumen final de todos los estudiantes de un ciclo
function obtenerResultadosFinalesCiclo($idCiclo) {
    $listaEstudiantes = listarEstudiantesPorCiclo($idCiclo);
    $listaModulos = obtenerModulosPorCiclo($idCiclo);
    $listaResultados = [];

    // Calculamos el resultado de cada estudiante y lo guardamos en la lista
    foreach ($listaEstudiantes as $estudiante) {
        $listaResultados[] = obtenerResultadosFinalesEstudiante($estudiante['idEstudiante'], $listaModulos);
    }
    return $listaResultados;
}

// Calcula el resultado final de un estudiante: medias, nota final y estado global
function obtenerResultadosFinalesEstudiante($idEstudiante, $listaModulos = null) {

    // Obtenemos los datos del estudiante para saber su nombre y su ciclo
    $datosEstudiante = obtenerEstudiantePorId($idEstudiante);

    // Si no nos pasan los módulos, los buscamos según el ciclo del estudiante
    if ($listaModulos == null) {
        $listaModulos = obtenerModulosPorCiclo($datosEstudiante['idCiclo']);
    }

    // Preparamos el array de resultados con los valores por defecto
    $resumen = [];
    $resumen['idEstudiante']     = $idEstudiante;
    $resumen['nombreEstudiante'] = $datosEstudiante['nombreEstudiante'];
    $resumen['nombreCiclo']      = $datosEstudiante['nombreCiclo'];
    $resumen['detalles_modulos'] = [];
    $resumen['media_modulos']    = 0;
    $resumen['media_retos']      = 0;
    $resumen['promedio_global']  = 0;
    $resumen['estado_global']    = 'PENDIENTE';
    $resumen['tiene_suspensos']  = false;

    // Añadimos calificación de TFG
    require_once __DIR__ . "/tfg.php";
    $calificacionTFG = obtenerCalificacionTFG($idEstudiante);
    $resumen['nota_tfg'] = $calificacionTFG ? $calificacionTFG['nota'] : null;
    $resumen['obs_tfg'] = $calificacionTFG ? $calificacionTFG['observaciones'] : '';

    // Variables para acumular los datos de todos los módulos
...

    $sumaRetos       = 0;
    $modulosConNotas = 0;
    $totalModulos    = count($listaModulos);

    // Recorremos cada módulo del ciclo para calcular la nota del estudiante
    foreach ($listaModulos as $modulo) {
        $idModuloActual = $modulo['idModulo'];

        // Obtenemos las notas guardadas del estudiante en este módulo
        $notas = obtenerNotasModulo($idEstudiante, $idModuloActual);

        // Inicializamos las cuatro notas posibles como vacías
        $nota1ev           = null;
        $nota1recuperacion = null;
        $nota2ev           = null;
        $nota2recuperacion = null;

        // Si tiene notas guardadas, las leemos y convertimos a número decimal
        if ($notas != null) {
            if ($notas['nota_1ev'] != null)    { $nota1ev           = floatval($notas['nota_1ev']); }
            if ($notas['nota_1final'] != null)  { $nota1recuperacion = floatval($notas['nota_1final']); }
            if ($notas['nota_2ev'] != null)    { $nota2ev           = floatval($notas['nota_2ev']); }
            if ($notas['nota_2final'] != null)  { $nota2recuperacion = floatval($notas['nota_2final']); }
        }

        // La nota definitiva de cada evaluación es la más alta entre la nota y la recuperación
        $notaDefinitiva1 = $nota1ev;
        if ($nota1recuperacion != null && $nota1recuperacion > $nota1ev) {
            $notaDefinitiva1 = $nota1recuperacion;
        }

        $notaDefinitiva2 = $nota2ev;
        if ($nota2recuperacion != null && $nota2recuperacion > $nota2ev) {
            $notaDefinitiva2 = $nota2recuperacion;
        }

        // Sumamos las evaluaciones que tienen nota para calcular la media de exámenes
        $sumaEvaluaciones    = 0;
        $evaluacionesConNota = 0;
        if ($notaDefinitiva1 != null) { $sumaEvaluaciones += $notaDefinitiva1; $evaluacionesConNota++; }
        if ($notaDefinitiva2 != null) { $sumaEvaluaciones += $notaDefinitiva2; $evaluacionesConNota++; }

        $mediaExamenes = 0;
        if ($evaluacionesConNota > 0) {
            $mediaExamenes = $sumaEvaluaciones / $evaluacionesConNota;
        }

        // Obtenemos la media de retos del estudiante en este módulo
        $calificacionesRetos = listarCalificacionesRetoPorModulo($idModuloActual);
        $mediaRetos = 0;
        if (isset($calificacionesRetos[$idEstudiante]) && $calificacionesRetos[$idEstudiante] != null) {
            $mediaRetos = floatval($calificacionesRetos[$idEstudiante]);
        }

        // La nota final del módulo es 75% exámenes y 25% retos
        $notaFinal = ($mediaExamenes * 0.75) + ($mediaRetos * 0.25);

        // Determinamos si el módulo está aprobado, suspenso o pendiente
        if ($evaluacionesConNota == 0) {
            $estado = "Pendiente";
        } elseif ($notaFinal >= 5) {
            $estado = "Aprobado";
        } else {
            $estado = "Suspenso";
            $resumen['tiene_suspensos'] = true;
        }

        // Guardamos el detalle de este módulo en el resumen
        $detalle = [];
        $detalle['idModulo']     = $idModuloActual;
        $detalle['nombreModulo'] = $modulo['nombreModulo'];
        $detalle['media_retos']  = round($mediaRetos, 2);
        $detalle['estado']       = $estado;

        if ($evaluacionesConNota > 0) {
            $detalle['media_notas'] = round($mediaExamenes, 2);
            $detalle['nota_final']  = round($notaFinal, 2);
        } else {
            $detalle['media_notas'] = "-";
            $detalle['nota_final']  = "-";
        }

        $resumen['detalles_modulos'][] = $detalle;

        // Solo contamos los módulos que ya tienen alguna nota para el promedio global
        if ($evaluacionesConNota > 0) {
            $sumaModulos     += $mediaExamenes;
            $sumaRetos       += $mediaRetos;
            $modulosConNotas++;
        }
    }

    // Si hay módulos con notas, calculamos el promedio global del estudiante
    if ($modulosConNotas > 0) {
        $mediaModulos     = $sumaModulos / $modulosConNotas;
        $mediaRetosGlobal = $sumaRetos / $modulosConNotas;
        $promedioGlobal   = ($mediaModulos * 0.75) + ($mediaRetosGlobal * 0.25);

        $resumen['media_modulos']    = round($mediaModulos, 2);
        $resumen['media_retos']      = round($mediaRetosGlobal, 2);
        $resumen['promedio_global']  = round($promedioGlobal, 2);
        
        // El cálculo está completo solo si todos los módulos tienen notas
        $resumen['calculo_completo'] = ($modulosConNotas == $totalModulos);

        // Para aprobar necesita media de 5 o más y no tener ningún módulo suspenso
        // Si no se han cursado todos los módulos aún, el estado es PENDIENTE a menos que ya esté suspendido
        if ($resumen['calculo_completo']) {
            if ($promedioGlobal >= 5 && !$resumen['tiene_suspensos']) {
                $resumen['estado_global'] = 'APROBADO';
            } else {
                $resumen['estado_global'] = 'SUSPENSO';
            }
        } else {
            $resumen['estado_global'] = $resumen['tiene_suspensos'] ? 'SUSPENSO' : 'PENDIENTE';
        }
    } else {
        // Todavía no hay ninguna nota registrada
        $resumen['media_modulos']    = "-";
        $resumen['media_retos']      = "-";
        $resumen['promedio_global']  = "-";
        $resumen['estado_global']    = 'PENDIENTE';
        $resumen['calculo_completo'] = false;
    }

    return $resumen;
}
?>
