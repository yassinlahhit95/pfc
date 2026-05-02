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
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "Gestionar Mensaje - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Detalles del Mensaje</h1>
    <a href="lista.php" class="boton-secundario">â† Volver</a>
</div>

<div class="tarjeta-blanca">
    <div class="disposicion-flexible espacio-entre-elementos margen-abajo">
        <div>
            <p class="texto-atenuado">Enviado por:</p>
            <h3><?= $mensaje['nombreEstudiante'] ?></h3>
        </div>
        <div>
            <p class="texto-atenuado">Fecha:</p>
            <p class="texto-negrita"><?= date('d/m/Y', strtotime($mensaje['fecha'])) ?></p>
        </div>
    </div>

    <div class="tarjeta-gris-suave margen-abajo">
        <p class="texto-negrita"><?= $mensaje['asunto'] ?></p>
        <hr class="mt-5 margen-abajo">
        <p><?= nl2br($mensaje['descripcion']) ?></p>
    </div>

    <form action="../../../controladores/profesores/mensajes/actualizar.php" method="POST">
        <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
        
        <div class="campo-formulario">
            <label>Tu Respuesta / ExplicaciÃ³n:</label>
            <textarea name="respuesta" rows="4" placeholder="Escribe aquÃ­ tu respuesta..."><?= $mensaje['respuesta'] ?></textarea>
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
