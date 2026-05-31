<?php
session_start();

if (empty($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$datosEstudiante_menu = obtenerEstudiantePorId($idEstudiante);
$idCicloEst_menu = $datosEstudiante_menu['idCiclo'] ?? 0;

$totalMensajes_menu = count(listarMensajesDeEstudiante($idEstudiante));
$totalSinLeer_menu = contarMensajesNoLeidosEstudiante($idEstudiante);
$totalAnuncios_menu = count(listarAnunciosPorRol('estudiantes'));
$totalPagos_menu = contarPagosEstudiante($idEstudiante);
$totalRetos_menu = count(listarRetosPorCiclo($idCicloEst_menu));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDelPagina ?? 'AulaPro Estudiante' ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/estilo.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
    <link rel="stylesheet" href="../../../public/css/notificaciones.css">
    <link rel="shortcut icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/aula-digital.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../public/js/aula-digital.js"></script>
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
            <div class="titulo-panel-sidebar">ESTUDIANTES PANEL</div>
        </div>

        <nav class="menu-navegacion">
            <a href="../inicio/dashboard.php" class="enlace-menu <?= ($seccionActual == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">MIS ESTUDIOS</p>

                <a href="../retos/lista.php" class="enlace-menu <?= ($seccionActual == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>MIS RETOS</span>
                    <span class="texto-contador"><?= $totalRetos_menu ?></span>
                </a>

                <a href="../calificaciones/lista.php" class="enlace-menu <?= ($seccionActual == 'calificaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>MIS NOTAS</span>
                </a>

                <a href="../calificaciones/retos.php" class="enlace-menu <?= ($seccionActual == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>MIS NOTAS RETOS</span>
                </a>

                <a href="../academico/resultadosFinales.php" class="enlace-menu <?= ($seccionActual == 'resultados_finales') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="../pfc/subir.php" class="enlace-menu <?= ($seccionActual == 'tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-file-pdf"></i> <span>MI TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">AULA DIGITAL</p>

                <a href="../aula/sesiones.php" class="enlace-menu <?= ($seccionActual == 'aula_sesiones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>AULA DIGITAL</span>
                </a>

                <a href="../aula/recursos.php" class="enlace-menu <?= ($seccionActual == 'aula_recursos') ? 'activo' : '' ?>">
                    <i class="fas fa-folder-open"></i> <span>RECURSOS</span>
                </a>

                <a href="../aula/favoritos.php" class="enlace-menu <?= ($seccionActual == 'aula_favoritos') ? 'activo' : '' ?>">
                    <i class="fas fa-star"></i> <span>FAVORITOS</span>
                </a>

                <a href="../aula/tareas.php" class="enlace-menu <?= ($seccionActual == 'aula_tareas') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>TAREAS</span>
                </a>

                <a href="../aula/mis_entregas.php" class="enlace-menu <?= ($seccionActual == 'aula_entregas') ? 'activo' : '' ?>">
                    <i class="fas fa-file-upload"></i> <span>MIS ENTREGAS</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PORTAL</p>

                <a href="../anuncios/lista.php" class="enlace-menu <?= ($seccionActual == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                    <span class="texto-contador"><?= $totalAnuncios_menu ?></span>
                </a>

                <a href="../mensajes/lista.php" class="enlace-menu <?= ($seccionActual == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="texto-contador <?= ($totalSinLeer_menu > 0) ? 'alerta-roja' : '' ?>"><?= $totalMensajes_menu ?></span>
                </a>

                <a href="../pagos/lista.php" class="enlace-menu <?= ($seccionActual == 'pagos') ? 'activo' : '' ?>">
                    <i class="fas fa-credit-card"></i> <span>MIS PAGOS</span>
                    <span class="texto-contador"><?= $totalPagos_menu ?></span>
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

    <section class="contenido-principal"><?php if (isset($_SESSION['idEstudiante'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idEstudiante'] ?>" data-user-role="estudiante" class="oculto"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>

