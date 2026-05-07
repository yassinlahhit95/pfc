<?php
if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProf = $_SESSION['idProfesor'];

$numAlumnos = contarEstudiantesDeProfesor($idProf);
$numCiclos = contarCiclosDeProfesor($idProf);
$numMensajes = contarMensajesDeProfesor($idProf);
$numSinLeer = contarMensajesNoLeidosProfesor($idProf);
$numTfgs = contarTFGsDeProfesor($idProf);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDelPagina ?? 'AulaPro Profesor' ?></title>
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
            <li><a href="../../../vistas/profesores/perfil/ver.php"><i class="fas fa-user-circle"></i> Mi Perfil</a></li>
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
            <a href="../../../vistas/profesores/inicio/dashboard.php" class="enlace-menu <?= ($seccionActual == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../../../vistas/profesores/estudiantes/lista.php" class="enlace-menu <?= ($seccionActual == 'estudiantes') ? 'activo' : '' ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="etiqueta-contador"><?= $numAlumnos ?></span>
                </a>

                <a href="../../../vistas/profesores/ciclos/lista.php" class="enlace-menu <?= ($seccionActual == 'ciclos') ? 'activo' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>MIS CICLOS</span>
                    <span class="etiqueta-contador"><?= $numCiclos ?></span>
                </a>

                <a href="../../../vistas/profesores/modulos/lista.php" class="enlace-menu <?= ($seccionActual == 'modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-cubes"></i> <span>MÓDULOS</span>
                </a>

                <a href="../../../vistas/profesores/retos/lista.php" class="enlace-menu <?= ($seccionActual == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS</span>
                </a>

                <a href="../../../vistas/profesores/calificaciones/lista.php" class="enlace-menu <?= ($seccionActual == 'calificaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../../../vistas/profesores/calificaciones/retos.php" class="enlace-menu <?= ($seccionActual == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="../../../vistas/profesores/academico/resultadosFinales.php" class="enlace-menu <?= ($seccionActual == 'resultados_finales') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="../../../vistas/profesores/pfc/lista.php" class="enlace-menu <?= ($seccionActual == 'tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-file-pdf"></i> <span>GESTIÓN TFG</span>
                    <span class="etiqueta-contador"><?= $numTfgs ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">COMUNICACIÓN</p>

                <a href="../../../vistas/profesores/anuncios/lista.php" class="enlace-menu <?= ($seccionActual == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                </a>

                <a href="../../../vistas/profesores/mensajes/lista.php" class="enlace-menu <?= ($seccionActual == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-paper-plane"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?= ($numSinLeer > 0) ? 'alerta-roja' : '' ?>"><?= $numMensajes ?></span>
                </a>

                <a href="../../../vistas/profesores/eventos/lista.php" class="enlace-menu <?= ($seccionActual == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="../../../vistas/profesores/perfil/ver.php" class="enlace-menu <?= ($seccionActual == 'perfil') ? 'activo' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="../../../vistas/profesores/comunes/sobreelproyecto.php" class="enlace-menu <?= ($seccionActual == 'creditos') ? 'activo' : '' ?>">
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

    <main class="contenido-principal">
    <?php if (isset($_SESSION['idProfesor'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idProfesor'] ?>" data-user-role="profesor" class="d-none"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>
