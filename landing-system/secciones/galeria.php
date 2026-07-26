<?php
// Galería Masonry
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-galeria-masonry" id="galeria"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>

    <div class="lp-galeria">
      <?php foreach ($items as $i => $item):
          $imgUrl = landing_img_url($item['imagen'] ?? '');
          if (!$imgUrl) continue;
      ?>
      <figure class="lp-galeria-item" data-lightbox="<?= Security::escapeHtml($imgUrl) ?>">
        <img src="<?= Security::escapeHtml($imgUrl) ?>" alt="<?= Security::escapeHtml($item['texto'] ?? '') ?>" loading="lazy"<?= landing_lb_field($preview, "items.$i.imagen", 'imagen') ?>>
        <?php if (!empty($item['texto'])): ?>
        <figcaption>
          <strong<?= landing_lb_field($preview, "items.$i.texto") ?>><?= Security::escapeHtml($item['texto']) ?></strong>
        </figcaption>
        <?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
