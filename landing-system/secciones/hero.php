<?php
// Sección hero. Variantes: fondo (imagen/video de fondo), split (texto + imagen), minimal.
$variante = $contenido['variante'] ?? 'fondo';
$imgUrl   = landing_img_url($contenido['imagen'] ?? '');
$videoUrl = landing_img_url($contenido['videoFondo'] ?? '');
$parallaxUrl = landing_img_url($contenido['fondoParallax'] ?? '');

$estiloLocal = $styleStr ?? '';
$claseAdicional = '';
if ($variante === 'fondo') {
    if ($videoUrl) {
        // Video de fondo (manejado abajo)
    } elseif ($parallaxUrl) {
        $estiloLocal .= 'background-image:url(\'' . Security::escapeHtml($parallaxUrl) . '\'); background-attachment: fixed; background-size: cover; background-position: center;';
        $claseAdicional = ' lp-hero-parallax';
    } elseif ($imgUrl) {
        $estiloLocal .= 'background-image:url(\'' . Security::escapeHtml($imgUrl) . '\');';
    }
}
$estiloFondo = $estiloLocal !== '' ? ' style="' . $estiloLocal . '"' : '';
?>
<section class="lp-sec lp-hero lp-hero-<?= Security::escapeHtml($variante) ?><?= $claseAdicional ?>" id="hero"<?= $estiloFondo ?>>
  <?php if ($variante === 'fondo' && $videoUrl): ?>
  <video class="lp-hero-video" autoplay loop muted playsinline preload="auto">
    <source src="<?= Security::escapeHtml($videoUrl) ?>" type="video/mp4">
  </video>
  <?php endif; ?>
  <div class="lp-hero-velo"></div>
  <div class="lp-contenedor lp-hero-inner">
    <div class="lp-hero-texto">
      <?php if (!empty($contenido['eyebrow'])): ?>
      <span class="lp-eyebrow"><?= Security::escapeHtml($contenido['eyebrow']) ?></span>
      <?php endif; ?>
      <h1><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h1>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p class="lp-hero-sub"><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
      <div class="lp-hero-botones">
        <?php if (!empty($contenido['botonTexto'])): ?>
        <a href="<?= Security::escapeHtml($contenido['botonUrl'] ?: '#contacto') ?>" class="lp-boton-primario lp-boton-grande">
          <?= Security::escapeHtml($contenido['botonTexto']) ?> <i class="fas fa-arrow-right"></i>
        </a>
        <?php endif; ?>
        <?php if (!empty($contenido['boton2Texto'])): ?>
        <a href="<?= Security::escapeHtml($contenido['boton2Url'] ?: '#contacto') ?>" class="lp-boton-borde lp-boton-grande">
          <?= Security::escapeHtml($contenido['boton2Texto']) ?>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php if ($variante === 'split' && $imgUrl): ?>
    <div class="lp-hero-visual">
      <img loading="lazy" src="<?= Security::escapeHtml($imgUrl) ?>" alt="">
    </div>
    <?php endif; ?>
  </div>
</section>




