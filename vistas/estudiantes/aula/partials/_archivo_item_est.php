<?php
// Partial: _archivo_item_est.php (estudiante - sin borrar)
$ext  = $arch['extension'];
$url  = '../../../public/uploads/aula/archivos/' . Security::escapeHtml($arch['nombreArchivo']);
$iconClass = $ext === 'pdf' ? 'pdf' : ($ext === 'docx' ? 'docx' : 'txt');
$faIcon    = $ext === 'pdf' ? 'fa-file-pdf' : ($ext === 'docx' ? 'fa-file-word' : 'fa-file-alt');
$tamStr    = $arch['tamanio'] > 0
    ? ($arch['tamanio'] > 1048576 ? round($arch['tamanio']/1048576,1).' MB' : round($arch['tamanio']/1024,1).' KB')
    : '';
?>
<div class="aula-archivo-item">
  <div class="aula-archivo-icono <?= Security::escapeHtml($iconClass ) ?>">
    <i class="fas <?= Security::escapeHtml($faIcon ) ?>"></i>
  </div>
  <div class="aula-archivo-info">
    <div class="aula-archivo-nombre" title="<?= Security::escapeHtml($arch['nombreOriginal']) ?>"><?= Security::escapeHtml($arch['nombreOriginal']) ?></div>
    <div class="aula-archivo-meta">
      <?= Security::escapeHtml($arch['nombreProfesor']) ?> ·
      <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?>
      <?= Security::escapeHtml($tamStr ? ' · '.$tamStr : '') ?>
    </div>
  </div>
  <div class="aula-archivo-acciones">
    <button class="btn-accion btn-ver" title="Ver"
            data-ver-archivo="<?= Security::escapeHtml($url ) ?>"
            data-ext="<?= Security::escapeHtml($ext ) ?>"
            data-nombre="<?= Security::escapeHtml($arch['nombreOriginal']) ?>">
      <i class="fas fa-eye"></i>
    </button>
    <a href="<?= Security::escapeHtml($url ) ?>" download="<?= Security::escapeHtml($arch['nombreOriginal']) ?>"
       class="btn-accion" title="Descargar" style="background:#f1f5f9;color:#475569;">
      <i class="fas fa-download"></i>
    </a>
  </div>
</div>


