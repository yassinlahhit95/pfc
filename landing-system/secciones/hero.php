<?php
// Sección hero. Variantes: fondo (imagen/video de fondo), split (texto + imagen),
// minimal, promo (cinta promocional + foto, estilo campaña tipo iLERNA).
$variante = $contenido['variante'] ?? 'fondo';
$imgUrl   = landing_img_url($contenido['imagen'] ?? '');
$videoUrl = landing_img_url($contenido['videoFondo'] ?? '');
$parallaxUrl = landing_img_url($contenido['fondoParallax'] ?? '');

$estiloLocal = $styleStr ?? '';
$claseAdicional = '';
$campoFondo = '';
if ($variante === 'fondo' && !$videoUrl) {
    if ($parallaxUrl) {
        $estiloLocal .= 'background-image:url(\'' . Security::escapeHtml($parallaxUrl) . '\'); background-attachment: fixed; background-size: cover; background-position: center;';
        $claseAdicional = ' lp-hero-parallax';
        $campoFondo = 'fondoParallax';
    } elseif ($imgUrl) {
        $estiloLocal .= 'background-image:url(\'' . Security::escapeHtml($imgUrl) . '\');';
        $campoFondo = 'imagen';
    }
}
// Reconstruye el mismo atributo que $styleInline (style + data-lb-id en preview),
// porque aquí no podemos partir de $styleInline: necesitamos añadirle el
// background-image propio de esta variante antes de cerrar el style="".
$estiloFondo = $estiloLocal !== '' ? ' style="' . $estiloLocal . '"' : '';
if (!empty($preview) && isset($s['idSeccion'])) {
    $estiloFondo .= ' data-lb-id="' . Security::escapeHtml($s['idSeccion']) . '"';
}
// Igual que en cifras.php: el campo de imagen de fondo se marca en la propia
// sección (cubre toda la pantalla), no en un <img> — un click en un hueco
// libre reemplaza la foto de fondo; un click en el título/subtítulo/botón
// edita ese campo en su lugar (closest() prioriza el nodo más específico).
if ($campoFondo !== '') {
    $estiloFondo .= landing_lb_field($preview, $campoFondo, 'imagen');
}
?>
<section class="lp-sec lp-hero lp-hero-<?= Security::escapeHtml($variante) ?><?= $claseAdicional ?>" id="hero"<?= $estiloFondo ?>>
  <?php if ($variante === 'promo' && !empty($contenido['promoTexto'])):
      $promoHref = landing_url_segura($contenido['promoUrl'] ?? '', ''); ?>
  <div class="lp-hero-ribbon">
    <?php if ($promoHref !== ''): ?>
    <a href="<?= Security::escapeHtml($promoHref) ?>"><i class="fas fa-bolt"></i> <span<?= landing_lb_field($preview, 'promoTexto') ?>><?= Security::escapeHtml($contenido['promoTexto']) ?></span></a>
    <?php else: ?>
    <span><i class="fas fa-bolt"></i> <span<?= landing_lb_field($preview, 'promoTexto') ?>><?= Security::escapeHtml($contenido['promoTexto']) ?></span></span>
    <?php endif; ?>
  </div>
  <?php endif; ?>
  <?php if ($variante === 'fondo' && $videoUrl): ?>
  <video class="lp-hero-video" autoplay loop muted playsinline preload="auto">
    <source src="<?= Security::escapeHtml($videoUrl) ?>" type="video/mp4">
  </video>
  <?php endif; ?>
  <div class="lp-hero-velo"></div>
  <div class="lp-contenedor lp-hero-inner">
    <div class="lp-hero-texto">
      <?php if (!empty($contenido['eyebrow'])): ?>
      <span class="lp-eyebrow lp-badge"<?= landing_lb_field($preview, 'eyebrow') ?>><?= Security::escapeHtml($contenido['eyebrow']) ?></span>
      <?php endif; ?>
      <h1<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h1>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p class="lp-hero-sub"<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
      <div class="lp-hero-botones">
        <?php if (!empty($contenido['botonTexto'])): ?>
        <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#contacto')) ?>" class="lp-boton-primario lp-boton-grande">
          <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
        </a>
        <?php endif; ?>
        <?php if (!empty($contenido['boton2Texto'])): ?>
        <a href="<?= Security::escapeHtml(landing_url_segura($contenido['boton2Url'] ?? '', '#contacto')) ?>" class="lp-boton-borde lp-boton-grande">
          <span<?= landing_lb_field($preview, 'boton2Texto') ?>><?= Security::escapeHtml($contenido['boton2Texto']) ?></span>
        </a>
        <?php endif; ?>
      </div>
    </div>
    <?php if (($variante === 'split' || $variante === 'promo') && $imgUrl): ?>
    <div class="lp-hero-visual">
      <img fetchpriority="high" src="<?= Security::escapeHtml($imgUrl) ?>" alt="<?= Security::escapeHtml($contenido['titulo'] ?? 'Hero image') ?>"<?= landing_lb_field($preview, 'imagen', 'imagen') ?>>
    </div>
    <?php endif; ?>
  </div>
</section>
