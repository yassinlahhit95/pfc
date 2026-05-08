<?php
if (empty($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

// Root paths: evita errores al incluir nav desde diferentes profundidades
$rel = "/pfc/";

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idEst = $_SESSION['idEstudiante'];

$mensajes = count(listarMensajesDeEstudiante($idEst));
$sinLeer = contarMensajesNoLeidosEstudiante($idEst);
$anuncios = count(listarAnunciosPorRol('estudiantes'));
$pagos = contarPagosEstudiante($idEst);
$retos = count(listarCalificacionesRetoPorEstudiante($idEst));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDelPagina ?? 'AulaPro Estudiante' ?></title>
    <link rel="stylesheet" href="<?= $rel ?>public/css/admin.css">
    <link rel="stylesheet" href="<?= $rel ?>public/css/responsive.css">
    <link rel="stylesheet" href="<?= $rel ?>public/css/notificaciones.css">
    <link rel="icon" href="<?= $rel ?>public/imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<header class="navbar-superior">
    <div class="logo-navbar-contenedor">
        <img src="<?= $rel ?>public/imagenes/aulapro.png" alt="Logo" class="logo-navbar logo-navbar-png">
        <img src="<?= $rel ?>public/imagenes/aulapro.jpeg" alt="Logo" class="logo-navbar logo-navbar-jpeg">
    </div>
    <div class="menu-superior">
        <ul class="navbar-nav">
            <li><a href="<?= $rel ?>vistas/estudiantes/perfil/ver.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
            <li><a href="<?= $rel ?>controladores/logout.php"><i class="fas fa-sign-out-alt"></i> Salir</a></li>
        </ul>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">
        <i class="fas fa-bars"></i>
    </button>
</header>

<div class="contenedor-principal">
    <aside class="barra-lateral" id="barraLateral">
        <div class="cabecera-menu">
            <img src="<?= $rel ?>public/imagenes/aulapro.png" alt="Logo" class="sidebar-logo sidebar-logo-png">
            <img src="<?= $rel ?>public/imagenes/aulapro.jpeg" alt="Logo" class="sidebar-logo sidebar-logo-jpeg">
            <div class="titulo-panel-sidebar">ESTUDIANTES PANEL</div>
        </div>

        <nav class="menu-navegacion">
            <a href="<?= $rel ?>vistas/estudiantes/inicio/dashboard.php" class="enlace-menu <?= ($seccionActual == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">MIS ESTUDIOS</p>

                <a href="<?= $rel ?>vistas/estudiantes/retos/lista.php" class="enlace-menu <?= ($seccionActual == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>MIS RETOS</span>
                    <span class="etiqueta-contador"><?= $retos ?></span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/calificaciones/lista.php" class="enlace-menu <?= ($seccionActual == 'calificaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>MIS NOTAS</span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/calificaciones/retos.php" class="enlace-menu <?= ($seccionActual == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>MIS NOTAS RETOS</span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/academico/resultadosFinales.php" class="enlace-menu <?= ($seccionActual == 'resultados_finales') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/pfc/subir.php" class="enlace-menu <?= ($seccionActual == 'tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-file-pdf"></i> <span>MI TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PORTAL</p>

                <a href="<?= $rel ?>vistas/estudiantes/anuncios/lista.php" class="enlace-menu <?= ($seccionActual == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                    <span class="etiqueta-contador"><?= $anuncios ?></span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/mensajes/lista.php" class="enlace-menu <?= ($seccionActual == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?= ($sinLeer > 0) ? 'alerta-roja' : '' ?>"><?= $mensajes ?></span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/pagos/lista.php" class="enlace-menu <?= ($seccionActual == 'pagos') ? 'activo' : '' ?>">
                    <i class="fas fa-credit-card"></i> <span>MIS PAGOS</span>
                    <span class="etiqueta-contador"><?= $pagos ?></span>
                </a>

                <a href="<?= $rel ?>vistas/estudiantes/eventos/lista.php" class="enlace-menu <?= ($seccionActual == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="<?= $rel ?>vistas/estudiantes/perfil/ver.php" class="enlace-menu <?= ($seccionActual == 'perfil') ? 'activo' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="<?= $rel ?>vistas/estudiantes/comunes/sobreelproyecto.php" class="enlace-menu <?= ($seccionActual == 'creditos') ? 'activo' : '' ?>">
                    <i class="fas fa-fingerprint"></i> <span>HUELLA DIGITAL</span>
                </a>
                <a href="<?= $rel ?>controladores/logout.php" class="enlace-menu">
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

    <main class="contenido-principal">
    <?php if (isset($_SESSION['idEstudiante'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idEstudiante'] ?>" data-user-role="estudiante" class="d-none"></div>
        <script type="module" src="<?= $rel ?>public/js/firebase/firebase-init.js"></script>
    <?php } ?>

