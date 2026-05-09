<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || $mensaje['idEstudiante'] != $_SESSION['idEstudiante']) {
    $_SESSION['error'] = strtoupper("MENSAJE NO ENCONTRADO O ACCESO DENEGADO.");
    header("Location: lista.php");
    exit;
}

// Marcar como leído automáticamente SOLO si el que abre el mensaje es el receptor (no el emisor)
if (!$mensaje['leido'] && $mensaje['emisor_rol'] != 'estudiante' && $mensaje['idEstudiante'] == $_SESSION['idEstudiante']) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$tituloDelPagina = "AULAPRO | DETALLES DEL MENSAJE";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>DETALLES DEL MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> Información del Mensaje</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'profesor') ? $mensaje['nombreProfesor'] : 'Administración (Sistema)' ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Enviado el</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?= strtoupper($mensaje['asunto']) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Estado</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="estado-bolita activo-verde">VISTO</span>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">NUEVO / SIN LEER</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba p-20 bg-gris-suave rounded-8">
        <label class="texto-atenuado texto-pequeno d-block mb-10">CONTENIDO DEL MENSAJE:</label>
        <div class="line-height-16 pre-wrap"><?= $mensaje['descripcion'] ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




