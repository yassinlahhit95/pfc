<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Segundo factor en el login (TOTP o código de respaldo).
// Hasta superarlo no existe $_SESSION['idAdmin'] → sin acceso al sistema.
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../include/Totp.php';
require_once __DIR__ . '/../../modelos/directores.php';
@include_once __DIR__ . '/../../include/Logger.php';
if (!class_exists('Logger')) {
    class Logger { public static function security($e,$d=[]){} public static function activity($a,$u=null,$d=[]){} }
}

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN Y VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if (empty($_SESSION['mfa_pending']['id'])) {
    header('Location: ../../vistas/login.php');
    exit;
}
$pending = $_SESSION['mfa_pending'];

// El estado intermedio caduca a los 5 minutos
if (time() - ($pending['ts'] ?? 0) > 300) {
    unset($_SESSION['mfa_pending'], $_SESSION['mfa_attempts']);
    $_SESSION['errores'] = 'La verificación ha caducado. Inicia sesión de nuevo.';
    header('Location: ../../vistas/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../vistas/auth/mfa_verificar.php');
    exit;
}
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = 'Solicitud inválida.';
    header('Location: ../../vistas/auth/mfa_verificar.php');
    exit;
}

$_SESSION['mfa_attempts'] = ($_SESSION['mfa_attempts'] ?? 0) + 1;
if ($_SESSION['mfa_attempts'] > 5) {
    Logger::security('MFA_TOO_MANY_ATTEMPTS', ['id' => $pending['id']]);
    unset($_SESSION['mfa_pending'], $_SESSION['mfa_attempts']);
    $_SESSION['errores'] = 'Demasiados intentos. Inicia sesión de nuevo.';
    header('Location: ../../vistas/login.php');
    exit;
}

$code = trim($_POST['code'] ?? '');
$mfa  = obtenerMfaDirector((int)$pending['id']);
if (!$mfa || empty($mfa['mfa_secret'])) {
    unset($_SESSION['mfa_pending']);
    header('Location: ../../vistas/login.php');
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// VERIFICACIÓN DEL CÓDIGO
// ══════════════════════════════════════════════════════════════════════
$ok = false; $usedBackup = false;

// 1) Código TOTP de 6 dígitos
if (preg_match('/^\d{6}$/', $code)) {
    $ok = Totp::verify($mfa['mfa_secret'], $code);
}
// 2) Código de respaldo de un solo uso
if (!$ok) {
    $norm  = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $code));
    $codes = json_decode($mfa['mfa_backup_codes'] ?? '[]', true) ?: [];
    foreach ($codes as $i => $hash) {
        if (is_string($hash) && $norm !== '' && password_verify($norm, $hash)) {
            $ok = true; $usedBackup = true;
            unset($codes[$i]);
            actualizarBackupCodesDirector((int)$pending['id'], json_encode(array_values($codes)));
            break;
        }
    }
}

if (!$ok) {
    Logger::security('MFA_FAILED', ['id' => $pending['id']]);
    $_SESSION['errores'] = 'Código incorrecto.';
    header('Location: ../../vistas/auth/mfa_verificar.php');
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// CONCEDER SESIÓN
// ══════════════════════════════════════════════════════════════════════
Security::regenerateSession();
$_SESSION['idAdmin'] = (int)$pending['id'];
$_SESSION['must_change_password'] = !empty($pending['must_change']);
$_SESSION['_pwd_at'] = time();
$_SESSION['_pwd_check'] = time();
unset($_SESSION['mfa_pending'], $_SESSION['mfa_attempts']);
Logger::activity('LOGIN_SUCCESS', $_SESSION['idAdmin'], ['role' => 'admin', 'mfa' => $usedBackup ? 'backup' : 'totp']);
if ($usedBackup) {
    $_SESSION['exito'] = 'Has accedido con un código de respaldo. Genera nuevos si te quedan pocos.';
}
header('Location: ../../vistas/admin/inicio/dashboard.php');
exit;
