<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../../index.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = strtoupper("ID DE CALIFICACIÓN NO VÁLIDO.");
    header("Location: lista.php");
    exit;
}

$nota = obtenerCalificacionPorId($id);

if (!$nota) {
    $_SESSION['error'] = strtoupper("NO SE ENCONTRÓ LA CALIFICACIÓN SOLICITADA.");
    header("Location: lista.php");
    exit;
}

$estudiantes = listarEstudiantes();
$modulos = listarModulos();

$tituloDelPagina = "Editar Nota - Portal Profesores";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Calificación</h1>
    <a href="lista.php" class="boton-secundario">? Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/calificaciones/actualizar.php" method="POST">
        <input type="hidden" name="idCalificacion" value="<?= $id ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante" disabled>
                    <?php foreach ($estudiantes as $est) { ?>
                        <option value="<?= $est['idEstudiante'] ?>" <?= $est['idEstudiante'] == $nota['idEstudiante'] ? 'selected' : '' ?>><?= $est['nombreEstudiante'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idEstudiante" value="<?= $nota['idEstudiante'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>Módulo *</label>
                <select name="idModulo" disabled>
                    <?php foreach ($modulos as $mod) { ?>
                        <option value="<?= $mod['idModulo'] ?>" <?= $mod['idModulo'] == $nota['idModulo'] ? 'selected' : '' ?>><?= $mod['nombreModulo'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idModulo" value="<?= $nota['idModulo'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>1ª Evaluación</label>
                <input type="text" name="nota_1ev" value="<?= $nota['nota_1ev'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>1ª Final</label>
                <input type="text" name="nota_1final" value="<?= $nota['nota_1final'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>2ª Evaluación</label>
                <input type="text" name="nota_2ev" value="<?= $nota['nota_2ev'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>2ª Final</label>
                <input type="text" name="nota_2final" value="<?= $nota['nota_2final'] ?? '' ?>">
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible alinear-centro">
            <button type="submit" name="actualizarNota" class="boton-primario">Actualizar Calificación</button>
            <label class="etiqueta-notificacion">
                <input type="checkbox" name="notificarEstudiante" value="1"> Notificar por Email
            </label>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


