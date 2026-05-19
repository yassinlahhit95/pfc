<?php
session_start();
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id_pago = $_GET['idPago'] ?? '';
$pago = obtenerPagoPorId($id_pago);

if (!$pago) {
    header("Location: verPagosGeneral.php");
    exit;
}

if (isset($_SESSION['datos_pago'])) {
    $pago = array_merge($pago, $_SESSION['datos_pago']);
}

$todos_los_estudiantes = listarEstudiantes();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_pago']);

$titulo_pagina = "AULAPRO | MODIFICAR PAGO";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR PAGO</h1>
    <a href="verPagosGeneral.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/pagos/actualizar.php">
        <input type="hidden" name="idPago" value="<?= $id_pago ?>">
        
        <div class="formulario">
            <div class="campo">
                <label for="idEstudiante">Estudiante</label>
                <select name="idEstudiante" id="idEstudiante">
                    <?php foreach ($todos_los_estudiantes as $estudiante) { ?>
                        <option value="<?= $estudiante['idEstudiante'] ?>" <?= $pago['idEstudiante'] == $estudiante['idEstudiante'] ? 'selected' : '' ?>>
                            <?= $estudiante['nombreEstudiante'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['idEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="tipoPago">Tipo de Pago</label>
                <select name="tipoPago" id="tipoPago">
                    <option value="mensual" <?= $pago['tipoPago'] == 'mensual' ? 'selected' : '' ?>>Mensual</option>
                    <option value="trimestral" <?= $pago['tipoPago'] == 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="semestral" <?= $pago['tipoPago'] == 'semestral' ? 'selected' : '' ?>>Semestral</option>
                    <option value="unico" <?= $pago['tipoPago'] == 'unico' ? 'selected' : '' ?>>Único</option>
                </select>
            </div>

            <div class="campo">
                <label for="cantidadPago">Cantidad (Monto)</label>
                <input type="number" name="cantidadPago" id="cantidadPago" step="0.01" value="<?= $pago['monto'] ?>">
                <?php if (isset($errores['cantidadPago'])) { ?>
                    <strong class="error-campo"><?= $errores['cantidadPago'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaPago">Fecha de Pago</label>
                <input type="date" name="fechaPago" id="fechaPago" value="<?= $pago['fechaPago'] ?>">
                <?php if (isset($errores['fechaPago'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaPago'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaProximoPago">Próxima Fecha de Pago</label>
                <input type="date" name="fechaProximoPago" id="fechaProximoPago" value="<?= $pago['fechaProximoPago'] ?>">
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPago" class="boton-primario" value="Guardar Cambios">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
