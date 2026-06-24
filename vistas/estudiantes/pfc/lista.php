<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MI TFG";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";

?>

<div class="cabecera">
    <h1>MI TRABAJO FIN DE GRADO (TFG)</h1>
</div>


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
                        <a href="../../../public/uploads/pfc/<?= Security::escapeHtml($tfg['archivoTFG'] ) ?>" target="_blank" class="boton-secundario">
                            <i class="fas fa-download"></i> Descargar TFG (PDF)
                        </a>
                        <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                            <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">
                            <input type="submit" name="borrarTFG" class="boton-icono rojo" value="Borrar entrega">
                        </form>
                    </div>
                    <p class="texto-pequeno texto-suave" style="margin-top: 10px;">
                        <b>Archivo:</b> <?= Security::escapeHtml($tfg['archivoTFG'] ) ?><br>
                        <b>Subido el:</b> <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG']))) ?>
                    </p>
                <?php } else { ?>
                    <p class="texto-suave">No se ha subido ningun archivo todavia.</p>
                <?php } ?>
            </div>
        </div>

        <div class="campo">
            <label class="texto-suave texto-pequeno"><?= Security::escapeHtml(empty($tfg['archivoTFG']) ? 'Subir TFG (Solo PDF)' : 'Actualizar TFG (Reemplaza el anterior)') ?></label>
            <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data" class="caja alinear-centro espacio-pequeno" style="margin-top: 5px;">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">
                <input type="file" name="archivoTFG" accept=".pdf">
                <input type="submit" name="subirTFG" class="boton-primario" value="<?= Security::escapeHtml(empty($tfg['archivoTFG']) ? 'ENVIAR' : 'ACTUALIZAR') ?>">
            </form>
        </div>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>



