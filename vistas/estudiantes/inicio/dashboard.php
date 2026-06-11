<?php
require_once __DIR__ . "/../../../include/Security.php";

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

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$listaAnuncios = listarAnunciosPorRol('estudiantes');
$listaEventosProximos = listarEventosProximos();

$tfgActual = obtenerTFGporEstudiante($idEstudiante);
$califTFG = obtenerCalificacionTFG($idEstudiante);

$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos        = listarModulosPorCiclo($idCiclo);
$listaRetos          = listarRetosPorCiclo($idCiclo);
$califModulos        = listarCalificacionesPorEstudiante($idEstudiante);
$califRetos          = listarCalificacionesRetoPorEstudiante($idEstudiante);
$cantidadPagos = contarPagosEstudiante($idEstudiante);
$listaMensajes = listarMensajesDeEstudiante($idEstudiante);

$tituloDelPagina = 'AULAPRO | PANEL DE CONTROL';
$seccionActual = 'inicio';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>HOLA, <?= Security::escapeHtml(strtoupper($estudianteActual['nombreEstudiante'])) ?>!</h1>
    <p class="texto-suave"><?= Security::escapeHtml(strtoupper($estudianteActual['nombreCiclo'])) ?></p>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>
<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>

<div class="cuadricula-estadisticas">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul">
        <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaModulos)) ?></h3><p>Módulos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde">
        <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaRetos)) ?></h3><p>Retos</p></div>
    </div>
    <div id="stat-tfg" class="tarjeta-estadistica tarjeta-estadistica-morada">
        <div class="info-estadistica"><h3><?= Security::escapeHtml($califTFG ? $califTFG['nota'] : (empty($tfgActual['archivoTFG']) ? 'PEND' : 'SUBIDO')) ?></h3><p>TFG</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-violeta">
        <div class="info-estadistica"><h3><?= Security::escapeHtml($cantidadPagos ) ?></h3><p>Pagos</p></div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-naranja">
        <div class="info-estadistica"><h3><?= Security::escapeHtml(count($listaMensajes)) ?></h3><p>Mensajes</p></div>
    </div>
</div>
<br>

<div class="cuadricula-secundaria" style="margin-top: 30px;">
  <div class="caja direccion-columna espacio-grande relleno">

    <div class="panel">
      <div class="titulo-tarjeta">
        <h3>TABLA DE ANUNCIOS</h3>
      </div>
      <?php if (!empty($listaAnuncios)) { ?>
        <div class="lista-anuncios-dashboard">
            <?php
            $contadorAnuncios = 0;
            foreach ($listaAnuncios as $anuncio) {
                if ($contadorAnuncios >= 4) break;
            ?>
            <div class="anuncio-item">
                <div class="anuncio-contenido">
                    <div class="caja espacio-entre-elementos alinear-centro">
                        <strong class="anuncio-titulo color-primario"><?= Security::escapeHtml(strtoupper($anuncio['tituloAnuncio'])) ?></strong>
                        <span class="texto-suave"><?= Security::escapeHtml(date('d/m/Y', strtotime($anuncio['fechaAnuncio']))) ?></span>
                    </div>
                    <br>
                    <p class="texto-pequeno" style="margin:0"><?= Security::escapeHtml(substr(strip_tags($anuncio['contenidoAnuncio']), 0, 150)) ?>...</p>
                    <div style="margin-top: 10px; text-align: right;">
                        <a href="../anuncios/lista.php" class="boton-secundario btn-pequeno">VER DETALLES</a>
                    </div>
                </div>
            </div>
            <?php $contadorAnuncios++; } ?>
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
            $contadorEventos = 0;
            foreach ($listaEventosProximos as $evento) {
                if ($contadorEventos >= 4) break;
                $diaMes  = date('d', strtotime($evento['fechaEvento']));
                $mesMes  = strtoupper(date('M', strtotime($evento['fechaEvento'])));
            ?>
            <div class="elemento-evento">
              <div class="fecha-evento azul"><div class="dia"><?= Security::escapeHtml($diaMes ) ?></div><div class="mes"><?= Security::escapeHtml($mesMes ) ?></div></div>
              <div>
                <p class="texto-negrita"><?= Security::escapeHtml(strtoupper($evento['tituloEvento'])) ?></p>
                <p class="texto-suave"><?= Security::escapeHtml(date('H:i', strtotime($evento['horaEvento']))) ?>h - <?= Security::escapeHtml($evento['ubicacionEvento'] ) ?></p>
              </div>
            </div>
            <?php $contadorEventos++; } ?>
        <?php } ?>
      </div>

    </div>
  </div>
</div>

<?php
// Build chart data — only modules that have at least one grade recorded
$chartLabels  = [];
$chartNotas   = [];
$chartColores = [];
foreach ($califModulos as $cm) {
    $nota = $cm['nota_2final'] ?? $cm['nota_1final'] ?? null;
    if ($nota === null) continue;
    $nota             = (float)$nota;
    $chartLabels[]    = $cm['nombreModulo'];
    $chartNotas[]     = $nota;
    $chartColores[]   = $nota >= 5 ? 'rgba(22,163,74,0.75)' : 'rgba(239,68,68,0.75)';
}

$chartRetosLabels  = [];
$chartRetosNotas   = [];
$chartRetosColores = [];
foreach ($califRetos as $cr) {
    $nota                = (float)$cr['nota'];
    $chartRetosLabels[]  = $cr['nombreReto'];
    $chartRetosNotas[]   = $nota;
    $chartRetosColores[] = $nota >= 5 ? 'rgba(14,165,233,0.75)' : 'rgba(239,68,68,0.75)';
}
?>
<?php if (!empty($chartLabels) || !empty($chartRetosLabels)) { ?>
<div class="panel" style="margin-top:24px;">
    <div class="titulo-tarjeta"><h3>HISTORIAL DE CALIFICACIONES</h3></div>
    <div style="display:grid;grid-template-columns:<?= !empty($chartLabels) && !empty($chartRetosLabels) ? '1fr 1fr' : '1fr' ?>;gap:24px;padding:16px;">
        <?php if (!empty($chartLabels)) { ?>
        <div>
            <p style="font-size:.8rem;font-weight:600;color:#64748b;margin-bottom:8px;">MÓDULOS</p>
            <canvas id="chartModulos" height="220"></canvas>
        </div>
        <?php } ?>
        <?php if (!empty($chartRetosLabels)) { ?>
        <div>
            <p style="font-size:.8rem;font-weight:600;color:#64748b;margin-bottom:8px;">RETOS</p>
            <canvas id="chartRetos" height="220"></canvas>
        </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    var optsBase = {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { min: 0, max: 10, ticks: { stepSize: 1 },
                 grid: { color: 'rgba(0,0,0,.05)' } },
            x: { ticks: { font: { size: 11 }, maxRotation: 30 } }
        }
    };

    <?php if (!empty($chartLabels)) { ?>
    new Chart(document.getElementById('chartModulos'), {
        type: 'bar',
        data: {
            labels:   <?= json_encode($chartLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{ data: <?= json_encode($chartNotas) ?>,
                         backgroundColor: <?= json_encode($chartColores) ?>,
                         borderRadius: 6, borderSkipped: false }]
        },
        options: optsBase
    });
    <?php } ?>

    <?php if (!empty($chartRetosLabels)) { ?>
    new Chart(document.getElementById('chartRetos'), {
        type: 'bar',
        data: {
            labels:   <?= json_encode($chartRetosLabels, JSON_UNESCAPED_UNICODE) ?>,
            datasets: [{ data: <?= json_encode($chartRetosNotas) ?>,
                         backgroundColor: <?= json_encode($chartRetosColores) ?>,
                         borderRadius: 6, borderSkipped: false }]
        },
        options: optsBase
    });
    <?php } ?>
})();
</script>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


