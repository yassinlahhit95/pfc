<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$totalEstudiantes = contarEstudiantes();
$totalProfesores = contarProfesores();
$totalDirectores = contarDirectores();
$totalPagos = contarPagos();
$totalAnuncios = contarAnuncios();
$totalReclamaciones = contarReclamaciones();
$totalCiclos = contarCiclos();
$totalModulos = contarModulos();
$totalRetos = contarRetos();
$totalAulas = contarAulas();
$totalInventario = contarInventario();
$totalPrestamos = contarPrestamosActivos();
$totalTFG = contarTFGsSubidos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>
    <link rel="stylesheet" href="/pfc/public/css/admin.css">
    <link rel="stylesheet" href="/pfc/public/css/responsive.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>


<button class="menu-toggle solo-movil" onclick="toggleMenu()">
    <i class="fas fa-bars"></i>
</button>

<div class="contenedor-principal">
    <aside class="barra-lateral" id="barraLateral">
        <div class="cabecera-menu">
            <h3>Super Admin</h3>
        </div>

        <nav class="menu-navegacion">
            <a href="/pfc/vistas/admin/dashboard.php" class="enlace-menu <?php echo ($seccion == 'inicio' ? 'activo' : ''); ?>">
                <i class="fas fa-chart-line"></i> <span>Dashboard</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Gestión Académica</p>
                
                <a href="/pfc/vistas/admin/estudiantes/verEstudiantes.php" class="enlace-menu <?php echo ($seccion == 'estudiantes' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-graduate"></i> <span>Estudiantes</span>
                    <span class="etiqueta-contador"><?php echo $totalEstudiantes; ?></span>
                </a>

                <a href="/pfc/vistas/admin/ciclos/verCiclos.php" class="enlace-menu <?php echo ($seccion == 'ciclos' ? 'activo' : ''); ?>">
                    <i class="fas fa-layer-group"></i> <span>Ciclos Formativos</span>
                    <span class="etiqueta-contador"><?php echo $totalCiclos; ?></span>
                </a>

                <a href="/pfc/vistas/admin/modulos/verModulos.php" class="enlace-menu <?php echo ($seccion == 'modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-book"></i> <span>Módulos</span>
                    <span class="etiqueta-contador"><?php echo $totalModulos; ?></span>
                </a>

                <a href="/pfc/vistas/admin/retos/verRetos.php" class="enlace-menu <?php echo ($seccion == 'retos' ? 'activo' : ''); ?>">
                    <i class="fas fa-tasks"></i> <span>Retos / Proyectos</span>
                    <span class="etiqueta-contador"><?php echo $totalRetos; ?></span>
                </a>

                <a href="/pfc/vistas/admin/academico/calificacionesModulos.php" class="enlace-menu <?php echo ($seccion == 'notas_modulos' ? 'activo' : ''); ?>">
                    <i class="fas fa-graduation-cap"></i> <span>Notas Módulos</span>
                </a>

                <a href="/pfc/vistas/admin/tfg/verTFGs.php" class="enlace-menu <?php echo ($seccion == 'tfg' ? 'activo' : ''); ?>">
                    <i class="fas fa-file-pdf"></i> <span>Gestión TFG</span>
                    <span class="etiqueta-contador"><?php echo $totalTFG; ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Personal y Centro</p>

                <a href="/pfc/vistas/admin/directores/verDirectores.php" class="enlace-menu <?php echo ($seccion == 'directores' ? 'activo' : ''); ?>">
                    <i class="fas fa-user-tie"></i> <span>Directores</span>
                    <span class="etiqueta-contador"><?php echo $totalDirectores; ?></span>
                </a>

                <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="enlace-menu <?php echo ($seccion == 'profesores' ? 'activo' : ''); ?>">
                    <i class="fas fa-chalkboard-teacher"></i> <span>Profesores</span>
                    <span class="etiqueta-contador"><?php echo $totalProfesores; ?></span>
                </a>

                <a href="/pfc/vistas/admin/pagos/verPagosGeneral.php" class="enlace-menu <?php echo ($seccion == 'pagos' ? 'activo' : ''); ?>">
                    <i class="fas fa-wallet"></i> <span>Pagos</span>
                    <span class="etiqueta-contador"><?php echo $totalPagos; ?></span>
                </a>

                <a href="/pfc/vistas/admin/anuncios/gestionAnuncios.php" class="enlace-menu <?php echo ($seccion == 'anuncios' ? 'activo' : ''); ?>">
                    <i class="fas fa-bullhorn"></i> <span>Anuncios</span>
                    <span class="etiqueta-contador"><?php echo $totalAnuncios; ?></span>
                </a>

                <a href="/pfc/vistas/admin/reclamaciones/verReclamaciones.php" class="enlace-menu <?php echo ($seccion == 'reclamaciones' ? 'activo' : ''); ?>">
                    <i class="fas fa-exclamation-triangle"></i> <span>Reclamaciones</span>
                    <span class="etiqueta-contador"><?php echo $totalReclamaciones; ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">Recursos</p>

                <a href="/pfc/vistas/admin/aulas/verAulas.php" class="enlace-menu <?php echo ($seccion == 'aulas' ? 'activo' : ''); ?>">
                    <i class="fas fa-door-open"></i> <span>Aulas</span>
                    <span class="etiqueta-contador"><?php echo $totalAulas; ?></span>
                </a>

                <a href="/pfc/vistas/admin/inventario/verInventario.php" class="enlace-menu <?php echo ($seccion == 'inventario' ? 'activo' : ''); ?>">
                    <i class="fas fa-boxes"></i> <span>Inventario</span>
                    <span class="etiqueta-contador"><?php echo $totalInventario; ?></span>
                </a>

                <a href="/pfc/vistas/admin/inventario/gestionarPrestamos.php" class="enlace-menu <?php echo ($seccion == 'prestamos' ? 'activo' : ''); ?>">
                    <i class="fas fa-hand-holding"></i> <span>Préstamos</span>
                    <span class="etiqueta-contador"><?php echo $totalPrestamos; ?></span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="/pfc/vistas/admin/comunes/creditos.php" class="enlace-menu enlace-creditos <?php echo ($seccion == 'creditos' ? 'activo' : ''); ?>">
                    <i class="fas fa-info-circle"></i> <span>Créditos</span>
                </a>
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

    <main class="contenido-derecha">
