<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
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

$totalEstudiantes = contarEstudiantes();
$totalProfesores = contarProfesores();
$totalRetos = (int)contarRetos();
$totalModulos = (int)contarModulos();
$porcentajeAprobados = obtenerPorcentajeAprobadosGlobal();

$totalRecaudado = obtenerTotalRecaudado();
$totalPagosRealizados = contarPagosRealizados();

$anunciosPorPagina = 5;
$paginaActual = 1;
if (isset($_GET['p_anuncios'])) {
    $paginaActual = $_GET['p_anuncios'];
}
if ($paginaActual < 1) {
    $paginaActual = 1;
}

$totalAnuncios = (int)contarAnunciosQueEstanActivos();
$totalPaginas = ceil($totalAnuncios / $anunciosPorPagina);
$listaAnuncios = listarAnunciosConPaginas($anunciosPorPagina);

// Obtener eventos
$listaEventos = listarEventosProximos();

$titulo_pagina = "Panel de Control - Super Admin";
$seccion = 'inicio';
include 'comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>Resumen del Centro</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Análisis Académico y Datos</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo $totalEstudiantes; ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian">
    <div class="info-estadistica"><h3><?php echo $totalProfesores; ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo $totalModulos; ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo $totalRetos; ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo $porcentajeAprobados; ?>%</h3><p>Aprobados</p></div>
  </div>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3 style="color:var(--color-primario);"><?php echo number_format($totalRecaudado, 2); ?> €</h3><p>Total Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3 style="color:var(--color-primario);"><?php echo $totalPagosRealizados; ?></h3><p>Cobros Realizados</p></div>
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
        <a href="/pfc/vistas/admin/anuncios/gestionAnuncios.php" class="accion-rapida"><span style="color:#ff9800;">🔔 Avisos y Push</span></a>
        <a href="/pfc/vistas/admin/eventos/gestionEventos.php" class="accion-rapida"><span>Nuevo Evento</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn texto-azul"></i> Tablón de Anuncios del Sistema</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div class="lista-anuncios-dashboard">
            <?php foreach ($listaAnuncios as $anuncio) { ?>
            <div class="anuncio-item">
                <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                    <strong class="anuncio-titulo"><?php echo $anuncio['titulo']; ?></strong>
                    <small class="texto-atenuado"><?php echo date('d/m/Y H:i', strtotime($anuncio['fechaAnuncio'])); ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?php echo $anuncio['mensaje']; ?></p>
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
        <h3>Próximos Eventos</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventos)) { ?>
            <p class="texto-atenuado">No hay eventos próximos.</p>
        <?php } else { ?>
            <?php 
            $contEventos = 0;
            foreach ($listaEventos as $evento) { 
                if ($contEventos < 4) {
                    $dia = date('d', strtotime($evento['fechaEvento']));
                    $mes = strtoupper(date('M', strtotime($evento['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?php echo $dia; ?></div><div class="mes"><?php echo $mes; ?></div></div>
              <div>
                <p class="texto-negrita"><?php echo $evento['tituloEvento']; ?></p>
                <p class="texto-atenuado"><?php echo date('H:i', strtotime($evento['horaEvento'])); ?>h - <?php echo $evento['ubicacionEvento']; ?></p>
              </div>
            </div>
            <?php 
                }
                $contEventos++;
            } ?>
        <?php } ?>
      </div>
      <div class="margen-arriba">
          <a href="/pfc/vistas/admin/eventos/gestionEventos.php" class="boton-secundario ancho-total" style="justify-content:center;">Gestionar Calendario</a>
      </div>
    </div>
  </div>
</div>

<?php include 'comunes/footer.php'; ?>