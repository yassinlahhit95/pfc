<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../index.php");
    exit;
}

require_once __DIR__ . "/../../modelos/profesores.php";
require_once __DIR__ . "/../../modelos/anuncios.php";
require_once __DIR__ . "/../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../modelos/estudiantes.php";
require_once __DIR__ . "/../../modelos/modulos.php";
require_once __DIR__ . "/../../modelos/retos.php";

$idProfesor = $_SESSION['idProfesor'];
$profesorActual = obtenerProfesorPorId($idProfesor);
$listaAnuncios = listarTodosLosAnuncios();
$listaMensajes = listarMensajesParaProfesor($idProfesor);
$listaEstudiantes = listarEstudiantesPorProfesor($idProfesor);
$listaModulos = obtenerModulosDeProfesor($idProfesor);
$listaRetos = obtenerRetosDeProfesor($idProfesor);

// Conteo de mensajes pendientes
$mensajesPendientes = 0;
foreach ($listaMensajes as $mensaje) {
    if ($mensaje['estadoReclamacion'] == 'pendiente') {
        $mensajesPendientes++;
    }
}

$tituloDelPagina = "Panel de Control - Profesor";
$seccionActual = 'inicio';
include_once __DIR__ . "/comunes/nav.php";
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>Bienvenido/a, <?php echo $profesorActual['nombreProfesor']; ?></h1>
  </div>
</div>

<h2 class="margen-abajo texto-oscuro">Resumen de Actividad</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?php echo count($listaEstudiantes); ?></h3><p>Alumnos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?php echo count($listaModulos); ?></h3><p>MÃ³dulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?php echo count($listaRetos); ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?php echo $mensajesPendientes; ?></h3><p>Mensajes</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">
    
    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones RÃ¡pidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="/pfc/vistas/profesores/calificaciones/agregar.php" class="accion-rapida"><span>Poner Notas</span></a>
        <a href="/pfc/vistas/profesores/retos/insertar.php" class="accion-rapida"><span>Nuevo Reto</span></a>
        <a href="/pfc/vistas/profesores/mensajes/lista.php" class="accion-rapida"><span>Ver Mensajes</span></a>
        <a href="/pfc/vistas/profesores/perfil/ver.php" class="accion-rapida"><span>Mi Perfil</span></a>
        <a href="#" class="accion-rapida color-secundario text-white" onclick="document.getElementById('formMasivo').style.display='block'; return false;">
          <span><i class="fas fa-paper-plane"></i> Notificar Notas</span>
        </a>
      </div>
      
      <div id="formMasivo" class="d-none mt-20 p-15 border-secundario rounded-8">
        <h4 class="mt-0">Enviar Resultados por Email a un Ciclo</h4>
        <form action="/pfc/controladores/admin/academico/enviarNotasMasivo.php" method="POST">
          <select name="idCiclo" class="ancho-total p-8 mb-10">
            <option value="">Seleccione un ciclo...</option>
            <?php 
            $ciclosVistos = [];
            foreach ($listaModulos as $m) { 
                if (!in_array($m['idCiclo'], $ciclosVistos)) {
                    $ciclosVistos[] = $m['idCiclo'];
            ?>
                <option value="<?php echo $m['idCiclo']; ?>"><?php echo $m['nombreCiclo']; ?></option>
            <?php } } ?>
          </select>
          <button type="submit" class="boton-primario ancho-total bg-secundario">Enviar a todos los alumnos</button>
        </form>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3><i class="fas fa-bullhorn"></i> Ãšltimos Avisos</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div>
            <?php 
            $c = 0;
            foreach ($listaAnuncios as $anuncio) { 
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
        <h3>PrÃ³ximos Eventos</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventos)) { ?>
            <p class="texto-atenuado">No hay eventos prÃ³ximos.</p>
        <?php } else { ?>
            <?php 
            $ce = 0;
            foreach ($listaEventos as $ev) { 
                if ($ce < 4) {
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
                $ce++;
            } ?>
        <?php } ?>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>InformaciÃ³n del Perfil</h3>
      </div>
      <div class="info-adicional-perfil">
        <p><strong>Email:</strong><br><?php echo $profesorActual['emailProfesor']; ?></p>
        <hr class="margen-arriba">
        <a href="/pfc/vistas/profesores/perfil/ver.php" class="boton-secundario ancho-total center-text">Gestionar Perfil</a>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/comunes/footer.php'; ?>
