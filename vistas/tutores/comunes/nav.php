<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../config/Config.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/tutores.php";

$datosTutor_menu        = obtenerTutorPorId($_SESSION['idTutor']);
$nombreUsuario_menu     = $datosTutor_menu['nombreTutor'] ?? 'Tutor';
$estudiantes_menu       = listarEstudiantesPorTutor($_SESSION['idTutor']);

$totalChatNoLeidos_menu = 0;
if (FeatureGuard::check('feature_chat')) {
    require_once __DIR__ . "/../../../modelos/chat.php";
    $totalChatNoLeidos_menu = (int)chatContarNoLeidos('tutor', (int)$_SESSION['idTutor']);
}

// Active-state helper
function _nav_active_tutor($check) {
    global $seccion;
    return ($seccion === $check) ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light" data-density="regular">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?= Security::escapeHtml($titulo_pagina ?? 'AulaPro Familias') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/features/notificaciones.css" />
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>window.TWEAK_DEFAULTS={accent:"#10B981",dark:false,animation:7,density:"regular"};</script>
</head>
<body>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>
<div class="app" id="app">
  <div class="bg-mesh" aria-hidden="true">
    <span class="blob b1" style="background: rgba(16, 185, 129, 0.4)"></span>
    <span class="blob b2" style="background: rgba(59, 130, 246, 0.4)"></span>
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
      <div class="brand-mark" style="background: var(--accent)"><span></span></div>
      <div class="brand-text"><strong>AulaPro</strong><small>Portal Familias</small></div>
      <button class="collapse-btn" id="collapse">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
      </button>
    </div>

    <nav class="sidebar-nav-scroll" id="sidebar-nav">
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_tutor('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Mi Resumen</span>
        <?php if (_nav_active_tutor('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <span class="nav-section-title">MIS HIJOS</span>
      <?php foreach ($estudiantes_menu as $estudianteMenu): ?>
        <a href="../estudiantes/expediente.php?id=<?= $estudianteMenu['idEstudiante'] ?>" class="nav-item">
          <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
          <span class="nav-label"><?= Security::escapeHtml(explode(' ', $estudianteMenu['nombreEstudiante'])[0]) ?></span>
        </a>
      <?php endforeach; ?>

      <span class="nav-section-title">CENTRO</span>
      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/lista.php" class="nav-item<?= _nav_active_tutor('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Anuncios</span>
        <?php if (_nav_active_tutor('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_eventos')): ?>
      <a href="../eventos/lista.php" class="nav-item<?= _nav_active_tutor('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_tutor('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <span class="nav-section-title">GESTIÓN</span>
      <?php if (FeatureGuard::check('feature_pagos')): ?>
      <a href="../pagos/misPagos.php" class="nav-item<?= _nav_active_tutor('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label">Pagos y Recibos</span>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')) { ?>
      <a href="../mensajes/chat.php" class="nav-item<?= _nav_active_tutor('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Mensajería Centro</span>
      </a>
      <?php } ?>
    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../../cambiar_password.php" class="nav-item">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
        <span class="nav-label">Cambiar Contraseña</span>
      </a>
      <a href="../../../controladores/logout.php" class="nav-item nav-item-logout">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
        <span class="nav-label">Cerrar Sesión</span>
      </a>
    </nav>
  </aside>

  <div class="scrim"></div>

  <main class="main" data-screen-label="<?= Security::escapeHtml($titulo_pagina ?? 'Tutor') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge" style="background: rgba(16, 185, 129, 0.1); color: var(--verde);">FAMILIA</span>
        <span class="topbar-user-name"><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
      </div>

      <div class="topbar-actions" style="margin-left: auto">
        <button class="icon-btn theme-btn" id="theme">
          <span class="theme-knob"><svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8z"/></svg></span>
        </button>
        <?php if (FeatureGuard::check('feature_chat')): ?>
        <div class="notif-wrap">
          <button class="icon-btn" id="notif-btn" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            <span class="dot" id="notif-dot" data-msgs="<?= $totalChatNoLeidos_menu ?>"<?= ($totalChatNoLeidos_menu > 0) ? '' : ' hidden' ?>></span>
          </button>
          <div class="notif-panel" id="notif-panel" hidden>
            <div class="notif-panel-head">Notificaciones</div>
            <?php if ($totalChatNoLeidos_menu > 0): ?>
            <a href="../mensajes/chat.php" class="notif-item">
              <span class="notif-ico"><i class="fas fa-comment-dots"></i></span>
              <div class="notif-body">
                <span class="notif-label">Tienes <?= $totalChatNoLeidos_menu ?> mensaje(s) sin leer</span>
              </div>
            </a>
            <?php else: ?>
            <div class="notif-empty">Sin mensajes nuevos</div>
            <?php endif; ?>
            <div class="notif-footer">
              <a href="../mensajes/chat.php">Ver mensajería</a>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>
    </header>

    <?php // Las vistas de tutores definen $seccion (no $seccionActual)
    if (FeatureGuard::check('feature_chat') && ($seccion ?? '') !== 'chat'):
        $cw_rol = 'tutor';
        $cw_id = (int)$_SESSION['idTutor'];
        $cw_unreadCount = $totalChatNoLeidos_menu;
        $cw_basePath = '../../../';
        include __DIR__ . '/../../comunes/chat_widget.php';
    endif; ?>

    <div class="content">
      <?php
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data"
             data-user-id="<?= (int)$_SESSION['idTutor'] ?>"
             data-user-role="tutor"
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
