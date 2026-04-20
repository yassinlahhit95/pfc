<?php
// --- PLANTILLA NAVEGACIÓN - SECCIÓN PROFESORES ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
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
                <span class="logo-icono">P</span>
                <span class="texto-negrita">Portal Profesores</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/profesores/vistas/perfil/ver.php" class="enlace-menu <?php echo ($seccionActual == 'perfil' ? 'activo' : ''); ?>">
                <i class="fas fa-user-circle"></i> <span>Mi Perfil</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Gestión Académica</p>
                
                <a href="/pfc/profesores/vistas/estudiantes/lista.php" class="enlace-menu <?php echo ($seccionActual == 'estudiantes' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-graduate"></i> <span>Estudiantes</span>
                </a>

                <a href="/pfc/profesores/vistas/modulos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-cubes"></i> <span>Módulos</span>
                </a>

                <a href="/pfc/profesores/vistas/retos/lista.php" class="enlace-menu <?php echo ($seccionActual == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Retos</span>
                </a>

                <a href="/pfc/profesores/vistas/calificaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'calificaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> <span>Notas Módulos</span>
                </a>

                <a href="/pfc/profesores/vistas/tfg/lista.php" class="enlace-menu <?php echo ($seccionActual == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> <span>Gestión TFG</span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Comunicación</p>

                <a href="/pfc/profesores/vistas/anuncios/lista.php" class="enlace-menu <?php echo ($seccionActual == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                </a>

                <a href="/pfc/profesores/vistas/reclamaciones/lista.php" class="enlace-menu <?php echo ($seccionActual == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-exclamation-triangle"></i> <span>Reclamaciones</span>
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