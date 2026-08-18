<?php
// Sección equipo_docente: presenta al profesorado o personal clave del centro
$variante = $contenido['variante'] ?? 'grid';
$items = $contenido['items'] ?? [];
if (!$items) return;
$esCarrusel = $variante === 'carrusel' && count($items) > 1;
$sliderId = 'equipo-slider-' . uniqid();

// Extraído porque se repite igual dentro de .lp-equipo-grid y .lp-equipo-slider.
$renderTarjetas = function () use ($items, $preview) {
    foreach ($items as $i => $item):
        $fotoUrl = landing_img_url($item['foto'] ?? '');
        ?>
      <article class="lp-tarjeta lp-equipo-card lp-flip-card">
        <div class="lp-flip-inner">
          <div class="lp-flip-front">
            <?php if ($fotoUrl): ?>
            <img loading="lazy" src="<?= Security::escapeHtml($fotoUrl) ?>" alt="<?= Security::escapeHtml($item['nombre'] ?? '') ?>" class="lp-equipo-foto"<?= landing_lb_field($preview, "items.$i.foto", 'imagen') ?>>
            <?php else: ?>
            <div class="lp-equipo-foto lp-equipo-foto-inicial" aria-hidden="true">
              <?= Security::escapeHtml(mb_strtoupper(mb_substr(trim($item['nombre'] ?? ''), 0, 1))) ?>
            </div>
            <?php endif; ?>
            <h3<?= landing_lb_field($preview, "items.$i.nombre") ?>><?= Security::escapeHtml($item['nombre'] ?? '') ?></h3>
            <?php if (!empty($item['cargo'])): ?>
            <span class="lp-equipo-cargo"<?= landing_lb_field($preview, "items.$i.cargo") ?>><?= Security::escapeHtml($item['cargo']) ?></span>
            <?php endif; ?>
          </div>
          <div class="lp-flip-back">
            <h3><?= Security::escapeHtml($item['nombre'] ?? '') ?></h3>
            <div class="lp-equipo-back-scroll">
              <?php if (!empty($item['bio'])): ?>
              <p class="lp-equipo-bio"<?= landing_lb_field($preview, "items.$i.bio", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['bio'] ?? '')) ?></p>
              <?php endif; ?>
            </div>
            <a href="#" class="lp-badge" aria-label="Perfil de LinkedIn de <?= Security::escapeHtml($item['nombre'] ?? 'miembro del equipo') ?>"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>
      </article>
    <?php
    endforeach;
};
?>
<section class="lp-sec lp-equipo lp-variante-<?= Security::escapeHtml($variante) ?>" id="equipo"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera<?= $esCarrusel ? ' lp-testimonios-cabecera' : '' ?>">
      <div>
        <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
        <?php if (!empty($contenido['subtitulo'])): ?>
        <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'] ?? '')) ?></p>
        <?php endif; ?>
      </div>
      <?php if ($esCarrusel): ?>
      <div class="lp-testimonios-controles">
        <button class="lp-testimonios-btn lp-equipo-prev" aria-label="Persona anterior" data-target="<?= $sliderId ?>">
          <i class="fas fa-chevron-left"></i>
        </button>
        <button class="lp-testimonios-btn lp-equipo-next" aria-label="Siguiente persona" data-target="<?= $sliderId ?>">
          <i class="fas fa-chevron-right"></i>
        </button>
      </div>
      <?php endif; ?>
    </div>

    <?php if ($esCarrusel): ?>
    <div class="lp-testimonios-wrapper">
      <div class="lp-equipo-slider" id="<?= $sliderId ?>">
        <?php $renderTarjetas(); ?>
      </div>
    </div>
    <?php else: ?>
    <div class="lp-equipo-grid">
      <?php $renderTarjetas(); ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php if ($esCarrusel): ?>
<script>
(function() {
  const slider = document.getElementById('<?= $sliderId ?>');
  if (!slider) return;

  const prevBtn = document.querySelector('.lp-equipo-prev[data-target="<?= $sliderId ?>"]');
  const nextBtn = document.querySelector('.lp-equipo-next[data-target="<?= $sliderId ?>"]');
  if (!prevBtn || !nextBtn) return;

  function getCardWidth() {
    const card = slider.querySelector('.lp-equipo-card');
    return card ? card.offsetWidth + 22 : 262; // ancho de tarjeta + gap
  }

  prevBtn.addEventListener('click', () => {
    slider.scrollBy({ left: -getCardWidth(), behavior: 'smooth' });
  });
  nextBtn.addEventListener('click', () => {
    slider.scrollBy({ left: getCardWidth(), behavior: 'smooth' });
  });

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
<?php endif; ?>
