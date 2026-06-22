<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_subida_tfg');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/configuracion.php";

$idEstudiante = $_SESSION['idEstudiante'];
$tfg = obtenerTFGporEstudiante($idEstudiante);
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$notaTFG = obtenerCalificacionTFG($idEstudiante);
$cfg = obtenerConfiguracionCentro();
$entregaAbierta = (bool)($cfg['feature_subida_tfg'] ?? 1);

$tituloDelPagina = "AULAPRO | MI TFG";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MI TRABAJO FIN DE GRADO (TFG)</h1>
    </div>
</div>


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
                <p class="texto-negrita"><?= Security::escapeHtml($tfg['archivoTFG'] ) ?></p>
                <span class="texto-suave">Fecha de entrega: <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG']))) ?></span>
            </div>
        </div>

        <div class="fila-datos">
            <div class="nombre-detalle">Acciones</div>
            <div class="valor-detalle">
                <div class="caja espacio-medio">
                    <a href="../../../public/uploads/pfc/<?= Security::escapeHtml($tfg['archivoTFG'] ) ?>" target="_blank" class="boton-secundario">DESCARGAR</a>
                    <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">
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
                    <span class="texto-verde texto-negrita" style="font-size: 1.3em;"><?= Security::escapeHtml($notaTFG['nota'] ) ?> / 10 — APROBADO</span>
                <?php } else { ?>
                    <span class="texto-rojo texto-negrita" style="font-size: 1.3em;"><?= Security::escapeHtml($notaTFG['nota'] ) ?> / 10 — SUSPENSO</span>
                <?php } ?>
                <p class="texto-suave" style="margin-top: 5px;"><em>Observaciones: <?= Security::escapeHtml($notaTFG['observaciones'] ) ?></em></p>
            </div>
        </div>
    <?php } ?>

    <div class="titulo-tarjeta" style="margin-top: 25px; padding-top: 20px;">SUBIR ARCHIVO</div>

    <?php if (!$entregaAbierta): ?>
        <div class="mensaje-error" style="margin-top: 15px;">
            <i class="fas fa-lock"></i> La entrega del TFG está cerrada en este momento. Contacta con tu profesor o director para más información.
        </div>
    <?php else: ?>
    <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante) ?>">

        <div class="campo">
            <label>Seleccione el archivo de su TFG (PDF o Word)</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Formatos aceptados: .pdf, .doc, .docx. Tamaño máximo: 20 MB.</p>
            <input type="file" name="archivoTFG" accept=".pdf,.doc,.docx">
        </div>

        <div class="acciones">
            <input type="submit" name="subirTFG" class="boton-primario" value="<?= Security::escapeHtml(empty($tfg['archivoTFG']) ? 'ENVIAR TFG' : 'ACTUALIZAR TFG') ?>">
            <input type="reset" class="boton-secundario" value="REINICIAR">
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include '../comunes/footer.php'; ?>


