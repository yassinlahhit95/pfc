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
            <label for="concepto">Concepto <span style="color:#ef4444">*</span></label>
            <input type="text" name="concepto" id="concepto" maxlength="255"
                   placeholder="Ej: Compra de papel y bolígrafos" required>
        </div>

        <div class="campo">
            <label for="importe">Importe (€) <span style="color:#ef4444">*</span></label>
            <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                   placeholder="0.00" required>
        </div>

        <div class="campo">
            <label for="fecha">Fecha del gasto <span style="color:#ef4444">*</span></label>
            <input type="date" name="fecha" id="fecha" value="<?= date('Y-m-d') ?>" required>
        </div>

        <div class="campo">
            <label for="idCategoria">Categoría <span style="color:#ef4444">*</span></label>
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
                <span>Adjuntar factura, ticket o recibo</span>
                <small>PDF, JPG, PNG — opcional</small>
                <input type="file" name="archivoJustificante" id="archivoJustificante"
                       accept=".pdf,.jpg,.jpeg,.png,.webp" style="display:none">
            </label>
            <small id="archivo-seleccionado" class="texto-suave"></small>
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
    var label = document.getElementById('archivo-seleccionado');
    label.textContent = this.files.length ? this.files[0].name : '';
});

(function () {
    var form = document.querySelector('form');
    var btn  = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        btn.disabled = true;
        btn.classList.add('cargando');

        $.ajax({
            url:         form.action,
            type:        'POST',
            data:        new FormData(form),
            processData: false,
            contentType: false,
            headers:     { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .done(function (res) {
            btn.disabled = false;
            btn.classList.remove('cargando');
            if (res && res.ok) {
                if (window.Toast) Toast.show(res.msg, 'success');
                setTimeout(function () { location.href = 'verGastos.php'; }, 800);
            } else {
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al guardar', 'error');
            }
        })
        .fail(function () {
            btn.disabled = false;
            btn.classList.remove('cargando');
            if (window.Toast) Toast.show('Error de conexión', 'error');
        });
    });
}());
</script>
