<?php
// --- PLANTILLA NAVEGACIÓN - SECCIÓN ESTUDIANTES ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $tituloDelPagina; ?></title>
    <link rel="stylesheet" href="/pfc/admin/estiloAdmin/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="contenedor-principal">
    <aside class="barra-lateral">
        <div class="cabecera-menu">
            <div class="logo-sistema">
                <span class="logo-icono">E</span>
                <span class="texto-negrita">Portal Estudiantes</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/estudiantes/index.php" class="enlace-menu <?php echo ($seccionActual == 'inicio' ? 'activo' : ''); ?>">
                <i class="fas fa-home"></i> <span>Inicio</span>
            </a>

            <a href="/pfc/estudiantes/vistas/perfil/ver.php" class="enlace-menu <?php echo ($seccionActual == 'perfil' ? 'activo' : ''); ?>">
                <i class="fas fa-user-circle"></i> <span>Mi Perfil</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Mis Estudios</p>

                <a href="/pfc/estudiantes/vistas/retos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Mis Retos</span>
                </a>

                <a href="/pfc/estudiantes/vistas/calificaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'calificaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> <span>Mis Notas</span>
                </a>

                <a href="/pfc/estudiantes/vistas/tfg/lista.php" class="enlace-menu <?php echo ($seccionActual == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> <span>Mi TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Portal</p>

                <a href="/pfc/estudiantes/vistas/anuncios/lista.php" class="enlace-menu <?php echo ($seccionActual == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                </a>

                <a href="/pfc/estudiantes/vistas/reclamaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-exclamation-triangle"></i> <span>Mis Reclamaciones</span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/logout.php" class="enlace-menu">
                    <i class="fas fa-sign-out-alt"></i> <span>Cerrar Sesión</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="contenido-principal">