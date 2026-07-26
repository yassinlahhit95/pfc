<?php
// Oferta formativa: tarjetas gestionadas manualmente desde el constructor
// (título, texto, foto y precio libres por tarjeta) — no dependen de los
// ciclos formativos académicos (tabla `ciclos`), que son una entidad aparte.
// Cada tarjeta puede enlazar opcionalmente (campo cicloSlug) a una ficha del
// catálogo público de ciclos (tabla landing_ciclos, /vistas/ciclos.php); si
// no se rellena, el botón sigue usando botonUrl o el enlace general de siempre.
$items          = $contenido['items'] ?? [];
$prematriculaOn = FeatureGuard::check('feature_prematricula');
$botonTexto     = $contenido['botonTexto'] ?: 'Solicitar plaza';
$variante       = $contenido['variante'] ?? 'grid';
$columnas       = in_array($contenido['columnas'] ?? 'cols-4', ['cols-3', 'cols-4'], true) ? $contenido['columnas'] : 'cols-4';
$enlaceDefecto  = $prematriculaOn ? '/vistas/admisiones/pre-matricula.php' : '#contacto';
?>
<section class="lp-sec lp-oferta lp-variante-<?= Security::escapeHtml($variante) ?> lp-oferta-<?= Security::escapeHtml($columnas) ?>" id="oferta_formativa"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
      <?php if (!empty($contenido['subtitulo'])): ?>
      <p<?= landing_lb_field($preview, 'subtitulo', 'textarea') ?>><?= nl2br(Security::escapeHtml($contenido['subtitulo'])) ?></p>
      <?php endif; ?>
    </div>

    <?php if (empty($items)): ?>
    <p class="lp-vacio">Próximamente publicaremos nuestra oferta de ciclos formativos.</p>
    <?php else: ?>
    <div class="lp-oferta-grid">
      <?php foreach ($items as $i => $item):
          $imgUrl = landing_img_url($item['imagen'] ?? '');
          $cicloSlug = trim($item['cicloSlug'] ?? '');
          $enlace = $cicloSlug !== ''
              ? '/vistas/ciclos.php?ciclo=' . rawurlencode($cicloSlug)
              : landing_url_segura($item['botonUrl'] ?? '', $enlaceDefecto);
      ?>
      <article class="lp-tarjeta lp-ciclo">
        <?php if ($imgUrl): ?>
        <div class="lp-ciclo-foto"><img src="<?= Security::escapeHtml($imgUrl) ?>" alt="" loading="lazy"<?= landing_lb_field($preview, "items.$i.imagen", 'imagen') ?>></div>
        <?php endif; ?>
        <div class="lp-ciclo-cuerpo">
          <?php if (!empty($item['etiqueta'])): ?>
          <span class="lp-badge lp-ciclo-nivel"<?= landing_lb_field($preview, "items.$i.etiqueta") ?>><?= Security::escapeHtml($item['etiqueta']) ?></span>
          <?php endif; ?>
          <h3<?= landing_lb_field($preview, "items.$i.titulo") ?>><?= Security::escapeHtml($item['titulo'] ?? '') ?></h3>
          <?php if (!empty($item['texto'])): ?>
          <p class="lp-ciclo-texto"<?= landing_lb_field($preview, "items.$i.texto", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['texto'])) ?></p>
          <?php endif; ?>
          <?php if (!empty($item['precio'])): ?>
          <p class="lp-ciclo-precio"<?= landing_lb_field($preview, "items.$i.precio") ?>><?= Security::escapeHtml($item['precio']) ?></p>
          <?php endif; ?>
          <a class="lp-boton-primario lp-ciclo-boton" href="<?= Security::escapeHtml($enlace) ?>">
            <span<?= landing_lb_field($preview, 'botonTexto') ?>><?= Security::escapeHtml($botonTexto) ?></span>
          </a>
        </div>
      </article>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php
// Datos estructurados Course — uno por ciclo con título, para el mismo tipo
// de rich result que ya usa la sección FAQ. Se agrupan en un único bloque
// @graph en vez de un <script> por ciclo (varios bloques ld+json sueltos
// también son válidos, pero esto evita repetir @context en cada uno).
$cursosSchema = [];
foreach ($items as $item) {
    $tituloCurso = trim($item['titulo'] ?? '');
    if ($tituloCurso === '') continue;
    $cursosSchema[] = [
        '@type'       => 'Course',
        'name'        => $tituloCurso,
        'description' => trim($item['texto'] ?? '') ?: $tituloCurso,
        'provider'    => [
            '@type' => 'EducationalOrganization',
            'name'  => $cfg['nombreCentro'] ?? '',
        ],
    ];
}
if ($cursosSchema):
?>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@graph' => $cursosSchema], JSON_UNESCAPED_UNICODE) ?></script>
<?php endif; ?>
