<?php
// CTA / Banner secundario (ancho completo, fino)
?>
<section class="lp-cta-secundario" id="cta_secundario_<?= uniqid() ?>"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-cta-sec-inner">
    <div class="lp-cta-sec-texto">
      <strong><i class="fas fa-bullhorn"></i> <span<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></span></strong>
    </div>
    <?php if (!empty($contenido['botonTexto'])): ?>
    <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#')) ?>" class="lp-boton-fantasma lp-cta-sec-btn">
      <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
    </a>
    <?php endif; ?>
  </div>
</section>
