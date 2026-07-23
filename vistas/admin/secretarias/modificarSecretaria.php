<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/form_helpers.php';
require_once __DIR__ . '/../../../modelos/secretarias.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
$datos   = $_SESSION['datos_secretaria'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_secretaria']);

$idSecretaria = (int)($_GET['id'] ?? 0);
if (!$idSecretaria) {
    header("Location: verSecretarias.php");
    exit;
}

$secretaria = $datos
    ? array_merge(obtenerSecretariaPorId($idSecretaria) ?? [], $datos)
    : obtenerSecretariaPorId($idSecretaria);

if (!$secretaria) {
    header("Location: verSecretarias.php");
    exit;
}

$titulo_pagina = 'AULAPRO | EDITAR SECRETARIA';
$seccion = 'secretarias';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>EDITAR SECRETARIA</h1>
    <a href="verSecretarias.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/secretarias/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idSecretaria" value="<?= (int)$secretaria['idSecretaria'] ?>">

        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreSecretaria') ?>">
                    <label for="nombreSecretaria">Nombre Completo <span style="color:red;">*</span></label>
                    <input type="text" id="nombreSecretaria" name="nombreSecretaria"
                           value="<?= Security::escapeHtml($secretaria['nombreSecretaria'] ?? '') ?>">
                    <?= fieldError($errores, 'nombreSecretaria') ?>
                </div>
                <div class="campo<?= fieldClass($errores, 'emailSecretaria') ?>">
                    <label for="emailSecretaria">Correo Electrónico <span style="color:red;">*</span></label>
                    <input type="email" id="emailSecretaria" name="emailSecretaria"
                           value="<?= Security::escapeHtml($secretaria['emailSecretaria'] ?? '') ?>">
                    <?= fieldError($errores, 'emailSecretaria') ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top:24px;">
            <input type="submit" name="actualizarSecretaria" class="boton-primario" value="GUARDAR CAMBIOS">
            <a href="verSecretarias.php" class="boton-secundario">CANCELAR</a>
        </div>
    </form>
</div>

<div class="panel" style="margin-top:20px;">
    <h3 style="margin:0 0 16px;"><i class="fas fa-key" style="color:var(--accent);"></i> Cambiar contraseña</h3>
    <div class="formulario" style="grid-template-columns:1fr 1fr;gap:15px;align-items:end;">
        <div class="campo">
            <label for="nueva-pass-sec">Nueva contraseña <small style="color:var(--dim);">(mín. 8 caracteres)</small></label>
            <input type="password" id="nueva-pass-sec" minlength="8" placeholder="Nueva contraseña" autocomplete="new-password">
        </div>
        <div class="campo">
            <label for="nueva-pass-sec-confirm">Confirmar contraseña</label>
            <input type="password" id="nueva-pass-sec-confirm" minlength="8" placeholder="Repetir contraseña" autocomplete="new-password">
        </div>
    </div>
    <div class="acciones" style="margin-top:16px;">
        <button type="button" class="boton-primario" onclick="cambiarPassSec()">
            <i class="fas fa-lock"></i> Guardar contraseña
        </button>
    </div>
</div>

<div class="panel" style="margin-top:20px;">
    <h3 style="margin:0 0 16px;"><i class="fas fa-route" style="color:var(--accent);"></i> Tour de bienvenida</h3>
    <form method="POST" action="/controladores/admin/tours/reiniciar.php"
          data-ajax-confirm="¿Reiniciar el tour de bienvenida de esta secretaría? Volverá a verlo en su próximo inicio de sesión.">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idUsuario" value="<?= $idSecretaria ?>">
        <input type="hidden" name="tipoUsuario" value="secretaria">
        <button type="submit" class="boton-secundario"><i class="fas fa-route"></i> Reiniciar tour</button>
    </form>
</div>

<script>
function cambiarPassSec() {
    var pass    = document.getElementById('nueva-pass-sec').value.trim();
    var confirm = document.getElementById('nueva-pass-sec-confirm').value.trim();
    if (pass.length < 8) {
        if (window.Toast) Toast.show('La contraseña debe tener al menos 8 caracteres.', 'error');
        return;
    }
    if (pass !== confirm) {
        if (window.Toast) Toast.show('Las contraseñas no coinciden.', 'error');
        return;
    }
    var token = document.querySelector('[name=csrf_token]').value;
    fetch('../../../controladores/admin/usuarios/cambiarPassword.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'tipo=secretaria&id=<?= (int)$secretaria['idSecretaria'] ?>&nuevaPassword=' + encodeURIComponent(pass) + '&csrf_token=' + encodeURIComponent(token)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) {
            document.getElementById('nueva-pass-sec').value = '';
            document.getElementById('nueva-pass-sec-confirm').value = '';
            setTimeout(function() { location.reload(); }, 1800);
        }
    })
    .catch(function() { if (window.Toast) Toast.show('Error de conexión.', 'error'); });
}
</script>

<?php include '../comunes/footer.php'; ?>
