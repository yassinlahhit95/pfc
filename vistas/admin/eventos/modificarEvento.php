<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = $_GET['idEvento'] ?? 0;
$evento = obtenerEventoPorId($idEvento);

if (!$evento) {
    header("Location: gestionEventos.php");
    exit;
}

$datos = $_SESSION['datos_evento'] ?? [];

$titulo_pagina = "AULAPRO | MODIFICAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <?php if ($errores) { ?>
        <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
    <form method="POST" action="../../../controladores/admin/eventos/actualizar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEvento" value="<?= $idEvento ?>">

        <div class="formulario">
            <div class="campo">
                <label for="tituloEvento">Título del Evento</label>
                <input type="text" name="tituloEvento" id="tituloEvento" value="<?= $datos['tituloEvento'] ?? $evento['tituloEvento'] ?? '' ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
                
            </div>

            <div class="campo">
                <label for="ubicacionEvento">Ubicación</label>
                <input type="text" name="ubicacionEvento" id="ubicacionEvento" value="<?= $datos['ubicacionEvento'] ?? $evento['ubicacionEvento'] ?? '' ?>" placeholder="Ej: Salón de Actos, Biblioteca...">
                
            </div>

            <div class="campo">
                <label for="fechaEvento">Fecha</label>
                <input type="date" name="fechaEvento" id="fechaEvento" value="<?= $datos['fechaEvento'] ?? $evento['fechaEvento'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="horaEvento">Hora</label>
                <input type="time" name="horaEvento" id="horaEvento" value="<?= $datos['horaEvento'] ?? date('H:i', strtotime($evento['horaEvento'] ?? 'now')) ?>">
                
            </div>

            <div class="campo">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."><?= $datos['descripcionEvento'] ?? $evento['descripcionEvento'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarEvento" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
