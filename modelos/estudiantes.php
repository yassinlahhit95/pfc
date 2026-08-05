<?php
require_once __DIR__ . "/conectar.php";
require_once __DIR__ . "/../include/Crypto.php";

// ── Cifrado de datos personales (RGPD Art. 32) ──────────────────────────
// dniEstudiante usa cifrado determinista (mismo texto → mismo cifrado) para
// que UNIQUE KEY y las búsquedas exactas (checkEstudianteExistente) sigan
// funcionando. El resto usa cifrado aleatorio (más fuerte, sin necesidad de
// igualdad exacta).
function _descifrarFilaEstudiante(?array $fila): ?array {
    if (!$fila) return $fila;
    foreach (['dniEstudiante', 'telefonoEstudiante', 'fechaNacimientoEstudiante', 'direccionEstudiante', 'observacionesEstudiante'] as $c) {
        if (array_key_exists($c, $fila)) $fila[$c] = Crypto::decrypt($fila[$c]);
    }
    return $fila;
}

// Público (con "descifrar" en vez de "_") para que modelos/rgpd.php pueda reutilizarlo.
function descifrarFilaEstudiante(?array $fila): ?array {
    return _descifrarFilaEstudiante($fila);
}

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
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel, grupos.nombreGrupo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            LEFT JOIN grupos ON estudiantes.idGrupo = grupos.idGrupo
            WHERE estudiantes.deleted_at IS NULL
            ORDER BY estudiantes.idEstudiante ASC";
    $res = mysqli_query($con, $sql);
    $rows = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $rows[] = _descifrarFilaEstudiante($fila);
    }
    return $rows;
}

function listarEstudiantesDeProfesor($idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT DISTINCT e.*, c.nombreCiclo, c.abreviaturaCiclo, c.idNivel
            FROM estudiantes e
            JOIN ciclos c ON e.idCiclo = c.idCiclo
            WHERE (e.idCiclo IN (SELECT idCiclo FROM ciclo_profesor WHERE idProfesor = ?)
               OR e.idCiclo IN (SELECT m.idCiclo FROM modulos m JOIN modulo_profesor pm ON m.idModulo = pm.idModulo WHERE pm.idProfesor = ?))
              AND e.deleted_at IS NULL
            ORDER BY e.nombreEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idProfesor, $idProfesor);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = _descifrarFilaEstudiante($fila);
    }
    return $lista;
}

function listarEstudiantesPorCiclo($idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.idCiclo = ? AND estudiantes.deleted_at IS NULL
            ORDER BY estudiantes.idEstudiante ASC";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idCiclo);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $lista = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $lista[] = _descifrarFilaEstudiante($fila);
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
    return _descifrarFilaEstudiante(mysqli_fetch_assoc($resultado));
}

function obtenerTokensEstudiantes() {
    $con = obtenerConexion();
    $sql = "SELECT fcm_token FROM estudiantes WHERE fcm_token IS NOT NULL AND fcm_token != '' AND deleted_at IS NULL";
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

function insertarEstudiante($nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null, $idGrupo = null) {
    $con = obtenerConexion();
    require_once __DIR__ . '/../include/credenciales.php';
    [$pass] = generarCredencialesTemporales($email, $nombre, 'Estudiante');
    $telefonoC        = Crypto::encrypt($telefono);
    $fechaNacimientoC = Crypto::encrypt($fechaNacimiento);
    $dniC             = Crypto::encryptDeterministic($dni);
    $direccionC       = Crypto::encrypt($direccion);
    $observacionesC   = Crypto::encrypt($observaciones);
    $sql = "INSERT INTO estudiantes (nombreEstudiante, emailEstudiante, password, telefonoEstudiante, fechaNacimientoEstudiante, dniEstudiante, fechaAltaEstudiante, direccionEstudiante, ciudadEstudiante, codigoPostalEstudiante, observacionesEstudiante, idCiclo, curso, anioEstudio, idGrupo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssisssi", $nombre, $email, $pass, $telefonoC, $fechaNacimientoC, $dniC, $fechaAlta, $direccionC, $ciudad, $codigoPostal, $observacionesC, $idCiclo, $curso, $anioEstudio, $idGrupo);
    return mysqli_stmt_execute($stmt);
}

// ══════════════════════════════════════════════════════════════════════
// ACTUALIZACIONES
// ══════════════════════════════════════════════════════════════════════

function actualizarEstudiante($id, $nombre, $email, $telefono, $fechaNacimiento, $dni, $fechaAlta, $direccion, $ciudad, $codigoPostal, $observaciones, $idCiclo, $curso = 'Grado Medio', $anioEstudio = null, $idGrupo = null) {
    $con = obtenerConexion();
    $telefonoC        = Crypto::encrypt($telefono);
    $fechaNacimientoC = Crypto::encrypt($fechaNacimiento);
    $dniC             = Crypto::encryptDeterministic($dni);
    $direccionC       = Crypto::encrypt($direccion);
    $observacionesC   = Crypto::encrypt($observaciones);
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, fechaNacimientoEstudiante=?, dniEstudiante=?, fechaAltaEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=?, observacionesEstudiante=?, idCiclo=?, curso=?, anioEstudio=?, idGrupo=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssssissii", $nombre, $email, $telefonoC, $fechaNacimientoC, $dniC, $fechaAlta, $direccionC, $ciudad, $codigoPostal, $observacionesC, $idCiclo, $curso, $anioEstudio, $idGrupo, $id);
    return mysqli_stmt_execute($stmt);
}

function actualizarPerfilEstudiante($idEstudiante, $nombre, $email, $telefono, $dni = null, $fechaNacimiento = null, $direccion = null, $ciudad = null, $codigoPostal = null) {
    $con = obtenerConexion();
    $telefonoC        = Crypto::encrypt($telefono);
    $dniC             = Crypto::encryptDeterministic($dni);
    $fechaNacimientoC = Crypto::encrypt($fechaNacimiento);
    $direccionC       = Crypto::encrypt($direccion);
    $sql = "UPDATE estudiantes SET nombreEstudiante=?, emailEstudiante=?, telefonoEstudiante=?, dniEstudiante=?, fechaNacimientoEstudiante=?, direccionEstudiante=?, ciudadEstudiante=?, codigoPostalEstudiante=? WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssssssssi", $nombre, $email, $telefonoC, $dniC, $fechaNacimientoC, $direccionC, $ciudad, $codigoPostal, $idEstudiante);
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
    $sql = "UPDATE estudiantes SET deleted_at=NOW() WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function restaurarEstudiante($idEstudiante) {
    $con = obtenerConexion();
    $sql = "UPDATE estudiantes SET deleted_at=NULL WHERE idEstudiante=?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "i", $idEstudiante);
    return mysqli_stmt_execute($stmt);
}

function listarEstudiantesEliminados() {
    $con = obtenerConexion();
    $sql = "SELECT estudiantes.*, ciclos.nombreCiclo, ciclos.abreviaturaCiclo, ciclos.idNivel
            FROM estudiantes
            JOIN ciclos ON estudiantes.idCiclo = ciclos.idCiclo
            WHERE estudiantes.deleted_at IS NOT NULL
            ORDER BY estudiantes.deleted_at DESC";
    $res = mysqli_query($con, $sql);
    $rows = [];
    while ($fila = mysqli_fetch_assoc($res)) {
        $rows[] = _descifrarFilaEstudiante($fila);
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
    $sql = "SELECT * FROM estudiantes WHERE emailEstudiante = ? AND deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $datos = mysqli_fetch_assoc($resultado);
    if ($datos && password_verify($password, $datos['password'])) {
        if (class_exists('Security')) Security::rehashOnLogin($con, 'estudiantes', 'idEstudiante', $datos['idEstudiante'], $password, $datos['password']);
        return _descifrarFilaEstudiante($datos);
    }
    return null;
}

// ══════════════════════════════════════════════════════════════════════
// UTILIDADES
// ══════════════════════════════════════════════════════════════════════

function estudiantePerteneceACiclo($idEstudiante, $idCiclo) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes WHERE idEstudiante = ? AND idCiclo = ? AND deleted_at IS NULL";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $idEstudiante, $idCiclo);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($res) > 0;
}

function estudiantePerteneceAProfesor($idEstudiante, $idProfesor) {
    $con = obtenerConexion();
    $sql = "SELECT 1 FROM estudiantes e
            WHERE e.idEstudiante = ? AND e.deleted_at IS NULL
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
    $dniC = Crypto::encryptDeterministic($dni);
    $sql = "SELECT idEstudiante FROM estudiantes WHERE (dniEstudiante = ? OR emailEstudiante = ?) AND idEstudiante != ?";
    $stmt = mysqli_prepare($con, $sql);
    mysqli_stmt_bind_param($stmt, "ssi", $dniC, $email, $idExcluir);
    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($resultado) > 0;
}
