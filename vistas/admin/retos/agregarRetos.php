<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_retos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_modulos = listarModulos();
$listaCiclos = listarTodosLosCiclos();

$datos = $_SESSION['datos_reto'] ?? [];
unset($_SESSION['datos_reto']);

$titulo_pagina = "AULAPRO | NUEVO RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVO RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/retos/insertar.php" method="POST" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="campo<?= fieldClass($errores, 'nombreReto') ?>">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= Security::escapeHtml($datos['nombreReto'] ?? '') ?>">
                <?= fieldError($errores, 'nombreReto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'horasReto') ?>">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= Security::escapeHtml($datos['horasReto'] ?? '') ?>">
                <?= fieldError($errores, 'horasReto') ?>
            </div>

            <div class="row">
                <div class="campo<?= fieldClass($errores, 'fechaInicioReto') ?>">
                    <label for="fechaInicioReto">Fecha de Inicio</label>
                    <input type="date" name="fechaInicioReto" id="fechaInicioReto" min="<?= date('Y-m-d') ?>" value="<?= Security::escapeHtml($datos['fechaInicioReto'] ?? '') ?>">
                    <?= fieldError($errores, 'fechaInicioReto') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'fechaFinReto') ?>">
                    <label for="fechaFinReto">Fecha de Fin</label>
                    <input type="date" name="fechaFinReto" id="fechaFinReto" min="<?= date('Y-m-d') ?>" value="<?= Security::escapeHtml($datos['fechaFinReto'] ?? '') ?>">
                    <?= fieldError($errores, 'fechaFinReto') ?>
                </div>
            </div>

        <div class="row">
            <div class="campo">
                <label for="filtroCicloReto">Filtrar por Ciclo</label>
                <select id="filtroCicloReto" onchange="filtrarModulosReto()">
                    <option value="">-- Todos los ciclos --</option>
                    <?php foreach ($listaCiclos as $c): ?>
                        <option value="<?= (int)$c['idCiclo'] ?>"><?= Security::escapeHtml($c['nombreCiclo']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="filtroCursoReto">Filtrar por Curso</label>
                <select id="filtroCursoReto" onchange="filtrarModulosReto()">
                    <option value="">-- Todos los cursos --</option>
                    <option value="1º">1º Año</option>
                    <option value="2º">2º Año</option>
                </select>
            </div>
        </div>

        <div class="campo<?= fieldClass($errores, 'modulosReto') ?>">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <option value="<?= Security::escapeHtml($modulo['idModulo']) ?>"
                            data-ciclo="<?= (int)$modulo['idCiclo'] ?>"
                            data-curso="<?= Security::escapeHtml($modulo['cursoAnio'] ?? '') ?>"
                            <?= ($datos['modulosReto'] ?? '') == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($modulo['nombreModulo']) ?>
                        <?= !empty($modulo['cursoAnio']) ? ' (' . Security::escapeHtml($modulo['cursoAnio']) . ')' : '' ?>
                        — <?= Security::escapeHtml($modulo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
            <?= fieldError($errores, 'modulosReto') ?>
        </div>

        <div class="campo ancho-total">
            <label>Materiales / Guía del Reto <span class="texto-suave" style="font-weight:400;">(PDF o imágenes)</span></label>
            <label for="archivosReto" class="boton-secundario" style="display:inline-flex;align-items:center;gap:6px;cursor:pointer;margin-bottom:0;">
                <i class="fas fa-paperclip"></i> Adjuntar archivos
            </label>
            <input type="file" name="archivosReto[]" id="archivosReto" multiple accept=".pdf,image/*"
                   style="position:absolute;opacity:0;width:0;height:0;" onchange="mostrarArchivosReto(this)">
            <div class="archivo-reto-lista" id="listaArchivosReto"></div>
            <div id="progressWrapper" style="display:none;margin-top:10px;">
                <div style="height:6px;background:var(--border-2,var(--border));border-radius:4px;overflow:hidden;">
                    <div id="progressFill" style="height:100%;background:var(--accent,#4F46E5);width:0%;transition:width .3s;"></div>
                </div>
                <p id="progressText" class="texto-suave" style="font-size:.8rem;margin-top:4px;">0%</p>
            </div>
            <p class="texto-suave" style="font-size:.8rem;margin-top:6px;">Múltiples archivos permitidos</p>
        </div>

        <div class="acciones">
            <button type="submit" name="guardarReto" class="boton-primario" id="btnGuardar">
                <i class="fas fa-plus"></i> CREAR RETO
            </button>
            <input type="reset" class="boton-secundario" value="LIMPIAR" onclick="$('#listaArchivosReto').empty();">
        </div>
    </form>
</div>

<script>
var _todosModulos = <?= json_encode(array_map(fn($m) => ['id' => (int)$m['idModulo'], 'ciclo' => (int)$m['idCiclo']], $todos_los_modulos)) ?>;

function filtrarModulosReto() {
    var idCiclo = parseInt($('#filtroCicloReto').val()) || 0;
    var curso = $('#filtroCursoReto').val();
    var $sel = $('#modulosReto');
    var currentVal = $sel.val();
    $sel.find('option').each(function() {
        if (!$(this).val()) return;
        var optCiclo = parseInt($(this).data('ciclo')) || 0;
        var optCurso = $(this).data('curso');
        var matchCiclo = !idCiclo || optCiclo === idCiclo;
        var matchCurso = !curso || optCurso === curso;
        $(this).toggle(matchCiclo && matchCurso);
    });
    // Reset selection if the selected option is now hidden
    if ($sel.find(':selected').val()) {
        var selCiclo = parseInt($sel.find(':selected').data('ciclo')) || 0;
        var selCurso = $sel.find(':selected').data('curso');
        if ((idCiclo && selCiclo !== idCiclo) || (curso && selCurso !== curso)) {
            $sel.val('');
        }
    }
}

function mostrarArchivosReto(input) {
    var $lista = $('#listaArchivosReto').empty();
    Array.from(input.files).forEach(function(f) {
        var icon = f.type === 'application/pdf' ? 'fa-file-pdf' : 'fa-file-image';
        var size = f.size > 1048576 ? (f.size / 1048576).toFixed(1) + ' MB' : Math.ceil(f.size / 1024) + ' KB';
        $lista.append(
            '<div class="archivo-reto-item">' +
            '<i class="fas ' + icon + '" style="color:var(--accent,#4F46E5);font-size:16px;flex-shrink:0;"></i>' +
            '<span class="archivo-reto-nombre">' + $('<span>').text(f.name).html() + '</span>' +
            '<span class="texto-suave" style="font-size:.75rem;white-space:nowrap;">' + size + '</span>' +
            '</div>'
        );
    });
}

$(function() {
    // Pre-select ciclo filter if a module was already selected (e.g. on validation error return)
    var $selMod = $('#modulosReto');
    var selCiclo = parseInt($selMod.find(':selected').data('ciclo')) || 0;
    if (selCiclo) {
        $('#filtroCicloReto').val(selCiclo);
        filtrarModulosReto();
        $selMod.val('<?= Security::escapeHtml($datos['modulosReto'] ?? '') ?>');
    }

    // AJAX upload with progress
    $('.formulario').on('submit', function(e) {
        if ($('#archivosReto').get(0).files.length === 0) return true;

        e.preventDefault();
        var formData = new FormData(this);
        formData.append('guardarReto', '1');

        $('#progressWrapper').fadeIn();
        $('#btnGuardar').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creando...');

        $.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener('progress', function(evt) {
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
            success: function() { window.location.href = 'verRetos.php'; },
            error: function() {
                if (window.Toast) Toast.show('Error al crear el reto', 'error');
                else alert('Error al crear el reto');
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
    var first = document.querySelector('.campo-invalido input, .campo-invalido select');
    if (first) { first.focus(); first.scrollIntoView({behavior:'smooth', block:'center'}); }
})();
</script>
<?php endif; ?>
