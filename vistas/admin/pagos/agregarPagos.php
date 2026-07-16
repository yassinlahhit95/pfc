<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idCicloElegido = (int)($_GET['idCiclo'] ?? 0);
$idEstudianteElegido = (int)($_GET['idEstudiante'] ?? 0);

$todosLosCiclos = listarTodosLosCiclos();

if (!empty($idCicloElegido)) {
    $todosLosEstudiantes = listarEstudiantesPorCiclo($idCicloElegido);
} else {
    $todosLosEstudiantes = listarEstudiantes();
}

$infoFinanciera = null;
if (!empty($idEstudianteElegido)) {
    $infoFinanciera = obtenerEstadoFinancieroEstudiante($idEstudianteElegido);
}

$hoy = date('Y-m-d');
$fechaLimite = date('Y') . '-06-30';
// Los administradores, secretaría y directores siempre pueden registrar pagos fuera de plazo.
$esDespuesDeJunio = false;

$datosPago = $_SESSION['datos_pago'] ?? [];
unset($_SESSION['datos_pago']);

$titulo_pagina = "AULAPRO | REGISTRAR PAGO";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO PAGO</h1>
    <a href="verPagosGeneral.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>



<div class="panel margen-abajo">
    <form method="GET" action="" class="caja al-final caja-libre espacio-medio">
        <div class="campo relleno">
            <label for="idCiclo">1. Filtrar por Ciclo:</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">-- Todos los Ciclos --</option>
                <?php foreach ($todosLosCiclos as $ciclo) { ?>
                    <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>  
                        <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="campo relleno">
            <label for="idEstudiante">2. Seleccionar Estudiante:</label>
            <select name="idEstudiante" id="idEstudiante" onchange="this.form.submit()">
                <option value="">-- Seleccionar Estudiante --</option>
                <?php foreach ($todosLosEstudiantes as $estudiante) { ?>
                    <option value="<?= (int)$estudiante['idEstudiante'] ?>" <?= ($idEstudianteElegido == $estudiante['idEstudiante']) ? 'selected' : '' ?>>
                        <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
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
        <form action="../../../controladores/admin/pagos/insertar.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= $idEstudianteElegido ?>">
            <input type="hidden" name="fechaPago" value="<?= $hoy ?>">

            <div class="campo<?= fieldClass($errores, 'tipoPago') ?>">
                <label for="tipoPago">Tipo de Pago</label>
                <select name="tipoPago" id="tipoPago">
                    <option value="">-- Elegir --</option>
                    <option value="mensual" <?= (isset($datosPago['tipoPago']) && $datosPago['tipoPago'] == 'mensual') ? 'selected' : '' ?>>Mensual (10% del total)</option>
                    <option value="trimestral" <?= (isset($datosPago['tipoPago']) && $datosPago['tipoPago'] == 'trimestral') ? 'selected' : '' ?>>Trimestral (25% del total)</option>
                    <option value="semestral" <?= (isset($datosPago['tipoPago']) && $datosPago['tipoPago'] == 'semestral') ? 'selected' : '' ?>>Semestral (50% del total)</option>
                    <option value="unico" <?= (isset($datosPago['tipoPago']) && $datosPago['tipoPago'] == 'unico') ? 'selected' : '' ?>>Todo lo restante (<?= number_format($infoFinanciera['restante'], 2) ?> €)</option>
                </select>
                <?= fieldError($errores, 'tipoPago') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'monto') ?>">
                <label for="montoInput">Cantidad a Cobrar (€)</label>
                <input type="number" name="monto" id="montoInput" step="0.01" max="<?= $infoFinanciera['restante'] ?>" readonly value="<?= Security::escapeHtml($datosPago['monto'] ?? '') ?>">
                <span>Máximo permitido: <?= $infoFinanciera['restante'] ?> €</span>
                <?= fieldError($errores, 'monto') ?>
            </div>

            <div class="campo">
                <label for="comprobante">Comprobante de Pago (Opcional)</label>
                <input type="file" name="comprobante" id="comprobante" accept=".pdf,.jpg,.jpeg,.png">
                <span>PDF o imagen. Solo si se requiere adjuntar recibo o transferencia.</span>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarPago" class="boton-primario" value="Confirmar y Registrar Pago">
            </div>
    </form>
    <?php } ?>
</div>

<script>
var precioTotal = <?= $infoFinanciera['precioCiclo'] ?>;
var restante = <?= $infoFinanciera['restante'] ?>;

function actualizarMontoRapido() {
    var tipo = $('#tipoPago').val();
    var cuota = 0;

    if (tipo === 'mensual') cuota = precioTotal / 10;
    else if (tipo === 'trimestral') cuota = precioTotal / 4;
    else if (tipo === 'semestral') cuota = precioTotal / 2;
    else if (tipo === 'unico') cuota = restante;

    if (cuota > restante) cuota = restante;
    $('#montoInput').val(cuota.toFixed(2));
}

$(function() {
    $('#tipoPago').on('change', actualizarMontoRapido);
    if ($('#tipoPago').val() !== '') actualizarMontoRapido();
});
</script>
<?php } ?>

<?php include '../comunes/footer.php'; ?>

