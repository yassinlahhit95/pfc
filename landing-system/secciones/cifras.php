<?php
// Cifras clave con contador animado (landing.js anima [data-contador]).
// Variantes: horizontal, tarjetas, minimalista, foto (imagen de fondo).
$items = $contenido['items'] ?? [];
$variante = $contenido['variante'] ?? 'horizontal';
if (!$items) return;

$imgUrl = landing_img_url($contenido['imagen'] ?? '');
if ($variante === 'foto' && !$imgUrl) $variante = 'horizontal';

// Misma técnica que hero.php: el background-image se añade al style="" en
// vez de usar $styleInline directamente, porque hay que combinarlo con el
// resto de overrides de estilo (estilo_fondo/estilo_texto/...) en un único
// atributo style.
$estiloLocal = $styleStr ?? '';
if ($variante === 'foto' && $imgUrl) {
    $estiloLocal .= 'background-image:url(\'' . Security::escapeHtml($imgUrl) . '\');';
}
$estiloFondo = $estiloLocal !== '' ? ' style="' . $estiloLocal . '"' : '';
if (!empty($preview) && isset($s['idSeccion'])) {
    $estiloFondo .= ' data-lb-id="' . Security::escapeHtml($s['idSeccion']) . '"';
}
// El campo "imagen" se marca en la propia sección (fondo a pantalla completa),
// no en un <img> — un click en cualquier hueco no ocupado por una cifra
// concreta abre el reemplazo de foto; un click en un número/etiqueta edita
// ese campo, porque closest('[data-lb-field]') prioriza siempre el nodo más
// cercano al punto de click.
if ($variante === 'foto') {
    $estiloFondo .= landing_lb_field($preview, 'imagen', 'imagen');
}
?>
<section class="lp-sec lp-cifras lp-variante-<?= Security::escapeHtml($variante) ?>" id="cifras"<?= $estiloFondo ?>>
  <div class="lp-contenedor lp-cifras-grid">
    <?php foreach ($items as $i => $item): ?>
    <div class="lp-cifra">
      <span class="lp-cifra-numero">
        <span data-contador="<?= Security::escapeHtml($item['numero'] ?? '0') ?>"<?= landing_lb_field($preview, "items.$i.numero") ?>><?= Security::escapeHtml($item['numero'] ?? '0') ?></span><?= Security::escapeHtml($item['sufijo'] ?? '') ?>
      </span>
      <span class="lp-cifra-etiqueta"<?= landing_lb_field($preview, "items.$i.etiqueta") ?>><?= Security::escapeHtml($item['etiqueta'] ?? '') ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>
