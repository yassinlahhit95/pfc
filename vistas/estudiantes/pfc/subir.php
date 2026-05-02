<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($id);
$estudianteActual = obtenerEstudiantePorId($id);
$nombreLimpio = str_replace(' ', '_', $estudianteActual['nombreEstudiante']);
$timestampDescarga = date('d-m-Y_H-i-s');
$nombreDescarga = "TFG_" . $nombreLimpio . "_" . $timestampDescarga . ".pdf";

$tituloDelPagina = "Mi TFG - Portal Estudiantes";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MI TRABAJO FIN DE GRADO (TFG)</h1>
</div>

<?php if ($error) { ?>
    <div class="alerta-error">
        <i class="fas fa-exclamation-circle"></i> <?= $error ?>
    </div>
<?php } ?>

<?php if ($exito) { ?>
    <div class="alerta-exito">
        <i class="fas fa-check-circle"></i> <?= $exito ?>
    </div>
<?php } ?>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>ESTADO DE LA ENTREGA Y GESTIÓN</h3>
    </div>

    <div class="fila-detalle">
        <div class="etiqueta-detalle">Estado Actual</div>
        <div class="valor-detalle">
            <?php if (!empty($tfg['archivoTFG'])) { ?>
                <span class="estado-bolita activo-verde">ENTREGADO</span>
            <?php } else { ?>
                <span class="estado-bolita inactivo-rojo">NO ENTREGADO</span>
            <?php } ?>
        </div>
    </div>

    <?php if (!empty($tfg['archivoTFG'])) { ?>
        <div class="fila-detalle">
            <div class="etiqueta-detalle">Archivo subido</div>
            <div class="valor-detalle">
                <p class="texto-negrita"><?= $tfg['archivoTFG'] ?></p>
                <small class="texto-atenuado">Fecha de entrega: <?= date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])) ?></small>
            </div>
        </div>

        <div class="fila-detalle">
            <div class="etiqueta-detalle">Acciones del Archivo</div>
            <div class="valor-detalle">
                <div class="disposicion-flexible separacion-media">
                    <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="boton-secundario" download="<?= $nombreDescarga ?>">
                        <i class="fas fa-download"></i> DESCARGAR PDF
                    </a>
                    <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar el archivo entregado?')">
                        <input type="hidden" name="idEstudiante" value="<?= $id ?>">
                        <button type="submit" name="borrarTFG" class="boton-secundario color-error">
                            <i class="fas fa-trash-alt"></i> ELIMINAR ENTREGA
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- INTEGRACIÓN DEL FORMULARIO DE SUBIDA -->
    <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">

        <div class="fila-detalle">
            <div class="etiqueta-detalle"><?= empty($tfg['archivoTFG']) ? 'Subir Trabajo' : 'Actualizar Archivo' ?></div>
            <div class="valor-detalle">
                <input type="file" name="archivoTFG">
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="subirTFG" class="boton-primario">
                <i class="fas fa-upload"></i> <?= empty($tfg['archivoTFG']) ? 'ENVIAR TFG' : 'ACTUALIZAR TFG' ?>
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

