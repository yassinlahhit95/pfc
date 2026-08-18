<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../include/form_helpers.php";
require_once __DIR__ . "/../../../include/R2Client.php";

$gasto = obtenerGastoPorId((int)($_GET['idGasto'] ?? 0));
if (!$gasto) {
    header("Location: verGastos.php");
    exit;
}

$categorias = listarCategorias();
$ciclos     = listarTodosLosCiclos();
$datos      = $_SESSION['datos_gasto'] ?? null;
unset($_SESSION['datos_gasto']);

$valorCampo = fn($k) => Security::escapeHtml($datos[$k] ?? $gasto[$k] ?? '');

$titulo_pagina = "Editar Gasto";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Editar Gasto</h1>
    <a href="verGastos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/gastos/actualizar.php" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idGasto" value="<?= (int)$gasto['idGasto'] ?>">

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'concepto') ?>">
                <label for="concepto">Concepto <span style="color:var(--rojo)">*</span></label>
                <input type="text" name="concepto" id="concepto" maxlength="255"
                       value="<?= $valorCampo('concepto') ?>">
                <?= fieldError($errores, 'concepto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'importe') ?>">
                <label for="importe">Importe (€) <span style="color:var(--rojo)">*</span></label>
                <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                       value="<?= $valorCampo('importe') ?>">
                <?= fieldError($errores, 'importe') ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'fecha') ?>">
                <label for="fecha">Fecha del gasto <span style="color:var(--rojo)">*</span></label>
                <input type="date" name="fecha" id="fecha" value="<?= $valorCampo('fecha') ?>">
                <?= fieldError($errores, 'fecha') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'idCategoria') ?>">
                <label for="idCategoria">Categoría <span style="color:var(--rojo)">*</span></label>
                <select name="idCategoria" id="idCategoria">
                    <option value="">— Selecciona —</option>
                    <?php
                    $selCat = $datos['idCategoria'] ?? $gasto['idCategoria'] ?? '';
                    foreach ($categorias as $categoria): ?>
                    <option value="<?= (int)$categoria['idCategoria'] ?>"
                        <?= ((string)$selCat === (string)$categoria['idCategoria']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($categoria['nombre']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?= fieldError($errores, 'idCategoria') ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="campo">
                <label for="tipoJustificante">Tipo de justificante</label>
                <select name="tipoJustificante" id="tipoJustificante">
                    <?php
                    $selTipo = $datos['tipoJustificante'] ?? $gasto['tipoJustificante'] ?? 'factura';
                    foreach (['factura' => 'Factura', 'ticket' => 'Ticket', 'recibo' => 'Recibo', 'otro' => 'Otro'] as $val => $lbl): ?>
                    <option value="<?= Security::escapeHtml($val) ?>" <?= ($selTipo === $val) ? 'selected' : '' ?>><?= Security::escapeHtml($lbl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="numeroReferencia">Nº Factura / Referencia</label>
                <input type="text" name="numeroReferencia" id="numeroReferencia" maxlength="100"
                       value="<?= $valorCampo('numeroReferencia') ?>">
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo asociado <span class="texto-suave">(opcional)</span></label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">— Sin ciclo específico —</option>
                    <?php
                    $selCiclo = $datos['idCiclo'] ?? $gasto['idCiclo'] ?? '';
                    foreach ($ciclos as $ciclo): ?>
                    <option value="<?= (int)$ciclo['idCiclo'] ?>"
                        <?= ((string)$selCiclo === (string)$ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['abreviaturaCiclo'] ?: $ciclo['idCiclo']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="campo ancho-total">
            <label>Justificante</label>
            <?php if (!empty($gasto['archivoJustificante'])):
                $archivos = json_decode($gasto['archivoJustificante'], true);
                if (is_array($archivos)):
                    foreach ($archivos as $i => $archivo):
                        $archivoUrl = R2Client::documentoUrl(
                            __DIR__ . '/../../../public/uploads/justificantes/' . $archivo,
                            '../../../public/uploads/justificantes/' . $archivo,
                            'justificantes/' . $archivo
                        ); ?>
                    <div class="file-item-premium" style="margin-bottom:10px;max-width:360px;">
                        <i class="fas fa-file-alt" style="color:var(--accent);font-size:1.2rem;flex-shrink:0;"></i>
                        <div class="file-info-premium">
                            <span class="file-name-premium">Archivo actual <?= count($archivos)>1 ? ($i+1) : '' ?></span>
                            <span class="file-type-premium">
                                <a href="<?= Security::escapeHtml($archivoUrl) ?>"
                                   target="_blank" rel="noopener">Ver archivo adjunto</a>
                            </span>
                        </div>
                    </div>
                    <?php endforeach;
                else:
                    $archivoUrl = R2Client::documentoUrl(
                        __DIR__ . '/../../../public/uploads/justificantes/' . $gasto['archivoJustificante'],
                        '../../../public/uploads/justificantes/' . $gasto['archivoJustificante'],
                        'justificantes/' . $gasto['archivoJustificante']
                    ); ?>
                    <div class="file-item-premium" style="margin-bottom:10px;max-width:360px;">
                        <i class="fas fa-file-alt" style="color:var(--accent);font-size:1.2rem;flex-shrink:0;"></i>
                        <div class="file-info-premium">
                            <span class="file-name-premium">Archivo actual</span>
                            <span class="file-type-premium">
                                <a href="<?= Security::escapeHtml($archivoUrl) ?>"
                                   target="_blank" rel="noopener">Ver archivo adjunto</a>
                            </span>
                        </div>
                    </div>
                <?php endif;
            endif; ?>
            <label class="zona-subida" for="archivoJustificante">
                <i class="fas fa-file-upload"></i>
                <span><?= !empty($gasto['archivoJustificante']) ? 'Reemplazar archivos' : 'Adjuntar justificantes' ?></span>
                <small>PDF, JPG, PNG — máx. 8 MB por archivo</small>
                <input type="file" name="archivoJustificante[]" id="archivoJustificante"
                       accept=".pdf,.jpg,.jpeg,.png,.webp" multiple style="display:none">
            </label>
            <ul id="lista-archivos" style="list-style: none; padding: 0; margin: 10px 0; font-size: 0.9em; color: var(--text-mut);"></ul>
            <div id="upload-progress-container" style="display: none; margin-top: 10px;">
                <div style="background: var(--border); border-radius: 4px; overflow: hidden; height: 8px;">
                    <div id="upload-progress-bar" style="background: var(--primary); width: 0%; height: 100%; transition: width 0.2s ease;"></div>
                </div>
                <small id="upload-progress-text" style="color: var(--text-mut); display: block; margin-top: 4px; text-align: center;">0%</small>
            </div>
        </div>

        <div class="campo campo-ancho-total">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="3"><?= Security::escapeHtml($gasto['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="acciones" style="margin-top:1.5rem;">
            <button type="submit" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <a href="verGastos.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<script>
document.getElementById('archivoJustificante').addEventListener('change', function () {
    var lista = document.getElementById('lista-archivos');
    lista.innerHTML = '';
    if (this.files.length > 0) {
        Array.from(this.files).forEach(file => {
            var li = document.createElement('li');
            li.innerHTML = '<i class="fas fa-file-alt" style="margin-right: 6px;"></i>' + file.name;
            lista.appendChild(li);
        });
    }
});

(function () {
    var form = document.querySelector('form');
    var btn  = form.querySelector('button[type="submit"]');
    var progressContainer = document.getElementById('upload-progress-container');
    var progressBar = document.getElementById('upload-progress-bar');
    var progressText = document.getElementById('upload-progress-text');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        btn.disabled = true;
        btn.classList.add('cargando');

        var hasFiles = document.getElementById('archivoJustificante').files.length > 0;
        if (hasFiles) {
            progressContainer.style.display = 'block';
            progressBar.style.width = '0%';
            progressText.textContent = '0%';
        }

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        new FormData(form),
            processData: false,
            contentType: false,
            headers:     { 'X-Requested-With': 'XMLHttpRequest' },
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                if (hasFiles) {
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            var percentComplete = Math.round((evt.loaded / evt.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = percentComplete + '%';
                        }
                    }, false);
                }
                return xhr;
            }
        })
        .done(function (res) {
            btn.disabled = false;
            btn.classList.remove('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                setTimeout(function () { location.href = 'verGastos.php'; }, 800);
            } else {
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al guardar', 'error');
                progressContainer.style.display = 'none';
            }
        })
        .fail(function (jqXHR) {
            btn.disabled = false;
            btn.classList.remove('cargando');
            progressContainer.style.display = 'none';
            // 401/403/0/5xx ya muestran su propio toast en el manejador global de footer.php
            if (jqXHR.status === 401 || jqXHR.status === 403 || jqXHR.status === 0 || jqXHR.status >= 500) return;
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>
