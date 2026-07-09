<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$categorias = listarCategorias();
$ciclos     = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | NUEVO GASTO";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO GASTO</h1>
    <a href="verGastos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/gastos/insertar.php"
          class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="insertarGasto" value="1">

        <div class="campo">
            <label for="concepto">Concepto <span style="color:var(--rojo)">*</span></label>
            <input type="text" name="concepto" id="concepto" maxlength="255"
                   placeholder="Ej: Compra de papel y bolígrafos" required>
        </div>

        <div class="campo">
            <label for="importe">Importe (€) <span style="color:var(--rojo)">*</span></label>
            <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                   placeholder="0.00" required>
        </div>

        <div class="campo">
            <label for="fecha">Fecha del gasto <span style="color:var(--rojo)">*</span></label>
            <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="campo">
            <label for="idCategoria">Categoría <span style="color:var(--rojo)">*</span></label>
            <select name="idCategoria" id="idCategoria" required>
                <option value="">— Selecciona —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['idCategoria'] ?>">
                    <?= Security::escapeHtml($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <small class="texto-suave">¿No encuentras la categoría? <a href="categorias.php">Gestiónala aquí</a></small>
        </div>

        <div class="campo">
            <label for="tipoJustificante">Tipo de justificante</label>
            <select name="tipoJustificante" id="tipoJustificante">
                <option value="factura">Factura</option>
                <option value="ticket">Ticket</option>
                <option value="recibo">Recibo</option>
                <option value="otro">Otro</option>
            </select>
        </div>

        <div class="campo">
            <label for="numeroReferencia">Nº Factura / Referencia</label>
            <input type="text" name="numeroReferencia" id="numeroReferencia" maxlength="100"
                   placeholder="Ej: FAC-2026-0123">
        </div>

        <div class="campo">
            <label for="idCiclo">Ciclo asociado <span class="texto-suave">(opcional)</span></label>
            <select name="idCiclo" id="idCiclo">
                <option value="">— Sin ciclo específico —</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>">
                    [<?= Security::escapeHtml($c['abreviaturaCiclo'] ?: $c['idCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
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
                      placeholder="Notas adicionales sobre este gasto..."></textarea>
        </div>

        <div class="acciones">
            <button type="submit" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR GASTO
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
        .fail(function () {
            btn.disabled = false;
            btn.classList.remove('cargando');
            progressContainer.style.display = 'none';
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>
