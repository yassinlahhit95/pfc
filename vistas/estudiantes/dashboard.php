<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/calificaciones.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../modelos/retos.php";
require_once __DIR__ . "/../../modelos/pagos.php";
require_once __DIR__ . "/../../modelos/reclamaciones.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios = listarTodosLosAnuncios();
$listaCalificaciones = listarCalificacionesPorEstudiante($idEstudiante);

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

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>¡Hola, <?php echo $estudianteActual['nombreEstudiante']; ?>!</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Resumen Académico</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo count($listaModulos); ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo count($listaRetos); ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo $cantidadPagos; ?></h3><p>Pagos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo count($listaMensajes); ?></h3><p>Mensajes</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/estudiantes/calificaciones/lista.php" class="accion-rapida"><span>Mis Notas</span></a>
        <a href="/pfc/vistas/estudiantes/tfg/subir.php" class="accion-rapida"><span>Mi TFG</span></a>
        <a href="/pfc/vistas/estudiantes/mensajes/agregar.php" class="accion-rapida"><span>Nuevo Mensaje</span></a>
        <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="accion-rapida"><span>Mi Perfil</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-graduation-cap"></i> Calificaciones Recientes</h3>
      </div>
      <div class="contenedor-tabla">
        <table class="tabla-datos">
            <thead>
                <tr>
                    <th>Módulo</th>
                    <th>1ª Final</th>
                    <th>2ª Final</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listaCalificaciones)) { ?>
                    <tr><td colspan="3" class="sin-datos">Sin notas aún.</td></tr>
                <?php } else { ?>
                    <?php 
                    $contadorNotas = 0;
                    foreach ($listaCalificaciones as $calificacion) { 
                        if ($contadorNotas < 4) {
                    ?>
                    <tr>
                        <td><?php echo $calificacion['nombreModulo']; ?></td>
                        <td class="texto-negrita"><?php echo $calificacion['nota_1final'] ?: '-'; ?></td>
                        <td class="texto-negrita"><?php echo $calificacion['nota_2final'] ?: '-'; ?></td>
                    </tr>
                    <?php 
                        }
                        $contadorNotas++;
                    } ?>
                <?php } ?>
            </tbody>
        </table>
      </div>
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
            $cest = 0;
            foreach ($listaEventos as $ev) { 
                if ($cest < 3) {
                    $d = date('d', strtotime($ev['fechaEvento']));
                    $m = strtoupper(date('M', strtotime($ev['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?php echo $d; ?></div><div class="mes"><?php echo $m; ?></div></div>
              <div>
                <p class="texto-negrita"><?php echo $ev['tituloEvento']; ?></p>
                <p class="texto-atenuado"><?php echo date('H:i', strtotime($ev['horaEvento'])); ?>h - <?php echo $ev['ubicacionEvento']; ?></p>
              </div>
            </div>
            <?php 
                }
                $cest++;
            } ?>
        <?php } ?>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Datos del Ciclo</h3>
      </div>
      <div class="detalle-perfil-corto">
        <p><strong>Ciclo:</strong><br><?php echo $estudianteActual['nombreCiclo']; ?></p>
        <p><strong>Email:</strong><br><?php echo $estudianteActual['emailEstudiante']; ?></p>
        <hr class="margen-arriba">
        <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario ancho-total center-text">Ver Datos Completos</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>