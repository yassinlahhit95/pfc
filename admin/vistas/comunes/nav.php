<?php
// --- TRUCO SENCILLO PARA RUTAS (Para principiantes) ---
// Intentamos cargar los archivos desde la ruta de las vistas o desde la ruta del dashboard

if (file_exists("../../modelos/conexion.php")) {
    // Si estamos en una subcarpeta (ej: vistas/estudiantes/)
    require_once "../../modelos/conexion.php";
    require_once "../../modelos/panelDeControl.php";
} else {
    // Si estamos en la carpeta principal (ej: dashboardAdmin.php)
    require_once "modelos/conexion.php";
    require_once "modelos/panelDeControl.php";
}

$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();
$panelControl = new panelDeControl($conexionBD);

// Obtener contadores para el menú lateral
$totalEstudiantes = $panelControl->contadorEstudiantes();
$totalProfesores = $panelControl->contadorProfesores();
$totalDirectores = $panelControl->contadorDirectores();
$totalCursos = $panelControl->contadorCursos();
$totalAulas = $panelControl->contadorAulas();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo_pagina; ?></title>
    <base href="/pfc/admin/">
    <link rel="stylesheet" href="estiloAdmin/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="contenedor-principal">
    <aside class="barra-lateral">
        <div class="cabecera-menu">
            <div class="logo-sistema">
                <span class="logo-icono">P</span>
                <span class="texto-negrita">Panel de Control</span>
            </div>
        </div>

        <nav class="menu-navegacion">
            <a href="dashboardAdmin.php" class="enlace-menu <?php echo ($seccion == 'inicio' ? 'activo' : ''); ?>">
                <i class="fas fa-home"></i> <span>Inicio</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Gestión Académica</p>
                
                <a href="vistas/estudiantes/verEstudiantes.php" class="enlace-menu <?php echo ($seccion == 'estudiantes' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-graduate"></i> 
                    <span>Estudiantes</span> 
                    <span class="etiqueta-contador"><?php echo $totalEstudiantes; ?></span>
                </a>

                <a href="vistas/profesores/verProfesores.php" class="enlace-menu <?php echo ($seccion == 'profesores' ? 'activo' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    <span>Profesores</span> 
                    <span class="etiqueta-contador"><?php echo $totalProfesores; ?></span>
                </a>

                <a href="vistas/directores/verDirectores.php" class="enlace-menu <?php echo ($seccion == 'directores' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-tie"></i> 
                    <span>Directores</span> 
                    <span class="etiqueta-contador"><?php echo $totalDirectores; ?></span>
                </a>

                <a href="vistas/cursos/verCursos.php" class="enlace-menu <?php echo ($seccion == 'cursos' ? 'activo' : ''); ?>">
                    <i class="fas fa-book"></i> 
                    <span>Cursos</span> 
                    <span class="etiqueta-contador"><?php echo $totalCursos; ?></span>
                </a>

                <a href="vistas/aulas/verAulas.php" class="enlace-menu <?php echo ($seccion == 'aulas' ? 'activo' : ''); ?>">
                    <i class="fas fa-door-open"></i> 
                    <span>Aulas</span> 
                    <span class="etiqueta-contador"><?php echo $totalAulas; ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Administración</p>
                
                <a href="vistas/pagos/verPagosGeneral.php" class="enlace-menu <?php echo ($seccion == 'pagos' ? 'activo' : ''); ?>">
                    <i class="fas fa-euro-sign"></i> <span>Pagos</span>
                </a>

                <a href="vistas/anuncios/gestionAnuncios.php" class="enlace-menu <?php echo ($seccion == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                </a>

                <a href="vistas/inventario/verInventario.php" class="enlace-menu <?php echo ($seccion == 'inventario' ? 'activo' : ''); ?>">
                    <i class="fas fa-laptop"></i> <span>Inventario</span>
                </a>

                <a href="vistas/reclamaciones/verReclamaciones.php" class="enlace-menu <?php echo ($seccion == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-exclamation-triangle"></i> <span>Reclamaciones</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="contenido-derecha">
