<?php
// Pie de la landing. Espera: $cfg, $ajustes, $logoUrl (de _head)
$redes = is_array($ajustes['redes'] ?? null) ? $ajustes['redes'] : [];
$iconosRedes = [
    'facebook'  => 'fa-brands fa-facebook',
    'instagram' => 'fa-brands fa-instagram',
    'twitter'   => 'fa-brands fa-x-twitter',
    'linkedin'  => 'fa-brands fa-linkedin',
    'youtube'   => 'fa-brands fa-youtube',
    'tiktok'    => 'fa-brands fa-tiktok',
];
?>
</main>
<footer class="lp-footer">
  <div class="lp-contenedor lp-footer-grid">
    <div class="lp-footer-col">
      <div class="lp-footer-marca">
        <?php if ($logoUrl): ?>
        <img src="<?= Security::escapeHtml($logoUrl) ?>" alt="" class="lp-footer-logo">
        <?php endif; ?>
        <strong><?= Security::escapeHtml($cfg['nombreCentro']) ?></strong>
      </div>
      <?php if (!empty($cfg['direccionCentro']) || !empty($cfg['ciudadCentro'])): ?>
      <p><i class="fas fa-location-dot"></i>
        <?= Security::escapeHtml(trim($cfg['direccionCentro'] . ', ' . $cfg['cpCentro'] . ' ' . $cfg['ciudadCentro'], ', ')) ?></p>
      <?php endif; ?>
      <?php if (!empty($cfg['telefonoCentro'])): ?>
      <p><i class="fas fa-phone"></i> <a href="tel:<?= Security::escapeHtml(preg_replace('/\s+/', '', $cfg['telefonoCentro'])) ?>"><?= Security::escapeHtml($cfg['telefonoCentro']) ?></a></p>
      <?php endif; ?>
      <?php if (!empty($cfg['emailCentro'])): ?>
      <p><i class="fas fa-envelope"></i> <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>"><?= Security::escapeHtml($cfg['emailCentro']) ?></a></p>
      <?php endif; ?>
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

    <div class="lp-footer-col">
      <h4>Información legal</h4>
      <a href="/vistas/legal/aviso-legal.php">Aviso legal</a>
      <a href="/vistas/legal/politica-de-privacidad.php">Política de privacidad</a>
      <a href="/vistas/legal/politica-de-cookies.php">Política de cookies</a>
    </div>

    <div class="lp-footer-col">
      <h4>Acceso</h4>
      <a href="/vistas/login.php">Acceso a la plataforma</a>
      <?php if (FeatureGuard::check('feature_prematricula')): ?>
      <a href="/vistas/admisiones/pre-matricula.php">Pre-matrícula online</a>
      <a href="/vistas/admisiones/consultar.php">Consultar mi solicitud</a>
      <?php endif; ?>
    </div>
  </div>
  <div class="lp-footer-linea">
    <div class="lp-contenedor">
      © <?= date('Y') ?> <?= Security::escapeHtml($cfg['nombreCentro']) ?> · Todos los derechos reservados
    </div>
  </div>
</footer>
<script src="/public/js/landing/landing.js"></script>
</body>
</html>
