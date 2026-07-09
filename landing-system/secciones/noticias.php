<?php
// Últimas noticias del blog. Muestra las entradas publicadas más recientes;
// si aún no hay ninguna, usa contenido de ejemplo para no dejar la sección vacía.
$titulo     = $contenido['titulo'] ?? 'Actualidad';
$subtitulo  = $contenido['subtitulo'] ?? '';
$botonTexto = $contenido['botonTexto'] ?? 'Ver todas las noticias';
$numPosts   = (int)($contenido['numPosts'] ?? 3);
if (!in_array($numPosts, [3, 6], true)) $numPosts = 3;

$noticias = [];
try {
    require_once __DIR__ . '/../../modelos/blog.php';
    $noticias = listarPostsPublicados($numPosts);
} catch (Throwable $e) {
    // La landing pública nunca debe romperse por un error de BD
}

$esEjemplo = empty($noticias);
if ($esEjemplo) {
    $noticias = [
        ['titulo' => 'Apertura del plazo de matriculación', 'resumen' => 'Ya puedes reservar tu plaza para el próximo curso. ¡Plazas limitadas!', 'fechaPublicacion' => date('Y-m-d', strtotime('-2 days')), 'categoria' => 'Admisiones', 'imagen' => '', 'slug' => '', 'autor' => ''],
        ['titulo' => 'Nuevos convenios de FP Dual', 'resumen' => 'Ampliamos nuestra red de empresas colaboradoras en el sector tecnológico y sanitario.', 'fechaPublicacion' => date('Y-m-d', strtotime('-5 days')), 'categoria' => 'FP Dual', 'imagen' => '', 'slug' => '', 'autor' => ''],
        ['titulo' => 'Jornada de puertas abiertas', 'resumen' => 'Ven a conocer nuestras instalaciones el próximo viernes. Contaremos con antiguos alumnos.', 'fechaPublicacion' => date('Y-m-d', strtotime('-10 days')), 'categoria' => 'Eventos', 'imagen' => '', 'slug' => '', 'autor' => ''],
    ];
}
?>
<section class="lp-sec lp-noticias" id="noticias"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($titulo) ?></h2>
      <?php if ($subtitulo): ?>
      <p><?= nl2br(Security::escapeHtml($subtitulo)) ?></p>
      <?php endif; ?>
    </div>

    <div class="lp-blog-grid lp-noticias-grid">
      <?php foreach ($noticias as $noticia):
          $enlace = $noticia['slug'] !== '' ? '/vistas/blog.php?post=' . rawurlencode($noticia['slug']) : '/vistas/blog.php';
          $img    = !empty($noticia['imagen']) ? '/public/uploads/blog/' . basename($noticia['imagen']) : '';
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
      <a href="/vistas/blog.php" class="lp-boton-borde lp-boton-grande"><?= Security::escapeHtml($botonTexto) ?> <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php endif; ?>
  </div>
</section>
