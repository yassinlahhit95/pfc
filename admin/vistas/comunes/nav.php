<?php
// --- TRUCO SENCILLO PARA RUTAS (Para principiantes) ---
// Intentamos cargar los archivos desde la ruta de las vistas o desde la ruta del dashboard

if (file_exists("../../modelos/conectar.php")) {
    // Si estamos en una subcarpeta (ej: vistas/estudiantes/)
    require_once "../../modelos/conectar.php";
    require_once "../../modelos/panelDeControl.php";
    require_once "../../modelos/tfg.php";
} else {
    // Si estamos en la carpeta principal (ej: dashboardAdmin.php)
    require_once "modelos/conectar.php";
    require_once "modelos/panelDeControl.php";
    require_once "modelos/tfg.php";
}

// Obtener contadores para el menú lateral (Funciones simples)
$totalEstudiantes = contarEstudiantes();
$totalProfesores = contarProfesores();
$totalDirectores = contarDirectores();
$totalAulas = contarAulas();
$totalCiclos = contarCiclos();
$totalModulos = contarModulos();
$totalRetos = contarRetos();
$totalInventario = contarInventario();
$totalPagos = contarPagos();
$totalAnuncios = contarAnuncios();
$totalReclamaciones = contarReclamaciones();
$totalPrestamos = contarPrestamosActivos();
$totalTFG = contarTFGsSubidos();
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

            <!-- 1. ÁREA ACADÉMICA -->
            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Área Académica</p>
                
                <a href="vistas/estudiantes/verEstudiantes.php" class="enlace-menu <?php echo ($seccion == 'estudiantes' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-graduate"></i> 
                    <span>Estudiantes</span> 
                    <span class="etiqueta-contador"><?php echo $totalEstudiantes; ?></span>
                </a>

                <a href="vistas/ciclos/verCiclos.php" class="enlace-menu <?php echo ($seccion == 'ciclos' ? 'activo' : ''); ?>">
                    <i class="fas fa-sync"></i> 
                    <span>Ciclos</span> 
                    <span class="etiqueta-contador"><?php echo $totalCiclos; ?></span>
                </a>

                <a href="vistas/modulos/verModulos.php" class="enlace-menu <?php echo ($seccion == 'modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-cubes"></i> 
                    <span>Módulos</span> 
                    <span class="etiqueta-contador"><?php echo $totalModulos; ?></span>
                </a>

                <a href="vistas/retos/verRetos.php" class="enlace-menu <?php echo ($seccion == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> 
                    <span>Retos</span> 
                    <span class="etiqueta-contador"><?php echo $totalRetos; ?></span>
                </a>

                <a href="vistas/academico/calificacionesModulos.php" class="enlace-menu <?php echo ($seccion == 'notas_modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> 
                    <span>Notas Módulos</span>
                </a>

                <a href="vistas/tfg/verTFGs.php" class="enlace-menu <?php echo ($seccion == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> 
                    <span>TFGs</span>
                    <span class="etiqueta-contador"><?php echo $totalTFG; ?></span>
                </a>
            </div>
            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Área Administrativa</p>

                <a href="vistas/directores/verDirectores.php" class="enlace-menu <?php echo ($seccion == 'directores' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-tie"></i> 
                    <span>Directores</span> 
                    <span class="etiqueta-contador"><?php echo $totalDirectores; ?></span>
                </a>

                <a href="vistas/profesores/verProfesores.php" class="enlace-menu <?php echo ($seccion == 'profesores' ? 'activo' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i> 
                    <span>Profesores</span> 
                    <span class="etiqueta-contador"><?php echo $totalProfesores; ?></span>
                </a>
                
                <a href="vistas/pagos/verPagosGeneral.php" class="enlace-menu <?php echo ($seccion == 'pagos' ? 'activo' : ''); ?>">
                    <i class="fas fa-euro-sign"></i> 
                    <span>Pagos</span>
                    <span class="etiqueta-contador"><?php echo $totalPagos; ?></span>
                </a>

                <a href="vistas/anuncios/gestionAnuncios.php" class="enlace-menu <?php echo ($seccion == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> 
                    <span>Anuncios</span>
                    <span class="etiqueta-contador"><?php echo $totalAnuncios; ?></span>
                </a>

                <a href="vistas/reclamaciones/verReclamaciones.php" class="enlace-menu <?php echo ($seccion == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <span>Reclamaciones</span>
                    <span class="etiqueta-contador"><?php echo $totalReclamaciones; ?></span>
                </a>
            </div>

            <!-- 3. ÁREA DE RECURSOS -->
            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Área de Recursos</p>

                <a href="vistas/aulas/verAulas.php" class="enlace-menu <?php echo ($seccion == 'aulas' ? 'activo' : ''); ?>">
                    <i class="fas fa-door-open"></i> 
                    <span>Aulas</span> 
                    <span class="etiqueta-contador"><?php echo $totalAulas; ?></span>
                </a>

                <a href="vistas/inventario/verInventario.php" class="enlace-menu <?php echo ($seccion == 'inventario' ? 'activo' : ''); ?>">
                    <i class="fas fa-box"></i> 
                    <span>Inventario</span>
                    <span class="etiqueta-contador"><?php echo $totalInventario; ?></span>
                </a>

                <a href="vistas/inventario/gestionarPrestamos.php" class="enlace-menu <?php echo ($seccion == 'prestamos' ? 'activo' : ''); ?>">
                    <i class="fas fa-hand-holding"></i> 
                    <span>Préstamos</span>
                    <span class="etiqueta-contador"><?php echo $totalPrestamos; ?></span>
                </a>
            </div>

            <!-- 4. INFORMACIÓN DEL PROYECTO -->
            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Sobre el Sistema</p>
                <a href="vistas/comunes/creditos.php" class="enlace-menu enlace-creditos <?php echo ($seccion == 'creditos' ? 'activo' : ''); ?>">
                    <i class="fas fa-copyright"></i> <span>Copyright TFG</span>
                </a>
            </div>
        </nav>
    </aside>

    <main class="contenido-derecha">
