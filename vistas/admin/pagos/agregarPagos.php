<?php
session_start();
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idCicloElegido = $_GET['idCiclo'] ?? '';
$idEstudianteElegido = $_GET['idEstudiante'] ?? '';

$todos_los_ciclos = listarTodosLosCiclos();

// Filtrar estudiantes por ciclo si se ha seleccionado uno
if (!empty($idCicloElegido)) :
    $todos_los_estudiantes = listarEstudiantesPorCiclo($idCicloElegido);
else :
    $todos_los_estudiantes = listarEstudiantes();
endif;

// Obtener info financiera si hay estudiante seleccionado
$infoFinanciera = null;
if (!empty($idEstudianteElegido)) :
    $infoFinanciera = obtenerEstadoFinancieroEstudiante($idEstudianteElegido);
endif;

// Regla: No se permiten pagos después del 30 de Junio
$hoy = date('Y-m-d');
$fechaLimite = date('Y') . '-06-30';
$esDespuesDeJunio = ($hoy > $fechaLimite);

$lista_de_errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);

$titulo_pagina = "Registrar Pago - Super Admin";
$seccion = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Registrar Nuevo Pago</h1>
    <a href="verPagosGeneral.php" class="boton-secundario">Volver</a>
</div>

<?php if ($esDespuesDeJunio) : ?>
    <div class="mensaje-error">
        <i class="fas fa-exclamation-triangle"></i> No se pueden registrar pagos después del 30 de Junio del año en curso.
    </div>
<?php endif; ?>

<div class="tarjeta-blanca margen-abajo">
    <form method="GET" action="">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>1. Filtrar por Ciclo:</label>
                <select name="idCiclo" onchange="this.form.submit()">
                    <option value="">-- Todos los Ciclos --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) : ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($idCicloElegido == $ciclo['idCiclo']) ? 'selected' : '' ?>>  
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>2. Seleccionar Estudiante:</label>
                <select name="idEstudiante" onchange="this.form.submit()">
                    <option value="">-- Seleccionar Estudiante --</option>
                    <?php foreach ($todos_los_estudiantes as $est) : ?>
                        <option value="<?= $est['idEstudiante'] ?>" <?= ($idEstudianteElegido == $est['idEstudiante']) ? 'selected' : '' ?>>
                            <?= $est['nombreEstudiante'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </form>
</div>

<?php if ($infoFinanciera) : ?>
<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Estado Financiero de Estudiante</h3>
    </div>
    <div class="formulario-cuadricula">
        <div class="campo-formulario">
            <label>Precio del Ciclo</label>
            <p><strong><?= number_format($infoFinanciera['precioCiclo'], 2) ?> €</strong></p>
        </div>
        <div class="campo-formulario">
            <label>Ya Pagado</label>
            <p><?= number_format($infoFinanciera['totalPagado'], 2) ?> €</p>
        </div>
        <div class="campo-formulario">
            <label>Pendiente</label>
            <p class="color-error texto-negrita"><?= number_format($infoFinanciera['restante'], 2) ?> €</p>
        </div>
    </div>

    <hr class="separador">

    <?php if ($infoFinanciera['restante'] <= 0) : ?>
        <p class="mensaje-exito">Este estudiante ya ha completado todos los pagos del ciclo.</p>
    <?php elseif ($esDespuesDeJunio) : ?>
        <p class="mensaje-error">Periodo de pagos finalizado (30/06 superado).</p>
    <?php else : ?>
        <form action="../../../controladores/admin/pagos/insertar.php" method="POST">
            <input type="hidden" name="idEstudiante" value="<?= $idEstudianteElegido ?>">
            <input type="hidden" name="fechaPago" value="<?= $hoy ?>">

            <div class="formulario-cuadricula">
                <div class="campo-formulario">
                    <label>Tipo de Pago *</label>
                    <select name="tipoPago" id="tipoPago" onchange="actualizarMontoRapido()">
                        <option value="">-- Elegir --</option>
                        <option value="mensual">Mensual (10% del total)</option>
                        <option value="trimestral">Trimestral (25% del total)</option>
                        <option value="semestral">Semestral (50% del total)</option>
                        <option value="unico">Todo lo restante (<?= number_format($infoFinanciera['restante'], 2) ?> €)</option>     
                    </select>
                </div>

                <div class="campo-formulario">
                    <label>Cantidad a Cobrar (€) *</label>
                    <input type="number" name="monto" id="montoInput" step="0.01" max="<?= $infoFinanciera['restante'] ?>">
                    <small>Máximo permitido: <?= $infoFinanciera['restante'] ?> €</small>
                </div>
            </div>

            <div class="margen-arriba">
                <button type="submit" name="guardarPago" class="boton-primario">
                    <i class="fas fa-check"></i> Confirmar y Registrar Pago (<?= date('d/m/Y', strtotime($hoy)) ?>)
                </button>
            </div>
        </form>
    <?php endif; ?>
</div>

<script>
// JavaScript muy simple para ayudar al usuario, no es AJAX
function actualizarMontoRapido() {
    const tipo = document.getElementById('tipoPago').value;
    const precioTotal = <?= $infoFinanciera['precioCiclo'] ?>;
    const restante = <?= $infoFinanciera['restante'] ?>;
    let cuota = 0;

    if (tipo === 'mensual') cuota = precioTotal / 10;
    else if (tipo === 'trimestral') cuota = precioTotal / 4;
    else if (tipo === 'semestral') cuota = precioTotal / 2;
    else if (tipo === 'unico') cuota = restante;

    if (cuota > restante) cuota = restante;

    document.getElementById('montoInput').value = cuota.toFixed(2);
}
</script>
<?php endif; ?>

<?php include '../comunes/footer.php'; ?>
