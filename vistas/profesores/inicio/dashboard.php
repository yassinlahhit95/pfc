<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito  = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$profesorActual     = obtenerProfesorPorId($idProfesor);
$listaAnuncios      = listarTodosLosAnuncios();
$listaMensajes      = listarMensajesParaProfesor($idProfesor);
$listaEstudiantes   = listarEstudiantesDeProfesor($idProfesor);
$listaModulos       = listarModulosDeProfesor($idProfesor);
$listaRetos         = listarRetosDeProfesor($idProfesor);
$listaEventos       = listarEventosProximos();
$alumnosRiesgo      = obtenerAlumnosEnRiesgoPorProfesorAula($idProfesor);

$listaTFGsProfesor      = listarTFGsPorProfesor($idProfesor);
$totalTFGsProfesor      = count($listaTFGsProfesor);
$calificadosTFGsProfesor = 0;
foreach ($listaTFGsProfesor as $tfg) {
    if (obtenerCalificacionTFG($tfg['idEstudiante'])) {
        $calificadosTFGsProfesor++;
    }
}

$mensajesPendientes = 0;
foreach ($listaMensajes as $mensaje) {
    if ($mensaje['estadoReclamacion'] == 'pendiente') {
        $mensajesPendientes++;
    }
}

$titulo_pagina = 'Panel de Control';
$seccionActual   = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../include/dashboard_helpers.php";
$eyebrow = fechaLegibleHoy();
$saludo  = saludoHorario();
$nombreProf = $profesorActual['nombreProfesor'] ?? '';

// Arrow SVG
$arrowSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';

// Avisos que necesitan atención — antes solo se veían si se bajaba hasta la
// tabla de "Alumnos en Riesgo" (que además solo aparece si no está vacía);
// aquí arriba se resume todo lo accionable de un vistazo, como ya hace el
// portal de familias con su franja de avisos.
$tfgsSinCalificar = $totalTFGsProfesor - $calificadosTFGsProfesor;
$avisosProfesor = [];
if ($mensajesPendientes > 0) {
    $avisosProfesor[] = [
        'tipo' => 'rojo', 'icono' => 'fa-envelope',
        'texto' => $mensajesPendientes . ' mensaje' . ($mensajesPendientes !== 1 ? 's' : '') . ' sin resolver',
        'url' => '../mensajes/lista.php',
    ];
}
if (!empty($alumnosRiesgo)) {
    $avisosProfesor[] = [
        'tipo' => 'naranja', 'icono' => 'fa-triangle-exclamation',
        'texto' => count($alumnosRiesgo) . ' alumno' . (count($alumnosRiesgo) !== 1 ? 's' : '') . ' en riesgo en Aula Digital',
        'url' => '#alumnos-riesgo',
    ];
}
if ($tfgsSinCalificar > 0) {
    $avisosProfesor[] = [
        'tipo' => 'azul', 'icono' => 'fa-file-signature',
        'texto' => $tfgsSinCalificar . ' TFG' . ($tfgsSinCalificar !== 1 ? 's' : '') . ' sin calificar',
        'url' => '../calificaciones/lista.php',
    ];
}
?>


<section class="hero">
  <div class="hero-text">
    <p class="eyebrow"><?= Security::escapeHtml($eyebrow) ?></p>
    <h1><?= Security::escapeHtml($saludo) ?>, <span><?= Security::escapeHtml($nombreProf) ?></span></h1>
    <p class="sub">Tu área docente — <b><?= count($listaEstudiantes) ?> estudiantes</b> · <b><?= count($listaModulos) ?> módulos</b> · <b><?= count($listaRetos) ?> retos</b></p>
  </div>
</section>

<?php if (!empty($avisosProfesor)): ?>
<div class="aviso-atencion" style="margin:16px 0 4px;">
  <?php foreach ($avisosProfesor as $aviso): ?>
    <a href="<?= Security::escapeHtml($aviso['url']) ?>" class="aviso-chip aviso-chip--<?= $aviso['tipo'] ?>">
      <i class="fas <?= Security::escapeHtml($aviso['icono']) ?>"></i>
      <span><?= Security::escapeHtml($aviso['texto']) ?></span>
    </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="section-head">
  <h2>Acceso rápido</h2>
  <span class="count">Tu área docente</span>
</div>

<section class="dash-grid">

  <a href="../estudiantes/lista.php" class="tile card-soft" style="--tint:#F59E0B; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <?php if (count($listaEstudiantes) > 0) { ?><span class="tile-badge"><?= count($listaEstudiantes) ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mis Estudiantes</span>
      <span class="tile-desc">Listado y expedientes</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaEstudiantes) ?> alumnos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../modulos/lista.php" class="tile card-soft" style="--tint:#14B8A6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/></svg>
      <?php if (count($listaModulos) > 0) { ?><span class="tile-badge"><?= count($listaModulos) ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Módulos</span>
      <span class="tile-desc">Contenidos asignados</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaModulos) ?> módulos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../retos/lista.php" class="tile card-soft" style="--tint:#8B5CF6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
      <?php if (count($listaRetos) > 0) { ?><span class="tile-badge"><?= count($listaRetos) ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Retos</span>
      <span class="tile-desc">Proyectos y desafíos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaRetos) ?> retos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../calificaciones/lista.php" class="tile card-soft" style="--tint:#0EA5E9; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Calificaciones</span>
      <span class="tile-desc">Poner y revisar notas</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Gestionar notas</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../mensajes/lista.php" class="tile card-soft" style="--tint:#F43F5E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      <?php if ($mensajesPendientes > 0) { ?><span class="tile-badge"><?= $mensajesPendientes ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mensajería</span>
      <span class="tile-desc">Comunicación interna</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= count($listaMensajes) ?> mensajes</span>
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
      <span class="tile-desc">Sesiones y contenidos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Gestionar aula</span>
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
      <span class="tile-desc">Cuadro de horarios</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Ver horario</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <a href="../eventos/lista.php" class="tile card-soft" style="--tint:#22C55E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Eventos</span>
      <span class="tile-desc">Calendario del centro</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Ver eventos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

</section>

<!-- Heatmap de Alumnos en Riesgo -->
<?php if (!empty($alumnosRiesgo)): ?>
<div class="section-head" id="alumnos-riesgo" style="margin-top:24px; scroll-margin-top:90px;">
  <h2><i class="fas fa-exclamation-triangle" style="color:var(--rojo); margin-right:8px;"></i> Alumnos en Riesgo (Aula Digital)</h2>
  <span class="count">Requieren atención temprana</span>
</div>
<div class="panel">
  <div class="contenedor-tabla">
    <table class="tabla-datos" id="tablaAlumnosRiesgo">
      <thead>
        <tr>
          <th>Estudiante</th>
          <th>Módulo</th>
          <th>Entregas</th>
          <th>Nota Media</th>
          <th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($alumnosRiesgo as $alumno): ?>
          <tr>
            <td>
              <b><?= Security::escapeHtml($alumno['nombreEstudiante'] . ' ' . ($alumno['apellidosEstudiante'] ?? '')) ?></b>
            </td>
            <td><?= Security::escapeHtml($alumno['nombreModulo']) ?></td>
            <td>
              <?= (int)$alumno['tareasEntregadas'] ?> / <?= (int)$alumno['totalTareas'] ?>
              <?php if ($alumno['totalTareas'] - $alumno['tareasEntregadas'] > 0): ?>
                <span class="texto-estado rojo" style="margin-left:6px;"><i class="fas fa-clock"></i> Faltan <?= $alumno['totalTareas'] - $alumno['tareasEntregadas'] ?></span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($alumno['notaMedia'] !== null): ?>
                <span class="texto-negrita" style="color:<?= $alumno['notaMedia'] < 5 ? 'var(--rojo)' : 'var(--verde)' ?>"><?= number_format($alumno['notaMedia'], 1) ?></span>
              <?php else: ?>
                <span class="texto-suave">Sin calificar</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($alumno['nivelRiesgo'] === 'rojo'): ?>
                <span class="texto-estado rojo"><i class="fas fa-circle" style="font-size:0.5rem"></i> Alto Riesgo</span>
              <?php else: ?>
                <span class="texto-estado naranja"><i class="fas fa-circle" style="font-size:0.5rem"></i> Precaución</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php endif; ?>
<!-- Announcements + Events panels -->
<div class="dash-panels">
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Últimos Avisos</h3>
      <a href="../anuncios/lista.php">Ver todos</a>
    </div>
    <div class="dash-panel-body">
      <?php if (!empty($listaAnuncios)) {
        $contador = 0;
        foreach ($listaAnuncios as $anuncio) {
          if ($contador >= 4) break; ?>
          <div class="ann-item">
            <div class="ann-item-head">
              <span class="ann-item-title"><?= Security::escapeHtml($anuncio['titulo']) ?></span>
              <span class="ann-item-date"><?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></span>
            </div>
            <p class="ann-item-body"><?= Security::escapeHtml(substr(strip_tags($anuncio['mensaje']), 0, 120)) ?>…</p>
            <span class="ann-item-tag">PARA: <?= Security::escapeHtml(strtoupper($anuncio['dirigidoA'])) ?></span>
          </div>
      <?php $contador++; } } else { ?>
        <p class="empty-state">No hay anuncios activos.</p>
      <?php } ?>
    </div>
  </div>

  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Próximos Eventos</h3>
      <a href="../eventos/lista.php">Ver todos</a>
    </div>
    <div class="dash-panel-body">
      <?php if (!empty($listaEventos)) {
        $contador = 0;
        foreach ($listaEventos as $evento) {
          if ($contador >= 4) break;
          $dia = date('d', strtotime($evento['fechaEvento']));
          $mes = strtoupper(date('M', strtotime($evento['fechaEvento']))); ?>
          <div class="evt-item">
            <div class="evt-date-box">
              <span class="evt-day"><?= $dia ?></span>
              <span class="evt-mon"><?= $mes ?></span>
            </div>
            <div class="evt-info">
              <span class="evt-title"><?= Security::escapeHtml($evento['tituloEvento']) ?></span>
              <span class="evt-meta"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h · <?= Security::escapeHtml($evento['ubicacionEvento']) ?></span>
            </div>
          </div>
      <?php $contador++; } } else { ?>
        <p class="empty-state">No hay eventos próximos.</p>
      <?php } ?>
    </div>
  </div>
</div>

<script>
if (typeof iniciarPaginacion === 'function' && document.getElementById('tablaAlumnosRiesgo')) {
  iniciarPaginacion('tablaAlumnosRiesgo', 10);
}
</script>
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
