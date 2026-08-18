<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/secretarias.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$secretaria = obtenerSecretariaPorId($_SESSION['idSecretaria']);
$datos      = $_SESSION['datos_perfil'] ?? null;
unset($_SESSION['datos_perfil']);

$valorCampo = fn($k) => Security::escapeHtml($datos[$k] ?? $secretaria[$k] ?? '');

$titulo_pagina = "Editar Perfil";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Editar Perfil</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/perfil/actualizar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'nombreSecretaria') ?>">
                <label for="nombreSecretaria">Nombre <span style="color:var(--rojo)">*</span></label>
                <input type="text" name="nombreSecretaria" id="nombreSecretaria" maxlength="100"
                       value="<?= $valorCampo('nombreSecretaria') ?>">
                <?= fieldError($errores, 'nombreSecretaria') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'emailSecretaria') ?>">
                <label for="emailSecretaria">Email <span style="color:var(--rojo)">*</span></label>
                <input type="email" name="emailSecretaria" id="emailSecretaria" maxlength="150"
                       value="<?= $valorCampo('emailSecretaria') ?>">
                <?= fieldError($errores, 'emailSecretaria') ?>
            </div>
        </div>

        <hr style="grid-column:1/-1; border:none; border-top:1px solid var(--border); margin:0.5rem 0;">

        <div class="campo campo-ancho-total" style="grid-column:1/-1;">
            <p class="texto-suave"><i class="fas fa-lock"></i> Cambio de contraseña (opcional)</p>
        </div>

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'current_password') ?>">
                <label for="current_password">Contraseña actual</label>
                <input type="password" name="current_password" id="current_password"
                       autocomplete="current-password">
                <?= fieldError($errores, 'current_password') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'new_password') ?>">
                <label for="new_password">Nueva contraseña</label>
                <input type="password" name="new_password" id="new_password"
                       placeholder="Dejar vacío para no cambiar"
                       autocomplete="new-password">
                <?= fieldError($errores, 'new_password') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'confirm_password') ?>">
                <label for="confirm_password">Confirmar nueva contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password"
                       autocomplete="new-password">
                <?= fieldError($errores, 'confirm_password') ?>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> GUARDAR CAMBIOS</button>
            <a href="ver.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
