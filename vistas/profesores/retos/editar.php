<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datosReto = $_SESSION['datos_reto'] ?? null;
unset($_SESSION['datos_reto']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idReto = (int)($_GET['id'] ?? 0);
$reto = obtenerRetoPorId($idReto);

if (!$reto) {
    header("Location: lista.php");
    exit;
}

$idProfesor   = $_SESSION['idProfesor'];
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);
$misModulos   = ($esTutor && $idCicloTutor)
    ? listarModulosDeCicloConNombre($idCicloTutor)
    : listarModulosDeProfesor($idProfesor);

$modulosAsociados = listarModulosDeReto($idReto);
$mapaModulosAsociados = [];
foreach ($modulosAsociados as $modAsociado) { $mapaModulosAsociados[$modAsociado['idModulo']] = true; }

if ($datosReto) {
    $reto = array_merge($reto, $datosReto);
    if (isset($datosReto['modulos'])) {
        $mapaModulosAsociados = [];
        foreach ($datosReto['modulos'] as $idModuloElegido) { $mapaModulosAsociados[$idModuloElegido] = true; }
    }
}

$tituloDelPagina = "AULAPRO | EDITAR RETO";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR RETO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="formulario" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto" value="<?= Security::escapeHtml($idReto ) ?>">

        <div class="campo<?= fieldClass($errores, 'nombreReto') ?>">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($reto['nombreReto'] ?? '') ?>">
            <?= fieldError($errores, 'nombreReto') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'horasReto') ?>">
            <label for="horasReto">Horas Totales</label>
            <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($reto['horasReto'] ?? '') ?>">
            <?= fieldError($errores, 'horasReto') ?>
        </div>

        <div class="row">
            <div class="campo<?= fieldClass($errores, 'fechaInicio') ?>">
                <label for="fechaInicio">Fecha Inicio</label>
                <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($reto['fechaInicio'] ?? '') ?>">
                <?= fieldError($errores, 'fechaInicio') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaFin') ?>">
                <label for="fechaFin">Fecha Fin</label>
                <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($reto['fechaFin'] ?? '') ?>">
                <?= fieldError($errores, 'fechaFin') ?>
            </div>
        </div>

        <div class="campo ancho-total">
            <label for="archivosReto">Añadir Materiales (PDF / Imágenes)</label>
            <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*">
            <p class="texto-suave" style="font-size:12px;margin-top:6px;">PDF o imágenes. Puede seleccionar varios archivos a la vez.</p>

            <div id="progressWrapper" style="display:none;margin-top:10px;">
                <div style="background:var(--border);border-radius:999px;height:6px;overflow:hidden;">
                    <div id="progressFill" style="height:100%;width:0;background:var(--accent);border-radius:999px;transition:width .2s;"></div>
                </div>
                <span class="texto-suave" id="progressText" style="font-size:12px;margin-top:4px;display:block;">0%</span>
            </div>

            <?php $archivosExistentes = obtenerArchivosReto($idReto);
            if (!empty($archivosExistentes)) { ?>
                <div class="archivo-reto-lista">
                    <?php foreach ($archivosExistentes as $archivoExistente) {
                        $isPdf = ($archivoExistente['tipoArchivo'] === 'pdf'); ?>
                    <div class="archivo-reto-item" id="file-<?= (int)$archivoExistente['idArchivo'] ?>">
                        <i class="fas <?= $isPdf ? 'fa-file-pdf' : 'fa-image' ?>" style="font-size:18px;color:<?= $isPdf ? 'var(--rojo)' : 'var(--accent)' ?>;flex-shrink:0;"></i>
                        <span class="archivo-reto-nombre" title="<?= Security::escapeHtml($archivoExistente['nombreArchivo']) ?>"><?= Security::escapeHtml($archivoExistente['nombreArchivo']) ?></span>
                        <span class="texto-estado <?= $isPdf ? 'rojo' : 'azul' ?>"><?= $isPdf ? 'PDF' : 'Imagen' ?></span>
                        <button type="button" class="boton-peligro btn-pequeno" onclick="borrarArchivoSmooth(<?= (int)$archivoExistente['idArchivo'] ?>, <?= (int)$idReto ?>)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'modulos') ?> ancho-total">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="font-size:13px;margin-bottom:10px;">Seleccione los módulos en los que se evaluará este reto.</p>
            <?= fieldError($errores, 'modulos') ?>
            <div class="modulo-chips">
                <?php foreach ($misModulos as $modulo) { ?>
                <label class="modulo-chip">
                    <input type="checkbox" name="modulos[]" value="<?= (int)$modulo['idModulo'] ?>"
                           <?= isset($mapaModulosAsociados[$modulo['idModulo']]) ? 'checked' : '' ?>>
                    <span>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?>
                        <em>(<?= Security::escapeHtml($modulo['abreviaturaCiclo']) ?>)</em>
                    </span>
                </label>
                <?php } ?>
            </div>
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <button type="submit" name="actualizarReto" class="boton-primario" id="btnGuardar">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
function borrarArchivoSmooth(idArchivo, idReto) {
    if (window.ModalConfirm) {
        ModalConfirm.prompt('¿Estás seguro de que quieres eliminar este archivo?').then(function(confirmed) {
            if (confirmed) executeDelete();
        });
    } else {
        if (confirm('¿Estás seguro de que quieres eliminar este archivo?')) executeDelete();
    }

    function executeDelete() {
        const $item = $('#file-' + idArchivo);
        $.ajax({
            url: '../../../controladores/comunes/borrar_archivo_reto.php',
            type: 'POST',
            data: { id: idArchivo, idReto: idReto, ajax: 1, csrf_token: $('[name="csrf_token"]').val() },
            success: function(res) {
                const data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.status === 'success') {
                    $item.addClass('removing');
                    setTimeout(() => $item.remove(), 400);
                } else {
                    if (window.Toast) Toast.show('Error: ' + data.message, 'error');
                }
            },
            error: function(jqXHR) {
                // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
                if (window.Toast) Toast.show('Error de conexión', 'error');
            }
        });
    }
}

$(document).ready(function() {
    $('.formulario').on('submit', function(e) {
        if ($('#archivosReto').get(0).files.length === 0) return true;

        e.preventDefault();
        const formData = new FormData(this);
        formData.append('actualizarReto', '1');

        $('#progressWrapper').fadeIn();
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Subiendo...');

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (xhr.lengthComputable) {
                        var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        $('#progressFill').css('width', percentComplete + '%');
                        $('#progressText').text(percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            type: 'POST',
            url: $(this).attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function() {
                window.location.reload();
            },
            error: function(jqXHR) {
                // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                if (!(jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500)) {
                    alert('Error al subir archivos');
                }
                $('#btnGuardar').prop('disabled', false).text('GUARDAR CAMBIOS');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
