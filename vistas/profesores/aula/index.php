<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$ciclos = listarCiclosDeProfesor($idProfesor);

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
  $paleta = ['recurso-card-azul', 'recurso-card-violeta', 'recurso-card-rosa', 'recurso-card-verde', 'recurso-card-ambar', 'recurso-card-teal'];
?>
<div class="aula-recursos-grid">
  <?php foreach ($ciclos as $i => $c):
      $modulos = listarModulosDeProfesorPorCiclo($idProfesor, $c['idCiclo']);
      $usado   = obtenerUsoAlmacenamientoCicloAula($c['idCiclo']);
      $limite  = obtenerLimiteAlmacenamientoCicloAula($c['idCiclo']);
      $pct     = $limite > 0 ? min(100, round($usado / $limite * 100)) : 0;
      $clase   = $paleta[$i % count($paleta)];
  ?>
  <a href="modulos.php?idCiclo=<?= $c['idCiclo'] ?>" class="recurso-ciclo-card <?= $clase ?>">
    <div class="recurso-ciclo-icon"><i class="fas fa-layer-group"></i></div>
    <div class="recurso-ciclo-body">
      <h3><?= htmlspecialchars($c['nombreCiclo']) ?></h3>
      <span class="recurso-ciclo-abrev"><?= htmlspecialchars($c['abreviaturaCiclo']) ?></span>
      <div class="recurso-ciclo-meta">
        <span><i class="fas fa-cubes"></i> <?= count($modulos) ?> módulos</span>
      </div>
      <div class="recurso-almacenamiento" title="<?= round($usado/1048576, 1) ?> MB de <?= round($limite/1073741824, 1) ?> GB">
        <div class="recurso-almacenamiento-barra"><span style="width:<?= $pct ?>%"></span></div>
        <small><?= $pct ?>% usado</small>
      </div>
    </div>
    <i class="fas fa-chevron-right recurso-ciclo-arrow"></i>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
