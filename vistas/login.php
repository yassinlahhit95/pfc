<?php
session_start();
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/BotGuard.php';

if (isset($_SESSION['idAdmin']))      { header("Location: admin/inicio/dashboard.php");      exit; }
if (isset($_SESSION['idProfesor']))   { header("Location: profesores/inicio/dashboard.php");  exit; }
if (isset($_SESSION['idTutor']))      { header("Location: tutores/inicio/dashboard.php");     exit; }
if (isset($_SESSION['idEstudiante'])) { header("Location: estudiantes/inicio/dashboard.php"); exit; }
if (isset($_SESSION['idSecretaria'])) { header("Location: secretaria/inicio/dashboard.php"); exit; }

$err    = $_SESSION['errores']   ?? null;
$ok     = $_SESSION['reset_ok']  ?? null;
$vals   = $_SESSION['datos_login'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_login'], $_SESSION['reset_ok']);

// Generar token CSRF
$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>

<div class="login-page">

    <div class="panel-izquierdo">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="panel-contenido">
            <div class="panel-logo">
                <img src="../public/imagenes/aulapro.png" alt="Logo">
            </div>
            <h1 class="panel-titulo">Plataforma de <span>Gestión Escolar</span></h1>
            <p class="panel-desc">Proyecto para la gestión de alumnos, profesores, notas y administración general del centro educativo.</p>
            <div class="panel-stats">
                <div class="stat"><strong>3</strong><span>Perfiles</span></div>
                <div class="stat"><strong>100%</strong><span>Online</span></div>
            </div>
        </div>
    </div>

    <div class="panel-derecho">
        <div class="form-contenedor">

            <div class="form-cabecera">
                <h2>Identificarse</h2>
                <p>Introduce tus datos para entrar al sistema</p>
            </div>

            <?php if ($ok) { ?>
            <div class="error-alerta" style="background:#ecfdf5;border-color:#6ee7b7;color:#065f46;"><?= Security::escapeHtml($ok) ?></div>
            <?php } ?>
            <?php if ($err) { ?>
            <div class="error-alerta">
                <?= Security::escapeHtml(is_array($err) ? implode(' ', $err) : $err) ?>
            </div>
            <?php } ?>

            <form action="../controladores/validacion.php" method="POST" id="formLogin">
                <!-- CSRF Token Protection -->
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <?= BotGuard::renderFields() ?>

                <div class="campo-grupo">
                    <label>Usuario / Email</label>
                    <input type="text" name="usuario" placeholder="ejemplo@correo.com" value="<?= Security::escapeHtml($vals['usuario'] ?? $_GET['u'] ?? '') ?>" autofocus>
                </div>

                <div class="campo-grupo">
                    <label>Contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="pass_field" name="contrasena" placeholder="********">
                        <button type="button" id="btn_ver" class="ojo-boton">
                            Ver
                        </button>
                    </div>
                </div>

                <button type="submit" name="enviar" class="boton-acceso">Entrar</button>

                <a href="auth/solicitar_reset.php" style="display:block;text-align:center;margin-top:12px;font-size:.875rem;color:#6b7280;">¿Olvidaste tu contraseña?</a>

                <a href="../index.php" class="enlace-volver">Volver a la web</a>

            </form>
        </div>

        <p class="form-pie">&copy; 2025/2026 AulaPro</p>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function() {
    $('#btn_ver').on('click', function() {
        var campo = $('#pass_field');
        if (campo.attr('type') === 'password') {
            campo.attr('type', 'text');
            $(this).text('Ocultar');
        } else {
            campo.attr('type', 'password');
            $(this).text('Ver');
        }
    });
});
</script>

</body>
</html>
