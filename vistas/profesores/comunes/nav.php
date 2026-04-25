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
                <span class="texto-negrita">Portal Profesores</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/vistas/profesores/dashboard.php" class="enlace-menu <?php echo ($seccionActual == 'inicio' ? 'activo' : ''); ?>">
                <i class="fas fa-home"></i> <span>Inicio</span>
            </a>

            <a href="/pfc/vistas/profesores/perfil/ver.php" class="enlace-menu <?php echo ($seccionActual == 'perfil' ? 'activo' : ''); ?>">
                <i class="fas fa-user-circle"></i> <span>Mi Perfil</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Gestión Académica</p>
                
                <a href="/pfc/vistas/profesores/estudiantes/lista.php" class="enlace-menu <?php echo ($seccionActual == 'estudiantes' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-graduate"></i> <span>Estudiantes</span>
                </a>

                <a href="/pfc/vistas/profesores/ciclos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'ciclos' ? 'activo' : ''); ?>">
                    <i class="fas fa-layer-group"></i> <span>Mis Ciclos</span>
                </a>

                <a href="/pfc/vistas/profesores/modulos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-cubes"></i> <span>Módulos</span>
                </a>

                <a href="/pfc/vistas/profesores/retos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Retos</span>
                </a>

                <a href="/pfc/vistas/profesores/calificaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'calificaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> <span>Notas Módulos</span>
                </a>

                <a href="/pfc/vistas/profesores/calificaciones/retos.php" class="enlace-menu <?php echo ($seccionActual == 'notas_retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Notas Retos</span>
                </a>

                <a href="/pfc/vistas/profesores/academico/resultadosFinales.php" class="enlace-menu <?php echo ($seccionActual == 'resultados_finales' ? 'activo' : ''); ?>">
                    <i class="fas fa-check-double"></i> <span>Resultados Finales</span>
                </a>

                <a href="/pfc/vistas/profesores/tfg/lista.php" class="enlace-menu <?php echo ($seccionActual == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> <span>Gestión TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Comunicación</p>

                <a href="/pfc/vistas/profesores/anuncios/lista.php" class="enlace-menu <?php echo ($seccionActual == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                </a>

                <a href="/pfc/vistas/profesores/mensajes/lista.php" class="enlace-menu <?php echo ($seccionActual == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-paper-plane"></i> <span>Mensajería</span>
                </a>

                <a href="/pfc/vistas/profesores/eventos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'eventos' ? 'activo' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i> <span>Eventos</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/controladores/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
                </a>
            </div>
        </nav>
    </aside>

    <script>
    function toggleMenu() {
        document.getElementById('barraLateral').classList.toggle('activo');
    }
    </script>

    <main class="contenido-principal">
    <?php if (isset($_SESSION['idProfesor'])): ?>
        <div id="firebase-user-data" data-user-id="<?php echo $_SESSION['idProfesor']; ?>" data-user-role="profesor" style="display:none;"></div>
        <script type="module" src="/pfc/public/js/firebase/firebase-init.js"></script>
    <?php endif; ?>
