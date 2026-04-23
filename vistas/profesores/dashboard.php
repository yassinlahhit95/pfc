<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$profesor = obtenerProfesorPorId($idProfesor);
$anuncios = listarTodosLosAnuncios();
$reclamaciones = listarReclamacionesPorProfesor($idProfesor);
$estudiantes = listarEstudiantesPorProfesor($idProfesor);
$modulos = obtenerModulosDeProfesor($idProfesor);
$retos = obtenerRetosDeProfesor($idProfesor);

// Conteo de reclamaciones pendientes
$pendientes = 0;
foreach ($reclamaciones as $r) {
    if ($r['estadoReclamacion'] == 'pendiente') {
        $pendientes++;
    }
}

$tituloDelPagina = "Panel de Control - Profesor";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>Bienvenido/a, <?php echo $profesor['nombreProfesor']; ?></h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Resumen de Actividad</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo count($estudiantes); ?></h3><p>Alumnos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo count($modulos); ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo count($retos); ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo $pendientes; ?></h3><p>Reportes</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/profesores/calificaciones/insertar.php" class="accion-rapida"><span>Poner Notas</span></a>
        <a href="/pfc/vistas/profesores/retos/insertar.php" class="accion-rapida"><span>Nuevo Reto</span></a>
        <a href="/pfc/vistas/profesores/reclamaciones/verReclamaciones.php" class="accion-rapida"><span>Ver Reportes</span></a>
        <a href="/pfc/vistas/profesores/perfil/actualizar.php" class="accion-rapida"><span>Mi Perfil</span></a>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn"></i> Últimos Avisos</h3>
      </div>
      <?php if (!empty($anuncios)) { ?>
        <div>
            <?php 
            $c = 0;
            foreach ($anuncios as $anuncio) { 
                if ($c < 4) {
            ?>
            <div class="anuncio-item">
                <strong class="anuncio-titulo"><?php echo $anuncio['titulo']; ?></strong>
                <p class="texto-pequeno sin-margen"><?php echo substr($anuncio['mensaje'], 0, 100); ?>...</p>
            </div>
            <?php 
                }
                $c++;
            } ?>
        </div>
      <?php } else { ?>
        <p class="texto-atenuado">No hay anuncios activos.</p>
      <?php } ?>
    </div>
  </div>

  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Reportes Recientes</h3>
      </div>
      <div class="lista-actividad">
        <?php if (empty($reclamaciones)) { ?>
            <p class="texto-atenuado">Sin reportes pendientes.</p>
        <?php } else { ?>
            <?php 
            $cr = 0;
            foreach ($reclamaciones as $r) { 
                if ($cr < 5) {
            ?>
            <div class="elemento-actividad">
              <div>
                <p class="texto-negrita"><?php echo $r['nombreEstudiante']; ?></p>
                <p class="texto-atenuado"><?php echo $r['asunto']; ?></p>
              </div>
            </div>
            <?php 
                }
                $cr++;
            } ?>
        <?php } ?>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Información del Perfil</h3>
      </div>
      <div class="info-adicional-perfil">
        <p><strong>Especialidad:</strong><br><?php echo $profesor['especialidad'] ?: 'No definida'; ?></p>
        <p><strong>Email:</strong><br><?php echo $profesor['emailProfesor']; ?></p>
        <hr class="margen-arriba">
        <a href="/pfc/vistas/profesores/perfil/ver.php" class="boton-secundario ancho-total center-text">Gestionar Perfil</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>