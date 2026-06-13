<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_reto = $_GET['idReto'] ?? '';
$reto = obtenerRetoPorId($id_reto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulos_del_reto = listarModulosDeReto($id_reto);
$idModuloActual = !empty($modulos_del_reto) ? $modulos_del_reto[0]['idModulo'] : '';

if (isset($_SESSION['datos_reto'])) {
    $reto = $_SESSION['datos_reto'];
    $idModuloActual = $reto['modulosReto'] ?? '';
}

$todos_los_modulos = listarModulos();

$titulo_pagina = "AULAPRO | MODIFICAR RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div>
    <?php } ?>

    <?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/retos/actualizar.php" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto" value="<?= $id_reto ?>">

        <div class="campo">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= $reto['nombreReto'] ?>">
            </div>

            <div class="campo">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= $reto['horasReto'] ?>">
            </div>

            <div class="row">
                <div class="campo">
                    <label for="fechaInicioReto">Fecha de Inicio</label>
                    <input type="date" name="fechaInicioReto" id="fechaInicioReto" value="<?= $reto['fechaInicio'] ?>">
                </div>

                <div class="campo">
                    <label for="fechaFinReto">Fecha de Fin</label>
                    <input type="date" name="fechaFinReto" id="fechaFinReto" value="<?= $reto['fechaFin'] ?>">
                </div>
            </div>

        <div class="campo">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>" <?= $idModuloActual == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?> (<?= Security::escapeHtml($modulo['nombreCiclo']) ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo">
            <label for="archivosReto">Añadir Materiales (PDF o Imágenes)</label>
            <div class="file-manager-premium">
                <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*" class="form-control mb-3">
                
                <div class="upload-progress-container" id="progressWrapper">
                    <div class="progress-bar-premium">
                        <div class="progress-fill-premium" id="progressFill"></div>
                    </div>
                    <div class="progress-text-premium" id="progressText">0%</div>
                </div>

                <?php 
                $archivosExistentes = obtenerArchivosReto($id_reto);
                if (!empty($archivosExistentes)) {
                    echo '<div class="mt-4">
                            <label class="small text-muted fw-bold text-uppercase">Archivos cargados actualmente:</label>
                            <div class="file-list-premium">';
                    foreach ($archivosExistentes as $ae) {
                        $isPdf = ($ae['tipoArchivo'] === 'pdf');
                        $icon = $isPdf ? 'fa-file-pdf text-danger' : 'fa-image text-primary';
                        echo "<div class='file-item-premium' id='file-{$ae['idArchivo']}'>
                                <i class='fas {$icon} fa-lg'></i> 
                                <div class='file-info-premium'>
                                    <span class='file-name-premium' title='{$ae['nombreArchivo']}'>{$ae['nombreArchivo']}</span>
                                    <span class='file-type-premium'>" . ($isPdf ? 'Documento PDF' : 'Imagen') . "</span>
                                </div>
                                <button type='button' class='file-delete-btn' onclick='borrarArchivoSmooth({$ae['idArchivo']}, {$id_reto})' title='Eliminar archivo'>
                                   <i class='fas fa-times-circle'></i>
                                </button>
                              </div>";
                    }
                    echo '</div></div>';
                }
                ?>
            </div>
            <small class="text-muted d-block mt-2">Puedes seleccionar varios archivos. Se mostrará una barra de progreso al guardar.</small>
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
    if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) return;

    const $item = $('#file-' + idArchivo);
    
    $.ajax({
        url: '../../../controladores/comunes/borrar_archivo_reto.php',
        type: 'GET',
        data: { id: idArchivo, idReto: idReto, ajax: 1 },
        success: function(res) {
            // Intentar parsear si no es objeto
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.status === 'success') {
                $item.addClass('removing');
                setTimeout(() => $item.remove(), 400);
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function() {
            alert('Error de conexión al borrar el archivo');
        }
    });
}

$(document).ready(function() {
    $('.formulario').on('submit', function(e) {
        if ($('#archivosReto').get(0).files.length === 0) return true; // Si no hay archivos, envío normal

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
            success: function(res) {
                // Como el controlador redirige, capturamos si hay éxito
                window.location.reload();
            },
            error: function() {
                alert('Error al subir archivos');
                $('#btnGuardar').prop('disabled', false).text('GUARDAR CAMBIOS');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
