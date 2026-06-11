<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../modelos/password_reset.php";
require_once __DIR__ . "/../../controladores/comunes/email_helper.php";
require_once __DIR__ . "/../../config/Config.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

if (!Security::validateCSRFToken()) {
    $_SESSION['reset_error'] = "Token de seguridad inválido. Recarga la página e inténtalo de nuevo.";
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

// Rate-limit: max 5 reset requests per IP per 10 minutes
$ip  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$con = obtenerConexion();

$row = dbFetchOne(
    "SELECT intentos, bloqueado_hasta FROM login_intentos WHERE ip = ?",
    "s", $ip
);
if ($row) {
    if ($row['bloqueado_hasta'] && strtotime($row['bloqueado_hasta']) > time()) {
        $espera = ceil((strtotime($row['bloqueado_hasta']) - time()) / 60);
        $_SESSION['reset_error'] = "Demasiadas solicitudes. Inténtalo de nuevo en $espera minutos.";
        header("Location: ../../vistas/auth/solicitar_reset.php");
        exit;
    }
    if ($row['intentos'] >= 5) {
        // Block for 10 minutes
        $hasta = date('Y-m-d H:i:s', time() + 600);
        $upd   = mysqli_prepare($con, "UPDATE login_intentos SET bloqueado_hasta=?, intentos=0 WHERE ip=?");
        mysqli_stmt_bind_param($upd, "ss", $hasta, $ip);
        mysqli_stmt_execute($upd);
        $_SESSION['reset_error'] = "Demasiadas solicitudes. Inténtalo de nuevo en 10 minutos.";
        header("Location: ../../vistas/auth/solicitar_reset.php");
        exit;
    }
    $inc = mysqli_prepare($con, "UPDATE login_intentos SET intentos = intentos + 1, ultimo_intento = NOW() WHERE ip = ?");
    mysqli_stmt_bind_param($inc, "s", $ip);
    mysqli_stmt_execute($inc);
} else {
    $ins = mysqli_prepare($con, "INSERT INTO login_intentos (ip, intentos) VALUES (?, 1)");
    mysqli_stmt_bind_param($ins, "s", $ip);
    mysqli_stmt_execute($ins);
}

$email = trim($_POST['email'] ?? '');
if (!Security::validateEmail($email)) {
    $_SESSION['reset_error'] = "Introduce un email válido.";
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

// Always show success to avoid user enumeration
$usuario = buscarUsuarioPorEmail($email);

// Debug: show exact failure reason in development
if (!$usuario && $config->get('APP_ENV') === 'development') {
    error_log("PASSWORD RESET DEV: email '$email' no encontrado en ninguna tabla.");
    $_SESSION['reset_error'] = "[DEV] Email '$email' no existe en la base de datos local.";
    header("Location: ../../vistas/auth/solicitar_reset.php");
    exit;
}

if ($usuario) {
    $token = crearTokenReset($email, $usuario['tipo']);

    $proto  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $base   = $proto . '://' . $_SERVER['HTTP_HOST'];
    $dir    = dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])));
    if ($dir === '/' || $dir === '\\' || $dir === '.') $dir = '';
    $enlace = $base . $dir . '/vistas/auth/nueva_contrasena.php?token=' . urlencode($token);

    $html = "
    <div style='font-family:sans-serif;max-width:520px;margin:0 auto;'>
      <h2 style='color:#0ea5e9;'>Recuperación de contraseña</h2>
      <p>Hemos recibido una solicitud para restablecer la contraseña de tu cuenta.</p>
      <p style='margin:24px 0;'>
        <a href='$enlace' style='background:#0ea5e9;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;'>
          Restablecer contraseña
        </a>
      </p>
      <p style='color:#6b7280;font-size:.875rem;'>Este enlace caduca en 1 hora. Si no solicitaste este cambio, ignora este mensaje.</p>
    </div>";

    $sent = sendEmail($email, 'Restablecer contraseña — AulaPro', $html);
    if (!$sent) {
        error_log("PASSWORD RESET: falló el envío de email a $email");
        $config = Config::getInstance();
        if ($config->get('APP_ENV') === 'development') {
            $_SESSION['reset_error'] = "Error al enviar el email. Revisa los logs y la API key de Brevo.";
            header("Location: ../../vistas/auth/solicitar_reset.php");
            exit;
        }
    }
}

$_SESSION['reset_ok'] = "Si el email existe en el sistema, recibirás las instrucciones en breve.";
header("Location: ../../vistas/auth/solicitar_reset.php");
exit;
