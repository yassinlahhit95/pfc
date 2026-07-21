<?php
// Navegación fija de la landing. Espera: $cfg, $ajustes, $menuAnclas, $logoUrl (de _head)
$prematriculaOn = FeatureGuard::check('feature_prematricula');
$isHome  = ($_SERVER['SCRIPT_NAME'] === '/index.php' || $_SERVER['SCRIPT_NAME'] === '/vistas/admin/landing/builder.php');
$isBlog  = (strpos($_SERVER['SCRIPT_NAME'], '/vistas/blog.php') !== false);
$homePrefix = $isHome ? '' : '/';

$redesNav = is_array($ajustes['redes'] ?? null) ? $ajustes['redes'] : [];
$iconosRedesNav = [
    'facebook'  => 'fa-brands fa-facebook-f',
    'instagram' => 'fa-brands fa-instagram',
    'twitter'   => 'fa-brands fa-x-twitter',
    'linkedin'  => 'fa-brands fa-linkedin-in',
    'youtube'   => 'fa-brands fa-youtube',
    'tiktok'    => 'fa-brands fa-tiktok',
];
$redesValidas = [];
foreach ($iconosRedesNav as $red => $icono) {
    $url = $redesNav[$red] ?? '';
    if (is_string($url) && preg_match('#^https?://#i', $url)) $redesValidas[$red] = $url;
}

// Resuelve el destino de un item del menú (ancla en la home, URL propia o página separada)
$lpEnlaceMenu = function ($info, $ancla) use ($homePrefix) {
    if (!empty($info['url']))      return $info['url'];
    if (!empty($info['separado'])) return '/vistas/contacto.php';
    return $homePrefix . '#' . $ancla;
};
$mostrarTopbar = ($ajustes['mostrarTopbar'] ?? 'si') === 'si';
?>
<!-- Barra superior de contacto -->
<?php if ($mostrarTopbar && (!empty($cfg['telefonoCentro']) || !empty($cfg['emailCentro']) || $redesValidas)): ?>
<div class="lp-topbar">
  <div class="lp-contenedor lp-topbar-inner">
    <div class="lp-topbar-datos">
      <?php if (!empty($cfg['telefonoCentro'])): ?>
      <a href="tel:<?= Security::escapeHtml(preg_replace('/\s+/', '', $cfg['telefonoCentro'])) ?>">
        <i class="fas fa-phone"></i><span><?= Security::escapeHtml($cfg['telefonoCentro']) ?></span>
      </a>
      <?php endif; ?>
      <?php if (!empty($cfg['emailCentro'])): ?>
      <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>">
        <i class="fas fa-envelope"></i><span><?= Security::escapeHtml($cfg['emailCentro']) ?></span>
      </a>
      <?php endif; ?>
      <?php if (!empty($cfg['ciudadCentro'])): ?>
      <span class="lp-topbar-ciudad"><i class="fas fa-location-dot"></i><span><?= Security::escapeHtml($cfg['ciudadCentro']) ?></span></span>
      <?php endif; ?>
    </div>
    <div class="lp-topbar-redes">
      <?php foreach ($redesValidas as $red => $url): ?>
      <a href="<?= Security::escapeHtml($url) ?>" target="_blank" rel="noopener" aria-label="<?= Security::escapeHtml(ucfirst($red)) ?>">
        <i class="<?= $iconosRedesNav[$red] ?>"></i>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- Overlay de fondo para el drawer móvil -->
<div class="lp-nav-overlay" id="lp-nav-overlay" aria-hidden="true"></div>

<header class="lp-nav" id="lp-nav">
  <div class="lp-contenedor lp-nav-inner">

    <!-- Marca / Logo -->
    <a href="<?= $homePrefix ?: '/' ?>#inicio" class="lp-marca">
      <?php if ($logoUrl): ?>
      <img src="<?= Security::escapeHtml($logoUrl) ?>" alt="<?= Security::escapeHtml($cfg['nombreCentro']) ?>" class="lp-marca-logo">
      <?php else: ?>
      <span class="lp-marca-icono"><i class="fas fa-graduation-cap"></i></span>
      <?php endif; ?>
      <span class="lp-marca-nombre"><?= Security::escapeHtml($cfg['nombreCentro']) ?></span>
    </a>

    <!-- Links desktop -->
    <nav class="lp-nav-links" id="lp-nav-links" aria-label="Navegación principal">
      <?php foreach ($menuAnclas as $ancla => $info):
          $enlace   = $lpEnlaceMenu($info, $ancla);
          $esAncla  = $isHome && empty($info['url']) && empty($info['separado']);
          $esActiva = ($ancla === 'noticias' && $isBlog);
      ?>
      <a href="<?= Security::escapeHtml($enlace) ?>"<?= $esAncla ? ' data-lp-ancla="' . Security::escapeHtml($ancla) . '"' : '' ?><?= $esActiva ? ' class="activa"' : '' ?>><?= Security::escapeHtml($info['texto']) ?></a>
      <?php endforeach; ?>
    </nav>

    <!-- CTAs desktop + burger -->
    <div class="lp-nav-cta">
      <button class="lp-btn-theme" id="lp-theme-toggle" aria-label="Cambiar tema">
        <i class="fas fa-moon"></i>
      </button>
      <a href="vistas/login.php" class="lp-boton-fantasma">Acceso</a>
      <?php if ($prematriculaOn): ?>
      <a href="vistas/admisiones/pre-matricula.php" class="lp-boton-primario">Pre-matrícula</a>
      <?php endif; ?>

      <button class="lp-nav-burger" id="lp-nav-burger"
              aria-label="Abrir menú" aria-expanded="false" aria-controls="lp-nav-movil">
        <span class="lp-nav-burger-line"></span>
        <span class="lp-nav-burger-line"></span>
        <span class="lp-nav-burger-line"></span>
      </button>
    </div>

  </div>
</header>

<!-- Drawer móvil (slide-over) -->
<nav class="lp-nav-movil" id="lp-nav-movil" aria-label="Menú móvil" aria-hidden="true">
  <div class="lp-nav-movil-header">
    <a href="<?= $homePrefix ?: '/' ?>#inicio" class="lp-marca">
      <?php if ($logoUrl): ?>
      <img src="<?= Security::escapeHtml($logoUrl) ?>" alt="" class="lp-marca-logo">
      <?php else: ?>
      <span class="lp-marca-icono"><i class="fas fa-graduation-cap"></i></span>
      <?php endif; ?>
      <span class="lp-marca-nombre"><?= Security::escapeHtml($cfg['nombreCentro']) ?></span>
    </a>
    <button class="lp-nav-movil-close" id="lp-nav-close" aria-label="Cerrar menú">
      <i class="fas fa-xmark"></i>
    </button>
  </div>

  <div class="lp-nav-movil-links">
    <?php foreach ($menuAnclas as $ancla => $info): ?>
    <a href="<?= Security::escapeHtml($lpEnlaceMenu($info, $ancla)) ?>"><?= Security::escapeHtml($info['texto']) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($redesValidas): ?>
  <div class="lp-nav-movil-redes">
    <?php foreach ($redesValidas as $red => $url): ?>
    <a href="<?= Security::escapeHtml($url) ?>" target="_blank" rel="noopener" aria-label="<?= Security::escapeHtml(ucfirst($red)) ?>">
      <i class="<?= $iconosRedesNav[$red] ?>"></i>
    </a>
    <?php endforeach; ?>
  </div>
  <?php endif; ?>

  <div class="lp-nav-movil-ctas">
    <a href="vistas/login.php" class="lp-boton-fantasma">Acceso a la plataforma</a>
    <?php if ($prematriculaOn): ?>
    <a href="vistas/admisiones/pre-matricula.php" class="lp-boton-primario">
      <i class="fas fa-file-signature"></i> Pre-matrícula online
    </a>
    <?php endif; ?>
  </div>
</nav>

<main id="inicio">
