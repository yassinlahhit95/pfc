<?php
// Seguridad: Inicializar sesión y cargar clases de seguridad
session_start();
require_once __DIR__ . "/../include/Security.php";
require_once __DIR__ . "/../include/Logger.php";
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";

if (!isset($_POST["enviar"])) {
    header("Location: ../vistas/login.php");
    exit;
}

// Validar CSRF token
if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "Solicitud inválida. Por favor, intenta de nuevo.";
    Logger::security('CSRF_TOKEN_VALIDATION_FAILED');
    header("Location: ../vistas/login.php");
    exit;
}

$email = strtolower(trim($_POST["usuario"]));
$pass  = trim($_POST["contrasena"]);

// Validar entrada básica
if (empty($email) || empty($pass)) {
    $_SESSION['errores'] = empty($email) && empty($pass)
        ? "El correo y la contraseña son obligatorios."
        : (empty($email) ? "El correo electrónico es obligatorio." : "La contraseña es obligatoria.");
    $_SESSION['datos_login'] = $_POST;
    Logger::warning('LOGIN_MISSING_CREDENTIALS', ['email' => $email]);
    header("Location: ../vistas/login.php");
    exit;
}

// Validar email formato
if (!Security::validateEmail($email)) {
    $_SESSION['errores'] = "El formato del correo no es válido.";
    Logger::warning('LOGIN_INVALID_EMAIL', ['email' => $email]);
    header("Location: ../vistas/login.php");
    exit;
}

// Rate limiting - Prevenir ataques de fuerza bruta
$rateLimit = Security::checkRateLimit($email);
if (!$rateLimit['allowed']) {
    $_SESSION['errores'] = $rateLimit['message'];
    Logger::security('RATE_LIMIT_EXCEEDED', ['email' => $email, 'remaining_time' => $rateLimit['remaining_time']]);
    header("Location: ../vistas/login.php");
    exit;
}

// Limpiar sesiones previas
unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

// Intentar login como Admin
$admin = validarLoginDirector($email, $pass);
if ($admin) {
    Security::clearFailedLogins($email);
    $_SESSION['idAdmin'] = $admin['idDirector'];
    Logger::activity('LOGIN_SUCCESS', $admin['idDirector'], ['role' => 'admin', 'email' => $email]);
    header("Location: ../vistas/admin/inicio/dashboard.php");
    exit;
}

// Intentar login como Profesor
$profe = validarLoginProfesor($email, $pass);
if ($profe) {
    Security::clearFailedLogins($email);
    $_SESSION['idProfesor'] = $profe['idProfesor'];
    Logger::activity('LOGIN_SUCCESS', $profe['idProfesor'], ['role' => 'profesor', 'email' => $email]);
    header("Location: ../vistas/profesores/inicio/dashboard.php");
    exit;
}

// Intentar login como Estudiante
$estu = validarLoginEstudiante($email, $pass);
if ($estu) {
    Security::clearFailedLogins($email);
    $_SESSION['idEstudiante'] = $estu['idEstudiante'];
    Logger::activity('LOGIN_SUCCESS', $estu['idEstudiante'], ['role' => 'estudiante', 'email' => $email]);
    header("Location: ../vistas/estudiantes/inicio/dashboard.php");
    exit;
}

// Login fallido - Registrar intento
$failureResult = Security::recordFailedLogin($email);
$_SESSION['errores'] = "El email o la contraseña no son correctos.";
$_SESSION['datos_login'] = ['usuario' => $email]; // No guardar contraseña

Logger::security('LOGIN_FAILED', [
    'email' => $email,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'attempt_blocked' => $failureResult['blocked']
]);

header("Location: ../vistas/login.php");
exit;
?>
