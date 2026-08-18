<?php
$variante = $contenido['variante'] ?? 'lista';
$items = $contenido['items'] ?? [];
if (!$items) return;
?>
<section class="lp-sec lp-faq lp-faq-<?= Security::escapeHtml($variante) ?>" id="faq"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor lp-faq-inner">
    <div class="lp-sec-cabecera">
      <h2<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($contenido['titulo'] ?? '') ?></h2>
    </div>
    <div class="lp-faq-lista">
      <?php foreach ($items as $i => $item): ?>
      <?php if ($variante === 'acordeon'): ?>
      <details class="lp-faq-item">
        <summary>
          <span<?= landing_lb_field($preview, "items.$i.pregunta") ?>><?= Security::escapeHtml($item['pregunta'] ?? '') ?></span>
        </summary>
        <p<?= landing_lb_field($preview, "items.$i.respuesta", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['respuesta'] ?? '')) ?></p>
      </details>
      <?php else: ?>
      <div class="lp-faq-item">
        <h3<?= landing_lb_field($preview, "items.$i.pregunta") ?>><?= Security::escapeHtml($item['pregunta'] ?? '') ?></h3>
        <p<?= landing_lb_field($preview, "items.$i.respuesta", 'textarea') ?>><?= nl2br(Security::escapeHtml($item['respuesta'] ?? '')) ?></p>
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
<script type="application/ld+json"><?= Security::jsonEncodeSafe($faqSchema) ?></script>
