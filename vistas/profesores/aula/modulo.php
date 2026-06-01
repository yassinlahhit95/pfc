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
$sesiones  = listarSesionesPorModulo($idModulo);

// PAGINACIÓN
$itemsPorPagina = 10;
$paginaArchivos = max(1, intval($_GET['pag_arch'] ?? 1));
$busquedaArchivos = $_GET['search_arch'] ?? '';

// Filtrar archivos por búsqueda
$archivosFiltered = array_filter($archivos, function($a) use ($busquedaArchivos) {
    return empty($busquedaArchivos) || stripos($a['nombreOriginal'], $busquedaArchivos) !== false;
});

// Paginación archivos
$totalArquivos = count($archivosFiltered);
$totalPaginasArchivos = ceil($totalArquivos / $itemsPorPagina);
$paginaArchivos = min($paginaArchivos, max(1, $totalPaginasArchivos));
$offsetArchivos = ($paginaArchivos - 1) * $itemsPorPagina;
$archivosPaginados = array_slice($archivosFiltered, $offsetArchivos, $itemsPorPagina);

$archivosPorCarpeta = [];
$archivosSueltos    = [];
foreach ($archivosPaginados as $arch) {
    if ($arch['idCarpeta']) $archivosPorCarpeta[$arch['idCarpeta']][] = $arch;
    else $archivosSueltos[] = $arch;
}

$ciclo = null;
$todosCiclos = listarCiclosDeProfesor($idProfesor);
foreach ($todosCiclos as $c) { if ($c['idCiclo'] == $modulo['idCiclo']) { $ciclo = $c; break; } }

$colores = ['#0ea5e9','#8b5cf6','#10b981','#f59e0b','#ef4444','#ec4899','#0ea5e9','#14b8a6'];

$tituloDelPagina = "AULAPRO | " . strtoupper($modulo['nombreModulo']);
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<!-- VARIABLES GLOBALES PARA ANALYTICS Y TEMAS -->
<script>
  const idUsuario = <?= Security::escapeHtml((int)$idProfesor) ?>;
  const tipoUsuario = 'profesor';
  const idModulo = <?= Security::escapeHtml($idModulo ) ?>;
</script>

<!-- BREADCRUMB -->
<nav class="breadcrumb-modern">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="breadcrumb-sep">/</span>
  <?php if ($ciclo): ?>
  <a href="modulos.php?idCiclo=<?= Security::escapeHtml($ciclo['idCiclo'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($ciclo['nombreCiclo'])) ?></a>
  <span class="breadcrumb-sep">/</span>
  <?php endif; ?>
  <span class="breadcrumb-actual"><?= Security::escapeHtml(htmlspecialchars($modulo['nombreModulo'])) ?></span>
</nav>

<!-- HEADER -->
<div class="header-modern">
  <h1 class="header-titulo"><?= Security::escapeHtml(htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8'))) ?></h1>
  <div class="header-acciones">
    <a href="analytics.php?id=<?= Security::escapeHtml($idModulo ) ?>" class="btn-modern btn-secondary-modern btn-small" title="Ver estadísticas">
      <i class="fas fa-chart-bar"></i> Analytics
    </a>
    <button onclick="abrirModal('modalCarpeta')" class="btn-modern btn-secondary-modern btn-small">
      <i class="fas fa-folder-plus"></i> Nueva Carpeta
    </button>
    <button onclick="abrirModal('modalSubir')" class="btn-modern btn-secondary-modern btn-small">
      <i class="fas fa-cloud-upload-alt"></i> Subir Archivos
    </button>
    <button onclick="abrirModal('modalSesion')" class="btn-modern btn-primary-modern btn-small">
      <i class="fas fa-video"></i> Nueva Sesión
    </button>
  </div>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml(htmlspecialchars($exito)) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><i class="fas fa-exclamation-circle"></i> <?= Security::escapeHtml(htmlspecialchars($errores)) ?></div><?php endif; ?>

<!-- BÚSQUEDA DINÁMICA -->
<div id="searchContainer" style="margin-bottom:var(--space-4);display:none;">
  <div class="search-modern">
    <input type="text" id="searchInput" placeholder="Buscar..." style="width:100%;">
  </div>
</div>

<!-- TABS -->
<div class="tabs-modern">
  <button class="tab-modern active" onclick="cambiarTab('archivos', this)">
    <i class="fas fa-folder"></i> Archivos
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
          <span class="carpeta-dot" style="background:<?= Security::escapeHtml(htmlspecialchars($carpeta['color'])) ?>;"></span>
          <span class="carpeta-nombre"><?= Security::escapeHtml(htmlspecialchars($carpeta['nombre'])) ?></span>
          <span class="carpeta-count"><?= Security::escapeHtml($carpeta['totalArchivos'] ) ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
          <button onclick="abrirEditarCarpeta(<?= Security::escapeHtml($carpeta['idCarpeta'] ) ?>, '<?= Security::escapeHtml(htmlspecialchars(addslashes($carpeta['nombre']))) ?>', '<?= Security::escapeHtml($carpeta['color'] ) ?>')" class="btn-ghost-modern btn-small" title="Editar" style="margin-left:auto;">
            <i class="fas fa-pen"></i>
          </button>
          <a href="../../../controladores/profesores/aula/borrarCarpeta.php?id=<?= Security::escapeHtml($carpeta['idCarpeta'] ) ?>&modulo=<?= Security::escapeHtml($idModulo ) ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar carpeta? Los archivos quedarán sueltos.')">
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
                <div class="archivo-icono-modern <?= Security::escapeHtml($arch['extension'] ) ?>">
                  <i class="fas fa-file-<?= Security::escapeHtml($arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt')) ?>"></i>
                </div>
                <div class="archivo-info-modern">
                  <div class="archivo-nombre-modern"><?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'])) ?></div>
                  <div class="archivo-meta-modern"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?> · <?= Security::escapeHtml($arch['tamanio'] > 1048576 ? round($arch['tamanio']/1048576,1).'MB' : round($arch['tamanio']/1024,1).'KB') ?></div>
                </div>
                <div class="archivo-acciones-modern">
                  <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= Security::escapeHtml(htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES)) ?>" data-ext="<?= Security::escapeHtml($arch['extension'] ) ?>" data-nombre="<?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES)) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                  <a href="../../../public/uploads/aula/archivos/<?= Security::escapeHtml(htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES)) ?>" download class="btn-ghost-modern btn-small" title="Descargar"><i class="fas fa-download"></i></a>
                  <button class="btn-ghost-modern btn-small" title="Mover" onclick="abrirMoverArchivo(<?= Security::escapeHtml($arch['idArchivo'] ) ?>, '<?= Security::escapeHtml(htmlspecialchars(addslashes($arch['nombreOriginal']))) ?>')"><i class="fas fa-folder-arrow-down"></i></button>
                  <a href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= Security::escapeHtml($arch['idArchivo'] ) ?>&modulo=<?= Security::escapeHtml($arch['idModulo'] ) ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar este archivo?')"><i class="fas fa-trash"></i></a>
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
          <span class="carpeta-count"><?= Security::escapeHtml(count($archivosSueltos)) ?></span>
          <i class="fas fa-chevron-right carpeta-chevron"></i>
        </div>
        <div class="carpeta-contenido">
          <div style="padding:var(--space-2) 0;">
            <?php foreach ($archivosSueltos as $arch): ?>
            <div class="archivo-card-modern">
              <div class="archivo-icono-modern <?= Security::escapeHtml($arch['extension'] ) ?>">
                <i class="fas fa-file-<?= Security::escapeHtml($arch['extension'] === 'pdf' ? 'pdf' : ($arch['extension'] === 'docx' ? 'word' : 'alt')) ?>"></i>
              </div>
              <div class="archivo-info-modern">
                <div class="archivo-nombre-modern"><?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'])) ?></div>
                <div class="archivo-meta-modern"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?></div>
              </div>
              <div class="archivo-acciones-modern">
                <button class="btn-ghost-modern btn-small" data-ver-archivo="../../../public/uploads/aula/archivos/<?= Security::escapeHtml(htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES)) ?>" data-ext="<?= Security::escapeHtml($arch['extension'] ) ?>" data-nombre="<?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'],ENT_QUOTES)) ?>" title="Ver"><i class="fas fa-eye"></i></button>
                <a href="../../../public/uploads/aula/archivos/<?= Security::escapeHtml(htmlspecialchars($arch['nombreArchivo'],ENT_QUOTES)) ?>" download class="btn-ghost-modern btn-small" title="Descargar"><i class="fas fa-download"></i></a>
                <button class="btn-ghost-modern btn-small" title="Mover" onclick="abrirMoverArchivo(<?= Security::escapeHtml($arch['idArchivo'] ) ?>, '<?= Security::escapeHtml(htmlspecialchars(addslashes($arch['nombreOriginal']))) ?>')"><i class="fas fa-folder-arrow-down"></i></button>
                <a href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= Security::escapeHtml($arch['idArchivo'] ) ?>&modulo=<?= Security::escapeHtml($arch['idModulo'] ) ?>" class="btn-ghost-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar este archivo?')"><i class="fas fa-trash"></i></a>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <?php endif; ?>

      <?php endif; ?>
    </div>
    <!-- PAGINACIÓN ARCHIVOS -->
    <?php if ($totalPaginasArchivos > 1): ?>
    <div style="border-top:1px solid var(--color-neutral-200);padding:var(--space-4) var(--space-5);">
      <div class="pagination">
        <?php if ($paginaArchivos > 1): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=1&search_arch=<?= Security::escapeHtml(urlencode($busquedaArchivos)) ?>" class="pagination-item" title="Primera"><i class="fas fa-chevron-left"></i><i class="fas fa-chevron-left"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($paginaArchivos - 1 ) ?>&search_arch=<?= Security::escapeHtml(urlencode($busquedaArchivos)) ?>" class="pagination-item" title="Anterior"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>

        <?php for ($i = max(1, $paginaArchivos - 1); $i <= min($totalPaginasArchivos, $paginaArchivos + 1); $i++): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($i ) ?>&search_arch=<?= Security::escapeHtml(urlencode($busquedaArchivos)) ?>" class="pagination-item <?= Security::escapeHtml($i === $paginaArchivos ? 'active' : '') ?>"><?= Security::escapeHtml($i ) ?></a>
        <?php endfor; ?>

        <?php if ($paginaArchivos < $totalPaginasArchivos): ?>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($paginaArchivos + 1 ) ?>&search_arch=<?= Security::escapeHtml(urlencode($busquedaArchivos)) ?>" class="pagination-item" title="Siguiente"><i class="fas fa-chevron-right"></i></a>
        <a href="?id=<?= Security::escapeHtml($idModulo ) ?>&pag_arch=<?= Security::escapeHtml($totalPaginasArchivos ) ?>&search_arch=<?= Security::escapeHtml(urlencode($busquedaArchivos)) ?>" class="pagination-item" title="Última"><i class="fas fa-chevron-right"></i><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>

        <span class="pagination-info"><?= Security::escapeHtml($paginaArchivos ) ?>/<?= Security::escapeHtml($totalPaginasArchivos ) ?></span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- TAB: SESIONES VIVAS -->
<div id="tab-sesiones" style="display:none;">
  <div class="panel-modern">
    <div class="panel-header-modern">
      <h3 class="panel-titulo-modern"><i class="fas fa-video" style="color:#ef4444;"></i> Sesiones Vivas</h3>
      <span style="font-size:var(--font-size-xs);color:var(--color-neutral-400);background:var(--color-neutral-100);padding:var(--space-1) var(--space-3);border-radius:4px;">
        <?= Security::escapeHtml(count($sesiones)) ?>
      </span>
    </div>
    <div class="panel-content-modern">
      <?php if (empty($sesiones)): ?>
      <div class="empty-state-modern">
        <i class="fas fa-video empty-state-icon"></i>
        <p class="empty-state-text">No hay sesiones vivas. Crea la primera.</p>
        <div class="empty-state-cta">
          <button onclick="abrirModal('modalSesion')" class="btn-modern btn-primary-modern">
            <i class="fas fa-plus"></i> Nueva Sesión
          </button>
        </div>
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
                <h3 class="tarea-titulo-modern"><?= Security::escapeHtml(htmlspecialchars($sesion['titulo'])) ?></h3>
                <span class="badge-estado-modern" style="background:<?= Security::escapeHtml($estilo['bg'] ) ?>;color:<?= Security::escapeHtml($estilo['text'] ) ?>;"><i class="fas fa-<?= Security::escapeHtml($estilo['icon'] ) ?>"></i> <?= Security::escapeHtml(strtoupper($estado)) ?></span>
              </div>
              <?php if ($sesion['descripcion']): ?>
              <p class="tarea-desc-modern"><?= Security::escapeHtml(htmlspecialchars($sesion['descripcion'])) ?></p>
              <?php endif; ?>
              <div style="margin-top:var(--space-2);display:flex;gap:var(--space-4);font-size:var(--font-size-sm);color:var(--color-neutral-600);">
                <span><i class="fas fa-calendar"></i> <?= Security::escapeHtml(date('d/m/Y', strtotime($sesion['fechaSesion']))) ?></span>
                <span><i class="fas fa-clock"></i> <?= Security::escapeHtml(date('H:i', strtotime($sesion['horaSesion']))) ?></span>
                <span><i class="fas fa-users"></i> <?= Security::escapeHtml($sesion['totalAsistentes']) ?> asistentes</span>
              </div>
              <?php if ($sesion['plataforma']): ?>
              <div style="margin-top:var(--space-2);padding:var(--space-2);background:var(--color-neutral-50);border-radius:4px;font-size:var(--font-size-sm);">
                <strong><?= Security::escapeHtml(htmlspecialchars($sesion['plataforma'])) ?></strong>
                <?php if ($sesion['enlaceReunion']): ?>
                <a href="<?= Security::escapeHtml(htmlspecialchars($sesion['enlaceReunion'])) ?>" target="_blank" class="btn-modern btn-ghost-modern btn-small" style="margin-left:var(--space-2);">
                  <i class="fas fa-external-link-alt"></i> Abrir enlace
                </a>
                <?php endif; ?>
              </div>
              <?php endif; ?>
            </div>
          </div>
          <div class="tarea-acciones-modern">
            <button type="button" class="btn-modern btn-ghost-modern btn-small" title="Editar" onclick="abrirEditarSesion(<?= Security::escapeHtml($sesion['idSesion']) ?>, '<?= Security::escapeHtml(htmlspecialchars(addslashes($sesion['titulo']))) ?>', '<?= Security::escapeHtml(htmlspecialchars(addslashes($sesion['descripcion'] ?? ''), ENT_QUOTES)) ?>', '<?= Security::escapeHtml($sesion['fechaSesion']) ?>', '<?= Security::escapeHtml($sesion['horaSesion']) ?>', '<?= Security::escapeHtml(htmlspecialchars(addslashes($sesion['enlaceReunion'] ?? ''))) ?>', '<?= Security::escapeHtml(htmlspecialchars($sesion['plataforma'] ?? '')) ?>')">
              <i class="fas fa-pen"></i>
            </button>
            <a href="sesionAsistencia.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="btn-modern btn-primary-modern btn-small">
              <i class="fas fa-list"></i> Asistencia
            </a>
            <a href="../../../controladores/profesores/aula/borrarSesion.php?id=<?= Security::escapeHtml($sesion['idSesion']) ?>" class="btn-modern btn-danger-modern btn-small" title="Eliminar" onclick="return confirm('¿Eliminar esta sesión?')">
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

<!-- ════════════════════════════════════════════════════════════════
     MODALES MODERNOS (4)
     ════════════════════════════════════════════════════════════════ -->

<!-- MODAL: CREAR CARPETA -->
<div id="modalCarpeta" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-folder-plus"></i> Nueva Carpeta</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalCarpeta')">✕</button>
    </div>
    <form method="POST" action="../../../controladores/profesores/aula/crearCarpeta.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="modal-body">
        <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <div class="modal-input-group">
          <label class="modal-label">Nombre de la carpeta</label>
          <input type="text" name="nombre" class="modal-input" placeholder="Ej: Tema 1" required>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Color</label>
          <div class="color-picker-group">
            <?php foreach ($colores as $color): ?>
            <label style="cursor:pointer;">
              <input type="radio" name="color" value="<?= Security::escapeHtml(htmlspecialchars($color)) ?>" style="display:none;" <?= Security::escapeHtml($color === '#0ea5e9' ? 'checked' : '') ?>>
              <span class="color-picker-option" style="background:<?= Security::escapeHtml(htmlspecialchars($color)) ?>;border-color:<?= Security::escapeHtml($color === '#0ea5e9' ? '#000' : 'transparent') ?>;"></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalCarpeta')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern" onclick="this.disabled=true;this.form.submit();">Crear</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: EDITAR CARPETA -->
<div id="modalEditarCarpeta" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-pen"></i> Editar Carpeta</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalEditarCarpeta')">✕</button>
    </div>
    <form id="formEditarCarpeta" method="POST" action="../../../controladores/profesores/aula/editarCarpeta.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="modal-body">
        <input type="hidden" name="idCarpeta" id="editCarpetaId">
        <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <div class="modal-input-group">
          <label class="modal-label">Nombre de la carpeta</label>
          <input type="text" id="editCarpetaNombre" name="nombre" class="modal-input" placeholder="Ej: Tema 1" required>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Color</label>
          <div id="editColorPickerGroup" class="color-picker-group">
            <?php foreach ($colores as $color): ?>
            <label style="cursor:pointer;">
              <input type="radio" name="color" value="<?= Security::escapeHtml(htmlspecialchars($color)) ?>" style="display:none;">
              <span class="color-picker-option" style="background:<?= Security::escapeHtml(htmlspecialchars($color)) ?>;"></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalEditarCarpeta')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern" onclick="this.disabled=true;this.form.submit();">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: SUBIR ARCHIVOS CON DRAG-DROP -->
<div id="modalSubir" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-cloud-upload-alt"></i> Subir Archivos</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalSubir')">✕</button>
    </div>
    <form id="formSubir" method="POST" action="../../../controladores/profesores/aula/subirArchivos.php" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="modal-body">
        <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <input type="hidden" name="subirArchivos" value="1">
        <div class="modal-input-group">
          <label class="modal-label">Seleccionar Carpeta (Opcional)</label>
          <select name="idCarpeta" class="modal-select">
            <option value="0">Sin carpeta</option>
            <?php foreach ($carpetas as $c): ?>
            <option value="<?= Security::escapeHtml($c['idCarpeta'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="drag-drop-zone" id="dragDropZone">
          <div class="drag-drop-zone-icon"><i class="fas fa-cloud-arrow-up"></i></div>
          <div class="drag-drop-zone-text">Arrastra archivos aquí</div>
          <div class="drag-drop-zone-hint">o haz clic para seleccionar</div>
          <input type="file" id="fileInput" name="archivos[]" multiple style="display:none;" accept=".pdf,.docx,.txt,.xlsx,.pptx">
        </div>
        <div id="fileList" style="margin-top:var(--space-4);"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalSubir')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern" onclick="this.disabled=true;this.form.submit();">Subir</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: MOVER ARCHIVO -->
<div id="modalMover" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-folder-arrow-down"></i> Mover Archivo</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalMover')">✕</button>
    </div>
    <form id="formMover" method="GET" action="../../../controladores/profesores/aula/moverArchivo.php">
      <div class="modal-body">
        <input type="hidden" name="id" id="moverArchivoId">
        <input type="hidden" name="modulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <p style="color:var(--color-neutral-600);font-size:var(--font-size-sm);margin-bottom:var(--space-3);">
          Archivo: <strong id="moverArchivoNombre"></strong>
        </p>
        <div class="modal-input-group">
          <label class="modal-label">Mover a:</label>
          <select name="carpeta" class="modal-select" required>
            <option value="0">Sin carpeta</option>
            <?php foreach ($carpetas as $c): ?>
            <option value="<?= Security::escapeHtml($c['idCarpeta'] ) ?>"><?= Security::escapeHtml(htmlspecialchars($c['nombre'])) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalMover')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern">Mover</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: VIEWER DE ARCHIVOS -->
<div id="modalViewer" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <span id="viewerNombre" class="modal-header-titulo" style="font-size:var(--font-size-base);"></span>
      <button class="modal-close-btn" onclick="cerrarModal('modalViewer')">✕</button>
    </div>
    <div id="viewerContenedor" style="padding:var(--space-4);flex:1;overflow:auto;">
      <p style="text-align:center;color:var(--color-neutral-400);"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>
    </div>
  </div>
</div>

<!-- MODAL: CREAR SESIÓN VIVA -->
<div id="modalSesion" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-video"></i> Nueva Sesión Viva</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalSesion')">✕</button>
    </div>
    <form method="POST" action="../../../controladores/profesores/aula/crearSesion.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="modal-body">
        <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <input type="hidden" name="idCiclo" value="<?= Security::escapeHtml($modulo['idCiclo'] ) ?>">
        <div class="modal-input-group">
          <label class="modal-label">Título de la sesión</label>
          <input type="text" name="titulo" class="modal-input" placeholder="Ej: Clase introductoria" required>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Descripción (Opcional)</label>
          <textarea name="descripcion" class="modal-textarea" placeholder="Temas a cubrir..."></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-2);">
          <div class="modal-input-group">
            <label class="modal-label">Fecha</label>
            <input type="date" name="fechaSesion" class="modal-input" required>
          </div>
          <div class="modal-input-group">
            <label class="modal-label">Hora</label>
            <input type="time" name="horaSesion" class="modal-input" required>
          </div>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Plataforma (Opcional)</label>
          <input type="text" name="plataforma" class="modal-input" placeholder="Ej: Google Meet, Zoom, Teams">
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Enlace de reunión (Opcional)</label>
          <input type="url" name="enlaceReunion" class="modal-input" placeholder="https://...">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalSesion')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern" onclick="this.disabled=true;this.form.submit();">Crear Sesión</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL: EDITAR SESIÓN VIVA -->
<div id="modalEditarSesion" class="modal-backdrop">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-header-titulo"><i class="fas fa-pen"></i> Editar Sesión</h2>
      <button class="modal-close-btn" onclick="cerrarModal('modalEditarSesion')">✕</button>
    </div>
    <form id="formEditarSesion" method="POST" action="../../../controladores/profesores/aula/editarSesion.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <div class="modal-body">
        <input type="hidden" name="idSesion" id="editSesionId">
        <input type="hidden" name="idModulo" value="<?= Security::escapeHtml($idModulo ) ?>">
        <div class="modal-input-group">
          <label class="modal-label">Título</label>
          <input type="text" id="editSesionTitulo" name="titulo" class="modal-input" required>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Descripción</label>
          <textarea id="editSesionDesc" name="descripcion" class="modal-textarea"></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-2);">
          <div class="modal-input-group">
            <label class="modal-label">Fecha</label>
            <input type="date" id="editSesionFecha" name="fechaSesion" class="modal-input" required>
          </div>
          <div class="modal-input-group">
            <label class="modal-label">Hora</label>
            <input type="time" id="editSesionHora" name="horaSesion" class="modal-input" required>
          </div>
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Plataforma</label>
          <input type="text" id="editSesionPlat" name="plataforma" class="modal-input">
        </div>
        <div class="modal-input-group">
          <label class="modal-label">Enlace de reunión</label>
          <input type="url" id="editSesionEnlace" name="enlaceReunion" class="modal-input">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-modern btn-secondary-modern" onclick="cerrarModal('modalEditarSesion')">Cancelar</button>
        <button type="submit" class="btn-modern btn-primary-modern" onclick="this.disabled=true;this.form.submit();">Guardar Cambios</button>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════════
     JAVASCRIPT AVANZADO
     ════════════════════════════════════════════════════════════════ -->
<script>
// Utilidades de modales
function abrirModal(id) {
  document.getElementById(id).classList.add('active');
}

function cerrarModal(id) {
  document.getElementById(id).classList.remove('active');
}

function cerrarModalAlEsc(event) {
  if (event.key === 'Escape') {
    document.querySelectorAll('.modal-backdrop.active').forEach(m => {
      m.classList.remove('active');
    });
  }
}

document.addEventListener('keydown', cerrarModalAlEsc);

// Cerrar modal al hacer clic afuera
document.querySelectorAll('.modal-backdrop').forEach(modal => {
  modal.addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('active');
  });
});

// Tab switching
function cambiarTab(tabName, btn) {
  document.querySelectorAll('#tab-archivos, #tab-sesiones').forEach(t => t.style.display = 'none');
  document.getElementById('tab-' + tabName).style.display = 'block';
  document.querySelectorAll('.tab-modern').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

// Editar carpeta
function abrirEditarCarpeta(id, nombre, color) {
  document.getElementById('editCarpetaId').value = id;
  document.getElementById('editCarpetaNombre').value = nombre;
  document.querySelector('#editColorPickerGroup input[value="' + color + '"]').checked = true;
  document.querySelectorAll('#editColorPickerGroup .color-picker-option').forEach(opt => opt.classList.remove('active'));
  document.querySelector('#editColorPickerGroup input[value="' + color + '"]').closest('label').querySelector('.color-picker-option').classList.add('active');
  abrirModal('modalEditarCarpeta');
}

// Mover archivo
function abrirMoverArchivo(id, nombre) {
  document.getElementById('moverArchivoId').value = id;
  document.getElementById('moverArchivoNombre').textContent = nombre;
  abrirModal('modalMover');
}

// Editar sesión
function abrirEditarSesion(id, titulo, desc, fecha, hora, enlace, plat) {
  document.getElementById('editSesionId').value = id;
  document.getElementById('editSesionTitulo').value = titulo;
  document.getElementById('editSesionDesc').value = desc;
  document.getElementById('editSesionFecha').value = fecha;
  document.getElementById('editSesionHora').value = hora;
  document.getElementById('editSesionEnlace').value = enlace;
  document.getElementById('editSesionPlat').value = plat;
  abrirModal('modalEditarSesion');
}

// Viewer de archivos
function abrirViewerAula(url, ext, nombre) {
  document.getElementById('viewerNombre').textContent = nombre;
  document.getElementById('viewerContenedor').innerHTML = '<p style="text-align:center;padding:40px;color:var(--color-neutral-400);"><i class="fas fa-spinner fa-spin"></i> Cargando...</p>';
  abrirModal('modalViewer');

  if (ext === 'txt') {
    fetch(url).then(r => r.text()).then(t => {
      document.getElementById('viewerContenedor').innerHTML = '<div style="padding:20px;font-family:monospace;font-size:0.85rem;white-space:pre-wrap;color:var(--color-neutral-700);">' +
        t.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;') + '</div>';
    }).catch(() => {
      document.getElementById('viewerContenedor').innerHTML = '<p style="padding:20px;color:var(--color-danger);">No se pudo cargar el archivo.</p>';
    });
  } else {
    document.getElementById('viewerContenedor').innerHTML = '<div style="text-align:center;padding:40px;"><i class="fas fa-file-word" style="font-size:3rem;color:var(--color-info);"></i><p style="margin-top:12px;color:var(--color-neutral-500);">Vista previa no disponible. Descárgalo para ver.</p></div>';
  }
}

// Color picker interactivo
document.querySelectorAll('.color-picker-group').forEach(group => {
  group.querySelectorAll('input[type="radio"]').forEach(radio => {
    radio.addEventListener('change', function() {
      group.querySelectorAll('.color-picker-option').forEach(opt => {
        opt.classList.remove('active');
      });
      this.closest('label').querySelector('.color-picker-option').classList.add('active');
    });
  });
});

// Drag-drop para subir archivos
const dragDropZone = document.getElementById('dragDropZone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');

if (dragDropZone && fileInput) {
  dragDropZone.addEventListener('click', () => fileInput.click());

  dragDropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dragDropZone.classList.add('dragover');
  });

  dragDropZone.addEventListener('dragleave', () => {
    dragDropZone.classList.remove('dragover');
  });

  dragDropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dragDropZone.classList.remove('dragover');
    fileInput.files = e.dataTransfer.files;
    mostrarArchivosCargados();
  });

  fileInput.addEventListener('change', mostrarArchivosCargados);
}

function mostrarArchivosCargados() {
  fileList.innerHTML = '';
  Array.from(fileInput.files).forEach((file, idx) => {
    const item = document.createElement('div');
    item.style.cssText = 'padding:var(--space-2) var(--space-3);background:var(--color-neutral-100);border-radius:var(--radius-md);margin-bottom:var(--space-2);display:flex;align-items:center;justify-content:space-between;';
    item.innerHTML = '<span><i class="fas fa-check" style="color:var(--color-success);margin-right:var(--space-2);"></i>' + file.name + ' (' + (file.size/1024).toFixed(1) + ' KB)</span>';
    fileList.appendChild(item);
  });
}

// NOTIFICACIONES TOAST
function mostrarToast(mensaje, tipo = 'info', duracion = 3000) {
  const container = document.getElementById('toastContainer') || crearToastContainer();
  const toast = document.createElement('div');
  toast.className = `toast ${tipo}`;
  toast.innerHTML = `
    <div class="toast-icon">
      <i class="fas fa-${tipo === 'success' ? 'check-circle' : tipo === 'error' ? 'exclamation-circle' : tipo === 'warning' ? 'exclamation-triangle' : 'info-circle'}"></i>
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

// Detectar mensajes de sesión
document.addEventListener('DOMContentLoaded', function() {
  const exito = document.querySelector('.mensaje-exito');
  const error = document.querySelector('.mensaje-error');
  if (exito) {
    mostrarToast(exito.textContent.trim(), 'success', 4000);
    exito.remove();
  }
  if (error) {
    mostrarToast(error.textContent.trim(), 'error', 5000);
    error.remove();
  }
});

// BÚSQUEDA EN TIEMPO REAL
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('searchInput');
  const searchContainer = document.getElementById('searchContainer');
  let currentTab = 'archivos';

  document.querySelectorAll('.tab-modern').forEach(tab => {
    tab.addEventListener('click', function() {
      const tabContent = this.textContent.toLowerCase();
      currentTab = tabContent.includes('archivo') ? 'archivos' : 'sesiones';
      searchInput.value = '';
      searchContainer.style.display = currentTab === 'archivos' ? 'block' : 'none';
      searchInput.placeholder = `Buscar en ${currentTab}...`;
    });
  });

  if (searchInput) {
    let timeout;
    searchInput.addEventListener('input', function() {
      clearTimeout(timeout);
      timeout = setTimeout(() => {
        const query = this.value.trim();
        const param = 'search_arch';
        const pagParam = 'pag_arch';
        const url = new URL(window.location);
        url.searchParams.set(param, query);
        url.searchParams.set(pagParam, '1');
        window.history.pushState({}, '', url);
        location.reload();
      }, 500);
    });
  }
});

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
  // Primera carpeta abierta
  var primera = document.querySelector('.carpeta');
  if (primera) primera.classList.add('abierta');

  // Toggle carpetas
  document.querySelectorAll('.carpeta-header-modern').forEach(function(h) {
    h.addEventListener('click', function(e) {
      if (!e.target.closest('button') && !e.target.closest('a')) {
        this.closest('.carpeta').classList.toggle('abierta');
      }
    });
  });

  // Ver archivo
  document.querySelectorAll('[data-ver-archivo]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      abrirViewerAula(this.dataset.verArchivo, this.dataset.ext, this.dataset.nombre);
    });
  });

  // Inicializar búsqueda
  const searchContainer = document.getElementById('searchContainer');
  if (searchContainer) searchContainer.style.display = 'block';

  // Preventivo double-submit
  document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
      const btns = this.querySelectorAll('button[type="submit"]');
      btns.forEach(btn => btn.disabled = true);
      setTimeout(() => {
        btns.forEach(btn => btn.disabled = false);
      }, 5000);
    });
  });

  // Track tab switches
  document.querySelectorAll('.tab-modern').forEach(tab => {
    tab.addEventListener('click', function() {
      const tabName = this.textContent.trim().toLowerCase();
      if (window.analytics) analytics.trackTabSwitch(tabName);
    });
  });

  // Track modal opens
  document.querySelectorAll('.modal-backdrop').forEach(modal => {
    const btn = document.querySelector(`[onclick*="${modal.id}"]`);
    if (btn) {
      btn.addEventListener('click', () => {
        if (window.analytics) analytics.trackModalOpen(modal.id);
      });
    }
  });
});
</script>

<!-- SCRIPTS: THEME SYSTEM Y ANALYTICS -->

<script src="../../../public/js/analytics.js"></script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


