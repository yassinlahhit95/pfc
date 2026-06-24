<?php
require_once __DIR__ . '/../../../include/AdminGuard.php';
require_once __DIR__ . '/../../../include/form_helpers.php';

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
$datos   = $_SESSION['datos_secretaria'] ?? [];
unset($_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_secretaria']);

$titulo_pagina = 'AULAPRO | NUEVA SECRETARIA';
$seccion = 'secretarias';
include_once __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>NUEVA SECRETARIA</h1>
    <a href="verSecretarias.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/secretarias/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreSecretaria') ?>">
                <label for="nombreSecretaria">Nombre Completo <span style="color:red;">*</span></label>
                <input type="text" id="nombreSecretaria" name="nombreSecretaria"
                       value="<?= Security::escapeHtml($datos['nombreSecretaria'] ?? '') ?>"
                       placeholder="Ej: María García López">
                <?= fieldError($errores, 'nombreSecretaria') ?>
            </div>
            <div class="campo<?= fieldClass($errores, 'emailSecretaria') ?>">
                <label for="emailSecretaria">Correo Electrónico <span style="color:red;">*</span></label>
                <input type="email" id="emailSecretaria" name="emailSecretaria"
                       value="<?= Security::escapeHtml($datos['emailSecretaria'] ?? '') ?>"
                       placeholder="correo@centro.es">
                <?= fieldError($errores, 'emailSecretaria') ?>
            </div>
        </div>

        <p class="texto-suave small" style="margin-top:12px;">
            <i class="fas fa-info-circle" style="color:var(--accent);"></i>
            Se generará una contraseña temporal y será enviada al correo indicado. La secretaria deberá cambiarla en su primer acceso.
        </p>

        <div class="acciones" style="margin-top:24px;">
            <input type="submit" name="guardarSecretaria" class="boton-primario" value="REGISTRAR SECRETARIA">
            <a href="verSecretarias.php" class="boton-secundario">CANCELAR</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
