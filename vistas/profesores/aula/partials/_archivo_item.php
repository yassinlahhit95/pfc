<?php
// Partial: _archivo_item.php
// Variables esperadas: $arch (array con campos de aula_archivos)
$ext  = $arch['extension'];
$url  = '../../../public/uploads/aula/archivos/' . htmlspecialchars($arch['nombreArchivo'], ENT_QUOTES);
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
    <div class="aula-archivo-nombre" title="<?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'])) ?>"><?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'])) ?></div>
    <div class="aula-archivo-meta">
      <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($arch['fechaSubida']))) ?>
      <?= Security::escapeHtml($tamStr ? ' · ' . $tamStr : '') ?>
    </div>
  </div>
  <div class="aula-archivo-acciones">
    <button class="btn-accion btn-ver" title="Ver"
            data-ver-archivo="<?= Security::escapeHtml($url ) ?>"
            data-ext="<?= Security::escapeHtml($ext ) ?>"
            data-nombre="<?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'], ENT_QUOTES)) ?>">
      <i class="fas fa-eye"></i>
    </button>
    <a href="<?= Security::escapeHtml($url ) ?>" download="<?= Security::escapeHtml(htmlspecialchars($arch['nombreOriginal'], ENT_QUOTES)) ?>"
       class="btn-accion" title="Descargar" style="background:#f1f5f9;color:#475569;">
      <i class="fas fa-download"></i>
    </a>
    <button class="btn-accion" title="Mover" onclick="abrirMoverArchivo(<?= Security::escapeHtml($arch['idArchivo'] ) ?>, '<?= Security::escapeHtml(htmlspecialchars(addslashes($arch['nombreOriginal']))) ?>')">
      <i class="fas fa-folder-arrow-down"></i>
    </button>
    <a href="../../../controladores/profesores/aula/borrarArchivo.php?id=<?= Security::escapeHtml($arch['idArchivo'] ) ?>&modulo=<?= Security::escapeHtml($arch['idModulo'] ) ?>"
       class="btn-accion btn-eliminar" title="Eliminar"
       onclick="return confirm('¿Eliminar este archivo?')">
      <i class="fas fa-trash"></i>
    </a>
  </div>
</div>


