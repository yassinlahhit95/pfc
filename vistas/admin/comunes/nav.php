<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/AssetMin.php";
require_once __DIR__ . "/../../../config/Config.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/tours.php";

$datosAdmin_menu    = obtenerDirectorPorId($_SESSION['idAdmin']);

$tourPendiente_menu = !tourEstaCompletado((int)$_SESSION['idAdmin'], 'admin', 'primeros_pasos_v1');
$nombreUsuario_menu = $datosAdmin_menu['nombreDirector'] ?? 'Administrador';

$navCounts = obtenerContadoresNavAdmin((int)($_SESSION['idAdmin'] ?? 0));
$totalSinLeer_menu              = $navCounts['total_sin_leer']              ?? 0;
$totalAdmisionesPendientes_menu = $navCounts['total_admisiones_pendientes'] ?? 0;
$totalChatNoLeidos_menu         = $navCounts['total_chat_no_leidos']        ?? 0;
$totalInventario_menu           = $navCounts['total_inventario']            ?? 0;
$totalPrestamos_menu            = $navCounts['total_prestamos']             ?? 0;
$totalMensajes_menu             = $navCounts['total_mensajes']              ?? 0;

// Notification panel: recent unread messages for admin (max 3)
$mensajesNotifAdmin = [];
if ($totalSinLeer_menu > 0) {
    $conexionNotif = obtenerConexion();
    $stmtNotif = mysqli_prepare($conexionNotif,
        "SELECT idReclamacion, asunto, fecha FROM reclamaciones
         WHERE leido = 0
           AND ((emisor_rol = 'estudiante' AND idProfesor IS NULL)
             OR (emisor_rol = 'profesor' AND idEstudiante IS NULL))
         ORDER BY idReclamacion DESC LIMIT 3");
    mysqli_stmt_execute($stmtNotif);
    $resultNotif = mysqli_stmt_get_result($stmtNotif);
    while ($filaNotif = mysqli_fetch_assoc($resultNotif)) { $mensajesNotifAdmin[] = $filaNotif; }
}

// Active-state helper
function _nav_active_admin($check) {
    global $seccion;
    return ($seccion === $check) ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light" data-density="regular">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?= Security::escapeHtml($titulo_pagina ?? 'AulaPro Admin') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <?php $__bundleCss = __DIR__ . '/../../../public/css/bundle.min.css'; ?>
  <?php if (is_file($__bundleCss)): ?>
  <link rel="stylesheet" href="../../../public/css/bundle.min.css?v=<?= filemtime($__bundleCss) ?>" />
  <?php else: ?>
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/features/notificaciones.css" />
  <link rel="stylesheet" href="../../../public/css/features/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/aula-digital.css') ?>" />
  <link rel="stylesheet" href="../../../public/css/features/onboarding-tour.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/onboarding-tour.css') ?>" />
  <?php endif; ?>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous" />
  <?php if (FeatureGuard::check('feature_chat')): ?>
  <link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/chat-widget.css') ?>" />
  <?php endif; ?>
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
  <script defer src="../../../public/js/core/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/core/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/menu-contextual.js') ?>"></script>
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>
<div class="app" id="app">
  <div class="bg-mesh" aria-hidden="true">
    <span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span>
  </div>

  <aside class="sidebar" id="main-sidebar">
    <script>
      try {
        if (JSON.parse(localStorage.getItem("aulapro_tweaks_v1")).sidebarCollapsed) {
          var sidebarEl = document.getElementById("main-sidebar");
          sidebarEl.classList.add("collapsed");
          sidebarEl.style.setProperty("transition", "none", "important");
          setTimeout(function() { sidebarEl.style.removeProperty("transition"); }, 150);
        }
      } catch (e) {}
    </script>
    <?php $navBrandSubtitle = 'Campus Suite'; include __DIR__ . '/../../comunes/nav_brand.php'; ?>

    <nav class="sidebar-nav-scroll" id="sidebar-nav">

      <!-- Dashboard -->
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_admin('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Dashboard</span>
        <?php if (_nav_active_admin('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- ACADÉMICO -->
      <span class="nav-section-title">ACADÉMICO</span>

      <a href="../estudiantes/verEstudiantes.php" data-tour="estudiantes" class="nav-item<?= _nav_active_admin('estudiantes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Estudiantes</span>
        <?php if (_nav_active_admin('estudiantes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../ciclos/verCiclos.php" class="nav-item<?= _nav_active_admin('ciclos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></span>
        <span class="nav-label">Ciclos Formativos</span>
        <?php if (_nav_active_admin('ciclos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../modulos/verModulos.php" class="nav-item<?= _nav_active_admin('modulos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/></svg></span>
        <span class="nav-label">Módulos</span>
        <?php if (_nav_active_admin('modulos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_fp_dual')): ?>
      <a href="../fp_dual/verEmpresas.php" class="nav-item<?= _nav_active_admin('fp_dual') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg></span>
        <span class="nav-label">FP Dual</span>
        <?php if (_nav_active_admin('fp_dual') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_retos')): ?>
      <a href="../retos/verRetos.php" class="nav-item<?= _nav_active_admin('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Retos</span>
        <?php if (_nav_active_admin('retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php $notasFlyoutActivo = in_array($seccion, ['notas_modulos', 'notas_retos', 'notas_tfg', 'resultados_modulos'], true); ?>
      <div class="nav-flyout-wrap">
        <button type="button" class="nav-item nav-flyout-btn<?= $notasFlyoutActivo ? ' active' : '' ?>" aria-haspopup="true" aria-expanded="false">
          <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
          <span class="nav-label">Notas</span>
          <span class="nav-flyout-caret"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></span>
          <?php if ($notasFlyoutActivo) { ?><span class="nav-rail"></span><?php } ?>
        </button>
        <div class="nav-flyout-menu">
          <a href="../academico/calificacionesModulos.php" class="nav-flyout-item<?= _nav_active_admin('notas_modulos') ?>"><i class="fas fa-chart-simple"></i> Notas Módulos</a>
          <?php if (FeatureGuard::check('feature_retos')): ?>
          <a href="../academico/calificacionesRetos.php" class="nav-flyout-item<?= _nav_active_admin('notas_retos') ?>"><i class="fas fa-trophy"></i> Notas Retos</a>
          <?php endif; ?>
          <a href="../academico/calificacionesTFG.php" class="nav-flyout-item<?= _nav_active_admin('notas_tfg') ?>"><i class="fas fa-graduation-cap"></i> Notas TFG</a>
          <a href="../academico/resultadosFinales.php" class="nav-flyout-item<?= _nav_active_admin('resultados_modulos') ?>"><i class="fas fa-flag-checkered"></i> Resultados Finales</a>
        </div>
      </div>

      <?php if (FeatureGuard::check('feature_fct')): ?>
      <a href="../fct/lista.php" class="nav-item<?= _nav_active_admin('fct') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
        <span class="nav-label">FCT</span>
        <?php if (_nav_active_admin('fct') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../academico/configuracionAcademica.php" class="nav-item<?= _nav_active_admin('configuracion_academica') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 1 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 1 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 1 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 1 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
        <span class="nav-label">Configuración Académica</span>
        <?php if (_nav_active_admin('configuracion_academica') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/regionalExporters.php" class="nav-item<?= _nav_active_admin('regional_exporters') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
        <span class="nav-label">Gestión Regional (Euskadi)</span>
        <?php if (_nav_active_admin('regional_exporters') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/gestionarGrupos.php" class="nav-item<?= _nav_active_admin('gestionar_grupos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Aulas / Grupos</span>
        <?php if (_nav_active_admin('gestionar_grupos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_horario')): ?>
      <a href="../horario/horario.php" class="nav-item<?= _nav_active_admin('horario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Cuadro Horario</span>
        <?php if (_nav_active_admin('horario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../asistencias/verAsistencias.php" class="nav-item<?= _nav_active_admin('asistencias') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>
        <span class="nav-label"><?= __('attendance', 'Asistencias') ?></span>
        <?php if (_nav_active_admin('asistencias') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../asistencias/justificaciones.php" class="nav-item<?= _nav_active_admin('justificaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
        <span class="nav-label"><?= __('justificaciones', 'Justificaciones') ?></span>
        <?php if (_nav_active_admin('justificaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_prematricula')): ?>
      <a href="../admisiones/listado.php" class="nav-item<?= _nav_active_admin('admisiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
        <span class="nav-label">Admisiones</span>
        <?php if ($totalAdmisionesPendientes_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalAdmisionesPendientes_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('admisiones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <!-- PERSONAL -->
      <span class="nav-section-title">PERSONAL</span>

      <a href="../directores/verDirectores.php" class="nav-item<?= _nav_active_admin('directores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label">Directores</span>
        <?php if (_nav_active_admin('directores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../profesores/verProfesores.php" class="nav-item<?= _nav_active_admin('profesores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Profesores</span>
        <?php if (_nav_active_admin('profesores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../tutores/verTutores.php" class="nav-item<?= _nav_active_admin('tutores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Sistema Parental</span>
        <?php if (_nav_active_admin('tutores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../secretarias/verSecretarias.php" class="nav-item<?= _nav_active_admin('secretarias') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/><path d="M12 11v4M10 13h4"/></svg></span>
        <span class="nav-label">Secretarias</span>
        <?php if (_nav_active_admin('secretarias') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- COMUNICACIÓN -->
      <span class="nav-section-title">COMUNICACIÓN</span>

      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/gestionAnuncios.php" class="nav-item<?= _nav_active_admin('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Avisos</span>
        <?php if (_nav_active_admin('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_mensajes')): ?>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_admin('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajeria</span>
        <?php if ($totalSinLeer_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalSinLeer_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('reclamaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')): ?>
      <a href="../chat/index.php" class="nav-item<?= _nav_active_admin('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
        <?php if ($totalChatNoLeidos_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalChatNoLeidos_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('chat') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_eventos')): ?>
      <a href="../eventos/gestionEventos.php" class="nav-item<?= _nav_active_admin('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_admin('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <!-- FINANZAS -->
      <span class="nav-section-title">FINANZAS</span>

      <?php if (FeatureGuard::check('feature_pagos')): ?>
      <a href="../pagos/verPagosGeneral.php" data-tour="pagos" class="nav-item<?= _nav_active_admin('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label">Pagos</span>
        <?php if (_nav_active_admin('pagos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_gastos')): ?>
      <a href="../gastos/verGastos.php" class="nav-item<?= _nav_active_admin('gastos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
        <span class="nav-label">Gastos</span>
        <?php if (_nav_active_admin('gastos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <!-- RECURSOS -->
      <span class="nav-section-title">RECURSOS</span>

      <?php if (FeatureGuard::check('feature_gastos')): ?>
      <a href="../gastos/verGastos.php" class="nav-item<?= _nav_active_admin('gastos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg></span>
        <span class="nav-label">Gastos</span>
        <?php if (_nav_active_admin('gastos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_inventario')): ?>
      <a href="../inventario/verInventario.php" class="nav-item<?= _nav_active_admin('inventario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27,6.96 12,12.01 20.73,6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
        <span class="nav-label">Inventario</span>
        <?php if (_nav_active_admin('inventario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../inventario/gestionarPrestamos.php" class="nav-item<?= _nav_active_admin('prestamos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg></span>
        <span class="nav-label">Préstamos</span>
        <?php if (_nav_active_admin('prestamos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../aulas/gestionAulas.php" class="nav-item<?= _nav_active_admin('aulas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9,22 9,12 15,12 15,22"/></svg></span>
        <span class="nav-label">Aulas</span>
        <?php if (_nav_active_admin('aulas') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- DOCUMENTOS -->
      <span class="nav-section-title">DOCUMENTOS</span>

      <?php if (FeatureGuard::check('feature_informes')): ?>
      <a href="../informes/informes.php" class="nav-item<?= _nav_active_admin('informes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10,9 9,9 8,9"/></svg></span>
        <span class="nav-label">Informes PDF</span>
        <?php if (_nav_active_admin('informes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <!-- PLATAFORMA -->
      <span class="nav-section-title">PLATAFORMA</span>

      <?php $configFlyoutActivo = in_array($seccion, ['landing', 'blog', 'ofertaCiclos', 'rgpd', 'saas_estado'], true); ?>
      <div class="nav-flyout-wrap">
        <button type="button" class="nav-item nav-flyout-btn<?= $configFlyoutActivo ? ' active' : '' ?>" aria-haspopup="true" aria-expanded="false">
          <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg></span>
          <span class="nav-label">Configuración</span>
          <span class="nav-flyout-caret"><svg viewBox="0 0 24 24" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg></span>
          <?php if ($configFlyoutActivo) { ?><span class="nav-rail"></span><?php } ?>
        </button>
        <div class="nav-flyout-menu">
          <?php if (FeatureGuard::check('feature_landing')): ?>
          <a href="../landing/builder.php" class="nav-flyout-item<?= _nav_active_admin('landing') ?>"><i class="fas fa-earth-americas"></i> Página Web</a>
          <a href="../blog/gestionBlog.php" class="nav-flyout-item<?= _nav_active_admin('blog') ?>"><i class="fas fa-newspaper"></i> Blog</a>
          <a href="../ofertaCiclos/gestion.php" class="nav-flyout-item<?= _nav_active_admin('ofertaCiclos') ?>"><i class="fas fa-layer-group"></i> Catálogo de ciclos</a>
          <?php endif; ?>
          <a href="../rgpd/index.php" class="nav-flyout-item<?= _nav_active_admin('rgpd') ?>"><i class="fas fa-shield-halved"></i> RGPD</a>
          <a href="../saas/estado.php" class="nav-flyout-item<?= _nav_active_admin('saas_estado') ?>"><i class="fas fa-server"></i> Estado SaaS</a>
        </div>
      </div>

      <a href="../configuracion/configuracion.php" data-tour="configuracion" class="nav-item<?= _nav_active_admin('configuracion') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
        <span class="nav-label">Configuración del Centro</span>
        <?php if (_nav_active_admin('configuracion') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../../ayuda.php" class="nav-item<?= ($seccion ?? '') === 'ayuda' ? ' active' : '' ?>">
        <span class="nav-ico"><i class="fas fa-question-circle" style="font-size:1.15rem;"></i></span>
        <span class="nav-label"><?= __('help_center', 'Centro de Ayuda') ?></span>
        <?php if (($seccion ?? '') === 'ayuda') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <a href="../directores/verDetallesDirectores.php?id=<?= (int)$_SESSION['idAdmin'] ?>" class="nav-item">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label"><?= __('my_profile', 'Mi Perfil') ?></span>
      </a>
      <a href="../../../controladores/logout.php" class="nav-item nav-item-logout">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
        <span class="nav-label"><?= __('logout', 'Cerrar Sesión') ?></span>
      </a>
    </nav>
  </aside>

  <div class="scrim"></div>

  <main class="main" data-screen-label="<?= Security::escapeHtml($titulo_pagina ?? 'Admin') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu" aria-label="Menú">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge">ADMIN</span>
        <span class="topbar-user-name"><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
      </div>
      <div class="topbar-actions">
        <!-- Mobile Trigger -->
        <button class="icon-btn mobile-search-trigger" id="mobile-search-trigger" aria-label="Buscar">
          <svg class="search-icon-svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
        </button>
        <!-- Desktop Input / Mobile Modal -->
        <div class="search-backdrop" id="search-backdrop" hidden></div>
        <div class="search-wrapper" id="search-wrapper" data-tour="busqueda">
          <label class="search-modal-bar">
            <svg class="search-icon-svg desktop-only-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
            <input id="sys-search" class="search-modal-input" type="search" placeholder="Buscar..." 
                   autocomplete="one-time-code" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other"
                   data-url="../../../controladores/admin/buscar.php" />
            <button class="search-close" id="search-close" aria-label="Cerrar búsqueda">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <kbd class="search-kbd">⌘K</kbd>
          </label>
          <ul class="search-results" id="search-results" hidden></ul>
        </div>
        <!-- Language Switcher -->
        <div class="lang-wrap" style="position:relative; display:inline-block; margin-right:8px;">
          <form action="../../../controladores/cambiar_idioma.php" method="POST" id="formLanguageAdmin" style="margin:0;">
             <select name="lang" onchange="document.getElementById('formLanguageAdmin').submit();" style="padding:5px 8px; border-radius:8px; border:1.5px solid var(--border); font-size:.85rem; background:var(--bg-card); color:var(--text); cursor:pointer; font-weight:600; outline:none; font-family:inherit;">
                <option value="es" <?= I18n::getLang() === 'es' ? 'selected' : '' ?>>ES</option>
                <option value="eu" <?= I18n::getLang() === 'eu' ? 'selected' : '' ?>>EU</option>
                <option value="ca" <?= I18n::getLang() === 'ca' ? 'selected' : '' ?>>CA</option>
                <option value="en" <?= I18n::getLang() === 'en' ? 'selected' : '' ?>>EN</option>
             </select>
          </form>
        </div>
        <button class="icon-btn theme-btn" id="theme" aria-label="Cambiar tema">
          <span class="theme-knob"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg></span>
        </button>
        <div class="notif-wrap">
          <button class="icon-btn" id="notif-btn" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            <span class="dot" id="notif-dot" data-msgs="<?= (int)$totalSinLeer_menu ?>"<?= ($totalSinLeer_menu > 0) ? '' : ' hidden' ?>></span>
          </button>
          <div class="notif-panel" id="notif-panel" hidden>
            <div class="notif-panel-head">Notificaciones</div>
            <?php if (!empty($mensajesNotifAdmin)): ?>
            <div class="notif-group-title">Mensajes sin leer</div>
            <?php foreach ($mensajesNotifAdmin as $msgNotif): ?>
            <a href="../mensajes/lista.php" class="notif-item">
              <span class="notif-ico"><i class="fas fa-envelope"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($msgNotif['asunto']) ?></span>
                <span class="notif-time"><?= date('d/m H:i', strtotime($msgNotif['fecha'])) ?></span>
              </div>
              <span class="notif-badge-new">Nuevo</span>
            </a>
            <?php endforeach; ?>
            <?php else: ?>
            <div class="notif-empty">Sin mensajes nuevos</div>
            <?php endif; ?>
            <div class="notif-footer">
              <a href="../mensajes/lista.php">Ver mensajería</a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <?php if (FeatureGuard::check('feature_chat') && ($seccion ?? '') !== 'chat'):
        $cw_rol = 'admin';
        $cw_id = (int)$_SESSION['idAdmin'];
        $cw_unreadCount = (int)$totalChatNoLeidos_menu;
        $cw_basePath = '../../../';
        include __DIR__ . '/../../comunes/chat_widget.php';
    endif; ?>
    <div class="content" id="main-content" tabindex="-1">
      <?php require __DIR__ . '/../../comunes/breadcrumb.php'; ?>
      <?php if ($tourPendiente_menu): ?>
      <script>
      window.AULAPRO_TOUR = {
        tourKey: 'primeros_pasos_v1',
        completeUrl: 'controladores/comunes/tour/completar.php',
        csrfToken: <?= json_encode(Security::generateCSRFToken()) ?>,
        steps: [
          { selector: '[data-tour="estudiantes"]', title: 'Estudiantes', text: 'Gestiona el alumnado del centro: fichas, matrículas y documentación.', placement: 'right' },
          { selector: '[data-tour="pagos"]', title: 'Pagos', text: 'Controla los recibos y comprobantes de pago de cada estudiante.', placement: 'right' },
          { selector: '[data-tour="configuracion"]', title: 'Configuración', text: 'Activa o desactiva funciones del centro y personaliza los datos generales.', placement: 'right' },
          { selector: '[data-tour="busqueda"]', title: 'Búsqueda global', text: 'Pulsa aquí (o Ctrl/Cmd+K) para buscar estudiantes, pagos, mensajes y más desde cualquier página.', placement: 'bottom' }
        ]
      };
      </script>
      <?php endif; ?>
      <?php
      // SaaS platform message banner — shown on every admin page
      if (class_exists('FeatureGuard')) {
          if (FeatureGuard::isSuspended()) {
              $mensajeSuspension = FeatureGuard::getSuspensionMessage() ?: 'Esta instancia ha sido suspendida por la plataforma SaaS. Contacta con el proveedor.';
              echo '<div style="position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.92);display:flex;align-items:center;justify-content:center;padding:24px;">';
              echo '<div style="max-width:520px;width:100%;background:var(--surface);border-radius:16px;padding:36px 32px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.35);">';
              echo '<div style="font-size:3rem;margin-bottom:12px;color:var(--rojo);"><i class="fas fa-lock"></i></div>';
              echo '<h2 style="margin:0 0 12px;color:var(--rojo);font-size:1.4rem;">Acceso Suspendido</h2>';
              echo '<p style="color:var(--text);font-size:.95rem;line-height:1.6;margin:0 0 24px;">' . htmlspecialchars($mensajeSuspension, ENT_QUOTES) . '</p>';
              echo '<a href="/vistas/admin/saas/estado.php" style="display:inline-block;padding:10px 22px;background:var(--accent);color:var(--accent-ink);text-decoration:none;border-radius:8px;font-weight:600;font-size:.9rem;">Ver estado de la plataforma</a>';
              echo '</div></div>';
          }
          $saasMensaje = FeatureGuard::getMessage();
          $saasTipo    = FeatureGuard::getMessageType();
          if ($saasMensaje) {
              // Los 5 tipos ya tienen su pareja semántica en dashboard.css
              // (--azul/-suave, --rojo/-suave, etc.) — antes se repetían aquí
              // como hex sueltos, así que un retoque de paleta en dashboard.css
              // no se enteraba de este banner. El borde se deriva con
              // color-mix() del mismo tono en vez de un 3er valor hardcodeado.
              $coloresSaas = [
                  'info'         => ['var(--azul)','var(--azul-suave)','color-mix(in srgb, var(--azul) 35%, transparent)','fa-circle-info'],
                  'warning'      => ['var(--naranja)','var(--naranja-suave)','color-mix(in srgb, var(--naranja) 35%, transparent)','fa-triangle-exclamation'],
                  'error'        => ['var(--rojo)','var(--rojo-suave)','color-mix(in srgb, var(--rojo) 35%, transparent)','fa-circle-exclamation'],
                  'subscription' => ['var(--violeta)','var(--violeta-suave)','color-mix(in srgb, var(--violeta) 35%, transparent)','fa-credit-card'],
                  'activation'   => ['var(--amarillo)','var(--amarillo-suave)','color-mix(in srgb, var(--amarillo) 35%, transparent)','fa-key'],
              ];
              [$colorSaas,$bgSaas,$bordeSaas,$iconoSaas] = $coloresSaas[$saasTipo] ?? $coloresSaas['info'];
              echo '<div style="margin-bottom:16px;padding:12px 18px;border-radius:10px;background:'.$bgSaas.';border:1px solid '.$bordeSaas.';display:flex;align-items:center;gap:12px;">';
              echo '<span style="font-size:1.25rem;line-height:1;color:'.$colorSaas.';"><i class="fas '.$iconoSaas.'"></i></span>';
              echo '<div style="flex:1;"><span style="font-weight:700;color:'.$colorSaas.';">Mensaje de la plataforma: </span><span style="font-size:.9rem;color:var(--text);">'.htmlspecialchars($saasMensaje, ENT_QUOTES).'</span></div>';
              echo '<a href="../saas/estado.php" style="font-size:.8rem;color:'.$colorSaas.';font-weight:600;white-space:nowrap;">Ver detalles →</a>';
              echo '</div>';
          }
      }
      ?>
      <?php if (isset($_SESSION['idAdmin'])) {
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data"
             data-user-id="<?= (int)$_SESSION['idAdmin'] ?>"
             data-user-role="admin"
             data-api-key="<?= Security::escapeHtml($configFB->get('FIREBASE_API_KEY')) ?>"
             data-auth-domain="<?= Security::escapeHtml($configFB->get('FIREBASE_AUTH_DOMAIN')) ?>"
             data-project-id="<?= Security::escapeHtml($configFB->get('FIREBASE_PROJECT_ID')) ?>"
             data-messaging-sender-id="<?= Security::escapeHtml($configFB->get('FIREBASE_MESSAGING_SENDER_ID')) ?>"
             data-app-id="<?= Security::escapeHtml($configFB->get('FIREBASE_APP_ID')) ?>"
             data-database-url="<?= Security::escapeHtml($configFB->get('FIREBASE_DATABASE_URL')) ?>"
             data-vapid-key="<?= Security::escapeHtml($configFB->get('FIREBASE_VAPID_KEY')) ?>"
             class="oculto"></div>
        <script type="module">
            import { setupFirebase } from '../../../public/js/firebase/firebase.js';
            const userData = document.getElementById('firebase-user-data');
            if (userData) {
                const userId = userData.dataset.userId;
                const userRole = userData.dataset.userRole;
                if (userId && userRole) {
                    setupFirebase(userId, userRole);
                }
            }
        </script>
      <?php } ?>

      <?php if ($tourPendiente_menu): ?>
      <script>
      window.AULAPRO_TOUR = {
        tourKey: 'primeros_pasos_v1',
        completeUrl: 'controladores/comunes/tour/completar.php',
        csrfToken: <?= json_encode(Security::generateCSRFToken()) ?>,
        steps: [
          { selector: '[data-tour="estudiantes"]', title: 'Estudiantes', text: 'Gestiona la base de datos de alumnos, expedientes y matrículas.', placement: 'right' },
          { selector: '[data-tour="pagos"]', title: 'Finanzas y Pagos', text: 'Realiza un seguimiento de los recibos de matrículas, facturación y estados de cuenta.', placement: 'right' },
          { selector: '[data-tour="configuracion"]', title: 'Ajustes de Plataforma', text: 'Personaliza los parámetros globales de la aplicación y políticas del centro.', placement: 'right' }
        ]
      };
      </script>
      <?php endif; ?>
