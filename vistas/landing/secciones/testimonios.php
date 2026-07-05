<?php
// Testimonios de alumnos.
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-testimonios" id="testimonios">
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
    </div>
    <div class="lp-testimonios-grid">
      <?php foreach ($items as $item):
          $fotoUrl = landing_img_url($item['foto'] ?? ''); ?>
      <article class="lp-tarjeta lp-testimonio">
        <i class="fas fa-quote-left lp-testimonio-comilla"></i>
        <p class="lp-testimonio-texto"><?= nl2br(Security::escapeHtml($item['texto'] ?? '')) ?></p>
        <div class="lp-testimonio-autor">
          <?php if ($fotoUrl): ?>
          <img src="<?= Security::escapeHtml($fotoUrl) ?>" alt="" class="lp-testimonio-foto">
          <?php else: ?>
          <span class="lp-testimonio-avatar"><?= Security::escapeHtml(mb_strtoupper(mb_substr($item['nombre'] ?? '?', 0, 1))) ?></span>
          <?php endif; ?>
          <div>
            <strong><?= Security::escapeHtml($item['nombre'] ?? '') ?></strong>
            <?php if (!empty($item['rol'])): ?>
            <span><?= Security::escapeHtml($item['rol']) ?></span>
            <?php endif; ?>
          </div>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>
