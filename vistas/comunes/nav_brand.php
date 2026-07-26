<?php
// Bloque de marca del sidebar (logo + nombre del centro + botón colapsar +
// botón cerrar en móvil) — idéntico en los 5 nav.php de cada rol salvo el
// subtítulo bajo el nombre. Antes se mantenía una copia manual en cada uno;
// eso es exactamente cómo el botón de cerrar en móvil (#mobile-close) acabó
// existiendo solo en el de admin y faltando en los otros 4 — nadie estaba
// "actualizando el sidebar", estaban actualizando "el sidebar de admin" y
// los otros cuatro se quedaban atrás sin que nadie lo notara. Requiere que
// el nav.php que lo incluye defina $navBrandSubtitle antes del include.
require_once __DIR__ . '/../../include/FeatureGuard.php';
$__navBrandSubtitle = $navBrandSubtitle ?? '';
?>
<div class="brand">
  <div class="brand-mark"><span></span></div>
  <div class="brand-text"><strong><?= Security::escapeHtml(FeatureGuard::getCenterName()) ?></strong><small><?= Security::escapeHtml($__navBrandSubtitle) ?></small></div>
  <button class="collapse-btn" id="collapse" aria-label="Contraer menú">
    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
  </button>
  <button class="mobile-close-btn" id="mobile-close" aria-label="Cerrar menú">
    <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
  </button>
</div>
