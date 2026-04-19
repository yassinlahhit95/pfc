<?php
session_start();
$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/estudiantes.php";

$listaEstudiantes = listarEstudiantes();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_pagos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_pagos']);

// Variables simples (Estudiante way)
$idElegido = $datos['idEstudiante'] ?? '';
$concepto = $datos['concepto'] ?? '';
$monto = $datos['monto'] ?? '0.00';
$tipoElegido = $datos['tipoPago'] ?? '';
$estadoElegido = $datos['estadoPago'] ?? '';
$fecha = $datos['fechaPago'] ?? date('Y-m-d');
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <p class="subtitulo-encabezado">Seleccione un estudiante y detalle el cobro</p>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/pagos/insertar.php" method="POST" enctype="multipart/form-data">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un Estudiante --</option>
                    <?php foreach ($listaEstudiantes as $estudiante) { ?>
                        <option value="<?php echo $estudiante['idEstudiante']; ?>" <?php if ($idElegido == $estudiante['idEstudiante']) { echo 'selected'; } ?>>
                            <?php echo $estudiante['nombreEstudiante']; ?> (<?php echo $estudiante['dniEstudiante']; ?>)
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])) { ?>
                    <p class="error-campo"><?php echo $errores['idEstudiante']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Concepto *</label>
                <input type="text" name="concepto" placeholder="Ej: Mensualidad Abril 2026" value="<?php echo $concepto; ?>">
                <?php if (isset($errores['concepto'])) { ?>
                    <p class="error-campo"><?php echo $errores['concepto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Monto (€) *</label>
                <input type="text" name="monto" value="<?php echo $monto; ?>">
                <?php if (isset($errores['monto'])) { ?>
                    <p class="error-campo"><?php echo $errores['monto']; ?></p>
                <?php } ?>
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
                <label>Estado *</label>
                <select name="estadoPago">
                    <option value="pendiente" <?php if ($estadoElegido == 'pendiente') { echo 'selected'; } ?>>Pendiente</option>
                    <option value="pagado" <?php if ($estadoElegido == 'pagado') { echo 'selected'; } ?>>Pagado</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago *</label>
                <input type="date" name="fechaPago" value="<?php echo $fecha; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Comprobante (Imagen o PDF)</label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
            </div>
        </div>

        <div class="margen-arriba">
            <a href="vistas/pagos/verPagosGeneral.php" class="boton-secundario">Cancelar</a>
            <button type="submit" name="guardarPago" class="boton-primario">Registrar Pago</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
