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

$totalEstudiantes = contarEstudiantes();
$totalProfesores = contarProfesores();
$totalRetos = intval(contarRetos());
$totalModulos = intval(contarModulos());
$pctAprobados = obtenerPorcentajeAprobadosGlobal();
$recaudado = obtenerTotalRecaudado();
$totalCobros = contarPagosRealizados();

$porPagina = 5;
$pagina = max(1, intval($_GET['p_anuncios'] ?? 1));
$totalAnuncios = intval(contarAnunciosQueEstanActivos());
$totalPaginas = ceil($totalAnuncios / $porPagina);
$listaAnuncios = listarAnunciosPaginados($pagina, $porPagina);

$eventos = listarEventosProximos();
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
    <div class="info-estadistica"><h3><?= $totalEstudiantes ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian">
    <div class="info-estadistica"><h3><?= $totalProfesores ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?= $totalModulos ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?= $totalRetos ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?= $pctAprobados ?>%</h3><p>Aprobados</p></div>
  </div>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?= number_format($recaudado, 2, ',', '.') ?> €</h3><p>Total Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica">
    <div class="info-estadistica"><h3><?= $totalCobros ?></h3><p>Cobros Realizados</p></div>
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
      <div class="titulo-tarjeta espacio-entre-elementos">
        <h3>TABLÓN DE ANUNCIOS</h3>
        <a href="../anuncios/gestionAnuncios.php" class="boton-secundario texto-pequeno">Gestionar</a>
      </div>
      <?php if ($listaAnuncios) { ?>
        <div class="lista-anuncios-dashboard">
            <?php foreach ($listaAnuncios as $anuncio) { ?>
            <div class="anuncio-item">
                <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                    <strong class="anuncio-titulo"><?= $anuncio['titulo'] ?></strong>
                    <small class="texto-atenuado"><?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?= nl2br($anuncio['mensaje']) ?></p>
                <div class="mt-5">
                    <span class="etiqueta-dirigido-a"><?= ucfirst($anuncio['dirigidoA']) ?></span>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php if ($totalPaginas > 1) { ?>
        <div class="paginacion">
            <?php if ($pagina > 1) { ?>
                <a href="dashboard.php?p_anuncios=<?= $pagina - 1 ?>" class="boton-paginacion"><i class="fas fa-chevron-left"></i></a>
            <?php } ?>
            <span class="boton-paginacion activo"><?= $pagina ?> / <?= $totalPaginas ?></span>
            <?php if ($pagina < $totalPaginas) { ?>
                <a href="dashboard.php?p_anuncios=<?= $pagina + 1 ?>" class="boton-paginacion"><i class="fas fa-chevron-right"></i></a>
            <?php } ?>
        </div>
        <?php } ?>

      <?php } else { ?>
        <p class="texto-atenuado">No hay anuncios activos.</p>
      <?php } ?>
    </div>
  </div>

  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>PRÓXIMOS EVENTOS</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($eventos)) { ?>
            <p class="texto-atenuado">No hay eventos próximos programados.</p>
        <?php } else { ?>
            <?php
            $i = 0;
            foreach ($eventos as $evento) {
                if ($i < 4) {
                    $dia = date('d', strtotime($evento['fechaEvento']));
                    $mes = strtoupper(date('M', strtotime($evento['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $dia ?></div><div class="mes"><?= $mes ?></div></div>
              <div>
                <p class="texto-negrita"><?= strtoupper($evento['tituloEvento']) ?></p>
                <p class="texto-atenuado"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h - <?= $evento['ubicacionEvento'] ?></p>
              </div>
            </div>
            <?php
                    $i++;
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
