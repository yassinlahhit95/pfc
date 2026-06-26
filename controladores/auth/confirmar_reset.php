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

// Read token to surface email/tipo for the password update — but do NOT mark used yet.
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
// PROCESAMIENTO — consumo atómico del token (evita race condition TOCTOU)
// ══════════════════════════════════════════════════════════════════════
// Un solo UPDATE atómico actúa como "claim": si affected_rows = 0 significa
// que otro proceso ya lo consumió entre el SELECT de arriba y este UPDATE.
$con  = obtenerConexion();
$hash = hash('sha256', $token);
$upd  = mysqli_prepare($con, "UPDATE password_resets SET usado=1 WHERE token=? AND usado=0 AND expires_at>NOW()");
mysqli_stmt_bind_param($upd, 's', $hash);
mysqli_stmt_execute($upd);
if (mysqli_stmt_affected_rows($con) === 0) {
    $_SESSION['reset_error'] = "El enlace ha caducado o ya fue usado. Solicita uno nuevo.";
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

if (!cambiarPasswordPorEmail($resetRow['email'], $resetRow['tipo_usuario'], $pass1)) {
    $_SESSION['reset_error'] = "No se pudo actualizar la contraseña. Inténtalo de nuevo.";
    header("Location: ../../vistas/login.php");
    exit;
}

$_SESSION['reset_ok'] = "Contraseña actualizada. Ya puedes iniciar sesión.";
header("Location: ../../vistas/login.php");
exit;
