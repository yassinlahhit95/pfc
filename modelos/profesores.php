<?php
require_once("conectar.php");

// Ver lista de todos los profes
function listarProfesores() {
    $db = obtenerConexion();
    $sql = "SELECT * FROM profesores ORDER BY idProfesor ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Meter profe nuevo
function insertarProfesor($nombre, $email, $tel, $dni, $dir, $esp, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $db = obtenerConexion();
    $sql = "INSERT INTO profesores (nombreProfesor, emailProfesor, telefonoProfesor, dniProfesor, direccionProfesor, especialidad, fechaNacimientoProfesor, fechaAltaProfesor, ciudadProfesor, codigoPostalProfesor, observacionesProfesor) VALUES ('$nombre', '$email', '$tel', '$dni', '$dir', '$esp', '$f_nac', '$f_alta', '$ciudad', '$cp', '$obs')";
    $resultado = mysqli_query($db, $sql);
    
    // Sacamos el ID mas alto (el nuevo)
    $sqlId = "SELECT MAX(idProfesor) as ultimoId FROM profesores";
    $resId = mysqli_query($db, $sqlId);
    $filaId = mysqli_fetch_assoc($resId);
    $idNuevo = $filaId['ultimoId'];

    mysqli_close($db);
    return $idNuevo;
}

// Actualizar un profesor
function actualizarProfesor($id, $nombre, $email, $tel, $dni, $dir, $esp, $f_nac, $f_alta, $ciudad, $cp, $obs) {
    $db = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor='$nombre', emailProfesor='$email', telefonoProfesor='$tel', dniProfesor='$dni', direccionProfesor='$dir', especialidad='$esp', fechaNacimientoProfesor='$f_nac', fechaAltaProfesor='$f_alta', ciudadProfesor='$ciudad', codigoPostalProfesor='$cp', observacionesProfesor='$obs' WHERE idProfesor=$id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Unir profe con ciclo
function asociarCicloProfesor($idCic, $idProf) {
    $db = obtenerConexion();
    $sql = "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES ($idCic, $idProf)";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Unir profe con modulo
function asociarModuloProfesor($idMod, $idProf) {
    $db = obtenerConexion();
    $sql = "INSERT INTO profesor_modulo (idModulo, idProfesor) VALUES ($idMod, $idProf)";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Quitar profe
function eliminarProfesor($id) {
    $db = obtenerConexion();
    $sql = "DELETE FROM profesores WHERE idProfesor = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Datos de un profe por ID
function obtenerProfesorPorId($id) {
    $db = obtenerConexion();
    $sql = "SELECT * FROM profesores WHERE idProfesor = $id";
    $resultado = mysqli_query($db, $sql);
    $fila = mysqli_fetch_assoc($resultado);
    mysqli_close($db);
    return $fila;
}

// Ver profes que dan clase en un ciclo
function listarProfesoresPorCiclo($idCic) {
    $db = obtenerConexion();
    $sql = "SELECT p.* FROM profesores p JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor WHERE cp.idCiclo = $idCic ORDER BY p.nombreProfesor ASC";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}

// Ver que modulos tiene este profe
function obtenerIdsModulosDeProfesor($idProf) {
    $db = obtenerConexion();
    $sql = "SELECT idModulo FROM profesor_modulo WHERE idProfesor = $idProf";
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila['idModulo']; 
    }
    mysqli_close($db);
    return $lista;
}

// Borrar todas las clases de un profe
function limpiarModulosProfesor($idProf) {
    $db = obtenerConexion();
    $sql = "DELETE FROM profesor_modulo WHERE idProfesor = $idProf";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Cambiar la clave
function actualizarPasswordProfesor($id, $pass) {
    $db = obtenerConexion();
    $sql = "UPDATE profesores SET password = '$pass' WHERE idProfesor = $id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}

// Datos basicos de perfil propio
function actualizarPerfilProfesor($id, $nombre, $email, $tel) {
    $db = obtenerConexion();
    $sql = "UPDATE profesores SET nombreProfesor='$nombre', emailProfesor='$email', telefonoProfesor='$tel' WHERE idProfesor=$id";
    $resultado = mysqli_query($db, $sql);
    mysqli_close($db);
    return $resultado;
}
// Obtener profesores y sus módulos para un estudiante específico (según su ciclo)
function obtenerProfesoresConModulosParaEstudiante($idEst) {
    $db = obtenerConexion();
    
    // Primero obtenemos el ciclo del estudiante
    $sqlCiclo = "SELECT idCiclo FROM estudiantes WHERE idEstudiante = $idEst";
    $resCiclo = mysqli_query($db, $sqlCiclo);
    $filaCiclo = mysqli_fetch_assoc($resCiclo);
    $idCiclo = $filaCiclo['idCiclo'];

    // Ahora buscamos los profesores que dan clase en los módulos de ese ciclo
    $sql = "SELECT p.idProfesor, p.nombreProfesor, m.nombreModulo 
            FROM profesores p 
            JOIN profesor_modulo pm ON p.idProfesor = pm.idProfesor 
            JOIN modulos m ON pm.idModulo = m.idModulo 
            WHERE m.idCiclo = $idCiclo 
            ORDER BY p.nombreProfesor ASC, m.nombreModulo ASC";
            
    $resultado = mysqli_query($db, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) { 
        $lista[] = $fila; 
    }
    mysqli_close($db);
    return $lista;
}
?>
