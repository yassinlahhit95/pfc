<?php
// ══════════════════════════════════════════════════════════════════════
// BLOG PÚBLICO DEL CENTRO
// ══════════════════════════════════════════════════════════════════════
// /vistas/blog.php            → listado con categorías y paginación
// /vistas/blog.php?post=slug  → detalle de una entrada
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../modelos/landing.php';
require_once __DIR__ . '/../modelos/blog.php';
require_once __DIR__ . '/../include/landing/secciones.php';
require_once __DIR__ . '/../include/landing/plantillas.php';

$cfg = obtenerConfiguracionCentro();

if (!FeatureGuard::check('feature_landing')) {
    include __DIR__ . '/landing/fallback.php';
    exit;
}

$landing = obtenerLandingConfig();
$tema    = in_array($landing['plantilla_pub'], landing_plantillas_slugs(), true) ? $landing['plantilla_pub'] : 'institucional';
$ajustes = json_decode($landing['ajustes_pub'] ?? '', true) ?: [];
$preview = false;

// Menú de navegación: mismas anclas que la portada
$secciones = listarSeccionesLanding('live', true);
$tipos     = landing_tipos();
$menuAnclas = [];
foreach ($secciones as $s) {
    if (!isset($tipos[$s['tipo']])) continue;
    $contenido = json_decode($s['contenido'] ?? '{}', true) ?: [];
    $navVisible = $contenido['navVisible'] ?? (!empty($tipos[$s['tipo']]['menu']) ? 'si' : 'no');
    $navTexto   = $contenido['navTexto'] ?? ($tipos[$s['tipo']]['menu'] ?? '');
    if ($navVisible === 'si' && !empty($navTexto) && !isset($menuAnclas[$s['tipo']])) {
        $esSeparado = ($s['tipo'] === 'contacto' && ($contenido['modoVisualizacion'] ?? 'integrado') === 'separado');
        $menuAnclas[$s['tipo']] = ['texto' => $navTexto, 'separado' => $esSeparado];
        if ($s['tipo'] === 'noticias') $menuAnclas[$s['tipo']]['url'] = '/vistas/blog.php';
    }
}

// ── Detalle de una entrada ───────────────────────────────────────────
$slug = trim($_GET['post'] ?? '');
$post = $slug !== '' ? obtenerPostPorSlug($slug) : null;

if ($post) {
    $ajustes['tituloSeo']      = $post['titulo'] . ' — ' . $cfg['nombreCentro'];
    $ajustes['descripcionSeo'] = $post['resumen'] ?: mb_substr(strip_tags($post['contenido']), 0, 155);

    $imgPost    = $post['imagen'] !== '' ? '/public/uploads/blog/' . basename($post['imagen']) : '';
    $contenidoEsHtml = (bool)preg_match('/<[a-z][\s\S]*>/i', $post['contenido']);
    $parrafos   = $contenidoEsHtml ? [] : (preg_split('/\n{2,}/', trim($post['contenido'])) ?: []);
    $palabras   = str_word_count(strip_tags($post['contenido']));
    $minLectura = max(1, (int)round($palabras / 200));
    $relacionados = listarPostsRelacionados($post['idPost'], 3);

    include __DIR__ . '/landing/_head.php';
    include __DIR__ . '/landing/_nav.php';
?>
  <article class="lp-articulo">
    <header class="lp-articulo-cabecera">
      <div class="lp-contenedor">
        <nav class="lp-migas" aria-label="Ruta">
          <a href="/">Inicio</a>
          <i class="fas fa-angle-right"></i>
          <a href="/vistas/blog.php">Blog</a>
          <?php if ($post['categoria'] !== ''): ?>
          <i class="fas fa-angle-right"></i>
          <a href="/vistas/blog.php?categoria=<?= urlencode($post['categoria']) ?>"><?= Security::escapeHtml($post['categoria']) ?></a>
          <?php endif; ?>
        </nav>
        <?php if ($post['categoria'] !== ''): ?>
        <span class="lp-blog-chip"><?= Security::escapeHtml($post['categoria']) ?></span>
        <?php endif; ?>
        <h1><?= Security::escapeHtml($post['titulo']) ?></h1>
        <div class="lp-articulo-meta">
          <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($post['fechaPublicacion'])) ?></span>
          <?php if ($post['autor'] !== ''): ?>
          <span><i class="far fa-user"></i> <?= Security::escapeHtml($post['autor']) ?></span>
          <?php endif; ?>
          <span><i class="far fa-clock"></i> <?= $minLectura ?> min de lectura</span>
        </div>
      </div>
    </header>

    <div class="lp-contenedor lp-articulo-contenedor">
      <?php if ($imgPost): ?>
      <figure class="lp-articulo-portada">
        <img src="<?= Security::escapeHtml($imgPost) ?>" alt="<?= Security::escapeHtml($post['titulo']) ?>">
      </figure>
      <?php endif; ?>

      <div class="lp-articulo-cuerpo">
        <?php if ($post['resumen'] !== ''): ?>
        <p class="lp-articulo-entradilla"><?= nl2br(Security::escapeHtml($post['resumen'])) ?></p>
        <?php endif; ?>
        <?php if ($contenidoEsHtml): ?>
        <?= $post['contenido'] /* ya saneado con HtmlSanitizer al guardar */ ?>
        <?php else: ?>
        <?php foreach ($parrafos as $p): if (trim($p) === '') continue; ?>
        <p><?= nl2br(Security::escapeHtml(trim($p))) ?></p>
        <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <footer class="lp-articulo-pie">
        <a href="/vistas/blog.php" class="lp-boton-borde"><i class="fas fa-arrow-left"></i> Volver al blog</a>
      </footer>
    </div>

    <?php if (!empty($relacionados)): ?>
    <section class="lp-sec lp-blog-relacionados">
      <div class="lp-contenedor">
        <div class="lp-sec-cabecera">
          <h2>Seguir leyendo</h2>
        </div>
        <div class="lp-blog-grid">
          <?php foreach ($relacionados as $rel):
              $imgRel = $rel['imagen'] !== '' ? '/public/uploads/blog/' . basename($rel['imagen']) : ''; ?>
          <article class="lp-blog-card">
            <a class="lp-blog-card-media" href="/vistas/blog.php?post=<?= Security::escapeHtml($rel['slug']) ?>">
              <?php if ($imgRel): ?>
              <img loading="lazy" src="<?= Security::escapeHtml($imgRel) ?>" alt="">
              <?php else: ?>
              <span class="lp-blog-card-placeholder"><i class="far fa-newspaper"></i></span>
              <?php endif; ?>
              <?php if ($rel['categoria'] !== ''): ?>
              <span class="lp-blog-chip"><?= Security::escapeHtml($rel['categoria']) ?></span>
              <?php endif; ?>
            </a>
            <div class="lp-blog-card-body">
              <div class="lp-blog-meta">
                <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($rel['fechaPublicacion'])) ?></span>
              </div>
              <h3><a href="/vistas/blog.php?post=<?= Security::escapeHtml($rel['slug']) ?>"><?= Security::escapeHtml($rel['titulo']) ?></a></h3>
              <span class="lp-blog-leer">Leer entrada <i class="fas fa-arrow-right"></i></span>
            </div>
          </article>
          <?php endforeach; ?>
        </div>
      </div>
    </section>
    <?php endif; ?>
  </article>
<?php
    include __DIR__ . '/landing/_footer.php';
    exit;
}

// Slug no encontrado → volvemos al listado
if ($slug !== '') {
    header('Location: /vistas/blog.php');
    exit;
}

// ── Listado ──────────────────────────────────────────────────────────
$porPagina  = 9;
$categoria  = trim($_GET['categoria'] ?? '');
$pagina     = max(1, (int)($_GET['pagina'] ?? 1));
$total      = contarPostsPublicados($categoria);
$totalPags  = max(1, (int)ceil($total / $porPagina));
$pagina     = min($pagina, $totalPags);
$posts      = listarPostsPublicados($porPagina, ($pagina - 1) * $porPagina, $categoria);
$categorias = listarCategoriasBlog();

$ajustes['tituloSeo'] = 'Blog y noticias — ' . ($ajustes['tituloSeo'] ?? '' ?: $cfg['nombreCentro']);
$ajustes['descripcionSeo'] = 'Noticias, eventos y actualidad de ' . $cfg['nombreCentro'] . '.';

// Enlace de paginación/categoría conservando los filtros activos
function blogUrl($pagina = 1, $categoria = '') {
    $qs = [];
    if ($categoria !== '') $qs['categoria'] = $categoria;
    if ($pagina > 1)       $qs['pagina'] = $pagina;
    return '/vistas/blog.php' . ($qs ? '?' . http_build_query($qs) : '');
}

include __DIR__ . '/landing/_head.php';
include __DIR__ . '/landing/_nav.php';
?>
  <header class="lp-blog-hero">
    <div class="lp-contenedor">
      <span class="lp-eyebrow">Actualidad</span>
      <h1>Blog del centro</h1>
      <p>Noticias, eventos y toda la actualidad de <?= Security::escapeHtml($cfg['nombreCentro']) ?>.</p>
    </div>
  </header>

  <section class="lp-sec lp-blog-listado" id="blog">
    <div class="lp-contenedor">

      <?php if (!empty($categorias)): ?>
      <nav class="lp-blog-cats" aria-label="Categorías">
        <a href="<?= blogUrl() ?>" class="lp-blog-cat<?= $categoria === '' ? ' activa' : '' ?>">Todas</a>
        <?php foreach ($categorias as $cat): ?>
        <a href="<?= Security::escapeHtml(blogUrl(1, $cat['categoria'])) ?>"
           class="lp-blog-cat<?= $categoria === $cat['categoria'] ? ' activa' : '' ?>">
          <?= Security::escapeHtml($cat['categoria']) ?> <span><?= (int)$cat['total'] ?></span>
        </a>
        <?php endforeach; ?>
      </nav>
      <?php endif; ?>

      <?php if (empty($posts)): ?>
      <div class="lp-blog-vacio">
        <i class="far fa-newspaper"></i>
        <h2>Todavía no hay entradas<?= $categoria !== '' ? ' en esta categoría' : '' ?></h2>
        <p>Muy pronto publicaremos aquí las noticias y novedades del centro.</p>
      </div>
      <?php else: ?>
      <div class="lp-blog-grid">
        <?php foreach ($posts as $i => $p):
            $img = $p['imagen'] !== '' ? '/public/uploads/blog/' . basename($p['imagen']) : '';
            $destacada = $pagina === 1 && $i === 0 && $categoria === ''; ?>
        <article class="lp-blog-card<?= $destacada ? ' lp-blog-card-destacada' : '' ?>">
          <a class="lp-blog-card-media" href="/vistas/blog.php?post=<?= Security::escapeHtml($p['slug']) ?>">
            <?php if ($img): ?>
            <img loading="lazy" src="<?= Security::escapeHtml($img) ?>" alt="">
            <?php else: ?>
            <span class="lp-blog-card-placeholder"><i class="far fa-newspaper"></i></span>
            <?php endif; ?>
            <?php if ($p['categoria'] !== ''): ?>
            <span class="lp-blog-chip"><?= Security::escapeHtml($p['categoria']) ?></span>
            <?php endif; ?>
          </a>
          <div class="lp-blog-card-body">
            <div class="lp-blog-meta">
              <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($p['fechaPublicacion'])) ?></span>
              <?php if ($p['autor'] !== ''): ?>
              <span><i class="far fa-user"></i> <?= Security::escapeHtml($p['autor']) ?></span>
              <?php endif; ?>
            </div>
            <h2><a href="/vistas/blog.php?post=<?= Security::escapeHtml($p['slug']) ?>"><?= Security::escapeHtml($p['titulo']) ?></a></h2>
            <?php if ($p['resumen'] !== ''): ?>
            <p><?= Security::escapeHtml(mb_substr($p['resumen'], 0, $destacada ? 260 : 140)) ?><?= mb_strlen($p['resumen']) > ($destacada ? 260 : 140) ? '…' : '' ?></p>
            <?php endif; ?>
            <span class="lp-blog-leer">Leer entrada <i class="fas fa-arrow-right"></i></span>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <?php if ($totalPags > 1): ?>
      <nav class="lp-blog-paginacion" aria-label="Paginación">
        <?php if ($pagina > 1): ?>
        <a href="<?= Security::escapeHtml(blogUrl($pagina - 1, $categoria)) ?>" class="lp-blog-pag-btn" aria-label="Anterior"><i class="fas fa-angle-left"></i></a>
        <?php endif; ?>
        <?php for ($n = 1; $n <= $totalPags; $n++): ?>
        <a href="<?= Security::escapeHtml(blogUrl($n, $categoria)) ?>"
           class="lp-blog-pag-btn<?= $n === $pagina ? ' activa' : '' ?>"<?= $n === $pagina ? ' aria-current="page"' : '' ?>><?= $n ?></a>
        <?php endfor; ?>
        <?php if ($pagina < $totalPags): ?>
        <a href="<?= Security::escapeHtml(blogUrl($pagina + 1, $categoria)) ?>" class="lp-blog-pag-btn" aria-label="Siguiente"><i class="fas fa-angle-right"></i></a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
      <?php endif; ?>

    </div>
  </section>
<?php
include __DIR__ . '/landing/_footer.php';
