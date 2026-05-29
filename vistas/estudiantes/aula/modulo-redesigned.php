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

<!-- BREADCRUMB -->
<nav class="breadcrumb-modern">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="breadcrumb-sep">/</span>
  <span class="breadcrumb-actual"><?= htmlspecialchars($modulo['nombreModulo']) ?></span>
</nav>

<!-- HEADER -->
<div class="header-modern">
  <div>
    <h1 class="header-titulo"><?= htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
  </div>
  <a href="index.php" class="btn-modern btn-secondary-modern btn-small">
    <i class="fas fa-arrow-left"></i> Módulos
  </a>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errores) ?></div><?php endif; ?>

<!-- TABS -->
<div class="tabs-modern">
  <button class="tab-modern active" onclick="cambiarTab('archivos', this)">
    <i class="fas fa-folder"></i> Archivos
  </button>
  <button class="tab-modern" onclick="cambiarTab('tareas', this)">
    <i class="fas fa-tasks"></i> Tareas
  </button>
</div>

<!-- TAB: ARCHIVOS -->
<div id="tab-archivos">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-folder-open" style="color:var(--color-primary);"></i> Materiales</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= count($archivos) ?> archivo<?= count($archivos) != 1 ? 's' : '' ?>
      </span>
    </div>
    <div class="panel-content-modern">
      <?php if (empty($carpetas) && empty($archivos)): ?>
      <div class="empty-state-modern">
        <i class="fas fa-folder-open empty-state-icon"></i>
        <p class="empty-state-text">No hay materiales disponibles aún.</p>
      </div>
      <?php else: ?>

      <?php foreach ($carpetas as $carpeta): ?>
      <div class="carpeta" style="margin-bottom:var(--space-4);">
        <div class="carpeta-header-modern">
          <span class="carpeta-dot" style="background:<?= htmlspecialchars($carpeta['color']) ?>;"></span>
          <span class="carpeta-nombre"><?= htmlspecialchars($carpeta['nombre']) ?></span>
          <span class="carpeta-count"><?= $carpeta['totalArchivos'] ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
        </div>
        <div class="carpeta-contenido">
          <?php $archsEnCarpeta = $archivosPorCarpeta[$carpeta['idCarpeta']] ?? []; ?>
          <?php if (empty($archsEnCarpeta)): ?>
            <div style="padding:var(--space-4);text-align:center;color:var(--color-neutral-400);font-size:var(--font-size-sm);">
              <i class="fas fa-folder-open"></i> Carpeta vacía
            </div>
          <?php else: ?>
            <div style="padding:var(--space-2) 0;">
              <?php foreach ($archsEnCarpeta as $arch): ?>
              <div class="archivo-card-modern">
                <div class="archivo-icono-modern <?= $arch['extension'] ?>">
                  <i class="fas fa-file-<?= $arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt') ?>"></i>
                </div>
                <div class="archivo-info-modern">
                  <div class="archivo-nombre-modern"><?= htmlspecialchars($arch['nombreOriginal']) ?></div>
                  <div class="archivo-meta-modern"><?= htmlspecialchars($arch['nombreProfesor']) ?> · <?= date('d/m/Y H:i', strtotime($arch['fechaSubida'])) ?></div>
                </div>
                <div class="archivo-acciones-modern">
                  <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" data-ext="<?= $arch['extension'] ?>" data-nombre="<?= htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                  <a href="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" download class="btn-modern btn-primary-modern btn-small" title="Descargar"><i class="fas fa-download"></i> Descargar</a>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>

      <?php if (!empty($archivosSueltos)): ?>
      <div class="carpeta" style="margin-bottom:var(--space-4);">
        <div class="carpeta-header-modern">
          <span class="carpeta-dot" style="background:var(--color-neutral-300);"></span>
          <span class="carpeta-nombre">Otros archivos</span>
          <span class="carpeta-count"><?= count($archivosSueltos) ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
        </div>
        <div class="carpeta-contenido">
          <div style="padding:var(--space-2) 0;">
            <?php foreach ($archivosSueltos as $arch): ?>
            <div class="archivo-card-modern">
              <div class="archivo-icono-modern <?= $arch['extension'] ?>">
                <i class="fas fa-file-<?= $arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt') ?>"></i>
              </div>
              <div class="archivo-info-modern">
                <div class="archivo-nombre-modern"><?= htmlspecialchars($arch['nombreOriginal']) ?></div>
                <div class="archivo-meta-modern"><?= htmlspecialchars($arch['nombreProfesor']) ?> · <?= date('d/m/Y H:i', strtotime($arch['fechaSubida'])) ?></div>
              </div>
              <div class="archivo-acciones-modern">
                <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" data-ext="<?= $arch['extension'] ?>" data-nombre="<?= htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                <a href="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" download class="btn-modern btn-primary-modern btn-small" title="Descargar"><i class="fas fa-download"></i> Descargar</a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</div>

<!-- TAB: TAREAS -->
<div id="tab-tareas" style="display:none;">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-tasks" style="color:#8b5cf6;"></i> Tareas</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= count($tareas) ?>
      </span>
    </div>
    <div class="panel-content-modern">
      <?php if (empty($tareas)): ?>
      <div class="empty-state-modern">
        <i class="fas fa-clipboard-list empty-state-icon"></i>
        <p class="empty-state-text">No hay tareas asignadas aún.</p>
      </div>
      <?php else: ?>
      <div>
        <?php foreach ($tareas as $tarea):
          $entrega = obtenerEntregaAula($tarea['idTarea'], $idEstudiante);
        ?>
        <a href="tarea.php?id=<?= $tarea['idTarea'] ?>" class="tarea-card-modern" style="text-decoration:none;color:inherit;display:block;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-3);margin-bottom:var(--space-3);">
            <div style="flex:1;">
              <h3 class="tarea-titulo-modern"><?= htmlspecialchars($tarea['titulo']) ?></h3>
              <?php if ($tarea['descripcion']): ?>
              <p class="tarea-desc-modern"><?= htmlspecialchars($tarea['descripcion']) ?></p>
              <?php endif; ?>
            </div>
            <?php if ($entrega):
              if ($entrega['estado'] === 'corregida') {
                echo '<span class="badge-estado-modern" style="background:#dcfce7;color:#15803d;"><i class="fas fa-check-circle"></i> ★ '.$entrega['nota'].'/10</span>';
              } else {
                echo '<span class="badge-estado-modern" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-paper-plane"></i> Enviada</span>';
              }
            else:
              echo '<span class="badge-estado-modern badge-pendiente"><i class="fas fa-clock"></i> Pendiente</span>';
            endif; ?>
          </div>
          <div style="color:var(--color-neutral-500);font-size:var(--font-size-xs);">
            <i class="fas fa-user-tie"></i> <?= htmlspecialchars($tarea['nombreProfesor']) ?>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- VIEWER MODAL -->
<div id="aulaViewerModal" class="modal-pdf-overlay" style="display:none;">
  <div style="background:var(--color-white);border-radius:var(--radius-lg);width:100%;max-width:800px;max-height:90vh;display:flex;flex-direction:column;overflow:hidden;">
    <div style="padding:var(--space-4) var(--space-5);border-bottom:1px solid var(--color-neutral-200);display:flex;align-items:center;justify-content:space-between;">
      <span id="aulaViewerNombre" style="font-size:var(--font-size-sm);font-weight:var(--font-weight-semibold);color:var(--color-neutral-800);"></span>
      <button onclick="document.getElementById('aulaViewerModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:var(--color-neutral-400);">✕</button>
    </div>
    <div id="aulaViewerContenedor" style="flex:1;overflow:auto;background:var(--color-neutral-50);"></div>
  </div>
</div>

<script>
function cambiarTab(tabName, btn) {
  document.querySelectorAll('#tab-archivos, #tab-tareas').forEach(t => t.style.display = 'none');
  document.getElementById('tab-' + tabName).style.display = 'block';
  document.querySelectorAll('.tab-modern').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
  var primera = document.querySelector('.carpeta');
  if (primera) primera.classList.add('abierta');

  document.querySelectorAll('.carpeta-header-modern').forEach(function(h) {
    h.addEventListener('click', function(e) {
      if (!e.target.closest('button') && !e.target.closest('a')) {
        this.closest('.carpeta').classList.toggle('abierta');
      }
    });
  });

  document.querySelectorAll('[data-ver-archivo]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      abrirViewerAula(this.dataset.verArchivo, this.dataset.ext, this.dataset.nombre);
    });
  });
});

function abrirViewerAula(url, ext, nombre) {
  var modal = document.getElementById('aulaViewerModal');
  var contenedor = document.getElementById('aulaViewerContenedor');
  document.getElementById('aulaViewerNombre').textContent = nombre;
  contenedor.innerHTML = '<p style="text-align:center;padding:40px;color:var(--color-neutral-400);"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>';
  modal.style.display = 'flex';

  if (ext === 'txt') {
    fetch(url).then(r => r.text()).then(t => {
      contenedor.innerHTML = '<div style="padding:20px;font-family:monospace;font-size:0.85rem;white-space:pre-wrap;color:var(--color-neutral-700);">' + t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div>';
    }).catch(() => {
      contenedor.innerHTML = '<p style="padding:20px;color:var(--color-danger);">No se pudo cargar el archivo.</p>';
    });
  } else {
    contenedor.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-file-word" style="font-size:3rem;color:var(--color-info);"></i><p style="margin-top:12px;color:var(--color-neutral-500);">Vista previa no disponible. Descárgalo para ver.</p></div>';
  }
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
