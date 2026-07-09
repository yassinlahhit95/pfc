<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$evento = obtenerEventoPorId((int)($_GET['idEvento'] ?? 0));
if (!$evento) {
    header("Location: gestionEventos.php");
    exit;
}

$datos = $_SESSION['datos_evento'] ?? null;
unset($_SESSION['datos_evento']);

$v = fn($k) => Security::escapeHtml($datos[$k] ?? $evento[$k] ?? '');

$titulo_pagina = "AULAPRO | EDITAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/eventos/actualizar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEvento" value="<?= (int)$evento['idEvento'] ?>">

        <div class="campo<?= fieldClass($errores, 'tituloEvento') ?>">
            <label for="tituloEvento">Título <span style="color:var(--rojo)">*</span></label>
            <input type="text" name="tituloEvento" id="tituloEvento" maxlength="255"
                   value="<?= $v('tituloEvento') ?>">
            <?= fieldError($errores, 'tituloEvento') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaEvento') ?>">
            <label for="fechaEvento">Fecha <span style="color:var(--rojo)">*</span></label>
            <input type="date" name="fechaEvento" id="fechaEvento"
                   value="<?= $v('fechaEvento') ?>">
            <?= fieldError($errores, 'fechaEvento') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'horaEvento') ?>">
            <label for="horaEvento">Hora <span style="color:var(--rojo)">*</span></label>
            <input type="time" name="horaEvento" id="horaEvento"
                   value="<?= $v('horaEvento') ?>">
            <?= fieldError($errores, 'horaEvento') ?>
        </div>

        <div class="campo">
            <label for="ubicacionEvento">Ubicación</label>
            <input type="text" name="ubicacionEvento" id="ubicacionEvento" maxlength="255"
                   value="<?= $v('ubicacionEvento') ?>">
        </div>

        <div class="campo campo-ancho-total">
            <label for="descripcionEvento">Descripción</label>
            <textarea name="descripcionEvento" id="descripcionEvento" rows="4"><?= $v('descripcionEvento') ?></textarea>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> GUARDAR CAMBIOS</button>
            <a href="gestionEventos.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
