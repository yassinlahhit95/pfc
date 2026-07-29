<?php
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../modelos/landing.php';
require_once __DIR__ . '/../modelos/blog.php';
require_once __DIR__ . '/../include/landing/secciones.php';
require_once __DIR__ . '/../include/landing/plantillas.php';
require_once __DIR__ . '/../include/R2Client.php';
// ══════════════════════════════════════════════════════════════════════
// BLOG PÚBLICO DEL CENTRO
// ══════════════════════════════════════════════════════════════════════
// /vistas/blog.php            → listado con categorías y paginación
// /vistas/blog.php?post=slug  → detalle de una entrada
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
foreach ($secciones as $seccion) {
    if (!isset($tipos[$seccion['tipo']])) continue;
    $contenido = json_decode($seccion['contenido'] ?? '{}', true) ?: [];
    $navVisible = $contenido['navVisible'] ?? (!empty($tipos[$seccion['tipo']]['menu']) ? 'si' : 'no');
    $navTexto   = $contenido['navTexto'] ?? ($tipos[$seccion['tipo']]['menu'] ?? '');
    if ($navVisible === 'si' && !empty($navTexto) && !isset($menuAnclas[$seccion['tipo']])) {
        $esSeparado = ($seccion['tipo'] === 'contacto' && ($contenido['modoVisualizacion'] ?? 'integrado') === 'separado');
        $menuAnclas[$seccion['tipo']] = ['texto' => $navTexto, 'separado' => $esSeparado];
        if ($seccion['tipo'] === 'noticias') $menuAnclas[$seccion['tipo']]['url'] = '/vistas/blog.php';
    }
}

// ── Detalle de una entrada ───────────────────────────────────────────
$slug = trim($_GET['post'] ?? '');
$post = $slug !== '' ? obtenerPostPorSlug($slug) : null;

if ($post) {
    $ajustes['tituloSeo']      = $post['titulo'] . ' — ' . $cfg['nombreCentro'];
    $ajustes['descripcionSeo'] = $post['resumen'] ?: mb_substr(strip_tags($post['contenido']), 0, 155);

    $imgPost    = R2Client::imagenUrl(
        __DIR__ . '/../public/uploads/blog/' . basename($post['imagen']),
        $post['imagen'] !== '' ? '/public/uploads/blog/' . basename($post['imagen']) : '',
        'blog/' . basename($post['imagen'])
    );
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
          <i class="fas fa-angle-right"></i>
          <a href="/vistas/blog.php?categoria=<?= urlencode($post['categoria']) ?>"><?= Security::escapeHtml($post['categoria']) ?></a>
        </nav>
        <span class="lp-blog-chip"><?= Security::escapeHtml($post['categoria']) ?></span>
        <h1><?= Security::escapeHtml($post['titulo']) ?></h1>
        <div class="lp-articulo-meta">
          <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($post['fechaPublicacion'])) ?></span>
          <span><i class="far fa-user"></i> <?= Security::escapeHtml($post['autor']) ?></span>
          <span><i class="far fa-clock"></i> <?= $minLectura ?> min de lectura</span>
        </div>
      </div>
    </header>

    <div class="lp-contenedor lp-articulo-contenedor">
      <figure class="lp-articulo-portada">
        <img src="<?= Security::escapeHtml($imgPost) ?>" alt="<?= Security::escapeHtml($post['titulo']) ?>">
      </figure>
      <div class="lp-articulo-cuerpo">
        <p class="lp-articulo-entradilla"><?= nl2br(Security::escapeHtml($post['resumen'])) ?></p>
        <?= $post['contenido'] /* ya saneado con HtmlSanitizer al guardar */ ?>
        <?php foreach ($parrafos as $parrafo): ?>
          <p><?= nl2br(Security::escapeHtml(trim($parrafo))) ?></p>
        <?php endforeach; ?>
      </div>

      <footer class="lp-articulo-pie">
        <a href="/vistas/blog.php" class="lp-boton-borde"><i class="fas fa-arrow-left"></i> Volver al blog</a>
      </footer>
    </div>

    <section class="lp-sec lp-blog-relacionados">
      <div class="lp-contenedor">
        <div class="lp-sec-cabecera">
          <h2>Seguir leyendo</h2>
        </div>
        <div class="lp-blog-grid">
          <?php foreach ($relacionados as $rel):
              $imgRel = R2Client::imagenUrl(
                  __DIR__ . '/../public/uploads/blog/' . basename($rel['imagen']),
                  $rel['imagen'] !== '' ? '/public/uploads/blog/' . basename($rel['imagen']) : '',
                  'blog/' . basename($rel['imagen'])
              ); ?>
          <article class="lp-blog-card">
            <a class="lp-blog-card-media" href="/vistas/blog.php?post=<?= Security::escapeHtml($rel['slug']) ?>">
              <img loading="lazy" src="<?= Security::escapeHtml($imgRel) ?>" alt="">
              <span class="lp-blog-card-placeholder"><i class="far fa-newspaper"></i></span>
              <span class="lp-blog-chip"><?= Security::escapeHtml($rel['categoria']) ?></span>
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
  </article>
    <?php include __DIR__ . '/landing/_footer.php';
    exit; ?>
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

      <nav class="lp-blog-cats" aria-label="Categorías">
        <a href="<?= blogUrl() ?>" class="lp-blog-cat<?= $categoria === '' ? ' activa' : '' ?>">Todas</a>
        <?php foreach ($categorias as $categoriaItem): ?>
        <a href="<?= Security::escapeHtml(blogUrl(1, $categoriaItem['categoria'])) ?>"
           class="lp-blog-cat<?= $categoria === $categoriaItem['categoria'] ? ' activa' : '' ?>">
          <?= Security::escapeHtml($categoriaItem['categoria']) ?> <span><?= (int)$categoriaItem['total'] ?></span>
        </a>
        <?php endforeach; ?>
      </nav>
      <div class="lp-blog-vacio">
        <i class="far fa-newspaper"></i>
        <h2>Todavía no hay entradas<?= $categoria !== '' ? ' en esta categoría' : '' ?></h2>
        <p>Muy pronto publicaremos aquí las noticias y novedades del centro.</p>
      </div>
      <div class="lp-blog-grid">
        <?php foreach ($posts as $i => $postItem):
            $img = R2Client::imagenUrl(
                __DIR__ . '/../public/uploads/blog/' . basename($postItem['imagen']),
                $postItem['imagen'] !== '' ? '/public/uploads/blog/' . basename($postItem['imagen']) : '',
                'blog/' . basename($postItem['imagen'])
            );
            $destacada = $pagina === 1 && $i === 0 && $categoria === ''; ?>
        <article class="lp-blog-card<?= $destacada ? ' lp-blog-card-destacada' : '' ?>">
          <a class="lp-blog-card-media" href="/vistas/blog.php?post=<?= Security::escapeHtml($postItem['slug']) ?>">
            <img loading="lazy" src="<?= Security::escapeHtml($img) ?>" alt="">
            <span class="lp-blog-card-placeholder"><i class="far fa-newspaper"></i></span>
            <span class="lp-blog-chip"><?= Security::escapeHtml($postItem['categoria']) ?></span>
          </a>
          <div class="lp-blog-card-body">
            <div class="lp-blog-meta">
              <span><i class="far fa-calendar"></i> <?= date('d/m/Y', strtotime($postItem['fechaPublicacion'])) ?></span>
              <span><i class="far fa-user"></i> <?= Security::escapeHtml($postItem['autor']) ?></span>
            </div>
            <h2><a href="/vistas/blog.php?post=<?= Security::escapeHtml($postItem['slug']) ?>"><?= Security::escapeHtml($postItem['titulo']) ?></a></h2>
            <p><?= Security::escapeHtml(mb_substr($postItem['resumen'], 0, $destacada ? 260 : 140)) ?><?= mb_strlen($postItem['resumen']) > ($destacada ? 260 : 140) ? '…' : '' ?></p>
            <span class="lp-blog-leer">Leer entrada <i class="fas fa-arrow-right"></i></span>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <nav class="lp-blog-paginacion" aria-label="Paginación">
        <a href="<?= Security::escapeHtml(blogUrl($pagina - 1, $categoria)) ?>" class="lp-blog-pag-btn" aria-label="Anterior"><i class="fas fa-angle-left"></i></a>
        <?php for ($n = 1; $n <= $totalPags; $n++): ?>
        <a href="<?= Security::escapeHtml(blogUrl($n, $categoria)) ?>"
           class="lp-blog-pag-btn<?= $n === $pagina ? ' activa' : '' ?>"<?= $n === $pagina ? ' aria-current="page"' : '' ?>><?= $n ?></a>
        <?php endfor; ?>
        <a href="<?= Security::escapeHtml(blogUrl($pagina + 1, $categoria)) ?>" class="lp-blog-pag-btn" aria-label="Siguiente"><i class="fas fa-angle-right"></i></a>
      </nav>
    </div>
  </section>
<?php include __DIR__ . '/landing/_footer.php'; ?>
