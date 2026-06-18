<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idReto = (int)($_GET['id'] ?? 0);
$reto = obtenerRetoPorId($idReto);

if (!$reto) {
    header("Location: lista.php");
    exit;
}

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);

$modulosAsociados = listarModulosDeReto($idReto);
$mapaModulosAsociados = [];
foreach ($modulosAsociados as $modAsociado) { $mapaModulosAsociados[$modAsociado['idModulo']] = true; }

$tituloDelPagina = "AULAPRO | EDITAR RETO";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR RETO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <form action="../../../controladores/profesores/retos/actualizar.php" method="POST" class="formulario" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReto" value="<?= Security::escapeHtml($idReto ) ?>">

        <div class="campo">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($reto['nombreReto'] ) ?>">
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales</label>
            <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($reto['horasReto'] ) ?>">
        </div>

        <div class="row">
            <div class="campo">
                <label for="fechaInicio">Fecha Inicio</label>
                <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($reto['fechaInicio'] ) ?>">
            </div>

            <div class="campo">
                <label for="fechaFin">Fecha Fin</label>
                <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($reto['fechaFin'] ) ?>">
            </div>
        </div>

        <div class="campo">
            <label for="archivosReto">Añadir Materiales (PDF / Imágenes)</label>
            <div class="file-manager-premium">
                <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*" class="form-control mb-3">
                
                <div class="upload-progress-container" id="progressWrapper">
                    <div class="progress-bar-premium">
                        <div class="progress-fill-premium" id="progressFill"></div>
                    </div>
                    <div class="progress-text-premium" id="progressText">0%</div>
                </div>

                <?php 
                $archivosExistentes = obtenerArchivosReto($idReto);
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
                                <button type='button' class='file-delete-btn' onclick='borrarArchivoSmooth({$ae['idArchivo']}, {$idReto})' title='Eliminar archivo'>
                                   <i class='fas fa-times-circle'></i>
                                </button>
                              </div>";
                    }
                    echo '</div></div>';
                }
                ?>
            </div>
        </div>

        <div class="campo">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Seleccione los modulos en los que se evaluare este reto.</p>
            <div class="checks scroll-v200">
                <?php foreach ($misModulos as $mod) { ?>
                    <label class="check-item" for="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>">
                        <input type="checkbox" name="modulos[]" id="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>" value="<?= Security::escapeHtml($mod['idModulo'] ) ?>" 
                            <?= Security::escapeHtml(isset($mapaModulosAsociados[$mod['idModulo']]) ? 'checked' : '') ?>>
                        <span><?= Security::escapeHtml($mod['nombreModulo'] ) ?> (<?= Security::escapeHtml($mod['abreviaturaCiclo'] ) ?>)</span>
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
    if (!confirm('¿Estás seguro de que quieres eliminar este archivo?')) return;

    const $item = $('#file-' + idArchivo);
    
    $.ajax({
        url: '../../../controladores/comunes/borrar_archivo_reto.php',
        type: 'GET',
        data: { id: idArchivo, idReto: idReto, ajax: 1 },
        success: function(res) {
            const data = typeof res === 'string' ? JSON.parse(res) : res;
            if (data.status === 'success') {
                $item.addClass('removing');
                setTimeout(() => $item.remove(), 400);
            } else {
                alert('Error: ' + data.message);
            }
        },
        error: function() {
            alert('Error de conexión');
        }
    });
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
            error: function() {
                alert('Error al subir archivos');
                $('#btnGuardar').prop('disabled', false).text('GUARDAR CAMBIOS');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>


