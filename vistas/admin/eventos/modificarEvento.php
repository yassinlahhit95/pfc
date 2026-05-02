<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = intval($_GET['idEvento'] ?? 0);
$evento = obtenerEventoPorId($idEvento);

if (!$evento) {
    header("Location: gestionEventos.php");
    exit;
}

$titulo_pagina = "Modificar Evento - Admin";
$seccion = 'eventos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Evento</h1>
    <a href="gestionEventos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la Lista
    </a>
</div>

<div class="tarjeta-blanca p-25">
    <form method="POST" action="../../../controladores/admin/eventos/actualizar.php" class="p-10">
        <input type="hidden" name="idEvento" value="<?= $idEvento ?>">
        
        <div class="disposicion-flexible direccion-columna separacion-grande">
            <div class="campo-formulario">
                <label class="texto-negrita">Título del Evento *</label>
                <input type="text" name="tituloEvento" class="mt-5 ancho-total" value="<?= $evento['tituloEvento'] ?? '' ?>" placeholder="Ej: Examen Final, Reunión de Profesores...">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Ubicación</label>
                <input type="text" name="ubicacionEvento" class="mt-5 ancho-total" value="<?= $evento['ubicacionEvento'] ?? '' ?>" placeholder="Ej: Aula 101, Salón de Actos...">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Fecha *</label>
                <input type="date" name="fechaEvento" class="mt-5 ancho-total" value="<?= $evento['fechaEvento'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Hora</label>
                <input type="time" name="horaEvento" class="mt-5 ancho-total" value="<?= date('H:i', strtotime($evento['horaEvento'] ?? 'now')) ?>">
            </div>

            <div class="campo-formulario">
                <label class="texto-negrita">Descripción</label>
                <textarea name="descripcionEvento" rows="4" class="mt-5 ancho-total" placeholder="Detalles del evento..."><?= $evento['descripcionEvento'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="margen-arriba-grande disposicion-flexible" style="justify-content: flex-end; gap: 15px;">
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
            <button type="submit" name="actualizarEvento" class="boton-primario px-30">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
