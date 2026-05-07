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
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_pago']);

$titulo_pagina = "Modificar Pago - Admin";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago</h1>
    <a href="verPagosGeneral.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/pagos/actualizar.php">
        <input type="hidden" name="idPago" value="<?= $id_pago ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="idEstudiante">Estudiante *</label>
                <select name="idEstudiante" id="idEstudiante" required>
                    <?php foreach ($todos_los_estudiantes as $estudiante) { ?>
                        <option value="<?= $estudiante['idEstudiante'] ?>" <?= $pago['idEstudiante'] == $estudiante['idEstudiante'] ? 'selected' : '' ?>>
                            <?= $estudiante['nombreEstudiante'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idEstudiante'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['idEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="tipoPago">Tipo de Pago *</label>
                <select name="tipoPago" id="tipoPago" required>
                    <option value="mensual" <?= $pago['tipoPago'] == 'mensual' ? 'selected' : '' ?>>Mensual</option>
                    <option value="trimestral" <?= $pago['tipoPago'] == 'trimestral' ? 'selected' : '' ?>>Trimestral</option>
                    <option value="semestral" <?= $pago['tipoPago'] == 'semestral' ? 'selected' : '' ?>>Semestral</option>
                    <option value="unico" <?= $pago['tipoPago'] == 'unico' ? 'selected' : '' ?>>Único</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label for="cantidadPago">Cantidad (Monto) *</label>
                <input type="number" name="cantidadPago" id="cantidadPago" step="0.01" value="<?= $pago['monto'] ?>" required>
                <?php if (isset($lista_de_errores['cantidadPago'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['cantidadPago'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaPago">Fecha de Pago *</label>
                <input type="date" name="fechaPago" id="fechaPago" value="<?= $pago['fechaPago'] ?>" required>
                <?php if (isset($lista_de_errores['fechaPago'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaPago'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaProximoPago">Próxima Fecha de Pago</label>
                <input type="date" name="fechaProximoPago" id="fechaProximoPago" value="<?= $pago['fechaProximoPago'] ?>">
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPago" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>



