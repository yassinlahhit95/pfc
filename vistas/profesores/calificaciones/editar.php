<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../../index.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

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
    $_SESSION['error'] = strtoupper("NO SE ENCONTRÁ LA CALIFICACIÓN SOLICITADA.");
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
                <label>1 Evaluacin</label>
                <input type="text" name="nota_1ev" value="<?= $nota['nota_1ev'] ?? '' ?>" class="<?= isset($errores['nota_1ev']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_1ev'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_1ev'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>1 Final</label>
                <input type="text" name="nota_1final" value="<?= $nota['nota_1final'] ?? '' ?>" class="<?= isset($errores['nota_1final']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_1final'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_1final'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>2 Evaluacin</label>
                <input type="text" name="nota_2ev" value="<?= $nota['nota_2ev'] ?? '' ?>" class="<?= isset($errores['nota_2ev']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_2ev'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_2ev'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>2 Final</label>
                <input type="text" name="nota_2final" value="<?= $nota['nota_2final'] ?? '' ?>" class="<?= isset($errores['nota_2final']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_2final'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_2final'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible alinear-centro separacion-media">
            <button type="submit" name="actualizarNota" class="boton-primario">Actualizar Calificacin</button>
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
            <label class="etiqueta-notificacion">
                <input type="checkbox" name="notificarEstudiante" value="1"> Notificar por Email
            </label>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>



