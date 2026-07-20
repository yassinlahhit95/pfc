<?php
// Pie de la landing. Espera: $cfg, $ajustes, $menuAnclas, $logoUrl (de _head)
$redes = is_array($ajustes['redes'] ?? null) ? $ajustes['redes'] : [];
$iconosRedes = [
    'facebook'  => 'fa-brands fa-facebook-f',
    'instagram' => 'fa-brands fa-instagram',
    'twitter'   => 'fa-brands fa-x-twitter',
    'linkedin'  => 'fa-brands fa-linkedin-in',
    'youtube'   => 'fa-brands fa-youtube',
    'tiktok'    => 'fa-brands fa-tiktok',
];
$prematriculaFooter = FeatureGuard::check('feature_prematricula');
$isHomeFooter = ($_SERVER['SCRIPT_NAME'] === '/index.php' || $_SERVER['SCRIPT_NAME'] === '/vistas/admin/landing/builder.php');
$homePrefixFooter = $isHomeFooter ? '' : '/';
$menuFooter = is_array($menuAnclas ?? null) ? $menuAnclas : [];
?>
</main>

<!-- Banda CTA previa al pie -->
<section class="lp-footer-cta">
  <div class="lp-contenedor lp-footer-cta-inner">
    <div class="lp-footer-cta-texto">
      <h2>¿Listo para dar el siguiente paso?</h2>
      <p>Infórmate sin compromiso: te ayudamos a elegir el ciclo que mejor encaja contigo.</p>
    </div>
    <div class="lp-footer-cta-botones">
      <?php if ($prematriculaFooter): ?>
      <a href="/vistas/admisiones/pre-matricula.php" class="lp-boton-primario lp-boton-grande">
        <i class="fas fa-file-signature"></i> Pre-matrícula online
      </a>
      <?php endif; ?>
      <a href="<?= $homePrefixFooter ?>#contacto" class="lp-boton-borde lp-boton-grande">
        <i class="far fa-comments"></i> Contactar
      </a>
    </div>
  </div>
</section>

<footer class="lp-footer">
  <div class="lp-contenedor lp-footer-grid">

    <!-- Marca + descripción + redes -->
    <div class="lp-footer-col lp-footer-col-marca">
      <div class="lp-footer-marca">
        <?php if ($logoUrl): ?>
        <img src="<?= Security::escapeHtml($logoUrl) ?>" alt="" class="lp-footer-logo">
        <?php endif; ?>
        <strong><?= Security::escapeHtml($cfg['nombreCentro']) ?></strong>
      </div>
      <p class="lp-footer-desc">
        Centro de Formación Profesional. Ciclos oficiales, prácticas en empresas
        y acompañamiento personalizado durante toda tu formación.
      </p>
      <?php $hayRedes = array_filter(array_intersect_key($redes, $iconosRedes)); if ($hayRedes): ?>
      <div class="lp-footer-redes">
        <?php foreach ($hayRedes as $red => $url):
            if (!preg_match('#^https?://#i', $url)) continue; ?>
        <a href="<?= Security::escapeHtml($url) ?>" target="_blank" rel="noopener" aria-label="<?= Security::escapeHtml(ucfirst($red)) ?>">
          <i class="<?= $iconosRedes[$red] ?>"></i>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- Secciones de la web -->
    <div class="lp-footer-col">
      <h4>El centro</h4>
      <a href="<?= $homePrefixFooter ?: '/' ?>#inicio">Inicio</a>
      <?php foreach ($menuFooter as $ancla => $info):
          if (!empty($info['url'])) { $enlace = $info['url']; }
          elseif (!empty($info['separado'])) { $enlace = '/vistas/contacto.php'; }
          else { $enlace = $homePrefixFooter . '#' . Security::escapeHtml($ancla); }
      ?>
      <a href="<?= Security::escapeHtml($enlace) ?>"><?= Security::escapeHtml($info['texto']) ?></a>
      <?php endforeach; ?>
      <?php if (!isset($menuFooter['noticias'])): ?>
      <a href="/vistas/blog.php">Blog</a>
      <?php endif; ?>
    </div>

    <!-- Contacto -->
    <div class="lp-footer-col">
      <h4>Contacto</h4>
      <?php if (!empty($cfg['direccionCentro']) || !empty($cfg['ciudadCentro'])): ?>
      <p><i class="fas fa-location-dot"></i>
        <span><?= Security::escapeHtml(trim($cfg['direccionCentro'] . ', ' . $cfg['cpCentro'] . ' ' . $cfg['ciudadCentro'], ', ')) ?></span></p>
      <?php endif; ?>
      <?php if (!empty($cfg['telefonoCentro'])): ?>
      <p><i class="fas fa-phone"></i> <a href="tel:<?= Security::escapeHtml(preg_replace('/\s+/', '', $cfg['telefonoCentro'])) ?>"><?= Security::escapeHtml($cfg['telefonoCentro']) ?></a></p>
      <?php endif; ?>
      <?php if (!empty($cfg['emailCentro'])): ?>
      <p><i class="fas fa-envelope"></i> <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>"><?= Security::escapeHtml($cfg['emailCentro']) ?></a></p>
      <?php endif; ?>
    </div>

    <!-- Acceso rápido + legal -->
    <div class="lp-footer-col">
      <h4>Acceso rápido</h4>
      <a href="/vistas/login.php">Acceso a la plataforma</a>
      <?php if ($prematriculaFooter): ?>
      <a href="/vistas/admisiones/pre-matricula.php">Pre-matrícula online</a>
      <a href="/vistas/admisiones/consultar.php">Consultar mi solicitud</a>
      <?php endif; ?>
      <a href="/vistas/legal/aviso-legal.php">Aviso legal</a>
      <a href="/vistas/legal/politica-de-privacidad.php">Política de privacidad</a>
      <a href="/vistas/legal/politica-de-cookies.php">Política de cookies</a>
      <a href="#" id="cookie-prefs-link">Preferencias de cookies</a>
    </div>

  </div>

  <div class="lp-footer-linea">
    <div class="lp-contenedor">
      <div>© <?= date('Y') ?> <?= Security::escapeHtml($cfg['nombreCentro']) ?> · Todos los derechos reservados</div>
      <div class="lp-footer-legal-links">
        <a href="/vistas/legal/aviso-legal.php">Aviso Legal</a>
        <a href="/vistas/legal/politica-de-privacidad.php">Privacidad</a>
        <a href="/vistas/legal/politica-de-cookies.php">Cookies</a>
      </div>
    </div>
  </div>
</footer>

<!-- Botón volver arriba -->
<button class="lp-volver-arriba" id="lp-volver-arriba" aria-label="Volver arriba">
  <i class="fas fa-arrow-up"></i>
</button>

<script src="/landing-system/assets/js/landing.js"></script>

<link rel="stylesheet" href="/public/css/features/cookie-consent.css">
<script src="/public/js/core/cookie-consent.js"></script>
<script>
  document.addEventListener('click', function (e) {
    var link = e.target.closest('#cookie-prefs-link');
    if (!link) return;
    e.preventDefault();
    if (window.CookieConsent) window.CookieConsent.reopen();
  });
</script>
</body>
</html>
