<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/BotGuard.php";
require_once __DIR__ . "/../../include/AssetMin.php";
if (isset($_SESSION['idAdmin']))      { header("Location: ../admin/inicio/dashboard.php");      exit; }
if (isset($_SESSION['idProfesor']))   { header("Location: ../profesores/inicio/dashboard.php");  exit; }
if (isset($_SESSION['idEstudiante'])) { header("Location: ../estudiantes/inicio/dashboard.php"); exit; }

$exito = $_SESSION['reset_ok']    ?? null;
$error = $_SESSION['reset_error'] ?? null;
unset($_SESSION['reset_ok'], $_SESSION['reset_error']);

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Recuperar contraseña — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../public/css/features/login.css') ?>">
</head>
<body>

<div class="login-page">

    <div class="panel-izquierdo">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="panel-contenido">
            <div class="panel-logo">
                <img src="../../public/imagenes/aulapro.png" alt="Logo">
            </div>
            <h1 class="panel-titulo">Recupera tu <span>acceso</span></h1>
            <p class="panel-desc">Introduce tu email y te enviaremos un enlace para restablecer tu contraseña.</p>
        </div>
    </div>

    <div class="panel-derecho">
        <div class="form-contenedor">

            <div class="form-cabecera">
                <h2>Olvidé mi contraseña</h2>
                <p>Te enviaremos las instrucciones por email</p>
            </div>

            <div class="error-alerta" style="background:#ecfdf5;border-color:#6ee7b7;color:#065f46;"><?= Security::escapeHtml($exito) ?></div>
            <div class="error-alerta"><?= Security::escapeHtml($error) ?></div>
            <form action="../../controladores/auth/solicitar_reset.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                <?= BotGuard::renderFields() ?>
                <div class="campo-grupo">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="ejemplo@correo.com" required autofocus>
                </div>
                <button type="submit" class="boton-acceso">Enviar instrucciones</button>
            </form>
            <a href="../login.php" class="enlace-volver" style="display:block;margin-top:16px;text-align:center;">Volver al inicio de sesión</a>
        </div>

        <p class="form-pie">&copy; 2025/2026 AulaPro</p>
    </div>

</div>

</body>
</html>
