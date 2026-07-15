<?php
// CTA / Banner secundario (ancho completo, fino)
?>
<section class="lp-cta-secundario" id="cta_secundario_<?= $idSec ?? uniqid() ?>"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-cta-sec-inner">
    <div class="lp-cta-sec-texto">
      <strong><i class="fas fa-bullhorn"></i> <?= Security::escapeHtml($contenido['titulo'] ?? '') ?></strong>
    </div>
    <?php if (!empty($contenido['botonTexto'])): ?>
    <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#')) ?>" class="lp-boton-fantasma lp-cta-sec-btn">
      <?= Security::escapeHtml($contenido['botonTexto']) ?> <i class="fas fa-arrow-right"></i>
    </a>
    <?php endif; ?>
  </div>
</section>




