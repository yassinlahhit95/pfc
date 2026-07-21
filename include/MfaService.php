<?php
// Generaliza el 2FA (antes solo admin) a los 5 roles. Las tablas/columnas
// vienen siempre de este mapa fijo, nunca de $_GET/$_POST — el nombre de
// tabla dinámico en las consultas de abajo es seguro por eso.
class MfaService {

    private static $ROLES = [
        'idAdmin'      => ['tabla' => 'directores',  'idCol' => 'idDirector',   'home' => '/vistas/admin/inicio/dashboard.php',
                            'getFn' => 'obtenerDirectorPorId',  'emailField' => 'emailDirector',  'modelo' => 'directores.php'],
        'idProfesor'   => ['tabla' => 'profesores',  'idCol' => 'idProfesor',   'home' => '/vistas/profesores/inicio/dashboard.php',
                            'getFn' => 'obtenerProfesorPorId',  'emailField' => 'emailProfesor',  'modelo' => 'profesores.php'],
        'idSecretaria' => ['tabla' => 'secretarias', 'idCol' => 'idSecretaria', 'home' => '/vistas/secretaria/inicio/dashboard.php',
                            'getFn' => 'obtenerSecretariaPorId', 'emailField' => 'emailSecretaria', 'modelo' => 'secretarias.php'],
        'idEstudiante' => ['tabla' => 'estudiantes', 'idCol' => 'idEstudiante', 'home' => '/vistas/estudiantes/inicio/dashboard.php',
                            'getFn' => 'obtenerEstudiantePorId', 'emailField' => 'emailEstudiante', 'modelo' => 'estudiantes.php'],
        'idTutor'      => ['tabla' => 'tutores',     'idCol' => 'idTutor',      'home' => '/vistas/tutores/inicio/dashboard.php',
                            'getFn' => 'obtenerTutorPorId',      'emailField' => 'emailTutor',      'modelo' => 'tutores.php'],
    ];

    public static function config($sessionKey) {
        return self::$ROLES[$sessionKey] ?? null;
    }

    // Detecta qué rol hay activo en la sesión actual y devuelve su config + id, o null.
    public static function sesionActual() {
        foreach (self::$ROLES as $sessionKey => $cfg) {
            if (!empty($_SESSION[$sessionKey])) {
                return $cfg + ['sessionKey' => $sessionKey, 'id' => (int)$_SESSION[$sessionKey]];
            }
        }
        return null;
    }

    public static function obtenerMfa($tabla, $idCol, $id) {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con, "SELECT mfa_enabled, mfa_secret, mfa_backup_codes FROM `$tabla` WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)) ?: null;
    }

    public static function activar($tabla, $idCol, $id, $secret, $backupCodesJson) {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET mfa_enabled = 1, mfa_secret = ?, mfa_backup_codes = ? WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $secret, $backupCodesJson, $id);
        return mysqli_stmt_execute($stmt);
    }

    public static function actualizarBackupCodes($tabla, $idCol, $id, $backupCodesJson) {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET mfa_backup_codes = ? WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "si", $backupCodesJson, $id);
        return mysqli_stmt_execute($stmt);
    }

    public static function desactivar($tabla, $idCol, $id) {
        $con  = obtenerConexion();
        $stmt = mysqli_prepare($con, "UPDATE `$tabla` SET mfa_enabled = 0, mfa_secret = NULL, mfa_backup_codes = NULL WHERE `$idCol` = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        return mysqli_stmt_execute($stmt);
    }
}
