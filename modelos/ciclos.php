<?php
require_once __DIR__ . "/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarTodosLosCiclos() {
    $con = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel
            FROM ciclos
            JOIN niveles ON ciclos.idNivel = niveles.idNivel
            WHERE ciclos.activo = 1
            ORDER BY ciclos.idCiclo ASC";
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) return [];
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarCiclosArchivados() {
    $con = obtenerConexion();
    $sql = "SELECT ciclos.*, niveles.nombreNivel
            FROM ciclos
            JOIN niveles ON ciclos.idNivel = niveles.idNivel
            WHERE ciclos.activo = 0
            ORDER BY ciclos.fechaArchivado DESC";
    $resultado = mysqli_query($con, $sql);
    if (!$resultado) return [];
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function listarCiclosDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT c.*, n.nombreNivel
            FROM ciclos c
            JOIN niveles n ON c.idNivel = n.idNivel
            WHERE c.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR c.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerCicloPorId($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT c.*, n.nombreNivel FROM ciclos c LEFT JOIN niveles n ON n.idNivel = c.idNivel WHERE c.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function listarProfesoresDeUnCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT idProfesor FROM ciclo_profesor WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['idProfesor'];
    }
    return $lista;
}

// Nombres de los profesores asignados al ciclo (no confundir con el rol "Tutores" de padres/tutores legales).
function listarNombresProfesoresCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT p.nombreProfesor
            FROM profesores p
            JOIN ciclo_profesor cp ON p.idProfesor = cp.idProfesor
            WHERE cp.idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $nombres = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $nombres[] = $fila['nombreProfesor'];
    }
    return $nombres;
}

// Profesores de varios ciclos a la vez, agrupados por idCiclo => [['idProfesor','nombreProfesor'], ...].
// Evita el patrón N+1 de llamar listarProfesoresDeUnCiclo()/listarNombresProfesoresCiclo()
// una vez por ciclo en las vistas de listado (verCiclos.php).
function listarProfesoresPorCiclos(array $idsCiclos): array {
    if (!$idsCiclos) return [];
    $con = obtenerConexion();
    $ph = implode(',', array_fill(0, count($idsCiclos), '?'));
    $types = str_repeat('i', count($idsCiclos));
    $sql = "SELECT cp.idCiclo, p.idProfesor, p.nombreProfesor
            FROM ciclo_profesor cp
            JOIN profesores p ON p.idProfesor = cp.idProfesor
            WHERE cp.idCiclo IN ($ph)
            ORDER BY p.idProfesor ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, $types, ...$idsCiclos);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $porCiclo = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $porCiclo[$fila['idCiclo']][] = ['idProfesor' => (int)$fila['idProfesor'], 'nombreProfesor' => $fila['nombreProfesor']];
    }
    return $porCiclo;
}

function actualizarProfesoresDeCiclo($idCiclo, array $listaIdsProfesores) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "DELETE FROM ciclo_profesor WHERE idCiclo = ?");
        mysqli_stmt_bind_param($stmt, "i", $idCiclo);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('delete ciclo_profesor');
        if (!empty($listaIdsProfesores)) {
            $stmt2 = mysqli_prepare($con, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)");
            foreach ($listaIdsProfesores as $idProf) {
                $idProf = (int)$idProf;
                mysqli_stmt_bind_param($stmt2, "ii", $idCiclo, $idProf);
                if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('insert ciclo_profesor');
            }
        }
        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarNuevoCiclo($nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "INSERT INTO ciclos (nombreCiclo, abreviaturaCiclo, idNivel, precioCiclo) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssid", $nombreCiclo, $abreviaturaCiclo, $idNivel, $precioCiclo);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('insert ciclo');
        $idNuevoCiclo = mysqli_insert_id($con);
        if (!empty($listaIdsProfesores)) {
            $stmt2 = mysqli_prepare($con, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)");
            foreach ($listaIdsProfesores as $idProfesor) {
                mysqli_stmt_bind_param($stmt2, "ii", $idNuevoCiclo, (int)$idProfesor);
                if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('insert ciclo_profesor');
            }
        }
        // Cursos por defecto (1º, 2º) para que el ciclo tenga opciones de año
        // seleccionables desde el primer momento; el asistente académico
        // permite luego renombrarlos/ampliarlos.
        $stmt3 = mysqli_prepare($con, "INSERT INTO cursos_academicos (idCiclo, nombre, orden) VALUES (?, ?, ?)");
        foreach ([['1º', 1], ['2º', 2]] as [$nombreCurso, $orden]) {
            mysqli_stmt_bind_param($stmt3, "isi", $idNuevoCiclo, $nombreCurso, $orden);
            if (!mysqli_stmt_execute($stmt3)) throw new \RuntimeException('insert curso_academico');
        }
        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarCicloExistente($idCiclo, $nombreCiclo, $abreviaturaCiclo, $idNivel, $listaIdsProfesores, $precioCiclo) {
    $con = obtenerConexion();
    mysqli_begin_transaction($con);
    try {
        $stmt = mysqli_prepare($con, "UPDATE ciclos SET nombreCiclo=?, abreviaturaCiclo=?, idNivel=?, precioCiclo=? WHERE idCiclo=?");
        mysqli_stmt_bind_param($stmt, "ssidi", $nombreCiclo, $abreviaturaCiclo, $idNivel, $precioCiclo, $idCiclo);
        if (!mysqli_stmt_execute($stmt)) throw new \RuntimeException('update ciclo');
        $stmt2 = mysqli_prepare($con, "DELETE FROM ciclo_profesor WHERE idCiclo = ?");
        mysqli_stmt_bind_param($stmt2, "i", $idCiclo);
        if (!mysqli_stmt_execute($stmt2)) throw new \RuntimeException('delete ciclo_profesor');
        if (!empty($listaIdsProfesores)) {
            $stmt3 = mysqli_prepare($con, "INSERT INTO ciclo_profesor (idCiclo, idProfesor) VALUES (?, ?)");
            foreach ($listaIdsProfesores as $idProfesor) {
                mysqli_stmt_bind_param($stmt3, "ii", $idCiclo, (int)$idProfesor);
                if (!mysqli_stmt_execute($stmt3)) throw new \RuntimeException('insert ciclo_profesor');
            }
        }
        mysqli_commit($con);
        return true;
    } catch (\Throwable $e) {
        mysqli_rollback($con);
        return false;
    }
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function checkCicloExistente($nombreCiclo, $abreviaturaCiclo, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idCiclo FROM ciclos WHERE (nombreCiclo = ? OR abreviaturaCiclo = ?) AND idCiclo != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $nombreCiclo, $abreviaturaCiclo, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}

function archivarCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "UPDATE ciclos SET activo = 0, fechaArchivado = NOW() WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    return mysqli_stmt_execute($stmt);
}

function restaurarCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "UPDATE ciclos SET activo = 1, fechaArchivado = NULL WHERE idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    return mysqli_stmt_execute($stmt);
}

function contarEstudiantesEnCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT COUNT(*) AS total FROM estudiantes WHERE idCiclo = ? AND eliminado = 0";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    return (int)($row['total'] ?? 0);
}
