<?php
// Sección becas_financiacion: becas, ayudas y facilidades de pago. Variantes: tarjetas, foto.
$items = $contenido['items'] ?? [];
$iconosPermitidos = landing_iconos_permitidos();
$variante = $contenido['variante'] ?? 'tarjetas';
$imgUrl = landing_img_url($contenido['imagen'] ?? '');
// La variante "foto" necesita una imagen real; sin ella, degrada a "tarjetas".
if ($variante === 'foto' && !$imgUrl) $variante = 'tarjetas';
?>
<section class="lp-sec lp-becas lp-variante-<?= Security::escapeHtml($variante) ?>" id="becas"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor<?= $variante === 'foto' ? ' lp-icono-foto-split lp-invertido' : '' ?>">
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
            $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-award'; ?>
        <li>
          <span class="lp-becas-icono"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></span>
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
      <?php if (!empty($contenido['botonTexto'])): ?>
      <div class="lp-becas-cta" style="justify-content: flex-start; margin-top: 28px;">
        <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#contacto')) ?>" class="lp-boton-primario lp-boton-grande">
          <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
        </a>
      </div>
      <?php endif; ?>
      <?php if (!empty($contenido['notaLegal'])): ?>
      <p class="lp-becas-nota" style="text-align: left;"<?= landing_lb_field($preview, 'notaLegal', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['notaLegal'])) ?></p>
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
    <div class="lp-becas-grid">
      <?php foreach ($items as $i => $item):
          $icono = isset($iconosPermitidos[$item['icono'] ?? '']) ? $item['icono'] : 'fa-award'; ?>
      <article class="lp-tarjeta lp-becas-item">
        <span class="lp-becas-icono"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></span>
        <h3<?= landing_lb_field($preview, "items.$i.titulo") ?>><?= Security::escapeHtml($item['titulo'] ?? '') ?></h3>
        <?php if (!empty($item['texto'])): ?>
        <p<?= landing_lb_field($preview, "items.$i.texto", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($contenido['botonTexto'])): ?>
    <div class="lp-becas-cta">
      <a href="<?= Security::escapeHtml(landing_url_segura($contenido['botonUrl'] ?? '', '#contacto')) ?>" class="lp-boton-primario lp-boton-grande">
        <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($contenido['botonTexto']) ?></span> <i class="fas fa-arrow-right"></i>
      </a>
    </div>
    <?php endif; ?>

    <?php if (!empty($contenido['notaLegal'])): ?>
    <p class="lp-becas-nota"<?= landing_lb_field($preview, 'notaLegal', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['notaLegal'])) ?></p>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
