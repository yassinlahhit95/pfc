<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$idEstudianteElegido = $_GET['idEstudiante'] ?? '';

$todos_los_ciclos = listarTodosLosCiclos();

if (!empty($idCicloElegido)) {
    $todos_los_estudiantes = listarEstudiantesPorCiclo($idCicloElegido);
} else {
    $todos_los_estudiantes = listarEstudiantes();
}

$infoFinanciera = null;
if (!empty($idEstudianteElegido)) {
    $infoFinanciera = obtenerEstadoFinancieroEstudiante($idEstudianteElegido);
}

$hoy = date('Y-m-d');
$fechaLimite = date('Y') . '-06-30';
$esDespuesDeJunio = ($hoy > $fechaLimite);

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos_pago = $_SESSION['datos_pago'] ?? [];
unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_pago']);

$titulo_pagina = "AULAPRO | REGISTRAR PAGO";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO PAGO</h1>
    <a href="verPagosGeneral.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<?php if ($esDespuesDeJunio) { ?>
    <div class="mensaje-error">
        <i class="fas fa-exclamation-triangle"></i> No se pueden registrar pagos después del 30 de Junio del año en curso.
    </div>
<?php } ?>

<div class="panel margen-abajo">
    <form method="GET" action="" class="caja al-final caja-libre espacio-medio">
        <div class="campo relleno">
            <label for="idCiclo">1. Filtrar por Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>  
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label for="idEstudiante">2. Seleccionar Estudiante:</label>
            <select name="idEstudiante" id="idEstudiante" onchange="this.form.submit()">
                <option value="">-- Seleccionar Estudiante --</option>
                <?php foreach ($todos_los_estudiantes as $est) { ?>
                    <option value="<?= $est['idEstudiante'] ?>" <?= ($idEstudianteElegido == $est['idEstudiante']) ? 'selected' : '' ?>>
                        <?= $est['nombreEstudiante'] ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <input type="reset" class="boton-secundario" style="margin-bottom: 5px;" value="LIMPIAR">
    </form>
</div>

<?php if ($infoFinanciera) { ?>
<div class="panel">
    <div class="titulo-tarjeta">
        <h3>Estado Financiero de Estudiante</h3>
    </div>
    <div class="form-cols">
        <div class="campo">
            <label>Precio del Ciclo</label>
            <p><b><?= number_format($infoFinanciera['precioCiclo'], 2) ?> €</b></p>
        </div>
        <div class="campo">
            <label>Ya Pagado</label>
            <p><?= number_format($infoFinanciera['totalPagado'], 2) ?> €</p>
        </div>
        <div class="campo">
            <label>Pendiente</label>
            <p class="color-error texto-negrita"><?= number_format($infoFinanciera['restante'], 2) ?> €</p>
        </div>
    </div>

    <hr class="separador">

    <?php if ($infoFinanciera['restante'] <= 0) { ?>
        <div class="mensaje-exito">Este estudiante ya ha completado todos los pagos del ciclo.</div>
    <?php } elseif ($esDespuesDeJunio) { ?>
        <p class="mensaje-error">Periodo de pagos finalizado (30/06 superado).</p>
    <?php } else { ?>
        <form action="../../../controladores/admin/pagos/insertar.php" method="POST">
            <input type="hidden" name="idEstudiante" value="<?= $idEstudianteElegido ?>">
            <input type="hidden" name="fechaPago" value="<?= $hoy ?>">

            <div class="campo">
                <label for="tipoPago">Tipo de Pago</label>
                <select name="tipoPago" id="tipoPago" onchange="actualizarMontoRapido()">
                    <option value="">-- Elegir --</option>
                    <option value="mensual" <?= (isset($datos_pago['tipoPago']) && $datos_pago['tipoPago'] == 'mensual') ? 'selected' : '' ?>>Mensual (10% del total)</option>
                    <option value="trimestral" <?= (isset($datos_pago['tipoPago']) && $datos_pago['tipoPago'] == 'trimestral') ? 'selected' : '' ?>>Trimestral (25% del total)</option>
                    <option value="semestral" <?= (isset($datos_pago['tipoPago']) && $datos_pago['tipoPago'] == 'semestral') ? 'selected' : '' ?>>Semestral (50% del total)</option>
                    <option value="unico" <?= (isset($datos_pago['tipoPago']) && $datos_pago['tipoPago'] == 'unico') ? 'selected' : '' ?>>Todo lo restante (<?= number_format($infoFinanciera['restante'], 2) ?> €)</option>     
                </select>
                <?php if (isset($errores['tipoPago'])) { ?>
                    <strong class="error-campo"><?= $errores['tipoPago'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="montoInput">Cantidad a Cobrar (€)</label>
                <input type="number" name="monto" id="montoInput" step="0.01" max="<?= $infoFinanciera['restante'] ?>" readonly value="<?= $datos_pago['monto'] ?? '' ?>">
                <span>Máximo permitido: <?= $infoFinanciera['restante'] ?> €</span>
                <?php if (isset($errores['monto'])) { ?>
                    <strong class="error-campo"><?= $errores['monto'] ?></strong>
                <?php } ?>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarPago" class="boton-primario" value="Confirmar y Registrar Pago ()">
            </div>
        </form>
    <?php } ?>
</div>

<script>
function actualizarMontoRapido() {
    var tipo = document.getElementById('tipoPago').value;
    var precioTotal = <?= $infoFinanciera['precioCiclo'] ?>;
    var restante = <?= $infoFinanciera['restante'] ?>;
    var cuota = 0;

    if (tipo === 'mensual') cuota = precioTotal / 10;
    else if (tipo === 'trimestral') cuota = precioTotal / 4;
    else if (tipo === 'semestral') cuota = precioTotal / 2;
    else if (tipo === 'unico') cuota = restante;

    if (cuota > restante) cuota = restante;

    document.getElementById('montoInput').value = cuota.toFixed(2);
}

if (document.getElementById('tipoPago') && document.getElementById('tipoPago').value !== '') {
    actualizarMontoRapido();
}
</script>
<?php } ?>

<?php include '../comunes/footer.php'; ?>

