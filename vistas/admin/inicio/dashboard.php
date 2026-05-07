<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/panelDeControl.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$totalEstudiantesRegistrados = contarEstudiantes();
$totalProfesoresRegistrados = contarProfesores();
$totalRetosAcademicos = intval(contarRetos());
$totalModulosProfesionales = intval(contarModulos());
$porcentajeGlobalAprobados = obtenerPorcentajeAprobadosGlobal();
$cantidadTotalRecaudada = obtenerTotalRecaudado();
$totalOperacionesDePago = contarPagosRealizados();

$anunciosAMostrarPorPagina = 5;
$numeroPaginaActual = max(1, intval($_GET['p_anuncios'] ?? 1));
$totalAnunciosActivos = intval(contarAnunciosQueEstanActivos());
$totalPaginasAnuncios = ceil($totalAnunciosActivos / $anunciosAMostrarPorPagina);
$listaAnunciosSistema = listarAnunciosPaginados($numeroPaginaActual, $anunciosAMostrarPorPagina);

$listaEventosProximos = listarEventosProximos();
$titulo_pagina = "PANEL DE CONTROL - ADMIN";
$seccion = 'inicio';

include __DIR__ . '/../comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>RESUMEN DEL CENTRO</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">ANÁLISIS ACADÉMICO Y DATOS</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?= $totalEstudiantesRegistrados ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian">
    <div class="info-estadistica"><h3><?= $totalProfesoresRegistrados ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?= $totalModulosProfesionales ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?= $totalRetosAcademicos ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?= $porcentajeGlobalAprobados ?>%</h3><p>Aprobados</p></div>
  </div>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?= number_format($cantidadTotalRecaudada, 2, ',', '.') ?> €</h3><p>Total Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?= $totalOperacionesDePago ?></h3><p>Cobros Realizados</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>ACCIONES RÁPIDAS</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="../estudiantes/agregarEstudiantes.php" class="accion-rapida"><span>Nuevo Estudiante</span></a>
        <a href="../profesores/agregarProfesores.php" class="accion-rapida"><span>Nuevo Profesor</span></a>
        <a href="../pagos/agregarPagos.php" class="accion-rapida"><span>Registrar Pago</span></a>
        <a href="../anuncios/gestionAnuncios.php" class="accion-rapida"><span>🔔 Avisos</span></a>
        <a href="../eventos/gestionEventos.php" class="accion-rapida"><span>Nuevo Evento</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>TABLÓN DE ANUNCIOS</h3>
      </div>
      <?php if ($listaAnunciosSistema) { ?>
        <div class="lista-anuncios-dashboard">
            <?php foreach ($listaAnunciosSistema as $anuncioIndividual) { ?>
            <div class="anuncio-item">
                <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                    <strong class="anuncio-titulo"><?= strtoupper($anuncioIndividual['titulo']) ?></strong>
                    <small class="texto-atenuado"><?= date('d/m/Y H:i', strtotime($anuncioIndividual['fechaAnuncio'])) ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?= nl2br($anuncioIndividual['mensaje']) ?></p>
            </div>
            <?php } ?>
        </div>

        <?php if ($totalPaginasAnuncios > 1) { ?>
        <div class="paginacion">
            <?php if ($numeroPaginaActual > 1) { ?>
                <a href="dashboard.php?p_anuncios=<?= $numeroPaginaActual - 1 ?>" class="boton-paginacion"><i class="fas fa-chevron-left"></i></a>
            <?php } ?>

            <span class="boton-paginacion activo"><?= $numeroPaginaActual ?> / <?= $totalPaginasAnuncios ?></span>

            <?php if ($numeroPaginaActual < $totalPaginasAnuncios) { ?>
                <a href="dashboard.php?p_anuncios=<?= $numeroPaginaActual + 1 ?>" class="boton-paginacion"><i class="fas fa-chevron-right"></i></a>
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
            <p class="texto-atenuado">No hay eventos próximos programados.</p>
        <?php } else { ?>
            <?php
            $contadorEventosMostrados = 0;
            foreach ($listaEventosProximos as $eventoIndividual) {
                if ($contadorEventosMostrados < 4) {
                    $diaEvento = date('d', strtotime($eventoIndividual['fechaEvento']));
                    $mesEvento = strtoupper(date('M', strtotime($eventoIndividual['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $diaEvento ?></div><div class="mes"><?= $mesEvento ?></div></div>
              <div>
                <p class="texto-negrita"><?= strtoupper($eventoIndividual['tituloEvento']) ?></p>
                <p class="texto-atenuado"><?= date('H:i', strtotime($eventoIndividual['horaEvento'])) ?>h - <?= $eventoIndividual['ubicacionEvento'] ?></p>
              </div>
            </div>
            <?php
                    $contadorEventosMostrados++;
                }
            } ?>
        <?php } ?>
      </div>
      <div class="margen-arriba">
          <a href="../eventos/gestionEventos.php" class="boton-secundario ancho-total">GESTIONAR CALENDARIO</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
