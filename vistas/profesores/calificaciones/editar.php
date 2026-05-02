<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = strtoupper("ID DE CALIFICACIÃ“N NO VÃLIDO.");
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

$nota = obtenerCalificacionPorId($id);

if (!$nota) {
    $_SESSION['error'] = strtoupper("NO SE ENCONTRÃ“ LA CALIFICACIÃ“N SOLICITADA.");
    header("Location: ../../../vistas/profesores/calificaciones/lista.php");
    exit;
}

$estudiantes = listarEstudiantes();
$modulos = listarModulos();

$tituloDelPagina = "Editar Nota - Portal Profesores";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar CalificaciÃ³n</h1>
    <a href="/pfc/vistas/profesores/calificaciones/lista.php" class="boton-secundario">â† Volver</a>
</div>

<?php if (isset($_SESSION['error'])) { ?>
    <div class="mensaje-error"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php } ?>

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
                <label>MÃ³dulo *</label>
                <select name="idModulo" disabled>
                    <?php foreach ($modulos as $mod) { ?>
                        <option value="<?php echo $mod['idModulo']; ?>" <?php if ($mod['idModulo'] == $nota['idModulo']) { echo 'selected'; } ?>><?php echo $mod['nombreModulo']; ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idModulo" value="<?php echo $nota['idModulo']; ?>">
            </div>

            <div class="campo-formulario">
                <label>1Âª EvaluaciÃ³n</label>
                <input type="text" name="nota_1ev" value="<?php echo $nota['nota_1ev']; ?>">
            </div>

            <div class="campo-formulario">
                <label>1Âª Final</label>
                <input type="text" name="nota_1final" value="<?php echo $nota['nota_1final']; ?>">
            </div>

            <div class="campo-formulario">
                <label>2Âª EvaluaciÃ³n</label>
                <input type="text" name="nota_2ev" value="<?php echo $nota['nota_2ev']; ?>">
            </div>

            <div class="campo-formulario">
                <label>2Âª Final</label>
                <input type="text" name="nota_2final" value="<?php echo $nota['nota_2final']; ?>">
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible alinear-centro">
            <button type="submit" name="actualizarNota" class="boton-primario">Actualizar CalificaciÃ³n</button>
            <label class="etiqueta-notificacion">
                <input type="checkbox" name="notificarEstudiante" value="1"> Notificar por Email
            </label>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

