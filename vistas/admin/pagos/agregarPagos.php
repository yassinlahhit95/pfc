<?php
session_start();
$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";

require_once "../../../modelos/conectar.php";
require_once "../../../modelos/estudiantes.php";

$listaEstudiantes = listarEstudiantes();

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['error']);

$fechaHoy = date('Y-m-d');
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <p class="subtitulo-encabezado">Gestión de cobros y recordatorio de próximos pagos</p>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/pagos/insertar.php" method="POST" enctype="multipart/form-data">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Estudiante *</label>
                <select name="idEstudiante">
                    <option value="">Seleccione un Estudiante</option>
                    <?php foreach ($listaEstudiantes as $estudiante) { ?>
                        <option value="<?php echo $estudiante['idEstudiante']; ?>">
                            <?php echo $estudiante['nombreEstudiante']; ?> (<?php echo $estudiante['dniEstudiante']; ?>)
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Monto (€) *</label>
                <input type="text" name="monto" placeholder="50.00">
            </div>

            <div class="campo-formulario">
                <label>Tipo de Pago *</label>
                <select name="tipoPago">
                    <option value="mensual">Mensual</option>
                    <option value="trimestral">Trimestral</option>
                    <option value="semestral">Semestral</option>
                    <option value="unico">Pago Único</option>
                </select>
                <small class="texto-atenuado">El sistema calculará automáticamente la fecha del próximo cobro.</small>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago Realizado</label>
                <!-- Se muestra hoy automáticamente en formato español -->
                <input type="text" value="<?php echo date('d/m/Y'); ?>" readonly style="background-color: #f9f9f9; color: #666;">
                <input type="hidden" name="fechaPago" value="<?php echo $fechaHoy; ?>">
            </div>

            <div class="campo-formulario">
                <label>Comprobante (Opcional)</label>
                <input type="file" name="comprobante" accept="image/*,.pdf">
            </div>
        </div>

        <div class="margen-arriba">
            <a href="/pfc/vistas/admin/pagos/verPagosGeneral.php" class="boton-secundario">Cancelar</a>
            <button type="submit" name="guardarPago" class="boton-primario">Registrar Pago</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>