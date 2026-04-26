<?php
session_start();
require_once "../../../modelos/estudiantes.php";
require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/pagos.php";

$idCicloElegido = isset($_GET['idCiclo']) ? $_GET['idCiclo'] : '';
$idEstudianteElegido = isset($_GET['idEstudiante']) ? $_GET['idEstudiante'] : '';

$todos_los_ciclos = listarTodosLosCiclos();

// Filtrar estudiantes por ciclo si se ha seleccionado uno
if ($idCicloElegido != '') {
    $todos_los_estudiantes = listarEstudiantesPorCiclo($idCicloElegido);
} else {
    $todos_los_estudiantes = listarEstudiantes();
}

// Obtener info financiera si hay estudiante seleccionado
$infoFinanciera = null;
if ($idEstudianteElegido != '') {
    $infoFinanciera = obtenerEstadoFinancieroEstudiante($idEstudianteElegido);
}

// Regla: No se permiten pagos después del 30 de Junio
$hoy = date('Y-m-d');
$fechaLimite = date('Y') . '-06-30';
$esDespuesDeJunio = ($hoy > $fechaLimite);

$lista_de_errores = array();
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }
unset($_SESSION['errores']);

$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <a href="/pfc/vistas/admin/pagos/verPagosGeneral.php" class="boton-secundario">Volver</a>
</div>

<?php if ($esDespuesDeJunio) { ?>
    <div class="mensaje-error">
        <i class="fas fa-exclamation-triangle"></i> No se pueden registrar pagos después del 30 de Junio del año en curso.
    </div>
<?php } ?>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>1. Filtrar por Ciclo:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($idCicloElegido == $ciclo['idCiclo']) echo 'selected'; ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>2. Seleccionar Estudiante:</label>
                <select name="idEstudiante" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) { ?>
                        <option value="<?php echo $est['idEstudiante']; ?>" <?php if ($idEstudianteElegido == $est['idEstudiante']) echo 'selected'; ?>>
                            <?php echo $est['nombreEstudiante']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </form>
</div>

<?php if ($infoFinanciera) { ?>
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Estado Financiero de Estudiante</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label>Precio del Ciclo</label>
            <p><strong><?php echo number_format($infoFinanciera['precioCiclo'], 2); ?> €</strong></p>
        </div>
        <div class="campo-formulario">
            <label>Ya Pagado</label>
            <p><?php echo number_format($infoFinanciera['totalPagado'], 2); ?> €</p>
        </div>
        <div class="campo-formulario">
            <label>Pendiente</label>
            <p style="color: #d9534f; font-weight: bold;"><?php echo number_format($infoFinanciera['restante'], 2); ?> €</p>
        </div>
    </div>

    <hr class="separador">

    <?php if ($infoFinanciera['restante'] <= 0) { ?>
        <p class="mensaje-exito">Este estudiante ya ha completado todos los pagos del ciclo.</p>
    <?php } else if ($esDespuesDeJunio) { ?>
        <p class="mensaje-error">Periodo de pagos finalizado (30/06 superado).</p>
    <?php } else { ?>
        <form action="/pfc/controladores/admin/pagos/insertar.php" method="POST">
            <input type="hidden" name="idEstudiante" value="<?php echo $idEstudianteElegido; ?>">
            <input type="hidden" name="fechaPago" value="<?php echo $hoy; ?>">
            
            <div class="formulario-cuadricula">
                <div class="campo-formulario">
                    <label>Tipo de Pago *</label>
                    <select name="tipoPago" id="tipoPago" required onchange="actualizarMontoRapido()">
                        <option value="">-- Elegir --</option>
                        <option value="mensual">Mensual (10% del total)</option>
                        <option value="trimestral">Trimestral (25% del total)</option>
                        <option value="semestral">Semestral (50% del total)</option>
                        <option value="unico">Todo lo restante (<?php echo number_format($infoFinanciera['restante'], 2); ?> €)</option>
                    </select>
                </div>

                <div class="campo-formulario">
                    <label>Cantidad a Cobrar (€) *</label>
                    <input type="number" name="monto" id="montoInput" step="0.01" max="<?php echo $infoFinanciera['restante']; ?>" required>
                    <small>Máximo permitido: <?php echo $infoFinanciera['restante']; ?> €</small>
                </div>
            </div>

            <div class="margen-arriba">
                <button type="submit" name="guardarPago" class="boton-primario">
                    <i class="fas fa-check"></i> Confirmar y Registrar Pago (<?php echo date('d/m/Y', strtotime($hoy)); ?>)
                </button>
            </div>
        </form>
    <?php } ?>
</div>

<script>
// JavaScript muy simple para ayudar al usuario, no es AJAX
function actualizarMontoRapido() {
    const tipo = document.getElementById('tipoPago').value;
    const precioTotal = <?php echo $infoFinanciera['precioCiclo']; ?>;
    const restante = <?php echo $infoFinanciera['restante']; ?>;
    let cuota = 0;

    if (tipo === 'mensual') cuota = precioTotal / 10;
    else if (tipo === 'trimestral') cuota = precioTotal / 4;
    else if (tipo === 'semestral') cuota = precioTotal / 2;
    else if (tipo === 'unico') cuota = restante;

    if (cuota > restante) cuota = restante;
    
    document.getElementById('montoInput').value = cuota.toFixed(2);
}
</script>
<?php } ?>

<?php include '../comunes/footer.php'; ?>

