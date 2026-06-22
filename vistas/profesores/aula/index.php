<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if ($esTutor && $idCicloTutor) {
    $cicloTutor = obtenerCicloPorId($idCicloTutor);
    $ciclos     = $cicloTutor ? [$cicloTutor] : [];
} else {
    $ciclos = listarCiclosDeProfesor($idProfesor);
}

$tituloDelPagina = "AULAPRO | RECURSOS";
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-folder-open"></i> RECURSOS EDUCATIVOS</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Selecciona un ciclo formativo para gestionar sus materiales</p>
  </div>
</div>

<?php if (empty($ciclos)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;margin-top:20px;">
  <i class="fas fa-folder-open" style="font-size:3rem;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
  <p class="texto-suave">No tienes ciclos formativos asignados.</p>
</div>
<?php else: ?>
<?php
  // Color e icono DETERMINISTAS por idCiclo: cada ciclo tiene su propio icono/color
  // y es el mismo para cualquier profesor del mismo ciclo (no depende del orden).
  $paleta = ['recurso-card-azul', 'recurso-card-violeta', 'recurso-card-rosa', 'recurso-card-verde', 'recurso-card-ambar', 'recurso-card-teal'];
  $iconos = ['fa-laptop-code', 'fa-network-wired', 'fa-database', 'fa-microchip', 'fa-code', 'fa-server', 'fa-diagram-project', 'fa-shield-halved'];
?>
<div class="aula-recursos-grid">
  <?php foreach ($ciclos as $c):
      $idc     = (int) $c['idCiclo'];
      $modulos = ($esTutor && $idCicloTutor)
          ? listarModulosPorCiclo($idc)
          : listarModulosDeProfesorPorCiclo($idProfesor, $idc);
      $usado   = obtenerUsoAlmacenamientoCicloAula($idc);
      $limite  = obtenerLimiteAlmacenamientoCicloAula($idc);
      $pct     = $limite > 0 ? min(100, round($usado / $limite * 100)) : 0;
      $clase   = $paleta[$idc % count($paleta)];
      $icono   = $iconos[$idc % count($iconos)];
  ?>
  <a href="modulos.php?idCiclo=<?= Security::escapeHtml($c['idCiclo'] ) ?>" class="recurso-ciclo-card <?= Security::escapeHtml($clase ) ?>">
    <div class="recurso-ciclo-icon"><i class="fas <?= Security::escapeHtml($icono ) ?>"></i></div>
    <div class="recurso-ciclo-body">
      <h3><?= Security::escapeHtml($c['nombreCiclo']) ?></h3>
      <span class="recurso-ciclo-abrev"><?= Security::escapeHtml($c['abreviaturaCiclo']) ?></span>
      <div class="recurso-ciclo-meta">
        <span><i class="fas fa-cubes"></i> <?= Security::escapeHtml(count($modulos)) ?> módulos</span>
      </div>
      <div class="recurso-almacenamiento" title="<?= Security::escapeHtml(round($usado/1048576, 1)) ?> MB de <?= Security::escapeHtml(round($limite/1073741824, 1)) ?> GB">
        <div class="recurso-almacenamiento-barra"><span style="width:<?= Security::escapeHtml($pct ) ?>%"></span></div>
        <small><?= Security::escapeHtml($pct ) ?>% usado</small>
      </div>
    </div>
    <i class="fas fa-chevron-right recurso-ciclo-arrow"></i>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


