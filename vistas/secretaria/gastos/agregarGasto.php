<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$categorias = listarCategorias();
$ciclos     = listarTodosLosCiclos();
$datos      = $_SESSION['datos_gasto'] ?? [];
unset($_SESSION['datos_gasto']);

$titulo_pagina = "AULAPRO | NUEVO GASTO";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO GASTO</h1>
    <a href="verGastos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/secretaria/gastos/insertar.php" class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'concepto') ?>">
                <label for="concepto">Concepto <span style="color:var(--rojo)">*</span></label>
                <input type="text" name="concepto" id="concepto" maxlength="255"
                       placeholder="Ej: Compra de papel y bolígrafos"
                       value="<?= Security::escapeHtml($datos['concepto'] ?? '') ?>">
                <?= fieldError($errores, 'concepto') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'importe') ?>">
                <label for="importe">Importe (€) <span style="color:var(--rojo)">*</span></label>
                <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                       placeholder="0.00"
                       value="<?= Security::escapeHtml($datos['importe'] ?? '') ?>">
                <?= fieldError($errores, 'importe') ?>
            </div>
        </div>

        <div class="form-fila">
            <div class="campo<?= fieldClass($errores, 'fecha') ?>">
                <label for="fecha">Fecha del gasto <span style="color:var(--rojo)">*</span></label>
                <input type="date" name="fecha" id="fecha"
                       value="<?= Security::escapeHtml($datos['fecha'] ?? date('Y-m-d')) ?>">
                <?= fieldError($errores, 'fecha') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'idCategoria') ?>">
                <label for="idCategoria">Categoría <span style="color:var(--rojo)">*</span></label>
                <select name="idCategoria" id="idCategoria">
                    <option value="">— Selecciona —</option>
                    <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= (int)$categoria['idCategoria'] ?>"
                        <?= ((string)($datos['idCategoria'] ?? '') === (string)$categoria['idCategoria']) ? 'selected' : '' ?>>
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
                    <?php foreach (['factura' => 'Factura', 'ticket' => 'Ticket', 'recibo' => 'Recibo', 'otro' => 'Otro'] as $val => $lbl): ?>
                    <option value="<?= Security::escapeHtml($val) ?>" <?= (($datos['tipoJustificante'] ?? '') === $val) ? 'selected' : '' ?>><?= Security::escapeHtml($lbl) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="numeroReferencia">Nº Factura / Referencia</label>
                <input type="text" name="numeroReferencia" id="numeroReferencia" maxlength="100"
                       placeholder="Ej: FAC-2026-0123"
                       value="<?= Security::escapeHtml($datos['numeroReferencia'] ?? '') ?>">
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo asociado <span class="texto-suave">(opcional)</span></label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">— Sin ciclo específico —</option>
                    <?php foreach ($ciclos as $ciclo): ?>
                    <option value="<?= (int)$ciclo['idCiclo'] ?>"
                        <?= ((string)($datos['idCiclo'] ?? '') === (string)$ciclo['idCiclo']) ? 'selected' : '' ?>>
                        [<?= Security::escapeHtml($ciclo['abreviaturaCiclo'] ?: $ciclo['idCiclo']) ?>] <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="campo ancho-total">
            <label>Justificante <span class="texto-suave">(PDF o imagen, máx. 8 MB)</span></label>
            <label class="zona-subida" for="archivoJustificante">
                <i class="fas fa-file-upload"></i>
                <span>Adjuntar facturas, tickets o recibos</span>
                <small>PDF, JPG, PNG — puedes seleccionar varios</small>
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
            <textarea name="observaciones" id="observaciones" rows="3"
                      placeholder="Notas adicionales sobre este gasto..."><?= Security::escapeHtml($datos['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> REGISTRAR GASTO</button>
            <a href="verGastos.php" class="boton-secundario">Cancelar</a>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
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
