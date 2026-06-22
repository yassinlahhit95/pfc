<?php
require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
require_once __DIR__ . "/../../../modelos/tutores.php";

$datosAdmin_menu    = obtenerDirectorPorId($_SESSION['idAdmin']);
$nombreUsuario_menu = $datosAdmin_menu['nombreDirector'] ?? 'Administrador';

// Single aggregate query replaces the previous 14 individual COUNT(*) queries.
$_nav_counts = obtenerContadoresNavAdmin();
$totalEstudiantes_menu = $_nav_counts['total_estudiantes'] ?? 0;
$totalProfesores_menu  = $_nav_counts['total_profesores']  ?? 0;
$totalTutores_menu     = $_nav_counts['total_tutores']     ?? 0;
$totalDirectores_menu  = $_nav_counts['total_directores']  ?? 0;
$totalPagos_menu       = $_nav_counts['total_pagos']       ?? 0;
$totalAnuncios_menu    = $_nav_counts['total_anuncios']    ?? 0;
$totalMensajes_menu    = $_nav_counts['total_mensajes']    ?? 0;
$totalSinLeer_menu     = $_nav_counts['total_sin_leer']    ?? 0;
$totalCiclos_menu      = $_nav_counts['total_ciclos']      ?? 0;
$totalModulos_menu     = $_nav_counts['total_modulos']     ?? 0;
$totalRetos_menu       = $_nav_counts['total_retos']       ?? 0;
$totalInventario_menu  = $_nav_counts['total_inventario']  ?? 0;
$totalPrestamos_menu   = $_nav_counts['total_prestamos']   ?? 0;

// Notification panel: recent unread messages for admin (max 3)
$_notif_msgs_admin = [];
if ($totalSinLeer_menu > 0) {
    $_con_notif_a = obtenerConexion();
    $_stmt_notif_a = mysqli_prepare($_con_notif_a,
        "SELECT idReclamacion, asunto, fecha FROM reclamaciones
         WHERE leido = 0
           AND ((emisor_rol = 'estudiante' AND idProfesor IS NULL)
             OR (emisor_rol = 'profesor' AND idEstudiante IS NULL))
         ORDER BY idReclamacion DESC LIMIT 3");
    mysqli_stmt_execute($_stmt_notif_a);
    $_r_a = mysqli_stmt_get_result($_stmt_notif_a);
    while ($_row_a = mysqli_fetch_assoc($_r_a)) { $_notif_msgs_admin[] = $_row_a; }
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
  <link rel="stylesheet" href="../../../public/css/dashboard.css" />
  <link rel="stylesheet" href="../../../public/css/estilo.css" />
  <link rel="stylesheet" href="../../../public/css/notificaciones.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link rel="stylesheet" href="../../../public/css/aula-digital.css?v=<?= @filemtime(__DIR__.'/../../../public/css/aula-digital.css') ?>" />
  <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script defer src="../../../public/js/aula-digital.js?v=<?= @filemtime(__DIR__.'/../../../public/js/aula-digital.js') ?>"></script>
  <script defer src="../../../public/js/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/menu-contextual.js') ?>"></script>
  <script>window.TWEAK_DEFAULTS={accent:"#4F46E5",dark:false,animation:7,density:"regular"};</script>
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

      <!-- Dashboard -->
      <a href="../inicio/dashboard.php" class="nav-item<?= _nav_active_admin('inicio') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 10.5 12 3l9 7.5V21a1 1 0 0 1-1 1h-5v-6h-6v6H4a1 1 0 0 1-1-1V10.5z"/></svg></span>
        <span class="nav-label">Dashboard</span>
        <?php if (_nav_active_admin('inicio') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- ACADÉMICO -->
      <span class="nav-section-title">ACADÉMICO</span>

      <a href="../estudiantes/verEstudiantes.php" class="nav-item<?= _nav_active_admin('estudiantes') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Estudiantes</span>
        <?php if ($totalEstudiantes_menu > 0) { ?><span class="nav-badge"><?= $totalEstudiantes_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('estudiantes') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../ciclos/verCiclos.php" class="nav-item<?= _nav_active_admin('ciclos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg></span>
        <span class="nav-label">Ciclos Formativos</span>
        <?php if ($totalCiclos_menu > 0) { ?><span class="nav-badge"><?= $totalCiclos_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('ciclos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../modulos/verModulos.php" class="nav-item<?= _nav_active_admin('modulos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20M4 19.5A2.5 2.5 0 0 0 6.5 22H20V2H6.5A2.5 2.5 0 0 0 4 4.5v15z"/></svg></span>
        <span class="nav-label">Módulos</span>
        <?php if ($totalModulos_menu > 0) { ?><span class="nav-badge"><?= $totalModulos_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('modulos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_retos')): ?>
      <a href="../retos/verRetos.php" class="nav-item<?= _nav_active_admin('retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Retos</span>
        <?php if ($totalRetos_menu > 0) { ?><span class="nav-badge"><?= $totalRetos_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../academico/calificacionesModulos.php" class="nav-item<?= _nav_active_admin('notas_modulos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span>
        <span class="nav-label">Notas Módulos</span>
        <?php if (_nav_active_admin('notas_modulos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <?php if (FeatureGuard::check('feature_retos')): ?>
      <a href="../academico/calificacionesRetos.php" class="nav-item<?= _nav_active_admin('notas_retos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg></span>
        <span class="nav-label">Notas Retos</span>
        <?php if (_nav_active_admin('notas_retos') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <a href="../academico/calificacionesTFG.php" class="nav-item<?= _nav_active_admin('notas_tfg') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg></span>
        <span class="nav-label">Notas TFG</span>
        <?php if (_nav_active_admin('notas_tfg') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../academico/resultadosFinales.php" class="nav-item<?= _nav_active_admin('resultados_modulos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L7 17l-5-5M22 10l-11 11-2-2"/></svg></span>
        <span class="nav-label">Resultados Finales</span>
        <?php if (_nav_active_admin('resultados_modulos') !== '') { ?><span class="nav-rail"></span><?php } ?>
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
        <span class="nav-label">Asistencias</span>
        <?php if (_nav_active_admin('asistencias') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../admisiones/listado.php" class="nav-item<?= _nav_active_admin('admisiones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg></span>
        <span class="nav-label">Admisiones</span>
        <?php if (_nav_active_admin('admisiones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- PERSONAL -->
      <span class="nav-section-title">PERSONAL</span>

      <a href="../directores/verDirectores.php" class="nav-item<?= _nav_active_admin('directores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
        <span class="nav-label">Directores</span>
        <?php if ($totalDirectores_menu > 0) { ?><span class="nav-badge"><?= $totalDirectores_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('directores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../profesores/verProfesores.php" class="nav-item<?= _nav_active_admin('profesores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Profesores</span>
        <?php if ($totalProfesores_menu > 0) { ?><span class="nav-badge"><?= $totalProfesores_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('profesores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../tutores/verTutores.php" class="nav-item<?= _nav_active_admin('tutores') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
        <span class="nav-label">Sistema Parental</span>
        <?php if ($totalTutores_menu > 0) { ?><span class="nav-badge"><?= $totalTutores_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('tutores') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <!-- COMUNICACIÓN -->
      <span class="nav-section-title">COMUNICACIÓN</span>

      <?php if (FeatureGuard::check('feature_anuncios')): ?>
      <a href="../anuncios/gestionAnuncios.php" class="nav-item<?= _nav_active_admin('anuncios') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 1 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/></svg></span>
        <span class="nav-label">Avisos</span>
        <?php if ($totalAnuncios_menu > 0) { ?><span class="nav-badge"><?= $totalAnuncios_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('anuncios') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_mensajes')): ?>
      <a href="../mensajes/lista.php" class="nav-item<?= _nav_active_admin('reclamaciones') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span>
        <span class="nav-label">Mensajeria</span>
        <?php if ($totalMensajes_menu > 0) { ?><span class="nav-badge<?= ($totalSinLeer_menu > 0) ? ' nav-badge-alert' : '' ?>"><?= $totalMensajes_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('reclamaciones') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>
      <?php endif; ?>

      <?php if (FeatureGuard::check('feature_chat')): ?>
      <a href="../chat/index.php" class="nav-item<?= _nav_active_admin('chat') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <span class="nav-label">Chat</span>
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
      <a href="../pagos/verPagosGeneral.php" class="nav-item<?= _nav_active_admin('pagos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg></span>
        <span class="nav-label">Pagos</span>
        <?php if ($totalPagos_menu > 0) { ?><span class="nav-badge"><?= $totalPagos_menu ?></span><?php } ?>
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

      <?php if (FeatureGuard::check('feature_inventario')): ?>
      <a href="../inventario/verInventario.php" class="nav-item<?= _nav_active_admin('inventario') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27,6.96 12,12.01 20.73,6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg></span>
        <span class="nav-label">Inventario</span>
        <?php if ($totalInventario_menu > 0) { ?><span class="nav-badge"><?= $totalInventario_menu ?></span><?php } ?>
        <?php if (_nav_active_admin('inventario') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../inventario/gestionarPrestamos.php" class="nav-item<?= _nav_active_admin('prestamos') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 11V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v0M14 10V4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v2M10 10.5V6a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v8"/><path d="M18 8a2 2 0 1 1 4 0v6a8 8 0 0 1-8 8h-2c-2.8 0-4.5-.86-5.99-2.34l-3.6-3.6a2 2 0 0 1 2.83-2.82L7 15"/></svg></span>
        <span class="nav-label">Préstamos</span>
        <?php if ($totalPrestamos_menu > 0) { ?><span class="nav-badge"><?= $totalPrestamos_menu ?></span><?php } ?>
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

      <a href="../configuracion/configuracion.php" class="nav-item<?= _nav_active_admin('configuracion') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg></span>
        <span class="nav-label">Configuración</span>
        <?php if (_nav_active_admin('configuracion') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../rgpd/index.php" class="nav-item<?= _nav_active_admin('rgpd') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
        <span class="nav-label">RGPD</span>
        <?php if (_nav_active_admin('rgpd') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

      <a href="../saas/estado.php" class="nav-item<?= _nav_active_admin('saas_estado') ?>">
        <span class="nav-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/><circle cx="12" cy="10" r="3"/></svg></span>
        <span class="nav-label">Estado SaaS</span>
        <?php if (_nav_active_admin('saas_estado') !== '') { ?><span class="nav-rail"></span><?php } ?>
      </a>

    </nav>

    <nav class="sidebar-bottom-nav">
      <a href="../directores/verDirectores.php" class="nav-item">
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

  <main class="main" data-screen-label="<?= Security::escapeHtml($titulo_pagina ?? 'Admin') ?>">
    <header class="topbar">
      <button class="icon-btn menu-btn" id="menu" aria-label="Menú">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor"><path d="M4 4h6v6H4zM14 4h6v6h-6zM4 14h6v6H4zM14 14h6v6h-6z"/></svg>
      </button>
      <div class="topbar-user">
        <span class="role-badge">ADMIN</span>
        <span class="topbar-user-name"><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
      </div>
      <div class="search-wrap">
        <label class="searchbar">
          <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m21 21-4.3-4.3M11 18a7 7 0 1 0 0-14 7 7 0 0 0 0 14z"/></svg>
          <input id="search" placeholder="Buscar..." autocomplete="new-password"
                 data-url="../../../controladores/admin/buscar.php" />
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
            <?php if (!empty($_notif_msgs_admin)): ?>
            <div class="notif-group-title">Mensajes sin leer</div>
            <?php foreach ($_notif_msgs_admin as $_m_a): ?>
            <a href="../mensajes/lista.php" class="notif-item">
              <span class="notif-ico"><i class="fas fa-envelope"></i></span>
              <div class="notif-body">
                <span class="notif-label"><?= Security::escapeHtml($_m_a['asunto']) ?></span>
                <span class="notif-time"><?= date('d/m H:i', strtotime($_m_a['fecha'])) ?></span>
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
      <?php
      // SaaS platform message banner — shown on every admin page
      if (!isset($FeatureGuardLoaded)) {
          @include_once __DIR__ . '/../../../include/FeatureGuard.php';
          $FeatureGuardLoaded = true;
      }
      if (class_exists('FeatureGuard')) {
          if (FeatureGuard::isSuspended()) {
              $__suspMsg = FeatureGuard::getSuspensionMessage() ?: 'Esta instancia ha sido suspendida por la plataforma SaaS. Contacta con el proveedor.';
              echo '<div style="position:fixed;inset:0;z-index:9999;background:rgba(15,23,42,.92);display:flex;align-items:center;justify-content:center;padding:24px;">';
              echo '<div style="max-width:520px;width:100%;background:#fff;border-radius:16px;padding:36px 32px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.35);">';
              echo '<div style="font-size:3rem;margin-bottom:12px;">🔒</div>';
              echo '<h2 style="margin:0 0 12px;color:#dc2626;font-size:1.4rem;">Acceso Suspendido</h2>';
              echo '<p style="color:#374151;font-size:.95rem;line-height:1.6;margin:0 0 24px;">' . htmlspecialchars($__suspMsg, ENT_QUOTES) . '</p>';
              echo '<a href="/vistas/admin/saas/estado.php" style="display:inline-block;padding:10px 22px;background:#6366f1;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;font-size:.9rem;">Ver estado de la plataforma</a>';
              echo '</div></div>';
          }
          $_saas_nav_msg  = FeatureGuard::getMessage();
          $_saas_nav_type = FeatureGuard::getMessageType();
          if ($_saas_nav_msg) {
              $__colors = [
                  'info'         => ['#3b82f6','#eff6ff','#dbeafe','ℹ️'],
                  'warning'      => ['#d97706','#fffbeb','#fde68a','⚠️'],
                  'error'        => ['#dc2626','#fef2f2','#fecaca','🚨'],
                  'subscription' => ['#7c3aed','#f5f3ff','#ddd6fe','💳'],
                  'activation'   => ['#0369a1','#f0f9ff','#bae6fd','🔑'],
              ];
              [$__c,$__bg,$__bd,$__icon] = $__colors[$_saas_nav_type] ?? $__colors['info'];
              echo '<div style="margin-bottom:16px;padding:12px 18px;border-radius:10px;background:'.$__bg.';border:1px solid '.$__bd.';display:flex;align-items:center;gap:12px;">';
              echo '<span style="font-size:1.25rem;line-height:1;">'.$__icon.'</span>';
              echo '<div style="flex:1;"><span style="font-weight:700;color:'.$__c.';">Mensaje de la plataforma: </span><span style="font-size:.9rem;color:#374151;">'.htmlspecialchars($_saas_nav_msg, ENT_QUOTES).'</span></div>';
              echo '<a href="../saas/estado.php" style="font-size:.8rem;color:'.$__c.';font-weight:600;white-space:nowrap;">Ver detalles →</a>';
              echo '</div>';
          }
      }
      ?>
      <?php if (isset($_SESSION['idAdmin'])) {
          $configFB = Config::getInstance();
      ?>
        <div id="firebase-user-data" 
             data-user-id="<?= $_SESSION['idAdmin'] ?>" 
             data-user-role="admin" 
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
