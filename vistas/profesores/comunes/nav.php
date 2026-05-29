<?php
session_start();

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];

// _menu suffix avoids collisions with variables in pages that include this nav
$totalAlumnos_menu = contarEstudiantesDeProfesor($idProfesor);
$totalCiclos_menu = contarCiclosDeProfesor($idProfesor);
$totalMensajes_menu = contarMensajesDeProfesor($idProfesor);
$totalSinLeer_menu = contarMensajesNoLeidosProfesor($idProfesor);
$totalTfgs_menu = contarTFGsDeProfesor($idProfesor);
$totalModulos_menu = count(listarModulosDeProfesor($idProfesor));
$totalRetos_menu = count(listarRetosDeProfesor($idProfesor));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDelPagina ?? 'AulaPro Profesor' ?></title>
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
            <li><a href="../perfil/ver.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
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
            <div class="titulo-panel-sidebar">PROFESORES PANEL</div>
        </div>

        <nav class="menu-navegacion">
            <a href="../inicio/dashboard.php" class="enlace-menu <?= ($seccionActual == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../estudiantes/lista.php" class="enlace-menu <?= ($seccionActual == 'estudiantes') ? 'activo' : '' ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="texto-contador"><?= $totalAlumnos_menu ?></span>
                </a>

                <a href="../ciclos/lista.php" class="enlace-menu <?= ($seccionActual == 'ciclos') ? 'activo' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>MIS CICLOS</span>
                    <span class="texto-contador"><?= $totalCiclos_menu ?></span>
                </a>

                <a href="../modulos/lista.php" class="enlace-menu <?= ($seccionActual == 'modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-cubes"></i> <span>MÓDULOS</span>
                    <span class="texto-contador"><?= $totalModulos_menu ?></span>
                </a>

                <a href="../retos/lista.php" class="enlace-menu <?= ($seccionActual == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS</span>
                    <span class="texto-contador"><?= $totalRetos_menu ?></span>
                </a>

                <a href="../calificaciones/lista.php" class="enlace-menu <?= ($seccionActual == 'calificaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../academico/calificacionesRetos.php" class="enlace-menu <?= ($seccionActual == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="../calificaciones/tfg.php" class="enlace-menu <?= ($seccionActual == 'notas_tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-star"></i> <span>NOTAS TFG</span>
                </a>

                <a href="../academico/resultadosFinales.php" class="enlace-menu <?= ($seccionActual == 'resultados_finales') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">AULA DIGITAL</p>

                <a href="../aula/sesiones.php" class="enlace-menu <?= ($seccionActual == 'aula_sesiones') ? 'activo' : '' ?>">
                    <i class="fas fa-video"></i> <span>MIS SESIONES VIVAS</span>
                </a>

                <a href="../aula/crear.php" class="enlace-menu <?= ($seccionActual == 'aula_crear') ? 'activo' : '' ?>">
                    <i class="fas fa-plus-circle"></i> <span>CREAR SESIÓN</span>
                </a>

                <a href="../aula/asistencia.php" class="enlace-menu <?= ($seccionActual == 'aula_asistencia') ? 'activo' : '' ?>">
                    <i class="fas fa-user-check"></i> <span>ASISTENCIAS</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">COMUNICACIÓN</p>

                <a href="../anuncios/lista.php" class="enlace-menu <?= ($seccionActual == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                </a>

                <a href="../mensajes/lista.php" class="enlace-menu <?= ($seccionActual == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-paper-plane"></i> <span>MENSAJERÍA</span>
                    <span class="texto-contador <?= ($totalSinLeer_menu > 0) ? 'alerta-roja' : '' ?>"><?= $totalMensajes_menu ?></span>
                </a>

                <a href="../eventos/lista.php" class="enlace-menu <?= ($seccionActual == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="../perfil/ver.php" class="enlace-menu <?= ($seccionActual == 'perfil') ? 'activo' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
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

    <section class="contenido-principal"><?php if (isset($_SESSION['idProfesor'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idProfesor'] ?>" data-user-role="profesor" class="oculto"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>
