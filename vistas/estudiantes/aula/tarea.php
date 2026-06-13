<?php
require_once __DIR__ . "/../../../include/Security.php";
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$idTarea    = intval($_GET['id'] ?? 0);
$tarea      = obtenerTareaPorIdAula($idTarea);
if (!$tarea || !$tarea['publicado']) { header("Location: index.php"); exit; }

$estudiante = obtenerEstudiantePorId($idEstudiante);
if ($tarea['idCiclo'] != $estudiante['idCiclo']) { header("Location: index.php"); exit; }

$entrega    = obtenerEntregaAula($idTarea, $idEstudiante);
$versiones  = $entrega ? listarVersionesPorEntregaAula($idTarea, $idEstudiante) : [];
$comentarios = $entrega ? listarComentariosPorEntregaAula($entrega['idEntrega']) : [];

$gravEstHash = md5(strtolower(trim($estudiante['emailEstudiante'] ?? '')));

$tituloDelPagina = "AULAPRO | " . mb_strtoupper($tarea['titulo'], 'UTF-8');
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="aula-breadcrumb">
  <a href="index.php"><i class="fas fa-chalkboard"></i> Aula</a>
  <span class="sep">/</span>
  <a href="modulo.php?id=<?= Security::escapeHtml($tarea['idModulo'] ) ?>"><?= Security::escapeHtml($tarea['nombreModulo']) ?></a>
  <span class="sep">/</span>
  <span class="actual"><?= Security::escapeHtml($tarea['titulo']) ?></span>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= Security::escapeHtml($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div><?php endif; ?>

<!-- ENUNCIADO -->
<div class="panel margen-arriba">
  <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
    <div>
      <h2 style="font-size:1.2rem;font-weight:700;color:#1e293b;"><?= Security::escapeHtml($tarea['titulo']) ?></h2>
      <p class="texto-suave" style="font-size:0.8rem;margin-top:4px;">
        <i class="fas fa-user-tie"></i> <?= Security::escapeHtml($tarea['nombreProfesor']) ?> ·
        <i class="fas fa-book"></i> <?= Security::escapeHtml($tarea['nombreModulo']) ?> ·
        <i class="fas fa-calendar"></i> <?= Security::escapeHtml(date('d/m/Y', strtotime($tarea['fechaCreacion']))) ?>
      </p>
    </div>
    <?php if ($entrega): ?>
    <span class="aula-estado-badge <?= Security::escapeHtml($entrega['estado']==='corregida' ? 'aula-estado-corregida' : 'aula-estado-enviada') ?>">
      <?= Security::escapeHtml($entrega['estado']==='corregida' ? '<i class="fas fa-check-circle"></i> Corregida · '.$entrega['nota'].'/10' : '<i class="fas fa-paper-plane"></i> Enviada (v'.$entrega['version'].')') ?>
    </span>
    <?php else: ?>
    <span class="aula-estado-badge aula-estado-pendiente"><i class="fas fa-clock"></i> Pendiente</span>
    <?php endif; ?>
  </div>

  <?php if ($tarea['descripcion']): ?>
  <div style="background:#f8fafc;border-radius:10px;padding:16px 20px;border-left:3px solid #8b5cf6;margin-bottom:16px;">
    <p style="white-space:pre-wrap;font-size:0.9rem;color:#374151;line-height:1.7;"><?= Security::escapeHtml($tarea['descripcion']) ?></p>
  </div>
  <?php endif; ?>

  <?php if ($tarea['archivoAdjunto']): ?>
  <?php $extAdj = strtolower(pathinfo($tarea['archivoAdjunto'], PATHINFO_EXTENSION)); ?>
  <div style="margin-top:8px;">
    <button class="boton-secundario btn-pequeno"
            data-ver-archivo="../../../public/uploads/aula/archivos/<?= Security::escapeHtml($tarea['archivoAdjunto']) ?>"
            data-ext="<?= Security::escapeHtml($extAdj ) ?>"
            data-nombre="Adjunto tarea">
      <i class="fas fa-paperclip"></i> Ver archivo adjunto
    </button>
  </div>
  <?php endif; ?>
</div>

<!-- CALIFICACIÓN -->
<?php if ($entrega && $entrega['estado']==='corregida'): ?>
<div class="panel margen-arriba" style="border-left:4px solid #10b981;">
  <div class="titulo-tarjeta"><h3>Calificación</h3></div>
  <div class="caja alinear-centro espacio-grande">
    <div style="text-align:center;">
      <div style="font-size:2.5rem;font-weight:700;color:<?= Security::escapeHtml($entrega['nota']>=5?'#15803d':'#dc2626') ?>;"><?= Security::escapeHtml($entrega['nota'] ) ?></div>
      <div style="font-size:0.75rem;color:#94a3b8;font-weight:600;">SOBRE 10</div>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- FEEDBACK THREAD -->
<?php if (!empty($comentarios)): ?>
<div class="panel margen-arriba">
  <div class="titulo-tarjeta"><h3><i class="fas fa-comments" style="color:#0ea5e9;margin-right:6px;"></i>Feedback</h3></div>
  <div class="aula-feedback-thread">
    <?php foreach ($comentarios as $com):
      $esProfesor = $com['tipoUsuario'] === 'profesor';
      $avatarHash = $esProfesor ? md5($tarea['nombreProfesor']) : $gravEstHash;
    ?>
    <div class="aula-feedback-msg <?= Security::escapeHtml(!$esProfesor ? 'propio' : '') ?>">
      <img src="https://www.gravatar.com/avatar/<?= Security::escapeHtml($avatarHash ) ?>?d=identicon&s=64"
           alt="avatar" class="aula-feedback-avatar">
      <div>
        <div class="aula-feedback-burbuja">
          <?= Security::escapeHtml($com['mensaje']) ?>
          <?php if ($com['archivoCorreccion']): ?>
          <div style="margin-top:8px;">
            <a href="../../../public/uploads/aula/correcciones/<?= Security::escapeHtml($com['archivoCorreccion']) ?>"
               target="_blank" style="font-size:0.78rem;color:#0ea5e9;display:flex;align-items:center;gap:4px;">
              <i class="fas fa-paperclip"></i> Archivo adjunto
            </a>
          </div>
          <?php endif; ?>
        </div>
        <div class="aula-feedback-fecha">
          <?= Security::escapeHtml($esProfesor ? $tarea['nombreProfesor'] : 'Tú') ?> ·
          <?= Security::escapeHtml(date('d/m H:i', strtotime($com['fechaComentario']))) ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>

<!-- MI ENTREGA -->
<div class="panel margen-arriba">
  <div class="titulo-tarjeta caja espacio-entre-elementos alinear-centro">
    <h3>Mi Entrega</h3>
    <?php if ($entrega): ?>
    <span style="font-size:0.75rem;color:#94a3b8;">
      Versión <?= Security::escapeHtml($entrega['version'] ) ?> · <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($entrega['fechaEntrega']))) ?>
    </span>
    <?php endif; ?>
  </div>

  <?php if ($entrega): ?>
  <?php if ($entrega['respuesta']): ?>
  <div style="background:#f8fafc;border-radius:10px;padding:14px 18px;border:1px solid #e2e8f0;margin-bottom:16px;">
    <p style="white-space:pre-wrap;font-size:0.88rem;color:#374151;"><?= Security::escapeHtml($entrega['respuesta']) ?></p>
  </div>
  <?php endif; ?>
  <?php if ($entrega['archivoEntrega']): ?>
  <div style="margin-bottom:16px;">
    <a href="../../../public/uploads/aula/entregas/<?= Security::escapeHtml($entrega['archivoEntrega']) ?>"
       target="_blank" class="boton-secundario btn-pequeno" style="display:inline-flex;">
      <i class="fas fa-paperclip"></i> Mi archivo entregado
    </a>
  </div>
  <?php endif; ?>

  <?php if (!empty($versiones)): ?>
  <details style="margin-bottom:16px;">
    <summary style="cursor:pointer;font-size:0.8rem;color:#64748b;font-weight:600;">Historial de versiones (<?= Security::escapeHtml(count($versiones)) ?>)</summary>
    <div class="aula-historial" style="margin-top:8px;">
      <?php foreach ($versiones as $v): ?>
      <div class="aula-historial-item">
        <div class="aula-historial-version">v<?= Security::escapeHtml($v['version'] ) ?></div>
        <div style="flex:1;">
          <p style="font-size:0.78rem;color:#64748b;"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($v['fechaVersion']))) ?></p>
          <?php if ($v['respuesta']): ?>
          <p style="font-size:0.8rem;color:#374151;margin-top:2px;"><?= Security::escapeHtml(substr($v['respuesta'],0,100)) . (strlen($v['respuesta'])>100?'…':'') ?></p>
          <?php endif; ?>
        </div>
        <?php if ($v['archivoEntrega']): ?>
        <a href="../../../public/uploads/aula/entregas/<?= Security::escapeHtml($v['archivoEntrega']) ?>"
           target="_blank" class="btn-accion btn-ver" title="Ver v<?= Security::escapeHtml($v['version'] ) ?>"><i class="fas fa-eye"></i></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </details>
  <?php endif; ?>
  <?php endif; ?>

  <!-- FORMULARIO ENVÍO -->
  <form action="../../../controladores/estudiantes/aula/enviarEntrega.php" method="POST" enctype="multipart/form-data" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
    <input type="hidden" name="idTarea" value="<?= Security::escapeHtml($idTarea ) ?>">
    <div class="campo">
      <label><?= Security::escapeHtml($entrega ? 'Actualizar respuesta' : 'Tu respuesta') ?></label>
      <textarea name="respuesta" rows="5" placeholder="Escribe tu respuesta, explicación o código..."><?= Security::escapeHtml($entrega ? ($entrega['respuesta'] ?? '') : '') ?></textarea>
    </div>
    <div class="aula-upload-zona" id="aulaUploadZona" style="margin-bottom:12px;">
      <i class="fas fa-cloud-upload-alt icono-upload"></i>
      <p><strong>Arrastra tu archivo</strong> o haz clic</p>
      <p class="aula-upload-tipos">Solo PDF · DOCX · TXT · Máx. 20 MB</p>
      <input type="file" id="aulaInputArchivos" name="archivoEntrega" accept=".pdf,.docx,.txt" style="display:none;">
    </div>
    <div id="aulaPreviewLista" class="aula-preview-lista"></div>
    <p id="aulaUploadAviso" style="font-size:0.8rem;margin-top:4px;"></p>
    <input type="submit" name="enviarEntrega" class="boton-primario"
           value="<?= Security::escapeHtml($entrega ? 'Reenviar entrega' : 'Enviar entrega') ?>"
           style="margin-top:12px;">
  </form>
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


