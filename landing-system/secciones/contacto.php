<?php
// Sección contacto
$mostrarFormulario = ($contenido['mostrarFormulario'] ?? 'si') === 'si';
$mostrarMapa = ($contenido['mostrarMapa'] ?? 'no') === 'si';
$iframeMapa = $contenido['iframeMapa'] ?? '';

// Determine layout classes based on what's shown
$gridClass = 'lp-contacto-grid';
if (!$mostrarFormulario && !$mostrarMapa) {
    $gridClass = 'lp-contacto-solo-datos';
} elseif ($mostrarMapa && $mostrarFormulario) {
    $gridClass = 'lp-contacto-grid lp-contacto-mapa-form';
}
?>
<section class="lp-sec lp-contacto" id="contacto"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera lp-anim">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['texto'])): ?>
      <p<?= landing_lb_field($preview, 'texto', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['texto'])) ?></p>
      <?php endif; ?>
    </div>

    <div class="<?= $gridClass ?> lp-anim">
      <div class="lp-contacto-col-izquierda">
        <div class="lp-contacto-datos">
          <div class="lp-contacto-dato">
            <i class="fas fa-location-dot"></i>
            <div>
              <strong>Dirección</strong>
              <span>
                <?= Security::escapeHtml($cfg['direccionCentro'] ?? '') ?><br>
                <?= Security::escapeHtml(($cfg['cpCentro'] ?? '') . ' ' . ($cfg['ciudadCentro'] ?? '')) ?>
              </span>
            </div>
          </div>

          <div class="lp-contacto-dato">
            <i class="fas fa-phone"></i>
            <div>
              <strong>Teléfono</strong>
              <?php if (!empty($cfg['telefonoCentro'])): ?>
              <a href="tel:<?= Security::escapeHtml(str_replace(' ', '', $cfg['telefonoCentro'])) ?>">
                <?= Security::escapeHtml($cfg['telefonoCentro']) ?>
              </a>
              <?php else: ?>
              <span>No disponible</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="lp-contacto-dato">
            <i class="fas fa-envelope"></i>
            <div>
              <strong>Correo electrónico</strong>
              <?php if (!empty($cfg['emailCentro'])): ?>
              <a href="mailto:<?= Security::escapeHtml($cfg['emailCentro']) ?>">
                <?= Security::escapeHtml($cfg['emailCentro']) ?>
              </a>
              <?php else: ?>
              <span>No disponible</span>
              <?php endif; ?>
            </div>
          </div>

          <?php if (!empty($contenido['textoHorario'])): ?>
          <div class="lp-contacto-dato">
            <i class="fas fa-clock"></i>
            <div>
              <strong>Horario</strong>
              <span<?= landing_lb_field($preview, 'textoHorario', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['textoHorario'])) ?></span>
            </div>
          </div>
          <?php endif; ?>

        </div>

        <?php if ($mostrarMapa && !empty($iframeMapa)): ?>
        <div class="lp-contacto-mapa">
          <!-- Se muestra sin escapar a propósito: es un <iframe> ya reconstruido
               y validado por _landing_sanear_iframe_mapa() (solo Google Maps
               embed, atributos propios descartados) al guardar en el constructor. -->
          <?= $iframeMapa ?>
        </div>
        <?php endif; ?>
      </div>

      <?php if ($mostrarFormulario): ?>
      <form class="lp-contacto-form" id="lp-form-contacto" method="POST" action="/controladores/contacto_centro.php" novalidate>
        <!-- Campo honeypot -->
        <input type="text" name="website" class="lp-honeypot" tabindex="-1" autocomplete="off" aria-hidden="true">

        <div class="lp-form-fila">
          <label>Nombre *<input type="text" name="nombre" required maxlength="100"></label>
          <label>Email *<input type="email" name="email" required maxlength="150"></label>
        </div>
        <label>Teléfono<input type="tel" name="telefono" maxlength="20"></label>
        <label>Mensaje *<textarea name="mensaje" rows="4" required maxlength="2000"></textarea></label>
        <button type="submit" class="lp-boton-primario lp-boton-grande">Enviar mensaje <i class="fas fa-paper-plane"></i></button>
        <p class="lp-form-aviso" id="lp-form-aviso" role="status"></p>
      </form>
      <?php endif; ?>

    </div>
  </div>
</section>
