<?php
// Sección becas_financiacion: becas, ayudas y facilidades de pago
$items = $contenido['items'] ?? [];
$iconosPermitidos = landing_iconos_permitidos();
?>
<section class="lp-sec lp-becas" id="becas"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>

    <?php if ($items): ?>
    <div class="lp-becas-grid">
      <?php foreach ($items as $item):
          $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-award'; ?>
      <article class="lp-tarjeta lp-becas-item">
        <span class="lp-becas-icono"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></span>
        <h3><?= Security::escapeHtml($item['titulo'] ?? '') ?></h3>
        <?php if (!empty($item['texto'])): ?>
        <p><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($contenido['botonTexto'])): ?>
    <div class="lp-becas-cta">
      <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#contacto')) ?>" class="lp-boton-primario lp-boton-grande">
        <?= Security::escapeHtml($contenido['botonTexto']) ?> <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    <?php endif; ?>

    <?php if (!empty($contenido['notaLegal'])): ?>
    <p class="lp-becas-nota"><?= nl2br(Security::escapeHtml($contenido['notaLegal'])) ?></p>
    <?php endif; ?>
  </div>
</section>
