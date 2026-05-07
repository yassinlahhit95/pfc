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

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios = listarTodosLosAnuncios();
$listaEventosProximos = listarEventosProximos();

$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = obtenerModulosPorCiclo($idCiclo);
$listaRetos = obtenerRetosPorCiclo($idCiclo);
$cantidadPagos = contarPagosEstudiante($idEstudiante);
$listaMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "Panel de Control - Estudiante";
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>¡HOLA, <?= strtoupper($estudianteActual['nombreEstudiante']) ?>!</h1>
    <p class="texto-atenuado"><?= strtoupper($estudianteActual['nombreCiclo']) ?></p>
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
    <div class="tarjeta-estadistica tarjeta-estadistica-violeta flexible-rellenar">
        <div class="info-estadistica"><h3><?= $cantidadPagos ?></h3><p>Pagos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-naranja flexible-rellenar">
        <div class="info-estadistica"><h3><?= count($listaMensajes) ?></h3><p>Mensajes</p></div>
    </div>
</div>

<div class="cuadricula-secundaria mt-30">
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
                <div class="disposicion-flexible espacio-entre-elementos alinear-centro">
                    <strong class="anuncio-titulo color-primario"><?= strtoupper($anu['tituloAnuncio']) ?></strong>
                    <small class="texto-atenuado"><?= date('d/m/Y', strtotime($anu['fechaAnuncio'])) ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?= substr(strip_tags($anu['contenidoAnuncio']), 0, 150) ?>...</p>
                <div class="mt-10 text-right">
                    <a href="../anuncios/lista.php" class="boton-secundario btn-pequeno">VER DETALLES</a>
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
                    $m = strtoupper(date('M', strtotime($ev['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $d ?></div><div class="mes"><?= $m ?></div></div>
              <div>
                <p class="texto-negrita"><?= strtoupper($ev['tituloEvento']) ?></p>
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
