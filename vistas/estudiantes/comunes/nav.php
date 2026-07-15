<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../config/Config.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/chat.php";
require_once __DIR__ . "/../../../include/Cache.php";

$idEstudiante           = $_SESSION['idEstudiante'];
$datosEstudiante_menu   = obtenerEstudiantePorId($idEstudiante);
$idCicloEst_menu        = $datosEstudiante_menu['idCiclo'] ?? 0;
$nombreUsuario_menu     = $datosEstudiante_menu['nombreEstudiante'] ?? 'Estudiante';

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
$_notif_msgs = [];
if ($totalSinLeer_menu > 0) {
    $_notif_msgs = Cache::remember("nav_estudiante_notif_{$idEstudiante}", 10, function () use ($idEstudiante) {
        $_con_notif = obtenerConexion();
        $_stmt_notif = mysqli_prepare($_con_notif,
            "SELECT idReclamacion, asunto, fecha FROM reclamaciones
             WHERE idEstudiante = ? AND leido = 0 AND emisor_rol != 'estudiante'
             ORDER BY idReclamacion DESC LIMIT 3");
        mysqli_stmt_bind_param($_stmt_notif, 'i', $idEstudiante);
        mysqli_stmt_execute($_stmt_notif);
        $r = mysqli_stmt_get_result($_stmt_notif);
        $out = [];
        while ($row = mysqli_fetch_assoc($r)) { $out[] = $row; }
        return $out;
    });
}
// Notification panel: 3 most recent pagos
$_notif_pagos = Cache::remember("nav_estudiante_pagos_{$idEstudiante}", 30, function () use ($idEstudiante) {
    return array_slice(listarPagosPorEstudiante($idEstudiante), 0, 3);
});

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
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/features/notificaciones.css" />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../../../public/css/features/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/aula-digital.css') ?>" />
  <link rel="stylesheet" href="../../../public/css/features/chat-widget.css?v=<?= @filemtime(__DIR__.'/../../../public/css/features/chat-widget.css') ?>" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="../../../public/js/core/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/core/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/menu-contextual.js') ?>"></script>
</head>
<body>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>
<div class="app" id="app">
  <div class="bg-mesh" aria-hidden="true">
    <span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span>
  </div>

  <aside class="sidebar" id="main-sidebar">
    <script>
      try {
        if (JSON.parse(localStorage.getItem("aulapro_tweaks_v1")).sidebarCollapsed) {
          var s = document.getElementById("main-sidebar");
          s.classList.add("collapsed");
          s.style.setProperty("transition", "none", "important");
          setTimeout(function() { s.style.removeProperty("transition"); }, 150);
        }
      } catch (e) {}
    </script>
    <div class="brand">
      <div class="brand-mark"><span></span></div>
      <div class="brand-text"><strong>AulaPro</strong><small>Campus Suite</small></div>
      <button class="collapse-btn" id="collapse" aria-label="Contraer menú">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
      </button>
    </div>

    <nav class="sidebar-nav-scroll" id="sidebar-nav">

      <!-- Inicio -->
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_est('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Inicio</span>
        <?php if (_nav_active_est('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- MIS ESTUDIOS -->
      <span class="nav-section-title">MIS ESTUDIOS</span>

      <?php if (FeatureGuard::check('feature_retos')): ?>
      <a href="../retos/lista.php" class="nav-item<?= _nav_active_est('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Retos</span>
        <?php if ($totalRetos_menu > 0) { ?><span class="nav-badge"><?= $totalRetos_menu ?></span><?php } ?>
        <?php if (_nav_active_est('retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../calificaciones/lista.php" class="nav-item<?= (in_array($seccionActual ?? '', ['calificaciones', 'notas_retos', 'resultados_finales'])) ? ' active' : '' ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label">Calificaciones</span>
        <?php if (in_array($seccionActual ?? '', ['calificaciones', 'notas_retos', 'resultados_finales'])) { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_subida_tfg') && ($datosEstudiante_menu['anioEstudio'] ?? '') !== '1º') { ?>
      <a href="../pfc/subir.php" class="nav-item<?= _nav_active_est('tfg') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <span class="nav-label">Mi TFG</span>
        <?php if (_nav_active_est('tfg') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php } ?>

      <a href="../horario/horario.php" class="nav-item<?= _nav_active_est('horario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Horario</span>
        <?php if (_nav_active_est('horario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- AULA DIGITAL -->
      <span class="nav-section-title">AULA DIGITAL</span>

      <a href="../aula/sesiones.php" class="nav-item<?= _nav_active_est('aula_sesiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>
        <span class="nav-label">Aula Digital</span>
        <?php if (_nav_active_est('aula_sesiones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../aula/recursos.php" class="nav-item<?= _nav_active_est('aula_recursos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Recursos</span>
        <?php if (_nav_active_est('aula_recursos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../aula/favoritos.php" class="nav-item<?= _nav_active_est('aula_favoritos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
        <span class="nav-label">Favoritos</span>
        <?php if (_nav_active_est('aula_favoritos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../aula/tareas.php" class="nav-item<?= _nav_active_est('aula_tareas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg></span>
        <span class="nav-label">Tareas</span>
        <?php if (_nav_active_est('aula_tareas') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../aula/mis_entregas.php" class="nav-item<?= _nav_active_est('aula_entregas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="m9 15 2 2 4-4"/></svg></span>
        <span class="nav-label">Mis Entregas</span>
        <?php if (_nav_active_est('aula_entregas') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- PORTAL -->
      <span class="nav-section-title">PORTAL</span>

      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/lista.php" class="nav-item<?= _nav_active_est('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Anuncios</span>
        <?php if ($totalAnuncios_menu > 0) { ?><span class="nav-badge"><?= $totalAnuncios_menu ?></span><?php } ?>
        <?php if (_nav_active_est('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_mensajes')): ?>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_est('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajería</span>
        <?php if ($totalMensajes_menu > 0) { ?><span class="nav-badge<?= ($totalSinLeer_menu > 0) ? ' nav-badge-alert' : '' ?>"><?= $totalMensajes_menu ?></span><?php } ?>
        <?php if (_nav_active_est('reclamaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')) { ?>
      <a href="../chat/index.php" class="nav-item<?= _nav_active_est('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
        <?php if ($totalChatNoLeidos_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalChatNoLeidos_menu ?></span><?php } ?>
        <?php if (_nav_active_est('chat') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php } ?>

      <?php if (FeatureGuard::check('feature_pagos')): ?>
      <a href="../pagos/lista.php" class="nav-item<?= _nav_active_est('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label">Pagos</span>
        <?php if ($totalPagos_menu > 0) { ?><span class="nav-badge"><?= $totalPagos_menu ?></span><?php } ?>
        <?php if (_nav_active_est('pagos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_eventos')): ?>
      <a href="../eventos/lista.php" class="nav-item<?= _nav_active_est('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_est('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../perfil/ver.php" class="nav-item<?= _nav_active_est('perfil') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label">Mi Perfil</span>
        <?php if (_nav_active_est('perfil') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <a href="../../../controladores/logout.php" class="nav-item nav-item-logout">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
        <span class="nav-label">Cerrar Sesión</span>
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
        <div class="search-wrapper" id="search-wrapper">
          <label class="search-modal-bar">
            <svg class="search-icon-svg desktop-only-icon" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
            <input id="sys-search" class="search-modal-input" type="search" placeholder="Buscar..."
                   autocomplete="new-password" autocorrect="off" autocapitalize="off" spellcheck="false"
                   data-lpignore="true" data-1p-ignore="true" data-form-type="other"
                   data-url="../../../controladores/estudiantes/buscar.php" />
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

        <!-- Notification bell -->
        <div class="notif-wrap">
          <button class="icon-btn" id="notif-btn" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            <span class="dot" id="notif-dot" data-msgs="<?= (int)$totalSinLeer_menu ?>"<?= ($totalSinLeer_menu > 0) ? '' : ' hidden' ?>></span>
          </button>
          <div class="notif-panel" id="notif-panel" hidden>
            <div class="notif-panel-head">Notificaciones</div>

            <?php if (!empty($_notif_msgs)): ?>
            <div class="notif-group-title">Mensajes sin leer</div>
            <?php foreach ($_notif_msgs as $_m): ?>
            <a href="../mensajes/detalles.php?id=<?= (int)$_m['idReclamacion'] ?>" class="notif-item">
              <span class="notif-ico"><i class="fas fa-envelope"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($_m['asunto']) ?></span>
                <span class="notif-time"><?= date('d/m H:i', strtotime($_m['fecha'])) ?></span>
              </div>
              <span class="notif-badge-new">Nuevo</span>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($_notif_pagos)): ?>
            <div class="notif-group-title">Pagos recientes</div>
            <?php foreach ($_notif_pagos as $_p): ?>
            <a href="../pagos/lista.php" class="notif-item notif-item--pago" data-pid="<?= (int)$_p['idPago'] ?>">
              <span class="notif-ico"><i class="fas fa-credit-card"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($_p['tipoPago']) ?> — <?= number_format((float)$_p['monto'], 2) ?>€</span>
                <span class="notif-time"><?= !empty($_p['fechaPago']) ? date('d/m/Y', strtotime($_p['fechaPago'])) : '' ?></span>
              </div>
              <span class="notif-badge-new notif-pago-new">Nuevo</span>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>

            <?php if (empty($_notif_msgs) && empty($_notif_pagos)): ?>
            <div class="notif-empty">Sin notificaciones nuevas</div>
            <?php endif; ?>

            <div class="notif-footer">
              <a href="../mensajes/lista.php">Ver mensajería</a>
              <a href="../pagos/lista.php">Ver pagos</a>
            </div>
          </div>
        </div><!-- /notif-wrap -->

      </div>
    </header>

    <?php if (FeatureGuard::check('feature_chat') && ($seccionActual ?? '') !== 'chat'):
        require_once __DIR__ . "/../../../modelos/chat.php";
        $cw_rol = 'estudiante';
        $cw_id = (int)$_SESSION['idEstudiante'];
        $cw_unreadCount = (int)chatContarNoLeidos('estudiante', $cw_id);
        $cw_basePath = '../../../';
        include __DIR__ . '/../../comunes/chat_widget.php';
    endif; ?>

    <div class="content">
      <?php if (isset($_SESSION['idEstudiante'])) { 
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data" 
             data-user-id="<?= Security::escapeHtml($_SESSION['idEstudiante']) ?>" 
             data-user-role="estudiante" 
             data-api-key="<?= $configFB->get('FIREBASE_API_KEY') ?>"
             data-auth-domain="<?= $configFB->get('FIREBASE_AUTH_DOMAIN') ?>"
             data-project-id="<?= $configFB->get('FIREBASE_PROJECT_ID') ?>"
             data-messaging-sender-id="<?= $configFB->get('FIREBASE_MESSAGING_SENDER_ID') ?>"
             data-app-id="<?= $configFB->get('FIREBASE_APP_ID') ?>"
             data-database-url="<?= $configFB->get('FIREBASE_DATABASE_URL') ?>"
             data-vapid-key="<?= $configFB->get('FIREBASE_VAPID_KEY') ?>"
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
