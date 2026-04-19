<?php
session_start();
require_once "modelos/conectar.php";
require_once "modelos/panelDeControl.php";
require_once "modelos/anuncios.php";

// Usamos las funciones del panel de control
$cantidadEstudiantes = contarEstudiantes();
$cantidadProfesores = contarProfesores();
$cantidadDirectores = contarDirectores();
$dineroRecaudado = obtenerTotalRecaudado();
$pagosPendientes = contarPagosPendientes();

// Configuracion de anuncios
$anunciosPorPagina = 5;
$paginaActual = 1;
if (isset($_GET['p_anuncios'])) {
    $paginaActual = (int)$_GET['p_anuncios'];
}
if ($paginaActual < 1) { 
    $paginaActual = 1; 
}

$totalAnuncios = contarAnunciosQueEstanActivos();
$totalPaginas = ceil($totalAnuncios / $anunciosPorPagina);
$listaAnuncios = listarAnunciosConPaginas($anunciosPorPagina);

$titulo_pagina = "Panel de Control - Super Admin";
$seccion = 'inicio';
include 'vistas/comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>Resumen del Centro</h1>
    <p class="texto-atenuado">Bienvenido de nuevo, Administrador</p>
  </div>
</div>

<!-- Sección de Estadísticas -->
<h2 class="margen-abajo texto-oscuro"><i class="fas fa-chart-pie"></i> Análisis de Datos</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="icono-estadistica"><i class="fas fa-user-graduate"></i></div>
    <div class="info-estadistica"><h3><?php echo $cantidadEstudiantes; ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="icono-estadistica"><i class="fas fa-chalkboard-teacher"></i></div>
    <div class="info-estadistica"><h3><?php echo $cantidadProfesores; ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="icono-estadistica"><i class="fas fa-hand-holding-usd"></i></div>
    <div class="info-estadistica"><h3><?php echo number_format($dineroRecaudado, 2); ?> €</h3><p>Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="icono-estadistica"><i class="fas fa-clock"></i></div>
    <div class="info-estadistica"><h3><?php echo $pagosPendientes; ?></h3><p>Pendientes</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <!-- Columna de Acciones y Anuncios -->
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <!-- Botones de Acceso Rápido -->
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="vistas/estudiantes/agregarEstudiantes.php" class="accion-rapida"><i class="fas fa-user-plus"></i><span>Nuevo Estudiante</span></a>
        <a href="vistas/profesores/agregarProfesores.php" class="accion-rapida"><i class="fas fa-chalkboard-teacher"></i><span>Nuevo Profesor</span></a>
        <a href="vistas/pagos/agregarPagos.php" class="accion-rapida"><i class="fas fa-euro-sign"></i><span>Registrar Pago</span></a>
        <a href="vistas/anuncios/gestionAnuncios.php" class="accion-rapida"><i class="fas fa-bullhorn"></i><span>Nuevo Anuncio</span></a>
      </div>
    </div>

    <!-- Lista de Anuncios -->
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn"></i> Tablón de Anuncios</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div>
            <?php foreach ($listaAnuncios as $anuncio) { ?>
            <div class="anuncio-item">
                <strong class="anuncio-titulo"><?php echo $anuncio['titulo']; ?></strong>
                <p class="texto-pequeno sin-margen"><?php echo $anuncio['mensaje']; ?></p>
            </div>
            <?php } ?>
        </div>

        <!-- Botones de Paginación -->
        <?php if ($totalPaginas > 1) { ?>
        <div class="paginacion">
            <?php if ($paginaActual > 1) { ?>
                <a href="dashboardAdmin.php?p_anuncios=<?php echo $paginaActual - 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-left"></i></a>
            <?php } ?>
            
            <span class="boton-paginacion activo"><?php echo $paginaActual; ?> / <?php echo $totalPaginas; ?></span>

            <?php if ($paginaActual < $totalPaginas) { ?>
                <a href="dashboardAdmin.php?p_anuncios=<?php echo $paginaActual + 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-right"></i></a>
            <?php } ?>
        </div>
        <?php } ?>

      <?php } else { ?>
        <p class="texto-atenuado">No hay anuncios activos actualmente.</p>
      <?php } ?>
    </div>
  </div>

  <!-- Columna de Actividad y Eventos -->
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-history color-primary mr-10"></i> Actividad Reciente</h3>
      </div>
      <div class="lista-actividad">
        <div class="elemento-actividad">
          <div class="icono-actividad azul"><i class="fas fa-user-plus"></i></div>
          <div>
            <p class="texto-negrita">Nuevos registros</p>
            <p class="texto-atenuado">Actualización diaria realizada</p>
          </div>
        </div>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-calendar-week color-success mr-10"></i> Próximos Eventos</h3>
      </div>
      <div class="lista-eventos">
        <div class="elemento-evento">
          <div class="fecha-evento azul"><div class="dia">10</div><div class="mes">DIC</div></div>
          <div>
            <p class="texto-negrita">Exámenes 1ª Evaluación</p>
            <p class="texto-atenuado">Todo el día</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include 'vistas/comunes/footer.php'; ?>
