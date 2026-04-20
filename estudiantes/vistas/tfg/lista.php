<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";

$id = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($id);

$tituloDelPagina = "Mi TFG - Portal Estudiantes";
$seccionActual = 'tfg';
include_once "../comunes/nav.php";

$exito = null;
if (isset($_SESSION['exito'])) {
    $exito = $_SESSION['exito'];
}

$error = null;
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['exito'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>Mi Trabajo Fin de Grado (TFG)</h1>
</div>

<?php if ($error) { ?>
<div class="mensaje-error">
    <i class="fas fa-exclamation-circle"></i>
    <p><?php echo $error; ?></p>
</div>
<?php } ?>

<?php if ($exito) { ?>
<div class="mensaje-exito">
    <i class="fas fa-check-circle"></i>
    <p><?php echo $exito; ?></p>
</div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Estado de tu TFG</h3>
    </div>
    
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Archivo del TFG</label>
            <div class="mt-5">
                <?php if ($tfg['archivoTFG']) { ?>
                    <a href="/pfc/admin/uploads/tfg/<?php echo $tfg['archivoTFG']; ?>" target="_blank" class="boton-secundario">
                        <i class="fas fa-download"></i> Descargar TFG (PDF)
                    </a>
                    <p class="texto-pequeno texto-atenuado mt-5">Archivo: <?php echo $tfg['archivoTFG']; ?></p>
                <?php } else { ?>
                    <p class="texto-atenuado">No se ha subido ningún archivo todavía.</p>
                <?php } ?>
            </div>
        </div>

        <div class="campo-formulario">
            <label class="texto-atenuado texto-pequeno">Subir / Cambiar TFG (Solo PDF)</label>
            <form action="../../controladores/tfg/subir.php" method="POST" enctype="multipart/form-data" class="disposicion-flexible alinear-centro separacion-pequena mt-5">
                <input type="hidden" name="idEstudiante" value="<?php echo $id; ?>">
                <input type="file" name="archivoTFG" accept=".pdf" required>
                <button type="submit" name="subirTFG" class="boton-primario">
                    <i class="fas fa-upload"></i> Subir
                </button>
            </form>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>