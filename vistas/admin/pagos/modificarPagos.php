<?php
session_start();
$titulo_pagina = "Modificar Pago - Super Admin";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$id_pago = $_GET['idPago'];
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
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago</h1>
    <a href="verPagosGeneral.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/pagos/actualizar.php">
        <input type="hidden" name="idPago" value="<?php echo $id_pago; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <?php foreach ($todos_los_estudiantes as $estudiante) { ?>
                        <option value="<?php echo $estudiante['idEstudiante']; ?>" <?php if($pago['idEstudiante'] == $estudiante['idEstudiante']) echo "selected"; ?>>
                            <?php echo $estudiante['nombreEstudiante']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Tipo de Pago *</label>
                <select name="tipoPago">
                    <option value="mensual" <?php if($pago['tipoPago'] == 'mensual') echo "selected"; ?>>Mensual</option>
                    <option value="trimestral" <?php if($pago['tipoPago'] == 'trimestral') echo "selected"; ?>>Trimestral</option>
                    <option value="semestral" <?php if($pago['tipoPago'] == 'semestral') echo "selected"; ?>>Semestral</option>
                    <option value="unico" <?php if($pago['tipoPago'] == 'unico') echo "selected"; ?>>Único</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Cantidad (Monto) *</label>
                <input type="number" name="cantidadPago" step="0.01" value="<?php echo $pago['monto']; ?>">
                <?php if (isset($lista_de_errores['cantidadPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['cantidadPago']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago *</label>
                <input type="date" name="fechaPago" value="<?php echo $pago['fechaPago']; ?>">
                <?php if (isset($lista_de_errores['fechaPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaPago']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Próxima Fecha de Pago</label>
                <input type="date" name="fechaProximoPago" value="<?php echo $pago['fechaProximoPago']; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarPago" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

