<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_pago'] ?? [];
unset($_SESSION['datos_pago']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
$estudiantes = listarEstudiantes();

$titulo_pagina = 'AULAPRO | REGISTRAR PAGO';
$seccion = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>REGISTRAR PAGO</h1>
    <a href="verPagos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/pagos/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'idEstudiante') ?>">
            <label for="idEstudiante">Estudiante</label>
            <select name="idEstudiante" id="idEstudiante">
                <option value="">— Seleccionar —</option>
                <?php foreach ($estudiantes as $est): ?>
                <option value="<?= (int)$est['idEstudiante'] ?>" <?= ((int)($datos['idEstudiante'] ?? 0) === (int)$est['idEstudiante']) ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($est['nombreEstudiante']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'idEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'monto') ?>">
            <label for="monto">Importe (€)</label>
            <input type="number" name="monto" id="monto" step="0.01" min="0.01"
                   value="<?= Security::escapeHtml($datos['monto'] ?? '') ?>">
            <?= fieldError($errores, 'monto') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'tipoPago') ?>">
            <label for="tipoPago">Tipo de pago</label>
            <select name="tipoPago" id="tipoPago">
                <option value="">— Seleccionar —</option>
                <?php
                $tipos = ['cuota_mensual'=>'Cuota mensual','matricula'=>'Matrícula','material'=>'Material','otro'=>'Otro'];
                foreach ($tipos as $val => $label): ?>
                <option value="<?= $val ?>" <?= (($datos['tipoPago'] ?? '') === $val) ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <?= fieldError($errores, 'tipoPago') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaPago') ?>">
            <label for="fechaPago">Fecha de pago</label>
            <input type="date" name="fechaPago" id="fechaPago"
                   value="<?= Security::escapeHtml($datos['fechaPago'] ?? date('Y-m-d')) ?>">
            <?= fieldError($errores, 'fechaPago') ?>
        </div>

        <div class="campo">
            <label for="fechaProximoPago">Próximo pago <span class="texto-suave">(opcional)</span></label>
            <input type="date" name="fechaProximoPago" id="fechaProximoPago"
                   value="<?= Security::escapeHtml($datos['fechaProximoPago'] ?? '') ?>">
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="verPagos.php" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> Registrar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
