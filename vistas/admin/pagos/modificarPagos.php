<?php
session_start();
$titulo_pagina = "Modificar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/conectar.php";
require_once "../../../modelos/pagos.php";
require_once "../../../modelos/estudiantes.php";

$idDelPago = 0;
if (isset($_GET['idPago'])) {
    $idDelPago = $_GET['idPago'];
}

$datosPagoBD = obtenerPagoPorId($idDelPago);

if (!$datosPagoBD) {
    header("Location: /pfc/vistas/admin/pagos/verPagosGeneral.php");
    exit;
}

$listaEstudiantes = listarEstudiantes();

$idElegido = $datosPagoBD['idEstudiante'];
$monto = $datosPagoBD['monto'];
$tipoElegido = $datosPagoBD['tipoPago'];
$fecha = $datosPagoBD['fechaPago'];
?>

<div class="encabezado-pagina">
    <h1>Modificar Pago #<?php echo $idDelPago; ?></h1>
    <a href="/pfc/vistas/admin/pagos/verPagosGeneral.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/pagos/actualizar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="idPago" value="<?php echo $idDelPago; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <?php foreach ($listaEstudiantes as $estudiante) { ?>
                        <option value="<?php echo $estudiante['idEstudiante']; ?>" <?php if ($idElegido == $estudiante['idEstudiante']) { echo 'selected'; } ?>>
                            <?php echo $estudiante['nombreEstudiante']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Monto (€) *</label>
                <input type="text" name="monto" value="<?php echo $monto; ?>" placeholder="50.00">
            </div>

            <div class="campo-formulario">
                <label>Tipo de Pago *</label>
                <select name="tipoPago">
                    <option value="mensual" <?php if ($tipoElegido == 'mensual') { echo 'selected'; } ?>>Mensual</option>
                    <option value="trimestral" <?php if ($tipoElegido == 'trimestral') { echo 'selected'; } ?>>Trimestral</option>
                    <option value="semestral" <?php if ($tipoElegido == 'semestral') { echo 'selected'; } ?>>Semestral</option>
                    <option value="unico" <?php if ($tipoElegido == 'unico') { echo 'selected'; } ?>>Pago Único</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago Realizado *</label>
                <input type="date" name="fechaPago" value="<?php echo $fecha; ?>">
            </div>

            <div class="campo-formulario">
                <label>Comprobante actual: <?php 
                    if ($datosPagoBD['comprobante']) {
                        echo $datosPagoBD['comprobante'];
                    } else {
                        echo 'Ninguno';
                    }
                ?></label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarPago" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
