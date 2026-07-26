<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$listaMisPagos = listarPagosPorEstudiante($idEstudiante);
// Extraer cursos escolares distintos
$cursosDisponibles = [];
foreach ($listaMisPagos as $pago) {
    $curso = $pago['cursoEscolar'] ?? 'Desconocido';
    if (!in_array($curso, $cursosDisponibles)) $cursosDisponibles[] = $curso;
}
if (empty($cursosDisponibles)) {
    require_once __DIR__ . '/../../../modelos/configuracion.php';
    $config = obtenerConfiguracionCentro();
    $cursosDisponibles[] = $config['cursoEscolar'] ?? (date('Y') . '-' . (date('Y') + 1));
}
$cursoSeleccionado = $_GET['cursoEscolar'] ?? $cursosDisponibles[0];

// Filtrar pagos por curso seleccionado
$pagosFiltrados = array_filter($listaMisPagos, function($pago) use ($cursoSeleccionado) {
    return ($pago['cursoEscolar'] ?? 'Desconocido') === $cursoSeleccionado;
});
$datosEstadoFinanciero = obtenerEstadoFinancieroEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | MIS PAGOS";
$seccionActual = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera" style="display:flex; justify-content:space-between; align-items:flex-end;">
    <div>
        <h1>MIS PAGOS</h1>
        <p class="subtitulo-encabezado">Consulta tu historial de pagos y estado financiero</p>
    </div>
    <div class="filtro-curso">
        <form action="" method="GET" style="display:flex; align-items:center; gap:10px;">
            <label for="cursoEscolar" style="font-weight:600; color:var(--text-color);">Curso Escolar:</label>
            <select name="cursoEscolar" id="cursoEscolar" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border-2); background:var(--bg-card); color:var(--text-color); outline:none;">
                <?php foreach ($cursosDisponibles as $curso) { ?>
                    <option value="<?= Security::escapeHtml($curso) ?>" <?= $curso === $cursoSeleccionado ? 'selected' : '' ?>><?= Security::escapeHtml($curso) ?></option>
                <?php } ?>
            </select>
        </form>
    </div>
</div>


<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <span class="tarjeta-estadistica-icono"><i class="fas fa-circle-check"></i></span>
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['totalPagado'], 2)) ?> €</h3>
        <p>Total pagado</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <span class="tarjeta-estadistica-icono"><i class="fas fa-tag"></i></span>
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['precioCiclo'], 2)) ?> €</h3>
        <p>Precio del ciclo</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?= Security::escapeHtml(($datosEstadoFinanciero['restante'] > 0) ? 'tarjeta-estadistica-naranja' : 'tarjeta-estadistica-cian') ?>">
    <span class="tarjeta-estadistica-icono"><i class="fas <?= ($datosEstadoFinanciero['restante'] > 0) ? 'fa-clock' : 'fa-check-double' ?>"></i></span>
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['restante'], 2)) ?> €</h3>
        <p>Pendiente de pago</p>
    </div>
  </div>
</div>

<div class="panel margen-arriba">
    <div class="titulo-tarjeta">
        <h3>HISTORIAL DE PAGOS</h3>
    </div>
    
    <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>FECHA</th>
                    <th>CONCEPTO / TIPO</th>
                    <th>MONTO</th>
                    <th>PROXIMO PAGO</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($pagosFiltrados)) { ?>
                    <tr>
                        <td colspan="4" class="vacio">No hay pagos registrados en su historial para este curso.</td>
                    </tr>
                <?php } else { ?>
                    <?php foreach ($pagosFiltrados as $pagoIndividual) { ?>
                    <tr>
                        <td><?= Security::escapeHtml(date('d/m/Y', strtotime($pagoIndividual['fechaPago']))) ?></td>
                        <td>
                            <span class="texto-pago"><?= Security::escapeHtml(strtoupper($pagoIndividual['tipoPago'])) ?></span>
                        </td>
                        <td class="texto-negrita"><?= Security::escapeHtml(number_format($pagoIndividual['monto'], 2)) ?> €</td>
                        <td>
                            <?php 
                                if ($pagoIndividual['tipoPago'] == 'unico') {
                                    echo '<span class="texto-gris">N/A (PAGO FINALIZADO)</span>';
                                } else {
                                    echo date('d/m/Y', strtotime($pagoIndividual['fechaProximoPago'])); 
                                }
                            ?>
                        </td>
                    </tr>
                    <?php } ?>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php include '../comunes/footer.php'; ?>
