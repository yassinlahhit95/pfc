<?php
session_start();
$titulo_pagina = "Detalle de Reclamación - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";

require_once "../../../modelos/reclamaciones.php";

$id = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

if (empty($id)) {
    header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
    exit;
}

$reclamacion = obtenerReclamacionPorId($id);
if (!$reclamacion) {
    header("Location: /pfc/vistas/admin/reclamaciones/verReclamaciones.php");
    exit;
}

$nombreReporta = $reclamacion['nombreProfesor'];
if (empty($nombreReporta)) {
    $nombreReporta = 'Sistema / Estudiante';
}
?>

<div class="encabezado-pagina">
    <div>
        <h1>Detalle de Reclamación #<?php echo $reclamacion['idReclamacion']; ?></h1>
        <p class="subtitulo-encabezado">Visualización de la queja o reporte</p>
    </div>
    <a href="/pfc/vistas/admin/reclamaciones/verReclamaciones.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label>Estudiante Implicado</label>
            <input type="text" value="<?php echo $reclamacion['nombreEstudiante']; ?>" readonly disabled>
        </div>

        <div class="campo-formulario">
            <label>Reportado por</label>
            <input type="text" value="<?php echo $nombreReporta; ?>" readonly disabled>
        </div>

        <div class="campo-formulario campo-ancho-total">
            <label>Asunto</label>
            <input type="text" value="<?php echo $reclamacion['asunto']; ?>" readonly disabled>
        </div>

        <div class="campo-formulario campo-ancho-total">
            <label>Descripción detallada</label>
            <textarea rows="5" readonly disabled><?php echo $reclamacion['descripcion']; ?></textarea>
        </div>

        <div class="campo-formulario">
            <label>Fecha</label>
            <input type="text" value="<?php echo date('d/m/Y', strtotime($reclamacion['fecha'])); ?>" readonly disabled>
        </div>

        <div class="campo-formulario">
            <label>Estado Actual</label>
            <input type="text" value="<?php echo ucfirst($reclamacion['estadoReclamacion']); ?>" readonly disabled>
        </div>
    </div>

    <div class="margen-arriba">
        <form action="/pfc/controladores/admin/reclamaciones/actualizar.php" method="POST" class="d-inline">
            <input type="hidden" name="idReclamacion" value="<?php echo $reclamacion['idReclamacion']; ?>">
            <?php if ($reclamacion['estadoReclamacion'] == 'pendiente') { ?>
                <input type="hidden" name="nuevo_estado" value="atendido">
                <button type="submit" class="boton-primario">
                    <i class="fas fa-check"></i> Marcar como Atendido
                </button>
            <?php } else { ?>
                <input type="hidden" name="nuevo_estado" value="pendiente">
                <button type="submit" class="boton-secundario">
                    <i class="fas fa-clock"></i> Reabrir (Marcar Pendiente)
                </button>
            <?php } ?>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
