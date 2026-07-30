<?php
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/FPSystem.php";
require_once __DIR__ . "/../../../include/AssetMin.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/chat.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Cache.php";
require_once __DIR__ . "/../../../modelos/tours.php";
        require_once __DIR__ . "/../../../modelos/chat.php";
$idEstudiante           = $_SESSION['idEstudiante'];
$datosEstudiante_menu   = obtenerEstudiantePorId($idEstudiante);
$idCicloEst_menu        = $datosEstudiante_menu['idCiclo'] ?? 0;
$nombreUsuario_menu     = $datosEstudiante_menu['nombreEstudiante'] ?? 'Estudiante';
$tourPendiente_menu     = !tourEstaCompletado((int)$idEstudiante, 'estudiante', 'primeros_pasos_v1');

// contarMensajesNoLeidosEstudiante() y chatContarNoLeidos() ya se cachean 10s
// dentro de sus propias funciones; el resto se agrupa aquí en un solo bloque
// cacheado 60s por estudiante para no repetir 4 consultas en cada carga de página.
$navCounts_menu = Cache::remember("nav_estudiante_counts_{$idEstudiante}", 60, function () use ($idEstudiante, $idCicloEst_menu) {
    return [
        'mensajes' => count(listarMensajesDeEstudiante($idEstudiante)),
        'anuncios' => count(listarAnunciosPorRol('estudiantes')),
        'pagos'    => contarPagosEstudiante($idEstudiante),
        'retos'    => count(listarRetosPorCiclo($idCicloEst_menu)),
    ];
});
$totalMensajes_menu     = $navCounts_menu['mensajes'];
$totalSinLeer_menu      = contarMensajesNoLeidosEstudiante($idEstudiante);
$totalAnuncios_menu     = $navCounts_menu['anuncios'];
$totalPagos_menu        = $navCounts_menu['pagos'];
$totalRetos_menu        = $navCounts_menu['retos'];
$totalChatNoLeidos_menu = chatContarNoLeidos('estudiante', $idEstudiante);

// Notification panel: recent unread messages (max 3)
$mensajesNotif = [];
if ($totalSinLeer_menu > 0) {
    $mensajesNotif = Cache::remember("nav_estudiante_notif_{$idEstudiante}", 10, function () use ($idEstudiante) {
        $conexionNotif = obtenerConexion();
        $stmtNotif = mysqli_prepare($conexionNotif,
            "SELECT idReclamacion, asunto, fecha FROM reclamaciones
             WHERE idEstudiante = ? AND leido = 0 AND emisor_rol != 'estudiante'
             ORDER BY idReclamacion DESC LIMIT 3");
        mysqli_stmt_bind_param($stmtNotif, 'i', $idEstudiante);
        mysqli_stmt_execute($stmtNotif);
        $resultNotif = mysqli_stmt_get_result($stmtNotif);
        $out = [];
        while ($filaNotif = mysqli_fetch_assoc($resultNotif)) { $out[] = $filaNotif; }
        return $out;
    });
}
// Notification panel: 3 most recent pagos
$pagosNotif = Cache::remember("nav_estudiante_pagos_{$idEstudiante}", 30, function () use ($idEstudiante) {
    return array_slice(listarPagosPorEstudiante($idEstudiante), 0, 3);
});

// Notificaciones genéricas (nota publicada, etc.) — mismo patrón que en
// profesores/comunes/nav.php: se marcan leídas por AJAX al abrir la campana.
$totalNotifGenericas_menu = contarNotificacionesNoLeidas($idEstudiante, 'estudiante');
$notifGenericas_menu = $totalNotifGenericas_menu > 0 ? listarNotificacionesNoLeidas($idEstudiante, 'estudiante', 3) : [];

// Notificaciones de Aula Digital (tarea nueva, sesión nueva, archivo subido,
// entrega corregida) — aula_notificaciones llevaba tiempo recibiendo estos
// eventos pero nunca se mostraban en ningún sitio (ver modelos/aula.php).
$totalNotifAula_menu = contarNotificacionesAulaNoLeidas($idEstudiante, 'estudiante');
$notifAula_menu = $totalNotifAula_menu > 0 ? listarNotificacionesAulaNoLeidas($idEstudiante, 'estudiante', 3) : [];

$totalNotifCampana_menu = $totalSinLeer_menu + $totalNotifGenericas_menu + $totalNotifAula_menu;

// Active-state helper
function _nav_active_est($check) {
    global $seccionActual;
    return ($seccionActual === $check) ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light" data-density="regular">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?= Security::escapeHtml($tituloDelPagina ?? 'AulaPro Estudiante') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/css/bundle.min.css?v=<?= filemtime($__bundleCss) ?>" />
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/features/notificaciones.css" />
  <link rel="stylesheet" href="../../../public/css/features/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/aula-digital.css') ?>" />
  <link rel="stylesheet" href="../../../public/css/features/onboarding-tour.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/onboarding-tour.css') ?>" />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha384-iw3OoTErCYJJB9mCa8LNS2hbsQ7M3C0EpIsO/H5+EGAkPGc6rk+V8i04oW/K5xq0" crossorigin="anonymous" />
  <link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/chat-widget.css') ?>" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha384-1H217gwSVyLSIfaLxHbE7dRb3v4mYCKbpQvzx0cegeju1MVsGrX5xXxAvs/HgeFs" crossorigin="anonymous"></script>
  <script defer src="../../../public/js/core/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/core/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/menu-contextual.js') ?>"></script>
</head>
<body>
<a href="#main-content" class="skip-link">Saltar al contenido principal</a>
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
    <nav class="sidebar-nav-scroll" id="sidebar-nav">

      <!-- Inicio -->
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_est('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Ini      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_est('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
        <span class="nav-label"><?= __('home', 'Inicio') ?></span>
      </a>

      <!-- MIS ESTUDIOS -->
      <span class="nav-section-title"><?= __('mis_estudios', 'MIS ESTUDIOS') ?></span>

      <a href="../retos/lista.php" class="nav-item<?= _nav_active_est('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label"><?= __('challenges', 'Retos') ?></span>
      </a>
      <a href="../calificaciones/lista.php" class="nav-item<?= (in_array($seccionActual ?? '', ['calificaciones', 'notas_retos', 'resultados_finales'])) ? ' active' : '' ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label"><?= __('grades', 'Calificaciones') ?></span>
      </a>

      <a href="../pfc/subir.php" class="nav-item<?= _nav_active_est('tfg') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <span class="nav-label"><?= __('my_tfg', 'Mi TFG') ?></span>
      </a>
      <a href="../horario/horario.php" class="nav-item<?= _nav_active_est('horario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label"><?= __('schedule', 'Horario') ?></span>
      </a>

      <a href="../asistencias/lista.php" class="nav-item<?= _nav_active_est('asistencias') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2 2V5a2 2 0 0 1 2-2h11"/></svg></span>
        <span class="nav-label"><?= __('my_attendance', 'Mis Faltas') ?></span>
      </a>

      <a href="../fct/diario.php" class="nav-item<?= _nav_active_est('fct_diario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
        <span class="nav-label"><?= __('my_fct_diary', 'Mi Diario FCT') ?></span>
      </a>
      <!-- AULA DIGITAL -->
      <span class="nav-section-title"><?= __('digital_classroom', 'AULA DIGITAL') ?></span>

      <a href="../aula/sesiones.php" class="nav-item<?= _nav_active_est('aula_sesiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>
        <span class="nav-label"><?= __('digital_classroom', 'Aula Digital') ?></span>
      </a>

      <a href="../aula/recursos.php" data-tour="recursos" class="nav-item<?= _nav_active_est('aula_recursos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label"><?= __('resources', 'Recursos') ?></span>
      </a>

      <a href="../aula/favoritos.php" class="nav-item<?= _nav_active_est('aula_favoritos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
        <span class="nav-label"><?= __('favorites', 'Favoritos') ?></span>
      </a>

      <a href="../aula/tareas.php" class="nav-item<?= _nav_active_est('aula_tareas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg></span>
        <span class="nav-label"><?= __('tasks', 'Tareas') ?></span>
      </a>

      <a href="../aula/mis_entregas.php" data-tour="entregas" class="nav-item<?= _nav_active_est('aula_entregas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg></span>
        <span class="nav-label"><?= __('my_submissions', 'Mis Entregas') ?></span>
      </a>

      <!-- PORTAL -->
      <span class="nav-section-title"><?= __('portal', 'PORTAL') ?></span>

      <a href="../anuncios/lista.php" class="nav-item<?= _nav_active_est('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label"><?= __('announcements', 'Anuncios') ?></span>
      </a>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_est('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label"><?= __('messaging', 'Mensajería') ?></span>
      </a>
      <a href="../chat/index.php" class="nav-item<?= _nav_active_est('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label"><?= __('chat', 'Chat') ?></span>
      </a>
      <a href="../pagos/lista.php" data-tour="pagos" class="nav-item<?= _nav_active_est('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label"><?= __('payments', 'Pagos') ?></span>
      </a>
      <a href="../eventos/lista.php" class="nav-item<?= _nav_active_est('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
        <span class="nav-label">Eventos</span>
      </a>
    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../../ayuda.php" class="nav-item<?= ($seccionActual ?? '') === 'ayuda' ? ' active' : '' ?>">
        <span class="nav-ico"><i class="fas fa-question-circle" style="font-size:1.15rem;"></i></span>
        <span class="nav-label"><?= __('help_center', 'Centro de Ayuda') ?></span>
      </a>
      <a href="../perfil/ver.php" class="nav-item<?= _nav_active_est('perfil') ?>">
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

  <main class="main" data-screen-label="<?= Security::escapeHtml($tituloDelPagina ?? 'Estudiante') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu" aria-label="Menú">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge">ALUMNO</span>
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
                   data-url="../../../controladores/estudiantes/buscar.php" />
            <button class="search-close" id="search-close" aria-label="Cerrar búsqueda">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <kbd class="search-kbd">⌘K</kbd>
          </label>
          <ul class="search-results" id="search-results" hidden></ul>
        </div>
        <!-- Language Switcher -->
        <div class="lang-wrap" style="position:relative; display:inline-block; margin-right:8px;">
          <form action="../../../controladores/cambiar_idioma.php" method="POST" id="formLanguageStudent" style="margin:0;">
             <select name="lang" onchange="document.getElementById('formLanguageStudent').submit();" style="padding:5px 8px; border-radius:8px; border:1.5px solid var(--border); font-size:.85rem; background:var(--bg-card); color:var(--text); cursor:pointer; font-weight:600; outline:none; font-family:inherit;">
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

        <!-- Notification bell -->
        <div class="notif-wrap">
          <button class="icon-btn" id="notif-btn" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            <span class="dot" id="notif-dot" data-msgs="<?= (int)$totalNotifCampana_menu ?>"<?= ($totalNotifCampana_menu > 0) ? '' : ' hidden' ?>></span>
          </button>
          <div class="notif-panel" id="notif-panel" hidden
               data-notif-ids="<?= Security::escapeHtml(implode(',', array_column($notifGenericas_menu, 'idNotificacion'))) ?>"
               data-aula-notif-ids="<?= Security::escapeHtml(implode(',', array_column($notifAula_menu, 'idNotificacion'))) ?>">
            <div class="notif-panel-head">Notificaciones</div>

            <div class="notif-group-title">Novedades</div>
            <?php if (!empty($notifGenericas_menu)): ?>
              <?php foreach ($notifGenericas_menu as $genNotif): ?>
                <a href="<?= Security::escapeHtml($genNotif['url'] ?: '#') ?>" class="notif-item">
                  <span class="notif-ico"><i class="fas fa-bell"></i></span>
                  <div class="notif-body">
                    <span class="notif-label"><?= Security::escapeHtml($genNotif['mensaje']) ?></span>
                    <span class="notif-time"><?= date('d/m H:i', strtotime($genNotif['fechaCreacion'])) ?></span>
                  </div>
                  <span class="notif-badge-new">Nuevo</span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="notif-empty">Sin novedades</div>
            <?php endif; ?>

            <div class="notif-group-title">Aula Digital</div>
            <?php if (!empty($notifAula_menu)): ?>
              <?php foreach ($notifAula_menu as $aulaNotif): ?>
                <a href="<?= Security::escapeHtml($aulaNotif['url']) ?>" class="notif-item">
                  <span class="notif-ico"><i class="fas fa-chalkboard"></i></span>
                  <div class="notif-body">
                    <span class="notif-label"><?= Security::escapeHtml($aulaNotif['titulo']) ?></span>
                    <span class="notif-time"><?= date('d/m H:i', strtotime($aulaNotif['fechaCreacion'])) ?></span>
                  </div>
                  <span class="notif-badge-new">Nuevo</span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="notif-empty">Sin notificaciones de aula</div>
            <?php endif; ?>

            <div class="notif-group-title">Mensajes sin leer</div>
            <?php if (!empty($mensajesNotif)): ?>
              <?php foreach ($mensajesNotif as $msgNotif): ?>
                <a href="../mensajes/detalles.php?id=<?= (int)$msgNotif['idReclamacion'] ?>" class="notif-item">
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

            <div class="notif-group-title">Pagos recientes</div>
            <?php if (!empty($pagosNotif)): ?>
              <?php foreach ($pagosNotif as $pagoNotif): ?>
                <a href="../pagos/lista.php" class="notif-item notif-item--pago" data-pid="<?= (int)$pagoNotif['idPago'] ?>">
                  <span class="notif-ico"><i class="fas fa-credit-card"></i></span>
                  <div class="notif-body">
                    <span class="notif-label"><?= Security::escapeHtml($pagoNotif['tipoPago']) ?> — <?= number_format((float)$pagoNotif['monto'], 2) ?>€</span>
                    <span class="notif-time"><?= !empty($pagoNotif['fechaPago']) ? date('d/m/Y', strtotime($pagoNotif['fechaPago'])) : '' ?></span>
                  </div>
                  <span class="notif-badge-new notif-pago-new">Nuevo</span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="notif-empty">Sin pagos recientes</div>
            <?php endif; ?>
            <div class="notif-footer">
              <a href="../mensajes/lista.php">Ver mensajería</a>
              <a href="../pagos/lista.php">Ver pagos</a>
            </div>
          </div>
        </div><!-- /notif-wrap -->

      </div>
     </header>
     <?php
     if (FeatureGuard::check('feature_chat') && ($seccion ?? '') !== 'chat'):
         $cw_rol = 'estudiante';
         $cw_id = (int)$_SESSION['idEstudiante'];
         $cw_unreadCount = (int)chatContarNoLeidos('estudiante', $cw_id);
         $cw_basePath = '../../../';
         include __DIR__ . '/../../comunes/chat_widget.php';
     endif; ?>

    <div class="content" id="main-content" tabindex="-1">
      <script>
      window.AULAPRO_TOUR = {
        tourKey: 'primeros_pasos_v1',
        completeUrl: 'controladores/comunes/tour/completar.php',
        csrfToken: <?= json_encode(Security::generateCSRFToken()) ?>,
        steps: [
          { selector: '[data-tour="recursos"]', title: 'Recursos', text: 'Consulta los materiales que tus profesores han subido a cada módulo.', placement: 'right' },
          { selector: '[data-tour="entregas"]', title: 'Mis Entregas', text: 'Entrega tus tareas y revisa las calificaciones que recibas.', placement: 'right' },
          { selector: '[data-tour="pagos"]', title: 'Pagos', text: 'Consulta tus recibos y sube el comprobante cuando pagues.', placement: 'right' },
          { selector: '[data-tour="busqueda"]', title: 'Búsqueda global', text: 'Pulsa aquí (o Ctrl/Cmd+K) para buscar módulos, tareas, pagos y más.', placement: 'bottom' }
        ]
      };
      </script>
       <?php
       $configFB = Config::getInstance();
       ?>
        <div id="firebase-user-data" 
             data-user-id="<?= Security::escapeHtml($_SESSION['idEstudiante']) ?>" 
             data-user-role="estudiante" 
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