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
    <div class="disposicion-flexible espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-atenuado">De:</p>
            <p class="texto-negrita"><?php echo $mensaje['nombreEstudiante'] ?: 'Administración (Sistema)'; ?></p>
        </div>
        <div>
            <p class="texto-atenuado">Para:</p>
            <p class="texto-negrita"><?php echo $mensaje['nombreProfesor'] ?: 'Dirección (Admin)'; ?></p>
        </div>
        <div>
            <p class="texto-atenuado">Fecha:</p>
            <p class="texto-negrita"><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <h3 class="margen-abajo"><?php echo $mensaje['asunto']; ?></h3>
        <p><?php echo nl2br($mensaje['descripcion']); ?></p>
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