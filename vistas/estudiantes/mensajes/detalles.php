<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || $mensaje['idEstudiante'] != $_SESSION['idEstudiante']) {
    $_SESSION['error'] = strtoupper("MENSAJE NO ENCONTRADO O ACCESO DENEGADO.");
    header("Location: /pfc/vistas/estudiantes/mensajes/lista.php");
    exit;
}

$tituloDelPagina = "Detalles del Mensaje - Portal Estudiantes";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> INFORMACIÓN DEL MENSAJE</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?php echo ($mensaje['idProfesor'] > 0) ? $mensaje['nombreProfesor'] : 'Dirección (Administración)'; ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Enviado el</div>
        <div class="valor-detalle"><?php echo date('d/m/Y H:i', strtotime($mensaje['fecha'])); ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?php echo strtoupper($mensaje['asunto']); ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Estado</div>
        <div class="valor-detalle">
            <?php if ($mensaje['leido']) { ?>
                <span class="estado-bolita activo-verde">LEÍDO POR EL DESTINATARIO</span>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">PENDIENTE DE LECTURA</span>
            <?php } ?>
        </div>
    </div>

    <div class="margen-arriba p-20 bg-gris-suave rounded-8">
        <label class="texto-atenuado texto-pequeno d-block mb-10">TU MENSAJE:</label>
        <div class="line-height-16 pre-wrap"><?php echo $mensaje['descripcion']; ?></div>
    </div>

    <?php if (!empty($mensaje['respuesta'])) { ?>
        <div class="margen-arriba p-20 bg-secundario text-white rounded-8">
            <label class="text-white texto-pequeno d-block mb-10">RESPUESTA RECIBIDA:</label>
            <div class="line-height-16 pre-wrap"><?php echo $mensaje['respuesta']; ?></div>
        </div>
    <?php } ?>

    <?php if (!$mensaje['leido'] && $mensaje['emisor_rol'] == 'profesor') { ?>
        <div class="form-acciones">
            <form action="/pfc/controladores/estudiantes/mensajes/marcar_visto.php" method="POST">
                <input type="hidden" name="idReclamacion" value="<?php echo $idReclamacion; ?>">
                <button type="submit" name="marcarVisto" class="boton-primario">
                    <i class="fas fa-check-double"></i> MARCAR COMO LEÍDO / VISTO
                </button>
            </form>
        </div>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>
