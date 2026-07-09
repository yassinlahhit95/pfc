<?php
// Galería Masonry
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-galeria-masonry" id="galeria"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>

    <div class="lp-galeria">
      <?php foreach ($items as $item):
          $imgUrl = landing_img_url($item['imagen'] ?? '');
          if (!$imgUrl) continue;
      ?>
      <figure class="lp-galeria-item" data-lightbox="<?= Security::escapeHtml($imgUrl) ?>">
        <img src="<?= Security::escapeHtml($imgUrl) ?>" alt="<?= Security::escapeHtml($item['texto'] ?? '') ?>" loading="lazy">
        <?php if (!empty($item['texto'])): ?>
        <figcaption>
          <strong><?= Security::escapeHtml($item['texto']) ?></strong>
        </figcaption>
        <?php endif; ?>
      </figure>
      <?php endforeach; ?>
    </div>

  </div>
</section>




