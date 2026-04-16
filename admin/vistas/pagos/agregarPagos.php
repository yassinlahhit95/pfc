<?php
session_start();
$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/estudiantes.php";

$estudianteObj = new estudiante();
$listaEstudiantes = $estudianteObj->listarEstudiantesModelo();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_pagos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_pagos']);
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <p class="subtitulo-encabezado">Seleccione un estudiante y detalle el cobro</p>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/pagos/insertar.php" method="POST" enctype="multipart/form-data">
        <div class="cuadricula-formulario">
            <div class="grupo-formulario ancho-completo">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">-- Seleccione un Estudiante --</option>
                    <?php 
                    foreach ($listaEstudiantes as $e) { 
                        $selected = ($datos['idEstudiante'] ?? '') == $e['idEstudiante'] ? 'selected' : '';
                        ?>
                        <option value="<?php echo $e['idEstudiante']; ?>" <?php echo $selected; ?>>
                            <?php echo htmlspecialchars($e['nombreEstudiante']); ?> (<?php echo htmlspecialchars($e['dniEstudiante']); ?>)
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idEstudiante'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['idEstudiante']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Concepto *</label>
                <input type="text" name="concepto" placeholder="Ej: Mensualidad Abril 2026" value="<?php echo htmlspecialchars($datos['concepto'] ?? ''); ?>">
                <?php if (isset($errores['concepto'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['concepto']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Monto (€) *</label>
                <input type="text" name="monto" value="<?php echo htmlspecialchars($datos['monto'] ?? '0.00'); ?>">
                <?php if (isset($errores['monto'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['monto']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Tipo de Pago *</label>
                <select name="tipoPago">
                    <option value="mensual" <?php echo ($datos['tipoPago'] ?? '') == 'mensual' ? 'selected' : ''; ?>>Mensual</option>
                    <option value="trimestral" <?php echo ($datos['tipoPago'] ?? '') == 'trimestral' ? 'selected' : ''; ?>>Trimestral</option>
                    <option value="semestral" <?php echo ($datos['tipoPago'] ?? '') == 'semestral' ? 'selected' : ''; ?>>Semestral</option>
                    <option value="unico" <?php echo ($datos['tipoPago'] ?? '') == 'unico' ? 'selected' : ''; ?>>Pago Único</option>
                </select>
                <?php if (isset($errores['tipoPago'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['tipoPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Estado *</label>
                <select name="estadoPago">
                    <option value="pendiente" <?php echo ($datos['estadoPago'] ?? '') == 'pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                    <option value="pagado" <?php echo ($datos['estadoPago'] ?? '') == 'pagado' ? 'selected' : ''; ?>>Pagado</option>
                </select>
                <?php if (isset($errores['estadoPago'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['estadoPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario">
                <label>Fecha de Pago *</label>
                <input type="date" name="fechaPago" value="<?php echo htmlspecialchars($datos['fechaPago'] ?? date('Y-m-d')); ?>">
                <?php if (isset($errores['fechaPago'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['fechaPago']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grupo-formulario ancho-completo">
                <label>Comprobante (Imagen o PDF)</label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
            </div>
        </div>

        <div class="acciones-formulario">
            <a href="vistas/pagos/verPagosGeneral.php" class="boton-cancelar">Cancelar</a>
            <button type="submit" name="guardarPago" class="boton-primario">Registrar Pago</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
