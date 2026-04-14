<?php
session_start();
$titulo_pagina = "Modificar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/pagos.php";
require_once "../../modelos/estudiantes.php";

$idPago = $_GET['id'];
$con = new Conexion();
$db = $con->conectar();

$pagoObj = new pago($db);
$datosPago = $pagoObj->obtenerPagoPorId($idPago);

$estudianteObj = new estudiante($db);
$datosEstudiante = $estudianteObj->obtenerEstudiantePorIdModelo($datosPago['idEstudiante']);

$errores = $_SESSION['errores'] ?? [];
$datos_sesion = $_SESSION['datos_pagos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_pagos']);

// Usar datos de sesión si existen (por error de validación), sino usar datos de la BD
$concepto = $datos_sesion['concepto'] ?? $datosPago['concepto'];
$monto = $datos_sesion['monto'] ?? $datosPago['monto'];
$tipoPago = $datos_sesion['tipoPago'] ?? $datosPago['tipoPago'];
$estadoPago = $datos_sesion['estadoPago'] ?? $datosPago['estadoPago'];
$fechaPago = $datos_sesion['fechaPago'] ?? $datosPago['fechaPago'];
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago</h1>
    <p class="subtitulo-encabezado">Editando pago de: <?php echo htmlspecialchars($datosEstudiante['nombreEstudiante']); ?></p>
</div>

<div class="contenedor-formulario">
    <form action="controlador/pagosControlador.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idPago" value="<?php echo $idPago; ?>">
        <input type="hidden" name="idEstudiante" value="<?php echo $datosPago['idEstudiante']; ?>">

        <div class="cuadricula-formulario">
            <div class="grupo-formulario ancho-completo">
                <label>Estudiante (No editable)</label>
                <input type="text" value="<?php echo htmlspecialchars($datosEstudiante['nombreEstudiante']); ?>" disabled>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Concepto</label>
                <input type="text" name="concepto" value="<?php echo htmlspecialchars($concepto); ?>">
                <?php if (isset($errores['concepto'])): ?>
                    <p style="color: red;"><?php echo $errores['concepto']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Monto (€)</label>
                <input type="number" step="0.01" name="monto" value="<?php echo htmlspecialchars($monto); ?>">
                <?php if (isset($errores['monto'])): ?>
                    <p style="color: red;"><?php echo $errores['monto']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Tipo de Pago</label>
                <select name="tipoPago">
                    <option value="mensual" <?php if($tipoPago == 'mensual') echo 'selected'; ?>>Mensual</option>
                    <option value="trimestral" <?php if($tipoPago == 'trimestral') echo 'selected'; ?>>Trimestral</option>
                    <option value="semestral" <?php if($tipoPago == 'semestral') echo 'selected'; ?>>Semestral</option>
                    <option value="unico" <?php if($tipoPago == 'unico') echo 'selected'; ?>>Pago Único</option>
                </select>
                <?php if (isset($errores['tipoPago'])): ?>
                    <p style="color: red;"><?php echo $errores['tipoPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Estado</label>
                <select name="estadoPago">
                    <option value="pendiente" <?php if($estadoPago == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="pagado" <?php if($estadoPago == 'pagado') echo 'selected'; ?>>Pagado</option>
                </select>
                <?php if (isset($errores['estadoPago'])): ?>
                    <p style="color: red;"><?php echo $errores['estadoPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Fecha de Pago</label>
                <input type="date" name="fechaPago" value="<?php echo htmlspecialchars($fechaPago); ?>">
                <?php if (isset($errores['fechaPago'])): ?>
                    <p style="color: red;"><?php echo $errores['fechaPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Comprobante Actual: <?php echo $datosPago['comprobante'] ? $datosPago['comprobante'] : 'Ninguno'; ?></label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
                <p class="text-xs text-muted mt-5">Deje vacío para mantener el archivo actual.</p>
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/pagos/verPagosGeneral.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" name="guardarPago" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
