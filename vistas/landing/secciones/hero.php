<?php
// Sección hero. Variantes: fondo (imagen de fondo), split (texto + imagen), minimal.
$variante = $contenido['variante'] ?? 'fondo';
$imgUrl   = landing_img_url($contenido['imagen'] ?? '');
$estiloFondo = ($variante === 'fondo' && $imgUrl)
    ? ' style="background-image:url(\'' . Security::escapeHtml($imgUrl) . '\');"'
    : '';
?>
<section class="lp-sec lp-hero lp-hero-<?= Security::escapeHtml($variante) ?>" id="hero"<?= $estiloFondo ?>>
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
          <?= Security::escapeHtml($contenido['botonTexto']) ?>
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
      <img src="<?= Security::escapeHtml($imgUrl) ?>" alt="">
    </div>
    <?php endif; ?>
  </div>
</section>
