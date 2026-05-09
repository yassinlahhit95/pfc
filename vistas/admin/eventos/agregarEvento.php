<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | AGREGAR EVENTO";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>CREAR NUEVO EVENTO</h1>
    <a href="gestionEventos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/eventos/insertar.php">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="tituloEvento">Título del Evento *</label>
                <input type="text" name="tituloEvento" id="tituloEvento" placeholder="Ej: Examen Final, Reunión de Profesores...">
            </div>

            <div class="campo-formulario">
                <label for="ubicacionEvento">Ubicación</label>
                <input type="text" name="ubicacionEvento" id="ubicacionEvento" placeholder="Ej: Aula 101, Salón de Actos...">
            </div>

            <div class="campo-formulario">
                <label for="fechaEvento">Fecha *</label>
                <input type="date" name="fechaEvento" id="fechaEvento" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="campo-formulario">
                <label for="horaEvento">Hora</label>
                <input type="time" name="horaEvento" id="horaEvento" value="09:00">
            </div>

            <div class="campo-formulario">
                <label for="descripcionEvento">Descripción</label>
                <textarea name="descripcionEvento" id="descripcionEvento" rows="4" placeholder="Detalles del evento..."></textarea>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarEvento" class="boton-primario">
                <i class="fas fa-calendar-plus"></i> PUBLICAR EVENTO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


