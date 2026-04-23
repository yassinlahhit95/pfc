<?php
session_start();
$titulo_pagina = "Modificar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/pagos.php";
require_once "../../../modelos/estudiantes.php";

$id_pago = $_GET['idPago'];
$pago = obtenerPagoPorId($id_pago);

if (!$pago) {
    header("Location: verPagosGeneral.php");
    exit;
}

if (isset($_SESSION['datos_pago'])) {
    $pago = $_SESSION['datos_pago'];
}

$todos_los_estudiantes = listarEstudiantes();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_pago']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago</h1>
    <a href="verPagosGeneral.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
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
                <label>Concepto *</label>
                <input type="text" name="conceptoPago" value="<?php echo $pago['conceptoPago']; ?>">
                <?php if (isset($lista_de_errores['conceptoPago'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['conceptoPago']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Cantidad *</label>
                <input type="text" name="cantidadPago" value="<?php echo $pago['cantidadPago']; ?>">
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
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarPago" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
