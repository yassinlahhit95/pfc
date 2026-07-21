<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_eventos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = (int)($_GET['idEvento'] ?? 0);
$evento = obtenerEventoPorId($idEvento);

if (!$evento) {
    header("Location: gestionEventos.php");
    exit;
}

$datos = $_SESSION['datos_evento'] ?? [];
unset($_SESSION['datos_evento']);

$titulo_pagina = "AULAPRO | MODIFICAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/eventos/actualizar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEvento" value="<?= $idEvento ?>">

        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'tituloEvento') ?>">
                    <label for="tituloEvento">Título del Evento</label>
                    <input type="text" name="tituloEvento" id="tituloEvento" value="<?= Security::escapeHtml($datos['tituloEvento'] ?? $evento['tituloEvento'] ?? '') ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
                    <?= fieldError($errores, 'tituloEvento') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'ubicacionEvento') ?>">
                    <label for="ubicacionEvento">Ubicación</label>
                    <input type="text" name="ubicacionEvento" id="ubicacionEvento" value="<?= Security::escapeHtml($datos['ubicacionEvento'] ?? $evento['ubicacionEvento'] ?? '') ?>" placeholder="Ej: Salón de Actos, Biblioteca...">
                    <?= fieldError($errores, 'ubicacionEvento') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'fechaEvento') ?>">
                    <label for="fechaEvento">Fecha</label>
                    <input type="date" name="fechaEvento" id="fechaEvento" value="<?= Security::escapeHtml($datos['fechaEvento'] ?? $evento['fechaEvento'] ?? '') ?>">
                    <?= fieldError($errores, 'fechaEvento') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'horaEvento') ?>">
                    <label for="horaEvento">Hora</label>
                    <input type="time" name="horaEvento" id="horaEvento" value="<?= Security::escapeHtml($datos['horaEvento'] ?? date('H:i', strtotime($evento['horaEvento'] ?? 'now'))) ?>">
                    <?= fieldError($errores, 'horaEvento') ?>
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."><?= Security::escapeHtml($datos['descripcionEvento'] ?? $evento['descripcionEvento'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEvento" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
