<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = $_GET['id'] ?? '';
$datosTFG = obtenerTFGporEstudiante($idEstudiante);

$tituloPagina = "Editar TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR DATOS TFG</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/pfc/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">
        <div class="form-cols">
            <div class="campo">
                <label for="nombreEstudiante">Estudiante</label>
                <input type="text" id="nombreEstudiante" value="<?= $datosTFG['nombreEstudiante'] ?? '' ?>" disabled>
            </div>

            <div class="campo">
                <label for="tituloTFG">Título del TFG</label>
                <input type="text" id="tituloTFG" name="tituloTFG" value="<?= $datosTFG['tituloTFG'] ?? '' ?>">
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarTFG" class="boton-primario" value="ACTUALIZAR TFG">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

