<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = intval($_GET['idEvento'] ?? 0);
$evento = obtenerEventoPorId($idEvento);

if (!$evento) {
    header("Location: gestionEventos.php");
    exit;
}

$titulo_pagina = "AULAPRO | MODIFICAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_evento'] ?? [];
$error = $_SESSION['error'] ?? '';
unset($_SESSION['errores'], $_SESSION['datos_evento'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>
    <form method="POST" action="../../../controladores/admin/eventos/actualizar.php">
        <input type="hidden" name="idEvento" value="<?= $idEvento ?>">

        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="tituloEvento">Título del Evento *</label>
                <input type="text" name="tituloEvento" id="tituloEvento" value="<?= $datos['tituloEvento'] ?? $evento['tituloEvento'] ?? '' ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
                <?php if (isset($errores['tituloEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['tituloEvento'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ubicacionEvento">Ubicación *</label>
                <input type="text" name="ubicacionEvento" id="ubicacionEvento" value="<?= $datos['ubicacionEvento'] ?? $evento['ubicacionEvento'] ?? '' ?>" placeholder="Ej: Salón de Actos, Biblioteca...">
                <?php if (isset($errores['ubicacionEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['ubicacionEvento'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaEvento">Fecha *</label>
                <input type="date" name="fechaEvento" id="fechaEvento" value="<?= $datos['fechaEvento'] ?? $evento['fechaEvento'] ?? '' ?>">
                <?php if (isset($errores['fechaEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaEvento'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="horaEvento">Hora *</label>
                <input type="time" name="horaEvento" id="horaEvento" value="<?= $datos['horaEvento'] ?? date('H:i', strtotime($evento['horaEvento'] ?? 'now')) ?>">
                <?php if (isset($errores['horaEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['horaEvento'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."><?= $datos['descripcionEvento'] ?? $evento['descripcionEvento'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarEvento" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


