<?php
declare(strict_types=1);
require_once __DIR__ . "/../modelos/conectar.php";
require_once __DIR__ . "/../include/Security.php";
Security::initSession();

require_once __DIR__ . "/../include/FeatureGuard.php";
require_once __DIR__ . "/../modelos/conectar.php";
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";
require_once __DIR__ . "/../modelos/tutores.php";
require_once __DIR__ . "/../modelos/secretarias.php";

// Logger es opcional; el login debe funcionar aunque Logger.php no exista en el servidor
@include_once __DIR__ . "/../include/Logger.php";
if (!class_exists('Logger')) {
    class Logger {
        public static function security($e, $d = []) {}
        public static function warning($m, $d = []) {}
        public static function activity($a, $u = null, $d = []) {}
    }
}

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN PREVIA
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST["id_token"])) {
    header("Location: ../vistas/login.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "La sesión ha expirado o la solicitud no es válida. Por favor, inténtelo de nuevo.";
    Logger::security('CSRF_TOKEN_VALIDATION_FAILED');
    header("Location: ../vistas/login.php");
    exit;
}

$idToken = trim($_POST["id_token"]);
$payload = Security::verifyGoogleIdToken($idToken);

if (!$payload) {
    $_SESSION['errores'] = "Autenticación de Google fallida. El token no es válido o ha expirado.";
    Logger::warning('GOOGLE_AUTH_INVALID_TOKEN');
    header("Location: ../vistas/login.php");
    exit;
}

$email = strtolower(trim($payload['email']));
$ip    = Security::clientIp();
$con   = obtenerConexion();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN POR ROL (SCAN ALL USER TABLES)
// ══════════════════════════════════════════════════════════════════════

// 1. Director / Admin
$st = mysqli_prepare($con, "SELECT * FROM directores WHERE emailDirector = ? LIMIT 1");
mysqli_stmt_bind_param($st, 's', $email);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if ($row) {
    if (function_exists('descifrarFilaDirector')) {
        $row = descifrarFilaDirector($row);
    }
    if (FeatureGuard::check('feature_geoblock_admin')) {
        $country = Security::getCountryFromIP($ip);
        if ($country !== 'ES') {
            $_SESSION['errores'] = "Acceso denegado: Por motivos de seguridad, el panel de administración solo es accesible desde España.";
            Logger::security('GEOBLOCK_ADMIN_BLOCKED', ['email' => $email, 'ip' => $ip, 'country' => $country, 'role' => 'admin']);
            header("Location: ../vistas/login.php");
            exit;
        }
    }
    Security::clearFailedLogins($email);
    Security::regenerateSession();

    $_SESSION['idAdmin'] = $row['idDirector'];
    $_SESSION['must_change_password'] = false;
    $_SESSION['_pwd_at'] = time();
    Logger::activity('LOGIN_SUCCESS', $row['idDirector'], ['role' => 'admin', 'email' => $email, 'method' => 'google']);
    header("Location: ../vistas/admin/inicio/dashboard.php");
    exit;
}

// 2. Profesor
$st = mysqli_prepare($con, "SELECT * FROM profesores WHERE emailProfesor = ? LIMIT 1");
mysqli_stmt_bind_param($st, 's', $email);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if ($row) {
    Security::clearFailedLogins($email);
    Security::regenerateSession();

    $_SESSION['idProfesor']   = $row['idProfesor'];
    $_SESSION['esTutor']      = !empty($row['esTutor']) ? 1 : 0;
    $_SESSION['idCicloTutor'] = (int)($row['idCicloTutor'] ?? 0);
    $_SESSION['must_change_password'] = false;
    $_SESSION['_pwd_at'] = time();
    Logger::activity('LOGIN_SUCCESS', $row['idProfesor'], ['role' => 'profesor', 'email' => $email, 'method' => 'google']);
    header("Location: ../vistas/profesores/inicio/dashboard.php");
    exit;
}

// 3. Tutor
$st = mysqli_prepare($con, "SELECT * FROM tutores WHERE emailTutor = ? LIMIT 1");
mysqli_stmt_bind_param($st, 's', $email);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if ($row) {
    Security::clearFailedLogins($email);
    Security::regenerateSession();

    $_SESSION['idTutor'] = $row['idTutor'];
    $_SESSION['must_change_password'] = false;
    $_SESSION['_pwd_at'] = time();
    Logger::activity('LOGIN_SUCCESS', $row['idTutor'], ['role' => 'tutor', 'email' => $email, 'method' => 'google']);
    header("Location: ../vistas/tutores/inicio/dashboard.php");
    exit;
}

// 4. Secretaria
$st = mysqli_prepare($con, "SELECT * FROM secretarias WHERE emailSecretaria = ? LIMIT 1");
mysqli_stmt_bind_param($st, 's', $email);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if ($row) {
    if (FeatureGuard::check('feature_geoblock_admin')) {
        $country = Security::getCountryFromIP($ip);
        if ($country !== 'ES') {
            $_SESSION['errores'] = "Acceso denegado: Por motivos de seguridad, el panel de administración solo es accesible desde España.";
            Logger::security('GEOBLOCK_ADMIN_BLOCKED', ['email' => $email, 'ip' => $ip, 'country' => $country, 'role' => 'secretaria']);
            header("Location: ../vistas/login.php");
            exit;
        }
    }
    Security::clearFailedLogins($email);
    Security::regenerateSession();

    $_SESSION['idSecretaria'] = $row['idSecretaria'];
    $_SESSION['must_change_password'] = false;
    $_SESSION['_pwd_at'] = time();
    Logger::activity('LOGIN_SUCCESS', $row['idSecretaria'], ['role' => 'secretaria', 'email' => $email, 'method' => 'google']);
    header("Location: ../vistas/secretaria/inicio/dashboard.php");
    exit;
}

// 5. Estudiante
$st = mysqli_prepare($con, "SELECT * FROM estudiantes WHERE emailEstudiante = ? AND eliminado = 0 LIMIT 1");
mysqli_stmt_bind_param($st, 's', $email);
mysqli_stmt_execute($st);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
if ($row) {
    Security::clearFailedLogins($email);
    Security::regenerateSession();

    $_SESSION['idEstudiante'] = $row['idEstudiante'];
    $_SESSION['must_change_password'] = false;
    $_SESSION['_pwd_at'] = time();
    Logger::activity('LOGIN_SUCCESS', $row['idEstudiante'], ['role' => 'estudiante', 'email' => $email, 'method' => 'google']);
    header("Location: ../vistas/estudiantes/inicio/dashboard.php");
    exit;
}

// Si llega aquí, el correo de Google no coincide con ningún usuario del sistema
$_SESSION['errores'] = "Acceso denegado: esta cuenta de Google ({$email}) no está registrada en el sistema.";
Logger::warning('GOOGLE_AUTH_USER_NOT_FOUND', ['email' => $email]);
header("Location: ../vistas/login.php");
exit;
