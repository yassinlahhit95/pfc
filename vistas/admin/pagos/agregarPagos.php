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
    <!-- Mensaje de aviso destacado arriba -->
    <div class="aviso-sistema-pago margen-abajo" style="background: #e7f3ff; border-left: 5px solid #2196F3; padding: 15px; border-radius: 4px;">
        <p style="margin: 0; color: #0d47a1; font-weight: 500;">
            <i class="fas fa-info-circle"></i> El sistema calculará automáticamente la fecha del próximo cobro.
        </p>
    </div>

    <form action="/pfc/controladores/admin/pagos/insertar.php" method="POST" enctype="multipart/form-data">
        <div class="formulario-cuadricula">
            
            <div class="campo-formulario campo-ancho-total">
                <label>Estudiante *</label>
                <!-- Filtro básico para buscar estudiante -->
                <input type="text" id="filtroEstudiante" onkeyup="filtrarLista()" placeholder="Buscar estudiante por nombre o DNI..." style="margin-bottom: 10px; border: 1px dashed #667eea;">
                
                <select name="idEstudiante" id="selectEstudiantes">
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
            </div>

            <div class="campo-formulario">
                <label>Fecha de Pago Realizado</label>
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

<script>
function filtrarLista() {
    var input = document.getElementById("filtroEstudiante");
    var filter = input.value.toLowerCase();
    var select = document.getElementById("selectEstudiantes");
    var options = select.options;

    for (var i = 1; i < options.length; i++) {
        var txtValue = options[i].text;
        if (txtValue.toLowerCase().indexOf(filter) > -1) {
            options[i].style.display = "";
        } else {
            options[i].style.display = "none";
        }
    }
}
</script>

<?php include '../comunes/footer.php'; ?>