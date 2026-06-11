<?php
require_once __DIR__ . "/../../../include/Security.php";

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: lista.php");
    exit;
}

if (!$mensaje['leido'] && $mensaje['emisor_rol'] != 'admin' && (($mensaje['emisor_rol'] == 'estudiante' && $mensaje['idProfesor'] === NULL) || ($mensaje['emisor_rol'] == 'profesor' && $mensaje['idEstudiante'] === NULL))) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$titulo_pagina = "AULAPRO | DETALLE DEL MENSAJE";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>DETALLES DEL MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope"></i> Información del Mensaje</h3>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?php if ($mensaje['emisor_rol'] == 'admin') { ?>
                Administración
            <?php } elseif ($mensaje['emisor_rol'] == 'profesor') { ?>
                <?= $mensaje['nombreProfesor'] ?> (Profesor)
            <?php } else { ?>
                <?= $mensaje['nombreEstudiante'] ?> (Estudiante)
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?php if ($mensaje['emisor_rol'] == 'admin') { ?>
                <?php if ($mensaje['idEstudiante'] > 0) { ?>
                    <?= $mensaje['nombreEstudiante'] ?> (Estudiante)
                <?php } elseif ($mensaje['idProfesor'] > 0) { ?>
                    <?= $mensaje['nombreProfesor'] ?> (Profesor)
                <?php } else { ?>
                    General
                <?php } ?>
            <?php } else { ?>
                Administración
            <?php } ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Estado</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="indicador-estado activo-verde">Leído</span>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">Pendiente</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba bg-gris-suave" style="padding: 20px;">
        <h4 class="color-primario" style="margin-bottom: 10px;"><?= $mensaje['asunto'] ?? '' ?></h4>
        <div style="line-height: 1.5; white-space: pre-wrap;"><?= $mensaje['descripcion'] ?? '' ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




