<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../include/AssetMin.php";
require_once __DIR__ . "/../../../config/Config.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/chat.php";
require_once __DIR__ . "/../../../modelos/justificacionesFalta.php";
require_once __DIR__ . "/../../../modelos/notificaciones.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Cache.php";
require_once __DIR__ . "/../../../modelos/tours.php";

$idProfesor             = $_SESSION['idProfesor'];
$datosProfesor_menu     = obtenerProfesorPorId($idProfesor);
$nombreUsuario_menu     = $datosProfesor_menu['nombreProfesor'] ?? 'Profesor';
$tourPendiente_menu     = !tourEstaCompletado((int)$idProfesor, 'profesor', 'primeros_pasos_v1');

// _menu suffix avoids collisions with variables in pages that include this nav
// contarMensajesNoLeidosProfesor() y chatContarNoLeidos() ya se cachean 10s
// dentro de sus propias funciones; el resto se agrupa aquí en un solo bloque
// cacheado 60s por profesor para no repetir 6 consultas en cada carga de página.
$navCounts_menu = Cache::remember("nav_profesor_counts_{$idProfesor}", 60, function () use ($idProfesor) {
    return [
        'alumnos' => contarEstudiantesDeProfesor($idProfesor),
        'ciclos'  => contarCiclosDeProfesor($idProfesor),
        'mensajes'=> contarMensajesDeProfesor($idProfesor),
        'tfgs'    => contarTFGsDeProfesor($idProfesor),
        'modulos' => count(listarModulosDeProfesor($idProfesor)),
        'retos'   => count(listarRetosDeProfesor($idProfesor)),
        'justificaciones' => count(listarJustificacionesPendientesPorProfesor($idProfesor)),
    ];
});
$totalAlumnos_menu      = $navCounts_menu['alumnos'];
$totalCiclos_menu       = $navCounts_menu['ciclos'];
$totalMensajes_menu     = $navCounts_menu['mensajes'];
$totalSinLeer_menu      = contarMensajesNoLeidosProfesor($idProfesor);
$totalTfgs_menu         = $navCounts_menu['tfgs'];
$totalModulos_menu      = $navCounts_menu['modulos'];
$totalRetos_menu        = $navCounts_menu['retos'];
$totalJustificaciones_menu = $navCounts_menu['justificaciones'];
$totalChatNoLeidos_menu = chatContarNoLeidos('profesor', $idProfesor);

// Notification panel: recent unread messages for profesor (max 3)
$mensajesNotifProf = [];
if ($totalSinLeer_menu > 0) {
    $mensajesNotifProf = Cache::remember("nav_profesor_notif_{$idProfesor}", 10, function () use ($idProfesor) {
        $conexionNotif = obtenerConexion();
        $stmtNotif = mysqli_prepare($conexionNotif,
            "SELECT idReclamacion, asunto, fecha FROM reclamaciones
             WHERE leido = 0 AND idProfesor = ? AND emisor_rol != 'profesor'
             ORDER BY idReclamacion DESC LIMIT 3");
        mysqli_stmt_bind_param($stmtNotif, 'i', $idProfesor);
        mysqli_stmt_execute($stmtNotif);
        $resultNotif = mysqli_stmt_get_result($stmtNotif);
        $out = [];
        while ($filaNotif = mysqli_fetch_assoc($resultNotif)) { $out[] = $filaNotif; }
        return $out;
    });
}

// Notificaciones genéricas (asignación de ciclo/módulo, etc.) — se marcan
// leídas por AJAX al abrir la campana (dashboard-shell.js), no aquí; esto
// solo lee el estado actual para pintarlas.
$totalNotifGenericas_menu = contarNotificacionesNoLeidas($idProfesor, 'profesor');
$notifGenericas_menu = $totalNotifGenericas_menu > 0 ? listarNotificacionesNoLeidas($idProfesor, 'profesor', 3) : [];

// Notificaciones de Aula Digital (entrega enviada por un estudiante) —
// aula_notificaciones llevaba tiempo recibiendo este evento pero nunca se
// mostraba en ningún sitio (ver modelos/aula.php).
$totalNotifAula_menu = contarNotificacionesAulaNoLeidas($idProfesor, 'profesor');
$notifAula_menu = $totalNotifAula_menu > 0 ? listarNotificacionesAulaNoLeidas($idProfesor, 'profesor', 3) : [];

$totalNotifCampana_menu = $totalSinLeer_menu + $totalNotifGenericas_menu + $totalNotifAula_menu;

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
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
  <title><?= Security::escapeHtml($tituloDelPagina ?? 'AulaPro Profesor') ?></title>
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
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
  <script defer src="../../../public/js/core/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/core/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/core/menu-contextual.js') ?>"></script>
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

      <?php if (FeatureGuard::check('feature_retos')): ?>
      <a href="../retos/lista.php" class="nav-item<?= _nav_active_prof('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Retos</span>
        <?php if ($totalRetos_menu > 0) { ?><span class="nav-badge"><?= $totalRetos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../calificaciones/lista.php" data-tour="calificaciones" class="nav-item<?= _nav_active_prof('calificaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label">Notas Módulos</span>
        <?php if (_nav_active_prof('calificaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/calificacionesRetos.php" class="nav-item<?= _nav_active_prof('notas_retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Notas Retos</span>
        <?php if (_nav_active_prof('notas_retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_subida_tfg')) { ?>
      <a href="../calificaciones/tfg.php" class="nav-item<?= _nav_active_prof('notas_tfg') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <span class="nav-label">Notas TFG</span>
        <?php if (_nav_active_prof('notas_tfg') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php } ?>

      <?php if (FeatureGuard::check('feature_fct')) { ?>
      <a href="../fct/lista.php" class="nav-item<?= _nav_active_prof('fct') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
        <span class="nav-label">FCT</span>
        <?php if (_nav_active_prof('fct') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php } ?>

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

      <a href="../asistencias/registrar.php" data-tour="asistencias" class="nav-item<?= _nav_active_prof('asistencias') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></span>
        <span class="nav-label">Asistencia</span>
        <?php if (_nav_active_prof('asistencias') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../asistencias/justificaciones.php" class="nav-item<?= _nav_active_prof('justificaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14,2 14,8 20,8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></span>
        <span class="nav-label">Justificaciones</span>
        <?php if ($totalJustificaciones_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalJustificaciones_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('justificaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
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

      <a href="../aula/tareas.php" data-tour="tareas" class="nav-item<?= _nav_active_prof('aula_tareas') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="m9 14 2 2 4-4"/></svg></span>
        <span class="nav-label">Tareas</span>
        <?php if (_nav_active_prof('aula_tareas') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- COMUNICACIÓN -->
      <span class="nav-section-title">COMUNICACIÓN</span>

      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/lista.php" class="nav-item<?= _nav_active_prof('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Anuncios</span>
        <?php if (_nav_active_prof('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_mensajes')): ?>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_prof('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajería</span>
        <?php if ($totalMensajes_menu > 0) { ?><span class="nav-badge<?= ($totalSinLeer_menu > 0) ? ' nav-badge-alert' : '' ?>"><?= $totalMensajes_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('reclamaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')) { ?>
      <a href="../chat/index.php" class="nav-item<?= _nav_active_prof('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
        <?php if ($totalChatNoLeidos_menu > 0) { ?><span class="nav-badge nav-badge-alert"><?= $totalChatNoLeidos_menu ?></span><?php } ?>
        <?php if (_nav_active_prof('chat') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php } ?>

      <?php if (FeatureGuard::check('feature_eventos')): ?>
      <a href="../eventos/lista.php" class="nav-item<?= _nav_active_prof('eventos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 11l19-9-9 19-2-8-8-2z"/></svg></span>
        <span class="nav-label">Eventos</span>
        <?php if (_nav_active_prof('eventos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

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
        <?php if (!empty($_SESSION['esTutor'])): ?>
          <span class="role-badge" style="background:var(--accent);color:#fff;">TUTOR</span>
        <?php else: ?>
          <span class="role-badge">PROFESOR</span>
        <?php endif; ?>
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
                   data-url="../../../controladores/profesores/buscar.php" />
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
        <div class="notif-wrap">
          <button class="icon-btn" id="notif-btn" aria-label="Notificaciones">
            <svg viewBox="0 0 24 24" width="19" height="19" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg>
            <span class="dot" id="notif-dot" data-msgs="<?= (int)$totalNotifCampana_menu ?>"<?= ($totalNotifCampana_menu > 0) ? '' : ' hidden' ?>></span>
          </button>
          <div class="notif-panel" id="notif-panel" hidden
               data-notif-ids="<?= Security::escapeHtml(implode(',', array_column($notifGenericas_menu, 'idNotificacion'))) ?>"
               data-aula-notif-ids="<?= Security::escapeHtml(implode(',', array_column($notifAula_menu, 'idNotificacion'))) ?>">
            <div class="notif-panel-head">Notificaciones</div>
            <?php if (!empty($notifGenericas_menu)): ?>
            <div class="notif-group-title">Novedades</div>
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
            <?php endif; ?>
            <?php if (!empty($notifAula_menu)): ?>
            <div class="notif-group-title">Aula Digital</div>
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
            <?php endif; ?>
            <?php if (!empty($mensajesNotifProf)): ?>
            <div class="notif-group-title">Mensajes sin leer</div>
            <?php foreach ($mensajesNotifProf as $msgNotif): ?>
            <a href="../mensajes/lista.php" class="notif-item">
              <span class="notif-ico"><i class="fas fa-envelope"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($msgNotif['asunto']) ?></span>
                <span class="notif-time"><?= date('d/m H:i', strtotime($msgNotif['fecha'])) ?></span>
              </div>
              <span class="notif-badge-new">Nuevo</span>
            </a>
            <?php endforeach; ?>
            <?php endif; ?>
            <?php if (empty($notifGenericas_menu) && empty($notifAula_menu) && empty($mensajesNotifProf)): ?>
            <div class="notif-empty">Sin mensajes nuevos</div>
            <?php endif; ?>
            <div class="notif-footer">
              <a href="../mensajes/lista.php">Ver mensajería</a>
            </div>
          </div>
        </div>
      </div>
    </header>

    <?php if (FeatureGuard::check('feature_chat') && ($seccionActual ?? '') !== 'chat'):
        $cw_rol = 'profesor';
        $cw_id = (int)$_SESSION['idProfesor'];
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
          { selector: '[data-tour="asistencias"]', title: 'Asistencia', text: 'Pasa lista por módulo y fecha en pocos clics.', placement: 'right' },
          { selector: '[data-tour="tareas"]', title: 'Tareas', text: 'Publica tareas y corrige las entregas de tus alumnos.', placement: 'right' },
          { selector: '[data-tour="calificaciones"]', title: 'Calificaciones', text: 'Registra las notas de módulos y retos.', placement: 'right' },
          { selector: '[data-tour="busqueda"]', title: 'Búsqueda global', text: 'Pulsa aquí (o Ctrl/Cmd+K) para buscar alumnos, módulos, mensajes y más.', placement: 'bottom' }
        ]
      };
      </script>
      <?php endif; ?>
      <?php if (isset($_SESSION['idProfesor'])) {
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data" 
             data-user-id="<?= Security::escapeHtml($_SESSION['idProfesor']) ?>" 
             data-user-role="profesor" 
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
