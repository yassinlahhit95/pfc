<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

if ($esTutor && $idCicloTutor) {
    $cicloTutor = obtenerCicloPorId($idCicloTutor);
    $ciclos     = $cicloTutor ? [$cicloTutor] : [];
} else {
    $ciclos = listarCiclosDeProfesor($idProfesor);
}

$titulo_pagina = "Recursos";
$seccionActual   = 'aula_recursos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1><i class="fas fa-folder-open"></i> RECURSOS EDUCATIVOS</h1>
    <p class="subtitulo-encabezado">Selecciona un ciclo formativo para gestionar sus materiales</p>
  </div>
</div>

<?php if (empty($ciclos)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;margin-top:20px;">
  <i class="fas fa-folder-open" style="font-size:3rem;color:var(--border);display:block;margin-bottom:16px;"></i>
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
  <?php foreach ($ciclos as $ciclo):
      $idCiclo = (int) $ciclo['idCiclo'];
      $modulos = ($esTutor && $idCicloTutor)
          ? listarModulosPorCiclo($idCiclo)
          : listarModulosDeProfesorPorCiclo($idProfesor, $idCiclo);
      $usado   = obtenerUsoAlmacenamientoCicloAula($idCiclo);
      $limite  = obtenerLimiteAlmacenamientoCicloAula($idCiclo);
      $pct     = $limite > 0 ? min(100, round($usado / $limite * 100)) : 0;
      $clase   = $paleta[$idCiclo % count($paleta)];
      $icono   = $iconos[$idCiclo % count($iconos)];
  ?>
  <a href="modulos.php?idCiclo=<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="recurso-ciclo-card <?= Security::escapeHtml($clase) ?>">
    <div class="recurso-ciclo-icon"><i class="fas <?= Security::escapeHtml($icono) ?>"></i></div>
    <div class="recurso-ciclo-body">
      <h3><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></h3>
      <span class="recurso-ciclo-abrev"><?= Security::escapeHtml($ciclo['abreviaturaCiclo']) ?></span>
      <div class="recurso-ciclo-meta">
        <span><i class="fas fa-cubes"></i> <?= Security::escapeHtml(count($modulos)) ?> módulos</span>
      </div>
      <div class="recurso-almacenamiento" title="<?= Security::escapeHtml(round($usado/1048576, 1)) ?> MB de <?= Security::escapeHtml(round($limite/1073741824, 1)) ?> GB">
        <div class="recurso-almacenamiento-barra"><span style="width:<?= Security::escapeHtml($pct) ?>%"></span></div>
        <small><?= Security::escapeHtml($pct) ?>% usado</small>
      </div>
    </div>
    <i class="fas fa-chevron-right recurso-ciclo-arrow"></i>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
