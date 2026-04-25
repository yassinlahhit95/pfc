<?php
session_start();
if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../../modelos/eventos.php";

$idEvento = $_GET['idEvento'] ?? 0;
$evento = obtenerEventoPorId($idEvento);

if (!$evento) {
    header("Location: /pfc/vistas/admin/eventos/gestionEventos.php");
    exit;
}

$titulo_pagina = "Modificar Evento - Super Admin";
$seccion = 'eventos';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Evento</h1>
    <a href="gestionEventos.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/eventos/actualizar.php">
        <input type="hidden" name="idEvento" value="<?php echo $idEvento; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Título del Evento *</label>
                <input type="text" name="tituloEvento" required value="<?php echo $evento['tituloEvento']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Ubicación</label>
                <input type="text" name="ubicacionEvento" value="<?php echo $evento['ubicacionEvento']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha *</label>
                <input type="date" name="fechaEvento" required value="<?php echo $evento['fechaEvento']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Hora</label>
                <input type="time" name="horaEvento" value="<?php echo date('H:i', strtotime($evento['horaEvento'])); ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción</label>
                <textarea name="descripcionEvento" rows="3"><?php echo $evento['descripcionEvento']; ?></textarea>
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
