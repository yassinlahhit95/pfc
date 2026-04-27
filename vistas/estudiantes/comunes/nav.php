<?php
if (isset($_SESSION['idEstudiante']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstMenu = $_SESSION['idEstudiante'];
$cantMensajesEst = count(listarMensajesDeEstudiante($idEstMenu));
$cantMensajesNoLeidosEst = contarMensajesNoLeidosEstudiante($idEstMenu);
$cantAnunciosEst = count(listarAnunciosPorRol('estudiantes'));
$cantPagosEst = contarPagosEstudiante($idEstMenu);
$cantRetosEst = count(listarCalificacionesRetoPorEstudiante($idEstMenu));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $tituloDelPagina; ?></title>
    <link rel="stylesheet" href="/pfc/public/css/admin.css">
    <link rel="stylesheet" href="/pfc/public/css/responsive.css">
    <link rel="stylesheet" href="/pfc/public/css/notificaciones.css">
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
                <span class="logo-icono">E</span>
                <span class="texto-negrita">PORTAL ESTUDIANTES</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/vistas/estudiantes/dashboard.php" class="enlace-menu <?php if ($seccionActual == 'inicio') { echo 'activo'; } ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">MIS ESTUDIOS</p>

                <a href="/pfc/vistas/estudiantes/retos/lista.php" class="enlace-menu <?php if ($seccionActual == 'retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>MIS RETOS</span>
                    <span class="etiqueta-contador"><?php echo $cantRetosEst; ?></span>
                </a>

                <a href="/pfc/vistas/estudiantes/calificaciones/lista.php" class="enlace-menu <?php if ($seccionActual == 'calificaciones') { echo 'activo'; } ?>">
                    <i class="fas fa-graduation-cap"></i> <span>MIS NOTAS</span>
                </a>

                <a href="/pfc/vistas/estudiantes/calificaciones/retos.php" class="enlace-menu <?php if ($seccionActual == 'notas_retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>MIS NOTAS RETOS</span>
                </a>

                <a href="/pfc/vistas/estudiantes/academico/resultadosFinales.php" class="enlace-menu <?php if ($seccionActual == 'resultados_finales') { echo 'activo'; } ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="/pfc/vistas/estudiantes/pfc/subir.php" class="enlace-menu <?php if ($seccionActual == 'tfg') { echo 'activo'; } ?>">
                    <i class="fas fa-file-pdf"></i> <span>MI TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PORTAL</p>

                <a href="/pfc/vistas/estudiantes/anuncios/lista.php" class="enlace-menu <?php if ($seccionActual == 'anuncios') { echo 'activo'; } ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                    <span class="etiqueta-contador"><?php echo $cantAnunciosEst; ?></span>
                </a>

                <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="enlace-menu <?php if ($seccionActual == 'reclamaciones') { echo 'activo'; } ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?php echo ($cantMensajesNoLeidosEst > 0) ? 'alerta-roja' : ''; ?>"><?php echo $cantMensajesEst; ?></span>
                </a>

                <a href="/pfc/vistas/estudiantes/pagos/lista.php" class="enlace-menu <?php if ($seccionActual == 'pagos') { echo 'activo'; } ?>">
                    <i class="fas fa-credit-card"></i> <span>MIS PAGOS</span>
                    <span class="etiqueta-contador"><?php echo $cantPagosEst; ?></span>
                </a>

                <a href="/pfc/vistas/estudiantes/eventos/lista.php" class="enlace-menu <?php if ($seccionActual == 'eventos') { echo 'activo'; } ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="enlace-menu <?php if ($seccionActual == 'perfil') { echo 'activo'; } ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="/pfc/vistas/admin/comunes/creditos.php" class="enlace-menu <?php if ($seccionActual == 'creditos') { echo 'activo'; } ?>">
                    <i class="fas fa-fingerprint"></i> <span>HUELLA DIGITAL</span>
                </a>
                <a href="/pfc/controladores/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>CERRAR SESIÓN</span>
                </a>
                <div class="info-sistema-footer">
                    &copy; <?php echo date('Y'); ?> Yassin Lahhit<br>Fingerprint Verified
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
    <?php if (isset($_SESSION['idEstudiante'])) { ?>
        <div id="firebase-user-data" data-user-id="<?php echo $_SESSION['idEstudiante']; ?>" data-user-role="estudiante" class="d-none"></div>
        <script type="module" src="/pfc/public/js/firebase/firebase-init.js"></script>
    <?php } ?>

