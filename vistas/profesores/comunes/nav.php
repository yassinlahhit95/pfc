<?php
if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
    exit;
}

// Calculamos la ruta base de forma dinámica
$URL_ACTUAL = $_SERVER['PHP_SELF'];
$partesRuta = explode('/vistas/', $URL_ACTUAL);
$rutaRelativaVistas = $partesRuta[1] ?? '';
$numeroCarpetas = substr_count($rutaRelativaVistas, '/');
$ruta_base = str_repeat('../', $numeroCarpetas + 1);

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idProfMenu = $_SESSION['idProfesor'];
$cantAlumnos = contarEstudiantesDeProfesor($idProfMenu);
$cantCiclos = contarCiclosDeProfesor($idProfMenu);
$cantMensajes = contarMensajesDeProfesor($idProfMenu);
$cantMensajesNoLeidosProf = contarMensajesNoLeidosProfesor($idProfMenu);
$cantTFGs = contarTFGsDeProfesor($idProfMenu);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $tituloDelPagina ?></title>
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/admin.css">
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/responsive.css">
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/notificaciones.css">
    <link rel="icon" href="<?= $ruta_base ?>public/imagenes/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

<button class="menu-toggle solo-movil" onclick="toggleMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="contenedor-principal">
    <aside class="barra-lateral" id="barraLateral">
        <div class="cabecera-menu">
            <div class="logo-sistema">
                <span class="logo-icono">P</span>
                <span class="texto-negrita">PORTAL PROFESORES</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="<?= $ruta_base ?>vistas/profesores/dashboard.php" class="enlace-menu <?= ($seccionActual == 'inicio') ? 'activo' : '' ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="<?= $ruta_base ?>vistas/profesores/estudiantes/lista.php" class="enlace-menu <?= ($seccionActual == 'estudiantes') ? 'activo' : '' ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="etiqueta-contador"><?= $cantAlumnos ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/ciclos/lista.php" class="enlace-menu <?= ($seccionActual == 'ciclos') ? 'activo' : '' ?>">
                    <i class="fas fa-layer-group"></i> <span>MIS CICLOS</span>
                    <span class="etiqueta-contador"><?= $cantCiclos ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/modulos/lista.php" class="enlace-menu <?= ($seccionActual == 'modulos') ? 'activo' : '' ?>">
                    <i class="fas fa-cubes"></i> <span>MÓDULOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/retos/lista.php" class="enlace-menu <?= ($seccionActual == 'retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/calificaciones/lista.php" class="enlace-menu <?= ($seccionActual == 'calificaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/calificaciones/retos.php" class="enlace-menu <?= ($seccionActual == 'notas_retos') ? 'activo' : '' ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/academico/resultadosFinales.php" class="enlace-menu <?= ($seccionActual == 'resultados_finales') ? 'activo' : '' ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/pfc/lista.php" class="enlace-menu <?= ($seccionActual == 'tfg') ? 'activo' : '' ?>">
                    <i class="fas fa-file-pdf"></i> <span>GESTIÓN TFG</span>
                    <span class="etiqueta-contador"><?= $cantTFGs ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">COMUNICACIÓN</p>

                <a href="<?= $ruta_base ?>vistas/profesores/anuncios/lista.php" class="enlace-menu <?= ($seccionActual == 'anuncios') ? 'activo' : '' ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/mensajes/lista.php" class="enlace-menu <?= ($seccionActual == 'reclamaciones') ? 'activo' : '' ?>">
                    <i class="fas fa-paper-plane"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?= ($cantMensajesNoLeidosProf > 0) ? 'alerta-roja' : '' ?>"><?= $cantMensajes ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/profesores/eventos/lista.php" class="enlace-menu <?= ($seccionActual == 'eventos') ? 'activo' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="<?= $ruta_base ?>vistas/profesores/perfil/ver.php" class="enlace-menu <?= ($seccionActual == 'perfil') ? 'activo' : '' ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="<?= $ruta_base ?>vistas/admin/comunes/creditos.php" class="enlace-menu <?= ($seccionActual == 'creditos') ? 'activo' : '' ?>">
                    <i class="fas fa-fingerprint"></i> <span>HUELLA DIGITAL</span>
                </a>
                <a href="<?= $ruta_base ?>controladores/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>CERRAR SESIÓN</span>
                </a>
                <div class="info-sistema-footer">
                    &copy; <?= date('Y') ?> Yassin Lahhit<br>Fingerprint Verified
                </div>
            </div>
        </nav>
    </aside>

    <script>
    function toggleMenu() {
        var sidebar = document.getElementById('barraLateral');
        sidebar.classList.toggle('activo');
    }
    </script>

    <main class="contenido-principal">
    <?php if (isset($_SESSION['idProfesor'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idProfesor'] ?>" data-user-role="profesor" class="d-none"></div>
        <script type="module" src="<?= $ruta_base ?>public/js/firebase/firebase-init.js"></script>
    <?php } ?>


