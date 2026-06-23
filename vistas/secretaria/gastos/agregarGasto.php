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
    <form method="POST" action="../../../controladores/secretaria/gastos/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'concepto') ?>">
            <label for="concepto">Concepto <span style="color:#ef4444">*</span></label>
            <input type="text" name="concepto" id="concepto" maxlength="255"
                   placeholder="Ej: Compra de papel y bolígrafos"
                   value="<?= Security::escapeHtml($datos['concepto'] ?? '') ?>">
            <?= fieldError($errores, 'concepto') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'importe') ?>">
            <label for="importe">Importe (€) <span style="color:#ef4444">*</span></label>
            <input type="number" name="importe" id="importe" step="0.01" min="0.01"
                   placeholder="0.00"
                   value="<?= Security::escapeHtml($datos['importe'] ?? '') ?>">
            <?= fieldError($errores, 'importe') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fecha') ?>">
            <label for="fecha">Fecha del gasto <span style="color:#ef4444">*</span></label>
            <input type="date" name="fecha" id="fecha"
                   value="<?= Security::escapeHtml($datos['fecha'] ?? date('Y-m-d')) ?>">
            <?= fieldError($errores, 'fecha') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'idCategoria') ?>">
            <label for="idCategoria">Categoría <span style="color:#ef4444">*</span></label>
            <select name="idCategoria" id="idCategoria">
                <option value="">— Selecciona —</option>
                <?php foreach ($categorias as $cat): ?>
                <option value="<?= (int)$cat['idCategoria'] ?>"
                    <?= ((string)($datos['idCategoria'] ?? '') === (string)$cat['idCategoria']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($cat['nombre']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idCategoria') ?>
        </div>

        <div class="campo">
            <label for="tipoJustificante">Tipo de justificante</label>
            <select name="tipoJustificante" id="tipoJustificante">
                <?php foreach (['factura' => 'Factura', 'ticket' => 'Ticket', 'recibo' => 'Recibo', 'otro' => 'Otro'] as $val => $lbl): ?>
                <option value="<?= $val ?>" <?= (($datos['tipoJustificante'] ?? '') === $val) ? 'selected' : '' ?>><?= $lbl ?></option>
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
                <?php foreach ($ciclos as $c): ?>
                <option value="<?= (int)$c['idCiclo'] ?>"
                    <?= ((string)($datos['idCiclo'] ?? '') === (string)$c['idCiclo']) ? 'selected' : '' ?>>
                    [<?= Security::escapeHtml($c['abreviaturaCiclo'] ?: $c['idCiclo']) ?>] <?= Security::escapeHtml($c['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
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

<?php include '../comunes/footer.php'; ?>
