<?php
// Navegación fija de la landing. Espera: $cfg, $menuAnclas, $logoUrl (de _head)
$prematriculaOn = FeatureGuard::check('feature_prematricula');
$isHome = ($_SERVER['SCRIPT_NAME'] === '/index.php' || $_SERVER['SCRIPT_NAME'] === '/vistas/admin/landing/builder.php');
$homePrefix = $isHome ? '' : '/';
?>
<!-- Overlay de fondo para el drawer móvil -->
<div class="lp-nav-overlay" id="lp-nav-overlay" aria-hidden="true"></div>

<header class="lp-nav" id="lp-nav">
  <div class="lp-contenedor lp-nav-inner">

    <!-- Marca / Logo -->
    <a href="<?= $homePrefix ?>#inicio" class="lp-marca">
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
          $enlace = !empty($info['separado']) ? '/vistas/contacto.php' : $homePrefix . '#' . Security::escapeHtml($ancla);
      ?>
      <a href="<?= $enlace ?>"><?= Security::escapeHtml($info['texto']) ?></a>
      <?php endforeach; ?>
    </nav>

    <!-- CTAs desktop + burger -->
    <div class="lp-nav-cta">
      <a href="/vistas/login.php" class="lp-boton-fantasma">Acceso</a>
      <?php if ($prematriculaOn): ?>
      <a href="/vistas/admisiones/pre-matricula.php" class="lp-boton-primario">Pre-matrícula</a>
      <?php endif; ?>

      <!-- Burger (visible only on mobile) -->
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
    <!-- Marca dentro del drawer -->
    <a href="<?= $homePrefix ?>#inicio" class="lp-marca" style="pointer-events:auto;">
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
    <?php foreach ($menuAnclas as $ancla => $info):
        $enlace = !empty($info['separado']) ? '/vistas/contacto.php' : $homePrefix . '#' . Security::escapeHtml($ancla);
    ?>
    <a href="<?= $enlace ?>"><?= Security::escapeHtml($info['texto']) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="lp-nav-movil-ctas">
    <a href="/vistas/login.php" class="lp-boton-fantasma">Acceso a la plataforma</a>
    <?php if ($prematriculaOn): ?>
    <a href="/vistas/admisiones/pre-matricula.php" class="lp-boton-primario">
      <i class="fas fa-file-signature"></i> Pre-matrícula online
    </a>
    <?php endif; ?>
  </div>
</nav>

<main id="inicio">

<script>
(function () {
  var nav     = document.getElementById('lp-nav');
  var burger  = document.getElementById('lp-nav-burger');
  var drawer  = document.getElementById('lp-nav-movil');
  var overlay = document.getElementById('lp-nav-overlay');
  var closeBtn= document.getElementById('lp-nav-close');
  var isOpen  = false;

  function openMenu() {
    isOpen = true;
    drawer.classList.add('abierto');
    overlay.classList.add('visible');
    nav.classList.add('lp-nav-abierta');
    burger.setAttribute('aria-expanded', 'true');
    drawer.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeMenu() {
    isOpen = false;
    drawer.classList.remove('abierto');
    overlay.classList.remove('visible');
    nav.classList.remove('lp-nav-abierta');
    burger.setAttribute('aria-expanded', 'false');
    drawer.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  burger.addEventListener('click', function () { isOpen ? closeMenu() : openMenu(); });
  overlay.addEventListener('click', closeMenu);
  closeBtn.addEventListener('click', closeMenu);

  // Close on link click (scroll to anchor)
  drawer.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', closeMenu);
  });

  // Close on ESC
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && isOpen) closeMenu();
  });

  // Navbar shrink on scroll
  var lastScroll = 0;
  window.addEventListener('scroll', function () {
    var y = window.scrollY;
    if (y > 40) {
      nav.classList.add('lp-nav-scrolled');
    } else {
      nav.classList.remove('lp-nav-scrolled');
    }
    lastScroll = y;
  }, { passive: true });
}());
</script>
