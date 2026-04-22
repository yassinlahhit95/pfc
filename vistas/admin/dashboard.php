<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../modelos/conectar.php";
require_once "../../modelos/panelDeControl.php";
require_once "../../modelos/anuncios.php";

$cantidadEstudiantes = contarEstudiantes();
$cantidadProfesores = contarProfesores();
$cantidadDirectores = contarDirectores();
$dineroRecaudado = obtenerTotalRecaudado();
$pagosPendientes = contarPagosPendientes();

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
include 'comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>Resumen del Centro</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Análisis de Datos</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo $cantidadEstudiantes; ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo $cantidadProfesores; ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo number_format($dineroRecaudado, 2); ?> €</h3><p>Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo $pagosPendientes; ?></h3><p>Pendientes</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/admin/estudiantes/agregarEstudiantes.php" class="accion-rapida"><span>Nuevo Estudiante</span></a>
        <a href="/pfc/vistas/admin/profesores/agregarProfesores.php" class="accion-rapida"><span>Nuevo Profesor</span></a>
        <a href="/pfc/vistas/admin/pagos/agregarPagos.php" class="accion-rapida"><span>Registrar Pago</span></a>
        <a href="/pfc/vistas/admin/anuncios/gestionAnuncios.php" class="accion-rapida"><span>Nuevo Anuncio</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Tablón de Anuncios</h3>
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

        <?php if ($totalPaginas > 1) { ?>
        <div class="paginacion">
            <?php if ($paginaActual > 1) { ?>
                <a href="dashboard.php?p_anuncios=<?php echo $paginaActual - 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-left"></i></a>
            <?php } ?>
            
            <span class="boton-paginacion activo"><?php echo $paginaActual; ?> / <?php echo $totalPaginas; ?></span>

            <?php if ($paginaActual < $totalPaginas) { ?>
                <a href="dashboard.php?p_anuncios=<?php echo $paginaActual + 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-right"></i></a>
            <?php } ?>
        </div>
        <?php } ?>

      <?php } else { ?>
        <p class="texto-atenuado">No hay anuncios activos actualmente.</p>
      <?php } ?>
    </div>
  </div>

  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Actividad Reciente</h3>
      </div>
      <div class="lista-actividad">
        <div class="elemento-actividad">
          <div>
            <p class="texto-negrita">Nuevos registros</p>
            <p class="texto-atenuado">Actualización diaria realizada</p>
          </div>
        </div>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Próximos Eventos</h3>
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

<?php include 'comunes/footer.php'; ?>
