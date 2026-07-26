<?php
// CTA de pre-matrícula. Si el módulo está desactivado no se renderiza nada.
if (!FeatureGuard::check('feature_prematricula')) return;
$variante = $contenido['variante'] ?? 'centrado';
?>
<section class="lp-sec lp-prematricula lp-prematricula-<?= Security::escapeHtml($variante) ?>" id="prematricula_cta"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-prematricula-caja">
    <div class="lp-prematricula-texto">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['texto'])): ?>
      <p<?= landing_lb_field($preview, 'texto', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['texto'])) ?></p>
      <?php endif; ?>
      <?php if (!empty($contenido['notaPlazo'])): ?>
      <span class="lp-prematricula-nota"><i class="fas fa-clock"></i> <span<?= landing_lb_field($preview, 'notaPlazo') ?>><?= Security::escapeHtml($contenido['notaPlazo']) ?></span></span>
      <?php endif; ?>
    </div>
    <div class="lp-prematricula-accion">
      <a href="/vistas/admisiones/pre-matricula.php" class="lp-boton-primario lp-boton-grande">
        <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto'] ?: 'Iniciar pre-matrícula') ?></span>
        <i class="fas fa-arrow-right"></i>
      </a>
      <a href="/vistas/admisiones/consultar.php" class="lp-enlace-suave">Consultar el estado de mi solicitud</a>
    </div>
  </div>
</section>
