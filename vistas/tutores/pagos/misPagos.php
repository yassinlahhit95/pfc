<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
$titulo_pagina = 'AulaPro Familias — Pagos y Recibos';
$seccion       = 'pagos';
include __DIR__ . '/../comunes/nav.php';

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/tutores.php";

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
?>

<div class="hero">
  <div class="hero-text">
    <div class="eyebrow">Gestión Económica</div>
    <h1>Mis <span>Pagos</span></h1>
    <p class="sub">Consulte el estado financiero y los recibos de sus hijos matriculados.</p>
  </div>
</div>

<?php if (empty($hijos)): ?>
    <div class="panel">
        <p class="vacio">No hay estudiantes vinculados a su cuenta.</p>
    </div>
<?php else: ?>
    <?php foreach ($hijos as $hijo): 
        $estadoFinan = obtenerEstadoFinancieroEstudiante($hijo['idEstudiante']);
        $pagos = listarPagosPorEstudiante($hijo['idEstudiante']);
    ?>
        <div class="dash-panel mt-4">
            <div class="dash-panel-head">
                <h3><?= Security::escapeHtml($hijo['nombreEstudiante']) ?> <small class="text-muted">(<?= Security::escapeHtml($hijo['nombreCiclo']) ?>)</small></h3>
            </div>
            <div class="dash-panel-body">
                <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 20px;">
                    <div class="tile card-soft" style="--tint: #4f46e5; min-height: auto; padding: 15px;">
                        <div class="tile-body">
                            <div class="tile-label">Total del Ciclo</div>
                            <div class="tile-desc"><?= number_format($estadoFinan['precioCiclo'], 2) ?> €</div>
                        </div>
                    </div>
                    <div class="tile card-soft" style="--tint: #10b981; min-height: auto; padding: 15px;">
                        <div class="tile-body">
                            <div class="tile-label">Total Pagado</div>
                            <div class="tile-desc"><?= number_format($estadoFinan['totalPagado'], 2) ?> €</div>
                        </div>
                    </div>
                    <div class="tile card-soft" style="--tint: #ef4444; min-height: auto; padding: 15px;">
                        <div class="tile-body">
                            <div class="tile-label">Pendiente</div>
                            <div class="tile-desc"><?= number_format($estadoFinan['restante'], 2) ?> €</div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-3">Fecha</th>
                                <th>Concepto</th>
                                <th>Monto</th>
                                <th class="text-end px-3">Recibo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pagos)): ?>
                                <tr><td colspan="4" class="text-center py-3 text-muted">No se han registrado pagos todavía.</td></tr>
                            <?php else: ?>
                                <?php foreach ($pagos as $p): ?>
                                    <tr>
                                        <td class="px-3"><?= date('d/m/Y', strtotime($p['fechaPago'])) ?></td>
                                        <td>Pago cuota <?= $p['tipoPago'] ?></td>
                                        <td class="fw-bold"><?= number_format($p['monto'], 2) ?> €</td>
                                        <td class="text-end px-3">
                                            <a href="#" class="btn btn-sm btn-outline-primary" title="Descargar Recibo (Próximamente)">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
