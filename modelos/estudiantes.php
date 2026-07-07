<?php
require_once __DIR__ . "/conectar.php";

// ── Auto-migration: soft-delete columns ──────────────────────────────
(function() {
    $con = obtenerConexion();
    $cols = [];
    $r = mysqli_query($con, "SHOW COLUMNS FROM estudiantes");
    while ($f = mysqli_fetch_assoc($r)) $cols[] = $f['Field'];
    if (!in_array('eliminado', $cols)) {
        mysqli_query($con, "ALTER TABLE estudiantes ADD COLUMN eliminado TINYINT(1) NOT NULL DEFAULT 0");
    }
    if (!in_array('fecha_eliminacion', $cols)) {
        mysqli_query($con, "ALTER TABLE estudiantes ADD COLUMN fecha_eliminacion DATETIME NULL DEFAULT NULL");
    }
})();

// ══════════════════════════════════════════════════════════════════════
// CONSULTAS
// ══════════════════════════════════════════════════════════════════════

function listarEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE (estudiantes.eliminado = 0 OR estudiantes.eliminado IS NULL)
            ORDER BY estudiantes.idEstudiante ASC";
    $res = mysqli_query($con, $sql);
    $rows = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $rows[] = $fila;
    }
    return $rows;
}

function listarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT e.*, c.nombreCiclo, c.abreviaturaCiclo, c.idNivel
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?)
            ORDER BY e.nombreEstudiante ASC";
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

function listarEstudiantesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = ?
            ORDER BY estudiantes.idEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila;
    }
    return $lista;
}

function obtenerEstudiantePorId($idEstudiante) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_fetch_assoc($resultado);
}

function obtenerTokensEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != ''";
    $resultado = mysqli_query($con, $sql);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = $fila['fcm_token'];
    }
    return $lista;
}

function obtenerTokenFCMEstudiante($idEstudiante) {
    return obtenerTokenFCM('estudiantes', 'idEstudiante', $idEstudiante);
}

// ══════════════════════════════════════════════════════════════════════
// INSERCIONES
// ══════════════════════════════════════════════════════════════════════

function insertarEstudiante($nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null) {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Estudiante');
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, curso, anioEstudio) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssssiss", $nombre, $email, $pass, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso, $anioEstudio);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEstudiante($id, $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=?, curso=?, anioEstudio=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssissi", $nombre, $email, $tel, $fecha_nac, $dni, $fecha_alta, $dir, $ciudad, $cp, $obs, $idCiclo, $curso, $anioEstudio, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono, $dni = null, $fechaNacimiento = null, $direccion = null, $ciudad = null, $codigoPostal = null) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, dniEstudiante=?, fechaNacimientoEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssi", $nombre, $email, $telefono, $dni, $fechaNacimiento, $direccion, $ciudad, $codigoPostal, $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function actualizarPasswordEstudiante($idEstudiante, $nuevaPassword) {
    $con = obtenerConexion();
    $hash = Security::hashPassword($nuevaPassword);
    $sql = "UPDATE estudiantes SET password = ? WHERE idEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "si", $hash, $idEstudiante);
    $resultado = mysqli_stmt_execute($stmt);
    if ($resultado && class_exists('Security')) {
        Security::touchPasswordChanged($con, 'estudiantes', 'idEstudiante', $idEstudiante);
    }
    return $resultado;
}

function actualizarTokenFCMEstudiante($idEstudiante, $nuevoToken) {
    return actualizarTokenFCM('estudiantes', 'idEstudiante', $idEstudiante, $nuevoToken);
}

// ══════════════════════════════════════════════════════════════════════
// ELIMINACIONES
// ══════════════════════════════════════════════════════════════════════

function softDeleteEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET eliminado=1, fecha_eliminacion=NOW() WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function restaurarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET eliminado=0, fecha_eliminacion=NULL WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function listarEstudiantesEliminados() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.eliminado = 1
            ORDER BY estudiantes.fecha_eliminacion DESC";
    $res = mysqli_query($con, $sql);
    $rows = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $rows[] = $fila;
    }
    return $rows;
}

// Legacy alias kept for backward compat (hard delete, used only if needed)
function eliminarEstudiante($idEstudiante) {
    return softDeleteEstudiante($idEstudiante);
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN
// ══════════════════════════════════════════════════════════════════════

function validarLoginEstudiante($email, $password) {
    $con = obtenerConexion();
    $sql = "SELECT * FROM estudiantes WHERE emailEstudiante = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    if ($datos && password_verify($password, $datos['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'estudiantes', 'idEstudiante', $datos['idEstudiante'], $password, $datos['password']);
        return $datos;
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function estudiantePerteneceACiclo($idEstudiante, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes WHERE idEstudiante = ? AND idCiclo = ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function estudiantePerteneceAProfesor($idEstudiante, $idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes e
            WHERE e.idEstudiante = ?
            AND (e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
              OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "iii", $idEstudiante, $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function checkEstudianteExistente($dni, $email, $idExcluir = 0) {
    $con = obtenerConexion();
    $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dni, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}

function obtenerCursosEscolaresEstudiante($idEstudiante) {
    $con = obtenerConexion();
    try {
        $sql = "SELECT DISTINCT cursoEscolar FROM calificaciones_modulos WHERE idEstudiante = ? AND cursoEscolar IS NOT NULL ORDER BY cursoEscolar DESC";
        $stmt = mysqli_prepare($con, $sql);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $lista = [];
            while ($fila = mysqli_fetch_assoc($res)) {
                $lista[] = $fila['cursoEscolar'];
            }
            if (!empty($lista)) return $lista;
        }
    } catch (\Throwable $e) {}
    
    require_once __DIR__ . '/configuracion.php';
    $config = obtenerConfiguracion();
    return [$config['cursoEscolar'] ?? (date('Y') . '-' . (date('Y') + 1))];
}
