<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || ($mensaje['idProfesor'] != $_SESSION['idProfesor'] && $mensaje['emisor_rol'] != 'profesor')) {
    $_SESSION['error'] = strtoupper("MENSAJE NO ENCONTRADO O ACCESO DENEGADO.");
    header("Location: /pfc/vistas/profesores/mensajes/lista.php");
    exit;
}

$tituloDelPagina = "Detalles del Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="/pfc/vistas/profesores/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-envelope-open-text"></i> INFORMACIÓN DEL MENSAJE</h3>
    </div>
    
    <div class="fila-detalle">
        <div class="etiqueta-detalle">De</div>
        <div class="valor-detalle texto-negrita">
            <?php echo ($mensaje['emisor_rol'] == 'estudiante') ? $mensaje['nombreEstudiante'] : 'Tú (Profesor)'; ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Para</div>
        <div class="valor-detalle texto-negrita">
            <?php echo ($mensaje['emisor_rol'] == 'profesor') ? ($mensaje['nombreEstudiante'] ?: 'Dirección') : 'Tú (Profesor)'; ?>
        </div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Fecha</div>
        <div class="valor-detalle"><?php echo date('d/m/Y H:i', strtotime($mensaje['fecha'])); ?></div>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Asunto</div>
        <div class="valor-detalle color-primario texto-negrita"><?php echo strtoupper($mensaje['asunto']); ?></div>
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

    <div class="margen-arriba p-20 bg-gris-suave rounded-8">
        <label class="texto-atenuado texto-pequeno d-block mb-10">CONTENIDO DEL MENSAJE:</label>
        <div class="line-height-16 pre-wrap"><?php echo $mensaje['descripcion']; ?></div>
    </div>

    <?php if (!empty($mensaje['respuesta'])) { ?>
        <div class="margen-arriba p-20 bg-secundario text-white rounded-8">
            <label class="text-white texto-pequeno d-block mb-10">TU RESPUESTA:</label>
            <div class="line-height-16 pre-wrap"><?php echo $mensaje['respuesta']; ?></div>
        </div>
    <?php } ?>

    <?php if (!$mensaje['leido'] && $mensaje['emisor_rol'] == 'estudiante') { ?>
        <div class="margen-arriba-grande pt-20" style="border-top: 1px solid #e2e8f0;">
            <div class="titulo-tarjeta"><h3><i class="fas fa-reply"></i> RESPONDER MENSAJE</h3></div>
            <form action="/pfc/controladores/profesores/mensajes/responder.php" method="POST" class="form-estandar">
                <input type="hidden" name="idReclamacion" value="<?php echo $idReclamacion; ?>">
                <div class="campo-formulario">
                    <label>Tu Respuesta (Máx 250 caracteres)</label>
                    <textarea name="respuesta" rows="4" maxlength="250" placeholder="Escribe aquí tu respuesta..."></textarea>
                </div>
                <div class="form-acciones">
                    <button type="submit" name="enviarRespuesta" class="boton-primario">
                        <i class="fas fa-paper-plane"></i> ENVIAR RESPUESTA Y MARCAR VISTO
                    </button>
                    <form action="/pfc/controladores/profesores/mensajes/marcar_visto.php" method="POST" style="display:inline;">
                         <input type="hidden" name="idReclamacion" value="<?php echo $idReclamacion; ?>">
                         <button type="submit" name="marcarVisto" class="boton-secundario">
                             <i class="fas fa-check"></i> SOLO MARCAR VISTO
                         </button>
                    </form>
                </div>
            </form>
        </div>
    <?php } ?>
</div>

<?php include '../comunes/footer.php'; ?>
