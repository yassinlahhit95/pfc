<?php
// Comprobación de seguridad: Solo administradores pueden ver esta navegación
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

// Calculamos la ruta base de forma dinámica para que los estilos carguen siempre bien
// Contamos cuántas carpetas hay entre este archivo y la raíz de 'vistas'
$URL_ACTUAL = $_SERVER['PHP_SELF'];
$partesRuta = explode('/vistas/', $URL_ACTUAL);
$rutaRelativaVistas = $partesRuta[1] ?? '';
$numeroCarpetas = substr_count($rutaRelativaVistas, '/');
$ruta_base = str_repeat('../', $numeroCarpetas + 1);

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/tfg.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

// Obtenemos todos los contadores para las etiquetas del menú
$cantidadAlumnosMenu = contarEstudiantes();
$cantidadProfesoresMenu = contarProfesores();
$cantidadDirectoresMenu = contarDirectores();
$cantidadPagosMenu = contarPagos();
$cantidadAnunciosMenu = contarAnuncios();
$cantidadMensajesMenu = contarReclamaciones();
$cantidadMensajesNoLeidosAdmin = contarMensajesNoLeidosAdmin();
$cantidadCiclosMenu = contarCiclos();
$cantidadModulosMenu = contarModulos();
$cantidadRetosMenu = contarRetos();
$cantidadAulasMenu = contarAulas();
$cantidadArticulosMenu = contarInventario();
$cantidadPrestamosMenu = contarPrestamosActivos();
$cantidadTFGMenu = contarTFGsSubidos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo_pagina ?></title>
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/admin.css">
    <link rel="icon" href="<?= $ruta_base ?>public/imagenes/favicon.ico">
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/responsive.css">
    <link rel="stylesheet" href="<?= $ruta_base ?>public/css/notificaciones.css">
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
            <h3>ADMIN</h3>
        </div>

        <nav class="menu-navegacion">
            <a href="<?= $ruta_base ?>vistas/admin/dashboard.php" class="enlace-menu <?php if ($seccion == 'inicio') { echo 'activo'; } ?>">
                <i class="fas fa-chart-line"></i> <span>DASHBOARD</span>
            </a>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">GESTIÓN ACADÉMICA</p>
                
                <a href="<?= $ruta_base ?>vistas/admin/estudiantes/verEstudiantes.php" class="enlace-menu <?php if ($seccion == 'estudiantes') { echo 'activo'; } ?>">
                    <i class="fas fa-user-graduate"></i> <span>ESTUDIANTES</span>
                    <span class="etiqueta-contador"><?= $cantidadAlumnosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/ciclos/verCiclos.php" class="enlace-menu <?php if ($seccion == 'ciclos') { echo 'activo'; } ?>">
                    <i class="fas fa-layer-group"></i> <span>CICLOS FORMATIVOS</span>
                    <span class="etiqueta-contador"><?= $cantidadCiclosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/modulos/verModulos.php" class="enlace-menu <?php if ($seccion == 'modulos') { echo 'activo'; } ?>">
                    <i class="fas fa-book"></i> <span>MÓDULOS</span>
                    <span class="etiqueta-contador"><?= $cantidadModulosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/retos/verRetos.php" class="enlace-menu <?php if ($seccion == 'retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>RETOS / PROYECTOS</span>
                    <span class="etiqueta-contador"><?= $cantidadRetosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/academico/calificacionesModulos.php" class="enlace-menu <?php if ($seccion == 'notas_modulos') { echo 'activo'; } ?>">
                    <i class="fas fa-graduation-cap"></i> <span>NOTAS MÓDULOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/academico/calificacionesRetos.php" class="enlace-menu <?php if ($seccion == 'notas_retos') { echo 'activo'; } ?>">
                    <i class="fas fa-tasks"></i> <span>NOTAS RETOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/academico/resultadosFinales.php" class="enlace-menu <?php if ($seccion == 'resultados_modulos') { echo 'activo'; } ?>">
                    <i class="fas fa-check-double"></i> <span>RESULTADOS FINALES</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/pfc/verTFGs.php" class="enlace-menu <?php if ($seccion == 'tfg') { echo 'activo'; } ?>">
                    <i class="fas fa-file-pdf"></i> <span>GESTIÓN TFG</span>
                    <span class="etiqueta-contador"><?= $cantidadTFGMenu ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">PERSONAL Y CENTRO</p>

                <a href="<?= $ruta_base ?>vistas/admin/directores/verDirectores.php" class="enlace-menu <?php if ($seccion == 'directores') { echo 'activo'; } ?>">
                    <i class="fas fa-user-tie"></i> <span>DIRECTORES</span>
                    <span class="etiqueta-contador"><?= $cantidadDirectoresMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/profesores/verProfesores.php" class="enlace-menu <?php if ($seccion == 'profesores') { echo 'activo'; } ?>">
                    <i class="fas fa-chalkboard-teacher"></i> <span>PROFESORES</span>
                    <span class="etiqueta-contador"><?= $cantidadProfesoresMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/pagos/verPagosGeneral.php" class="enlace-menu <?php if ($seccion == 'pagos') { echo 'activo'; } ?>">
                    <i class="fas fa-wallet"></i> <span>PAGOS</span>
                    <span class="etiqueta-contador"><?= $cantidadPagosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/eventos/gestionEventos.php" class="enlace-menu <?php if ($seccion == 'eventos') { echo 'activo'; } ?>">
                    <i class="fas fa-calendar-alt"></i> <span>EVENTOS</span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/anuncios/gestionAnuncios.php" class="enlace-menu <?php if ($seccion == 'anuncios' || $seccion == 'push') { echo 'activo'; } ?>">
                    <i class="fas fa-bullhorn"></i> <span>AVISOS Y PUSH</span>
                    <span class="etiqueta-contador"><?= $cantidadAnunciosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/mensajes/lista.php" class="enlace-menu <?php if ($seccion == 'reclamaciones') { echo 'activo'; } ?>">
                    <i class="fas fa-envelope"></i> <span>MENSAJERÍA</span>
                    <span class="etiqueta-contador <?= ($cantidadMensajesNoLeidosAdmin > 0) ? 'alerta-roja' : '' ?>"><?= $cantidadMensajesMenu ?></span>
                </a>
            </div>

            <div class="seccion-del-menu">
                <p class="titulo-de-seccion">RECURSOS</p>

                <a href="<?= $ruta_base ?>vistas/admin/aulas/verAulas.php" class="enlace-menu <?php if ($seccion == 'aulas') { echo 'activo'; } ?>">
                    <i class="fas fa-door-open"></i> <span>AULAS</span>
                    <span class="etiqueta-contador"><?= $cantidadAulasMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/inventario/verInventario.php" class="enlace-menu <?php if ($seccion == 'inventario') { echo 'activo'; } ?>">
                    <i class="fas fa-boxes"></i> <span>INVENTARIO</span>
                    <span class="etiqueta-contador"><?= $cantidadArticulosMenu ?></span>
                </a>

                <a href="<?= $ruta_base ?>vistas/admin/inventario/gestionarPrestamos.php" class="enlace-menu <?php if ($seccion == 'prestamos') { echo 'activo'; } ?>">
                    <i class="fas fa-hand-holding"></i> <span>PRÉSTAMOS</span>
                    <span class="etiqueta-contador"><?= $cantidadPrestamosMenu ?></span>
                </a>
            </div>

            <div class="separador-menu-inferior">
                <a href="<?= $ruta_base ?>vistas/admin/directores/perfil.php" class="enlace-menu <?php if ($seccion == 'perfil') { echo 'activo'; } ?>">
                    <i class="fas fa-user-circle"></i> <span>MI PERFIL</span>
                </a>
                <a href="<?= $ruta_base ?>vistas/admin/comunes/creditos.php" class="enlace-menu enlace-creditos <?php if ($seccion == 'creditos') { echo 'activo'; } ?>">
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

    <main class="contenido-derecha">
    <?php if (isset($_SESSION['idAdmin'])) { ?>
        <div id="firebase-user-data" data-user-id="<?= $_SESSION['idAdmin'] ?>" data-user-role="admin" class="d-none"></div>
        <script type="module" src="<?= $ruta_base ?>public/js/firebase/firebase-init.js"></script>
    <?php } ?>


