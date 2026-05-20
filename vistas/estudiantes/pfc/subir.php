<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($id);
$estudianteActual = obtenerEstudiantePorId($id);
$notaTFG = obtenerCalificacionTFG($id);

$tituloDelPagina = "AULAPRO | MI TFG";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI TRABAJO FIN DE GRADO (TFG)</h1>
    </div>
</div>

<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <div class="titulo-tarjeta">ESTADO DE LA ENTREGA</div>

    <div class="fila-datos">
        <div class="nombre-detalle">Estado Actual</div>
        <div class="valor-detalle">
            <?php if (!empty($tfg['archivoTFG'])) { ?>
                <span class="indicador-estado activo-verde">ENTREGADO</span>
            <?php } else { ?>
                <span class="indicador-estado inactivo-rojo">NO ENTREGADO</span>
            <?php } ?>
        </div>
    </div>

    <?php if (!empty($tfg['archivoTFG'])) { ?>
        <div class="fila-datos">
            <div class="nombre-detalle">Archivo subido</div>
            <div class="valor-detalle">
                <p class="texto-negrita"><?= $tfg['archivoTFG'] ?></p>
                <span class="texto-suave">Fecha de entrega: <?= date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])) ?></span>
            </div>
        </div>

        <div class="fila-datos">
            <div class="nombre-detalle">Acciones</div>
            <div class="valor-detalle">
                <div class="caja espacio-medio">
                    <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="boton-secundario">DESCARGAR</a>
                    <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar el archivo entregado?')">
                        <input type="hidden" name="idEstudiante" value="<?= $id ?>">
                        <input type="submit" name="borrarTFG" class="boton-secundario color-error" value="ELIMINAR">
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($notaTFG)) { ?>
        <div class="fila-datos">
            <div class="nombre-detalle">Nota Final</div>
            <div class="valor-detalle">
                <?php if ($notaTFG['nota'] >= 5) { ?>
                    <span class="texto-verde texto-negrita" style="font-size: 1.3em;"><?= $notaTFG['nota'] ?> / 10 — APROBADO</span>
                <?php } else { ?>
                    <span class="texto-rojo texto-negrita" style="font-size: 1.3em;"><?= $notaTFG['nota'] ?> / 10 — SUSPENSO</span>
                <?php } ?>
                <p class="texto-suave" style="margin-top: 5px;"><em>Observaciones: <?= $notaTFG['observaciones'] ?></em></p>
            </div>
        </div>
    <?php } ?>

    <div class="titulo-tarjeta" style="margin-top: 25px; padding-top: 20px; ">SUBIR ARCHIVO</div>

    <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">

        <div class="campo">
            <label>Seleccione el archivo de su TFG (PDF o Word)</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Formatos aceptados: .pdf, .doc, .docx. Tamaño máximo recomendado: 10MB.</p>
            <input type="file" name="archivoTFG" accept=".pdf,.doc,.docx" class="<?= isset($errores['archivoTFG']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['archivoTFG'])) { ?>
                <strong class="error-campo"><?= $errores['archivoTFG'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="subirTFG" class="boton-primario" value="<?= empty($tfg['archivoTFG']) ? 'ENVIAR TFG' : 'ACTUALIZAR TFG' ?>">
            <input type="reset" class="boton-secundario" value="REINICIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
