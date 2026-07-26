<?php
// Ventajas del centro (tarjetas con icono). Variantes: grid, tarjetas, lateral, foto.
$iconosPermitidos = landing_iconos_permitidos();
$items = $contenido['items'] ?? [];
$variante = $contenido['variante'] ?? 'grid';
$imgUrl = landing_img_url($contenido['imagen'] ?? '');
// La variante "foto" necesita una imagen real; sin ella, degrada a "grid"
// en vez de mostrar una sección con la mitad del layout vacía.
if ($variante === 'foto' && !$imgUrl) $variante = 'grid';
?>
<section class="lp-sec lp-porque lp-variante-<?= Security::escapeHtml($variante) ?>" id="porque_elegirnos"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor<?= $variante === 'foto' ? ' lp-icono-foto-split' : '' ?>">
    <?php if ($variante === 'foto'): ?>
    <div class="lp-icono-foto-visual">
      <img loading="lazy" src="<?= Security::escapeHtml($imgUrl) ?>" alt=""<?= landing_lb_field($preview, 'imagen', 'imagen') ?>>
    </div>
    <div>
      <div class="lp-sec-cabecera" style="text-align: left; margin: 0 0 32px;">
        <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
        <?php if (!empty($contenido['subtitulo'])): ?>
        <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
        <?php endif; ?>
      </div>
      <?php if ($items): ?>
      <ul class="lp-icono-foto-lista">
        <?php foreach ($items as $i => $item):
            $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-star'; ?>
        <li>
          <span class="lp-porque-icono"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></span>
          <div>
            <strong<?= landing_lb_field($preview, "items.$i.titulo") ?>><?= Security::escapeHtml($item['titulo'] ?? '') ?></strong>
            <?php if (!empty($item['texto'])): ?>
            <p<?= landing_lb_field($preview, "items.$i.texto", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
            <?php endif; ?>
          </div>
        </li>
        <?php endforeach; ?>
      </ul>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>
    <?php if ($items): ?>
    <div class="lp-porque-grid">
      <?php foreach ($items as $i => $item):
          $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-star'; ?>
      <article class="lp-tarjeta lp-porque-item">
        <span class="lp-porque-icono"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></span>
        <h3<?= landing_lb_field($preview, "items.$i.titulo") ?>><?= Security::escapeHtml($item['titulo'] ?? '') ?></h3>
        <?php if (!empty($item['texto'])): ?>
        <p<?= landing_lb_field($preview, "items.$i.texto", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
