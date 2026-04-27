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

$titulo_pagina = "Detalle del Mensaje - Super Admin";
$seccion = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Gestión de Mensaje</h1>
    <a href="/pfc/vistas/admin/mensajes/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="disposicion-flexible separacion-grande">
    <div class="flexible-rellenar">
        <div class="tarjeta-blanca">
            <div class="titulo-tarjeta">
                <h3><i class="fas fa-envelope"></i> Detalles del Mensaje</h3>
            </div>
            
            <div class="fila-detalle">
                <div class="etiqueta-detalle">De</div>
                <div class="valor-detalle texto-negrita"><?php echo $mensaje['nombreEstudiante'] ?: 'Administración (Sistema)'; ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">Para</div>
                <div class="valor-detalle texto-negrita"><?php echo $mensaje['nombreProfesor'] ?: 'Dirección (Admin)'; ?></div>
            </div>

            <div class="fila-detalle">
                <div class="etiqueta-detalle">Fecha</div>
                <div class="valor-detalle"><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></div>
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

            <div class="margen-arriba">
                <div class="tarjeta-gris-suave p-20">
                    <h4 class="mb-10 text-uppercase"><?php echo $mensaje['asunto']; ?></h4>
                    <p class="font-size-11 line-height-15"><?php echo nl2br($mensaje['descripcion']); ?></p>
                </div>
            </div>

    <form action="/pfc/controladores/admin/mensajes/actualizar.php" method="POST">
        <input type="hidden" name="idReclamacion" value="<?php echo $idReclamacion; ?>">
        
        <div class="campo-formulario">
            <label>Escribir Respuesta o Nota Administrativa:</label>
            <textarea name="respuesta" rows="5" placeholder="Escribe aquí para informar al estudiante o profesor..."><?php echo $mensaje['respuesta']; ?></textarea>
        </div>

        <div class="disposicion-flexible separacion-grande margen-arriba">
            <button type="submit" name="guardarCambios" class="boton-primario">
                <i class="fas fa-save"></i> Guardar y Enviar Respuesta
            </button>
            <?php if (!$mensaje['leido']) { ?>
                <button type="submit" name="marcarLeido" class="boton-secundario">
                    <i class="fas fa-check-double"></i> Confirmar Lectura (Marcar Leído)
                </button>
            <?php } else { ?>
                <span class="estado-bolita activo-verde"><i class="fas fa-check-double"></i> Este mensaje ya fue marcado como leído</span>
            <?php } ?>
        </div>
    </form>
</div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
