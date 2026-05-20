<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || $mensaje['idEstudiante'] != $_SESSION['idEstudiante']) {
    $_SESSION['errores'] = strtoupper("MENSAJE NO ENCONTRADO O ACCESO DENEGADO.");
    header("Location: lista.php");
    exit;
}

if (!$mensaje['leido'] && $mensaje['emisor_rol'] != 'estudiante' && $mensaje['idEstudiante'] == $_SESSION['idEstudiante']) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$tituloDelPagina = "AULAPRO | DETALLES DEL MENSAJE";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>DETALLES DEL MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> Informacion del Mensaje</h3>
    </div>
    
    <div class="fila-datos">
        <div class="nombre-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'profesor') ? $mensaje['nombreProfesor'] : 'Administracion (Sistema)' ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Enviado el</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?= strtoupper($mensaje['asunto']) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Contenido</div>
        <div class="valor-detalle valor-mensaje"><?= $mensaje['descripcion'] ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Estado</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="indicador-estado activo-verde">VISTO</span>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">NUEVO / SIN LEER</span>
            <?php } ?>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

