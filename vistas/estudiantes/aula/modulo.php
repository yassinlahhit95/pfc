<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
$idEstudiante = $_SESSION['idEstudiante'] ?? '';

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idModulo = intval($_GET['id'] ?? 0);
$modulo   = obtenerModuloPorId($idModulo);
if (!$modulo) { header("Location: recursos.php"); exit; }

$estudiante = obtenerEstudiantePorId($idEstudiante);
if ($modulo['idCiclo'] != $estudiante['idCiclo']) { header("Location: recursos.php"); exit; }

$carpetas  = listarCarpetasPorModuloAula($idModulo);
$archivos  = listarArchivosPorModuloAula($idModulo);
$tareas    = listarTareasPorModuloAula($idModulo);
$sesiones  = listarSesionesPorModulo($idModulo);

// PAGINACIÓN
$itemsPorPagina = 10;
$paginaArchivos = max(1, intval($_GET['pag_arch'] ?? 1));
$paginaTareas   = max(1, intval($_GET['pag_tareas'] ?? 1));

// Filtrar archivos
$archivosFiltered = array_filter($archivos, function($archivo) {
    $search = $_GET['search_arch'] ?? '';
    return empty($search) || stripos($archivo['nombreOriginal'], $search) !== false;
});

// Filtrar tareas
$tareasFiltered = array_filter($tareas, function($tarea) {
    $search = $_GET['search_tareas'] ?? '';
    return empty($search) || stripos($tarea['titulo'], $search) !== false;
});

// Paginación archivos
$totalArchivos = count($archivosFiltered);
$totalPaginasArchivos = ceil($totalArchivos / $itemsPorPagina);
$paginaArchivos = min($paginaArchivos, max(1, $totalPaginasArchivos));
$offsetArchivos = ($paginaArchivos - 1) * $itemsPorPagina;
$archivosPaginados = array_slice($archivosFiltered, $offsetArchivos, $itemsPorPagina);

// Paginación tareas
$totalTareas = count($tareasFiltered);
$totalPaginasTareas = ceil($totalTareas / $itemsPorPagina);
$paginaTareas = min($paginaTareas, max(1, $totalPaginasTareas));
$offsetTareas = ($paginaTareas - 1) * $itemsPorPagina;
$tareasPaginadas = array_slice($tareasFiltered, $offsetTareas, $itemsPorPagina);

$archivosPorCarpeta = [];
$archivosSueltos    = [];
foreach ($archivosPaginados as $arch) {
    if ($arch['idCarpeta']) $archivosPorCarpeta[$arch['idCarpeta']][] = $arch;
    else $archivosSueltos[] = $arch;
}

$tituloDelPagina = "AULAPRO | " . strtoupper($modulo['nombreModulo']);
$seccionActual   = 'aula_sesiones';
$breadcrumbSectionUrl = 'recursos.php';
$breadcrumbExtra = [ ['label' => $modulo['nombreModulo'], 'url' => null] ];
include_once __DIR__ . "/../comunes/nav.php";
?>

<!-- VARIABLES GLOBALES PARA ANALYTICS Y TEMAS -->
<script>
  const idUsuario = <?= Security::escapeHtml((int)$idEstudiante) ?>;
  const tipoUsuario = 'estudiante';
  const idModulo = <?= Security::escapeHtml($idModulo ) ?>;
</script>

<!-- HEADER -->
<div class="header-modern">
  <div>
    <h1 class="header-titulo"><?= Security::escapeHtml(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
  </div>
  <a href="recursos.php" class="btn-modern btn-secondary-modern btn-small">
    <i class="fas fa-arrow-left"></i> Módulos
  </a>
</div>


<!-- TABS -->
<div class="tabs-modern">
  <button class="tab-modern active" onclick="cambiarTab('archivos', this)">
    <i class="fas fa-folder"></i> Archivos
  </button>
  <button class="tab-modern" onclick="cambiarTab('tareas', this)">
    <i class="fas fa-tasks"></i> Tareas
  </button>
  <button class="tab-modern" onclick="cambiarTab('sesiones', this)">
    <i class="fas fa-video"></i> Sesiones Vivas
  </button>
</div>

<!-- TAB: ARCHIVOS -->
<div id="tab-archivos">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-folder-open" style="color:var(--color-primary);"></i> Materiales</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= Security::escapeHtml(count($archivos)) ?> archivo<?= Security::escapeHtml(count($archivos) != 1 ? 's' : '') ?>
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
          <span class="carpeta-dot" style="background:<?= Security::escapeHtml($carpeta['color']) ?>;"></span>
          <span class="carpeta-nombre"><?= Security::escapeHtml($carpeta['nombre']) ?></span>
          <span class="carpeta-count"><?= Security::escapeHtml($carpeta['totalArchivos'] ) ?></span>
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
              <?php foreach ($archsEnCarpeta as $arch):
                $archUrl = '../../../controladores/aula/verArchivo.php?id=' . (int)$arch['idArchivo'];
              ?>
              <div class="archivo-card-modern">
                <div class="archivo-icono-modern <?= Security::escapeHtml($arch['extension'] ) ?>">
                  <i class="fas fa-file-<?= Security::escapeHtml($arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt')) ?>"></i>
                </div>
                <div class="archivo-info-modern">
                  <div class="archivo-nombre-modern"><?= Security::escapeHtml($arch['nombreOriginal']) ?></div>
                  <div class="archivo-meta-modern"><?= Security::escapeHtml($arch['nombreProfesor']) ?> · <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?></div>
                </div>
                <div class="archivo-acciones-modern">
                  <button class="btn-ghost-modern btn-small" data-ver-archivo="<?= Security::escapeHtml($archUrl . '&modo=ver') ?>" data-ext="<?= Security::escapeHtml($arch['extension'] ) ?>" data-nombre="<?= Security::escapeHtml($arch['nombreOriginal']) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                  <a href="<?= Security::escapeHtml($archUrl . '&modo=descarga') ?>" class="btn-modern btn-primary-modern btn-small"><i class="fas fa-download"></i> <span class="hidden-mobile">Descargar</span></a>
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
          <span class="carpeta-count"><?= Security::escapeHtml(count($archivosSueltos)) ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
        </div>
        <div class="carpeta-contenido">
          <div style="padding:var(--space-2) 0;">
            <?php foreach ($archivosSueltos as $arch):
              $archUrl = '../../../controladores/aula/verArchivo.php?id=' . (int)$arch['idArchivo'];
            ?>
            <div class="archivo-card-modern">
              <div class="archivo-icono-modern <?= Security::escapeHtml($arch['extension'] ) ?>">
                <i class="fas fa-file-<?= Security::escapeHtml($arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt')) ?>"></i>
              </div>
              <div class="archivo-info-modern">
                <div class="archivo-nombre-modern"><?= Security::escapeHtml($arch['nombreOriginal']) ?></div>
                <div class="archivo-meta-modern"><?= Security::escapeHtml($arch['nombreProfesor']) ?> · <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?></div>
              </div>
              <div class="archivo-acciones-modern">
                <button class="btn-ghost-modern btn-small" data-ver-archivo="<?= Security::escapeHtml($archUrl . '&modo=ver') ?>" data-ext="<?= Security::escapeHtml($arch['extension'] ) ?>" data-nombre="<?= Security::escapeHtml($arch['nombreOriginal']) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                <a href="<?= Security::escapeHtml($archUrl . '&modo=descarga') ?>" class="btn-modern btn-primary-modern btn-small"><i class="fas fa-download"></i> <span class="hidden-mobile">Descargar</span></a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
    <?php if ($totalPaginasArchivos > 1): ?>
    <div style="border-top:1px solid var(--color-neutral-200);padding:var(--space-4) var(--space-5);">
      <div class="pagination">
        <?php if ($paginaArchivos > 1): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=1" class="pagination-item" title="Primera"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($paginaArchivos - 1 ) ?>" class="pagination-item"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = max(1, $paginaArchivos - 1); $i <= min($totalPaginasArchivos, $paginaArchivos + 1); $i++): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($i ) ?>" class="pagination-item <?= Security::escapeHtml($i === $paginaArchivos ? 'active' : '') ?>"><?= Security::escapeHtml($i ) ?></a>
        <?php endfor; ?>
        <?php if ($paginaArchivos < $totalPaginasArchivos): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($paginaArchivos + 1 ) ?>" class="pagination-item"><i class="fas fa-chevron-right"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($totalPaginasArchivos ) ?>" class="pagination-item" title="Última"><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
        <span class="pagination-info"><?= Security::escapeHtml($paginaArchivos ) ?>/<?= Security::escapeHtml($totalPaginasArchivos ) ?></span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- TAB: TAREAS -->
<div id="tab-tareas" style="display:none;">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-tasks" style="color:var(--accent);"></i> Tareas</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= Security::escapeHtml(count($tareas)) ?>
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
        <?php foreach ($tareasPaginadas as $tarea):
          $entrega = obtenerEntregaAula($tarea['idTarea'], $idEstudiante);
        ?>
        <a href="tarea.php?id=<?= Security::escapeHtml($tarea['idTarea'] ) ?>" class="tarea-card-modern" style="text-decoration:none;color:inherit;display:block;">
          <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:var(--space-3);margin-bottom:var(--space-3);">
            <div style="flex:1;">
              <h3 class="tarea-titulo-modern"><?= Security::escapeHtml($tarea['titulo']) ?></h3>
              <?php if ($tarea['descripcion']): ?>
              <p class="tarea-desc-modern"><?= Security::escapeHtml($tarea['descripcion']) ?></p>
              <?php endif; ?>
            </div>
            <?php if ($entrega):
              if ($entrega['estado'] === 'corregida') {
                echo '<span class="badge-estado-modern" style="background:var(--verde-suave);color:var(--verde);"><i class="fas fa-check-circle"></i> '.$entrega['nota'].'/10</span>';
              } else {
                echo '<span class="badge-estado-modern" style="background:var(--azul-suave);color:var(--azul-ink);"><i class="fas fa-paper-plane"></i> Enviada</span>';
              }
            else:
              echo '<span class="badge-estado-modern badge-pendiente"><i class="fas fa-clock"></i> Pendiente</span>';
            endif; ?>
          </div>
          <div style="color:var(--color-neutral-500);font-size:var(--font-size-xs);">
            <i class="fas fa-user-tie"></i> <?= Security::escapeHtml($tarea['nombreProfesor']) ?>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php if ($totalPaginasTareas > 1): ?>
    <div style="border-top:1px solid var(--color-neutral-200);padding:var(--space-4) var(--space-5);">
      <div class="pagination">
        <?php if ($paginaTareas > 1): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_tareas=1" class="pagination-item" title="Primera"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_tareas=<?= Security::escapeHtml($paginaTareas - 1 ) ?>" class="pagination-item"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = max(1, $paginaTareas - 1); $i <= min($totalPaginasTareas, $paginaTareas + 1); $i++): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_tareas=<?= Security::escapeHtml($i ) ?>" class="pagination-item <?= Security::escapeHtml($i === $paginaTareas ? 'active' : '') ?>"><?= Security::escapeHtml($i ) ?></a>
        <?php endfor; ?>
        <?php if ($paginaTareas < $totalPaginasTareas): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_tareas=<?= Security::escapeHtml($paginaTareas + 1 ) ?>" class="pagination-item"><i class="fas fa-chevron-right"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_tareas=<?= Security::escapeHtml($totalPaginasTareas ) ?>" class="pagination-item" title="Última"><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
        <span class="pagination-info"><?= Security::escapeHtml($paginaTareas ) ?>/<?= Security::escapeHtml($totalPaginasTareas ) ?></span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- TAB: SESIONES VIVAS -->
<div id="tab-sesiones" style="display:none;">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-video" style="color:var(--rojo);"></i> Sesiones Vivas</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= Security::escapeHtml(count($sesiones)) ?>
      </span>
    </div>
    <div class="panel-content-modern">
      <?php if (empty($sesiones)): ?>
      <div class="empty-state-modern">
        <i class="fas fa-video empty-state-icon"></i>
        <p class="empty-state-text">No hay sesiones vivas disponibles aún.</p>
      </div>
      <?php else: ?>
      <div>
        <?php foreach ($sesiones as $sesion):
          $fechaSesion = new DateTime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']);
          $ahora = new DateTime();
          $estado = ($fechaSesion > $ahora) ? 'programada' : ($sesion['estado'] === 'en_vivo' ? 'en_vivo' : 'finalizada');
          $coloresEstado = [
            'programada' => ['bg' => '#fef3c7', 'text' => '#92400e', 'icon' => 'clock'],
            'en_vivo' => ['bg' => '#dbeafe', 'text' => '#0c4a6e', 'icon' => 'circle-notch'],
            'finalizada' => ['bg' => '#f3f4f6', 'text' => '#374151', 'icon' => 'check-circle']
          ];
          $estilo = $coloresEstado[$estado];
        ?>
        <div class="tarea-card-modern">
          <div style="display:flex;align-items:flex-start;gap:var(--space-3);margin-bottom:var(--space-3);">
            <div style="flex:1;">
              <div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-2);">
                <h3 class="tarea-titulo-modern"><?= Security::escapeHtml($sesion['titulo']) ?></h3>
                <span class="badge-estado-modern" style="background:<?= Security::escapeHtml($estilo['bg'] ) ?>;color:<?= Security::escapeHtml($estilo['text'] ) ?>;"><i class="fas fa-<?= Security::escapeHtml($estilo['icon'] ) ?>"></i> <?= Security::escapeHtml(strtoupper($estado)) ?></span>
              </div>
              <?php if ($sesion['descripcion']): ?>
              <p class="tarea-desc-modern"><?= Security::escapeHtml($sesion['descripcion']) ?></p>
              <?php endif; ?>
              <div style="margin-top:var(--space-2);display:flex;gap:var(--space-4);font-size:var(--font-size-sm);color:var(--color-neutral-600);">
                <span><i class="fas fa-calendar"></i> <?= Security::escapeHtml(date('d/m/Y', strtotime($sesion['fechaSesion']))) ?></span>
                <span><i class="fas fa-clock"></i> <?= Security::escapeHtml(date('H:i', strtotime($sesion['horaSesion']))) ?></span>
                <span><i class="fas fa-chalkboard-user"></i> <?= Security::escapeHtml($sesion['nombreProfesor']) ?></span>
              </div>
              <?php if ($sesion['plataforma']): ?>
              <div style="margin-top:var(--space-2);padding:var(--space-2);background:var(--color-neutral-50);border-radius:4px;font-size:var(--font-size-sm);">
                <strong><?= Security::escapeHtml($sesion['plataforma']) ?></strong>
                <?php if ($sesion['enlaceReunion']): ?>
                <a href="<?= Security::escapeHtml($sesion['enlaceReunion']) ?>" target="_blank" class="btn-modern btn-primary-modern btn-small" style="margin-left:var(--space-2);">
                  <i class="fas fa-external-link-alt"></i> Unirse a la sesión
                </a>
                <?php endif; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
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
      <button onclick="document.getElementById('aulaViewerModal').style.display='none'" class="modal-cerrar-btn">✕</button>
    </div>
    <div id="aulaViewerContenedor" style="flex:1;overflow:auto;background:var(--color-neutral-50);"></div>
  </div>
</div>

<script>
// NOTIFICACIONES TOAST
function mostrarToast(mensaje, tipo = 'info', duracion = 3000) {
  const container = document.getElementById('toastContainer') || crearToastContainer();
  const toast = document.createElement('div');
  toast.className = `toast ${tipo}`;
  toast.innerHTML = `
    <div class="toast-icon">
      <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
    </div>
    <div class="toast-content">${mensaje}</div>
    <button class="toast-close" onclick="this.parentElement.remove()">✕</button>
  `;
  container.appendChild(toast);
  if (duracion) setTimeout(() => {
    if (toast.parentElement) {
      toast.classList.add('removing');
      setTimeout(() => toast.remove(), 250);
    }
  }, duracion);
}

function crearToastContainer() {
  const container = document.createElement('div');
  container.id = 'toastContainer';
  container.className = 'toast-container';
  document.body.appendChild(container);
  return container;
}

function cambiarTab(tabName, btn) {
  document.querySelectorAll('#tab-archivos, #tab-tareas, #tab-sesiones').forEach(t => t.style.display = 'none');
  document.getElementById('tab-' + tabName).style.display = 'block';
  document.querySelectorAll('.tab-modern').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
  var primera = document.querySelector('.carpeta');
  if (primera) primera.classList.add('abierta');

  document.querySelectorAll('.carpeta-header-modern').forEach(function(header) {
    header.addEventListener('click', function(e) {
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

  // Track tab switches
  document.querySelectorAll('.tab-modern').forEach(tab => {
    tab.addEventListener('click', function() {
      const tabName = this.textContent.trim().toLowerCase();
      if (window.analytics) analytics.trackTabSwitch(tabName);
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
    fetch(url).then(response => response.text()).then(texto => {
      contenedor.innerHTML = '<div style="padding:20px;font-family:monospace;font-size:0.85rem;white-space:pre-wrap;color:var(--color-neutral-700);">' + texto.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div>';
    }).catch(() => {
      contenedor.innerHTML = '<p style="padding:20px;color:var(--color-danger);">No se pudo cargar el archivo.</p>';
    });
  } else {
    contenedor.innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-file-word" style="font-size:3rem;color:var(--color-info);"></i><p style="margin-top:12px;color:var(--color-neutral-500);">Vista previa no disponible. Descárgalo para ver.</p></div>';
  }
}
</script>

<!-- SCRIPTS: THEME SYSTEM Y ANALYTICS -->

<script src="../../../public/js/core/analytics.js"></script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


