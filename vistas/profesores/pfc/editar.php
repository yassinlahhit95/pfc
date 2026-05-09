<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

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

<div class="encabezado-pagina">
    <h1>EDITAR DATOS TFG</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/pfc/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudiante ?>">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="nombreEstudiante">Estudiante</label>
                <input type="text" id="nombreEstudiante" value="<?= $datosTFG['nombreEstudiante'] ?? '' ?>" disabled>
            </div>

            <div class="campo-formulario">
                <label for="tituloTFG">Título del TFG *</label>
                <input type="text" id="tituloTFG" name="tituloTFG" value="<?= $datosTFG['tituloTFG'] ?? '' ?>" class="<?= isset($errores['tituloTFG']) ? 'input-error' : '' ?>">
                <?php if (isset($errores['tituloTFG'])) { ?>
                    <strong class="error-campo"><?= $errores['tituloTFG'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarTFG" class="boton-primario">
                <i class="fas fa-save"></i> ACTUALIZAR TFG
            </button>
            <button type="reset" class="boton-secundario">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>




