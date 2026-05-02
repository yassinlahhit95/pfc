<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = $_GET['id'] ?? 0;
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: ../../../vistas/profesores/mensajes/lista.php");
    exit;
}

$tituloDelPagina = "Gestionar Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="/pfc/vistas/profesores/mensajes/lista.php" class="boton-secundario">â† Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-atenuado">Enviado por:</p>
            <h3><?php echo $mensaje['nombreEstudiante']; ?></h3>
        </div>
        <div>
            <p class="texto-atenuado">Fecha:</p>
            <p class="texto-negrita"><?php echo date('d/m/Y', strtotime($mensaje['fecha'])); ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <p class="texto-negrita"><?php echo $mensaje['asunto']; ?></p>
        <hr class="mt-5 margen-abajo">
        <p><?php echo nl2br($mensaje['descripcion']); ?></p>
    </div>

    <form action="/pfc/controladores/profesores/mensajes/actualizar.php" method="POST">
        <input type="hidden" name="idReclamacion" value="<?php echo $idReclamacion; ?>">
        
        <div class="campo-formulario">
            <label>Tu Respuesta / ExplicaciÃ³n:</label>
            <textarea name="respuesta" rows="4" placeholder="Escribe aquÃ­ tu respuesta..."><?php echo $mensaje['respuesta']; ?></textarea>
        </div>

        <div class="disposicion-flexible separacion-grande margen-arriba">
            <button type="submit" name="guardarRespuesta" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Respuesta
            </button>
            <button type="submit" name="marcarLeido" class="boton-secundario">
                <i class="fas fa-check"></i> Solo marcar como LeÃ­do
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
