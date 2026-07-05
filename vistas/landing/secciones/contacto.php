<?php
// Contacto: datos del centro (configuracion_centro) + formulario opcional.
$mostrarFormulario = ($contenido['mostrarFormulario'] ?? 'si') === 'si';
$direccionCompleta = trim(($cfg['direccionCentro'] ?? '') . ', ' . ($cfg['cpCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? ''), ', ');
$urlMaps = $direccionCompleta !== ''
    ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($direccionCompleta)
    : '';
?>
<section class="lp-sec lp-contacto" id="contacto">
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['texto'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['texto'])) ?></p>
      <?php endif; ?>
    </div>

    <div class="lp-contacto-grid<?= $mostrarFormulario ? '' : ' lp-contacto-solo-datos' ?>">
      <div class="lp-contacto-datos">
        <?php if ($direccionCompleta !== ''): ?>
        <div class="lp-contacto-dato">
          <i class="fas fa-location-dot"></i>
          <div>
            <strong>Dirección</strong>
            <span><?= Security::escapeHtml($direccionCompleta) ?></span>
            <?php if ($urlMaps): ?>
            <a href="<?= Security::escapeHtml($urlMaps) ?>" target="_blank" rel="noopener" class="lp-enlace-suave">Cómo llegar <i class="fas fa-arrow-up-right-from-square"></i></a>
            <?php endif; ?>
          </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($cfg['telefonoCentro'])): ?>
        <div class="lp-contacto-dato">
          <i class="fas fa-phone"></i>
          <div>
            <strong>Teléfono</strong>
            <a href="tel:<?= Security::escapeHtml(preg_replace('/\s+/', '', $cfg['telefonoCentro'])) ?>"><?= Security::escapeHtml($cfg['telefonoCentro']) ?></a>
          </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($cfg['emailCentro'])): ?>
        <div class="lp-contacto-dato">
          <i class="fas fa-envelope"></i>
          <div>
            <strong>Email</strong>
            <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>"><?= Security::escapeHtml($cfg['emailCentro']) ?></a>
          </div>
        </div>
        <?php endif; ?>
        <?php if (!empty($contenido['textoHorario'])): ?>
        <div class="lp-contacto-dato">
          <i class="fas fa-clock"></i>
          <div>
            <strong>Horario</strong>
            <span><?= nl2br(Security::escapeHtml($contenido['textoHorario'])) ?></span>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($mostrarFormulario): ?>
      <form class="lp-contacto-form" id="lp-form-contacto" method="POST" action="/controladores/contacto_centro.php" novalidate>
        <input type="text" name="website" tabindex="-1" autocomplete="off" class="lp-honeypot" aria-hidden="true">
        <div class="lp-form-fila">
          <label>Nombre *<input type="text" name="nombre" required maxlength="100"></label>
          <label>Email *<input type="email" name="email" required maxlength="150"></label>
        </div>
        <label>Teléfono<input type="tel" name="telefono" maxlength="20"></label>
        <label>Mensaje *<textarea name="mensaje" rows="4" required maxlength="2000"></textarea></label>
        <button type="submit" class="lp-boton-primario lp-boton-grande">Enviar mensaje</button>
        <p class="lp-form-aviso" id="lp-form-aviso" role="status"></p>
      </form>
      <?php endif; ?>
    </div>
  </div>
</section>
