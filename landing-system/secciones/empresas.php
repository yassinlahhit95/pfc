<?php
// Empresas colaboradoras (logos).
$items = $contenido['items'] ?? [];
$variante = $contenido['variante'] ?? 'grid';
?>
<section class="lp-sec lp-empresas lp-variante-<?= Security::escapeHtml($variante) ?>" id="empresas"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['texto'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['texto'])) ?></p>
      <?php endif; ?>
    </div>
    <?php if ($items): ?>
    <div class="lp-empresas-grid">
      <?php if ($variante === 'marquee'): ?><div class="lp-marquee-track"><?php endif; ?>
      <?php 
      // Si es marquee, duplicamos los items para que el loop infinito no tenga saltos
      $renderItems = $variante === 'marquee' ? array_merge($items, $items) : $items;
      foreach ($renderItems as $item):
          $logoUrl = landing_img_url($item['logo'] ?? '');
          $url = trim($item['url'] ?? '');
          $enlaceValido = (bool)preg_match('#^https?://#i', $url); ?>
      <?php if ($enlaceValido): ?><a class="lp-empresa" href="<?= Security::escapeHtml($url) ?>" target="_blank" rel="noopener"><?php else: ?><div class="lp-empresa"><?php endif; ?>
        <?php if ($logoUrl): ?>
        <img loading="lazy" src="<?= Security::escapeHtml($logoUrl) ?>" alt="<?= Security::escapeHtml($item['nombre'] ?? '') ?>" title="<?= Security::escapeHtml($item['nombre'] ?? '') ?>">
        <?php else: ?>
        <span><?= Security::escapeHtml($item['nombre'] ?? '') ?></span>
        <?php endif; ?>
      <?php if ($enlaceValido): ?></a><?php else: ?></div><?php endif; ?>
      <?php endforeach; ?>
      <?php if ($variante === 'marquee'): ?></div><?php endif; ?>
    </div>
    <?php else: ?>
    <p class="lp-vacio">Añade empresas colaboradoras desde el constructor.</p>
    <?php endif; ?>
  </div>
</section>




