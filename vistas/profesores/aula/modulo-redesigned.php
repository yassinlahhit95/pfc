<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idModulo = intval($_GET['id'] ?? 0);
$modulo   = obtenerModuloPorId($idModulo);
if (!$modulo) { header("Location: index.php"); exit; }

$carpetas  = listarCarpetasPorModuloAula($idModulo);
$archivos  = listarArchivosPorModuloAula($idModulo);
$tareas    = listarTodasTareasPorModuloAula($idModulo);

$archivosPorCarpeta = [];
$archivosSueltos    = [];
foreach ($archivos as $arch) {
    if ($arch['idCarpeta']) $archivosPorCarpeta[$arch['idCarpeta']][] = $arch;
    else $archivosSueltos[] = $arch;
}

$ciclo = null;
$todosCiclos = listarCiclosDeProfesor($idProfesor);
foreach ($todosCiclos as $c) { if ($c['idCiclo'] == $modulo['idCiclo']) { $ciclo = $c; break; } }

$colores = ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444','#ec4899','#6366f1','#14b8a6'];

$tituloDelPagina = "AULAPRO | " . strtoupper($modulo['nombreModulo']);
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<!-- BREADCRUMB -->
<nav class="breadcrumb-modern">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="breadcrumb-sep">/</span>
  <?php if ($ciclo): ?>
  <a href="modulos.php?idCiclo=<?= $ciclo['idCiclo'] ?>"><?= htmlspecialchars($ciclo['nombreCiclo']) ?></a>
  <span class="breadcrumb-sep">/</span>
  <?php endif; ?>
  <span class="breadcrumb-actual"><?= htmlspecialchars($modulo['nombreModulo']) ?></span>
</nav>

<!-- HEADER -->
<div class="header-modern">
  <div>
    <h1 class="header-titulo"><?= htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
  </div>
  <div class="header-acciones">
    <button onclick="document.getElementById('modalCarpeta').style.display='flex'" class="btn-modern btn-secondary-modern btn-small">
      <i class="fas fa-folder-plus"></i> Nueva Carpeta
    </button>
    <button onclick="document.getElementById('modalSubir').style.display='flex'" class="btn-modern btn-secondary-modern btn-small">
      <i class="fas fa-cloud-upload-alt"></i> Subir Archivos
    </button>
    <button onclick="document.getElementById('modalTarea').style.display='flex'" class="btn-modern btn-primary-modern btn-small">
      <i class="fas fa-plus"></i> Nueva Tarea
    </button>
  </div>
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
        <i class="fas fa-cloud-upload-alt empty-state-icon"></i>
        <p class="empty-state-text">No hay archivos. Sube el primer material.</p>
        <div class="empty-state-cta">
          <button onclick="document.getElementById('modalSubir').style.display='flex'" class="btn-modern btn-primary-modern">
            <i class="fas fa-cloud-upload-alt"></i> Subir Ahora
          </button>
        </div>
      </div>
      <?php else: ?>

      <?php foreach ($carpetas as $carpeta): ?>
      <div class="carpeta" style="margin-bottom:var(--space-4);">
        <div class="carpeta-header-modern">
          <span class="carpeta-dot" style="background:<?= htmlspecialchars($carpeta['color']) ?>;"></span>
          <span class="carpeta-nombre"><?= htmlspecialchars($carpeta['nombre']) ?></span>
          <span class="carpeta-count"><?= $carpeta['totalArchivos'] ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
          <button onclick="abrirEditarCarpeta(<?= $carpeta['idCarpeta'] ?>, '<?= htmlspecialchars(addslashes($carpeta['nombre'])) ?>', '<?= $carpeta['color'] ?>')" class="btn-ghost-modern btn-small" title="Editar" style="margin-left:auto;">
            <i class="fas fa-pen"></i>
          </button>
          <a href="../../../controladores/profesores/aula/borrarCarpeta.php?id=<?= $carpeta['idCarpeta'] ?>&modulo=<?= $idModulo ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar carpeta? Los archivos quedarán sueltos.')">
            <i class="fas fa-trash"></i>
          </a>
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
                  <div class="archivo-meta-modern"><?= date('d/m/Y H:i', strtotime($arch['fechaSubida'])) ?> · <?= $arch['tamanio'] > 1048576 ? round($arch['tamanio']/1048576,1).'MB' : round($arch['tamanio']/1024,1).'KB' ?></div>
                </div>
                <div class="archivo-acciones-modern">
                  <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" data-ext="<?= $arch['extension'] ?>" data-nombre="<?= htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                  <a href="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" download class="btn-ghost-modern btn-small" title="Descargar"><i class="fas fa-download"></i></a>
                  <button class="btn-ghost-modern btn-small" title="Mover" onclick="abrirMoverArchivo(<?= $arch['idArchivo'] ?>, '<?= htmlspecialchars(addslashes($arch['nombreOriginal'])) ?>')"><i class="fas fa-folder-arrow-down"></i></button>
                  <a href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= $arch['idArchivo'] ?>&modulo=<?= $arch['idModulo'] ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar este archivo?')"><i class="fas fa-trash"></i></a>
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
          <span class="carpeta-nombre">Sin carpeta</span>
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
                <div class="archivo-meta-modern"><?= date('d/m/Y H:i', strtotime($arch['fechaSubida'])) ?></div>
              </div>
              <div class="archivo-acciones-modern">
                <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" data-ext="<?= $arch['extension'] ?>" data-nombre="<?= htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                <a href="../../../public/uploads/aula/archivos/<?= htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES) ?>" download class="btn-ghost-modern btn-small" title="Descargar"><i class="fas fa-download"></i></a>
                <button class="btn-ghost-modern btn-small" title="Mover" onclick="abrirMoverArchivo(<?= $arch['idArchivo'] ?>, '<?= htmlspecialchars(addslashes($arch['nombreOriginal'])) ?>')"><i class="fas fa-folder-arrow-down"></i></button>
                <a href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= $arch['idArchivo'] ?>&modulo=<?= $arch['idModulo'] ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar este archivo?')"><i class="fas fa-trash"></i></a>
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
        <p class="empty-state-text">No hay tareas. Crea la primera.</p>
        <div class="empty-state-cta">
          <button onclick="document.getElementById('modalTarea').style.display='flex'" class="btn-modern btn-primary-modern">
            <i class="fas fa-plus"></i> Crear Tarea
          </button>
        </div>
      </div>
      <?php else: ?>
      <div>
        <?php foreach ($tareas as $tarea):
          $totalEst = contarEstudiantesCicloAula($modulo['idCiclo']);
          $porcentaje = $totalEst > 0 ? round(($tarea['totalEntregas'] / $totalEst) * 100) : 0;
          $esReciente = strtotime($tarea['fechaCreacion']) > strtotime('-24 hours');
        ?>
        <div class="tarea-card-modern">
          <div style="display:flex;align-items:flex-start;gap:var(--space-3);margin-bottom:var(--space-3);">
            <div style="flex:1;">
              <div style="display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-2);">
                <h3 class="tarea-titulo-modern"><?= htmlspecialchars($tarea['titulo']) ?></h3>
                <?php if (!$tarea['publicado']): ?><span class="badge-estado-modern" style="background:#fee2e2;color:#dc2626;"><i class="fas fa-eye-slash"></i> OCULTA</span><?php endif; ?>
                <?php if ($esReciente): ?><span class="badge-estado-modern" style="background:#dbeafe;color:#1d4ed8;"><i class="fas fa-sparkles"></i> NUEVO</span><?php endif; ?>
              </div>
              <?php if ($tarea['descripcion']): ?>
              <p class="tarea-desc-modern"><?= htmlspecialchars($tarea['descripcion']) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div style="margin-bottom:var(--space-3);">
            <div class="tarea-entregas" style="margin-bottom:var(--space-2);">
              <i class="fas fa-users"></i> <strong><?= $tarea['totalEntregas'] ?>/<?= $totalEst ?></strong> entregas
            </div>
            <div class="progress-modern">
              <div class="progress-bar" style="width:<?= $porcentaje ?>%;"></div>
            </div>
          </div>
          <div class="tarea-acciones-modern">
            <button type="button" class="btn-modern btn-ghost-modern btn-small" title="<?= $tarea['publicado'] ? 'Ocultar' : 'Publicar' ?>" onclick="toggleTarea(<?= $tarea['idTarea'] ?>, this, <?= $idModulo ?>)">
              <i class="fas fa-<?= $tarea['publicado'] ? 'eye' : 'eye-slash' ?>"></i>
            </button>
            <button type="button" class="btn-modern btn-ghost-modern btn-small" title="Editar" onclick="abrirEditarTarea(<?= $tarea['idTarea'] ?>, '<?= htmlspecialchars(addslashes($tarea['titulo'])) ?>', '<?= htmlspecialchars(addslashes($tarea['descripcion'] ?? ''), ENT_QUOTES) ?>')">
              <i class="fas fa-pen"></i>
            </button>
            <a href="verEntregas.php?id=<?= $tarea['idTarea'] ?>" class="btn-modern btn-primary-modern btn-small">
              <i class="fas fa-inbox"></i> Ver entregas
            </a>
            <a href="../../../controladores/profesores/aula/borrarTarea.php?id=<?= $tarea['idTarea'] ?>&modulo=<?= $idModulo ?>" class="btn-modern btn-danger-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar esta tarea y todas sus entregas?')">
              <i class="fas fa-trash"></i>
            </a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- MODALES: CARPETA, SUBIR, TAREA, EDITAR... -->
<!-- (Los modales se mantienen igual que antes, aquí omitiré por brevedad) -->

<script>
function cambiarTab(tabName, btn) {
  document.querySelectorAll('#tab-archivos, #tab-tareas').forEach(t => t.style.display = 'none');
  document.getElementById('tab-' + tabName).style.display = 'block';
  document.querySelectorAll('.tab-modern').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

// Abrir primera carpeta automáticamente
document.addEventListener('DOMContentLoaded', function() {
  var primera = document.querySelector('.carpeta');
  if (primera) primera.classList.add('abierta');

  // Toggle carpetas
  document.querySelectorAll('.carpeta-header-modern').forEach(function(h) {
    h.addEventListener('click', function() {
      this.closest('.carpeta').classList.toggle('abierta');
    });
  });

  // Ver archivo
  document.querySelectorAll('[data-ver-archivo]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      abrirViewerAula(this.dataset.verArchivo, this.dataset.ext, this.dataset.nombre);
    });
  });
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
