<?php
require_once __DIR__ . '/../modelos/conectar.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/BotGuard.php';
require_once __DIR__ . '/../include/AssetMin.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../include/I18n.php';
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
$currentLang = I18n::getLang();
?>
<!DOCTYPE html>
<html lang="<?= Security::escapeHtml($currentLang) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title><?= Security::escapeHtml(__('login_title', 'Acceso')) ?> — <?= Security::escapeHtml(FeatureGuard::getCenterName()) ?></title>
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous">
    <link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../public/css/features/login.css') ?>">
    <?php if ($googleClientId) { ?>
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <style>
        .google-login-separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0 16px;
            color: var(--lp-text-muted);
            font-size: 0.85rem;
        }
        .google-login-separator::before,
        .google-login-separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid var(--lp-input-border);
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
            margin-bottom: 12px;
        }
        .google-login-btn-container > div {
            width: 100% !important;
        }
    </style>
    <?php } ?>
</head>
<body>

<div class="login-page">

    <!-- Topbar: Multi-Language Selector -->
    <div class="login-topbar">
        <form action="../controladores/cambiar_idioma.php" method="POST" id="formLoginLang" style="margin:0;">
            <select name="lang" class="login-lang-select" onchange="document.getElementById('formLoginLang').submit();" aria-label="<?= Security::escapeHtml(__('language', 'Idioma')) ?>">
                <option value="es" <?= $currentLang === 'es' ? 'selected' : '' ?>>🇪🇸 <?= Security::escapeHtml(__('spanish', 'Español')) ?></option>
                <option value="en" <?= $currentLang === 'en' ? 'selected' : '' ?>>🇬🇧 <?= Security::escapeHtml(__('english', 'English')) ?></option>
                <option value="ca" <?= $currentLang === 'ca' ? 'selected' : '' ?>>🏴 <?= Security::escapeHtml(__('catalan', 'Català')) ?></option>
                <option value="eu" <?= $currentLang === 'eu' ? 'selected' : '' ?>>🏴 <?= Security::escapeHtml(__('basque', 'Euskara')) ?></option>
            </select>
        </form>
    </div>

    <div class="panel-izquierdo">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>

        <div class="panel-contenido">
            <div class="panel-logo">
                <img src="../public/imagenes/aulapro.png" alt="<?= Security::escapeHtml(FeatureGuard::getCenterName()) ?> Logo">
            </div>
            <h1 class="panel-titulo"><?= Security::escapeHtml(__('login_hero_title', 'Plataforma de Gestión Educativa')) ?></h1>
            <p class="panel-desc"><?= Security::escapeHtml(__('login_hero_desc', 'Gestión académica integral, aula virtual y comunicación institucional en un único ecosistema.')) ?></p>
            <div class="panel-stats">
                <div class="stat"><strong>5</strong><span><?= Security::escapeHtml(__('stats_profiles', 'Perfiles')) ?></span></div>
                <div class="stat"><strong>100%</strong><span><?= Security::escapeHtml(__('stats_online', 'Online')) ?></span></div>
                <div class="stat"><strong>256-bit</strong><span><?= Security::escapeHtml(__('stats_security', 'Seguro')) ?></span></div>
            </div>
        </div>
    </div>

    <div class="panel-derecho">
        <div class="form-contenedor">

            <div class="form-cabecera">
                <h2><?= Security::escapeHtml(__('login_title', 'Identificarse')) ?></h2>
                <p><?= Security::escapeHtml(__('login_subtitle', 'Introduce tus datos para acceder a tu panel')) ?></p>
            </div>

            <?php if ($exito) { ?>
            <div class="error-alerta" style="background:rgba(16,185,129,0.15);border-color:rgba(16,185,129,0.3);color:#34d399;">
                <i class="fas fa-check-circle"></i>
                <span><?= Security::escapeHtml($exito) ?></span>
            </div>
            <?php } ?>
            <?php if ($errores) { ?>
            <div class="error-alerta">
                <i class="fas fa-triangle-exclamation"></i>
                <span><?= Security::escapeHtml(is_array($errores) ? implode(' ', $errores) : $errores) ?></span>
            </div>
            <?php } ?>

            <form action="../controladores/validacion.php" method="POST" id="formLogin">
                <!-- CSRF Token Protection -->
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                <?= BotGuard::renderFields() ?>

                <div class="campo-grupo">
                    <label for="campo_usuario"><?= Security::escapeHtml(__('user_or_email', 'Usuario o correo electrónico')) ?></label>
                    <input type="text" id="campo_usuario" name="usuario" placeholder="ejemplo@correo.com" value="<?= Security::escapeHtml($datos['usuario'] ?? $_GET['u'] ?? '') ?>" autofocus required>
                </div>

                <div class="campo-grupo">
                    <label for="pass_field"><?= Security::escapeHtml(__('password', 'Contraseña')) ?></label>
                    <div class="campo-password">
                        <input type="password" id="pass_field" name="contrasena" placeholder="••••••••" autocomplete="current-password" required>
                        <button type="button" id="btn_ver" class="ojo-boton" aria-label="<?= Security::escapeHtml(__('show', 'Ver')) ?>">
                            <i class="fas fa-eye" id="icono_ojo"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" name="enviar" class="boton-acceso"><?= Security::escapeHtml(__('enter_btn', 'Iniciar Sesión')) ?></button>

                <?php if ($googleClientId) { ?>
                <div class="google-login-separator">
                    <span><?= Security::escapeHtml(__('continue_with_google', 'o continuar con Google')) ?></span>
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
                         data-width="400">
                    </div>
                </div>
                <?php } ?>

                <a href="auth/solicitar_reset.php" class="enlace-reset"><?= Security::escapeHtml(__('forgot_password', '¿Olvidaste tu contraseña?')) ?></a>

                <a href="../index.php" class="enlace-volver">
                    <i class="fas fa-arrow-left"></i>
                    <span><?= Security::escapeHtml(__('back_to_website', 'Volver a la web')) ?></span>
                </a>

            </form>
        </div>

        <?php if ($googleClientId) { ?>
        <form id="googleLoginForm" action="../controladores/validacion_google.php" method="POST" style="display:none;">
            <input type="hidden" name="id_token" id="googleIdToken">
            <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
        </form>
        <?php } ?>

        <p class="form-pie">&copy; <?= date('Y') ?> <?= Security::escapeHtml(FeatureGuard::getCenterName()) ?> </p>
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
        var icono = $('#icono_ojo');
        if (campo.attr('type') === 'password') {
            campo.attr('type', 'text');
            icono.removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            campo.attr('type', 'password');
            icono.removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });
});
</script>

</body>
</html>
