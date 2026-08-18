<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../modelos/asistencias.php';
require_once __DIR__ . '/../../../modelos/justificacionesFalta.php';
require_once __DIR__ . '/../../../modelos/pagos.php';
$titulo_pagina = 'Aulapro Familias — Panel Principal';
$seccion       = 'inicio';
include __DIR__ . '/../comunes/nav.php';

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$arrowSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

// ── Avisos que necesitan atención: faltas sin justificar, pagos pendientes, mensajes sin leer ──
$avisos = [];
foreach ($hijos as $hijo) {
    $nombreCorto = explode(' ', $hijo['nombreEstudiante'])[0];

    $sinJustificar = contarFaltasSinJustificarPorEstudiante((int)$hijo['idEstudiante']);
    if ($sinJustificar > 0) {
        $avisos[] = [
            'tipo'  => 'rojo',
            'icono' => 'fa-calendar-xmark',
            'texto' => $sinJustificar . ' falta' . ($sinJustificar !== 1 ? 's' : '') . ' sin justificar — ' . $nombreCorto,
            'url'   => '../asistencias/lista.php?id=' . (int)$hijo['idEstudiante'],
        ];
    }

    if (FeatureGuard::check('feature_pagos')) {
        $estadoFinan = obtenerEstadoFinancieroEstudiante((int)$hijo['idEstudiante']);
        $pendiente   = $estadoFinan['restante'] ?? 0;
        if ($pendiente > 0) {
            $avisos[] = [
                'tipo'  => 'naranja',
                'icono' => 'fa-credit-card',
                'texto' => 'Pago pendiente de ' . number_format($pendiente, 2) . ' € — ' . $nombreCorto,
                'url'   => '../pagos/misPagos.php',
            ];
        }
    }
}
if (FeatureGuard::check('feature_chat') && $totalChatNoLeidos_menu > 0) {
    $avisos[] = [
        'tipo'  => 'azul',
        'icono' => 'fa-comment-dots',
        'texto' => $totalChatNoLeidos_menu . ' mensaje' . ($totalChatNoLeidos_menu !== 1 ? 's' : '') . ' sin leer',
        'url'   => '../mensajes/chat.php',
    ];
}
?>

<?php if (!empty($avisos)): ?>
<div class="aviso-atencion">
  <?php foreach ($avisos as $aviso): ?>
    <a href="<?= Security::escapeHtml($aviso['url']) ?>" class="aviso-chip aviso-chip--<?= $aviso['tipo'] ?>">
      <i class="fas <?= Security::escapeHtml($aviso['icono']) ?>"></i>
      <span><?= Security::escapeHtml($aviso['texto']) ?></span>
    </a>
  <?php endforeach; ?>
</div>
<?php else: ?>
<div class="aviso-atencion">
  <span class="aviso-chip aviso-chip--verde">
    <i class="fas fa-circle-check"></i>
    <span>Todo al día — sin avisos pendientes</span>
  </span>
</div>
<?php endif; ?>

<div class="hero">
  <div class="hero-text">
    <div class="eyebrow">Bienvenido/a</div>
    <h1>Portal de <span>Familias</span></h1>
    <p class="sub">Desde aquí puede realizar el seguimiento académico de sus hijos y gestionar trámites administrativos.</p>
  </div>
</div>

<div class="grid">
  <?php foreach ($hijos as $hijo):
    $asist = contarResumenAsistencia((int)$hijo['idEstudiante']);
    $totalDias = array_sum($asist);
    $pctPresente = $totalDias > 0 ? round($asist['presente'] / $totalDias * 100) : 0;
  ?>
    <a href="../estudiantes/expediente.php?id=<?= (int)$hijo['idEstudiante'] ?>" class="tile card-soft" style="--tint:var(--accent); text-decoration:none">
      <span class="tile-sheen"></span>
      <span class="tile-ico">
        <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      </span>
      <span class="tile-body">
        <span class="tile-label"><?= Security::escapeHtml($hijo['nombreEstudiante']) ?></span>
        <span class="tile-desc"><?= Security::escapeHtml($hijo['nombreCiclo']) ?></span>
        <?php if ($totalDias > 0): ?>
        <span class="tile-asist">
          <span class="asist-dot asist-dot--<?= $asist['ausente'] > 0 ? 'warn' : 'ok' ?>"></span>
          Asistencia: <?= $pctPresente ?>% &middot; <?= $asist['ausente'] ?> ausencia<?= $asist['ausente'] !== 1 ? 's' : '' ?>
          <?php if ($asist['retraso'] > 0): ?>&middot; <?= $asist['retraso'] ?> retraso<?= $asist['retraso'] !== 1 ? 's' : '' ?><?php endif; ?>
        </span>
        <?php endif; ?>
      </span>
      <span class="tile-foot">
        <span class="tile-stat">Ver Expediente</span>
        <span class="tile-go"><?= $arrowSvg ?></span>
      </span>
    </a>
  <?php endforeach; ?>

  <a href="../pagos/misPagos.php" class="tile card-soft" style="--tint:var(--verde); text-decoration:none">
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

  <a href="../mensajes/chat.php" class="tile card-soft" style="--tint:var(--naranja); text-decoration:none">
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

<style>
.tile-asist {
    display: block;
    font-size: .75rem;
    color: var(--dim);
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 5px;
}
.asist-dot {
    width: 7px; height: 7px;
    border-radius: 50%;
    flex-shrink: 0;
}
.asist-dot--ok   { background: var(--verde); }
.asist-dot--warn { background: var(--naranja); }
</style>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" integrity="sha384-g4NTh/Iv5PPU4xPyhEWqPcwtNXOvdaDI8LLnyYfyNZOjKJeYQyjzQ9X5275eBjpt" crossorigin="anonymous"></script>
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
