<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";

$todos_los_modulos = listarModulos();

$datos = $_SESSION['datos_reto'] ?? [];

$titulo_pagina = "AULAPRO | NUEVO RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVO RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores) && !is_array($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <form action="../../../controladores/admin/retos/insertar.php" method="POST" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="campo">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($datos['nombreReto'] ?? '') ?>" class="<?= (is_array($errores) && isset($errores['nombreReto'])) ? 'border-error' : '' ?>">
                <?php if (is_array($errores) && isset($errores['nombreReto'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['nombreReto']) ?></span><?php endif; ?>
            </div>

            <div class="campo">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($datos['horasReto'] ?? '') ?>" class="<?= (is_array($errores) && isset($errores['horasReto'])) ? 'border-error' : '' ?>">
                <?php if (is_array($errores) && isset($errores['horasReto'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['horasReto']) ?></span><?php endif; ?>
            </div>

            <div class="row">
                <div class="campo">
                    <label for="fechaInicioReto">Fecha de Inicio</label>
                    <input type="date" name="fechaInicioReto" id="fechaInicioReto" min="<?= date('Y-m-d') ?>" value="<?= Security::escapeHtml($datos['fechaInicioReto'] ?? '') ?>" class="<?= (is_array($errores) && isset($errores['fechaInicioReto'])) ? 'border-error' : '' ?>">
                    <?php if (is_array($errores) && isset($errores['fechaInicioReto'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['fechaInicioReto']) ?></span><?php endif; ?>
                </div>

                <div class="campo">
                    <label for="fechaFinReto">Fecha de Fin</label>
                    <input type="date" name="fechaFinReto" id="fechaFinReto" min="<?= date('Y-m-d') ?>" value="<?= Security::escapeHtml($datos['fechaFinReto'] ?? '') ?>" class="<?= (is_array($errores) && isset($errores['fechaFinReto'])) ? 'border-error' : '' ?>">
                    <?php if (is_array($errores) && isset($errores['fechaFinReto'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['fechaFinReto']) ?></span><?php endif; ?>
                </div>
            </div>

        <div class="campo">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto" class="<?= (is_array($errores) && isset($errores['modulosReto'])) ? 'border-error' : '' ?>">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>" <?= ($datos['modulosReto'] ?? '') == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?> (<?= Security::escapeHtml($modulo['nombreCiclo']) ?>)
                    </option>
                <?php } ?>
            </select>
            <?php if (is_array($errores) && isset($errores['modulosReto'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['modulosReto']) ?></span><?php endif; ?>
        </div>

        <div class="campo">
            <label for="archivosReto">Materiales / Guía del Reto (PDF o Imágenes)</label>
            <div class="file-manager-premium">
                <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*" class="form-control mb-2">
                <div class="upload-progress-container" id="progressWrapper">
                    <div class="progress-bar-premium">
                        <div class="progress-fill-premium" id="progressFill"></div>
                    </div>
                    <div class="progress-text-premium" id="progressText">0%</div>
                </div>
            </div>
            <small class="text-muted">Puedes seleccionar varios archivos a la vez.</small>
        </div>

        <div class="acciones">
            <button type="submit" name="guardarReto" class="boton-primario" id="btnGuardar">
                <i class="fas fa-plus"></i> CREAR RETO
            </button>
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    $('.formulario').on('submit', function(e) {
        if ($('#archivosReto').get(0).files.length === 0) return true;

        e.preventDefault();
        const formData = new FormData(this);
        formData.append('guardarReto', '1');

        $('#progressWrapper').fadeIn();
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

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
                window.location.href = 'verRetos.php';
            },
            error: function() {
                alert('Error al crear el reto');
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-plus"></i> CREAR RETO');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
<?php if (is_array($errores) && !empty($errores)): ?>
<script>
(function(){
    var first = document.querySelector('.border-error');
    if (first) { first.focus(); first.scrollIntoView({behavior:'smooth', block:'center'}); }
})();
</script>
<?php endif; ?>
