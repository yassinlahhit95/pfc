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

$idProfesor = $_SESSION['idProfesor'];
$datosProfesor_menu = obtenerProfesorPorId($idProfesor);
$nombreUsuario_menu = $datosProfesor_menu['nombreProfesor'] ?? 'Profesor';

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
    <title><?= Security::escapeHtml($tituloDelPagina ?? 'AulaPro Profesor') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/css/estilo.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
    <link rel="stylesheet" href="../../../public/css/notificaciones.css">
    <link rel="shortcut icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="icon" href="../../../public/imagenes/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/css/aula-digital.css?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/css/aula-digital.css")) ?>">
    <link rel="stylesheet" href="../../../public/css/sidebar.css?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/css/sidebar.css")) ?>">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../public/js/aula-digital.js?v=<?= Security::escapeHtml(@filemtime(__DIR__."/../../../public/js/aula-digital.js")) ?>"></script>
    <script src="../../../public/js/menu-contextual.js?v=<?= @filemtime(__DIR__.'/../../../public/js/menu-contextual.js') ?>"></script>
</head>
<body>
<?php require __DIR__ . "/../../../include/icon-sprite.php"; ?>

<header class="navbar-superior">
    <div class="logo-navbar-contenedor">
        <img src="../../../public/imagenes/aulapro.jpeg" alt="AulaPro" class="logo-navbar">
    </div>
    <div class="menu-superior">
        <ul class="navbar-nav">
            <li><a href="../perfil/ver.php"><svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg> Mi Perfil</a></li>
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
            <a href="../inicio/dashboard.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'inicio') ? 'activo' : '') ?>">
                <svg class="ico" aria-hidden="true"><use href="#ic-home"/></svg> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="../estudiantes/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'estudiantes') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-user-graduate"/></svg> <span>ESTUDIANTES</span>
                    <span class="texto-contador"><?= Security::escapeHtml($totalAlumnos_menu ) ?></span>
                </a>

                <a href="../ciclos/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'ciclos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-layer-group"/></svg> <span>MIS CICLOS</span>
                    <span class="texto-contador"><?= Security::escapeHtml($totalCiclos_menu ) ?></span>
                </a>

                <a href="../modulos/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'modulos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-cubes"/></svg> <span>MÓDULOS</span>
                    <span class="texto-contador"><?= Security::escapeHtml($totalModulos_menu ) ?></span>
                </a>

                <a href="../retos/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'retos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-tasks"/></svg> <span>RETOS</span>
                    <span class="texto-contador"><?= Security::escapeHtml($totalRetos_menu ) ?></span>
                </a>

                <a href="../calificaciones/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'calificaciones') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-graduation-cap"/></svg> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="../academico/calificacionesRetos.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'notas_retos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-tasks"/></svg> <span>NOTAS RETOS</span>
                </a>

                <a href="../calificaciones/tfg.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'notas_tfg') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-star"/></svg> <span>NOTAS TFG</span>
                </a>

                <a href="../academico/resultadosFinales.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'resultados_finales') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-check-double"/></svg> <span>RESULTADOS FINALES</span>
                </a>

                <a href="../horario/horario.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'horario') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-calendar-alt"/></svg> <span>CUADRO HORARIO</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">AULA DIGITAL</p>

                <a href="../aula/sesiones.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'aula_sesiones') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-graduation-cap"/></svg> <span>AULA DIGITAL</span>
                </a>

                <a href="../aula/index.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'aula_recursos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-folder-open"/></svg> <span>RECURSOS</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">COMUNICACIÓN</p>

                <a href="../anuncios/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'anuncios') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-bullhorn"/></svg> <span>ANUNCIOS</span>
                </a>

                <a href="../mensajes/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'reclamaciones') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-paper-plane"/></svg> <span>MENSAJERÍA</span>
                    <span class="texto-contador <?= Security::escapeHtml(($totalSinLeer_menu > 0) ? 'alerta-roja' : '') ?>"><?= Security::escapeHtml($totalMensajes_menu ) ?></span>
                </a>

                <a href="../eventos/lista.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'eventos') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-calendar-alt"/></svg> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="../perfil/ver.php" class="enlace-menu <?= Security::escapeHtml(($seccionActual == 'perfil') ? 'activo' : '') ?>">
                    <svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg> <span>MI PERFIL</span>
                </a>
                <a href="https://yassin.agency" target="_blank" class="enlace-menu">
                    <svg class="ico" aria-hidden="true"><use href="#ic-home"/></svg> <span>PÁGINA INICIO</span>
                </a>
                <a href="../../../controladores/logout.php" class="enlace-menu">
                    <svg class="ico" aria-hidden="true"><use href="#ic-sign-out-alt"/></svg> <span>CERRAR SESIÓN</span>
                </a>
                <div class="info-sistema-footer">
                    &copy; <?= Security::escapeHtml(date('Y')) ?> Yassin Lahhit
                </div>
            </div>
        </nav>
    </aside>

    <div class="sb-menu" id="sbMenu" role="menu" aria-label="Opciones">
        <a href="../perfil/ver.php" class="sb-menu-item" role="menuitem"><svg class="ico" aria-hidden="true"><use href="#ic-user-circle"/></svg> Mi Perfil</a>
        <a href="../../../controladores/logout.php" class="sb-menu-item salir" role="menuitem"><svg class="ico" aria-hidden="true"><use href="#ic-sign-out-alt"/></svg> Cerrar Sesión</a>
    </div>

    <section class="contenido-principal"><?php if (isset($_SESSION['idProfesor'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= Security::escapeHtml($_SESSION['idProfesor'] ) ?>" data-user-role="profesor" class="oculto"></div>
        <script type="module" src="../../../public/js/firebase/firebase-init.js"></script>
    <?php } ?>


