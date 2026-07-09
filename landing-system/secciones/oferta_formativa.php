<?php
// Oferta formativa: los ciclos salen de la BD (solo activos), no del contenido.
require_once __DIR__ . '/../../modelos/ciclos.php';
$ciclosLanding  = listarTodosLosCiclos();
$prematriculaOn = FeatureGuard::check('feature_prematricula');
$mostrarPrecio  = ($contenido['mostrarPrecio'] ?? 'no') === 'si';
$botonTexto     = $contenido['botonTexto'] ?: 'Solicitar plaza';
$variante       = $contenido['variante'] ?? 'grid';
?>
<section class="lp-sec lp-oferta lp-variante-<?= Security::escapeHtml($variante) ?>" id="oferta_formativa"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>

    <?php if (empty($ciclosLanding)): ?>
    <p class="lp-vacio">Próximamente publicaremos nuestra oferta de ciclos formativos.</p>
    <?php else: ?>
    <div class="lp-oferta-grid">
      <?php foreach ($ciclosLanding as $ciclo): ?>
      <article class="lp-tarjeta lp-ciclo">
        <span class="lp-ciclo-nivel"><?= Security::escapeHtml($ciclo['nombreNivel'] ?? '') ?></span>
        <h3><?= Security::escapeHtml($ciclo['nombreCiclo'] ?? '') ?></h3>
        <?php if (!empty($ciclo['abreviaturaCiclo'])): ?>
        <p class="lp-ciclo-abrev"><?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?></p>
        <?php endif; ?>
        <?php if ($mostrarPrecio && !empty($ciclo['precioCiclo'])): ?>
        <p class="lp-ciclo-precio"><?= Security::escapeHtml(number_format((float)$ciclo['precioCiclo'], 0, ',', '.')) ?> €<span>/curso</span></p>
        <?php endif; ?>
        <a class="lp-boton-primario lp-ciclo-boton"
           href="<?= $prematriculaOn ? '/vistas/admisiones/pre-matricula.php' : '#contacto' ?>">
          <?= Security::escapeHtml($botonTexto) ?>
        </a>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>




