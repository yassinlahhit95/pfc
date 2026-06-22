<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_eventos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_evento'] ?? [];

$titulo_pagina = "AULAPRO | AGREGAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVO EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/eventos/insertar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo">
                <label for="tituloEvento">Título del Evento</label>
                <input type="text" name="tituloEvento" id="tituloEvento" value="<?= Security::escapeHtml($datos['tituloEvento'] ?? '') ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
                
            </div>

            <div class="campo">
                <label for="ubicacionEvento">Ubicación</label>
                <input type="text" name="ubicacionEvento" id="ubicacionEvento" value="<?= Security::escapeHtml($datos['ubicacionEvento'] ?? '') ?>" placeholder="Ej: Salón de Actos, Biblioteca...">
                
            </div>

            <div class="campo">
                <label for="fechaEvento">Fecha</label>
                <input type="date" name="fechaEvento" id="fechaEvento" value="<?= $datos['fechaEvento'] ?? date('Y-m-d') ?>">
                
            </div>

            <div class="campo">
                <label for="horaEvento">Hora</label>
                <input type="time" name="horaEvento" id="horaEvento" value="<?= Security::escapeHtml($datos['horaEvento'] ?? '09:00') ?>">
                
            </div>

            <div class="campo ancho-total">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."><?= Security::escapeHtml($datos['descripcionEvento'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEvento" class="boton-primario" value="PUBLICAR EVENTO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
