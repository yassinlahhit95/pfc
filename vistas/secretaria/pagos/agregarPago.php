<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idCicloElegido      = (int)($_GET['idCiclo']      ?? 0);
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

$hoy             = date('Y-m-d');
$fechaLimite     = date('Y') . '-06-30';
// Secretaría puede registrar pagos fuera de plazo.
$esDespuesDeJunio = false;

$titulo_pagina = "AULAPRO | REGISTRAR PAGO";
$seccion = 'pagos';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO PAGO</h1>
    <a href="verPagos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores): ?>
<div class="mensaje-error" style="margin-bottom:16px;">
    <i class="fas fa-exclamation-triangle"></i>
    <?= is_array($errores) ? implode('<br>', array_map([Security::class,'escapeHtml'], $errores)) : Security::escapeHtml($errores) ?>
</div>
<?php endif; ?>

<!-- ── Paso 1 y 2: elegir ciclo + estudiante ── -->
<div class="panel margen-abajo">
    <form method="GET" action="" class="caja al-final caja-libre espacio-medio">
        <div class="campo relleno">
            <label for="idCiclo">1. Filtrar por Ciclo</label>
            <select name="idCiclo" id="idCiclo" onchange="this.form.submit()">
                <option value="">— Todos los ciclos —</option>
                <?php foreach ($todosLosCiclos as $ciclo): ?>
                <option value="<?= (int)$ciclo['idCiclo'] ?>"
                    <?= $idCicloElegido == $ciclo['idCiclo'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo relleno">
            <label for="idEstudiante">2. Seleccionar Estudiante</label>
            <select name="idEstudiante" id="idEstudiante" onchange="this.form.submit()">
                <option value="">— Seleccionar estudiante —</option>
                <?php foreach ($todosLosEstudiantes as $estudiante): ?>
                <option value="<?= (int)$estudiante['idEstudiante'] ?>"
                    <?= $idEstudianteElegido == $estudiante['idEstudiante'] ? 'selected' : '' ?>>
                    <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <input type="reset" class="boton-secundario" style="margin-bottom:5px;" value="LIMPIAR"
               onclick="window.location='agregarPago.php'; return false;">
    </form>
</div>

<!-- ── Paso 3: estado financiero + formulario de pago ── -->
<?php if ($infoFinanciera): ?>
<div class="panel">
    <div class="titulo-tarjeta"><h3>Estado Financiero del Estudiante</h3></div>
    <div class="fila-datos" style="margin-bottom:16px;">
        <div class="dato">
            <span class="dato-label">Precio del Ciclo</span>
            <span class="dato-valor"><b><?= number_format($infoFinanciera['precioCiclo'], 2) ?> €</b></span>
        </div>
        <div class="dato">
            <span class="dato-label">Ya Pagado</span>
            <span class="dato-valor"><?= number_format($infoFinanciera['totalPagado'], 2) ?> €</span>
        </div>
        <div class="dato">
            <span class="dato-label">Pendiente</span>
            <span class="dato-valor" style="color:var(--accent);font-weight:700;">
                <?= number_format($infoFinanciera['restante'], 2) ?> €
            </span>
        </div>
    </div>

    <hr style="border:none;border-top:1px solid var(--border);margin:16px 0;">

    <?php if ($infoFinanciera['restante'] <= 0): ?>
        <div class="mensaje-exito" style="padding:12px 16px;border-radius:8px;background:rgba(16,185,129,.08);color:#065f46;">
            <i class="fas fa-check-circle"></i> Este estudiante ya ha completado todos los pagos del ciclo.
        </div>
    <?php elseif ($esDespuesDeJunio): ?>
        <p style="color:var(--rojo);"><i class="fas fa-ban"></i> Período de pagos finalizado (30/06 superado).</p>
    <?php else: ?>
    <form action="../../../controladores/secretaria/pagos/insertar.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= $idEstudianteElegido ?>">
        <input type="hidden" name="fechaPago"     value="<?= $hoy ?>">

        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'tipoPago') ?>">
                    <label for="tipoPago">Tipo de Pago</label>
                    <select name="tipoPago" id="tipoPago">
                        <option value="">— Elegir —</option>
                        <option value="mensual">Mensual (10% del total)</option>
                        <option value="trimestral">Trimestral (25% del total)</option>
                        <option value="semestral">Semestral (50% del total)</option>
                        <option value="unico">Todo lo restante (<?= number_format($infoFinanciera['restante'], 2) ?> €)</option>
                    </select>
                    <?= fieldError($errores, 'tipoPago') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'monto') ?>">
                    <label for="montoInput">Cantidad a Cobrar (€)</label>
                    <input type="number" name="monto" id="montoInput" step="0.01"
                           max="<?= $infoFinanciera['restante'] ?>" readonly
                           placeholder="Se calculará al elegir el tipo">
                    <small class="texto-suave">Máximo: <?= number_format($infoFinanciera['restante'], 2) ?> €</small>
                    <?= fieldError($errores, 'monto') ?>
                </div>

                <div class="campo">
                    <label for="comprobante">Comprobante de Pago (Opcional)</label>
                    <input type="file" name="comprobante" id="comprobante" accept=".pdf,.jpg,.jpeg,.png">
                    <small class="texto-suave">PDF o imagen. Adjuntar si hay transferencia/recibo.</small>
                </div>
            </div>

            <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
                <a href="verPagos.php" class="boton-secundario">Cancelar</a>
                <button type="submit" name="guardarPago" class="boton-primario">
                    <i class="fas fa-save"></i> Confirmar y Registrar Pago
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
var precioTotal = <?= (float)$infoFinanciera['precioCiclo'] ?>;
var restante    = <?= (float)$infoFinanciera['restante'] ?>;

$('#tipoPago').on('change', function () {
    var tipo  = $(this).val();
    var cuota = 0;
    if (tipo === 'mensual')    cuota = precioTotal / 10;
    else if (tipo === 'trimestral') cuota = precioTotal / 4;
    else if (tipo === 'semestral')  cuota = precioTotal / 2;
    else if (tipo === 'unico')      cuota = restante;
    if (cuota > restante) cuota = restante;
    $('#montoInput').val(cuota > 0 ? cuota.toFixed(2) : '');
});
</script>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
