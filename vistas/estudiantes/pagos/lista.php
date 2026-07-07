<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_pagos');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/pagos.php";

$idDeEsteEstudiante = $_SESSION['idEstudiante'];
$listaMisPagos = listarPagosPorEstudiante($idDeEsteEstudiante);
// Extraer cursos escolares distintos
$cursosDisponibles = [];
foreach ($listaMisPagos as $pago) {
    $c = $pago['cursoEscolar'] ?? 'Desconocido';
    if (!in_array($c, $cursosDisponibles)) $cursosDisponibles[] = $c;
}
if (empty($cursosDisponibles)) {
    require_once __DIR__ . '/../../../modelos/configuracion.php';
    $config = obtenerConfiguracion();
    $cursosDisponibles[] = $config['cursoEscolar'] ?? (date('Y') . '-' . (date('Y') + 1));
}
$cursoSeleccionado = $_GET['cursoEscolar'] ?? $cursosDisponibles[0];

// Filtrar pagos por curso seleccionado
$pagosFiltrados = array_filter($listaMisPagos, function($p) use ($cursoSeleccionado) {
    return ($p['cursoEscolar'] ?? 'Desconocido') === $cursoSeleccionado;
});
$datosEstadoFinanciero = obtenerEstadoFinancieroEstudiante($idDeEsteEstudiante);

$tituloDelPagina = "AULAPRO | MIS PAGOS";
$seccionActual = 'pagos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera" style="display:flex; justify-content:space-between; align-items:flex-end;">
    <div>
        <h1>MIS PAGOS</h1>
        <p class="subtitulo">Consulta tu historial de pagos y estado financiero</p>
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
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['totalPagado'], 2)) ?> €</h3>
        <p>TOTAL PAGADO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['precioCiclo'], 2)) ?> €</h3>
        <p>PRECIO DEL CICLO</p>
    </div>
  </div>
  <div class="tarjeta-estadistica <?= Security::escapeHtml(($datosEstadoFinanciero['restante'] > 0) ? 'tarjeta-estadistica-naranja' : 'tarjeta-estadistica-cian') ?>">
    <div class="info-estadistica">
        <h3><?= Security::escapeHtml(number_format($datosEstadoFinanciero['restante'], 2)) ?> €</h3>
        <p>PENDIENTE DE PAGO</p>
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



