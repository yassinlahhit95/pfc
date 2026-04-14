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
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago</h1>
    <p class="subtitulo-encabezado">Editando pago de: <?php echo htmlspecialchars($datosEstudiante['nombreEstudiante']); ?></p>
</div>

<div class="contenedor-formulario">
    <form action="controlador/pagosControlador.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idPago" value="<?php echo $idPago; ?>">

        <div class="cuadricula-formulario">
            <div class="grupo-formulario ancho-completo">
                <label>Estudiante (No editable)</label>
                <input type="text" value="<?php echo htmlspecialchars($datosEstudiante['nombreEstudiante']); ?>" disabled>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Concepto</label>
                <input type="text" name="concepto" required value="<?php echo htmlspecialchars($datosPago['concepto']); ?>">
            </div>

            <div class="grupo-formulario">
                <label>Monto (€)</label>
                <input type="number" step="0.01" name="monto" required value="<?php echo $datosPago['monto']; ?>">
            </div>

            <div class="grupo-formulario">
                <label>Tipo de Pago</label>
                <select name="tipoPago">
                    <option value="mensual" <?php if($datosPago['tipoPago'] == 'mensual') echo 'selected'; ?>>Mensual</option>
                    <option value="trimestral" <?php if($datosPago['tipoPago'] == 'trimestral') echo 'selected'; ?>>Trimestral</option>
                    <option value="semestral" <?php if($datosPago['tipoPago'] == 'semestral') echo 'selected'; ?>>Semestral</option>
                    <option value="unico" <?php if($datosPago['tipoPago'] == 'unico') echo 'selected'; ?>>Pago Único</option>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Estado</label>
                <select name="estadoPago">
                    <option value="pendiente" <?php if($datosPago['estadoPago'] == 'pendiente') echo 'selected'; ?>>Pendiente</option>
                    <option value="pagado" <?php if($datosPago['estadoPago'] == 'pagado') echo 'selected'; ?>>Pagado</option>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Fecha de Pago</label>
                <input type="date" name="fechaPago" value="<?php echo $datosPago['fechaPago']; ?>">
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Comprobante Actual: <?php echo $datosPago['comprobante'] ? $datosPago['comprobante'] : 'Ninguno'; ?></label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
                <p class="text-xs text-muted mt-5">Deje vacío para mantener el archivo actual.</p>
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/pagos/verPagosGeneral.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
