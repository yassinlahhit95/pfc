<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
unset($_SESSION['error'], $_SESSION['exito']);

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

// Datos TFG
$tfgActual = obtenerTFGporEstudiante($idEstudiante);
$califTFG = obtenerCalificacionTFG($idEstudiante);

$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = obtenerModulosPorCiclo($idCiclo);
$listaRetos = obtenerRetosPorCiclo($idCiclo);
$cantidadPagos = contarPagosEstudiante($idEstudiante);
$listaMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "AULAPRO | PANEL DE CONTROL";
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>¡HOLA, <?= mb_strtoupper($estudianteActual['nombreEstudiante'], 'UTF-8') ?>!</h1>
    <p class="texto-atenuado"><?= mb_strtoupper($estudianteActual['nombreCiclo'], 'UTF-8') ?></p>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="disposicion-flexible envoltura-flexible separacion-grande margen-abajo">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul flexible-rellenar">
        <div class="info-estadistica"><h3><?= count($listaModulos) ?></h3><p>Módulos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde flexible-rellenar">
        <div class="info-estadistica"><h3><?= count($listaRetos) ?></h3><p>Retos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-morada flexible-rellenar">
        <div class="info-estadistica"><h3><?= $califTFG ? $califTFG['nota'] : (empty($tfgActual['archivoTFG']) ? 'PEND' : 'SUBIDO') ?></h3><p>TFG</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-violeta flexible-rellenar">
        <div class="info-estadistica"><h3><?= $cantidadPagos ?></h3><p>Pagos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-naranja flexible-rellenar">
        <div class="info-estadistica"><h3><?= count($listaMensajes) ?></h3><p>Mensajes</p></div>
    </div>
</div>

<div class="cuadricula-secundaria" style="margin-top: 30px;">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">

    <div class="tarjeta-blanca">
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
                    <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                        <strong class="anuncio-titulo color-primario"><?= mb_strtoupper($anu['tituloAnuncio'], 'UTF-8') ?></strong>
                        <small class="texto-atenuado"><?= date('d/m/Y', strtotime($anu['fechaAnuncio'])) ?></small>
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
            $cest = 0;
            foreach ($listaEventosProximos as $ev) {
                if ($cest < 4) {
                    $d = date('d', strtotime($ev['fechaEvento']));
                    $m = mb_strtoupper(date('M', strtotime($ev['fechaEvento'])), 'UTF-8');
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $d ?></div><div class="mes"><?= $m ?></div></div>
              <div>
                <p class="texto-negrita"><?= mb_strtoupper($ev['tituloEvento'], 'UTF-8') ?></p>
                <p class="texto-atenuado"><?= date('H:i', strtotime($ev['horaEvento'])) ?>h - <?= $ev['ubicacionEvento'] ?></p>
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
