<?php
// Sección de Vídeo de Presentación Moderna
$variante    = $contenido['variante'] ?? 'split';
$orientacion = $contenido['orientacion'] ?? 'derecha';
$videoUrl    = landing_img_url($contenido['videoUrl'] ?? '');
$posterUrl   = landing_img_url($contenido['posterUrl'] ?? '');

if (!$videoUrl) return;

$claseSeccion = 'lp-sec lp-video-pres lp-video-pres-' . $variante;
if ($variante === 'split') {
    $claseSeccion .= ' lp-video-pres-align-' . $orientacion;
}
?>
<section class="<?= Security::escapeHtml($claseSeccion) ?>" id="video_presentacion"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-video-pres-inner">
      <div class="lp-video-pres-content">
        <?php if (!empty($contenido['eyebrow'])): ?>
        <span class="lp-eyebrow"<?= landing_lb_field($preview, 'eyebrow') ?>><?= Security::escapeHtml($contenido['eyebrow']) ?></span>
        <?php endif; ?>

        <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>

        <?php if (!empty($contenido['subtitulo'])): ?>
        <p class="lp-video-pres-sub"<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
        <?php endif; ?>

        <?php if (!empty($contenido['parrafo'])): ?>
        <div class="lp-video-pres-text">
          <p<?= landing_lb_field($preview, 'parrafo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['parrafo'])) ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($contenido['botonTexto'])): ?>
        <div class="lp-video-pres-actions">
          <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#contacto')) ?>" class="lp-boton-primario">
            <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
          </a>
        </div>
        <?php endif; ?>
      </div>

      <div class="lp-video-pres-media">
        <div class="lp-video-pres-wrapper">
          <video class="lp-video-pres-player" controls preload="metadata" 
                 <?php if ($posterUrl): ?> poster="<?= Security::escapeHtml($posterUrl) ?>" <?php endif; ?>>
            <source src="<?= Security::escapeHtml($videoUrl) ?>" type="video/mp4">
            Tu navegador no soporta reproducción de vídeo HTML5.
          </video>
        </div>
      </div>

    </div>
  </div>
</section>
