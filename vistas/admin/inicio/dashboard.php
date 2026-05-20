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
require_once __DIR__ . "/../../../modelos/directores.php";

$totalEstudiantes = contarEstudiantes();
$totalProfesores = contarProfesores();
$totalRetos = contarRetos();
$totalModulos = contarModulos();
$recaudado = obtenerTotalRecaudado();
$totalCobros = contarPagosRealizados();

$totalTFGs = contarTFGsEntregados();

$adminInfo = obtenerDirectorPorId($_SESSION['idAdmin']);
$nombreAdmin = $adminInfo['nombreDirector'] ?? 'ADMINISTRADOR';

$listaAnuncios = listarTodosLosAnuncios();

$eventos = listarEventosProximos();
$titulo_pagina = "AULAPRO | PANEL DE CONTROL";
$seccion = 'inicio';

include __DIR__ . '/../comunes/nav.php';
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo caja">
  <div>
    <h1>BIENVENIDO/A, <?= strtoupper($nombreAdmin) ?></h1>
  </div>
</div>


<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?= $totalEstudiantes ?></h3><p>Estudiantes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian">
    <div class="info-estadistica"><h3><?= $totalProfesores ?></h3><p>Profesores</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?= $totalModulos ?></h3><p>MÓDULOS</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?= $totalRetos ?></h3><p>Retos</p></div>
  </div>
</div>

<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-morada">
    <div class="info-estadistica"><h3><?= number_format($recaudado, 2, ',', '.') ?> €</h3><p>Total Recaudado</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-cian-claro">
    <div class="info-estadistica"><h3><?= $totalCobros ?></h3><p>Cobros Realizados</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-morada">
    <div class="info-estadistica"><h3><?= $totalTFGs ?></h3><p>TFGs Entregados</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="caja direccion-columna espacio-grande relleno">

    <div class="panel">
      <div class="titulo-tarjeta"><h3>ACCIONES RÁPIDAS</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="../estudiantes/agregarEstudiantes.php" class="accion-rapida"><span>Nuevo Estudiante</span></a>
        <a href="../profesores/agregarProfesores.php" class="accion-rapida"><span>Nuevo Profesor</span></a>
        <a href="../pagos/agregarPagos.php" class="accion-rapida"><span>Registrar Pago</span></a>
        <a href="../anuncios/gestionAnuncios.php" class="accion-rapida"><span>?? Avisos</span></a>
        <a href="../eventos/gestionEventos.php" class="accion-rapida"><span>Nuevo Evento</span></a>
      </div>
    </div>


    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>TABLÓN DE ANUNCIOS</h3>
      </div>
      <?php if ($listaAnuncios) { ?>
        <div class="lista-anuncios-dashboard">
            <?php foreach ($listaAnuncios as $anuncio) { ?>
            <div class="anuncio-item">
                <div class="anuncio-contenido">
                    <div class="caja espacio-entre-elementos alinear-centro">
                        <strong class="anuncio-titulo"><?= strtoupper($anuncio['titulo']) ?></strong>
                        <span class="texto-suave"><?= date('d/m/Y', strtotime($anuncio['fechaAnuncio'])) ?></span>
                    </div>
                    <p class="texto-pequeno" style="margin: 0; margin-top: 5px;"><?= nl2br($anuncio['mensaje']) ?></p>
                    <div style="margin-top: 5px;">
                        <span class="texto-dirigido"><?= strtoupper($anuncio['dirigidoA']) ?></span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>


      <?php } else { ?>
        <p class="texto-suave">No hay anuncios activos por ahora.</p>
      <?php } ?>
    </div>
  </div>

  <div class="caja direccion-columna espacio-grande relleno">
    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>PRÓXIMOS EVENTOS</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($eventos)) { ?>
            <p class="texto-suave">No hay eventos próximos programados.</p>
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
                <p class="texto-suave"><?= date('H:i', strtotime($evento['horaEvento'])) ?>h - <?= $evento['ubicacionEvento'] ?></p>
              </div>
            </div>
            <?php
                    $i++;
                }
            } ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

