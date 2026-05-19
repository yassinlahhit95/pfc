<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$id = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($id);

$tituloDelPagina = "AULAPRO | MI TFG";
$seccionActual = 'tfg';
include_once "../comunes/nav.php";

$exito = '';
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="cabecera">
    <h1>MI TRABAJO FIN DE GRADO (TFG)</h1>
</div>

<?php if (!empty($error)) { ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<?php if (!empty($exito)) { ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Estado de tu TFG</h3>
    </div>
    
    <div class="form-cols">
        <div class="campo">
            <label class="texto-suave texto-pequeno">Archivo del TFG</label>
            <div style="margin-top: 5px;">
                <?php if (!empty($tfg['archivoTFG'])) { ?>
                    <div class="caja alinear-centro espacio-grande">
                        <a href="../../../public/uploads/pfc/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                            <i class="fas fa-download"></i> Descargar TFG (PDF)
                        </a>
                        <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST">
                            <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">
                            <input type="submit" name="borrarTFG" class="boton-icono rojo" value="Borrar entrega">
                        </form>
                    </div>
                    <p class="texto-pequeno texto-suave" style="margin-top: 10px;">
                        <b>Archivo:</b> <?php echo $tfg['archivoTFG']; ?><br>
                        <b>Subido el:</b> <?php echo date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])); ?>
                    </p>
                <?php } else { ?>
                    <p class="texto-suave">No se ha subido ningun archivo todavia.</p>
                <?php } ?>
            </div>
        </div>

        <div class="campo">
            <label class="texto-suave texto-pequeno"><?php echo empty($tfg['archivoTFG']) ? 'Subir TFG (Solo PDF)' : 'Actualizar TFG (Reemplaza el anterior)'; ?></label>
            <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data" class="caja alinear-centro espacio-pequeno" style="margin-top: 5px;">
                <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">
                <input type="file" name="archivoTFG" accept=".pdf">
                <input type="submit" name="subirTFG" class="boton-primario" value="<?= empty($tfg['archivoTFG']) ? 'ENVIAR' : 'ACTUALIZAR' ?>">
            </form>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



