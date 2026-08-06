<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/landing.php";
require_once __DIR__ . "/../../../include/I18n.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$landingCfg = obtenerLandingConfig();

require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

require_once __DIR__ . "/../../../include/Cache.php";
require_once __DIR__ . "/../../../include/AssetMin.php";
require_once __DIR__ . "/../../../modelos/notificacionesRecordatorios.php";

$stats = Cache::remember('admin_dashboard_stats_' . $_SESSION['idAdmin'], 60, function () {
    return [
        'totalEstudiantes'  => contarEstudiantes(),
        'totalProfesores'   => contarProfesores(),
        'totalTutores'      => contarTutores(),
        'totalSecretarias'  => contarSecretarias(),
        'totalRetos'        => contarRetos(),
        'totalModulos'      => contarModulos(),
        'totalCiclos'       => contarCiclos(),
        'recaudado'         => obtenerTotalRecaudado(),
        'totalTFGs'         => contarTFGsEntregados(),
        'nuevosEstudiantes' => contarEstudiantesNuevos(7),
        'nuevosProfesores'  => contarProfesoresNuevos(7),
    ];
});
$totalEstudiantes  = $stats['totalEstudiantes'];
$totalProfesores   = $stats['totalProfesores'];
$totalTutores      = $stats['totalTutores'];
$totalSecretarias  = $stats['totalSecretarias'];
$totalRetos        = $stats['totalRetos'];
$totalModulos      = $stats['totalModulos'];
$totalCiclos       = $stats['totalCiclos'];
$recaudado         = $stats['recaudado'];
$totalTFGs         = $stats['totalTFGs'];
$nuevosEstudiantes = $stats['nuevosEstudiantes'];
$nuevosProfesores  = $stats['nuevosProfesores'];

$adminInfo   = obtenerDirectorPorId($_SESSION['idAdmin']);
$nombreAdmin = $adminInfo['nombreDirector'] ?? 'ADMINISTRADOR';

$listaAnuncios = listarTodosLosAnuncios();
$eventos       = listarEventosProximos();
$recordatoriosPendientes = obtenerNotificacionesNoLeidas((int)$_SESSION['idAdmin'], 'director', 5);
$estudiantesPendientes = listarEstudiantesConPagosPendientes();

$titulo_pagina = 'AulaPro — Panel de Control';
$seccion       = 'inicio';
include __DIR__ . '/../comunes/nav.php';


require_once __DIR__ . "/../../../modelos/actualizaciones.php";

try {
    $actualizaciones = obtenerActualizacionesRecientes(5);
    // Calcula los segundos hasta la próxima medianoche en hora de España (Europe/Madrid)
    $tz = new DateTimeZone('Europe/Madrid');
    $now = new DateTime('now', $tz);
    $midnight = (clone $now)->setTime(0,0,0)->modify('+1 day');
    $secondsToMidnight = $midnight->getTimestamp() - $now->getTimestamp();
} catch (Throwable $e) {
    error_log('Error loading dashboard updates: ' . $e->getMessage());
    $actualizaciones = [];
    $secondsToMidnight = 0;
}

require_once __DIR__ . "/../../../include/dashboard_helpers.php";
$eyebrow = fechaLegibleHoy();
$saludo  = saludoHorario();
?>
<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/calendario.css') ?>">
<?php

// Arrow SVG helper
$arrowSvg = '<svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
?>

<section class="hero">
  <div class="hero-text">
    <p class="eyebrow"><?= Security::escapeHtml($eyebrow) ?></p>
    <h1><?= Security::escapeHtml($saludo) ?>, <span><?= Security::escapeHtml($nombreAdmin) ?></span></h1>
    <p class="sub">Panel de control — <b><?= $totalEstudiantes ?> estudiantes</b> · <b><?= $totalProfesores ?> profesores</b> · <b><?= $totalModulos ?> módulos</b></p>
  </div>
</section>

<?php if (empty($landingCfg['plantilla'])): ?>
<div class="panel" style="background: linear-gradient(135deg, var(--accent) 0%, #312e81 100%); color: white; margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between; border-radius: 12px; padding: 20px 24px; box-shadow: 0 10px 25px rgba(79,70,229,0.3);">
    <div>
        <h3 style="color: white; margin-top: 0; display: flex; align-items: center; gap: 8px;"><i class="fas fa-magic"></i> ¡Bienvenido a AulaPro!</h3>
        <p style="color: rgba(255,255,255,0.85); margin-bottom: 0;">Parece que es su primera vez aquí. Le recomendamos configurar la identidad y plantilla de su centro educativo.</p>
    </div>
    <a href="../landing/onboarding.php" class="boton-primario" style="background: white; color: var(--accent); white-space: nowrap;">
        Configurar estilo y plantillas
    </a>
</div>
<?php endif; ?>

<div class="section-head">
  <h2><?= I18n::translate('quick_access', 'Acceso rápido') ?></h2>
  <span class="count">Gestión del centro</span>
</div>

<!-- 4 Quick Access Cards -->
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px;margin-bottom:24px;">
  <a href="../gastos/verGastos.php" class="tile card-soft" style="--tint:#E67E22; text-decoration:none; padding:20px;">
    <span class="tile-ico" style="font-size:1.6rem;margin-bottom:12px;">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
      </svg>
    </span>
    <span class="tile-body">
      <span class="tile-label"><?= I18n::translate('expenses', 'Gastos') ?></span>
      <span class="tile-desc" style="font-size:0.75rem;"><?= I18n::translate('expenses_management', 'Gestión de gastos') ?></span>
    </span>
  </a>
  <a href="../pagos/verPagosGeneral.php" class="tile card-soft" style="--tint:#27AE60; text-decoration:none; padding:20px;">
    <span class="tile-ico" style="font-size:1.6rem;margin-bottom:12px;">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
      </svg>
    </span>
    <span class="tile-body">
      <span class="tile-label"><?= I18n::translate('payments', 'Pagos') ?></span>
      <span class="tile-desc" style="font-size:0.75rem;"><?= I18n::translate('payments_management', 'Gestión de pagos') ?></span>
    </span>
  </a>
  <a href="../estudiantes/verEstudiantes.php" class="tile card-soft" style="--tint:#F59E0B; text-decoration:none; padding:20px;">
    <span class="tile-ico" style="font-size:1.6rem;margin-bottom:12px;">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>
      </svg>
    </span>
    <span class="tile-body">
      <span class="tile-label"><?= I18n::translate('students', 'Alumnos') ?></span>
      <span class="tile-desc" style="font-size:0.75rem;"><?= I18n::translate('student_management', 'Gestión de alumnos') ?></span>
    </span>
  </a>
  <a href="../profesores/verProfesores.php" class="tile card-soft" style="--tint:#3498DB; text-decoration:none; padding:20px;">
    <span class="tile-ico" style="font-size:1.6rem;margin-bottom:12px;">
      <svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>
      </svg>
    </span>
    <span class="tile-body">
      <span class="tile-label"><?= I18n::translate('teachers', 'Profesores') ?></span>
      <span class="tile-desc" style="font-size:0.75rem;"><?= I18n::translate('teacher_management', 'Gestión de profesores') ?></span>
    </span>
  </a>
</div>

<?php if (!empty($actualizaciones)): ?>
<section class="updates" data-seconds="<?= $secondsToMidnight ?>">
  <h2>Actualizaciones recientes</h2>
  <ul class="updates-list">
    <?php foreach ($actualizaciones as $act): ?>
      <li>
        <span class="update-msg"><?= Security::escapeHtml($act['mensaje']) ?></span>
        <span class="update-date"><?= date('d/m/Y H:i', strtotime($act['fecha'])) ?></span>
      </li>
    <?php endforeach; ?>
  </ul>
</section>
<?php endif; ?>
<section class="dash-grid">

  <!-- A: Admisiones -->
  <?php if (FeatureGuard::check('feature_prematricula')): ?>
  <a href="../admisiones/listado.php" class="tile card-soft" style="--tint:#7C3AED; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 5.9L11.8 16.1l-4.2-4.2"/></svg>
      <?php if (($totalAdmisionesPendientes_menu ?? 0) > 0) { ?><span class="tile-badge"><?= $totalAdmisionesPendientes_menu ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Admisiones</span>
      <span class="tile-desc">Gestión de pre-matrículas</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalAdmisionesPendientes_menu ?? 0 ?> pendientes</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>
  <?php endif; ?>

  <!-- C: Ciclos Formativos -->
  <a href="../ciclos/verCiclos.php" class="tile card-soft" style="--tint:#0284C7; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Ciclos Formativos</span>
      <span class="tile-desc">Programas académicos activos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalCiclos ?> ciclos · <?= $totalModulos ?> módulos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- F: FP Dual -->
  <?php if (FeatureGuard::check('feature_fp_dual')): ?>
  <a href="../fp_dual/verEmpresas.php" class="tile card-soft" style="--tint:#10b981; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">FP Dual</span>
      <span class="tile-desc">Empresas colaboradoras</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Gestión de empresas</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>
  <?php endif; ?>

  <!-- E: RA / CE -->
  <?php if (FeatureGuard::check('feature_ra_ce')): ?>
  <a href="../modulos/verModulos.php" class="tile card-soft" style="--tint:#f43f5e; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Evaluación RA/CE</span>
      <span class="tile-desc">Gestión LOMLOE</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Seleccione un módulo</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>
  <?php endif; ?>

  <!-- C: Configuración -->
  <a href="../configuracion/configuracion.php" class="tile card-soft" style="--tint:#64748B; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Configuración</span>
      <span class="tile-desc">Ajustes del sistema</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Sistema</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- E: Estudiantes -->
  <a href="../estudiantes/verEstudiantes.php" class="tile card-soft" style="--tint:#F59E0B; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      <?php if ($nuevosEstudiantes > 0) { ?><span class="tile-badge"><?= $nuevosEstudiantes ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Estudiantes</span>
      <span class="tile-desc">Expedientes y matrículas</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalEstudiantes ?> matriculados · <?= $totalTFGs ?> TFGs</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- E: Eventos -->
  <a href="../eventos/gestionEventos.php" class="tile card-soft" style="--tint:#06B6D4; text-decoration:none">
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

  <!-- F: Familias (Sistema Parental) -->
  <a href="../tutores/verTutores.php" class="tile card-soft" style="--tint:#10B981; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Familias</span>
      <span class="tile-desc">Sistema parental</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalTutores ?> registradas</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- H: Horario -->
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

  <!-- I: Informes PDF -->
  <a href="../informes/informes.php" class="tile card-soft" style="--tint:#D946EF; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Informes PDF</span>
      <span class="tile-desc">Generación de informes</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat">Exportar datos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- I: Inventario -->
  <a href="../inventario/verInventario.php" class="tile card-soft" style="--tint:#F97316; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27,6.96 12,12.01 20.73,6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
      <?php if (($totalPrestamos_menu ?? 0) > 0) { ?><span class="tile-badge"><?= $totalPrestamos_menu ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Inventario</span>
      <span class="tile-desc">Recursos materiales</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalInventario_menu ?? 0 ?> artículos · <?= $totalPrestamos_menu ?? 0 ?> en préstamo</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- M: Mensajería -->
  <a href="../mensajes/lista.php" class="tile card-soft" style="--tint:#F43F5E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
      <?php if (($totalSinLeer_menu ?? 0) > 0) { ?><span class="tile-badge"><?= $totalSinLeer_menu ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Mensajería</span>
      <span class="tile-desc">Comunicación interna</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalMensajes_menu ?? 0 ?> mensajes<?php if (($totalSinLeer_menu ?? 0) > 0): ?> · <span style="color:var(--rojo);font-weight:800"><?= $totalSinLeer_menu ?> sin leer</span><?php endif; ?></span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- M: Módulos -->
  <a href="../modulos/verModulos.php" class="tile card-soft" style="--tint:#14B8A6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Módulos</span>
      <span class="tile-desc">Contenidos curriculares</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalModulos ?> módulos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- P: Pagos -->
  <a href="../pagos/verPagosGeneral.php" class="tile card-soft" style="--tint:#22C55E; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Pagos</span>
      <span class="tile-desc">Gestión de cobros</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= number_format($recaudado, 0, ',', '.') ?>€ recaudado</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- P: Profesores -->
  <a href="../profesores/verProfesores.php" class="tile card-soft" style="--tint:#0EA5E9; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      <?php if ($nuevosProfesores > 0) { ?><span class="tile-badge"><?= $nuevosProfesores ?></span><?php } ?>
    </span>
    <span class="tile-body">
      <span class="tile-label">Profesores</span>
      <span class="tile-desc">Equipo docente</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalProfesores ?> activos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- R: Retos -->
  <a href="../retos/verRetos.php" class="tile card-soft" style="--tint:#8B5CF6; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Retos</span>
      <span class="tile-desc">Proyectos y desafíos</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalRetos ?> retos</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

  <!-- S: Secretarias -->
  <a href="../secretarias/verSecretarias.php" class="tile card-soft" style="--tint:#6366F1; text-decoration:none">
    <span class="tile-sheen"></span>
    <span class="tile-ico">
      <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 11v4M10 13h4"/></svg>
    </span>
    <span class="tile-body">
      <span class="tile-label">Secretarias</span>
      <span class="tile-desc">Personal administrativo</span>
    </span>
    <span class="tile-foot">
      <span class="tile-stat"><?= $totalSecretarias ?> registradas</span>
      <span class="tile-go"><?= $arrowSvg ?></span>
    </span>
  </a>

</section>

<!-- Calendar widget -->
<?php include __DIR__ . '/../../comunes/eventos/_calendario_widget.php'; ?>

<!-- Modal evento (para calendar widget CRUD) -->
<?php $rolBase = 'admin'; include __DIR__ . '/../../comunes/eventos/_modal_evento.php'; ?>

<!-- Announcements + Events panels -->
<div class="dash-panels">
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Anuncios</h3>
      <a href="../anuncios/gestionAnuncios.php">Ver todos</a>
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
            <span class="ann-item-tag"><?= Security::escapeHtml(strtoupper($anuncio['dirigidoA'])) ?></span>
          </div>
      <?php $contador++; } } else { ?>
        <p class="empty-state">No hay anuncios activos por ahora.</p>
      <?php } ?>
    </div>
  </div>

  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Próximos Eventos</h3>
      <a href="../eventos/gestionEventos.php">Ver todos</a>
    </div>
    <div class="dash-panel-body">
      <?php if (!empty($eventos)) {
        $contador = 0;
        foreach ($eventos as $evento) {
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
        <p class="empty-state">No hay eventos próximos programados.</p>
      <?php } ?>
    </div>
  </div>

  <!-- Panel: Recordatorios de eventos (notificaciones_recordatorios) -->
  <div class="dash-panel">
    <div class="dash-panel-head">
      <h3>Recordatorios <span id="notif-badge" class="texto-estado rojo" style="display:inline-block;" hidden></span></h3>
      <a href="../eventos/gestionEventos.php">Ver todos los recordatorios</a>
    </div>
    <div class="dash-panel-body">
      <div id="lista-notificaciones" class="recordatorios-widget-lista">
        <?php if (empty($recordatoriosPendientes)) { ?>
          <p class="empty-state">No hay recordatorios pendientes.</p>
        <?php } else { foreach ($recordatoriosPendientes as $rec) { ?>
          <div class="recordatorio-item" data-id="<?= (int)$rec['idNotificacion'] ?>">
            <div class="recordatorio-item-info">
              <span class="recordatorio-item-titulo"><?= Security::escapeHtml($rec['tituloEvento']) ?></span>
              <span class="recordatorio-item-fecha"><?= Security::escapeHtml(date('d/m/Y', strtotime($rec['fechaEvento']))) ?><?= $rec['horaEvento'] ? ' ' . Security::escapeHtml(date('H:i', strtotime($rec['horaEvento']))) : '' ?></span>
            </div>
            <button type="button" class="recordatorio-item-marcar" data-marcar-leido="<?= (int)$rec['idNotificacion'] ?>">Marcar leído</button>
          </div>
        <?php } } ?>
      </div>
    </div>
  </div>
</div>

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
<script>
  const updatesSection = document.querySelector('.updates[data-seconds]');
  if (updatesSection) {
    const seconds = parseInt(updatesSection.dataset.seconds, 10);
    if (!isNaN(seconds) && seconds > 0) {
      setTimeout(() => {
        updatesSection.style.transition = 'opacity 0.5s';
        updatesSection.style.opacity = '0';
        setTimeout(() => updatesSection.remove(), 500);
      }, seconds * 1000);
    }
  }
</script>

<script src="<?= AssetMin::url(__DIR__, '../../../public/js/core/notificaciones-dashboard.js') ?>"></script>
<script src="<?= AssetMin::url(__DIR__, '../../../public/js/features/calendario.js') ?>"></script>
<?php include __DIR__ . '/../comunes/footer.php'; ?>
