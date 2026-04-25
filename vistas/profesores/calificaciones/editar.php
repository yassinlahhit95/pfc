<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id = $_GET['id'];
$nota = obtenerCalificacionPorId($id);
$estudiantes = listarEstudiantes();
$modulos = listarModulos();

$tituloDelPagina = "Editar Nota - Portal Profesores";
$seccionActual = 'calificaciones';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Editar Calificación</h1>
    <a href="/pfc/vistas/profesores/calificaciones/lista.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/profesores/calificaciones/actualizar.php" method="POST">
        <input type="hidden" name="idCalificacion" value="<?php echo $id; ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante" disabled>
                    <?php foreach ($estudiantes as $est) { ?>
                        <option value="<?php echo $est['idEstudiante']; ?>" <?php if ($est['idEstudiante'] == $nota['idEstudiante']) { echo 'selected'; } ?>><?php echo $est['nombreEstudiante']; ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idEstudiante" value="<?php echo $nota['idEstudiante']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Módulo *</label>
                <select name="idModulo" disabled>
                    <?php foreach ($modulos as $mod) { ?>
                        <option value="<?php echo $mod['idModulo']; ?>" <?php if ($mod['idModulo'] == $nota['idModulo']) { echo 'selected'; } ?>><?php echo $mod['nombreModulo']; ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idModulo" value="<?php echo $nota['idModulo']; ?>">
            </div>

            <div class="campo-formulario">
                <label>1ª Evaluación</label>
                <input type="text" name="nota_1ev" value="<?php echo $nota['nota_1ev']; ?>">
            </div>

            <div class="campo-formulario">
                <label>1ª Final</label>
                <input type="text" name="nota_1final" value="<?php echo $nota['nota_1final']; ?>">
            </div>

            <div class="campo-formulario">
                <label>2ª Evaluación</label>
                <input type="text" name="nota_2ev" value="<?php echo $nota['nota_2ev']; ?>">
            </div>

            <div class="campo-formulario">
                <label>2ª Final</label>
                <input type="text" name="nota_2final" value="<?php echo $nota['nota_2final']; ?>">
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible alinear-centro">
            <button type="submit" name="actualizarNota" class="boton-primario">Actualizar Calificación</button>
            <label style="margin-left: 20px; font-weight: bold; color: #3498db; cursor: pointer;">
                <input type="checkbox" name="notificarEstudiante" value="1"> Notificar por Email
            </label>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
