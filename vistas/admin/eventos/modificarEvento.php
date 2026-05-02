<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/eventos.php";

$idEvento = $_GET['idEvento'] ?? 0;
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
    <a href="gestionEventos.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/eventos/actualizar.php">
        <input type="hidden" name="idEvento" value="<?= $idEvento ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Evento *</label>
                <input type="text" name="tituloEvento" value="<?= $evento['tituloEvento'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>Ubicación</label>
                <input type="text" name="ubicacionEvento" value="<?= $evento['ubicacionEvento'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha *</label>
                <input type="date" name="fechaEvento" value="<?= $evento['fechaEvento'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>Hora</label>
                <input type="time" name="horaEvento" value="<?= date('H:i', strtotime($evento['horaEvento'] ?? 'now')) ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción</label>
                <textarea name="descripcionEvento" rows="3"><?= $evento['descripcionEvento'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarEvento" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


