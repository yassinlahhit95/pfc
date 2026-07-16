<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/asistencias.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
$idEstudiante = (int)($_GET['id'] ?? 0);

if (empty($idEstudiante)) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$esHijo    = false;
$estudiante = null;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $esHijo    = true;
        $estudiante = $hijo;
        break;
    }
}

if (!$esHijo) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

$titulo_pagina = 'AulaPro Familias — Expediente Académico';
$seccion       = 'inicio';
include __DIR__ . '/../comunes/nav.php';

$resultados = obtenerResultadosFinalesEstudiante($idEstudiante);
$asist      = contarResumenAsistencia($idEstudiante);
$totalDias  = array_sum($asist);
$pctPresente = $totalDias > 0 ? round($asist['presente'] / $totalDias * 100) : null;

$estadoColor = match($resultados['estado_global']) {
    'APROBADO'  => 'verde',
    'SUSPENSO'  => 'rojo',
    default     => 'naranja',
};
?>

<div class="cabecera">
  <div>
    <h1><?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    <p style="color:var(--dim);margin:2px 0 0"><?= Security::escapeHtml($estudiante['nombreCiclo']) ?><?php if (!empty($estudiante['curso'])): ?> &mdash; <?= Security::escapeHtml($estudiante['curso']) ?><?php endif; ?></p>
  </div>
  <a href="../inicio/dashboard.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<!-- Stat tiles -->
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:12px;margin-bottom:20px">
  <div class="panel" style="padding:18px 20px">
    <div style="font-size:.75rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Media Global</div>
    <div style="font-size:1.9rem;font-weight:700;color:var(--accent)"><?= Security::escapeHtml((string)$resultados['promedio_global']) ?></div>
  </div>
  <div class="panel" style="padding:18px 20px">
    <div style="font-size:.75rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Estado</div>
    <div style="margin-top:2px"><span class="texto-estado <?= $estadoColor ?>"><?= Security::escapeHtml($resultados['estado_global']) ?></span></div>
    <div style="font-size:.75rem;color:var(--dim);margin-top:6px">APROBADO = todos los módulos con nota final ≥ 5</div>
  </div>
  <?php if ($totalDias > 0): ?>
  <div class="panel" style="padding:18px 20px">
    <div style="font-size:.75rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Asistencia</div>
    <div style="font-size:1.9rem;font-weight:700;color:var(--accent)"><?= $pctPresente ?>%</div>
    <div style="font-size:.75rem;color:var(--dim);margin-top:2px"><?= $asist['presente'] ?> presentes &middot; <?= $asist['ausente'] ?> ausencias<?php if ($asist['justificado']): ?> &middot; <?= $asist['justificado'] ?> justif.<?php endif; ?></div>
  </div>
  <?php endif; ?>
</div>

<!-- Calificaciones -->
<div class="panel">
  <div class="panel-titulo-seccion">Calificaciones por Módulo</div>
  <?php if (empty($resultados['detalles_modulos'])): ?>
    <div class="panel-vacio">
      <div class="panel-vacio-icono"><i class="fas fa-graduation-cap"></i></div>
      <div class="panel-vacio-titulo">Sin calificaciones</div>
      <div class="panel-vacio-desc">Todavía no hay notas registradas para este estudiante.</div>
    </div>
  <?php else: ?>
  <div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaCalificaciones">
      <thead>
        <tr>
          <th>Módulo</th>
          <th style="text-align:center">Media Retos</th>
          <th style="text-align:center">Media Exámenes</th>
          <th style="text-align:center">Nota Final</th>
          <th style="text-align:right">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($resultados['detalles_modulos'] as $detalle): ?>
          <tr>
            <td><strong><?= Security::escapeHtml($detalle['nombreModulo']) ?></strong></td>
            <td style="text-align:center"><?= Security::escapeHtml((string)$detalle['media_retos']) ?></td>
            <td style="text-align:center"><?= Security::escapeHtml((string)$detalle['media_notas']) ?></td>
            <td style="text-align:center"><strong><?= Security::escapeHtml((string)$detalle['nota_final']) ?></strong></td>
            <td style="text-align:right">
              <?php
              $chip = match($detalle['estado']) {
                  'Aprobado' => 'verde',
                  'Suspenso' => 'rojo',
                  default    => 'gris',
              };
              ?>
              <span class="texto-estado <?= $chip ?>"><?= Security::escapeHtml($detalle['estado']) ?></span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</div>

<!-- Asistencia detallada -->
<?php if ($totalDias > 0): ?>
<div class="panel" style="margin-top:16px">
  <div class="panel-titulo-seccion">Resumen de Asistencia</div>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:10px;padding:4px 0 8px">
    <?php
    $chips = [
        'presente'    => ['verde',   'Presentes'],
        'ausente'     => ['rojo',    'Ausencias'],
        'retraso'     => ['naranja', 'Retrasos'],
        'justificado' => ['azul',    'Justificadas'],
    ];
    foreach ($chips as $key => [$color, $label]):
      if (!$asist[$key]) continue;
    ?>
    <div style="background:var(--surface-2);border-radius:8px;padding:12px 16px">
      <div style="font-size:.72rem;color:var(--dim);text-transform:uppercase;letter-spacing:.05em"><?= $label ?></div>
      <div style="font-size:1.6rem;font-weight:700;margin-top:2px"><span class="texto-estado <?= $color ?>"><?= $asist[$key] ?></span></div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($resultados['nota_tfg'])): ?>
<?php
require_once __DIR__ . "/../../../modelos/tfg.php";
$tfgDoc = obtenerTFGporEstudiante($idEstudiante);
?>
<div class="panel" style="margin-top:16px">
  <div class="panel-titulo-seccion">Proyecto Final (TFG)</div>
  <div style="display:flex;align-items:center;gap:16px;padding:4px 0 8px;flex-wrap:wrap">
    <div style="font-size:2rem;font-weight:700;color:var(--accent)"><?= Security::escapeHtml((string)$resultados['nota_tfg']) ?></div>
    <?php if (!empty($resultados['obs_tfg'])): ?>
    <div style="color:var(--dim);font-size:.9rem"><?= Security::escapeHtml($resultados['obs_tfg']) ?></div>
    <?php endif; ?>
    <?php if (!empty($tfgDoc['archivoTFG'])): ?>
    <a href="../../../controladores/comunes/verTFG.php?id=<?= (int)$idEstudiante ?>" target="_blank" class="boton-secundario" style="margin-left:auto">
      <i class="fas fa-file-pdf"></i> Ver documento
    </a>
    <?php endif; ?>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
