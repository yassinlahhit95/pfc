<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_reto'] ?? [];

require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$misModulos = listarModulosDeProfesor($idProfesor);
$modulosElegidos = $datos['modulos'] ?? [];
$mapaModulosElegidos = [];
foreach ($modulosElegidos as $idM) { $mapaModulosElegidos[$idM] = true; }

$tituloDelPagina = "AULAPRO | NUEVO RETO";
$seccionActual = 'retos';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO RETO</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/profesores/retos/insertar.php" method="POST" class="formulario" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="campo">
            <label for="nombreReto">Nombre del Reto</label>
            <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($datos['nombreReto'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="horasReto">Horas Totales</label>
            <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($datos['horasReto'] ?? '') ?>">
        </div>

        <div class="row">
            <div class="campo">
                <label for="fechaInicio">Fecha Inicio</label>
                <input type="date" name="fechaInicio" id="fechaInicio" value="<?= Security::escapeHtml($datos['fechaInicio'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="fechaFin">Fecha Fin</label>
                <input type="date" name="fechaFin" id="fechaFin" value="<?= Security::escapeHtml($datos['fechaFin'] ?? '') ?>">
            </div>
        </div>

        <div class="campo">
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

        <div class="campo">
            <label>Asociar a Módulos</label>
            <p class="texto-suave" style="margin-bottom: 10px;">Seleccione los modulos en los que se evaluare este reto.</p>
            <div class="checks scroll-v200">
                <?php if (empty($misModulos)) { ?>
                    <p class="texto-rojo">No tiene modulos asignados. No puede crear retos.</p>
                <?php } else { ?>
                    <?php foreach ($misModulos as $mod) { ?>
                        <label class="check-item" for="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>">
                            <input type="checkbox" name="modulos[]" id="mod_<?= Security::escapeHtml($mod['idModulo'] ) ?>" value="<?= Security::escapeHtml($mod['idModulo'] ) ?>" 
                                <?= Security::escapeHtml(isset($mapaModulosElegidos[$mod['idModulo']]) ? 'checked' : '') ?>>
                            <span><?= Security::escapeHtml($mod['nombreModulo'] ) ?> (<?= Security::escapeHtml($mod['abreviaturaCiclo'] ) ?>)</span>
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
    <?php if ($errores): ?>
    if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');
    <?php endif; ?>
    <?php if ($exito): ?>
    if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');
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
            error: function() {
                if (window.Toast) Toast.show('Error al registrar el reto. Inténtalo de nuevo.', 'error');
                $('#btnGuardar').prop('disabled', false).html('<i class="fas fa-plus"></i> REGISTRAR RETO');
            }
        });
    });
});
</script>

<?php include '../comunes/footer.php'; ?>


