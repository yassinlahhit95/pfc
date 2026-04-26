<?php
if (isset($_SESSION['idProfesor']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
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
            <a href="/pfc/vistas/profesores/dashboard.php" class="enlace-menu <?php if ($seccionActual == 'inicio') { echo 'activo'; } ?>">
                <i class="fas fa-home"></i> <span>INICIO</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="/pfc/vistas/profesores/estudiantes/lista.php" class="enlace-menu <?php if ($seccionActual == 'estudiantes') { echo 'activo'; } ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                </a>

                <a href="/pfc/vistas/profesores/ciclos/lista.php" class="enlace-menu <?php if ($seccionActual == 'ciclos') { echo 'activo'; } ?>">
                    <i class="fas fa-layer-group"></i> <span>MIS CICLOS</span>
                </a>

                <a href="/pfc/vistas/profesores/modulos/lista.php" class="enlace-menu <?php if ($seccionActual == 'modulos') { echo 'activo'; } ?>">
                    <i class="fas fa-cubes"></i> <span>MÓDULOS</span>
                </a>

                <a href="/pfc/vistas/profesores/retos/lista.php" class="enlace-menu <?php if ($seccionActual == 'retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS</span>
                </a>

                <a href="/pfc/vistas/profesores/calificaciones/lista.php" class="enlace-menu <?php if ($seccionActual == 'calificaciones') { echo 'activo'; } ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="/pfc/vistas/profesores/calificaciones/retos.php" class="enlace-menu <?php if ($seccionActual == 'notas_retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="/pfc/vistas/profesores/academico/resultadosFinales.php" class="enlace-menu <?php if ($seccionActual == 'resultados_finales') { echo 'activo'; } ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="/pfc/vistas/profesores/tfg/lista.php" class="enlace-menu <?php if ($seccionActual == 'tfg') { echo 'activo'; } ?>">
                    <i class="fas fa-file-pdf"></i> <span>GESTIÓN TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">COMUNICACIÓN</p>

                <a href="/pfc/vistas/profesores/anuncios/lista.php" class="enlace-menu <?php if ($seccionActual == 'anuncios') { echo 'activo'; } ?>">
                    <i class="fas fa-bullhorn"></i> <span>ANUNCIOS</span>
                </a>

                <a href="/pfc/vistas/profesores/mensajes/lista.php" class="enlace-menu <?php if ($seccionActual == 'reclamaciones') { echo 'activo'; } ?>">
                    <i class="fas fa-paper-plane"></i> <span>MENSAJERÍA</span>
                </a>

                <a href="/pfc/vistas/profesores/eventos/lista.php" class="enlace-menu <?php if ($seccionActual == 'eventos') { echo 'activo'; } ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/vistas/profesores/perfil/ver.php" class="enlace-menu <?php if ($seccionActual == 'perfil') { echo 'activo'; } ?>">
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
    <?php if (isset($_SESSION['idProfesor'])): ?>
        <div id="firebase-user-data" data-user-id="<?php echo $_SESSION['idProfesor']; ?>" data-user-role="profesor" style="display:none;"></div>
        <script type="module" src="/pfc/public/js/firebase/firebase-init.js"></script>
    <?php endif; ?>
