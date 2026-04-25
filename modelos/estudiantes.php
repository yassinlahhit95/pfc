<?php
require_once("conectar.php");

function listarEstudiantes() {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo) 
            VALUES ('$nombre', '$email', '$telefono', '$fechaNacimiento', '$dni', '$fechaAlta', '$direccion', '$ciudad', '$codigoPostal', '$observaciones', $idCiclo)";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function actualizarEstudiante($idEstudiante, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono', fechaNacimientoEstudiante = '$fechaNacimiento', dniEstudiante = '$dni', fechaAltaEstudiante = '$fechaAlta', direccionEstudiante = '$direccion', ciudadEstudiante = '$ciudad', codigoPostalEstudiante = '$codigoPostal', observacionesEstudiante = '$observaciones', idCiclo = $idCiclo 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function listarEstudiantesPorProfesor($idProfesor) {
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = $idProfesor) 
            ORDER BY nombreEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function listarEstudiantesPorCiclo($idCiclo) {
    if ($idCiclo == "" || !is_numeric($idCiclo)) {
        return array();
    }
    $conexion = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo FROM estudiantes 
            LEFT JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo 
            WHERE estudiantes.idCiclo = $idCiclo ORDER BY idEstudiante ASC";
    $resultado = mysqli_query($conexion, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    mysqli_close($conexion);
    return $lista;
}

function eliminarEstudiante($idEstudiante) {
    $conexion = obtenerConexion();
    $sql = "DELETE FROM estudiantes WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}

function obtenerPorcentajeAprobadosGlobal() {
    $conexion = obtenerConexion();

    // 1. Obtener todos los estudiantes
    $sqlEst = "SELECT idEstudiante, idCiclo FROM estudiantes";
    $resEst = mysqli_query($conexion, $sqlEst);
    $totalEstudiantes = mysqli_num_rows($resEst);

    if ($totalEstudiantes == 0) { return 0; }

    $aprobados = 0;

    require_once "calificaciones.php";
    require_once "retos.php";
    require_once "modulos.php";

    while ($est = mysqli_fetch_assoc($resEst)) {
        $id_est = $est['idEstudiante'];
        $id_ciclo = $est['idCiclo'];

        if (!$id_ciclo) continue;

        $lista_modulos = obtenerModulosPorCiclo($id_ciclo);
        if (empty($lista_modulos)) continue;

        $suma_global = 0;
        $cont_global = 0;

        foreach ($lista_modulos as $mod) {
            $id_mod = $mod['idModulo'];

            // Media modulo
            $notas_mod = obtenerNotasModulo($id_est, $id_mod);
            $suma_m = 0; $cont_m = 0;
            $campos = array('nota_1ev', 'nota_1final', 'nota_2ev', 'nota_2final');
            foreach ($campos as $c) {
                if (isset($notas_mod[$c]) && $notas_mod[$c] > 0) {
                    $suma_m += $notas_mod[$c]; $cont_m++;
                }
            }
            $media_m = ($cont_m > 0) ? $suma_m / $cont_m : 0;

            // Media retos
            $medias_retos = listarCalificacionesRetoPorModulo($id_mod);
            $media_r = isset($medias_retos[$id_est]) ? $medias_retos[$id_est] : 0;

            $nota_f = ($media_m * 0.75) + ($media_r * 0.25);
            $suma_global += $nota_f;
            $cont_global++;
        }

        $media_final_estudiante = ($cont_global > 0) ? $suma_global / $cont_global : 0;
        if ($media_final_estudiante >= 5.00) {
            $aprobados++;
        }
    }

    mysqli_close($conexion);
    return round(($aprobados / $totalEstudiantes) * 100, 1);
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono) {
    $conexion = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante = '$nombre', emailEstudiante = '$email', telefonoEstudiante = '$telefono' 
            WHERE idEstudiante = $idEstudiante";
    $resultado = mysqli_query($conexion, $sql);
    mysqli_close($conexion);
    return $resultado;
}
?>