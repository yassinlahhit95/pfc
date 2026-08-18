<?php
require_once __DIR__ . '/../../modelos/conectar.php';
require_once __DIR__ . "/../../include/Security.php";
require_once __DIR__ . "/../../include/FeatureGuard.php";
require_once __DIR__ . "/../../include/I18n.php";
require_once __DIR__ . "/../../include/AssetMin.php";
require_once __DIR__ . "/../../modelos/password_reset.php";
Security::initSession();

if (isset($_SESSION['idAdmin']))      { header("Location: ../admin/inicio/dashboard.php");      exit; }
if (isset($_SESSION['idProfesor']))   { header("Location: ../profesores/inicio/dashboard.php");  exit; }
if (isset($_SESSION['idEstudiante'])) { header("Location: ../estudiantes/inicio/dashboard.php"); exit; }

$token    = trim($_GET['token'] ?? '');
$resetRow = $token ? validarTokenReset($token) : null;

$error = $_SESSION['reset_error'] ?? null;
unset($_SESSION['reset_error']);

$csrfToken = Security::generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="<?= Security::escapeHtml(I18n::getLang()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <title>Nueva contraseña — <?= Security::escapeHtml(FeatureGuard::getCenterName()) ?></title>
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
            <h1 class="panel-titulo">Nueva <span>contraseña</span></h1>
            <p class="panel-desc">Elige una contraseña segura con al menos 8 caracteres, una mayúscula, una minúscula y un número.</p>
        </div>
    </div>

    <div class="panel-derecho">
        <div class="form-contenedor">

            <div class="form-cabecera">
                <h2>Restablecer contraseña</h2>
                <p>Introduce tu nueva contraseña</p>
            </div>

            <?php if (!$resetRow): ?>
            <div class="error-alerta">
                Este enlace ha caducado o ya fue usado.
                <a href="solicitar_reset.php" style="color:inherit;text-decoration:underline;">Solicitar uno nuevo</a>.
            </div>
            <?php else: ?>
            <?php if (!empty($error)): ?>
            <div class="error-alerta"><?= Security::escapeHtml($error) ?></div>
            <?php endif; ?>
            <form action="../../controladores/auth/confirmar_reset.php" method="POST">
                <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                <input type="hidden" name="token"      value="<?= Security::escapeHtml($token) ?>">
                <div class="campo-grupo">
                    <label>Nueva contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="pass1" name="password" placeholder="••••••••" required minlength="8" autocomplete="new-password">
                        <button type="button" class="ojo-boton" onclick="togglePass('pass1',this)">Ver</button>
                    </div>
                </div>
                <div class="campo-grupo">
                    <label>Repetir contraseña</label>
                    <div class="campo-password">
                        <input type="password" id="pass2" name="password2" placeholder="••••••••" required minlength="8" autocomplete="new-password">
                        <button type="button" class="ojo-boton" onclick="togglePass('pass2',this)">Ver</button>
                    </div>
                </div>
                <button type="submit" class="boton-acceso">Guardar nueva contraseña</button>
            </form>
            <?php endif; ?>
            <a href="../login.php" class="enlace-volver" style="display:block;margin-top:16px;text-align:center;"><?= Security::escapeHtml(__('back_to_website', 'Volver al inicio de sesión')) ?></a>
        </div>

        <p class="form-pie">&copy; <?= date('Y') ?> <?= Security::escapeHtml(FeatureGuard::getCenterName()) ?> </p>
    </div>

</div>

<script>
function togglePass(id, btn) {
    var campo = document.getElementById(id);
    campo.type = campo.type === 'password' ? 'text' : 'password';
    btn.textContent = campo.type === 'password' ? 'Ver' : 'Ocultar';
}
</script>

</body>
</html>
