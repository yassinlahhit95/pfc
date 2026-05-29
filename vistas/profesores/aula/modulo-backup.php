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

// Agrupar archivos por carpeta
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

<div class="aula-breadcrumb">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="sep">/</span>
  <?php if ($ciclo): ?>
  <a href="modulos.php?idCiclo=<?= $ciclo['idCiclo'] ?>"><?= htmlspecialchars($ciclo['nombreCiclo']) ?></a>
  <span class="sep">/</span>
  <?php endif; ?>
  <span class="actual"><?= htmlspecialchars($modulo['nombreModulo']) ?></span>
</div>

<div class="cabecera">
  <div>
    <h1><?= htmlspecialchars(mb_strtoupper($modulo['nombreModulo'], 'UTF-8')) ?></h1>
  </div>
  <div class="caja alinear-centro espacio-pequeno">
    <button onclick="document.getElementById('modalCarpeta').style.display='flex'" class="boton-secundario">
      <i class="fas fa-folder-plus"></i> Carpeta
    </button>
    <button onclick="document.getElementById('modalSubir').style.display='flex'" class="boton-secundario">
      <i class="fas fa-cloud-upload-alt"></i> Subir archivos
    </button>
    <button onclick="document.getElementById('modalTarea').style.display='flex'" class="boton-primario">
      <i class="fas fa-plus"></i> Nueva tarea
    </button>
  </div>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<div class="aula-modulo-layout" style="margin-top:20px;">

  <!-- ── ARCHIVOS ── -->
  <div class="aula-panel">
    <div class="aula-panel-header">
      <h3><i class="fas fa-folder-open" style="color:#0ea5e9;margin-right:8px;"></i>Materiales</h3>
      <span style="font-size:0.75rem;color:#94a3b8;"><?= count($archivos) ?> archivo<?= count($archivos) != 1 ? 's' : '' ?></span>
    </div>

    <?php if (empty($carpetas) && empty($archivos)): ?>
    <div style="text-align:center;padding:40px 20px;">
      <i class="fas fa-cloud-upload-alt" style="font-size:2.5rem;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
      <p class="texto-suave" style="font-size:0.85rem;">Sin archivos. Sube el primer material.</p>
    </div>
    <?php else: ?>

    <?php foreach ($carpetas as $carpeta): ?>
    <div class="aula-carpeta">
      <div class="aula-carpeta-titulo">
        <span class="aula-carpeta-dot" style="background:<?= htmlspecialchars($carpeta['color']) ?>;"></span>
        <span><?= htmlspecialchars($carpeta['nombre']) ?></span>
        <span class="aula-carpeta-count"><?= $carpeta['totalArchivos'] ?></span>
        <i class="fas fa-chevron-right aula-carpeta-chevron"></i>
        <!-- Acciones carpeta -->
        <button onclick="abrirEditarCarpeta(<?= $carpeta['idCarpeta'] ?>, '<?= htmlspecialchars(addslashes($carpeta['nombre'])) ?>', '<?= $carpeta['color'] ?>')"
                style="background:none;border:none;cursor:pointer;padding:2px 6px;color:#94a3b8;margin-left:4px;" title="Editar">
          <i class="fas fa-pen" style="font-size:0.7rem;"></i>
        </button>
        <a href="../../../controladores/profesores/aula/borrarCarpeta.php?id=<?= $carpeta['idCarpeta'] ?>&modulo=<?= $idModulo ?>"
           style="color:#94a3b8;padding:2px 6px;" title="Eliminar"
           onclick="return confirm('¿Eliminar carpeta? Los archivos quedarán sueltos.')">
          <i class="fas fa-trash" style="font-size:0.7rem;"></i>
        </a>
      </div>
      <div class="aula-archivos-lista">
        <?php $archsEnCarpeta = $archivosPorCarpeta[$carpeta['idCarpeta']] ?? []; ?>
        <?php if (empty($archsEnCarpeta)): ?>
          <p class="aula-carpeta-vacia">Carpeta vacía</p>
        <?php else: ?>
          <?php foreach ($archsEnCarpeta as $arch): ?>
          <?php include __DIR__ . '/partials/_archivo_item.php'; ?>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if (!empty($archivosSueltos)): ?>
    <div class="aula-carpeta">
      <div class="aula-carpeta-titulo">
        <span class="aula-carpeta-dot" style="background:#94a3b8;"></span>
        <span>Sin carpeta</span>
        <span class="aula-carpeta-count"><?= count($archivosSueltos) ?></span>
        <i class="fas fa-chevron-right aula-carpeta-chevron"></i>
      </div>
      <div class="aula-archivos-lista">
        <?php foreach ($archivosSueltos as $arch): ?>
        <?php include __DIR__ . '/partials/_archivo_item.php'; ?>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
  </div>

  <!-- ── TAREAS ── -->
  <div class="aula-panel">
    <div class="aula-panel-header">
      <h3><i class="fas fa-tasks" style="color:#8b5cf6;margin-right:8px;"></i>Tareas</h3>
      <span style="font-size:0.75rem;color:#94a3b8;"><?= count($tareas) ?></span>
    </div>

    <?php if (empty($tareas)): ?>
    <div style="text-align:center;padding:40px 20px;">
      <i class="fas fa-clipboard-list" style="font-size:2.5rem;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
      <p class="texto-suave" style="font-size:0.85rem;">Sin tareas asignadas.</p>
    </div>
    <?php else: ?>
    <?php foreach ($tareas as $tarea):
      $totalEst = contarEstudiantesCicloAula($modulo['idCiclo']);
      $porcentaje = $totalEst > 0 ? round(($tarea['totalEntregas'] / $totalEst) * 100) : 0;
      $esReciente = strtotime($tarea['fechaCreacion']) > strtotime('-24 hours');
    ?>
    <div class="aula-tarea-item" style="position:relative;">
      <?php if (!$tarea['publicado']): ?><span style="position:absolute;top:8px;right:8px;background:#fee2e2;color:#dc2626;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:20px;"><i class="fas fa-eye-slash"></i> OCULTA</span><?php endif; ?>
      <?php if ($esReciente): ?><span style="position:absolute;top:8px;right:<?= $tarea['publicado'] ? '8px' : '90px' ?>;background:#dbeafe;color:#1d4ed8;font-size:0.65rem;font-weight:700;padding:2px 8px;border-radius:20px;"><i class="fas fa-sparkles"></i> NUEVO</span><?php endif; ?>
      <div class="aula-tarea-titulo"><?= htmlspecialchars($tarea['titulo']) ?></div>
      <?php if ($tarea['descripcion']): ?>
      <div class="aula-tarea-desc"><?= htmlspecialchars($tarea['descripcion']) ?></div>
      <?php endif; ?>
      <div style="margin:10px 0;font-size:0.75rem;color:#64748b;">
        <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;">
          <i class="fas fa-check-circle"></i> <strong><?= $tarea['totalEntregas'] ?>/<?= $totalEst ?></strong> entregas
        </div>
        <div style="width:100%;height:5px;background:#f1f5f9;border-radius:3px;overflow:hidden;">
          <div style="height:100%;background:linear-gradient(90deg,#0ea5e9,#06b6d4);width:<?= $porcentaje ?>%;transition:width 0.3s;"></div>
        </div>
      </div>
      <div class="aula-tarea-footer">
        <div class="caja alinear-centro espacio-pequeno">
          <button type="button" class="btn-accion" title="<?= $tarea['publicado'] ? 'Ocultar' : 'Publicar' ?>"
                  onclick="toggleTarea(<?= $tarea['idTarea'] ?>, this, <?= $idModulo ?>)">
            <i class="fas fa-<?= $tarea['publicado'] ? 'eye' : 'eye-slash' ?>"></i>
          </button>
          <button type="button" class="btn-accion btn-editar" title="Editar"
                  onclick="abrirEditarTarea(<?= $tarea['idTarea'] ?>, '<?= htmlspecialchars(addslashes($tarea['titulo'])) ?>', '<?= htmlspecialchars(addslashes($tarea['descripcion'] ?? ''), ENT_QUOTES) ?>')">
            <i class="fas fa-pen"></i>
          </button>
          <a href="verEntregas.php?id=<?= $tarea['idTarea'] ?>" class="boton-primario btn-pequeno">
            <i class="fas fa-inbox"></i> Ver entregas
          </a>
          <a href="../../../controladores/profesores/aula/borrarTarea.php?id=<?= $tarea['idTarea'] ?>&modulo=<?= $idModulo ?>"
             class="btn-accion btn-eliminar" title="Eliminar tarea"
             onclick="return confirm('¿Eliminar esta tarea y todas sus entregas?')">
            <i class="fas fa-trash"></i>
          </a>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<!-- MODAL CARPETA -->
<div id="modalCarpeta" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:400px;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Nueva Carpeta</h3>
      <button onclick="document.getElementById('modalCarpeta').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/crearCarpeta.php" method="POST" class="formulario">
      <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
      <div class="campo"><label>Nombre *</label><input type="text" name="nombre" required maxlength="100" placeholder="Ej: Tema 1 – Introducción"></div>
      <div class="campo">
        <label>Color</label>
        <div class="caja alinear-centro espacio-pequeno" style="flex-wrap:wrap;gap:8px;margin-top:6px;">
          <?php foreach ($colores as $col): ?>
          <label style="cursor:pointer;">
            <input type="radio" name="color" value="<?= $col ?>" <?= $col === '#0ea5e9' ? 'checked' : '' ?> style="display:none;">
            <span style="display:block;width:24px;height:24px;border-radius:50%;background:<?= $col ?>;border:2px solid transparent;"
                  onclick="this.closest('.caja').querySelectorAll('[name=color]+span').forEach(s=>s.style.borderColor='transparent');this.previousElementSibling.checked=true;this.style.borderColor='#1e293b';"></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <input type="submit" name="crearCarpeta" class="boton-primario" value="Crear" style="width:100%;margin-top:12px;">
    </form>
  </div>
</div>

<!-- MODAL SUBIR ARCHIVOS -->
<div id="modalSubir" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Subir Archivos</h3>
      <button onclick="document.getElementById('modalSubir').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/subirArchivos.php" method="POST" enctype="multipart/form-data" class="formulario">
      <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
      <div class="campo">
        <label>Carpeta (opcional)</label>
        <select name="idCarpeta">
          <option value="">Sin carpeta</option>
          <?php foreach ($carpetas as $c): ?>
          <option value="<?= $c['idCarpeta'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="aula-upload-zona" id="aulaUploadZona">
        <i class="fas fa-cloud-upload-alt icono-upload"></i>
        <p><strong>Arrastra aquí</strong> o haz clic para seleccionar</p>
        <p class="aula-upload-tipos">Solo PDF · DOCX · TXT · Máx. 20 MB por archivo</p>
        <input type="file" id="aulaInputArchivos" name="archivos[]" multiple accept=".pdf,.docx,.txt" style="display:none;">
      </div>
      <div id="aulaPreviewLista" class="aula-preview-lista"></div>
      <p id="aulaUploadAviso" style="font-size:0.8rem;margin-top:8px;"></p>
      <div class="campo">
        <label>Descripción (opcional)</label>
        <input type="text" name="descripcion" placeholder="Ej: Apuntes del tema 3" maxlength="500">
      </div>
      <input type="submit" name="subirArchivos" class="boton-primario" value="Subir archivos" style="width:100%;margin-top:12px;" onclick="this.disabled=true;this.textContent='Subiendo...';this.form.submit();">
    </form>
  </div>
</div>

<!-- MODAL NUEVA TAREA -->
<div id="modalTarea" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:500px;max-height:90vh;overflow-y:auto;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Nueva Tarea</h3>
      <button onclick="document.getElementById('modalTarea').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/crearTarea.php" method="POST" enctype="multipart/form-data" class="formulario">
      <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
      <div class="campo"><label>Título *</label><input type="text" name="titulo" required maxlength="150" placeholder="Ej: Ejercicio práctico 1"></div>
      <div class="campo"><label>Descripción / Enunciado</label><textarea name="descripcion" rows="5" placeholder="Instrucciones, recursos, objetivos..."></textarea></div>
      <div class="campo">
        <label>Archivo adjunto (PDF/DOCX/TXT)</label>
        <input type="file" name="archivoTarea" accept=".pdf,.docx,.txt">
      </div>
      <input type="submit" name="guardarTarea" class="boton-primario" value="Publicar tarea" style="width:100%;margin-top:12px;">
    </form>
  </div>
</div>

<!-- MODAL EDITAR CARPETA -->
<div id="modalEditarCarpeta" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:380px;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Editar Carpeta</h3>
      <button onclick="document.getElementById('modalEditarCarpeta').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/editarCarpeta.php" method="POST" class="formulario">
      <input type="hidden" name="idCarpeta" id="editCarpetaId">
      <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
      <div class="campo"><label>Nombre</label><input type="text" name="nombre" id="editCarpetaNombre" required maxlength="100"></div>
      <div class="campo">
        <label>Color</label>
        <div class="caja alinear-centro espacio-pequeno" style="flex-wrap:wrap;gap:8px;margin-top:6px;">
          <?php foreach ($colores as $col): ?>
          <label style="cursor:pointer;">
            <input type="radio" name="color" value="<?= $col ?>" class="editColorRadio" style="display:none;">
            <span style="display:block;width:24px;height:24px;border-radius:50%;background:<?= $col ?>;border:2px solid transparent;"
                  onclick="this.closest('.caja').querySelectorAll('.editColorRadio+span').forEach(s=>s.style.borderColor='transparent');this.previousElementSibling.checked=true;this.style.borderColor='#1e293b';"></span>
          </label>
          <?php endforeach; ?>
        </div>
      </div>
      <input type="submit" class="boton-primario" value="Guardar cambios" style="width:100%;margin-top:12px;">
    </form>
  </div>
</div>

<!-- MODAL MOVER ARCHIVO -->
<div id="modalMoverArchivo" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:380px;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Mover archivo</h3>
      <button onclick="document.getElementById('modalMoverArchivo').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <p style="font-size:0.85rem;color:#374151;margin-bottom:16px;">
      <strong id="moverArchivoNombre"></strong>
    </p>
    <div style="max-height:300px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;">
      <a href="../../../controladores/profesores/aula/moverArchivo.php?id=" id="linkSinCarpeta"
         style="display:block;padding:10px 14px;color:#374151;text-decoration:none;font-size:0.85rem;border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''">
        <i class="fas fa-times" style="color:#94a3b8;"></i> Sin carpeta
      </a>
      <?php foreach ($carpetas as $carp): ?>
      <a href="../../../controladores/profesores/aula/moverArchivo.php?id=" data-carp="<?= $carp['idCarpeta'] ?>" data-modulo="<?= $idModulo ?>"
         style="display:block;padding:10px 14px;color:#374151;text-decoration:none;font-size:0.85rem;border-bottom:1px solid #f1f5f9;transition:background 0.15s;"
         onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background=''"
         onclick="moverArchivoA(this); return false;">
        <i class="fas fa-folder" style="color:<?= $carp['color'] ?>;"></i> <?= htmlspecialchars($carp['nombre']) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <input type="hidden" id="moverArchivoId">
  </div>
</div>

<!-- MODAL EDITAR TAREA -->
<div id="modalEditarTarea" class="modal-pdf-overlay" style="display:none;">
  <div style="background:#fff;border-radius:16px;padding:28px;width:100%;max-width:460px;">
    <div class="caja espacio-entre-elementos alinear-centro" style="margin-bottom:20px;">
      <h3 style="font-size:1rem;font-weight:700;margin:0;">Editar Tarea</h3>
      <button onclick="document.getElementById('modalEditarTarea').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:1.2rem;color:#94a3b8;">✕</button>
    </div>
    <form action="../../../controladores/profesores/aula/editarTarea.php" method="POST" class="formulario">
      <input type="hidden" name="idTarea" id="editTareaId">
      <input type="hidden" name="idModulo" value="<?= $idModulo ?>">
      <div class="campo"><label>Título</label><input type="text" name="titulo" id="editTareaTitulo" required maxlength="150"></div>
      <div class="campo"><label>Descripción</label><textarea name="descripcion" id="editTareaDesc" rows="4"></textarea></div>
      <input type="submit" class="boton-primario" value="Guardar cambios" style="width:100%;margin-top:12px;">
    </form>
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

<script>
function abrirEditarCarpeta(id, nombre, color) {
  document.getElementById('editCarpetaId').value = id;
  document.getElementById('editCarpetaNombre').value = nombre;
  document.querySelectorAll('.editColorRadio').forEach(function(r) {
    r.checked = (r.value === color);
    var span = r.nextElementSibling;
    if (span) span.style.borderColor = (r.value === color) ? '#1e293b' : 'transparent';
  });
  document.getElementById('modalEditarCarpeta').style.display = 'flex';
}
function abrirEditarTarea(id, titulo, desc) {
  document.getElementById('editTareaId').value = id;
  document.getElementById('editTareaTitulo').value = titulo;
  document.getElementById('editTareaDesc').value = desc;
  document.getElementById('modalEditarTarea').style.display = 'flex';
}
function toggleTarea(id, btn, idMod) {
  fetch('../../../controladores/profesores/aula/toggleTarea.php?id=' + id, { method: 'GET' })
    .then(() => { location.reload(); })
    .catch(() => alert('Error al cambiar estado de la tarea'));
}
function abrirMoverArchivo(id, nombre) {
  document.getElementById('moverArchivoId').value = id;
  document.getElementById('moverArchivoNombre').textContent = nombre;
  document.getElementById('linkSinCarpeta').href = '../../../controladores/profesores/aula/moverArchivo.php?id=' + id + '&carpeta=0&modulo=<?= $idModulo ?>';
  document.querySelectorAll('#modalMoverArchivo [data-carp]').forEach(function(a) {
    a.href = '../../../controladores/profesores/aula/moverArchivo.php?id=' + id + '&carpeta=' + a.dataset.carp + '&modulo=<?= $idModulo ?>';
  });
  document.getElementById('modalMoverArchivo').style.display = 'flex';
}
function moverArchivoA(el) {
  if (confirm('¿Mover archivo a esta carpeta?')) {
    window.location.href = el.href;
  }
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
