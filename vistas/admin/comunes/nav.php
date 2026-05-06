<?php
// Seguridad: Solo admin
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

// Usamos rutas raíz directas (más humano y estable sin $_SERVER)
// Esto evita errores al incluir el nav desde diferentes profundidades
$rel = "/pfc/"; 

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

// Contadores para el menú
$_nEstudiantes = contarEstudiantes();
$_nProfesores = contarProfesores();
$_nDirectores = contarDirectores();
$_nPagos = contarPagos();
$_nAnuncios = contarAnuncios();
$_nMensajes = contarReclamaciones();
$_nSinLeer = contarMensajesNoLeidosAdmin();
$_nCiclos = contarCiclos();
$_nModulos = contarModulos();
$_nRetos = contarRetos();
$_nAulas = contarAulas();
$_nInventario = contarInventario();
$_nPrestamos = contarPrestamosActivos();
$_nTFG = contarTFGsSubidos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?? 'AulaPro Admin' ?></title>
    <link rel="stylesheet" href="../../../public/css/admin.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
    <link rel="stylesheet" href="../../../public/css/notificaciones.css">
    <link rel="icon" href="../../../public/imagenes/favicon.ico">
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
            <li><a href="../../../vistas/admin/directores/perfil.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
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
            <a href="../../../vistas/admin/inicio/dashboard.php" class="enlace-menu <?= ($seccion == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-chart-line"></i> <span>DASHBOARD</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="enlace-menu <?= ($seccion == 'estudiantes') ? 'activo' : '' ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="etiqueta-contador"><?= $_nEstudiantes ?></span>
                </a>

                <a href="../../../vistas/admin/ciclos/verCiclos.php" class="enlace-menu <?= ($seccion == 'ciclos') ? 'activo' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>CICLOS FORMATIVOS</span>
                    <span class="etiqueta-contador"><?= $_nCiclos ?></span>
                </a>

                <a href="../../../vistas/admin/modulos/verModulos.php" class="enlace-menu <?= ($seccion == 'modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-book"></i> <span>MÓDULOS</span>
                    <span class="etiqueta-contador"><?= $_nModulos ?></span>
                </a>

                <a href="../../../vistas/admin/retos/verRetos.php" class="enlace-menu <?= ($seccion == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS / PROYECTOS</span>
                    <span class="etiqueta-contador"><?= $_nRetos ?></span>
                </a>

                <a href="../../../vistas/admin/academico/calificacionesModulos.php" class="enlace-menu <?= ($seccion == 'notas_modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../../../vistas/admin/academico/calificacionesRetos.php" class="enlace-menu <?= ($seccion == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="../../../vistas/admin/academico/resultadosFinales.php" class="enlace-menu <?= ($seccion == 'resultados_modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="../../../vistas/admin/pfc/verTFGs.php" class="enlace-menu <?= ($seccion == 'tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-file-pdf"></i> <span>GESTIÓN TFG</span>
                    <span class="etiqueta-contador"><?= $_nTFG ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PERSONAL Y CENTRO</p>

                <a href="../../../vistas/admin/directores/verDirectores.php" class="enlace-menu <?= ($seccion == 'directores') ? 'activo' : '' ?>">
                    <i class="fas fa-user-tie"></i> <span>DIRECTORES</span>
                    <span class="etiqueta-contador"><?= $_nDirectores ?></span>
                </a>

                <a href="../../../vistas/admin/profesores/verProfesores.php" class="enlace-menu <?= ($seccion == 'profesores') ? 'activo' : '' ?>">
                    <i class="fas fa-chalkboard-teacher"></i> <span>PROFESORES</span>
                    <span class="etiqueta-contador"><?= $_nProfesores ?></span>
                </a>

                <a href="../../../vistas/admin/pagos/verPagosGeneral.php" class="enlace-menu <?= ($seccion == 'pagos') ? 'activo' : '' ?>">
                    <i class="fas fa-wallet"></i> <span>PAGOS</span>
                    <span class="etiqueta-contador"><?= $_nPagos ?></span>
                </a>

                <a href="../../../vistas/admin/eventos/gestionEventos.php" class="enlace-menu <?= ($seccion == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>

                <a href="../../../vistas/admin/anuncios/gestionAnuncios.php" class="enlace-menu <?= ($seccion == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>AVISOS Y PUSH</span>
                    <span class="etiqueta-contador"><?= $_nAnuncios ?></span>
                </a>

                <a href="../../../vistas/admin/mensajes/lista.php" class="enlace-menu <?= ($seccion == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?= ($_nSinLeer > 0) ? 'alerta-roja' : '' ?>"><?= $_nMensajes ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">RECURSOS</p>

                <a href="../../../vistas/admin/aulas/verAulas.php" class="enlace-menu <?= ($seccion == 'aulas') ? 'activo' : '' ?>">
                    <i class="fas fa-door-open"></i> <span>AULAS</span>
                    <span class="etiqueta-contador"><?= $_nAulas ?></span>
                </a>

                <a href="../../../vistas/admin/inventario/verInventario.php" class="enlace-menu <?= ($seccion == 'inventario') ? 'activo' : '' ?>">
                    <i class="fas fa-boxes"></i> <span>INVENTARIO</span>
                    <span class="etiqueta-contador"><?= $_nInventario ?></span>
                </a>

                <a href="../../../vistas/admin/inventario/gestionarPrestamos.php" class="enlace-menu <?= ($seccion == 'prestamos') ? 'activo' : '' ?>">
                    <i class="fas fa-hand-holding"></i> <span>PRÉSTAMOS</span>
                    <span class="etiqueta-contador"><?= $_nPrestamos ?></span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="../../../vistas/admin/directores/perfil.php" class="enlace-menu <?= ($seccion == 'perfil') ? 'activo' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="../../../vistas/admin/comunes/sobreelproyecto.php" class="enlace-menu <?= ($seccion == 'creditos') ? 'activo' : '' ?>">
                    <i class="fas fa-fingerprint"></i> <span>HUELLA DIGITAL</span>
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

    <script>
    function toggleMenu() {
        var sidebar = document.getElementById('barraLateral');
        sidebar.classList.toggle('activo');
        document.body.classList.toggle('menu-abierto');
    }
    </script>

    <main class="contenido-derecha">
    <?php if (isset($_SESSION['idAdmin'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idAdmin'] ?>" data-user-role="admin" class="d-none"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>

