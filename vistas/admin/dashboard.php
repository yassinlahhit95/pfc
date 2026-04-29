<?php
session_start();

// Control de acceso para administradores
if (empty($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../modelos/conectar.php";
require_once "../../modelos/panelDeControl.php";
require_once "../../modelos/anuncios.php";
require_once "../../modelos/eventos.php";
require_once "../../modelos/retos.php";
require_once "../../modelos/modulos.php";
require_once "../../modelos/estudiantes.php";

// Obtener estadísticas generales
$totalEstudiantesRegistrados = contarEstudiantes();
$totalProfesoresRegistrados = contarProfesores();
$totalRetosAcademicos = (int)contarRetos();
$totalModulosProfesionales = (int)contarModulos();
$porcentajeGlobalAprobados = obtenerPorcentajeAprobadosGlobal();

$cantidadTotalRecaudada = obtenerTotalRecaudado();
$totalOperacionesDePago = contarPagosRealizados();

// Lógica simple para paginación de anuncios
$anunciosAMostrarPorPagina = 5;
$numeroPaginaActual = 1;

if (isset($_GET['p_anuncios'])) {
    $numeroPaginaActual = (int)$_GET['p_anuncios'];
}

if ($numeroPaginaActual < 1) {
    $numeroPaginaActual = 1;
}

$totalAnunciosActivos = (int)contarAnunciosQueEstanActivos();
$totalPaginasAnuncios = ceil($totalAnunciosActivos / $anunciosAMostrarPorPagina);
$listaAnunciosSistema = listarAnunciosConPaginas($anunciosAMostrarPorPagina);

// Obtener eventos próximos
$listaEventosProximos = listarEventosProximos();

$titulo_pagina = "PANEL DE CONTROL - SUPER ADMIN";
$seccion = 'inicio';
include 'comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>RESUMEN DEL CENTRO</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">ANÁLISIS ACADÉMICO Y DATOS</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo $totalEstudiantesRegistrados; ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian">
    <div class="info-estadistica"><h3><?php echo $totalProfesoresRegistrados; ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo $totalModulosProfesionales; ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo $totalRetosAcademicos; ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo $porcentajeGlobalAprobados; ?>%</h3><p>Aprobados</p></div>
  </div>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?php echo number_format($cantidadTotalRecaudada, 2); ?> €</h3><p>Total Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?php echo $totalOperacionesDePago; ?></h3><p>Cobros Realizados</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>ACCIONES RÁPIDAS</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/admin/estudiantes/agregarEstudiantes.php" class="accion-rapida"><span>Nuevo Estudiante</span></a>
        <a href="/pfc/vistas/admin/profesores/agregarProfesores.php" class="accion-rapida"><span>Nuevo Profesor</span></a>
        <a href="/pfc/vistas/admin/pagos/agregarPagos.php" class="accion-rapida"><span>Registrar Pago</span></a>
        <a href="/pfc/vistas/admin/anuncios/gestionAnuncios.php" class="accion-rapida"><span>🔔 Avisos y Push</span></a>
        <a href="/pfc/vistas/admin/eventos/gestionEventos.php" class="accion-rapida"><span>Nuevo Evento</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn texto-azul"></i> TABLÓN DE ANUNCIOS</h3>
      </div>
      <?php if ($listaAnunciosSistema) { ?>
        <div class="lista-anuncios-dashboard">
            <?php foreach ($listaAnunciosSistema as $anuncioIndividual) { ?>
            <div class="anuncio-item">
                <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                    <strong class="anuncio-titulo"><?php echo strtoupper($anuncioIndividual['titulo']); ?></strong>
                    <small class="texto-atenuado"><?php echo date('d/m/Y H:i', strtotime($anuncioIndividual['fechaAnuncio'])); ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?php echo $anuncioIndividual['mensaje']; ?></p>
            </div>
            <?php } ?>
        </div>

        <?php if ($totalPaginasAnuncios > 1) { ?>
        <div class="paginacion">
            <?php if ($numeroPaginaActual > 1) { ?>
                <a href="dashboard.php?p_anuncios=<?php echo $numeroPaginaActual - 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-left"></i></a>
            <?php } ?>
            
            <span class="boton-paginacion activo"><?php echo $numeroPaginaActual; ?> / <?php echo $totalPaginasAnuncios; ?></span>

            <?php if ($numeroPaginaActual < $totalPaginasAnuncios) { ?>
                <a href="dashboard.php?p_anuncios=<?php echo $numeroPaginaActual + 1; ?>" class="boton-paginacion"><i class="fas fa-chevron-right"></i></a>
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
        <h3>PRÓXIMOS EVENTOS</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventosProximos)) { ?>
            <p class="texto-atenuado">No hay eventos próximos.</p>
        <?php } else { ?>
            <?php 
            $contadorEventosMostrados = 0;
            foreach ($listaEventosProximos as $eventoIndividual) { 
                if ($contadorEventosMostrados < 4) {
                    $diaEvento = date('d', strtotime($eventoIndividual['fechaEvento']));
                    $mesEvento = strtoupper(date('M', strtotime($eventoIndividual['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?php echo $diaEvento; ?></div><div class="mes"><?php echo $mesEvento; ?></div></div>
              <div>
                <p class="texto-negrita"><?php echo strtoupper($eventoIndividual['tituloEvento']); ?></p>
                <p class="texto-atenuado"><?php echo date('H:i', strtotime($eventoIndividual['horaEvento'])); ?>h - <?php echo $eventoIndividual['ubicacionEvento']; ?></p>
              </div>
            </div>
            <?php 
                    $contadorEventosMostrados++;
                }
            } ?>
        <?php } ?>
      </div>
      <div class="margen-arriba">
          <a href="/pfc/vistas/admin/eventos/gestionEventos.php" class="boton-secundario ancho-total">Gestionar Calendario</a>
      </div>
    </div>
  </div>
</div>

<?php include 'comunes/footer.php'; ?>

