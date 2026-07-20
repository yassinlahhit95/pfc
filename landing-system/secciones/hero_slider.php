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
        <div class="lp-slide-bg" style="<?= $bgStyle ?>"></div>
        <div class="lp-hero-velo"></div>
        
        <div class="lp-contenedor lp-hero-inner lp-slide-inner">
          <div class="lp-hero-texto">
            <?php if (!empty($contenido['eyebrow'])): ?>
            <span class="lp-eyebrow"><?= Security::escapeHtml($contenido['eyebrow']) ?></span>
            <?php endif; ?>
            
            <h1 class="lp-slide-title"><?= Security::escapeHtml($slide['titulo'] ?? '') ?></h1>
            
            <?php if (!empty($slide['subtitulo'])): ?>
            <p class="lp-hero-sub lp-slide-sub"><?= nl2br(Security::escapeHtml($slide['subtitulo'])) ?></p>
            <?php endif; ?>
            
            <div class="lp-hero-botones lp-slide-btns">
              <?php if (!empty($contenido['botonTexto'])): ?>
              <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#oferta_formativa')) ?>" class="lp-boton-primario lp-boton-grande">
                <?= Security::escapeHtml($contenido['botonTexto']) ?> <i class="fas fa-arrow-right"></i>
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
    </div>
    <?php endif; ?>
    
  </div>
</section>

<?php if (count($slides) > 1): ?>
<script>
(function() {
    const container = document.getElementById('<?= $sliderId ?>');
    if (!container) return;
    
    const slides = container.querySelectorAll('.lp-slide');
    const dots = container.querySelectorAll('.lp-slider-dot');
    const prevBtn = container.querySelector('.lp-slider-prev');
    const nextBtn = container.querySelector('.lp-slider-next');
    
    let currentIndex = 0;
    const totalSlides = slides.length;
    let autoplayTimer = null;
    const autoplayEnabled = <?= $autoplay ? 'true' : 'false' ?>;
    
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
        
        resetAutoplay();
    }
    
    function nextSlide() { goToSlide(currentIndex + 1); }
    function prevSlide() { goToSlide(currentIndex - 1); }
    
    function resetAutoplay() {
        if (autoplayTimer) clearInterval(autoplayTimer);
        if (autoplayEnabled) {
            autoplayTimer = setInterval(nextSlide, 6000);
        }
    }
    
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    dots.forEach(dot => {
        dot.addEventListener('click', () => {
            goToSlide(parseInt(dot.getAttribute('data-index')));
        });
    });
    
    resetAutoplay();
})();
</script>
<?php endif; ?>
