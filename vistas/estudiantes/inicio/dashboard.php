<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/pagos.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios = listarTodosLosAnuncios();
$listaEventosProximos = listarEventosProximos();

$tfgActual = obtenerTFGporEstudiante($idEstudiante);
$califTFG = obtenerCalificacionTFG($idEstudiante);

$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = listarModulosPorCiclo($idCiclo);
$listaRetos = listarRetosPorCiclo($idCiclo);
$cantidadPagos = contarPagosEstudiante($idEstudiante);
$listaMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | PANEL DE CONTROL";
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>HOLA, <?= strtoupper($estudianteActual['nombreEstudiante']) ?>!</h1>
    <p class="texto-suave"><?= strtoupper($estudianteActual['nombreCiclo']) ?></p>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="caja caja-libre espacio-grande margen-abajo">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul relleno">
        <div class="info-estadistica"><h3><?= count($listaModulos) ?></h3><p>Módulos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde relleno">
        <div class="info-estadistica"><h3><?= count($listaRetos) ?></h3><p>Retos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-morada relleno">
        <div class="info-estadistica"><h3><?= $califTFG ? $califTFG['nota'] : (empty($tfgActual['archivoTFG']) ? 'PEND' : 'SUBIDO') ?></h3><p>TFG</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-violeta relleno">
        <div class="info-estadistica"><h3><?= $cantidadPagos ?></h3><p>Pagos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-naranja relleno">
        <div class="info-estadistica"><h3><?= count($listaMensajes) ?></h3><p>Mensajes</p></div>
    </div>
</div>

<div class="cuadricula-secundaria" style="margin-top: 30px;">
  <div class="caja direccion-columna espacio-grande relleno">

    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>TABLÓN DE ANUNCIOS</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div class="lista-anuncios-dashboard">
            <?php
            $canu = 0;
            foreach ($listaAnuncios as $anu) {
                if ($canu < 4) {
            ?>
            <div class="anuncio-item">
                <div class="anuncio-contenido">
                    <div class="caja espacio-entre-elementos alinear-centro">
                        <strong class="anuncio-titulo color-primario"><?= strtoupper($anu['tituloAnuncio']) ?></strong>
                        <span class="texto-suave"><?= date('d/m/Y', strtotime($anu['fechaAnuncio'])) ?></span>
                    </div>
                    <p class="texto-pequeno" style="margin: 0; margin-top: 5px;"><?= substr(strip_tags($anu['contenidoAnuncio']), 0, 150) ?>...</p>
                    <div style="margin-top: 10px; text-align: right;">
                        <a href="../anuncios/lista.php" class="boton-secundario btn-pequeno">VER DETALLES</a>
                    </div>
                </div>
            </div>
            <?php
                $canu++;
                }
            } ?>
        </div>
      <?php } else { ?>
        <p class="texto-suave">No hay anuncios activos actualmente.</p>
      <?php } ?>
    </div>
  </div>

  <div class="caja direccion-columna espacio-grande relleno">
    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>PRÓXIMOS EVENTOS</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventosProximos)) { ?>
            <p class="texto-suave">No hay eventos próximos.</p>
        <?php } else { ?>
            <?php
            $cest = 0;
            foreach ($listaEventosProximos as $ev) {
                if ($cest < 4) {
                    $d = date('d', strtotime($ev['fechaEvento']));
                    $m = strtoupper(date('M', strtotime($ev['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $d ?></div><div class="mes"><?= $m ?></div></div>
              <div>
                <p class="texto-negrita"><?= strtoupper($ev['tituloEvento']) ?></p>
                <p class="texto-suave"><?= date('H:i', strtotime($ev['horaEvento'])) ?>h - <?= $ev['ubicacionEvento'] ?></p>
              </div>
            </div>
            <?php
                    $cest++;
                }
            } ?>
        <?php } ?>
      </div>

    </div>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
