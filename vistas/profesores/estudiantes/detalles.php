<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['exito'], $_SESSION['errores']);

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = $_GET['idEstudiante'] ?? 0;
$idEstudiante = intval($idEstudiante);

$estudiante = obtenerEstudiantePorId($idEstudiante);

if (!$estudiante) {
    $_SESSION['errores'] = "ESTUDIANTE NO ENCONTRADO.";
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
        <h3><i class="fas fa-user-graduate"></i> INFORMACION PERSONAL</h3>
    </div>
    
    <div class="fila-datos">
        <div class="nombre-detalle">Nombre Completo</div>
        <div class="valor-detalle texto-negrita"><?= strtoupper($estudiante['nombreEstudiante']) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Email</div>
        <div class="valor-detalle"><?= $estudiante['emailEstudiante'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">DNI</div>
        <div class="valor-detalle"><?= $estudiante['dniEstudiante'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Telefono</div>
        <div class="valor-detalle"><?= $estudiante['telefonoEstudiante'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciclo Formativo</div>
        <div class="valor-detalle">
            <span class="indicador-estado activo-verde"><?= $estudiante['nombreCiclo'] ?></span>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha de Nacimiento</div>
        <div class="valor-detalle"><?= date('d/m/Y', strtotime($estudiante['fechaNacimientoEstudiante'])) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Ciudad / Direccion</div>
        <div class="valor-detalle"><?= $estudiante['direccionEstudiante'] . ", " . $estudiante['ciudadEstudiante'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Observaciones</div>
        <div class="valor-detalle">
            <?= !empty($estudiante['observacionesEstudiante']) ? nl2br($estudiante['observacionesEstudiante']) : '<span class="texto-suave">Sin observaciones registradas.</span>' ?>
        </div>
    </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-file-pdf"></i> SITUACION DEL TFG</h3>
    </div>
    <div class="caja alinear-centro espacio-entre-elementos">
        <div>
            <?php if (!empty($estudiante['archivoTFG'])) { ?>
                <span class="indicador-estado activo-verde">ENTREGADO</span>
                <p class="texto-pequeno texto-suave" style="margin-top: 5px;">Subido el: <?= date('d/m/Y H:i', strtotime($estudiante['fechaSubidaTFG'])) ?></p>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">PENDIENTE / NO SUBIDO</span>
            <?php } ?>
        </div>
        
        <?php
        $notaTFG = obtenerCalificacionTFG($idEstudiante);
        if ($notaTFG) {
        ?>
            <div style="text-align: right;">
                <p class="nombre-detalle" style="margin-bottom: 5px;">CALIFICACIUN TFG</p>
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

