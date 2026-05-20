<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    $_SESSION['errores'] = strtoupper("ID DE CALIFICACIÓN NO VÁLIDO.");
    header("Location: lista.php");
    exit;
}

$nota = obtenerCalificacionPorId($id);

if (!$nota) {
    $_SESSION['errores'] = strtoupper("NO SE ENCONTRÁ LA CALIFICACIÓN SOLICITADA.");
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
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
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
                
            </div>

            <div class="campo">
                <label for="nota_1final">1ª Final</label>
                
            </div>

            <div class="campo">
                <label for="nota_2ev">2ª Evaluación</label>
                
            </div>

            <div class="campo">
                <label for="nota_2final">2ª Final</label>
                
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
