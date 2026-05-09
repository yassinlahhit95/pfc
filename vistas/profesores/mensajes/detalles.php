<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    $_SESSION['error'] = strtoupper("MENSAJE NO ENCONTRADO.");
    header("Location: lista.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
$profeActual = obtenerProfesorPorId($_SESSION['idProfesor']);
$nombreProfeParaVista = $profeActual['nombreProfesor'] ?? 'Profesor';

// Marcar como leído automáticamente SOLO si el que abre el mensaje es el receptor (no el emisor)
if (!$mensaje['leido'] && $mensaje['emisor_rol'] == 'estudiante' && $mensaje['idProfesor'] == $_SESSION['idProfesor']) {
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

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> INFORMACIÓN DEL MENSAJE</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'estudiante') ? $mensaje['nombreEstudiante'] : $nombreProfeParaVista . ' (Profesor)' ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'profesor') ? ($mensaje['nombreEstudiante'] ?: 'Dirección') : $nombreProfeParaVista . ' (Profesor)' ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?= strtoupper($mensaje['asunto']) ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Estado Actual</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="estado-bolita activo-verde">LEÍDO / VISTO</span>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">PENDIENTE / NUEVO</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba p-20 bg-gris-suave rounded-8 break-word ancho-completo">
        <label for="contenidoMensaje" class="texto-atenuado texto-pequeno d-block mb-10">CONTENIDO DEL MENSAJE:</label>
        <div id="contenidoMensaje" class="line-height-16 pre-wrap max-ancho-completo"><?= $mensaje['descripcion'] ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>




