<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) {
    header("Location: ../../login.php");
    exit;
}

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
$listaModulos = listarModulosDeProfesor($idProfesor);
$listaRetos = listarRetosDeProfesor($idProfesor);
$listaEventos = listarEventosProximos();

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
    if ($mensaje['estadoReclamacion'] == 'pendiente') {
        $mensajesPendientes++;
    }
}

$tituloDelPagina = "AULAPRO | PANEL DE CONTROL";
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="espacio-entre-elementos alinear-centro margen-abajo caja">
  <div>
    <h1>BIENVENIDO/A, <?= Security::escapeHtml($profesorActual['nombreProfesor'] ?? '') ?></h1>
  </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>

<h2 class="margen-abajo texto-oscuro">Resumen de Actividad</h2>
<div class="cuadricula-estadisticas">
  <div class="tarjeta-estadistica tarjeta-estadistica-azul">
    <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaEstudiantes)) ?></h3><p>Alumnos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-verde">
    <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaModulos)) ?></h3><p>Modulos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
    <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaRetos)) ?></h3><p>Retos</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
    <div class="info-estadistica"><h3><?= Security::escapeHtml($mensajesPendientes ) ?></h3><p>Mensajes</p></div>
  </div>
  <div class="tarjeta-estadistica tarjeta-estadistica-morada">
    <div class="info-estadistica"><h3><?= Security::escapeHtml($totalTFGsProfesor ) ?> / <?= Security::escapeHtml($calificadosTFGsProfesor ) ?></h3><p>TFG (ENTREGADOS/OK)</p></div>
  </div>
</div>

<div class="cuadricula-secundaria">
  <div class="caja direccion-columna espacio-grande relleno">

    <div class="panel">
      <div class="titulo-tarjeta"><h3>Acciones Rapidas</h3></div>
      <div class="cuadricula-acciones-rapidas">
        <a href="../calificaciones/lista.php" class="accion-rapida"><span>Poner Notas</span></a>
        <a href="../retos/lista.php" class="accion-rapida"><span>Nuevo Reto</span></a>
        <a href="../mensajes/lista.php" class="accion-rapida"><span>Ver Mensajes</span></a>
        <a href="../perfil/ver.php" class="accion-rapida"><span>Mi Perfil</span></a>
        <a href="#" class="accion-rapida" id="btnToggleFormMasivo" style="background: #3498db; color: white;">
          <span><i class="fas fa-paper-plane"></i> Notificar Notas</span>
        </a>
      </div>

      <div id="formMasivo" class="oculto" style="margin-top: 20px; padding: 15px; border: none; border-radius: 8px;">
        <h4 style="margin-top: 0;">Enviar Resultados por Email a un Ciclo</h4>
        <form action="../../../controladores/admin/academico/enviarNotasMasivo.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
          <select name="idCiclo" class="ancho-total" style="padding: 8px; margin-bottom: 10px;">
            <option value="">Seleccione un ciclo...</option>
            <?php
            $ciclosVistos = [];
            foreach ($listaModulos as $m) {
                if (!isset($ciclosVistos[$m['idCiclo']])) {
                    $ciclosVistos[$m['idCiclo']] = true;
            ?>
                <option value="<?= Security::escapeHtml($m['idCiclo'] ) ?>"><?= Security::escapeHtml($m['nombreCiclo'] ) ?></option>
            <?php } } ?>
          </select>
          <input type="submit" class="boton-primario ancho-total" value="ENVIAR A TODOS LOS ALUMNOS">
        </form>
      </div>
    </div>

    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>Ultimos Avisos</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div>
            <?php
            $contadorAnuncios = 0;
            foreach ($listaAnuncios as $anuncio) {
                if ($contadorAnuncios >= 4) break;
            ?>
            <div class="anuncio-item">
                <div class="anuncio-contenido">
                    <div class="caja espacio-entre-elementos alinear-centro">
                        <strong class="anuncio-titulo"><?= Security::escapeHtml($anuncio['titulo'] ) ?></strong>
                        <span class="texto-suave" style="font-size: 0.8rem;"><?= Security::escapeHtml(date('d/m/Y', strtotime($anuncio['fechaAnuncio']))) ?></span>
                    </div>
                    <div class="margen-arriba-pequeno">
                        <span class="indicador-estado <?= Security::escapeHtml($anuncio['dirigidoA'] == 'todos' ? 'activo-verde' : ($anuncio['dirigidoA'] == 'profesores' ? 'azul' : 'morado')) ?>" style="font-size: 0.7rem; padding: 2px 8px;">
                            PARA: <?= Security::escapeHtml(strtoupper($anuncio['dirigidoA'])) ?>
                        </span>
                    </div>
                    <p class="texto-pequeno" style="margin-top: 8px;"><?= Security::escapeHtml(substr($anuncio['mensaje'], 0, 100)) ?>...</p>
                </div>
            </div>
            <?php
                $contadorAnuncios++;
            } ?>
        </div>
      <?php } else { ?>
        <p class="texto-suave">No hay anuncios activos.</p>
      <?php } ?>
    </div>
  </div>

  <div class="caja direccion-columna espacio-grande relleno">
    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>Proximos Eventos</h3>
      </div>
      <div class="lista-eventos">
        <?php if (empty($listaEventos)) { ?>
            <p class="texto-suave">No hay eventos proximos.</p>
        <?php } else { ?>
            <?php
            $contadorEventos = 0;
            foreach ($listaEventos as $evento) {
                if ($contadorEventos >= 4) break;
                $diaMes = date('d', strtotime($evento['fechaEvento']));
                $mesMes = strtoupper(date('M', strtotime($evento['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= Security::escapeHtml($diaMes ) ?></div><div class="mes"><?= Security::escapeHtml($mesMes ) ?></div></div>
              <div>
                <p class="texto-negrita"><?= Security::escapeHtml($evento['tituloEvento'] ) ?></p>
                <p class="texto-suave"><?= Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) ?>h - <?= Security::escapeHtml($evento['ubicacionEvento'] ) ?></p>
              </div>
            </div>
            <?php
                $contadorEventos++;
            } ?>
        <?php } ?>
      </div>
    </div>
  </div>
</div>

<script>
$(function() {
    $('#btnToggleFormMasivo').on('click', function(e) {
        e.preventDefault();
        $('#formMasivo').toggleClass('oculto');
    });
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


