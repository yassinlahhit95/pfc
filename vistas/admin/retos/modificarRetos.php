<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idReto = (int)($_GET['idReto'] ?? 0);
$reto = obtenerRetoPorId($idReto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulosDelReto = listarModulosDeReto($idReto);
$idModuloActual = !empty($modulosDelReto) ? $modulosDelReto[0]['idModulo'] : '';

if (isset($_SESSION['datos_reto'])) {
    $reto = $_SESSION['datos_reto'];
    $idModuloActual = $reto['modulosReto'] ?? '';
}
unset($_SESSION['datos_reto']);

$todosLosModulos = listarModulos();

$titulo_pagina = "AULAPRO | MODIFICAR RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/retos/actualizar.php" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto" value="<?= $idReto ?>">

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'nombreReto') ?>">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($reto['nombreReto'] ?? '') ?>">
                <?= fieldError($errores, 'nombreReto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'horasReto') ?>">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($reto['horasReto'] ?? '') ?>">
                <?= fieldError($errores, 'horasReto') ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'fechaInicioReto') ?>">
                <label for="fechaInicioReto">Fecha de Inicio</label>
                <input type="date" name="fechaInicioReto" id="fechaInicioReto" value="<?= Security::escapeHtml($reto['fechaInicio'] ?? '') ?>">
                <?= fieldError($errores, 'fechaInicioReto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaFinReto') ?>">
                <label for="fechaFinReto">Fecha de Fin</label>
                <input type="date" name="fechaFinReto" id="fechaFinReto" value="<?= Security::escapeHtml($reto['fechaFin'] ?? '') ?>">
                <?= fieldError($errores, 'fechaFinReto') ?>
            </div>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'modulosReto') ?>">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todosLosModulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>" <?= $idModuloActual == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?> (<?= Security::escapeHtml($modulo['nombreCiclo']) ?>)
                    </option>
                <?php } ?>
            </select>
            <?= fieldError($errores, 'modulosReto') ?>
        </div>

        <div class="campo ancho-total">
            <label for="archivosReto">Materiales del Reto</label>
            <div class="file-manager-premium">
                <label class="zona-subida" for="archivosReto">
                    <i class="fas fa-cloud-upload-alt"></i>
                    <span>Añadir PDF o imágenes</span>
                    <small>Haz clic para seleccionar — múltiple selección permitida</small>
                    <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*" style="display:none">
                </label>

                <div class="upload-progress-container" id="progressWrapper">
                    <div class="progress-bar-premium">
                        <div class="progress-fill-premium" id="progressFill"></div>
                    </div>
                    <div class="progress-text-premium" id="progressText">0%</div>
                </div>

                <?php
                $archivosExistentes = obtenerArchivosReto($idReto);
                if (!empty($archivosExistentes)):
                ?>
                <div class="archivos-cargados-wrap">
                    <p class="archivos-cargados-titulo"><i class="fas fa-paperclip"></i> Archivos actuales</p>
                    <div class="file-list-premium">
                    <?php foreach ($archivosExistentes as $archivo):
                        $isPdf = ($archivo['tipoArchivo'] === 'pdf');
                        $icon  = $isPdf ? 'fa-file-pdf' : 'fa-image';
                        $color = $isPdf ? '#e53e3e' : 'var(--accent)';
                    ?>
                        <div class="file-item-premium" id="file-<?= (int)$archivo['idArchivo'] ?>">
                            <i class="fas <?= $icon ?>" style="color:<?= $color ?>;font-size:1.3rem;flex-shrink:0;"></i>
                            <div class="file-info-premium">
                                <span class="file-name-premium" title="<?= Security::escapeHtml($archivo['nombreArchivo']) ?>"><?= Security::escapeHtml($archivo['nombreArchivo']) ?></span>
                                <span class="file-type-premium"><?= $isPdf ? 'PDF' : 'Imagen' ?></span>
                            </div>
                            <button type="button" class="file-delete-btn"
                                    onclick="borrarArchivoSmooth(<?= (int)$archivo['idArchivo'] ?>, <?= $idReto ?>)"
                                    title="Eliminar archivo">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarReto" class="boton-primario" id="btnGuardar">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
function borrarArchivoSmooth(idArchivo, idReto) {
    const $item = $('#file-' + idArchivo);
    const $btn = $item.find('.file-delete-btn');

    if ($btn.hasClass('confirming')) {
        $btn.removeClass('confirming');
        $.ajax({
            url: '../../../controladores/comunes/borrar_archivo_reto.php',
            type: 'POST',
            data: { id: idArchivo, idReto: idReto, ajax: 1, csrf_token: $('[name="csrf_token"]').val() },
            success: function(res) {
                const data = typeof res === 'string' ? JSON.parse(res) : res;
                if (data.ok) {
                    $item.addClass('removing');
                    setTimeout(() => $item.remove(), 400);
                    if (window.Toast) Toast.show('Archivo eliminado', 'success');
                } else {
                    if (window.Toast) Toast.show('Error: ' + data.msg, 'error');
                }
            },
            error: function(jqXHR) {
                // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
                if (window.Toast) Toast.show('Error de conexión al borrar el archivo', 'error');
            }
        });
    } else {
        $btn.addClass('confirming').attr('title', 'Haz clic de nuevo para confirmar');
        setTimeout(() => $btn.removeClass('confirming').attr('title', 'Eliminar archivo'), 2500);
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
                    if (evt.lengthComputable) {
                        var pct = Math.round((evt.loaded / evt.total) * 100);
                        $('#progressFill').css('width', pct + '%');
                        $('#progressText').text(pct + '%');
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
                    if (window.Toast) Toast.show('Error al subir archivos', 'error');
                }
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-save"></i> GUARDAR CAMBIOS');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
