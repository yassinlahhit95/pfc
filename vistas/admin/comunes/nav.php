<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header class="navbar-superior">
    <div class="logo-navbar-contenedor">
        <img src="../../../public/imagenes/aulapro.png" alt="Logo" class="logo-navbar logo-navbar-png">
        <img src="../../../public/imagenes/aulapro.jpeg" alt="Logo" class="logo-navbar logo-navbar-jpeg">
    </div>
    <div class="menu-superior">
        <ul class="navbar-nav">
            <li><a href="../directores/verDirectores.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
            <li><a href="../../../controladores/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>
</header>

<div class="contenedor-principal">
    <aside class="barra-lateral" id="barraLateral">
        <div class="cabecera-menu">
            <img src="../../../public/imagenes/aulapro.png" alt="Logo" class="sidebar-logo sidebar-logo-png">
            <img src="../../../public/imagenes/aulapro.jpeg" alt="Logo" class="sidebar-logo sidebar-logo-jpeg">
            <div class="titulo-panel-sidebar">ADMIN PANEL</div>
        </div>

        <nav class="menu-navegacion">
            <a href="../inicio/dashboard.php" class="enlace-menu <?= ($seccion == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-chart-line"></i> <span>DASHBOARD</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../estudiantes/verEstudiantes.php" class="enlace-menu <?= ($seccion == 'estudiantes') ? 'activo' : '' ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="texto-contador"><?= $totalEstudiantes_menu ?></span>
                </a>

                <a href="../ciclos/verCiclos.php" class="enlace-menu <?= ($seccion == 'ciclos') ? 'activo' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>CICLOS FORMATIVOS</span>
                    <span class="texto-contador"><?= $totalCiclos_menu ?></span>
                </a>

                <a href="../modulos/verModulos.php" class="enlace-menu <?= ($seccion == 'modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-book"></i> <span>MÓDULOS</span>
                    <span class="texto-contador"><?= $totalModulos_menu ?></span>
                </a>

                <a href="../retos/verRetos.php" class="enlace-menu <?= ($seccion == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS / PROYECTOS</span>
                    <span class="texto-contador"><?= $totalRetos_menu ?></span>
                </a>

                <a href="../academico/calificacionesModulos.php" class="enlace-menu <?= ($seccion == 'notas_modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../academico/calificacionesRetos.php" class="enlace-menu <?= ($seccion == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="../academico/calificacionesTFG.php" class="enlace-menu <?= ($seccion == 'notas_tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-star"></i> <span>NOTAS TFG</span>
                </a>

                <a href="../academico/resultadosFinales.php" class="enlace-menu <?= ($seccion == 'resultados_modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PERSONAL Y CENTRO</p>

                <a href="../directores/verDirectores.php" class="enlace-menu <?= ($seccion == 'directores') ? 'activo' : '' ?>">
                    <i class="fas fa-user-tie"></i> <span>DIRECTORES</span>
                    <span class="texto-contador"><?= $totalDirectores_menu ?></span>
                </a>

                <a href="../profesores/verProfesores.php" class="enlace-menu <?= ($seccion == 'profesores') ? 'activo' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i> <span>PROFESORES</span>
                    <span class="texto-contador"><?= $totalProfesores_menu ?></span>
                </a>

                <a href="../pagos/verPagosGeneral.php" class="enlace-menu <?= ($seccion == 'pagos') ? 'activo' : '' ?>">
                    <i class="fas fa-wallet"></i> <span>PAGOS</span>
                    <span class="texto-contador"><?= $totalPagos_menu ?></span>
                </a>

                <a href="../eventos/gestionEventos.php" class="enlace-menu <?= ($seccion == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>

                <a href="../anuncios/gestionAnuncios.php" class="enlace-menu <?= ($seccion == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>AVISOS Y PUSH</span>
                    <span class="texto-contador"><?= $totalAnuncios_menu ?></span>
                </a>

                <a href="../mensajes/lista.php" class="enlace-menu <?= ($seccion == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="texto-contador <?= ($totalSinLeer_menu > 0) ? 'alerta-roja' : '' ?>"><?= $totalMensajes_menu ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">RECURSOS</p>

                <a href="../inventario/verInventario.php" class="enlace-menu <?= ($seccion == 'inventario') ? 'activo' : '' ?>">
                    <i class="fas fa-boxes"></i> <span>INVENTARIO</span>
                    <span class="texto-contador"><?= $totalInventario_menu ?></span>
                </a>

                <a href="../inventario/gestionarPrestamos.php" class="enlace-menu <?= ($seccion == 'prestamos') ? 'activo' : '' ?>">
                    <i class="fas fa-hand-holding"></i> <span>PRÉSTAMOS</span>
                    <span class="texto-contador"><?= $totalPrestamos_menu ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">AULA DIGITAL</p>

                <a href="../aula/sesiones.php" class="enlace-menu <?= ($seccion == 'aula_sesiones') ? 'activo' : '' ?>">
                    <i class="fas fa-video"></i> <span>SESIONES VIVAS</span>
                </a>

                <a href="../aula/asistencia.php" class="enlace-menu <?= ($seccion == 'aula_asistencia') ? 'activo' : '' ?>">
                    <i class="fas fa-user-check"></i> <span>ASISTENCIAS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="https://yassin.agency" target="_blank" class="enlace-menu">
                    <i class="fas fa-home"></i> <span>PÁGINA INICIO</span>
                </a>
                <a href="../../../controladores/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>CERRAR SESIÓN</span>
                </a>
                <div class="info-sistema-footer">
                    &copy; <?= date('Y') ?> Yassin Lahhit
                </div>
            </div>
        </nav>
    </aside>

    <section class="contenido-principal"><?php if (isset($_SESSION['idAdmin'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idAdmin'] ?>" data-user-role="admin" class="oculto"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>
