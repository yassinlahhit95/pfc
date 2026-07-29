<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . "/../include/Security.php";
require_once __DIR__ . "/../include/BotGuard.php";
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
Security::initSession();
require_once __DIR__ . "/../include/AccountLockout.php";
// Logger es opcional; el login debe funcionar aunque Logger.php no exista en el servidor
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
require_once __DIR__ . "/../modelos/tutores.php";
require_once __DIR__ . "/../modelos/secretarias.php";
require_once __DIR__ . "/../modelos/conectar.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN PREVIA
// ══════════════════════════════════════════════════════════════════════
if (!isset($_POST["enviar"])) {
    header("Location: ../vistas/login.php");
    exit;
}

if (!BotGuard::validate()) {
    header("Location: ../vistas/login.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['errores'] = "La sesión ha expirado o la solicitud no es válida. Por favor, inténtelo de nuevo.";
    Logger::security('CSRF_TOKEN_VALIDATION_FAILED');
    header("Location: ../vistas/login.php");
    exit;
}

$email = strtolower(trim($_POST["usuario"]));
$pass  = trim($_POST["contrasena"]);

if (empty($email) || empty($pass)) {
    $_SESSION['errores'] = empty($email) && empty($pass)
        ? "El correo electrónico y la contraseña son campos obligatorios."
        : (empty($email) ? "El correo electrónico es un campo obligatorio." : "La contraseña es un campo obligatorio.");
    $_SESSION['datos_login'] = ['usuario' => $_POST['usuario'] ?? ''];
    Logger::warning('LOGIN_MISSING_CREDENTIALS', ['email' => $email]);
    header("Location: ../vistas/login.php");
    exit;
}

if (!Security::validateEmail($email)) {
    $_SESSION['errores'] = "El formato del correo electrónico introducido no es válido.";
    Logger::warning('LOGIN_INVALID_EMAIL', ['email' => $email]);
    header("Location: ../vistas/login.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// LÍMITES DE TASA Y BLOQUEOS
// ══════════════════════════════════════════════════════════════════════
$ip  = Security::clientIp();
$con = obtenerConexion();
$rowIp = dbFetchOne("SELECT intentos, bloqueado_hasta FROM login_intentos WHERE ip = ?", "s", $ip);
if ($rowIp) {
    if ($rowIp['bloqueado_hasta'] && strtotime($rowIp['bloqueado_hasta']) > time()) {
        $espera = ceil((strtotime($rowIp['bloqueado_hasta']) - time()) / 60);
        $_SESSION['errores'] = "Se ha superado el límite de intentos permitidos. Por favor, inténtelo de nuevo en $espera minutos.";
        Logger::security('IP_RATE_LIMIT_EXCEEDED', ['ip' => $ip]);
        header("Location: ../vistas/login.php");
        exit;
    }
}

$rateLimit = Security::checkRateLimit($email);
if (!$rateLimit['allowed']) {
    $_SESSION['errores'] = $rateLimit['message'];
    Logger::security('RATE_LIMIT_EXCEEDED', ['email' => $email, 'remaining_time' => $rateLimit['remaining_time']]);
    header("Location: ../vistas/login.php");
    exit;
}

// Bloqueo por cuenta independiente de la IP (resistente a fuerza bruta distribuida)
$lock = AccountLockout::status($con, $email);
if ($lock['locked']) {
    $_SESSION['errores'] = "Esta cuenta ha sido bloqueada temporalmente por motivos de seguridad. Por favor, inténtelo de nuevo en {$lock['minutes']} minutos.";
    Logger::security('ACCOUNT_LOCKED', ['email' => $email]);
    header("Location: ../vistas/login.php");
    exit;
}

// Limpia contadores de IP y cuenta tras login exitoso
$clearIpAttempts = function() use ($con, $ip, $email) {
    $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = 0, bloqueado_hasta = NULL WHERE ip = ?");
    if ($upd) {
        mysqli_stmt_bind_param($upd, "s", $ip);
        mysqli_stmt_execute($upd);
    }
    AccountLockout::clear($con, $email);
};

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN POR ROL
// ══════════════════════════════════════════════════════════════════════
unset($_SESSION['idAdmin'], $_SESSION['idProfesor'], $_SESSION['idEstudiante'], $_SESSION['idTutor'], $_SESSION['idSecretaria']);

$admin = validarLoginDirector($email, $pass);
if ($admin) {
    require_once __DIR__ . '/../include/FeatureGuard.php';
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
    $clearIpAttempts();
    Security::regenerateSession();

    // MFA activo: no conceder sesión todavía, exigir segundo factor (estado intermedio "mfa_pending")
    if (!empty($admin['mfa_enabled'])) {
        $_SESSION['mfa_pending'] = [
            'id'          => $admin['idDirector'],
            'role'        => 'idAdmin',
            'must_change' => !empty($admin['must_change_password']),
            'ts'          => time(),
        ];
        Logger::security('LOGIN_MFA_REQUIRED', ['id' => $admin['idDirector'], 'email' => $email]);
        header("Location: ../vistas/auth/mfa_verificar.php");
        exit;
    }

    $_SESSION['idAdmin'] = $admin['idDirector'];
    $_SESSION['must_change_password'] = !empty($admin['must_change_password']);
    $_SESSION['_pwd_at'] = !empty($admin['pwd_changed_at']) ? strtotime($admin['pwd_changed_at']) : 0;
    Logger::activity('LOGIN_SUCCESS', $admin['idDirector'], ['role' => 'admin', 'email' => $email]);
    header("Location: ../vistas/admin/inicio/dashboard.php");
    exit;
}

$profe = validarLoginProfesor($email, $pass);
if ($profe) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    Security::regenerateSession();

    if (!empty($profe['mfa_enabled'])) {
        $_SESSION['mfa_pending'] = [
            'id'          => $profe['idProfesor'],
            'role'        => 'idProfesor',
            'must_change' => !empty($profe['must_change_password']),
            'ts'          => time(),
        ];
        Logger::security('LOGIN_MFA_REQUIRED', ['id' => $profe['idProfesor'], 'email' => $email]);
        header("Location: ../vistas/auth/mfa_verificar.php");
        exit;
    }

    $_SESSION['idProfesor']   = $profe['idProfesor'];
    $_SESSION['esTutor']      = !empty($profe['esTutor']) ? 1 : 0;
    $_SESSION['idCicloTutor'] = (int)($profe['idCicloTutor'] ?? 0);
    $_SESSION['must_change_password'] = !empty($profe['must_change_password']);
    $_SESSION['_pwd_at'] = !empty($profe['pwd_changed_at']) ? strtotime($profe['pwd_changed_at']) : 0;
    Logger::activity('LOGIN_SUCCESS', $profe['idProfesor'], ['role' => 'profesor', 'email' => $email]);
    header("Location: ../vistas/profesores/inicio/dashboard.php");
    exit;
}

$tutor = validarLoginTutor($email, $pass);
if ($tutor) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    Security::regenerateSession();

    if (!empty($tutor['mfa_enabled'])) {
        $_SESSION['mfa_pending'] = [
            'id'          => $tutor['idTutor'],
            'role'        => 'idTutor',
            'must_change' => !empty($tutor['must_change_password']),
            'ts'          => time(),
        ];
        Logger::security('LOGIN_MFA_REQUIRED', ['id' => $tutor['idTutor'], 'email' => $email]);
        header("Location: ../vistas/auth/mfa_verificar.php");
        exit;
    }

    $_SESSION['idTutor'] = $tutor['idTutor'];
    $_SESSION['must_change_password'] = !empty($tutor['must_change_password']);
    $_SESSION['_pwd_at'] = !empty($tutor['pwd_changed_at']) ? strtotime($tutor['pwd_changed_at']) : 0;
    Logger::activity('LOGIN_SUCCESS', $tutor['idTutor'], ['role' => 'tutor', 'email' => $email]);
    header("Location: ../vistas/tutores/inicio/dashboard.php");
    exit;
}

$secretaria = validarLoginSecretaria($email, $pass);
if ($secretaria) {
    require_once __DIR__ . '/../include/FeatureGuard.php';
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
    $clearIpAttempts();
    Security::regenerateSession();

    if (!empty($secretaria['mfa_enabled'])) {
        $_SESSION['mfa_pending'] = [
            'id'          => $secretaria['idSecretaria'],
            'role'        => 'idSecretaria',
            'must_change' => !empty($secretaria['must_change_password']),
            'ts'          => time(),
        ];
        Logger::security('LOGIN_MFA_REQUIRED', ['id' => $secretaria['idSecretaria'], 'email' => $email]);
        header("Location: ../vistas/auth/mfa_verificar.php");
        exit;
    }

    $_SESSION['idSecretaria'] = $secretaria['idSecretaria'];
    $_SESSION['must_change_password'] = !empty($secretaria['must_change_password']);
    $_SESSION['_pwd_at'] = !empty($secretaria['pwd_changed_at']) ? strtotime($secretaria['pwd_changed_at']) : 0;
    Logger::activity('LOGIN_SUCCESS', $secretaria['idSecretaria'], ['role' => 'secretaria', 'email' => $email]);
    header("Location: ../vistas/secretaria/inicio/dashboard.php");
    exit;
}

$estu = validarLoginEstudiante($email, $pass);
if ($estu) {
    Security::clearFailedLogins($email);
    $clearIpAttempts();
    Security::regenerateSession();

    if (!empty($estu['mfa_enabled'])) {
        $_SESSION['mfa_pending'] = [
            'id'          => $estu['idEstudiante'],
            'role'        => 'idEstudiante',
            'must_change' => !empty($estu['must_change_password']),
            'ts'          => time(),
        ];
        Logger::security('LOGIN_MFA_REQUIRED', ['id' => $estu['idEstudiante'], 'email' => $email]);
        header("Location: ../vistas/auth/mfa_verificar.php");
        exit;
    }

    $_SESSION['idEstudiante'] = $estu['idEstudiante'];
    $_SESSION['must_change_password'] = !empty($estu['must_change_password']);
    $_SESSION['_pwd_at'] = !empty($estu['pwd_changed_at']) ? strtotime($estu['pwd_changed_at']) : 0;
    Logger::activity('LOGIN_SUCCESS', $estu['idEstudiante'], ['role' => 'estudiante', 'email' => $email]);
    header("Location: ../vistas/estudiantes/inicio/dashboard.php");
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// LOGIN FALLIDO — REGISTRAR INTENTO
// ══════════════════════════════════════════════════════════════════════
$failureResult = Security::recordFailedLogin($email);
AccountLockout::recordFailure($con, $email);
$_SESSION['errores'] = "El correo electrónico o la contraseña introducidos no son correctos.";
$_SESSION['datos_login'] = ['usuario' => $email];

if ($rowIp) {
    $nuevosIntentos = $rowIp['intentos'] + 1;
    if ($nuevosIntentos >= 10) {
        $hasta = date('Y-m-d H:i:s', time() + 600);
        $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = 0, bloqueado_hasta = ?, ultimo_intento = NOW() WHERE ip = ?");
        if ($upd) {
            mysqli_stmt_bind_param($upd, "ss", $hasta, $ip);
            mysqli_stmt_execute($upd);
        }
    } else {
        $upd = mysqli_prepare($con, "UPDATE login_intentos SET intentos = intentos + 1, ultimo_intento = NOW() WHERE ip = ?");
        if ($upd) {
            mysqli_stmt_bind_param($upd, "s", $ip);
            mysqli_stmt_execute($upd);
        }
    }
} else {
    $ins = mysqli_prepare($con, "INSERT INTO login_intentos (ip, intentos) VALUES (?, 1)");
    if ($ins) {
        mysqli_stmt_bind_param($ins, "s", $ip);
        mysqli_stmt_execute($ins);
    }
}

Logger::security('LOGIN_FAILED', [
    'email' => $email,
    'ip' => $ip,
    'attempt_blocked' => $failureResult['blocked']
]);

header("Location: ../vistas/login.php");
exit;
?>
