<?php
// ══════════════════════════════════════════════════════════════════════
// DEPENDENCIAS
// ══════════════════════════════════════════════════════════════════════
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../modelos/password_reset.php";

// ══════════════════════════════════════════════════════════════════════
// VALIDACIÓN
// ══════════════════════════════════════════════════════════════════════
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/login.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['reset_error'] = "Token de seguridad inválido. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../vistas/login.php");
    exit;
}

$token  = trim($_POST['token'] ?? '');
$pass1  = $_POST['password'] ?? '';
$pass2  = $_POST['password2'] ?? '';

$resetRow = $token ? validarTokenReset($token) : null;
if (!$resetRow) {
    $_SESSION['reset_error'] = "El enlace ha caducado o ya fue usado. Solicita uno nuevo.";
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

if ($pass1 !== $pass2) {
    $_SESSION['reset_error'] = "Las contraseñas no coinciden.";
    header("Location: ../../vistas/auth/nueva_contrasena.php?token=" . urlencode($token));
    exit;
}

$validacion = Security::validatePassword($pass1);
if (!$validacion['valid']) {
    $_SESSION['reset_error'] = $validacion['error'];
    header("Location: ../../vistas/auth/nueva_contrasena.php?token=" . urlencode($token));
    exit;
}

// ══════════════════════════════════════════════════════════════════════
// PROCESAMIENTO
// ══════════════════════════════════════════════════════════════════════
if (!cambiarPasswordPorEmail($resetRow['email'], $resetRow['tipo_usuario'], $pass1)) {
    $_SESSION['reset_error'] = "No se pudo actualizar la contraseña. Inténtalo de nuevo.";
    header("Location: ../../vistas/auth/nueva_contrasena.php?token=" . urlencode($token));
    exit;
}

marcarTokenUsado($token);

$_SESSION['reset_ok'] = "Contraseña actualizada. Ya puedes iniciar sesión.";
header("Location: ../../vistas/login.php");
exit;
