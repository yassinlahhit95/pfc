<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    $_SESSION['errores'] = strtoupper("MENSAJE NO ENCONTRADO.");
    header("Location: lista.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
$profeActual = obtenerProfesorPorId($_SESSION['idProfesor']);
$nombreProfeParaVista = $profeActual['nombreProfesor'] ?? 'Profesor';

if (!$mensaje['leido'] && $mensaje['emisor_rol'] != 'profesor' && $mensaje['idProfesor'] == $_SESSION['idProfesor']) {
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

<div class="panel">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> INFORMACION DEL MENSAJE</h3>
    </div>
    
    <div class="fila-datos">
        <div class="nombre-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'estudiante') ? $mensaje['nombreEstudiante'] : $nombreProfeParaVista . ' (Profesor)' ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?= ($mensaje['emisor_rol'] == 'profesor') ? ($mensaje['nombreEstudiante'] ?: 'Direccion') : $nombreProfeParaVista . ' (Profesor)' ?>
        </div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Fecha</div>
        <div class="valor-detalle"><?= date('d/m/Y H:i', strtotime($mensaje['fecha'])) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?= strtoupper($mensaje['asunto']) ?></div>
    </div>

    <div class="fila-datos">
        <div class="nombre-detalle">Estado Actual</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="indicador-estado activo-verde">LEIDO / VISTO</span>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">PENDIENTE / NUEVO</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba bg-gris-suave ancho-total" style="padding: 20px; border-radius: 8px; word-break: break-word;">
        <label for="contenidoMensaje" class="texto-suave texto-pequeno" style="display: block; margin-bottom: 10px;">CONTENIDO DEL MENSAJE:</label>
        <div id="contenidoMensaje" style="max-width: 100%; line-height: 1.6; white-space: pre-wrap;"><?= $mensaje['descripcion'] ?? '' ?></div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

