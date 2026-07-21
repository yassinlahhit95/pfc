<?php
$variante = $contenido['variante'] ?? 'lista';
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-faq lp-faq-<?= Security::escapeHtml($variante) ?>" id="faq"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-faq-inner">
    <div class="lp-sec-cabecera">
      <h2><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
    </div>
    <div class="lp-faq-lista">
      <?php foreach ($items as $item): ?>
      <?php if ($variante === 'acordeon'): ?>
      <details class="lp-faq-item">
        <summary>
          <?= Security::escapeHtml($item['pregunta'] ?? '') ?>
        </summary>
        <p><?= nl2br(Security::escapeHtml($item['respuesta'] ?? '')) ?></p>
      </details>
      <?php else: ?>
      <div class="lp-faq-item">
        <h3><?= Security::escapeHtml($item['pregunta'] ?? '') ?></h3>
        <p><?= nl2br(Security::escapeHtml($item['respuesta'] ?? '')) ?></p>
      </div>
      <?php endif; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php
// Datos estructurados FAQPage — habilita el rich snippet de preguntas
// desplegables de Google para esta sección.
$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(function ($item) {
        return [
            '@type'          => 'Question',
            'name'           => $item['pregunta'] ?? '',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => $item['respuesta'] ?? '',
            ],
        ];
    }, $items),
];
?>
<script type="application/ld+json"><?= json_encode($faqSchema, JSON_UNESCAPED_UNICODE) ?></script>
