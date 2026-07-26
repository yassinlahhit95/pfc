<?php
// Sección de testimonios de alumnos
$variante = $contenido['variante'] ?? 'tarjetas';
$items = $contenido['items'] ?? [];
if (!$items) return;
$sliderId = 'testimonials-' . uniqid();
?>
<section class="lp-sec lp-testimonios lp-testimonios-<?= Security::escapeHtml($variante) ?>" id="testimonios"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera lp-testimonios-cabecera">
      <div>
        <span class="lp-eyebrow">Opiniones reales</span>
        <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? 'Lo que dicen nuestros alumnos') ?></h2>
      </div>
      
      <?php if ($variante === 'carrusel' && count($items) > 1): ?>
      <div class="lp-testimonios-controles">
        <button class="lp-testimonios-btn lp-testimonios-prev" aria-label="Testimonio anterior" data-target="<?= $sliderId ?>">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="lp-testimonios-btn lp-testimonios-next" aria-label="Siguiente testimonio" data-target="<?= $sliderId ?>">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>

    <div class="lp-testimonios-wrapper">
      <div class="<?= $variante === 'carrusel' ? 'lp-testimonios-slider' : 'lp-testimonios-grid' ?>" id="<?= $sliderId ?>">
        <?php foreach ($items as $i => $item):
            $fotoUrl = landing_img_url($item['foto'] ?? ''); ?>
        <article class="lp-testimonio-card">
          <div class="lp-testimonio-top">
            <span class="lp-testimonio-comilla"><i class="fas fa-quote-left"></i></span>
          </div>
          <p class="lp-testimonio-texto"<?= landing_lb_field($preview, "items.$i.texto", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['texto'] ?? '')) ?></p>
          <div class="lp-testimonio-autor">
            <?php if ($fotoUrl): ?>
            <img loading="lazy" src="<?= Security::escapeHtml($fotoUrl) ?>" alt="<?= Security::escapeHtml($item['nombre'] ?? '') ?>" class="lp-testimonio-foto"<?= landing_lb_field($preview, "items.$i.foto", 'imagen') ?>>
            <?php else: ?>
            <span class="lp-testimonio-avatar"><?= Security::escapeHtml(mb_strtoupper(mb_substr($item['nombre'] ?? '?', 0, 1))) ?></span>
            <?php endif; ?>
            <div>
              <strong<?= landing_lb_field($preview, "items.$i.nombre") ?>><?= Security::escapeHtml($item['nombre'] ?? '') ?></strong>
              <?php if (!empty($item['rol'])): ?>
              <span<?= landing_lb_field($preview, "items.$i.rol") ?>><?= Security::escapeHtml($item['rol']) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<script>
(function() {
  const slider = document.getElementById('<?= $sliderId ?>');
  if (!slider) return;
  
  const prevBtn = document.querySelector('.lp-testimonios-prev[data-target="<?= $sliderId ?>"]');
  const nextBtn = document.querySelector('.lp-testimonios-next[data-target="<?= $sliderId ?>"]');
  if (!prevBtn || !nextBtn) return;

  function getCardWidth() {
    const card = slider.querySelector('.lp-testimonio-card');
    return card ? card.offsetWidth + 24 : 364; // card width + gap
  }

  prevBtn.addEventListener('click', () => {
    slider.scrollBy({ left: -getCardWidth(), behavior: 'smooth' });
  });

  nextBtn.addEventListener('click', () => {
    slider.scrollBy({ left: getCardWidth(), behavior: 'smooth' });
  });

  // Toggle buttons state based on scroll position
  function updateButtons() {
    const isAtStart = slider.scrollLeft <= 5;
    const isAtEnd = slider.scrollLeft + slider.clientWidth >= slider.scrollWidth - 5;
    prevBtn.style.opacity = isAtStart ? '0.4' : '1';
    nextBtn.style.opacity = isAtEnd ? '0.4' : '1';
  }

  slider.addEventListener('scroll', updateButtons, { passive: true });
  window.addEventListener('resize', updateButtons, { passive: true });
  updateButtons();
})();
</script>
