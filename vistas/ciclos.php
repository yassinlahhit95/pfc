<?php
require_once __DIR__ . '/../modelos/configuracion.php';
require_once __DIR__ . '/../include/Security.php';
require_once __DIR__ . '/../include/FeatureGuard.php';
require_once __DIR__ . '/../modelos/landing.php';
require_once __DIR__ . '/../modelos/landingCiclos.php';
require_once __DIR__ . '/../include/landing/secciones.php';
require_once __DIR__ . '/../include/landing/plantillas.php';
require_once __DIR__ . '/../include/R2Client.php';
// ══════════════════════════════════════════════════════════════════════
// CATÁLOGO PÚBLICO DE CICLOS
// ══════════════════════════════════════════════════════════════════════
// /vistas/ciclos.php              → listado del catálogo completo
// /vistas/ciclos.php?ciclo=slug   → ficha de detalle de un ciclo
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

$prematriculaOn = FeatureGuard::check('feature_prematricula');
$enlaceDefecto  = $prematriculaOn ? '/vistas/admisiones/pre-matricula.php' : '#contacto';

// ── Ficha de detalle ─────────────────────────────────────────────────
$slug  = trim($_GET['ciclo'] ?? '');
$ciclo = $slug !== '' ? obtenerCicloLandingPorSlug($slug) : null;

if ($ciclo) {
    $ajustes['tituloSeo']      = $ciclo['titulo'] . ' — ' . $cfg['nombreCentro'];
    $ajustes['descripcionSeo'] = $ciclo['resumen'] ?: mb_substr(strip_tags($ciclo['descripcion']), 0, 155);

    $imgCiclo     = R2Client::imagenUrl(
        __DIR__ . '/../public/uploads/ofertaCiclos/' . basename($ciclo['imagen']),
        $ciclo['imagen'] !== '' ? '/public/uploads/ofertaCiclos/' . basename($ciclo['imagen']) : '',
        'ofertaCiclos/' . basename($ciclo['imagen'])
    );
    $relacionados = listarCiclosLandingRelacionados($ciclo['idLandingCiclo'], 3);

    include __DIR__ . '/landing/_head.php';
    include __DIR__ . '/landing/_nav.php';
?>
  <article class="lp-articulo">
    <header class="lp-articulo-cabecera">
      <div class="lp-contenedor">
        <nav class="lp-migas" aria-label="Ruta">
          <a href="/">Inicio</a>
          <i class="fas fa-angle-right"></i>
          <a href="/vistas/ciclos.php">Catálogo de ciclos</a>
        </nav>
        <span class="lp-blog-chip"><?= Security::escapeHtml($ciclo['etiqueta']) ?></span>
        <h1><?= Security::escapeHtml($ciclo['titulo']) ?></h1>
        <div class="lp-ciclo-badges">
        </div>
      </div>
    </header>

    <div class="lp-contenedor lp-articulo-contenedor">
      <figure class="lp-articulo-portada">
        <img src="<?= Security::escapeHtml($imgCiclo) ?>" alt="<?= Security::escapeHtml($ciclo['titulo']) ?>">
      </figure>
      <div class="lp-articulo-cuerpo">
        <p class="lp-articulo-entradilla"><?= nl2br(Security::escapeHtml($ciclo['resumen'])) ?></p>
        <?= $ciclo['descripcion'] /* ya saneado con HtmlSanitizer al guardar */ ?>
      </div>

      <footer class="lp-articulo-pie">
        <a href="/vistas/ciclos.php" class="lp-boton-borde"><i class="fas fa-arrow-left"></i> Volver al catálogo</a>
        <a href="<?= Security::escapeHtml($enlaceDefecto) ?>" class="lp-boton-primario">
          <?= $prematriculaOn ? 'Solicitar plaza' : 'Contactar' ?> <i class="fas fa-arrow-right"></i>
        </a>
      </footer>
    </div>

    <section class="lp-sec lp-blog-relacionados">
      <div class="lp-contenedor">
        <div class="lp-sec-cabecera">
          <h2>Otros ciclos</h2>
        </div>
        <div class="lp-blog-grid">
              $imgRel = R2Client::imagenUrl(
                  __DIR__ . '/../public/uploads/ofertaCiclos/' . basename($rel['imagen']),
                  $rel['imagen'] !== '' ? '/public/uploads/ofertaCiclos/' . basename($rel['imagen']) : '',
                  'ofertaCiclos/' . basename($rel['imagen'])
              ); ?>
          <article class="lp-blog-card lp-ciclo-card">
            <a class="lp-blog-card-media" href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($rel['slug']) ?>">
              <img loading="lazy" src="<?= Security::escapeHtml($imgRel) ?>" alt="">
              <span class="lp-blog-card-placeholder"><i class="fas fa-graduation-cap"></i></span>
              <span class="lp-blog-chip"><?= Security::escapeHtml($rel['etiqueta']) ?></span>
            </a>
            <div class="lp-blog-card-body">
              <h3><a href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($rel['slug']) ?>"><?= Security::escapeHtml($rel['titulo']) ?></a></h3>
              <span class="lp-blog-leer">Ver ficha <i class="fas fa-arrow-right"></i></span>
            </div>
          </article>
        </div>
      </div>
    </section>
  </article>
    include __DIR__ . '/landing/_footer.php';
    exit;
}

// Slug no encontrado → volvemos al listado
if ($slug !== '') {
    header('Location: /vistas/ciclos.php');
    exit;
}

// ── Listado del catálogo ────────────────────────────────────────────
$porPagina = 9;
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$total     = contarCiclosLandingPublicados();
$totalPags = max(1, (int)ceil($total / $porPagina));
$pagina    = min($pagina, $totalPags);
$ciclos    = listarCiclosLandingPublicados($porPagina, ($pagina - 1) * $porPagina);

$ajustes['tituloSeo'] = 'Catálogo de ciclos — ' . ($ajustes['tituloSeo'] ?? '' ?: $cfg['nombreCentro']);
$ajustes['descripcionSeo'] = 'Ciclos formativos de ' . $cfg['nombreCentro'] . ': temario, requisitos y salidas profesionales.';

function cicloUrl($pagina = 1) {
    return '/vistas/ciclos.php' . ($pagina > 1 ? '?pagina=' . (int)$pagina : '');
}

include __DIR__ . '/landing/_head.php';
include __DIR__ . '/landing/_nav.php';
?>
  <header class="lp-blog-hero">
    <div class="lp-contenedor">
      <span class="lp-eyebrow">Formación Profesional</span>
      <h1>Catálogo de ciclos</h1>
      <p>Todos los ciclos formativos de <?= Security::escapeHtml($cfg['nombreCentro']) ?>, con temario, requisitos y salidas profesionales.</p>
    </div>
  </header>

  <section class="lp-sec lp-blog-listado" id="ciclos">
    <div class="lp-contenedor">

      <div class="lp-blog-vacio">
        <i class="fas fa-graduation-cap"></i>
        <h2>Todavía no hay ciclos publicados</h2>
        <p>Muy pronto publicaremos aquí nuestra oferta formativa completa.</p>
      </div>
      <div class="lp-blog-grid">
            $img = R2Client::imagenUrl(
                __DIR__ . '/../public/uploads/ofertaCiclos/' . basename($cicloItem['imagen']),
                $cicloItem['imagen'] !== '' ? '/public/uploads/ofertaCiclos/' . basename($cicloItem['imagen']) : '',
                'ofertaCiclos/' . basename($cicloItem['imagen'])
            ); ?>
        <article class="lp-blog-card lp-ciclo-card">
          <a class="lp-blog-card-media" href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($cicloItem['slug']) ?>">
            <img loading="lazy" src="<?= Security::escapeHtml($img) ?>" alt="">
            <span class="lp-blog-card-placeholder"><i class="fas fa-graduation-cap"></i></span>
            <span class="lp-blog-chip"><?= Security::escapeHtml($cicloItem['etiqueta']) ?></span>
          </a>
          <div class="lp-blog-card-body">
            <div class="lp-blog-meta">
            </div>
            <h2><a href="/vistas/ciclos.php?ciclo=<?= Security::escapeHtml($cicloItem['slug']) ?>"><?= Security::escapeHtml($cicloItem['titulo']) ?></a></h2>
            <p><?= Security::escapeHtml(mb_substr($cicloItem['resumen'], 0, 140)) ?><?= mb_strlen($cicloItem['resumen']) > 140 ? '…' : '' ?></p>
            <span class="lp-blog-leer">Ver ficha <i class="fas fa-arrow-right"></i></span>
          </div>
        </article>
      </div>

      <nav class="lp-blog-paginacion" aria-label="Paginación">
        <a href="<?= Security::escapeHtml(cicloUrl($pagina - 1)) ?>" class="lp-blog-pag-btn" aria-label="Anterior"><i class="fas fa-angle-left"></i></a>
        <a href="<?= Security::escapeHtml(cicloUrl($n)) ?>"
           class="lp-blog-pag-btn<?= $n === $pagina ? ' activa' : '' ?>"<?= $n === $pagina ? ' aria-current="page"' : '' ?>><?= $n ?></a>
        <a href="<?= Security::escapeHtml(cicloUrl($pagina + 1)) ?>" class="lp-blog-pag-btn" aria-label="Siguiente"><i class="fas fa-angle-right"></i></a>
      </nav>
    </div>
  </section>
include __DIR__ . '/landing/_footer.php';
