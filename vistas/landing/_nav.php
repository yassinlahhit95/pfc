<?php
// Navegación fija de la landing. Espera: $cfg, $menuAnclas, $logoUrl (de _head)
$prematriculaOn = FeatureGuard::check('feature_prematricula');
?>
<header class="lp-nav" id="lp-nav">
  <div class="lp-contenedor lp-nav-inner">
    <a href="#inicio" class="lp-marca">
      <?php if ($logoUrl): ?>
      <img src="<?= Security::escapeHtml($logoUrl) ?>" alt="<?= Security::escapeHtml($cfg['nombreCentro']) ?>" class="lp-marca-logo">
      <?php else: ?>
      <span class="lp-marca-icono"><i class="fas fa-graduation-cap"></i></span>
      <?php endif; ?>
      <span class="lp-marca-nombre"><?= Security::escapeHtml($cfg['nombreCentro']) ?></span>
    </a>

    <nav class="lp-nav-links" id="lp-nav-links">
      <?php foreach ($menuAnclas as $ancla => $etiqueta): ?>
      <a href="#<?= Security::escapeHtml($ancla) ?>"><?= Security::escapeHtml($etiqueta) ?></a>
      <?php endforeach; ?>
    </nav>

    <div class="lp-nav-cta">
      <a href="/vistas/login.php" class="lp-boton-fantasma">Acceso</a>
      <?php if ($prematriculaOn): ?>
      <a href="/vistas/admisiones/pre-matricula.php" class="lp-boton-primario">Pre-matrícula</a>
      <?php endif; ?>
      <button class="lp-nav-burger" id="lp-nav-burger" aria-label="Abrir menú" aria-expanded="false">
        <i class="fas fa-bars"></i>
      </button>
    </div>
  </div>

  <div class="lp-nav-movil" id="lp-nav-movil">
    <?php foreach ($menuAnclas as $ancla => $etiqueta): ?>
    <a href="#<?= Security::escapeHtml($ancla) ?>"><?= Security::escapeHtml($etiqueta) ?></a>
    <?php endforeach; ?>
    <a href="/vistas/login.php">Acceso a la plataforma</a>
    <?php if ($prematriculaOn): ?>
    <a href="/vistas/admisiones/pre-matricula.php" class="lp-nav-movil-cta">Pre-matrícula</a>
    <?php endif; ?>
  </div>
</header>
<main id="inicio">
