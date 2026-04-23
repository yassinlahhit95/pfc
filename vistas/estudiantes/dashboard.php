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
$estudiante = obtenerEstudiantePorId($idEstudiante);
$anuncios = listarTodosLosAnuncios();
$notas = listarCalificacionesPorEstudiante($idEstudiante);
$idCiclo = $estudiante['idCiclo'];

$modulos = obtenerModulosPorCiclo($idCiclo);
$retos = obtenerRetosPorCiclo($idCiclo);
$pagos = contarPagosEstudiante($idEstudiante);
$reclamaciones = listarReclamacionesPorEstudiante($idEstudiante);

$tituloDelPagina = "Panel de Control - Estudiante";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>¡Hola, <?php echo $estudiante['nombreEstudiante']; ?>!</h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Resumen Académico</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo count($modulos); ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo count($retos); ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo $pagos; ?></h3><p>Pagos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo count($reclamaciones); ?></h3><p>Reclamaciones</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/estudiantes/calificaciones/lista.php" class="accion-rapida"><span>Mis Notas</span></a>
        <a href="/pfc/vistas/estudiantes/tfg/subir.php" class="accion-rapida"><span>Mi TFG</span></a>
        <a href="/pfc/vistas/estudiantes/reclamaciones/agregar.php" class="accion-rapida"><span>Nueva Reclamación</span></a>
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
                <?php if (empty($notas)) { ?>
                    <tr><td colspan="3" class="sin-datos">Sin notas aún.</td></tr>
                <?php } else { ?>
                    <?php 
                    $cn = 0;
                    foreach ($notas as $n) { 
                        if ($cn < 4) {
                    ?>
                    <tr>
                        <td><?php echo $n['nombreModulo']; ?></td>
                        <td class="texto-negrita"><?php echo $n['nota_1final'] ?: '-'; ?></td>
                        <td class="texto-negrita"><?php echo $n['nota_2final'] ?: '-'; ?></td>
                    </tr>
                    <?php 
                        }
                        $cn++;
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
        <h3><i class="fas fa-bullhorn"></i> Últimos Avisos</h3>
      </div>
      <div class="lista-actividad">
        <?php if (empty($anuncios)) { ?>
            <p class="texto-atenuado">No hay avisos del centro.</p>
        <?php } else { ?>
            <?php 
            $ca = 0;
            foreach ($anuncios as $anuncio) { 
                if ($ca < 3) {
            ?>
            <div class="anuncio-item">
                <strong class="anuncio-titulo"><?php echo $anuncio['titulo']; ?></strong>
                <p class="texto-pequeno sin-margen"><?php echo substr($anuncio['mensaje'], 0, 80); ?>...</p>
            </div>
            <?php 
                }
                $ca++;
            } ?>
        <?php } ?>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Datos del Ciclo</h3>
      </div>
      <div class="detalle-perfil-corto">
        <p><strong>Ciclo:</strong><br><?php echo $estudiante['nombreCiclo']; ?></p>
        <p><strong>Email:</strong><br><?php echo $estudiante['emailEstudiante']; ?></p>
        <hr class="margen-arriba">
        <a href="/pfc/vistas/estudiantes/perfil/ver.php" class="boton-secundario ancho-total center-text">Ver Datos Completos</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>