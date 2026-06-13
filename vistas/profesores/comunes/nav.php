<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor             = $_SESSION['idProfesor'];
$datosProfesor_menu     = obtenerProfesorPorId($idProfesor);
$nombreUsuario_menu     = $datosProfesor_menu['nombreProfesor'] ?? 'Profesor';

// _menu suffix avoids collisions with variables in pages that include this nav
$totalAlumnos_menu   = contarEstudiantesDeProfesor($idProfesor);
$totalCiclos_menu    = contarCiclosDeProfesor($idProfesor);
$totalMensajes_menu  = contarMensajesDeProfesor($idProfesor);
$totalSinLeer_menu   = contarMensajesNoLeidosProfesor($idProfesor);
$totalTfgs_menu      = contarTFGsDeProfesor($idProfesor);
$totalModulos_menu   = count(listarModulosDeProfesor($idProfesor));
$totalRetos_menu     = count(listarRetosDeProfesor($idProfesor));

// Notification panel: recent unread messages for profesor (max 3)
$_notif_msgs_prof = [];
if ($totalSinLeer_menu > 0) {
    $_con_notif_p = obtenerConexion();
    $_stmt_notif_p = mysqli_prepare($_con_notif_p,
        "SELECT idReclamacion, asunto, fecha FROM reclamaciones
         WHERE leido = 0 AND idProfesor = ? AND emisor_rol != 'profesor'
         ORDER BY idReclamacion DESC LIMIT 3");
    mysqli_stmt_bind_param($_stmt_notif_p, 'i', $idProfesor);
    mysqli_stmt_execute($_stmt_notif_p);
    $_r_p = mysqli_stmt_get_result($_stmt_notif_p);
    while ($_row_p = mysqli_fetch_assoc($_r_p)) { $_notif_msgs_prof[] = $_row_p; }
}

// Active-state helper
function _nav_active_prof($check) {
    global $seccionActual;
    return ($seccionActual === $check) ? ' active' : '';
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light" data-density="regular">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= Security::escapeHtml($tituloDelPagina ?? 'AulaPro Profesor') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/notificaciones.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../../../public/css/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/aula-digital.css') ?>" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="../../../public/js/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/menu-contextual.js') ?>"></script>
</head>
<body>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>
<div class="app" id="app">
  <div class="bg-mesh" aria-hidden="true">
    <span class="blob b1"></span><span class="blob b2"></span><span class="blob b3"></span>
  </div>

  <aside class="sidebar">
    <div class="brand">
      <div class="brand-mark"><span></span></div>
      <div class="brand-text"><strong>AulaPro</strong><small>Campus Suite</small></div>
      <button class="collapse-btn" id="collapse" aria-label="Contraer menú">
        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m15 6-6 6 6 6"/></svg>
      </button>
    </div>

    <nav class="sidebar-nav-scroll" id="sidebar-nav">

      <!-- Inicio -->
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_prof('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Inicio</span>
        <?php if (_nav_active_prof('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- ACADÉMICO -->
      <span class="nav-section-title">ACADÉMICO</span>

      <a href="../estudiantes/lista.php" class="nav-item<?= _nav_active_prof('estudiantes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Estudiantes</span>
        <?php if ($totalAlumnos_menu > 0) { ?><span class="nav-badge"><?= $totalAlumnos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('estudiantes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../ciclos/lista.php" class="nav-item<?= _nav_active_prof('ciclos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></span>
        <span class="nav-label">Ciclos</span>
        <?php if ($totalCiclos_menu > 0) { ?><span class="nav-badge"><?= $totalCiclos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('ciclos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../modulos/lista.php" class="nav-item<?= _nav_active_prof('modulos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/></svg></span>
        <span class="nav-label">Módulos</span>
        <?php if ($totalModulos_menu > 0) { ?><span class="nav-badge"><?= $totalModulos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('modulos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../retos/lista.php" class="nav-item<?= _nav_active_prof('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Retos</span>
        <?php if ($totalRetos_menu > 0) { ?><span class="nav-badge"><?= $totalRetos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../calificaciones/lista.php" class="nav-item<?= _nav_active_prof('calificaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label">Notas Módulos</span>
        <?php if (_nav_active_prof('calificaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/calificacionesRetos.php" class="nav-item<?= _nav_active_prof('notas_retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Notas Retos</span>
        <?php if (_nav_active_prof('notas_retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../calificaciones/tfg.php" class="nav-item<?= _nav_active_prof('notas_tfg') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <span class="nav-label">Notas TFG</span>
        <?php if (_nav_active_prof('notas_tfg') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/resultadosFinales.php" class="nav-item<?= _nav_active_prof('resultados_finales') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L7 17l-5-5M22 10l-11 11-2-2"/></svg></span>
        <span class="nav-label">Resultados Finales</span>
        <?php if (_nav_active_prof('resultados_finales') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../horario/horario.php" class="nav-item<?= _nav_active_prof('horario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
        <span class="nav-label">Cuadro Horario</span>
        <?php if (_nav_active_prof('horario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- AULA DIGITAL -->
      <span class="nav-section-title">AULA DIGITAL</span>

      <a href="../aula/sesiones.php" class="nav-item<?= _nav_active_prof('aula_sesiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></span>
        <span class="nav-label">Aula Digital</span>
        <?php if (_nav_active_prof('aula_sesiones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../aula/index.php" class="nav-item<?= _nav_active_prof('aula_recursos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Recursos</span>
        <?php if (_nav_active_prof('aula_recursos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- COMUNICACIÓN -->
      <span class="nav-section-title">COMUNICACIÓN</span>

      <a href="../anuncios/lista.php" class="nav-item<?= _nav_active_prof('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Anuncios</span>
        <?php if (_nav_active_prof('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_prof('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajería</span>
        <?php if ($totalMensajes_menu > 0) { ?><span class="nav-badge<?= ($totalSinLeer_menu > 0) ? ' nav-badge-alert' : '' ?>"><?= $totalMensajes_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('reclamaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../chat/index.php" class="nav-item<?= _nav_active_prof('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
        <?php if (_nav_active_prof('chat') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../eventos/lista.php" class="nav-item<?= _nav_active_prof('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_prof('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../perfil/ver.php" class="nav-item<?= _nav_active_prof('perfil') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label">Mi Perfil</span>
        <?php if (_nav_active_prof('perfil') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <a href="../../../controladores/logout.php" class="nav-item nav-item-logout">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg></span>
        <span class="nav-label">Cerrar Sesión</span>
      </a>
    </nav>
  </aside>

  <div class="scrim"></div>

  <main class="main" data-screen-label="<?= Security::escapeHtml($tituloDelPagina ?? 'Profesor') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu" aria-label="Menú">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge">PROFESOR</span>
        <span class="topbar-user-name"><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
      </div>
      <div class="search-wrap">
        <label class="searchbar">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
          <input id="search" placeholder="Buscar..." autocomplete="off"
                 data-url="../../../controladores/profesores/buscar.php" />
          <kbd>⌘K</kbd>
        </label>
        <ul class="search-results" id="search-results" hidden></ul>
      </div>
      <div class="topbar-actions">
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
            <?php if (!empty($_notif_msgs_prof)): ?>
            <div class="notif-group-title">Mensajes sin leer</div>
            <?php foreach ($_notif_msgs_prof as $_m_p): ?>
            <a href="../mensajes/lista.php" class="notif-item">
              <span class="notif-ico"><i class="fas fa-envelope"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($_m_p['asunto']) ?></span>
                <span class="notif-time"><?= date('d/m H:i', strtotime($_m_p['fecha'])) ?></span>
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
        <button class="avatar-btn" aria-label="Cuenta">
          <span class="ava"><?= strtoupper(substr($nombreUsuario_menu, 0, 1)) . strtoupper(substr(explode(' ', trim($nombreUsuario_menu))[1] ?? '', 0, 1)) ?></span>
          <span class="presence"></span>
        </button>
      </div>
    </header>
    <div class="content">
      <?php if (isset($_SESSION['idProfesor'])) { 
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data" 
             data-user-id="<?= Security::escapeHtml($_SESSION['idProfesor']) ?>" 
             data-user-role="profesor" 
             data-api-key="<?= $configFB->get('FIREBASE_API_KEY') ?>"
             data-auth-domain="<?= $configFB->get('FIREBASE_AUTH_DOMAIN') ?>"
             data-project-id="<?= $configFB->get('FIREBASE_PROJECT_ID') ?>"
             data-messaging-sender-id="<?= $configFB->get('FIREBASE_MESSAGING_SENDER_ID') ?>"
             data-app-id="<?= $configFB->get('FIREBASE_APP_ID') ?>"
             data-database-url="<?= $configFB->get('FIREBASE_DATABASE_URL') ?>"
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
