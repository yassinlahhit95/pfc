<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id_pago = (int)($_GET['idPago'] ?? 0);
$pago = obtenerPagoPorId($id_pago);

if (!$pago) {
    header("Location: verPagosGeneral.php");
    exit;
}

if (isset($_SESSION['datos_pago'])) {
    $pago = array_merge($pago, $_SESSION['datos_pago']);
}
unset($_SESSION['datos_pago']);

$todos_los_estudiantes = listarEstudiantes();

$titulo_pagina = "AULAPRO | MODIFICAR PAGO";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR PAGO</h1>
    <a href="verPagosGeneral.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/pagos/actualizar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idPago" value="<?= $id_pago ?>">
        
        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'idEstudiante') ?>">
                <label for="idEstudiante">Estudiante</label>
                <select name="idEstudiante" id="idEstudiante">
                    <?php foreach ($todos_los_estudiantes as $estudiante) { ?>
                        <option value="<?= (int)$estudiante['idEstudiante'] ?>" <?= $pago['idEstudiante'] == $estudiante['idEstudiante'] ? 'selected' : '' ?>>
                            <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
                        </option>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idEstudiante') ?>
            </div>

            <div class="campo">
                <label for="tipoPago">Tipo de Pago</label>
                <select name="tipoPago" id="tipoPago">
                    <option value="mensual" <?= ($pago['tipoPago'] ?? '') == 'mensual' ? 'selected' : '' ?>>Mensual</option>
                    <option value="trimestral" <?= ($pago['tipoPago'] ?? '') == 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="semestral" <?= ($pago['tipoPago'] ?? '') == 'semestral' ? 'selected' : '' ?>>Semestral</option>
                    <option value="unico" <?= ($pago['tipoPago'] ?? '') == 'unico' ? 'selected' : '' ?>>Único</option>
                </select>
            </div>

            <div class="campo<?= fieldClass($errores, 'cantidadPago') ?>">
                <label for="cantidadPago">Cantidad (Monto)</label>
                <input type="number" name="cantidadPago" id="cantidadPago" step="0.01" value="<?= Security::escapeHtml($pago['cantidadPago'] ?? $pago['monto'] ?? '') ?>">
                <?= fieldError($errores, 'cantidadPago') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaPago') ?>">
                <label for="fechaPago">Fecha de Pago</label>
                <input type="date" name="fechaPago" id="fechaPago" value="<?= Security::escapeHtml($pago['fechaPago'] ?? '') ?>">
                <?= fieldError($errores, 'fechaPago') ?>
            </div>

            <div class="campo">
                <label for="fechaProximoPago">Próxima Fecha de Pago</label>
                <input type="date" name="fechaProximoPago" id="fechaProximoPago" value="<?= Security::escapeHtml($pago['fechaProximoPago'] ?? '') ?>">
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPago" class="boton-primario" value="Guardar Cambios">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
