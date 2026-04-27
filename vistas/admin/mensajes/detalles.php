<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: /pfc/vistas/admin/mensajes/lista.php");
    exit;
}

// Marcar como leído automáticamente SOLO si el que abre el mensaje es el receptor (Administración)
if (!$mensaje['leido'] && $mensaje['emisor_rol'] != 'admin' && (($mensaje['emisor_rol'] == 'estudiante' && $mensaje['idProfesor'] === NULL) || ($mensaje['emisor_rol'] == 'profesor' && $mensaje['idEstudiante'] === NULL))) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$titulo_pagina = "Detalle del Mensaje - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="/pfc/vistas/admin/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope"></i> Información del Mensaje</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?php 
                if ($mensaje['emisor_rol'] == 'admin') echo 'Tú (Administración)';
                else if ($mensaje['emisor_rol'] == 'profesor') echo $mensaje['nombreProfesor'] . ' (Profesor)';
                else echo $mensaje['nombreEstudiante'] . ' (Estudiante)';
            ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?php 
                if ($mensaje['emisor_rol'] == 'admin') {
                    if ($mensaje['idEstudiante'] > 0) echo $mensaje['nombreEstudiante'] . ' (Estudiante)';
                    else if ($mensaje['idProfesor'] > 0) echo $mensaje['nombreProfesor'] . ' (Profesor)';
                    else echo 'General';
                } else {
                    echo 'Tú (Administración)';
                }
            ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha</div>
        <div class="valor-detalle"><?php echo date('d/m/Y H:i', strtotime($mensaje['fecha'])); ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Estado</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="estado-bolita activo-verde">Leído</span>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">Pendiente</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba p-20 bg-gris-suave rounded-8">
        <h4 class="mb-10 text-uppercase color-primario"><?php echo $mensaje['asunto']; ?></h4>
        <div class="line-height-15 pre-wrap"><?php echo $mensaje['descripcion']; ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
