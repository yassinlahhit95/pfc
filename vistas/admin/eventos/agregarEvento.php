<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "Agregar Evento - Admin";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>Crear Nuevo Evento</h1>
    <a href="gestionEventos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER A LA LISTA
    </a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca p-25">
    <form method="POST" action="../../../controladores/admin/eventos/insertar.php" class="p-10">
        <div class="disposicion-flexible direccion-columna separacion-grande">
            <div class="campo-formulario">
                <label class="texto-negrita">Título del Evento *</label>
                <input type="text" name="tituloEvento" class="mt-5 ancho-total" placeholder="Ej: Examen Final, Reunión de Profesores...">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Ubicación</label>
                <input type="text" name="ubicacionEvento" class="mt-5 ancho-total" placeholder="Ej: Aula 101, Salón de Actos...">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Fecha *</label>
                <input type="date" name="fechaEvento" class="mt-5 ancho-total" value="<?= date('Y-m-d') ?>">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Hora</label>
                <input type="time" name="horaEvento" class="mt-5 ancho-total" value="09:00">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Descripción</label>
                <textarea name="descripcionEvento" rows="4" class="mt-5 ancho-total" placeholder="Detalles del evento..."></textarea>
            </div>
        </div>

        <div class="margen-arriba-grande disposicion-flexible" style="justify-content: flex-end; gap: 15px;">
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
            <button type="submit" name="guardarEvento" class="boton-primario px-30">
                <i class="fas fa-calendar-plus"></i> PUBLICAR EVENTO
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

