<?php
// Galería de instalaciones con lightbox (landing.js).
$items = $contenido['items'] ?? [];
$variante = $contenido['variante'] ?? 'grid';
?>
<section class="lp-sec lp-instalaciones lp-variante-<?= Security::escapeHtml($variante) ?>" id="instalaciones"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>
    <?php if ($items): ?>
    <div class="lp-galeria">
      <?php foreach ($items as $i => $item):
          $imgUrl = landing_img_url($item['imagen'] ?? '');
          if (!$imgUrl) continue; ?>
      <figure class="lp-galeria-item" data-lightbox="<?= Security::escapeHtml($imgUrl) ?>">
        <img src="<?= Security::escapeHtml($imgUrl) ?>" alt="<?= Security::escapeHtml($item['titulo'] ?? '') ?>" loading="lazy"<?= landing_lb_field($preview, "items.$i.imagen", 'imagen') ?>>
        <?php if (!empty($item['titulo']) || !empty($item['texto'])): ?>
        <figcaption>
          <?php if (!empty($item['titulo'])): ?><strong<?= landing_lb_field($preview, "items.$i.titulo") ?>><?= Security::escapeHtml($item['titulo']) ?></strong><?php endif; ?>
          <?php if (!empty($item['texto'])): ?><span<?= landing_lb_field($preview, "items.$i.texto") ?>><?= Security::escapeHtml($item['texto']) ?></span><?php endif; ?>
        </figcaption>
        <?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="lp-vacio">Añade fotos de tus instalaciones desde el constructor.</p>
    <?php endif; ?>
  </div>
</section>
