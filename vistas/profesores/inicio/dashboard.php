<?php
session_start();

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/anuncios.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/eventos.php";
require_once __DIR__ . "/../../../modelos/tfg.php";

$profesorActual = obtenerProfesorPorId($idProfesor);
$listaAnuncios = listarTodosLosAnuncios();
$listaMensajes = listarMensajesParaProfesor($idProfesor);
$listaEstudiantes = listarEstudiantesDeProfesor($idProfesor);
$listaModulos = obtenerModulosDeProfesor($idProfesor);
$listaRetos = obtenerRetosDeProfesor($idProfesor);
$listaEventos = listarEventosProximos();

// Estadísticas TFG para el profesor
$listaTFGsProfesor = listarTFGsPorProfesor($idProfesor);
$totalTFGsProfesor = count($listaTFGsProfesor);
$calificadosTFGsProfesor = 0;
foreach ($listaTFGsProfesor as $tfg) {
    if (obtenerCalificacionTFG($tfg['idEstudiante'])) {
        $calificadosTFGsProfesor++;
    }
}

$mensajesPendientes = 0;
foreach ($listaMensajes as $mensaje) {
    if ($mensaje['estadoReclamacion'] === 'pendiente') {
        $mensajesPendientes++;
    }
}

$tituloDelPagina = "AULAPRO | PANEL DE CONTROL";
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo disposicion-flexible">
  <div>
    <h1>BIENVENIDO/A, <?= $profesorActual['nombreProfesor'] ?? '' ?></h1>
  </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<h2 class="margen-abajo texto-oscuro">Resumen de Actividad</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?= count($listaEstudiantes) ?></h3><p>Alumnos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?= count($listaModulos) ?></h3><p>Módulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?= count($listaRetos) ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?= $mensajesPendientes ?></h3><p>Mensajes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-morada">
    <div class="info-estadistica"><h3><?= $totalTFGsProfesor ?> / <?= $calificadosTFGsProfesor ?></h3><p>TFG (ENTREGADOS/OK)</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="disposicion-flexible direccion-columna separacion-grande flexible-rellenar">

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta"><h3>Acciones Rápidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="../calificaciones/lista.php" class="accion-rapida"><span>Poner Notas</span></a>
        <a href="../retos/lista.php" class="accion-rapida"><span>Nuevo Reto</span></a>
        <a href="../mensajes/lista.php" class="accion-rapida"><span>Ver Mensajes</span></a>
        <a href="../perfil/ver.php" class="accion-rapida"><span>Mi Perfil</span></a>
        <a href="#" class="accion-rapida" style="background: #3498db; color: white;" onclick="const f = document.getElementById('formMasivo'); f.classList.contains('oculto') ? f.classList.remove('oculto') : f.classList.add('oculto'); return false;">
          <span><i class="fas fa-paper-plane"></i> Notificar Notas</span>
        </a>
      </div>

      <div id="formMasivo" class="oculto" style="margin-top: 20px; padding: 15px; border: none; border-radius: 8px;">
        <h4 style="margin-top: 0;">Enviar Resultados por Email a un Ciclo</h4>
        <form action="../../../controladores/admin/academico/enviarNotasMasivo.php" method="POST">
          <select name="idCiclo" class="ancho-total" style="padding: 8px; margin-bottom: 10px;">
            <option value="">Seleccione un ciclo...</option>
            <?php
            $ciclosVistos = [];
            foreach ($listaModulos as $m) {
                if (!in_array($m['idCiclo'], $ciclosVistos)) {
                    $ciclosVistos[] = $m['idCiclo'];
            ?>
                <option value="<?= $m['idCiclo'] ?>"><?= $m['nombreCiclo'] ?></option>
            <?php } } ?>
          </select>
          <button type="submit" class="boton-primario ancho-total">ENVIAR A TODOS LOS ALUMNOS</button>
        </form>
      </div>
    </div>

    <div class="tarjeta-blanca">
      <div class="titulo-tarjeta">
        <h3>Últimos Avisos</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div>
            <?php
            $c = 0;
            foreach ($listaAnuncios as $anuncio) {
                if ($c < 4) {
            ?>
            <div class="anuncio-item">
                <div class="anuncio-contenido">
                    <strong class="anuncio-titulo"><?= $anuncio['titulo'] ?></strong>
                    <p class="texto-pequeno" style="margin: 0;"><?= substr($anuncio['mensaje'], 0, 100) ?>...</p>
                </div>
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
        <h3>Próximos Eventos</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventos)) { ?>
            <p class="texto-atenuado">No hay eventos próximos.</p>
        <?php } else { ?>
            <?php
            $ce = 0;
            foreach ($listaEventos as $ev) {
                if ($ce < 4) {
                    $d = date('d', strtotime($ev['fechaEvento']));
                    $m = mb_strtoupper(date('M', strtotime($ev['fechaEvento'])), 'UTF-8');
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= $d ?></div><div class="mes"><?= $m ?></div></div>
              <div>
                <p class="texto-negrita"><?= $ev['tituloEvento'] ?></p>
                <p class="texto-atenuado"><?= date('H:i', strtotime($ev['horaEvento'])) ?>h - <?= $ev['ubicacionEvento'] ?></p>
              </div>
            </div>
            <?php
                }
                $ce++;
            } ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
