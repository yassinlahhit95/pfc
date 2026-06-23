<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

$exito  = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante        = $_SESSION['idEstudiante'];
$estudianteActual    = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios       = listarAnunciosPorRol('estudiantes');
$listaEventosProximos = listarEventosProximos();

$tfgActual  = obtenerTFGporEstudiante($idEstudiante);
$califTFG   = obtenerCalificacionTFG($idEstudiante);

$idCiclo         = $estudianteActual['idCiclo'] ?? 0;
$listaModulos    = listarModulosPorCiclo($idCiclo);
$listaRetos      = listarRetosPorCiclo($idCiclo);
$califModulos    = listarCalificacionesPorEstudiante($idEstudiante);
$califRetos      = listarCalificacionesRetoPorEstudiante($idEstudiante);
$cantidadPagos   = contarPagosEstudiante($idEstudiante);
$listaMensajes   = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = 'AulaPro — Panel de Control';
$seccionActual   = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";

// Spanish date
$dias   = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
$meses  = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
$eyebrow = $dias[date('w')] . ', ' . date('j') . ' de ' . $meses[date('n')-1];

// Time-aware greeting
$hora   = (int)date('H');
$saludo = $hora < 12 ? 'Buenos días' : ($hora < 19 ? 'Buenas tardes' : 'Buenas noches');
$nombreEst = $estudianteActual['nombreEstudiante'] ?? '';
$nombreCiclo = $estudianteActual['nombreCiclo'] ?? '';

// TFG status
$tfgEstado = $califTFG ? $califTFG['nota'] : (empty($tfgActual['archivoTFG']) ? 'Pendiente' : 'Subido');

// Arrow SVG
$arrowSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>


<section class="hero">
  <div class="hero-text">
    <p class="eyebrow"><?= Security::escapeHtml($eyebrow) ?></p>
    <h1><?= Security::escapeHtml($saludo) ?>, <span><?= Security::escapeHtml($nombreEst) ?></span></h1>
    <p class="sub"><?= Security::escapeHtml($nombreCiclo) ?> — <b><?= count($listaModulos) ?> módulos</b> · <b><?= count($listaRetos) ?> retos</b> · <b><?= $cantidadPagos ?> pagos</b></p>
  </div>
  <div class="hero-stats">
    <div class="stat"><span class="stat-k">Módulos</span><span class="stat-v"><?= count($listaModulos) ?></span></div>
    <div class="stat"><span class="stat-k">Retos</span><span class="stat-v"><?= count($listaRetos) ?></span></div>
    <div class="stat"><span class="stat-k">TFG</span><span class="stat-v"><?= Security::escapeHtml($tfgEstado) ?></span></div>
    <div class="stat"><span class="stat-k">Pagos</span><span class="stat-v"><?= $cantidadPagos ?></span></div>
  </div>
</section>

<div class="section-head">
  <h2>Mi portal</h2>
  <span class="count">Acceso rápido</span>
</div>

<section class="dash-grid">

  <a href="../retos/lista.php" class="tile card-soft" style="--tint:#8B5CF6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
      <?php if (count($listaRetos) > 0) { ?><span class="tile-badge"><?= count($listaRetos) ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Retos</span>
      <span class="tile-desc">Mis proyectos y desafíos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaRetos) ?> retos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../calificaciones/lista.php" class="tile card-soft" style="--tint:#14B8A6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Calificaciones</span>
      <span class="tile-desc">Mis notas y resultados</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaModulos) ?> módulos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../pfc/subir.php" class="tile card-soft" style="--tint:#D946EF; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mi TFG</span>
      <span class="tile-desc">Proyecto Final de Grado</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= Security::escapeHtml($tfgEstado) ?></span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../horario/horario.php" class="tile card-soft" style="--tint:#4F46E5; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Horario</span>
      <span class="tile-desc">Mi cuadro horario</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Ver horario</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../aula/sesiones.php" class="tile card-soft" style="--tint:#06B6D4; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Aula Digital</span>
      <span class="tile-desc">Sesiones y materiales</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Acceder</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../mensajes/lista.php" class="tile card-soft" style="--tint:#F43F5E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      <?php if (count($listaMensajes) > 0) { ?><span class="tile-badge"><?= count($listaMensajes) ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mensajería</span>
      <span class="tile-desc">Comunicación con el centro</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaMensajes) ?> mensajes</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../pagos/lista.php" class="tile card-soft" style="--tint:#22C55E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
      <?php if ($cantidadPagos > 0) { ?><span class="tile-badge"><?= $cantidadPagos ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Pagos</span>
      <span class="tile-desc">Historial de pagos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $cantidadPagos ?> pagos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../eventos/lista.php" class="tile card-soft" style="--tint:#F97316; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Eventos</span>
      <span class="tile-desc">Próximos eventos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Ver eventos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

</section>

<!-- Announcements + Events panels -->
<div class="dash-panels">
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Anuncios</h3>
      <a href="../anuncios/lista.php">Ver todos</a>
    </div>
    <div class="dash-panel-body">
      <?php if (!empty($listaAnuncios)) {
        $cnt = 0;
        foreach ($listaAnuncios as $anuncio) {
          if ($cnt >= 4) break; ?>
          <div class="ann-item">
            <div class="ann-item-head">
              <span class="ann-item-title"><?= Security::escapeHtml(strtoupper($anuncio['tituloAnuncio'])) ?></span>
              <span class="ann-item-date"><?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></span>
            </div>
            <p class="ann-item-body"><?= Security::escapeHtml(substr(strip_tags($anuncio['contenidoAnuncio']), 0, 120)) ?>…</p>
            <a href="../anuncios/lista.php" class="ann-item-tag">Ver detalles</a>
          </div>
      <?php $cnt++; } } else { ?>
        <p class="empty-state">No hay anuncios activos actualmente.</p>
      <?php } ?>
    </div>
  </div>

  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Próximos Eventos</h3>
      <a href="../eventos/lista.php">Ver todos</a>
    </div>
    <div class="dash-panel-body">
      <?php if (!empty($listaEventosProximos)) {
        $cnt = 0;
        foreach ($listaEventosProximos as $evento) {
          if ($cnt >= 4) break;
          $dia = date('d', strtotime($evento['fechaEvento']));
          $mes = strtoupper(date('M', strtotime($evento['fechaEvento']))); ?>
          <div class="evt-item">
            <div class="evt-date-box">
              <span class="evt-day"><?= $dia ?></span>
              <span class="evt-mon"><?= $mes ?></span>
            </div>
            <div class="evt-info">
              <span class="evt-title"><?= Security::escapeHtml(strtoupper($evento['tituloEvento'])) ?></span>
              <span class="evt-meta"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h · <?= Security::escapeHtml($evento['ubicacionEvento']) ?></span>
            </div>
          </div>
      <?php $cnt++; } } else { ?>
        <p class="empty-state">No hay eventos próximos.</p>
      <?php } ?>
    </div>
  </div>
</div>

<?php
// Build chart data — only modules/retos that have at least one grade recorded
$chartLabels   = [];
$chartNotas    = [];
$chartColores  = [];
foreach ($califModulos as $cm) {
    $nota = $cm['nota_2final'] ?? $cm['nota_1final'] ?? null;
    if ($nota === null) continue;
    $nota            = (float)$nota;
    $chartLabels[]   = $cm['nombreModulo'];
    $chartNotas[]    = $nota;
    $chartColores[]  = $nota >= 5 ? 'rgba(22,163,74,0.75)' : 'rgba(239,68,68,0.75)';
}

$chartRetosLabels   = [];
$chartRetosNotas    = [];
$chartRetosColores  = [];
foreach ($califRetos as $cr) {
    $nota                = (float)$cr['nota'];
    $chartRetosLabels[]  = $cr['nombreReto'];
    $chartRetosNotas[]   = $nota;
    $chartRetosColores[] = $nota >= 5 ? 'rgba(14,165,233,0.75)' : 'rgba(239,68,68,0.75)';
}
?>
<?php if (!empty($chartLabels) || !empty($chartRetosLabels)) { ?>
<div class="dash-panel" style="margin-top:var(--gap);">
    <div class="dash-panel-head"><h3>Historial de Calificaciones</h3></div>
    <div style="display:grid;grid-template-columns:<?= !empty($chartLabels) && !empty($chartRetosLabels) ? '1fr 1fr' : '1fr' ?>;gap:24px;padding:16px 20px;">
        <?php if (!empty($chartLabels)) { ?>
        <div>
            <p style="font-size:.8rem;font-weight:700;color:var(--mut);margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em;">Módulos</p>
            <canvas id="chartModulos" height="220"></canvas>
        </div>
        <?php } ?>
        <?php if (!empty($chartRetosLabels)) { ?>
        <div>
            <p style="font-size:.8rem;font-weight:700;color:var(--mut);margin-bottom:8px;text-transform:uppercase;letter-spacing:.04em;">Retos</p>
            <canvas id="chartRetos" height="220"></canvas>
        </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    var optsBase = {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 10, ticks: { stepSize: 1 },
                 grid: { color: 'rgba(0,0,0,.05)' } },
            x: { ticks: { font: { size: 11 }, maxRotation: 30 } }
        }
    };

    <?php if (!empty($chartLabels)) { ?>
    new Chart(document.getElementById('chartModulos'), {
        type: 'bar',
        data: {
            labels:   <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{ data: <?= json_encode($chartNotas) ?>,
                         backgroundColor: <?= json_encode($chartColores) ?>,
                         borderRadius: 6, borderSkipped: false }]
        },
        options: optsBase
    });
    <?php } ?>

    <?php if (!empty($chartRetosLabels)) { ?>
    new Chart(document.getElementById('chartRetos'), {
        type: 'bar',
        data: {
            labels:   <?= json_encode($chartRetosLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{ data: <?= json_encode($chartRetosNotas) ?>,
                         backgroundColor: <?= json_encode($chartRetosColores) ?>,
                         borderRadius: 6, borderSkipped: false }]
        },
        options: optsBase
    });
    <?php } ?>
})();
</script>
<?php } ?>

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
