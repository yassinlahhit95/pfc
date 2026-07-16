<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idModulo = intval($_GET['id'] ?? 0);
$modulo = obtenerModuloPorId($idModulo);
if (!$modulo) { header("Location: index.php"); exit; }

$dias = intval($_GET['dias'] ?? 30);
$resumen = obtenerResumenAnalytics($idModulo, $dias);
$topArchivos = obtenerTopArchivosPorDescargas($idModulo, 10);
$topTareas = obtenerTopTareasPorEntregas($idModulo, 10);

$tituloDelPagina = "AULAPRO | Analytics - " . strtoupper($modulo['nombreModulo']);
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<!-- BREADCRUMB -->
<nav class="breadcrumb-modern">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="breadcrumb-sep">/</span>
  <a href="modulo.php?id=<?= Security::escapeHtml($idModulo) ?>"><?= Security::escapeHtml($modulo['nombreModulo']) ?></a>
  <span class="breadcrumb-sep">/</span>
  <span class="breadcrumb-actual">Estadísticas</span>
</nav>

<!-- HEADER -->
<div class="header-modern">
  <h1 class="header-titulo"><i class="fas fa-chart-bar"></i> Análisis de uso</h1>
  <div class="header-acciones">
    <select class="modal-select" style="width: auto; padding: var(--space-2) var(--space-3);" onchange="window.location.href='?id=<?= Security::escapeHtml($idModulo) ?>&dias=' + this.value">
      <option value="7" <?= Security::escapeHtml($dias === 7 ? 'selected' : '') ?>>Última semana</option>
      <option value="30" <?= Security::escapeHtml($dias === 30 ? 'selected' : '') ?>>Último mes</option>
      <option value="90" <?= Security::escapeHtml($dias === 90 ? 'selected' : '') ?>>Últimos 3 meses</option>
      <option value="365" <?= Security::escapeHtml($dias === 365 ? 'selected' : '') ?>>Último año</option>
    </select>
    <a href="modulo.php?id=<?= Security::escapeHtml($idModulo) ?>" class="btn-modern btn-secondary-modern btn-small">
      <i class="fas fa-arrow-left"></i> Volver
    </a>
  </div>
</div>

<!-- TARJETAS DE RESUMEN -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-6);">

  <!-- Total Acciones -->
  <div class="card-modern" style="padding: var(--space-5);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <div>
        <p style="color: var(--color-neutral-500); font-size: var(--font-size-sm); margin: 0;">Acciones totales</p>
        <h2 style="font-size: 2.5rem; margin: var(--space-2) 0; color: var(--color-primary);">
          <?= Security::escapeHtml($resumen['totalAcciones'] ?? 0) ?>
        </h2>
      </div>
      <i class="fas fa-fire" style="font-size: 3rem; color: var(--color-warning); opacity: 0.2;"></i>
    </div>
  </div>

  <!-- Usuarios Únicos -->
  <div class="card-modern" style="padding: var(--space-5);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <div>
        <p style="color: var(--color-neutral-500); font-size: var(--font-size-sm); margin: 0;">Usuarios únicos</p>
        <h2 style="font-size: 2.5rem; margin: var(--space-2) 0; color: var(--color-success);">
          <?= Security::escapeHtml($resumen['usuariosUnicos'] ?? 0) ?>
        </h2>
      </div>
      <i class="fas fa-users" style="font-size: 3rem; color: var(--color-success); opacity: 0.2;"></i>
    </div>
  </div>

  <!-- Total Descargas -->
  <div class="card-modern" style="padding: var(--space-5);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <div>
        <p style="color: var(--color-neutral-500); font-size: var(--font-size-sm); margin: 0;">Descargas</p>
        <h2 style="font-size: 2.5rem; margin: var(--space-2) 0; color: var(--color-info);">
          <?= Security::escapeHtml($resumen['totalDescargas'] ?? 0) ?>
        </h2>
      </div>
      <i class="fas fa-download" style="font-size: 3rem; color: var(--color-info); opacity: 0.2;"></i>
    </div>
  </div>

  <!-- Total Entregas -->
  <div class="card-modern" style="padding: var(--space-5);">
    <div style="display: flex; align-items: center; justify-content: space-between;">
      <div>
        <p style="color: var(--color-neutral-500); font-size: var(--font-size-sm); margin: 0;">Entregas</p>
        <h2 style="font-size: 2.5rem; margin: var(--space-2) 0; color: var(--color-primary);">
          <?= Security::escapeHtml($resumen['totalEntregas'] ?? 0) ?>
        </h2>
      </div>
      <i class="fas fa-inbox" style="font-size: 3rem; color: var(--color-primary); opacity: 0.2;"></i>
    </div>
  </div>

</div>

<!-- TOP ARCHIVOS POR DESCARGAS -->
<div class="panel-modern" style="margin-bottom: var(--space-6);">
  <div class="panel-header-modern">
    <h3 class="panel-titulo-modern">
      <i class="fas fa-star" style="color: var(--color-warning);"></i> Archivos más descargados
    </h3>
    <span class="badge-estado-modern" style="background: var(--color-neutral-100); color: var(--color-neutral-600);">
      Top 10
    </span>
  </div>
  <div class="panel-content-modern">
    <?php if (empty($topArchivos)): ?>
    <p style="color: var(--color-neutral-400); text-align: center; padding: var(--space-4);">
      <i class="fas fa-inbox"></i> Sin datos de descarga
    </p>
    <?php else: ?>
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="border-bottom: 2px solid var(--color-neutral-200);">
          <th style="padding: var(--space-3); text-align: left; font-weight: 600; color: var(--color-neutral-600);">Archivo</th>
          <th style="padding: var(--space-3); text-align: right; font-weight: 600; color: var(--color-neutral-600);">Descargas</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topArchivos as $i => $archivo): ?>
        <tr style="border-bottom: 1px solid var(--color-neutral-100);">
          <td style="padding: var(--space-3);">
            <span style="display: inline-block; width: 24px; height: 24px; background: var(--color-primary); color: white; border-radius: 50%; text-align: center; font-weight: 600; margin-right: var(--space-2);">
              <?= Security::escapeHtml($i + 1) ?>
            </span>
            <?= Security::escapeHtml($archivo['nombreOriginal']) ?>
          </td>
          <td style="padding: var(--space-3); text-align: right; font-weight: 600; color: var(--color-primary);">
            <?= Security::escapeHtml($archivo['descargas']) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<!-- TOP TAREAS POR ENTREGAS -->
<div class="panel-modern">
  <div class="panel-header-modern">
    <h3 class="panel-titulo-modern">
      <i class="fas fa-star" style="color: var(--color-warning);"></i> Tareas con más entregas
    </h3>
    <span class="badge-estado-modern" style="background: var(--color-neutral-100); color: var(--color-neutral-600);">
      Top 10
    </span>
  </div>
  <div class="panel-content-modern">
    <?php if (empty($topTareas)): ?>
    <p style="color: var(--color-neutral-400); text-align: center; padding: var(--space-4);">
      <i class="fas fa-inbox"></i> Sin datos de entregas
    </p>
    <?php else: ?>
    <table style="width: 100%; border-collapse: collapse;">
      <thead>
        <tr style="border-bottom: 2px solid var(--color-neutral-200);">
          <th style="padding: var(--space-3); text-align: left; font-weight: 600; color: var(--color-neutral-600);">Tarea</th>
          <th style="padding: var(--space-3); text-align: right; font-weight: 600; color: var(--color-neutral-600);">Entregas</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($topTareas as $i => $tarea): ?>
        <tr style="border-bottom: 1px solid var(--color-neutral-100);">
          <td style="padding: var(--space-3);">
            <span style="display: inline-block; width: 24px; height: 24px; background: var(--color-success); color: white; border-radius: 50%; text-align: center; font-weight: 600; margin-right: var(--space-2);">
              <?= Security::escapeHtml($i + 1) ?>
            </span>
            <?= Security::escapeHtml($tarea['titulo']) ?>
          </td>
          <td style="padding: var(--space-3); text-align: right; font-weight: 600; color: var(--color-success);">
            <?= Security::escapeHtml($tarea['entregas']) ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<style>
  table tr:hover {
    background: var(--color-neutral-50);
  }
</style>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
