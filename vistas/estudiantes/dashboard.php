<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../modelos/pagos.php";
require_once __DIR__ . "/../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../modelos/eventos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios = listarTodosLosAnuncios();
$listaEventosProximos = listarEventosProximos();

// Validamos que el estudiante exista y obtenemos su ciclo
$idCiclo = 0;
if ($estudianteActual) {
    $idCiclo = $estudianteActual['idCiclo'];
}

$listaModulos = obtenerModulosPorCiclo($idCiclo);
$listaRetos = obtenerRetosPorCiclo($idCiclo);
$cantidadPagos = contarPagosEstudiante($idEstudiante);
$listaMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = "Panel de Control - Estudiante";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Â¡HOLA, <?php echo strtoupper($estudianteActual['nombreEstudiante']); ?>!</h1>
    <p class="texto-atenuado"><?php echo strtoupper($estudianteActual['nombreCiclo']); ?></p>
</div>

<!-- ESTADÃSTICAS EN FLEX -->
<div class="disposicion-flexible envoltura-flexible separacion-grande margen-abajo">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul flexible-rellenar">
        <div class="info-estadistica"><h3><?php echo count($listaModulos); ?></h3><p>MÃ³dulos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde flexible-rellenar">
        <div class="info-estadistica"><h3><?php echo count($listaRetos); ?></h3><p>Retos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-violeta flexible-rellenar">
        <div class="info-estadistica"><h3><?php echo $cantidadPagos; ?></h3><p>Pagos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-naranja flexible-rellenar">
        <div class="info-estadistica"><h3><?php echo count($listaMensajes); ?></h3><p>Mensajes</p></div>
    </div>
</div>

<div class="cuadricula-secundaria mt-30">
  <!-- COLUMNA IZQUIERDA: ANUNCIOS -->
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn texto-azul"></i> TABLÃ“N DE ANUNCIOS</h3>
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
                    <strong class="anuncio-titulo color-primario"><?php echo strtoupper($anu['tituloAnuncio']); ?></strong>
                    <small class="texto-atenuado"><?php echo date('d/m/Y', strtotime($anu['fechaAnuncio'])); ?></small>
                </div>
                <p class="texto-pequeno sin-margen mt-5"><?php echo substr(strip_tags($anu['contenidoAnuncio']), 0, 150); ?>...</p>
                <div class="mt-10 text-right">
                    <a href="/pfc/vistas/estudiantes/anuncios/lista.php" class="boton-secundario btn-pequeno">VER DETALLES</a>
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

  <!-- COLUMNA DERECHA: EVENTOS -->
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>PRÃ“XIMOS EVENTOS</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventosProximos)) { ?>
            <p class="texto-atenuado">No hay eventos prÃ³ximos.</p>
        <?php } else { ?>
            <?php 
            $cest = 0;
            foreach ($listaEventosProximos as $ev) { 
                if ($cest < 4) {
                    $d = date('d', strtotime($ev['fechaEvento']));
                    $m = strtoupper(date('M', strtotime($ev['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?php echo $d; ?></div><div class="mes"><?php echo $m; ?></div></div>
              <div>
                <p class="texto-negrita"><?php echo strtoupper($ev['tituloEvento']); ?></p>
                <p class="texto-atenuado"><?php echo date('H:i', strtotime($ev['horaEvento'])); ?>h - <?php echo $ev['ubicacionEvento']; ?></p>
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

<?php include __DIR__ . '/comunes/footer.php'; ?>
