<?php
// Ventajas del centro (tarjetas con icono). Variantes: grid, tarjetas, lateral.
$iconosPermitidos = landing_iconos_permitidos();
$items = $contenido['items'] ?? [];
$variante = $contenido['variante'] ?? 'grid';
?>
<section class="lp-sec lp-porque lp-variante-<?= Security::escapeHtml($variante) ?>" id="porque_elegirnos">
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>
    <?php if ($items): ?>
    <div class="lp-porque-grid">
      <?php foreach ($items as $item):
          $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-star'; ?>
      <article class="lp-tarjeta lp-porque-item">
        <span class="lp-porque-icono"><i class="fas <?= $icono ?>"></i></span>
        <h3><?= Security::escapeHtml($item['titulo'] ?? '') ?></h3>
        <?php if (!empty($item['texto'])): ?>
        <p><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
