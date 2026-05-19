<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';

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

$tituloDelPagina = "AULAPRO | EDITAR NOTA";
$seccionActual = 'calificaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR CALIFICACIÓN</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/calificaciones/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="idCalificacion" value="<?= $id ?>">
        <div class="form-cols">
            <div class="campo">
                <label for="idEstudiante">Estudiante</label>
                <select name="idEstudiante" id="idEstudiante" disabled>
                    <?php foreach ($estudiantes as $est) { ?>
                        <option value="<?= $est['idEstudiante'] ?>" <?= $est['idEstudiante'] == $nota['idEstudiante'] ? 'selected' : '' ?>><?= $est['nombreEstudiante'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idEstudiante" value="<?= $nota['idEstudiante'] ?? '' ?>">
            </div>

            <div class="campo">
                <label for="idModulo">Módulo</label>
                <select name="idModulo" id="idModulo" disabled>
                    <?php foreach ($modulos as $mod) { ?>
                        <option value="<?= $mod['idModulo'] ?>" <?= $mod['idModulo'] == $nota['idModulo'] ? 'selected' : '' ?>><?= $mod['nombreModulo'] ?></option>
                    <?php } ?>
                </select>
                <input type="hidden" name="idModulo" value="<?= $nota['idModulo'] ?? '' ?>">
            </div>

            <div class="campo">
                <label for="nota_1ev">1ª Evaluación</label>
                <input type="text" name="nota_1ev" id="nota_1ev" value="<?= $nota['nota_1ev'] ?? '' ?>" class="<?= isset($errores['nota_1ev']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_1ev'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_1ev'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="nota_1final">1ª Final</label>
                <input type="text" name="nota_1final" id="nota_1final" value="<?= $nota['nota_1final'] ?? '' ?>" class="<?= isset($errores['nota_1final']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_1final'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_1final'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="nota_2ev">2ª Evaluación</label>
                <input type="text" name="nota_2ev" id="nota_2ev" value="<?= $nota['nota_2ev'] ?? '' ?>" class="<?= isset($errores['nota_2ev']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_2ev'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_2ev'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="nota_2final">2ª Final</label>
                <input type="text" name="nota_2final" id="nota_2final" value="<?= $nota['nota_2final'] ?? '' ?>" class="<?= isset($errores['nota_2final']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['nota_2final'])) { ?>
                    <strong class="error-campo"><?= $errores['nota_2final'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarNota" class="boton-primario" value="GUARDAR CAMBIOS">
            <button type="button" class="boton-secundario" onclick="window.location.reload();">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
            <label class="texto-aviso ml-auto">
                <input type="checkbox" name="notificarEstudiante" value="1"> 
                <i class="fas fa-envelope"></i> Notificar por Email
            </label>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
