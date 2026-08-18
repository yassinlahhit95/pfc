<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_reto'] ?? [];
unset($_SESSION['datos_reto']);

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);
$modulosElegidos = $datos['modulos'] ?? [];
$mapaModulosElegidos = [];
foreach ($modulosElegidos as $idModuloElegido) { $mapaModulosElegidos[$idModuloElegido] = true; }

$titulo_pagina = "Nuevo Reto";
$seccionActual = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<style>
.checks-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 15px;
}
.check-btn {
    cursor: pointer;
    user-select: none;
}
.check-btn input[type="checkbox"] {
    display: none;
}
.check-btn span {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    border: 2px solid var(--border);
    border-radius: 12px;
    background: var(--bg-panel);
    color: var(--fg);
    font-size: 0.9rem;
    font-weight: 600;
    transition: all 0.2s ease;
}
.check-btn input[type="checkbox"]:checked + span {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
    box-shadow: 0 4px 15px rgba(var(--accent-rgb), 0.35);
    transform: translateY(-2px);
}
.check-btn:hover input[type="checkbox"]:not(:checked) + span {
    border-color: var(--accent);
    background: rgba(var(--accent-rgb), 0.05);
}
.error-text {
    color: #ef4444;
    font-size: 0.85rem;
    font-weight: 600;
    margin-top: 5px;
    display: block;
}
</style>
<div class="cabecera">
    <h1>Nuevo Reto</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/profesores/retos/insertar.php" method="POST" class="formulario" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'nombreReto') ?>">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($datos['nombreReto'] ?? '') ?>">
                <?= fieldError($errores, 'nombreReto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'horasReto') ?>">
                <label for="horasReto">Horas Totales</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($datos['horasReto'] ?? '') ?>">
                <?= fieldError($errores, 'horasReto') ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'fechaInicio') ?>">
                <label for="fechaInicio">Fecha Inicio</label>
                <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($datos['fechaInicio'] ?? '') ?>">
                <?= fieldError($errores, 'fechaInicio') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaFin') ?>">
                <label for="fechaFin">Fecha Fin</label>
                <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($datos['fechaFin'] ?? '') ?>">
                <?= fieldError($errores, 'fechaFin') ?>
            </div>
        </div>

        <div class="campo ancho-total">
            <label for="archivosReto">Materiales del Reto (PDF / Imágenes)</label>
            <div class="file-manager-premium">
                <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*" class="form-control mb-2">
                <div class="upload-progress-container" id="progressWrapper">
                    <div class="progress-bar-premium">
                        <div class="progress-fill-premium" id="progressFill"></div>
                    </div>
                    <div class="progress-text-premium" id="progressText">0%</div>
                </div>
            </div>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'modulos') ?>" style="margin-top: 25px;">
            <label style="font-size: 1.1rem;">Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 5px;">Seleccione los módulos en los que se evaluará este reto.</p>
            <?php if (!empty($errores['modulos'])): ?>
                <span class="error-text"><i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml($errores['modulos']) ?></span>
            <?php endif; ?>

            <div class="checks-grid">
                <?php if (empty($misModulos)) { ?>
                    <p class="texto-rojo" style="padding: 10px; background: rgba(239, 68, 68, 0.1); border-radius: 8px;">No tiene módulos asignados. No puede crear retos.</p>
                <?php } else { ?>
                    <?php foreach ($misModulos as $modulo) { ?>
                        <label class="check-btn" for="mod_<?= Security::escapeHtml($modulo['idModulo'] ) ?>">
                            <input type="checkbox" name="modulos[]" id="mod_<?= Security::escapeHtml($modulo['idModulo'] ) ?>" value="<?= Security::escapeHtml($modulo['idModulo'] ) ?>"
                                <?= isset($mapaModulosElegidos[$modulo['idModulo']]) ? 'checked' : '' ?>>
                            <span><i class="fas fa-cube" style="margin-right: 8px; opacity: 0.7;"></i> <?= Security::escapeHtml($modulo['nombreModulo'] ) ?> (<?= Security::escapeHtml($modulo['abreviaturaCiclo'] ) ?>)</span>
                        </label>
                    <?php } ?>
                <?php } ?>
            </div>
        </div>

        <div class="acciones" style="margin-top: 20px;">
            <button type="submit" name="insertarReto" class="boton-primario" id="btnGuardar">
                <i class="fas fa-plus"></i> REGISTRAR RETO
            </button>
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
$(document).ready(function() {
    <?php if ($exito): ?>
    if (window.Toast) Toast.show(<?= Security::jsonEncodeSafe($exito) ?>, 'success');
    <?php endif; ?>

    $('.formulario').on('submit', function(e) {
        if ($('#archivosReto').get(0).files.length === 0) return true;

        e.preventDefault();
        const formData = new FormData(this);
        formData.append('insertarReto', '1');

        $('#progressWrapper').fadeIn();
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Registrando...');

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
                window.location.href = 'lista.php';
            },
            error: function(jqXHR) {
                // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
                if (!(jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500)) {
                    if (window.Toast) Toast.show('Error al registrar el reto. Inténtalo de nuevo.', 'error');
                }
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-plus"></i> REGISTRAR RETO');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>
