<?php
// Sección hero_slider: Hero con slider de múltiples imágenes con transiciones premium y efecto Ken Burns
$slides = $contenido['slides'] ?? [];
if (empty($slides)) {
    $slides = [
        ['imagen' => '', 'titulo' => 'Tu futuro empieza aquí', 'subtitulo' => '']
    ];
}
$autoplay = ($contenido['autoplay'] ?? 'si') === 'si';
$sliderId = 'hero-slider-' . uniqid();

// LCP Optimization: Preload the first slide's background image
$firstSlideImg = landing_img_url($slides[0]['imagen'] ?? '');
if ($firstSlideImg !== '') {
    echo '<link rel="preload" as="image" href="' . Security::escapeHtml($firstSlideImg) . '" fetchpriority="high">';
}
?>
<section class="lp-sec lp-hero-slider" id="hero-slider"<?= $styleInline ?? '' ?>>
  <div class="lp-slider-container" id="<?= $sliderId ?>">
    <div class="lp-slider-track">
      <?php foreach ($slides as $i => $slide): 
        $imgUrl = landing_img_url($slide['imagen'] ?? '');
        $bgStyle = $imgUrl ? 'background-image:url(\'' . Security::escapeHtml($imgUrl) . '\');' : '';
      ?>
      <div class="lp-slide <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>">
        <!-- Imagen de fondo con zoom Ken Burns -->
        <div class="lp-slide-bg" style="<?= $bgStyle ?>"<?= landing_lb_field($preview, "slides.$i.imagen", 'imagen') ?>></div>
        <div class="lp-hero-velo"></div>

        <div class="lp-contenedor lp-hero-inner lp-slide-inner">
          <div class="lp-hero-texto">
            <?php if (!empty($contenido['eyebrow'])): ?>
            <span class="lp-eyebrow"<?= landing_lb_field($preview, 'eyebrow') ?>><?= Security::escapeHtml($contenido['eyebrow']) ?></span>
            <?php endif; ?>

            <h1 class="lp-slide-title"<?= landing_lb_field($preview, "slides.$i.titulo") ?>><?= Security::escapeHtml($slide['titulo'] ?? '') ?></h1>

            <?php if (!empty($slide['subtitulo'])): ?>
            <p class="lp-hero-sub lp-slide-sub"<?= landing_lb_field($preview, "slides.$i.subtitulo", 'textarea') ?>><?= nl2br(Security::escapeHtml($slide['subtitulo'])) ?></p>
            <?php endif; ?>

            <div class="lp-hero-botones lp-slide-btns">
              <?php if (!empty($contenido['botonTexto'])): ?>
              <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#oferta_formativa')) ?>" class="lp-boton-primario lp-boton-grande">
                <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($slides) > 1): ?>
    <div class="lp-slider-nav">
      <button class="lp-slider-prev" aria-label="Anterior slide"><i class="fas fa-arrow-left"></i></button>
      <div class="lp-slider-dots">
        <?php foreach ($slides as $i => $slide): ?>
        <button class="lp-slider-dot <?= $i === 0 ? 'active' : '' ?>" data-index="<?= $i ?>" aria-label="Ir a slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <button class="lp-slider-next" aria-label="Siguiente slide"><i class="fas fa-arrow-right"></i></button>
      <?php if ($autoplay): ?>
      <button class="lp-slider-playpause" type="button" aria-label="Pausar reproducción automática" aria-pressed="false"><i class="fas fa-pause"></i></button>
      <?php endif; ?>
    </div>
    <?php endif; ?>
    
  </div>
</section>

<?php if (count($slides) > 1): ?>
<script>
(function() {
    const container = document.getElementById('<?= $sliderId ?>');
    if (!container) return;

    const slides  = container.querySelectorAll('.lp-slide');
    const dots    = container.querySelectorAll('.lp-slider-dot');
    const prevBtn = container.querySelector('.lp-slider-prev');
    const nextBtn = container.querySelector('.lp-slider-next');
    const playBtn = container.querySelector('.lp-slider-playpause');

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoplayTimer = null;
    // El autoplay respeta "reduce motion" del sistema: si el usuario lo pide,
    // el carrusel arranca en pausa y solo avanza si el propio usuario le da a play.
    let running = (<?= $autoplay ? 'true' : 'false' ?>) && !reduceMotion;

    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;

        // Remove active class from previous
        slides[currentIndex].classList.remove('active');
        dots[currentIndex].classList.remove('active');

        currentIndex = index;

        // Add active class to new
        slides[currentIndex].classList.add('active');
        dots[currentIndex].classList.add('active');

        if (running) startAutoplay();
    }

    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }

    function startAutoplay() {
        stopAutoplay();
        autoplayTimer = setInterval(nextSlide, 6000);
    }
    function stopAutoplay() {
        if (autoplayTimer) { clearInterval(autoplayTimer); autoplayTimer = null; }
    }

    function pintarPlayBtn() {
        if (!playBtn) return;
        playBtn.innerHTML = running ? '<i class="fas fa-pause"></i>' : '<i class="fas fa-play"></i>';
        playBtn.setAttribute('aria-label', running ? 'Pausar reproducción automática' : 'Reanudar reproducción automática');
        playBtn.setAttribute('aria-pressed', running ? 'false' : 'true');
    }

    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);

    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.getAttribute('data-index')));
        });
    });

    // Control manual: pausa/reanuda de forma explícita (WCAG 2.2.2).
    if (playBtn) {
        playBtn.addEventListener('click', function () {
            running = !running;
            if (running) startAutoplay(); else stopAutoplay();
            pintarPlayBtn();
        });
        pintarPlayBtn();
    }

    // Pausa temporal al pasar el ratón o el foco por encima, sin alterar
    // la preferencia manual del usuario (solo reanuda si seguía "running").
    container.addEventListener('mouseenter', stopAutoplay);
    container.addEventListener('mouseleave', function () { if (running) startAutoplay(); });
    container.addEventListener('focusin', stopAutoplay);
    container.addEventListener('focusout', function () { if (running) startAutoplay(); });

    if (running) startAutoplay();
})();
</script>
<?php endif; ?>
