<?php
session_start();
$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/conexion.php";
require_once "../../modelos/estudiantes.php";

$con = new Conexion();
$db = $con->conectar();
$estudianteObj = new estudiante($db);
$listaEstudiantes = $estudianteObj->listarEstudiantesModelo();
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <p class="subtitulo-encabezado">Seleccione un estudiante y detalle el cobro</p>
</div>

<div class="contenedor-formulario">
    <form action="controlador/pagosControlador.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="insertar">

        <div class="cuadricula-formulario">
            <div class="grupo-formulario ancho-completo">
                <label>Estudiante</label>
                <select name="idEstudiante" required>
                    <option value="">-- Seleccione un Estudiante --</option>
                    <?php 
                    foreach ($listaEstudiantes as $e) { ?>
                        <option value="<?php echo $e['idEstudiante']; ?>">
                            <?php echo htmlspecialchars($e['nombreEstudiante']); ?> (<?php echo htmlspecialchars($e['dniEstudiante']); ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Concepto</label>
                <input type="text" name="concepto" required placeholder="Ej: Mensualidad Abril 2026">
            </div>

            <div class="grupo-formulario">
                <label>Monto (€)</label>
                <input type="number" step="0.01" name="monto" required value="0.00">
            </div>

            <div class="grupo-formulario">
                <label>Tipo de Pago</label>
                <select name="tipoPago">
                    <option value="mensual">Mensual</option>
                    <option value="trimestral">Trimestral</option>
                    <option value="semestral">Semestral</option>
                    <option value="unico">Pago Único</option>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Estado</label>
                <select name="estadoPago">
                    <option value="pendiente">Pendiente</option>
                    <option value="pagado">Pagado</option>
                </select>
            </div>

            <div class="grupo-formulario">
                <label>Fecha de Pago</label>
                <input type="date" name="fechaPago" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Comprobante (Imagen o PDF)</label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/pagos/verPagosGeneral.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" class="boton-primario">Registrar Pago</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
