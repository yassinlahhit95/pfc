<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$idSesion = intval($_GET['id'] ?? 0);
$sesion = obtenerSesionPorId($idSesion);
if (!$sesion || $sesion['idProfesor'] != $idProfesor) {
    header("Location: index.php");
    exit;
}

// Paginación
$itemsPorPagina = 20;
$paginaActual = max(1, intval($_GET['pag'] ?? 1));

$asistenciasCompleta = listarAsistenciasPorSesion($idSesion);
$totalAsistentes = count($asistenciasCompleta);
$totalPaginas = ceil($totalAsistentes / $itemsPorPagina);
$paginaActual = min($paginaActual, max(1, $totalPaginas));

$offsetAsistencias = ($paginaActual - 1) * $itemsPorPagina;
$asistencias = array_slice($asistenciasCompleta, $offsetAsistencias, $itemsPorPagina);

$tituloDelPagina = "AULAPRO | Asistencia - " . htmlspecialchars($sesion['titulo']);
$seccionActual = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<nav class="breadcrumb-modern">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="breadcrumb-sep">/</span>
  <a href="modulo.php?id=<?= Security::escapeHtml($sesion['idModulo']) ?>"><i class="fas fa-book"></i> <?= Security::escapeHtml($sesion['nombreModulo']) ?></a>
  <span class="breadcrumb-sep">/</span>
  <span class="breadcrumb-actual">Asistencia</span>
</nav>

<div class="header-modern">
  <div>
    <h1 class="header-titulo"><?= Security::escapeHtml($sesion['titulo']) ?></h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">
      <i class="fas fa-calendar"></i> <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']))) ?>
      · <i class="fas fa-users"></i> <?= Security::escapeHtml(count($asistencias)) ?> asistentes
    </p>
  </div>
  <a href="modulo.php?id=<?= Security::escapeHtml($sesion['idModulo']) ?>" class="btn-modern btn-secondary-modern btn-small">
    <i class="fas fa-arrow-left"></i> Volver
  </a>
</div>

<?php if (empty($asistencias)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;margin-top:20px;">
  <i class="fas fa-users" style="font-size:3rem;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
  <p class="texto-suave">No hay registros de asistencia aún.</p>
</div>
<?php else: ?>
<div class="panel-modern" style="margin-top:20px;">
  <div class="panel-header-modern">
    <h3 class="panel-titulo-modern"><i class="fas fa-list"></i> Lista de Asistencia</h3>
    <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
      <?= Security::escapeHtml(count($asistencias)) ?> estudiante<?= Security::escapeHtml(count($asistencias) != 1 ? 's' : '') ?>
    </span>
  </div>
  <div class="panel-content-modern">
    <table style="width:100%;border-collapse:collapse;">
      <thead>
        <tr style="border-bottom:2px solid var(--color-neutral-200);">
          <th style="text-align:left;padding:var(--space-3);font-weight:var(--font-weight-semibold);color:var(--color-neutral-600);"><i class="fas fa-user"></i> Estudiante</th>
          <th style="text-align:center;padding:var(--space-3);font-weight:var(--font-weight-semibold);color:var(--color-neutral-600);"><i class="fas fa-clock"></i> Hora de Unión</th>
          <th style="text-align:center;padding:var(--space-3);font-weight:var(--font-weight-semibold);color:var(--color-neutral-600);"><i class="fas fa-hourglass-end"></i> Hora de Salida</th>
          <th style="text-align:center;padding:var(--space-3);font-weight:var(--font-weight-semibold);color:var(--color-neutral-600);"><i class="fas fa-stopwatch"></i> Duración</th>
          <th style="text-align:center;padding:var(--space-3);font-weight:var(--font-weight-semibold);color:var(--color-neutral-600);">Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($asistencias as $asist): ?>
        <tr style="border-bottom:1px solid var(--color-neutral-100);hover:background:var(--color-neutral-50);">
          <td style="padding:var(--space-3);">
            <div style="display:flex;align-items:center;gap:var(--space-2);">
              <div style="width:36px;height:36px;border-radius:50%;background:var(--color-primary);color:white;display:flex;align-items:center;justify-content:center;font-weight:var(--font-weight-semibold);">
                <?= Security::escapeHtml(strtoupper(substr($asist['nombreEstudiante'], 0, 1))) ?>
              </div>
              <div>
                <div style="font-weight:var(--font-weight-semibold);color:var(--color-neutral-800);"><?= Security::escapeHtml($asist['nombreEstudiante']) ?></div>
                <div style="font-size:0.85rem;color:var(--color-neutral-500);"><?= Security::escapeHtml($asist['emailEstudiante']) ?></div>
              </div>
            </div>
          </td>
          <td style="padding:var(--space-3);text-align:center;color:var(--color-neutral-600);">
            <?= Security::escapeHtml($asist['horaUnion'] ? date('H:i', strtotime($asist['horaUnion'])) : '-') ?>
          </td>
          <td style="padding:var(--space-3);text-align:center;color:var(--color-neutral-600);">
            <?= Security::escapeHtml($asist['horaSalida'] ? date('H:i', strtotime($asist['horaSalida'])) : '-') ?>
          </td>
          <td style="padding:var(--space-3);text-align:center;color:var(--color-neutral-600);">
            <?php if ($asist['duracion']): ?>
              <?= Security::escapeHtml(floor($asist['duracion'] / 60)) ?>h <?= Security::escapeHtml($asist['duracion'] % 60 ) ?>m
            <?php else: ?>
              -
            <?php endif; ?>
          </td>
          <td style="padding:var(--space-3);text-align:center;">
            <?php if ($asist['presente']): ?>
              <span class="badge-estado-modern" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i> PRESENTE</span>
            <?php else: ?>
              <span class="badge-estado-modern" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-times-circle"></i> AUSENTE</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <!-- PAGINACIÓN -->
    <?php if ($totalPaginas > 1): ?>
    <div style="border-top:1px solid var(--color-neutral-200);padding:var(--space-4) var(--space-5);margin-top:var(--space-3);">
      <div class="pagination">
        <?php if ($paginaActual > 1): ?>
        <a href="?id=<?= Security::escapeHtml($idSesion ) ?>&pag=1" class="pagination-item" title="Primera"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></a>
        <a href="?id=<?= Security::escapeHtml($idSesion ) ?>&pag=<?= Security::escapeHtml($paginaActual - 1 ) ?>" class="pagination-item" title="Anterior"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $paginaActual - 1); $i <= min($totalPaginas, $paginaActual + 1); $i++): ?>
        <a href="?id=<?= Security::escapeHtml($idSesion ) ?>&pag=<?= Security::escapeHtml($i ) ?>" class="pagination-item <?= Security::escapeHtml($i === $paginaActual ? 'active' : '') ?>"><?= Security::escapeHtml($i ) ?></a>
        <?php endfor; ?>

        <?php if ($paginaActual < $totalPaginas): ?>
        <a href="?id=<?= Security::escapeHtml($idSesion ) ?>&pag=<?= Security::escapeHtml($paginaActual + 1 ) ?>" class="pagination-item" title="Siguiente"><i class="fas fa-chevron-right"></i></a>
        <a href="?id=<?= Security::escapeHtml($idSesion ) ?>&pag=<?= Security::escapeHtml($totalPaginas ) ?>" class="pagination-item" title="Última"><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>

        <span class="pagination-info"><?= Security::escapeHtml($paginaActual ) ?>/<?= Security::escapeHtml($totalPaginas ) ?></span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<div style="margin-top:20px;padding:20px;background:var(--color-neutral-50);border-radius:8px;">
  <h3 style="margin:0 0 12px 0;color:var(--color-neutral-800);"><i class="fas fa-chart-bar"></i> Estadísticas</h3>
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
    <div style="background:white;padding:16px;border-radius:6px;border-left:4px solid var(--color-primary);">
      <div style="font-size:0.85rem;color:var(--color-neutral-500);">Total de Asistentes</div>
      <div style="font-size:1.8rem;font-weight:bold;color:var(--color-primary);"><?= Security::escapeHtml($totalAsistentes ) ?></div>
    </div>
    <div style="background:white;padding:16px;border-radius:6px;border-left:4px solid var(--color-success);">
      <div style="font-size:0.85rem;color:var(--color-neutral-500);">Promedio de Permanencia</div>
      <div style="font-size:1.8rem;font-weight:bold;color:var(--color-success);">
        <?php
        $totalDuracion = 0;
        foreach ($asistenciasCompleta as $a) {
            $totalDuracion += $a['duracion'] ?? 0;
        }
        $promedio = !empty($asistenciasCompleta) ? floor($totalDuracion / count($asistenciasCompleta)) : 0;
        echo floor($promedio / 60) . 'h ' . ($promedio % 60) . 'm';
        ?>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


