<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores']);

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

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<!-- Panel de Estado -->
<div class="panel">
    <div class="titulo-tarjeta">
        <h3>ESTADO DE LA ENTREGA</h3>
    </div>

    <div class="fila-dat">
        <div class="etiqueta-detalle">Estado Actual</div>
        <div class="valor-detalle">
            <?php if (!empty($tfg['archivoTFG'])) { ?>
                <span class="bolita activo-verde">ENTREGADO</span>
            <?php } else { ?>
                <span class="bolita inactivo-rojo">NO ENTREGADO</span>
            <?php } ?>
        </div>
    </div>

    <?php if (!empty($tfg['archivoTFG'])) { ?>
        <div class="fila-dat">
            <div class="etiqueta-detalle">Archivo subido</div>
            <div class="valor-detalle">
                <p class="texto-negrita"><?= $tfg['archivoTFG'] ?></p>
                <span class="atenuado">Fecha de entrega: <?= date('d/m/Y H:i', strtotime($tfg['fechaSubidaTFG'])) ?></span>
            </div>
        </div>

        <div class="fila-dat">
            <div class="etiqueta-detalle">Acciones</div>
            <div class="valor-detalle">
                <div class="d-flex separacion-media">
                    <a href="../../../public/uploads/pfc/<?= $tfg['archivoTFG'] ?>" target="_blank" class="boton-secundario">
                        <i class="fas fa-download"></i> DESCARGAR
                    </a>
                    <form action="../../../controladores/estudiantes/pfc/eliminar.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar el archivo entregado?')">
                        <input type="hidden" name="idEstudiante" value="<?= $id ?>">
                        <button type="submit" name="borrarTFG" class="boton-secundario color-error">
                            <i class="fas fa-trash-alt"></i> ELIMINAR
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php } ?>

    <?php if (!empty($notaTFG)) { ?>
        <div class="fila-dat">
            <div class="etiqueta-detalle">Nota Final</div>
            <div class="valor-detalle">
                <?php if ($notaTFG['nota'] >= 5) { ?>
                    <span class="texto-verde texto-negrita" style="font-size: 1.3em;"><?= $notaTFG['nota'] ?> / 10 — APROBADO</span>
                <?php } else { ?>
                    <span class="texto-rojo texto-negrita" style="font-size: 1.3em;"><?= $notaTFG['nota'] ?> / 10 — SUSPENSO</span>
                <?php } ?>
                <p class="atenuado" style="margin-top: 5px;"><em>Observaciones: <?= $notaTFG['observaciones'] ?></em></p>
            </div>
        </div>
    <?php } ?>
</div>

<!-- Panel de Formulario -->
<div class="panel" style="margin-top: 30px;">
    <div class="titulo-tarjeta">
        <h3>GESTIÓN DEL ARCHIVO</h3>
    </div>

    <form action="../../../controladores/estudiantes/pfc/subir.php" method="POST" enctype="multipart/form-data" class="formulario">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">

        <div class="campo">
            <label>Seleccione el archivo de su TFG (PDF o Word)</label>
            <p class="atenuado" style="margin-bottom: 10px;">Formatos aceptados: .pdf, .doc, .docx. Tamaño máximo recomendado: 10MB.</p>
            <input type="file" name="archivoTFG" accept=".pdf,.doc,.docx" class="<?= isset($errores['archivoTFG']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['archivoTFG'])) { ?>
                <strong class="error-campo"><?= $errores['archivoTFG'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <button type="submit" name="subirTFG" class="boton-primario">
                <i class="fas fa-paper-plane"></i> <?= empty($tfg['archivoTFG']) ? 'ENVIAR TFG' : 'ACTUALIZAR TFG' ?>
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = 'subir.php';">
                <i class="fas fa-sync-alt"></i> REINICIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
