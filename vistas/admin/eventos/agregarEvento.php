<?php
session_start();

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_evento'] ?? [];
unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_evento']);

$titulo_pagina = "AULAPRO | AGREGAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVO EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/eventos/insertar.php">
        <div class="formulario">
            <div class="campo">
                <label for="tituloEvento">Título del Evento *</label>
                <input type="text" name="tituloEvento" id="tituloEvento" value="<?= $datos['tituloEvento'] ?? '' ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
                <?php if (isset($errores['tituloEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['tituloEvento'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="ubicacionEvento">Ubicación *</label>
                <input type="text" name="ubicacionEvento" id="ubicacionEvento" value="<?= $datos['ubicacionEvento'] ?? '' ?>" placeholder="Ej: Salón de Actos, Biblioteca...">
                <?php if (isset($errores['ubicacionEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['ubicacionEvento'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaEvento">Fecha *</label>
                <input type="date" name="fechaEvento" id="fechaEvento" value="<?= $datos['fechaEvento'] ?? date('Y-m-d') ?>">
                <?php if (isset($errores['fechaEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaEvento'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="horaEvento">Hora *</label>
                <input type="time" name="horaEvento" id="horaEvento" value="<?= $datos['horaEvento'] ?? '09:00' ?>">
                <?php if (isset($errores['horaEvento'])) { ?>
                    <strong class="error-campo"><?= $errores['horaEvento'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."><?= $datos['descripcionEvento'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="guardarEvento" class="boton-primario">
                <i class="fas fa-calendar-plus"></i> PUBLICAR EVENTO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
