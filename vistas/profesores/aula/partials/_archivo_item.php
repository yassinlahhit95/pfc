<?php
// Partial: _archivo_item.php
// Variables esperadas: $archivo (array con campos de aula_archivos)
$ext  = $archivo['extension'];
$url  = '../../../public/uploads/aula/archivos/' . Security::escapeHtml($archivo['nombreArchivo']);
$iconClass = $ext === 'pdf' ? 'pdf' : ($ext === 'docx' ? 'docx' : 'txt');
$faIcon    = $ext === 'pdf' ? 'fa-file-pdf' : ($ext === 'docx' ? 'fa-file-word' : 'fa-file-alt');
$tamStr    = $archivo['tamanio'] > 0
    ? ($archivo['tamanio'] > 1048576 ? round($archivo['tamanio']/1048576,1).' MB' : round($archivo['tamanio']/1024,1).' KB')
    : '';
?>
<div class="aula-archivo-item">
  <div class="aula-archivo-icono <?= Security::escapeHtml($iconClass) ?>">
    <i class="fas <?= Security::escapeHtml($faIcon) ?>"></i>
  </div>
  <div class="aula-archivo-info">
    <div class="aula-archivo-nombre" title="<?= Security::escapeHtml($archivo['nombreOriginal']) ?>"><?= Security::escapeHtml($archivo['nombreOriginal']) ?></div>
    <div class="aula-archivo-meta">
      <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($archivo['fechaSubida']))) ?>
      <?= Security::escapeHtml($tamStr ? ' · ' . $tamStr : '') ?>
    </div>
  </div>
  <div class="aula-archivo-acciones">
    <button class="btn-accion btn-ver" title="Ver"
            data-ver-archivo="<?= Security::escapeHtml($url) ?>"
            data-ext="<?= Security::escapeHtml($ext) ?>"
            data-nombre="<?= Security::escapeHtml($archivo['nombreOriginal']) ?>">
      <i class="fas fa-eye"></i>
    </button>
    <a href="<?= Security::escapeHtml($url) ?>" download="<?= Security::escapeHtml($archivo['nombreOriginal']) ?>"
       class="btn-accion" title="Descargar" style="background:var(--surface-2);color:var(--dim);">
      <i class="fas fa-download"></i>
    </a>
    <button class="btn-accion" title="Mover" onclick="abrirMoverArchivo(<?= Security::escapeHtml($archivo['idArchivo']) ?>, '<?= Security::escapeHtml(addslashes($archivo['nombreOriginal'])) ?>')">
      <i class="fas fa-folder-arrow-down"></i>
    </button>
    <form method="POST" action="../../../controladores/profesores/aula/borrarArchivo.php"
          style="display:inline" data-ajax-confirm="¿Eliminar este archivo?">
      <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
      <input type="hidden" name="id" value="<?= Security::escapeHtml($archivo['idArchivo']) ?>">
      <input type="hidden" name="modulo" value="<?= Security::escapeHtml($archivo['idModulo']) ?>">
      <button type="submit" class="btn-accion btn-eliminar" title="Eliminar">
        <i class="fas fa-trash"></i>
      </button>
    </form>
  </div>
</div>

