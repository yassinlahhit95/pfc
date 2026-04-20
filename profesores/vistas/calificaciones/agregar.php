<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idProfesor = $_SESSION['idProfesor'];

// Filtramos solo los estudiantes y módulos que pertenecen a los ciclos del profesor
$estudiantes = listarEstudiantesPorProfesor($idProfesor);
$modulos = listarModulosPorProfesor($idProfesor);

$tituloDelPagina = "Asignar Nota - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Asignar Nueva Nota</h1>
    <a href="/pfc/profesores/vistas/calificaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/calificaciones/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante" required>
                    <?php foreach ($estudiantes as $est) { ?>
                        <option value="<?php echo $est['idEstudiante']; ?>"><?php echo $est['nombreEstudiante']; ?> (<?php echo $est['nombreCiclo']; ?>)</option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Módulo *</label>
                <select name="idModulo" required>
                    <?php foreach ($modulos as $mod) { ?>
                        <option value="<?php echo $mod['idModulo']; ?>"><?php echo $mod['nombreModulo']; ?> (<?php echo $mod['nombreCiclo']; ?>)</option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>1ª Evaluación</label>
                <input type="text" name="nota_1ev" value="0">
            </div>

            <div class="campo-formulario">
                <label>1ª Final</label>
                <input type="text" name="nota_1final" value="0">
            </div>

            <div class="campo-formulario">
                <label>2ª Evaluación</label>
                <input type="text" name="nota_2ev" value="0">
            </div>

            <div class="campo-formulario">
                <label>2ª Final</label>
                <input type="text" name="nota_2final" value="0">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="insertarNota" class="boton-primario">Guardar Calificación</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>