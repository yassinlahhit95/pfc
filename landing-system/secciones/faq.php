<?php
// Preguntas frecuentes (acordeón nativo <details>).
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-faq" id="faq">
  <div class="lp-contenedor lp-faq-inner">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
    </div>
    <div class="lp-faq-lista">
      <?php foreach ($items as $item): ?>
      <details class="lp-faq-item">
        <summary>
          <?= Security::escapeHtml($item['pregunta'] ?? '') ?>
          <i class="fas fa-chevron-down"></i>
        </summary>
        <p><?= nl2br(Security::escapeHtml($item['respuesta'] ?? '')) ?></p>
      </details>
      <?php endforeach; ?>
    </div>
  </div>
</section>
