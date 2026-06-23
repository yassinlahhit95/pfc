<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
$titulo_pagina = 'AulaPro Familias — Panel Principal';
$seccion       = 'inicio';
include __DIR__ . '/../comunes/nav.php';

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$arrowSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>

<div class="hero">
  <div class="hero-text">
    <div class="eyebrow">Bienvenido/a</div>
    <h1>Portal de <span>Familias</span></h1>
    <p class="sub">Desde aquí puede realizar el seguimiento académico de sus hijos y gestionar trámites administrativos.</p>
  </div>
</div>

<div class="grid">
  <?php foreach ($hijos as $hijo): ?>
    <a href="../estudiantes/expediente.php?id=<?= (int)$hijo['idEstudiante'] ?>" class="tile card-soft" style="--tint:#4F46E5; text-decoration:none">
      <span class="tile-sheen"></span>
      <span class="tile-ico">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </span>
      <span class="tile-body">
        <span class="tile-label"><?= Security::escapeHtml($hijo['nombreEstudiante']) ?></span>
        <span class="tile-desc"><?= Security::escapeHtml($hijo['nombreCiclo']) ?></span>
      </span>
      <span class="tile-foot">
        <span class="tile-stat">Ver Expediente</span>
        <span class="tile-go"><?= $arrowSvg ?></span>
      </span>
    </a>
  <?php endforeach; ?>

  <a href="../pagos/misPagos.php" class="tile card-soft" style="--tint:#10B981; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Pagos y Recibos</span>
      <span class="tile-desc">Gestión económica</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Consultar</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../mensajes/chat.php" class="tile card-soft" style="--tint:#F59E0B; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mensajería</span>
      <span class="tile-desc">Contactar con el centro</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Ir al chat</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script>
if (window.gsap && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  var factor = ((window.TWEAK_DEFAULTS && window.TWEAK_DEFAULTS.animation) || 7) / 10;
  gsap.fromTo('.tile',
    { opacity: 0, y: 24 + 30 * factor, scale: 0.96 },
    { opacity: 1, y: 0, scale: 1, duration: 0.5 + 0.35 * factor, ease: 'power3.out',
      stagger: { each: 0.04, from: 'start' }, clearProps: 'transform,opacity',
      delay: 0.3 });
}
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
