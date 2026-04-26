<?php
if (isset($_SESSION['idEstudiante']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
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
                <span class="logo-icono">E</span>
                <span class="texto-negrita">Portal Estudiantes</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/vistas/estudiantes/dashboard.php" class="enlace-menu <?php echo ($seccionActual == 'inicio' ? 'activo' : ''); ?>">
                <i class="fas fa-home"></i> <span>Inicio</span>
            </a>

            <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="enlace-menu <?php echo ($seccionActual == 'perfil' ? 'activo' : ''); ?>">
                <i class="fas fa-user-circle"></i> <span>Mi Perfil</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Mis Estudios</p>

                <a href="/pfc/vistas/estudiantes/retos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Mis Retos</span>
                </a>

                <a href="/pfc/vistas/estudiantes/calificaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'calificaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> <span>Mis Notas</span>
                </a>

                <a href="/pfc/vistas/estudiantes/calificaciones/retos.php" class="enlace-menu <?php echo ($seccionActual == 'notas_retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Mis Notas Retos</span>
                </a>

                <a href="/pfc/vistas/estudiantes/academico/resultadosFinales.php" class="enlace-menu <?php echo ($seccionActual == 'resultados_finales' ? 'activo' : ''); ?>">
                    <i class="fas fa-check-double"></i> <span>Resultados Finales</span>
                </a>

                <a href="/pfc/vistas/estudiantes/tfg/subir.php" class="enlace-menu <?php echo ($seccionActual == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> <span>Mi TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Portal</p>

                <a href="/pfc/vistas/estudiantes/anuncios/lista.php" class="enlace-menu <?php echo ($seccionActual == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                </a>

                <a href="/pfc/vistas/estudiantes/mensajes/lista.php" class="enlace-menu <?php echo ($seccionActual == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-envelope"></i> <span>Mensajería</span>
                </a>

                <a href="/pfc/vistas/estudiantes/pagos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'pagos' ? 'activo' : ''); ?>">
                    <i class="fas fa-credit-card"></i> <span>Mis Pagos</span>
                </a>

                <a href="/pfc/vistas/estudiantes/eventos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'eventos' ? 'activo' : ''); ?>">
                    <i class="fas fa-calendar-alt"></i> <span>Eventos</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/vistas/admin/comunes/creditos.php" class="enlace-menu <?php echo ($seccionActual == 'creditos' ? 'activo' : ''); ?>">
                    <i class="fas fa-fingerprint"></i> <span>Huella Digital</span>
                </a>
                <a href="/pfc/controladores/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
                </a>
                <div style="padding: 15px; text-align: center; color: rgba(255,255,255,0.4); font-size: 10px; border-top: 1px solid rgba(255,255,255,0.1);">
                    &copy; <?php echo date('Y'); ?> Yassin Lahhit<br>Fingerprint Verified
                </div>
            </div>
        </nav>
    </aside>

    <script>
    function toggleMenu() {
        document.getElementById('barraLateral').classList.toggle('activo');
    }
    </script>

    <main class="contenido-principal">
    <?php if (isset($_SESSION['idEstudiante'])): ?>
        <div id="firebase-user-data" data-user-id="<?php echo $_SESSION['idEstudiante']; ?>" data-user-role="estudiante" style="display:none;"></div>
        <script type="module" src="/pfc/public/js/firebase/firebase-init.js"></script>
    <?php endif; ?>
