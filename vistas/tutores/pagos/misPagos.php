<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_pagos');
$titulo_pagina = 'AulaPro Familias — Pagos y Recibos';
$seccion       = 'pagos';
include __DIR__ . '/../comunes/nav.php';

require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/tutores.php";

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
?>

<div class="cabecera">
  <h1>Pagos y Recibos</h1>
</div>

<?php if (empty($hijos)): ?>
  <div class="panel">
    <div class="panel-vacio">
      <div class="panel-vacio-icono"><i class="fas fa-credit-card"></i></div>
      <div class="panel-vacio-titulo">Sin estudiantes vinculados</div>
      <div class="panel-vacio-desc">No hay estudiantes vinculados a su cuenta.</div>
    </div>
  </div>
<?php else: ?>
  <?php foreach ($hijos as $hijo):
    $estadoFinan = obtenerEstadoFinancieroEstudiante((int)$hijo['idEstudiante']);
    $pagos       = listarPagosPorEstudiante((int)$hijo['idEstudiante']);
    $pendiente   = max(0, $estadoFinan['restante']);
  ?>
  <div class="panel margen-abajo">
    <div class="panel-titulo-seccion"><?= Security::escapeHtml($hijo['nombreEstudiante']) ?> &mdash; <span style="font-weight:400;color:var(--dim)"><?= Security::escapeHtml($hijo['nombreCiclo']) ?></span></div>

    <!-- Financial summary -->
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;margin-bottom:20px">
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Total ciclo actual</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px"><?= number_format($estadoFinan['precioCiclo'], 2) ?> €</div>
      </div>
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Total pagado</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px;color:#10b981"><?= number_format($estadoFinan['totalPagado'], 2) ?> €</div>
      </div>
      <div style="background:var(--surface-2);border-radius:8px;padding:14px 16px">
        <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em">Pendiente</div>
        <div style="font-size:1.4rem;font-weight:700;margin-top:4px;color:<?= $pendiente > 0 ? '#ef4444' : '#10b981' ?>"><?= number_format($pendiente, 2) ?> €</div>
      </div>
    </div>

    <!-- Payment history table -->
    <?php if (empty($pagos)): ?>
      <div class="panel-vacio" style="padding:24px 0">
        <div class="panel-vacio-icono"><i class="fas fa-receipt"></i></div>
        <div class="panel-vacio-titulo">Sin pagos registrados</div>
        <div class="panel-vacio-desc">No se han registrado pagos para este estudiante todavía.</div>
      </div>
    <?php else: ?>
    <div class="contenedor-tabla">
      <table class="tabla-datos" id="tablaPagos<?= (int)$hijo['idEstudiante'] ?>">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Concepto</th>
            <th style="text-align:right">Importe</th>
            <th style="text-align:right">Próx. vencimiento</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($pagos as $p): ?>
            <tr>
              <td><?= date('d/m/Y', strtotime($p['fechaPago'])) ?></td>
              <td><?= Security::escapeHtml($p['tipoPago']) ?></td>
              <td style="text-align:right;font-weight:600"><?= number_format((float)$p['monto'], 2) ?> €</td>
              <td style="text-align:right;color:var(--dim)">
                <?= !empty($p['fechaProximoPago']) ? date('d/m/Y', strtotime($p['fechaProximoPago'])) : '—' ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
