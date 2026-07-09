<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../include/form_helpers.php";

$datos = $_SESSION['datos_evento'] ?? [];
unset($_SESSION['datos_evento']);

$titulo_pagina = "AULAPRO | NUEVO EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/eventos/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'tituloEvento') ?>">
            <label for="tituloEvento">Título <span style="color:var(--rojo)">*</span></label>
            <input type="text" name="tituloEvento" id="tituloEvento" maxlength="255"
                   placeholder="Ej: Jornada de puertas abiertas"
                   value="<?= Security::escapeHtml($datos['tituloEvento'] ?? '') ?>">
            <?= fieldError($errores, 'tituloEvento') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaEvento') ?>">
            <label for="fechaEvento">Fecha <span style="color:var(--rojo)">*</span></label>
            <input type="date" name="fechaEvento" id="fechaEvento"
                   value="<?= Security::escapeHtml($datos['fechaEvento'] ?? '') ?>">
            <?= fieldError($errores, 'fechaEvento') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'horaEvento') ?>">
            <label for="horaEvento">Hora <span style="color:var(--rojo)">*</span></label>
            <input type="time" name="horaEvento" id="horaEvento"
                   value="<?= Security::escapeHtml($datos['horaEvento'] ?? '') ?>">
            <?= fieldError($errores, 'horaEvento') ?>
        </div>

        <div class="campo">
            <label for="ubicacionEvento">Ubicación</label>
            <input type="text" name="ubicacionEvento" id="ubicacionEvento" maxlength="255"
                   placeholder="Ej: Salón de actos"
                   value="<?= Security::escapeHtml($datos['ubicacionEvento'] ?? '') ?>">
        </div>

        <div class="campo campo-ancho-total">
            <label for="descripcionEvento">Descripción</label>
            <textarea name="descripcionEvento" id="descripcionEvento" rows="4"
                      placeholder="Descripción del evento..."><?= Security::escapeHtml($datos['descripcionEvento'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> CREAR EVENTO</button>
            <a href="gestionEventos.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
