<?php
session_start();

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_GET['idEstudiante'] ?? 0;
$idEstudiante = intval($idEstudiante);

$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    $_SESSION['error'] = "ESTUDIANTE NO ENCONTRADO.";
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | DETALLES DEL ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>FICHA DE ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-user-graduate"></i> INFORMACIÓN PERSONAL</h3>
    </div>
    
    <div class="fila-dat">
        <div class="etiqueta-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= strtoupper($estudiante['nombreEstudiante']) ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Teléfono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciclo Formativo</div>
        <div class="valor-detalle">
            <span class="bolita activo-verde"><?= $estudiante['nombreCiclo'] ?></span>
        </div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Ciudad / Dirección</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?= !empty($estudiante['observacionesEstudiante']) ? nl2br($estudiante['observacionesEstudiante']) : '<span class="atenuado">Sin observaciones registradas.</span>' ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-file-pdf"></i> SITUACIÓN DEL TFG</h3>
    </div>
    <div class="d-flex alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="bolita activo-verde">ENTREGADO</span>
                <p class="texto-pequeno atenuado" style="margin-top: 5px;">Subido el: <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?></p>
            <?php } else { ?>
                <span class="bolita inactivo-rojo">PENDIENTE / NO SUBIDO</span>
            <?php } ?>
        </div>
        
        <?php 
        require_once __DIR__ . "/../../../modelos/tfg.php";
        $notaTFG = obtenerCalificacionTFG($idEstudiante);
        if ($notaTFG) {
        ?>
            <div style="text-align: right;">
                <p class="etiqueta-detalle" style="margin-bottom: 5px;">CALIFICACIÓN TFG</p>
                <span class="texto-negrita <?= $notaTFG['nota'] >= 5 ? 'texto-verde' : 'texto-rojo' ?>" style="font-size: 1.5em;">
                    <?= $notaTFG['nota'] ?> / 10
                </span>
            </div>
        <?php } ?>
    </div>
    
    <?php if ($notaTFG && !empty($notaTFG['observaciones'])) { ?>
        <div class="margen-arriba tarjeta-gris-suave" style="padding: 15px;">
            <p class="texto-negrita" style="font-size: 13px; color: #718096; margin-bottom: 5px;">FEEDBACK DEL TFG:</p>
            <p class="texto-pequeno"><?= nl2br($notaTFG['observaciones']) ?></p>
        </div>
    <?php } ?>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

