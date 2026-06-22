<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_gastos');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/gastos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idGasto = (int)($_GET['idGasto'] ?? 0);
$gasto   = obtenerGastoPorId($idGasto);

if (!$gasto) {
    header("Location: verGastos.php");
    exit;
}

$categorias = listarCategorias();
$ciclos     = listarTodosLosCiclos();

$titulo_pagina = "AULAPRO | MODIFICAR GASTO";
$seccion = 'gastos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR GASTO</h1>
    <a href="verGastos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/gastos/actualizar.php"
          class="formulario" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="actualizarGasto" value="1">
        <input type="hidden" name="idGasto" value="<?= (int)$gasto['idGasto'] ?>">

        <div class="campo">
            <label for="concepto">Concepto <span style="color:#ef4444">*</span></label>
            <input type="text" name="concepto" id="concepto" maxlength="255"
                   value="<?= Security::escapeHtml($gasto['concepto']) ?>" required>
        </div>

        <div class="campo">
            <label for="importe">Importe (€) <span style="color:#ef4444">*</span></label>
            <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                   value="<?= Security::escapeHtml($gasto['importe']) ?>" required>
        </div>

        <div class="campo">
            <label for="fecha">Fecha del gasto <span style="color:#ef4444">*</span></label>
            <input type="date" name="fecha" id="fecha"
                   value="<?= Security::escapeHtml($gasto['fecha']) ?>" required>
        </div>

        <div class="campo">
            <label for="idCategoria">Categoría <span style="color:#ef4444">*</span></label>
            <select name="idCategoria" id="idCategoria" required>
                <option value="">— Selecciona —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['idCategoria'] ?>"
                    <?= $gasto['idCategoria'] == $cat['idCategoria'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="tipoJustificante">Tipo de justificante</label>
            <select name="tipoJustificante" id="tipoJustificante">
                <?php foreach (['factura'=>'Factura','ticket'=>'Ticket','recibo'=>'Recibo','otro'=>'Otro'] as $val => $label): ?>
                <option value="<?= $val ?>" <?= $gasto['tipoJustificante'] === $val ? 'selected' : '' ?>>
                    <?= $label ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="numeroReferencia">Nº Factura / Referencia</label>
            <input type="text" name="numeroReferencia" id="numeroReferencia" maxlength="100"
                   value="<?= Security::escapeHtml($gasto['numeroReferencia'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="idCiclo">Ciclo asociado</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">— Sin ciclo específico —</option>
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>"
                    <?= $gasto['idCiclo'] == $c['idCiclo'] ? 'selected' : '' ?>>
                    [<?= Security::escapeHtml($c['abreviaturaCiclo'] ?: $c['idCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label>Justificante</label>
            <?php if (!empty($gasto['archivoJustificante'])): ?>
            <div class="file-item-premium" style="margin-bottom:10px;max-width:360px;">
                <i class="fas fa-file-alt" style="color:var(--accent);font-size:1.2rem;flex-shrink:0;"></i>
                <div class="file-info-premium">
                    <span class="file-name-premium">Archivo actual</span>
                    <span class="file-type-premium">
                        <a href="../../../public/uploads/justificantes/<?= Security::escapeHtml($gasto['archivoJustificante']) ?>"
                           target="_blank" rel="noopener">Ver archivo adjunto</a>
                    </span>
                </div>
            </div>
            <?php endif; ?>
            <label class="zona-subida" for="archivoJustificante">
                <i class="fas fa-file-upload"></i>
                <span><?= !empty($gasto['archivoJustificante']) ? 'Reemplazar archivo' : 'Adjuntar justificante' ?></span>
                <small>PDF, JPG, PNG — máx. 8 MB</small>
                <input type="file" name="archivoJustificante" id="archivoJustificante"
                       accept=".pdf,.jpg,.jpeg,.png,.webp" style="display:none">
            </label>
            <small id="archivo-seleccionado" class="texto-suave"></small>
        </div>

        <div class="campo campo-ancho-total">
            <label for="observaciones">Observaciones</label>
            <textarea name="observaciones" id="observaciones" rows="3"><?= Security::escapeHtml($gasto['observaciones'] ?? '') ?></textarea>
        </div>

        <div class="acciones">
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
                if (window.Toast) Toast.show((res && res.msg) ? res.msg : 'Error al actualizar', 'error');
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
