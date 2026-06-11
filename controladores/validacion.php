<?php
session_start();
require_once __DIR__ . "/../include/Security.php";
require_once __DIR__ . "/../include/BotGuard.php";
// Logger is optional — login must work even if Logger.php is absent on server
@include_once __DIR__ . "/../include/Logger.php";
if (!class_exists('Logger')) {
    class Logger {
        public static function security($e, $d = []) {}
        public static function warning($m, $d = []) {}
        public static function activity($a, $u = null, $d = []) {}
    }
}
require_once __DIR__ . "/../modelos/directores.php";
require_once __DIR__ . "/../modelos/profesores.php";
require_once __DIR__ . "/../modelos/estudiantes.php";
require_once __DIR__ . "/../modelos/conectar.php";

if (!isset($_POST["enviar"])) {
    header("Location: ../vistas/login.php");
    exit;
}

// Rechazar bots (honeypot + tiempo mínimo)
if (!BotGuard::validate()) {
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

// Rate limiting por IP (DB) — no bypasseable sin sesión
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$con = obtenerConexion();
$rowIp = dbFetchOne("SELECT intentos, bloqueado_hasta FROM login_intentos WHERE ip = ?", "s", $ip);
if ($rowIp) {
    if ($rowIp['bloqueado_hasta'] && strtotime($rowIp['bloqueado_hasta']) > time()) {
        $espera = ceil((strtotime($rowIp['bloqueado_hasta']) - time()) / 60);
        $_SESSION['errores'] = "Demasiados intentos. Inténtalo de nuevo en $espera minutos.";
        Logger::security('IP_RATE_LIMIT_EXCEEDED', ['ip' => $ip]);
        header("Location: ../vistas/login.php");
        exit;
    }
}

// Rate limiting por sesión (fallback para usuarios legítimos con intentos fallidos)
$rateLimit = Security::checkRateLimit($email);
if (!$rateLimit['allowed']) {
    $_SESSION['errores'] = $rateLimit['message'];
    Logger::security('RATE_LIMIT_EXCEEDED', ['email' => $email, 'remaining_time' => $rateLimit['remaining_time']]);
    header("Location: ../vistas/login.php");
    exit;
}

// Helper: limpiar contador IP tras login exitoso
$clearIpAttempts = function() use ($con, $ip) {
    $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = 0, bloqueado_hasta = NULL WHERE ip = ?");
    mysqli_stmt_bind_param($upd, "s", $ip);
    mysqli_stmt_execute($upd);
};

// Limpiar sesiones previas
unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante']);

// Intentar login como Admin
$admin = validarLoginDirector($email, $pass);
if ($admin) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    $_SESSION['idAdmin'] = $admin['idDirector'];
    Logger::activity('LOGIN_SUCCESS', $admin['idDirector'], ['role' => 'admin', 'email' => $email]);
    header("Location: ../vistas/admin/inicio/dashboard.php");
    exit;
}

// Intentar login como Profesor
$profe = validarLoginProfesor($email, $pass);
if ($profe) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    $_SESSION['idProfesor'] = $profe['idProfesor'];
    Logger::activity('LOGIN_SUCCESS', $profe['idProfesor'], ['role' => 'profesor', 'email' => $email]);
    header("Location: ../vistas/profesores/inicio/dashboard.php");
    exit;
}

// Intentar login como Estudiante
$estu = validarLoginEstudiante($email, $pass);
if ($estu) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    $_SESSION['idEstudiante'] = $estu['idEstudiante'];
    Logger::activity('LOGIN_SUCCESS', $estu['idEstudiante'], ['role' => 'estudiante', 'email' => $email]);
    header("Location: ../vistas/estudiantes/inicio/dashboard.php");
    exit;
}

// Login fallido - Registrar intento (sesión + IP en DB)
$failureResult = Security::recordFailedLogin($email);
$_SESSION['errores'] = "El email o la contraseña no son correctos.";
$_SESSION['datos_login'] = ['usuario' => $email];

// Actualizar contador IP en DB
if ($rowIp) {
    $nuevosIntentos = $rowIp['intentos'] + 1;
    if ($nuevosIntentos >= 10) {
        $hasta = date('Y-m-d H:i:s', time() + 600);
        $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = 0, bloqueado_hasta = ?, ultimo_intento = NOW() WHERE ip = ?");
        mysqli_stmt_bind_param($upd, "ss", $hasta, $ip);
    } else {
        $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = intentos + 1, ultimo_intento = NOW() WHERE ip = ?");
        mysqli_stmt_bind_param($upd, "s", $ip);
    }
    mysqli_stmt_execute($upd);
} else {
    $ins = mysqli_prepare($con, "INSERT INTO login_intentos (ip, intentos) VALUES (?, 1)");
    mysqli_stmt_bind_param($ins, "s", $ip);
    mysqli_stmt_execute($ins);
}

Logger::security('LOGIN_FAILED', [
    'email' => $email,
    'ip' => $ip,
    'attempt_blocked' => $failureResult['blocked']
]);

header("Location: ../vistas/login.php");
exit;
?>
