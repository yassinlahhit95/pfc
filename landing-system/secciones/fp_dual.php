<?php
// Sección informativa de FP Dual (marketing; independiente del módulo feature_fp_dual).
$variante = $contenido['variante'] ?? 'split';
$imgUrl = landing_img_url($contenido['imagen'] ?? '');
$items  = $contenido['items'] ?? [];
?>
<section class="lp-sec lp-fpdual lp-fpdual-<?= Security::escapeHtml($variante) ?>" id="fp_dual"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-fpdual-inner">
    <div class="lp-fpdual-texto">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['texto'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['texto'])) ?></p>
      <?php endif; ?>
      <?php if ($items): ?>
      <ul class="lp-fpdual-lista">
        <?php foreach ($items as $item): ?>
        <li>
          <i class="fas fa-circle-check"></i>
          <div>
            <strong><?= Security::escapeHtml($item['titulo'] ?? '') ?></strong>
            <?php if (!empty($item['texto'])): ?>
            <span><?= nl2br(Security::escapeHtml($item['texto'])) ?></span>
            <?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
    <?php if ($imgUrl): ?>
    <div class="lp-fpdual-visual">
      <img loading="lazy" src="<?= Security::escapeHtml($imgUrl) ?>" alt="">
    </div>
    <?php endif; ?>
  </div>
</section>
