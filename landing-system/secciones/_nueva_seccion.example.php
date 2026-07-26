<?php
// ════════════════════════════════════════════════════════════════════════
// SECCIÓN DE EJEMPLO — landing-system/secciones/_nueva_seccion.example.php
// ════════════════════════════════════════════════════════════════════════
// Copia este archivo con el nombre de tu sección (ej: galeria.php).
// La variable $contenido viene resuelta por el motor antes de incluir este archivo.
//
// PASOS para registrar una nueva sección:
//   1. Crea este archivo: landing-system/secciones/galeria.php
//   2. Define los campos en engine/secciones.php dentro de landing_tipos()
//   3. El constructor la mostrará automáticamente en la lista de secciones añadibles.
// ════════════════════════════════════════════════════════════════════════

// Ejemplo: acceder al contenido guardado en la base de datos
$titulo = $contenido['titulo'] ?? 'Nuestra galería';
$items  = $contenido['items']  ?? [];
?>

<section class="lp-sec lp-galeria" id="galeria"<?= $styleInline ?? '' ?>>
  <div class="lp-contenedor">
    <h2 class="lp-titulo-sec"<?= landing_lb_field($preview, 'titulo') ?>><?= Security::escapeHtml($titulo) ?></h2>

    <?php if (!empty($items)): ?>
    <div class="lp-galeria-grid">
      <?php foreach ($items as $i => $item): ?>
      <div class="lp-galeria-item">
        <?php if (!empty($item['imagen'])): ?>
        <!-- Campo de tipo "imagen" -> kind 'imagen'; los campos de lista usan
             la ruta "items.<indice>.<subcampo>" para identificar el item. -->
        <img loading="lazy" src="<?= Security::escapeHtml(landing_img_url($item['imagen'])) ?>"
             alt="<?= Security::escapeHtml($item['alt'] ?? '') ?>"<?= landing_lb_field($preview, "items.$i.imagen", 'imagen') ?>>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
