<?php
// Últimas noticias del blog. Muestra solo entradas publicadas reales.
$titulo     = $contenido['titulo'] ?? 'Actualidad';
$subtitulo  = $contenido['subtitulo'] ?? '';
$botonTexto = $contenido['botonTexto'] ?? 'Ver todas las noticias';
$numPosts   = ($contenido['numPosts'] ?? 'n3') === 'n6' ? 6 : 3;

$noticias = [];
try {
    require_once __DIR__ . '/../../modelos/blog.php';
    require_once __DIR__ . '/../../include/R2Client.php';
    $noticias = listarPostsPublicados($numPosts);
} catch (Throwable $e) {
    // La landing pública nunca debe romperse por un error de BD
}
?>
<section class="lp-sec lp-noticias" id="noticias"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($titulo) ?></h2>
      <?php if ($subtitulo): ?>
      <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($subtitulo)) ?></p>
      <?php endif; ?>
    </div>

    <?php if (empty($noticias)): ?>
    <div class="lp-blog-vacio">
      <i class="far fa-newspaper"></i>
      <h2>Todavía no hay entradas</h2>
      <p>Muy pronto publicaremos aquí las noticias y novedades del centro.</p>
    </div>
    <?php else: ?>
    <div class="lp-blog-grid lp-noticias-grid">
      <?php foreach ($noticias as $noticia):
          $enlace = $noticia['slug'] !== '' ? '/vistas/blog.php?post=' . rawurlencode($noticia['slug']) : '/vistas/blog.php';
          $img    = !empty($noticia['imagen']) ? R2Client::imagenUrl(
              __DIR__ . '/../../public/uploads/blog/' . basename($noticia['imagen']),
              '/public/uploads/blog/' . basename($noticia['imagen']),
              'blog/' . basename($noticia['imagen'])
          ) : '';
      ?>
      <article class="lp-blog-card">
        <a class="lp-blog-card-media" href="<?= Security::escapeHtml($enlace) ?>">
          <?php if ($img): ?>
          <img loading="lazy" src="<?= Security::escapeHtml($img) ?>" alt="">
          <?php else: ?>
          <span class="lp-blog-card-placeholder"><i class="far fa-newspaper"></i></span>
          <?php endif; ?>
          <?php if (!empty($noticia['categoria'])): ?>
          <span class="lp-blog-chip"><?= Security::escapeHtml($noticia['categoria']) ?></span>
          <?php endif; ?>
        </a>
        <div class="lp-blog-card-body">
          <div class="lp-blog-meta">
            <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($noticia['fechaPublicacion'])) ?></span>
            <?php if (!empty($noticia['autor'])): ?>
            <span><i class="far fa-user"></i> <?= Security::escapeHtml($noticia['autor']) ?></span>
            <?php endif; ?>
          </div>
          <h3><a href="<?= Security::escapeHtml($enlace) ?>"><?= Security::escapeHtml($noticia['titulo']) ?></a></h3>
          <?php if (!empty($noticia['resumen'])): ?>
          <p><?= Security::escapeHtml(mb_substr($noticia['resumen'], 0, 140)) ?><?= mb_strlen($noticia['resumen']) > 140 ? '…' : '' ?></p>
          <?php endif; ?>
          <span class="lp-blog-leer">Leer entrada <i class="fas fa-arrow-right"></i></span>
        </div>
      </article>
      <?php endforeach; ?>
    </div>

    <?php if ($botonTexto): ?>
    <div class="lp-noticias-cta">
      <a href="/vistas/blog.php" class="lp-boton-borde lp-boton-grande"><span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($botonTexto) ?></span> <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</section>
