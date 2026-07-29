<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . '/../../include/Security.php';
require_once __DIR__ . '/../../include/Totp.php';
require_once __DIR__ . '/../../include/MfaService.php';
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
// Activa MFA para cualquier rol: verifica el primer código TOTP, guarda el
// secreto y genera códigos de respaldo (mostrados una sola vez).
Security::initSession();

// ══════════════════════════════════════════════════════════════════════
// AUTENTICACIÓN Y VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
$actor = MfaService::sesionActual();
if (!$actor) {
    header('Location: ../../vistas/login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../vistas/auth/mfa_configurar.php');
    exit;
}
if (!Security::validateCSRFToken($_POST['csrf_token'] ?? '')) {
    $_SESSION['errores'] = 'Solicitud inválida.';
    header('Location: ../../vistas/auth/mfa_configurar.php');
    exit;
}

$secret = $_SESSION['mfa_setup_secret'] ?? '';
$code   = trim($_POST['code'] ?? '');

if ($secret === '' || !Totp::verify($secret, $code)) {
    $_SESSION['errores'] = 'El código no es válido. Asegúrate de que la hora del teléfono es correcta.';
    header('Location: ../../vistas/auth/mfa_configurar.php');
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
// 8 códigos de respaldo de un solo uso, almacenados hasheados
$plain = []; $hashes = [];
for ($i = 0; $i < 8; $i++) {
    $raw = strtoupper(bin2hex(random_bytes(4)));
    $plain[]  = substr($raw, 0, 4) . '-' . substr($raw, 4, 4);
    $hashes[] = password_hash($raw, PASSWORD_DEFAULT);
}

if (!MfaService::activar($actor['tabla'], $actor['idCol'], $actor['id'], $secret, json_encode($hashes))) {
    $_SESSION['errores'] = 'No se pudo activar MFA. Inténtalo de nuevo.';
    header('Location: ../../vistas/auth/mfa_configurar.php');
    exit;
}

unset($_SESSION['mfa_setup_secret'], $_SESSION['mfa_setup_required']);
$_SESSION['mfa_backup_plain'] = $plain;
Security::regenerateSession();
header('Location: ../../vistas/auth/mfa_backup.php');
exit;
