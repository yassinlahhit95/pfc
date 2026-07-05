<?php
// Cifras clave con contador animado (landing.js anima [data-contador]).
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-cifras" id="cifras">
  <div class="lp-contenedor lp-cifras-grid">
    <?php foreach ($items as $item): ?>
    <div class="lp-cifra">
      <span class="lp-cifra-numero">
        <span data-contador="<?= Security::escapeHtml($item['numero'] ?? '0') ?>"><?= Security::escapeHtml($item['numero'] ?? '0') ?></span><?= Security::escapeHtml($item['sufijo'] ?? '') ?>
      </span>
      <span class="lp-cifra-etiqueta"><?= Security::escapeHtml($item['etiqueta'] ?? '') ?></span>
    </div>
    <?php endforeach; ?>
  </div>
</section>
