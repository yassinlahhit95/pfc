<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/secretarias.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../include/Cache.php";

$datosSecretaria     = obtenerSecretariaPorId($_SESSION['idSecretaria']);
$nombreUsuario_menu = $datosSecretaria['nombreSecretaria'] ?? 'Secretaria';

// Badges. total_admisiones_pendientes es idéntico al que ya cachea
// obtenerContadoresNavAdmin(), así que lo reutilizamos; total_sin_leer aquí
// filtra además "id_parent IS NULL" (solo hilos raíz), a diferencia del
// contador de admin, así que se mantiene como consulta propia (cacheada aparte).
$totalSinLeer_menu = Cache::remember('nav_secretaria_sin_leer', 60, function () {
    $con = obtenerConexion();
    $resultado = mysqli_query($con, "SELECT COUNT(*) AS n FROM reclamaciones WHERE leido=0 AND id_parent IS NULL AND ((emisor_rol='estudiante' AND idProfesor IS NULL) OR (emisor_rol='profesor' AND idEstudiante IS NULL))");
    return $resultado ? (int)(mysqli_fetch_assoc($resultado)['n'] ?? 0) : 0;
});

$totalAdmisionesPendientes_menu = obtenerContadoresNavAdmin()['total_admisiones_pendientes'];

function _nav_active_sec($check) {
    global $seccion;
    return ($seccion === $check) ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light" data-density="regular">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?= Security::escapeHtml($titulo_pagina ?? 'AulaPro Secretaría') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/features/notificaciones.css" />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../../../public/css/features/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/aula-digital.css') ?>" />
  <link rel="stylesheet" href="../../../public/css/features/chat-widget.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/chat-widget.css') ?>" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="../../../public/js/core/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/core/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/menu-contextual.js') ?>"></script>
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
</head>
<body>
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
    <div class="brand">
      <div class="brand-mark"><span></span></div>
      <div class="brand-text"><strong>AulaPro</strong><small>Secretaría</small></div>
      <button class="collapse-btn" id="collapse" aria-label="Contraer menú">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
      </button>
    </div>

    <nav class="sidebar-nav-scroll" id="sidebar-nav">

      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_sec('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Dashboard</span>
        <?php if (_nav_active_sec('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <span class="nav-section-title">GESTIÓN</span>

      <a href="../estudiantes/verEstudiantes.php" class="nav-item<?= _nav_active_sec('estudiantes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Estudiantes</span>
        <?php if (_nav_active_sec('estudiantes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../estudiantes/papelera.php" class="nav-item<?= _nav_active_sec('papelera') ?>" title="Papelera de estudiantes">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg></span>
        <span class="nav-label">Papelera</span>
        <?php if (_nav_active_sec('papelera') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../tutores/verTutores.php" class="nav-item<?= _nav_active_sec('tutores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Sistema Parental</span>
        <?php if (_nav_active_sec('tutores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_prematricula')): ?>
      <a href="../admisiones/listado.php" class="nav-item<?= _nav_active_sec('admisiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
        <span class="nav-label">Admisiones</span>
        <?php if ($totalAdmisionesPendientes_menu > 0) { ?><span class="nav-badge"><?= $totalAdmisionesPendientes_menu ?></span><?php } ?>
        <?php if (_nav_active_sec('admisiones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <span class="nav-section-title">FINANZAS</span>

      <?php if (FeatureGuard::check('feature_pagos')): ?>
      <a href="../pagos/verPagos.php" class="nav-item<?= _nav_active_sec('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label">Pagos</span>
        <?php if (_nav_active_sec('pagos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_gastos')): ?>
      <a href="../gastos/verGastos.php" class="nav-item<?= _nav_active_sec('gastos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
        <span class="nav-label">Gastos</span>
        <?php if (_nav_active_sec('gastos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <span class="nav-section-title">COMUNICACIÓN</span>

      <?php if (FeatureGuard::check('feature_mensajes')): ?>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_sec('mensajes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajería</span>
        <?php if ($totalSinLeer_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalSinLeer_menu ?></span><?php } ?>
        <?php if (_nav_active_sec('mensajes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')): ?>
      <a href="../mensajes/chat.php" class="nav-item<?= _nav_active_sec('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
        <?php if (_nav_active_sec('chat') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/gestionAnuncios.php" class="nav-item<?= _nav_active_sec('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Avisos</span>
        <?php if (_nav_active_sec('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_eventos')): ?>
      <a href="../eventos/gestionEventos.php" class="nav-item<?= _nav_active_sec('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_sec('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <span class="nav-section-title">RECURSOS</span>

      <?php if (FeatureGuard::check('feature_inventario')): ?>
      <a href="../inventario/verInventario.php" class="nav-item<?= _nav_active_sec('inventario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27,6.96 12,12.01 20.73,6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
        <span class="nav-label">Inventario</span>
        <?php if (_nav_active_sec('inventario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <a href="../inventario/gestionarPrestamos.php" class="nav-item<?= _nav_active_sec('prestamos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg></span>
        <span class="nav-label">Préstamos</span>
        <?php if (_nav_active_sec('prestamos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <span class="nav-section-title">ACADÉMICO</span>

      <a href="../calificaciones/verCalificaciones.php" class="nav-item<?= _nav_active_sec('calificaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label">Calificaciones</span>
        <?php if (_nav_active_sec('calificaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../horario/horario.php" class="nav-item<?= _nav_active_sec('horario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Horario</span>
        <?php if (_nav_active_sec('horario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_informes')): ?>
      <a href="../informes/informes.php" class="nav-item<?= _nav_active_sec('informes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
        <span class="nav-label">Informes PDF</span>
        <?php if (_nav_active_sec('informes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_landing')): ?>
      <a href="../blog/gestionBlog.php" class="nav-item<?= _nav_active_sec('blog') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/></svg></span>
        <span class="nav-label">Blog</span>
        <?php if (_nav_active_sec('blog') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../perfil/ver.php" class="nav-item">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label">Mi Perfil</span>
      </a>
      <a href="../../../controladores/logout.php" class="nav-item nav-item-logout">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
        <span class="nav-label">Cerrar Sesión</span>
      </a>
    </nav>
  </aside>

  <div class="scrim"></div>

  <main class="main" data-screen-label="<?= Security::escapeHtml($titulo_pagina ?? 'Secretaría') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu" aria-label="Menú">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge">SECRETARIA</span>
        <span class="topbar-user-name"><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
      </div>
      <div class="topbar-actions">
        <!-- Mobile Trigger -->
        <button class="icon-btn mobile-search-trigger" id="mobile-search-trigger" aria-label="Buscar">
          <svg class="search-icon-svg" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
        </button>
        <!-- Desktop Input / Mobile Modal -->
        <div class="search-backdrop" id="search-backdrop" hidden></div>
        <div class="search-wrapper" id="search-wrapper">
          <label class="search-modal-bar">
            <svg class="search-icon-svg desktop-only-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
            <input id="sys-search" class="search-modal-input" type="search" placeholder="Buscar..."
                   autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other"
                   data-url="../../../controladores/secretaria/buscar.php" />
            <button class="search-close" id="search-close" aria-label="Cerrar búsqueda">
              <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
            <kbd class="search-kbd">⌘K</kbd>
          </label>
          <ul class="search-results" id="search-results" hidden></ul>
        </div>
        <button class="icon-btn theme-btn" id="theme" aria-label="Cambiar tema">
          <span class="theme-knob"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg></span>
        </button>
      </div>
    </header>

    <?php
    $mesActual = date('m');
    if ($mesActual === '06') {
        $diasRestantes = 30 - (int)date('d');
        if ($diasRestantes >= 0) {
            echo '<div style="background-color: var(--rojo-suave, #fee2e2); color: var(--rojo); padding: 12px 16px; text-align: center; font-weight: 600; border-bottom: 1px solid var(--rojo-borde, #fecaca);">';
            echo '<i class="fas fa-clock"></i> ¡Atención! Quedan ' . $diasRestantes . ' días para el fin del periodo de pagos (30 de junio).';
            echo '</div>';
        }
    }
    ?>

    <?php // Las vistas de secretaría definen $seccion (no $seccionActual)
    if (FeatureGuard::check('feature_chat') && ($seccion ?? '') !== 'chat'):
        require_once __DIR__ . "/../../../modelos/chat.php";
        $cw_rol = 'secretaria';
        $cw_id = (int)$_SESSION['idSecretaria'];
        $cw_unreadCount = (int)chatContarNoLeidos('secretaria', $cw_id);
        $cw_basePath = '../../../';
        include __DIR__ . '/../../comunes/chat_widget.php';
    endif; ?>

    <div class="content">
