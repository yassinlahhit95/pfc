<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idProfesor = $_SESSION['idProfesor'];
// Filtrar estudiantes por los ciclos asignados al profesor
$estudiantes = listarEstudiantesPorProfesor($idProfesor);

$tituloDelPagina = "Nueva Reclamación - Portal Profesores";
$seccionActual = 'reclamaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nueva Reclamación</h1>
    <a href="/pfc/profesores/vistas/reclamaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/reclamaciones/insertar.php" method="POST" class="disposicion-flexible direccion-columna separacion-grande">
        <input type="hidden" name="idProfesor" value="<?php echo $idProfesor; ?>">
        
        <div class="campo-formulario">
            <label>Estudiante *</label>
            <select name="idEstudiante" required>
                <?php foreach ($estudiantes as $est) { ?>
                    <option value="<?php echo $est['idEstudiante']; ?>"><?php echo $est['nombreEstudiante']; ?> (<?php echo $est['nombreCiclo']; ?>)</option>
                <?php } ?>
            </select>
        </div>

        <div class="campo-formulario">
            <label>Asunto *</label>
            <input type="text" name="asunto" required placeholder="Ej: Comportamiento inapropiado">
        </div>

        <div class="campo-formulario">
            <label>Gravedad *</label>
            <select name="gravedad" required>
                <option value="leve">Leve</option>
                <option value="grave">Grave</option>
                <option value="muy grave">Muy Grave</option>
            </select>
        </div>

        <div class="campo-formulario">
            <label>Fecha *</label>
            <input type="date" name="fecha" value="<?php echo date('Y-m-d'); ?>" required>
        </div>

        <div class="campo-formulario">
            <label>Descripción detallada *</label>
            <textarea name="descripcion" rows="5" required placeholder="Explica aquí el motivo de la reclamación..."></textarea>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarReclamacion" class="boton-primario">Enviar Reclamación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>