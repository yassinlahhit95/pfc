<?php
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/BotGuard.php';
require_once __DIR__ . '/../include/AssetMin.php';
Security::initSession();

if (isset($_SESSION['idAdmin']))      { header("Location: admin/inicio/dashboard.php");      exit; }
if (isset($_SESSION['idProfesor']))   { header("Location: profesores/inicio/dashboard.php");  exit; }
if (isset($_SESSION['idTutor']))      { header("Location: tutores/inicio/dashboard.php");     exit; }
if (isset($_SESSION['idEstudiante'])) { header("Location: estudiantes/inicio/dashboard.php"); exit; }
if (isset($_SESSION['idSecretaria'])) { header("Location: secretaria/inicio/dashboard.php"); exit; }

$errores = $_SESSION['errores']   ?? null;
$exito   = $_SESSION['reset_ok']  ?? null;
$datos   = $_SESSION['datos_login'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_login'], $_SESSION['reset_ok']);

// Generar token CSRF
$csrfToken = Security::generateCSRFToken();
$googleClientId = Config::getInstance()->get('GOOGLE_CLIENT_ID', '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Acceso — AulaPro</title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../public/css/features/login.css') ?>">
    <?php if ($googleClientId) { ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .google-login-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 24px 0 16px;
            color: #94a3b8;
            font-size: 0.875rem;
        }
        .google-login-separator::before,
        .google-login-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }
        .google-login-separator:not(:empty)::before {
            margin-right: .75em;
        }
        .google-login-separator:not(:empty)::after {
            margin-left: .75em;
        }
        .google-login-btn-container {
            display: flex;
            justify-content: center;
            width: 100%;
            margin-bottom: 16px;
        }
        .google-login-btn-container > div {
            width: 100% !important;
        }
    </style>
    <?php } ?>
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

            <?php if ($exito) { ?>
            <div class="error-alerta" style="background:#ecfdf5;border-color:#6ee7b7;color:#065f46;"><?= Security::escapeHtml($exito) ?></div>
            <?php } ?>
            <?php if ($errores) { ?>
            <div class="error-alerta">
                <?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?>
            </div>
            <?php } ?>

            <form action="../controladores/validacion.php" method="POST" id="formLogin">
                <!-- CSRF Token Protection -->
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                <?= BotGuard::renderFields() ?>

                <div class="campo-grupo">
                    <label>Usuario / Email</label>
                    <input type="text" name="usuario" placeholder="ejemplo@correo.com" value="<?= Security::escapeHtml($datos['usuario'] ?? $_GET['u'] ?? '') ?>" autofocus>
                </div>

                <div class="campo-grupo">
                    <label>Contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="pass_field" name="contrasena" placeholder="********" autocomplete="new-password">
                        <button type="button" id="btn_ver" class="ojo-boton">
                            Ver
                        </button>
                    </div>
                </div>

                <button type="submit" name="enviar" class="boton-acceso">Entrar</button>

                <?php if ($googleClientId) { ?>
                <div class="google-login-separator">
                    <span>o continuar con</span>
                </div>

                <div class="google-login-btn-container">
                    <div id="g_id_onload"
                         data-client_id="<?= Security::escapeHtml($googleClientId) ?>"
                         data-context="signin"
                         data-ux_mode="popup"
                         data-callback="handleGoogleCredential"
                         data-auto_prompt="false">
                    </div>
                    <div class="g_id_signin"
                         data-type="standard"
                         data-shape="rectangular"
                         data-theme="outline"
                         data-text="signin_with"
                         data-size="large"
                         data-logo_alignment="left"
                         data-width="380">
                    </div>
                </div>
                <?php } ?>

                <a href="auth/solicitar_reset.php" style="display:block;text-align:center;margin-top:12px;font-size:.875rem;color:var(--dim);">¿Olvidaste tu contraseña?</a>

                <a href="../index.php" class="enlace-volver">Volver a la web</a>

            </form>
        </div>

        <?php if ($googleClientId) { ?>
        <form id="googleLoginForm" action="../controladores/validacion_google.php" method="POST" style="display:none;">
            <input type="hidden" name="id_token" id="googleIdToken">
            <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
        </form>
        <?php } ?>

        <p class="form-pie">&copy; 2025/2026 AulaPro</p>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
<?php if ($googleClientId) { ?>
<script>
function handleGoogleCredential(response) {
    if (response.credential) {
        document.getElementById('googleIdToken').value = response.credential;
        document.getElementById('googleLoginForm').submit();
    }
}
</script>
<?php } ?>
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
