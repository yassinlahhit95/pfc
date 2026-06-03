<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/directores.php";
$datosAdmin_menu = obtenerDirectorPorId($_SESSION['idAdmin']);
$nombreUsuario_menu = $datosAdmin_menu['nombreDirector'] ?? 'Administrador';
$totalEstudiantes_menu = contarEstudiantes();
$totalProfesores_menu = contarProfesores();
$totalDirectores_menu = contarDirectores();
$totalPagos_menu = contarPagosRealizados();
$totalAnuncios_menu = contarAnuncios();
$totalMensajes_menu = contarMensajesParaAdmin();
$totalSinLeer_menu = contarMensajesNoLeidosAdmin();
$totalCiclos_menu = contarCiclos();
$totalModulos_menu = contarModulos();
$totalRetos_menu = contarRetos();
$totalInventario_menu = contarInventario();
$totalPrestamos_menu = contarPrestamosActivos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?? 'AulaPro Admin' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/estilo.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
    <link rel="stylesheet" href="../../../public/css/notificaciones.css">
    <link rel="shortcut icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/aula-digital.css?v=<?= @filemtime(__DIR__."/../../../public/css/aula-digital.css") ?>">
    <link rel="stylesheet" href="../../../public/css/sidebar.css?v=<?= @filemtime(__DIR__."/../../../public/css/sidebar.css") ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../public/js/aula-digital.js?v=<?= @filemtime(__DIR__."/../../../public/js/aula-digital.js") ?>"></script>
    <script src="../../../public/js/menu-contextual.js?v=<?= @filemtime(__DIR__."/../../../public/js/menu-contextual.js") ?>"></script>
</head>
<body>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>

<header class="navbar-superior">
    <div class="logo-navbar-contenedor">
        <img src="../../../public/imagenes/aulapro.jpeg" alt="AulaPro" class="logo-navbar">
    </div>
    <div class="menu-superior">
        <ul class="navbar-nav">
            <li><a href="../directores/verDirectores.php"><svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg> Mi Perfil</a></li>
            <li><a href="../../../controladores/logout.php"><svg class="ico" aria-hidden="true"><use href="#ic-sign-out-alt"/></svg> Salir</a></li>
        </ul>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">
        <svg class="ico" aria-hidden="true"><use href="#ic-bars"/></svg>
    </button>
</header>

<div class="contenedor-principal">
    <aside class="barra-lateral" id="barraLateral">
        <div class="cabecera-menu">
            <div class="sb-traffic" aria-hidden="true"><span></span><span></span><span></span></div>
            <div class="sb-brand">
                <img src="../../../public/imagenes/aulapro.jpeg" alt="AulaPro" class="sidebar-logo">
                <!-- <span class="sb-brand-name">AulaPro</span> -->
                <button type="button" class="sb-more" id="sbMore" aria-label="Opciones" aria-haspopup="true" aria-expanded="false">
                    <svg class="ico" aria-hidden="true"><use href="#ic-ellipsis-v"/></svg>
                </button>
            </div>
            <div class="titulo-panel-sidebar">
                <svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg>
                <span><?= Security::escapeHtml($nombreUsuario_menu) ?></span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="../inicio/dashboard.php" class="enlace-menu <?= ($seccion == 'inicio') ? 'activo' : '' ?>">
                <svg class="ico" aria-hidden="true"><use href="#ic-chart-line"/></svg> <span>DASHBOARD</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../estudiantes/verEstudiantes.php" class="enlace-menu <?= ($seccion == 'estudiantes') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-user-graduate"/></svg> <span>ESTUDIANTES</span>
                    <span class="texto-contador"><?= $totalEstudiantes_menu ?></span>
                </a>

                <a href="../ciclos/verCiclos.php" class="enlace-menu <?= ($seccion == 'ciclos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-layer-group"/></svg> <span>CICLOS FORMATIVOS</span>
                    <span class="texto-contador"><?= $totalCiclos_menu ?></span>
                </a>

                <a href="../modulos/verModulos.php" class="enlace-menu <?= ($seccion == 'modulos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-book"/></svg> <span>MÓDULOS</span>
                    <span class="texto-contador"><?= $totalModulos_menu ?></span>
                </a>

                <a href="../retos/verRetos.php" class="enlace-menu <?= ($seccion == 'retos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-tasks"/></svg> <span>RETOS / PROYECTOS</span>
                    <span class="texto-contador"><?= $totalRetos_menu ?></span>
                </a>

                <a href="../academico/calificacionesModulos.php" class="enlace-menu <?= ($seccion == 'notas_modulos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-graduation-cap"/></svg> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../academico/calificacionesRetos.php" class="enlace-menu <?= ($seccion == 'notas_retos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-tasks"/></svg> <span>NOTAS RETOS</span>
                </a>

                <a href="../academico/calificacionesTFG.php" class="enlace-menu <?= ($seccion == 'notas_tfg') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-star"/></svg> <span>NOTAS TFG</span>
                </a>

                <a href="../academico/resultadosFinales.php" class="enlace-menu <?= ($seccion == 'resultados_modulos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-check-double"/></svg> <span>RESULTADOS FINALES</span>
                </a>

            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PERSONAL Y CENTRO</p>

                <a href="../directores/verDirectores.php" class="enlace-menu <?= ($seccion == 'directores') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-user-tie"/></svg> <span>DIRECTORES</span>
                    <span class="texto-contador"><?= $totalDirectores_menu ?></span>
                </a>

                <a href="../profesores/verProfesores.php" class="enlace-menu <?= ($seccion == 'profesores') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-chalkboard-teacher"/></svg> <span>PROFESORES</span>
                    <span class="texto-contador"><?= $totalProfesores_menu ?></span>
                </a>

                <a href="../pagos/verPagosGeneral.php" class="enlace-menu <?= ($seccion == 'pagos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-wallet"/></svg> <span>PAGOS</span>
                    <span class="texto-contador"><?= $totalPagos_menu ?></span>
                </a>

                <a href="../eventos/gestionEventos.php" class="enlace-menu <?= ($seccion == 'eventos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-calendar-alt"/></svg> <span>EVENTOS</span>
                </a>

                <a href="../anuncios/gestionAnuncios.php" class="enlace-menu <?= ($seccion == 'anuncios') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-bullhorn"/></svg> <span>AVISOS Y PUSH</span>
                    <span class="texto-contador"><?= $totalAnuncios_menu ?></span>
                </a>

                <a href="../mensajes/lista.php" class="enlace-menu <?= ($seccion == 'reclamaciones') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-envelope"/></svg> <span>MENSAJERÍA</span>
                    <span class="texto-contador <?= ($totalSinLeer_menu > 0) ? 'alerta-roja' : '' ?>"><?= $totalMensajes_menu ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">RECURSOS</p>

                <a href="../inventario/verInventario.php" class="enlace-menu <?= ($seccion == 'inventario') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-boxes"/></svg> <span>INVENTARIO</span>
                    <span class="texto-contador"><?= $totalInventario_menu ?></span>
                </a>

                <a href="../inventario/gestionarPrestamos.php" class="enlace-menu <?= ($seccion == 'prestamos') ? 'activo' : '' ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-hand-holding"/></svg> <span>PRÉSTAMOS</span>
                    <span class="texto-contador"><?= $totalPrestamos_menu ?></span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="https://yassin.agency" target="_blank" class="enlace-menu">
                    <svg class="ico" aria-hidden="true"><use href="#ic-home"/></svg> <span>PÁGINA INICIO</span>
                </a>
                <a href="../../../controladores/logout.php" class="enlace-menu">
                    <svg class="ico" aria-hidden="true"><use href="#ic-sign-out-alt"/></svg> <span>CERRAR SESIÓN</span>
                </a>
                <div class="info-sistema-footer">
                    &copy; <?= date('Y') ?> Yassin Lahhit
                </div>
            </div>
        </nav>
    </aside>

    <div class="sb-menu" id="sbMenu" role="menu" aria-label="Opciones">
        <a href="../directores/verDirectores.php" class="sb-menu-item" role="menuitem"><svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg> Mi Perfil</a>
        <a href="../../../controladores/logout.php" class="sb-menu-item salir" role="menuitem"><svg class="ico" aria-hidden="true"><use href="#ic-sign-out-alt"/></svg> Cerrar Sesión</a>
    </div>

    <section class="contenido-principal"><?php if (isset($_SESSION['idAdmin'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idAdmin'] ?>" data-user-role="admin" class="oculto"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>
