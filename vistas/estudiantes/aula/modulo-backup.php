<?php
session_start();
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idModulo = intval($_GET['id'] ?? 0);
$modulo   = obtenerModuloPorId($idModulo);
if (!$modulo) { header("Location: index.php"); exit; }

$estudiante = obtenerEstudiantePorId($idEstudiante);
if ($modulo['idCiclo'] != $estudiante['idCiclo']) { header("Location: index.php"); exit; }

$carpetas = listarCarpetasPorModuloAula($idModulo);
$archivos = listarArchivosPorModuloAula($idModulo);
$tareas   = listarTareasPorModuloAula($idModulo);

$archivosPorCarpeta = [];
$archivosSueltos    = [];
foreach ($archivos as $arch) {
    if ($arch['idCarpeta']) $archivosPorCarpeta[$arch['idCarpeta']][] = $arch;
    else $archivosSueltos[] = $arch;
}

$tituloDelPagina = "AULAPRO | " . strtoupper($modulo['nombreModulo']);
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="aula-breadcrumb">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="sep">/</span>
  <span class="actual"><?= htmlspecialchars($modulo['nombreModulo']) ?></span>
</div>

<div class="cabecera">
  <h1><?= htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
  <a href="index.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Módulos</a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<div class="aula-modulo-layout" style="margin-top:20px;">

  <!-- ARCHIVOS -->
  <div class="aula-panel">
    <div class="aula-panel-header">
      <h3><i class="fas fa-folder-open" style="color:#0ea5e9;margin-right:8px;"></i>Materiales</h3>
      <span style="font-size:0.75rem;color:#94a3b8;"><?= count($archivos) ?> archivo<?= count($archivos)!=1?'s':'' ?></span>
    </div>

    <?php if (empty($carpetas) && empty($archivos)): ?>
    <div style="text-align:center;padding:40px 20px;">
      <i class="fas fa-folder-open" style="font-size:2.5rem;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
      <p class="texto-suave" style="font-size:0.85rem;">El profesor aún no ha subido materiales.</p>
    </div>
    <?php else: ?>
    <?php foreach ($carpetas as $carpeta): ?>
    <div class="aula-carpeta">
      <div class="aula-carpeta-titulo">
        <span class="aula-carpeta-dot" style="background:<?= htmlspecialchars($carpeta['color']) ?>;"></span>
        <span><?= htmlspecialchars($carpeta['nombre']) ?></span>
        <span class="aula-carpeta-count"><?= $carpeta['totalArchivos'] ?></span>
        <i class="fas fa-chevron-right aula-carpeta-chevron"></i>
      </div>
      <div class="aula-archivos-lista">
        <?php $archsEnCarpeta = $archivosPorCarpeta[$carpeta['idCarpeta']] ?? []; ?>
        <?php if (empty($archsEnCarpeta)): ?>
          <p class="aula-carpeta-vacia">Carpeta vacía</p>
        <?php else: ?>
          <?php foreach ($archsEnCarpeta as $arch): ?>
          <?php include __DIR__ . '/partials/_archivo_item_est.php'; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (!empty($archivosSueltos)): ?>
    <div class="aula-carpeta">
      <div class="aula-carpeta-titulo">
        <span class="aula-carpeta-dot" style="background:#94a3b8;"></span>
        <span>Otros archivos</span>
        <span class="aula-carpeta-count"><?= count($archivosSueltos) ?></span>
        <i class="fas fa-chevron-right aula-carpeta-chevron"></i>
      </div>
      <div class="aula-archivos-lista">
        <?php foreach ($archivosSueltos as $arch): ?>
        <?php include __DIR__ . '/partials/_archivo_item_est.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>

  <!-- TAREAS -->
  <div class="aula-panel">
    <div class="aula-panel-header">
      <h3><i class="fas fa-tasks" style="color:#8b5cf6;margin-right:8px;"></i>Tareas</h3>
    </div>
    <?php if (empty($tareas)): ?>
    <div style="text-align:center;padding:40px 20px;">
      <i class="fas fa-clipboard-list" style="font-size:2.5rem;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
      <p class="texto-suave" style="font-size:0.85rem;">No hay tareas asignadas.</p>
    </div>
    <?php else: ?>
    <?php foreach ($tareas as $tarea):
      $entrega = obtenerEntregaAula($tarea['idTarea'], $idEstudiante);
      if ($entrega) {
        if ($entrega['estado']==='corregida') { $badgeClass='aula-estado-corregida'; $badgeTxt='✓ Corregida · '.$entrega['nota'].'/10'; }
        else { $badgeClass='aula-estado-enviada'; $badgeTxt='Enviada (v'.$entrega['version'].')'; }
      } else { $badgeClass='aula-estado-pendiente'; $badgeTxt='Pendiente'; }
    ?>
    <a href="tarea.php?id=<?= $tarea['idTarea'] ?>" class="aula-tarea-item" style="display:block;text-decoration:none;">
      <div class="aula-tarea-titulo"><?= htmlspecialchars($tarea['titulo']) ?></div>
      <?php if ($tarea['descripcion']): ?>
      <div class="aula-tarea-desc"><?= htmlspecialchars($tarea['descripcion']) ?></div>
      <?php endif; ?>
      <div class="aula-tarea-footer">
        <span class="aula-tarea-meta"><i class="fas fa-user-tie"></i> <?= htmlspecialchars($tarea['nombreProfesor']) ?></span>
        <span class="aula-estado-badge <?= $badgeClass ?>"><?= $badgeTxt ?></span>
      </div>
    </a>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- MODAL VIEWER -->
<div id="aulaViewerModal" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;">
    <div style="padding:14px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;">
      <span id="aulaViewerNombre" style="font-size:0.85rem;font-weight:600;color:#1e293b;"></span>
      <button onclick="document.getElementById('aulaViewerModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <div id="aulaViewerContenedor" class="aula-viewer-wrap" style="flex:1;overflow:auto;"></div>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
