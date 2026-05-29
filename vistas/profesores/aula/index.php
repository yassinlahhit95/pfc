<?php
session_start();
$idProfesor = $_SESSION['idProfesor'] ?? '';
if (!$idProfesor) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$exito   = $_SESSION['exito'] ?? ''; unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? ''; unset($_SESSION['errores']);

$ciclos = listarCiclosDeProfesor($idProfesor);

// Enriquecer con stats
foreach ($ciclos as &$ciclo) {
    $modulos = listarModulosDeProfesorPorCiclo($idProfesor, $ciclo['idCiclo']);
    $totalArchivos = 0;
    $totalTareas   = 0;
    foreach ($modulos as $m) {
        $totalArchivos += contarArchivosPorModuloAula($m['idModulo']);
        $tareas = listarTodasTareasPorModuloAula($m['idModulo']);
        $totalTareas += count($tareas);
    }
    $ciclo['totalModulos']  = count($modulos);
    $ciclo['totalArchivos'] = $totalArchivos;
    $ciclo['totalTareas']   = $totalTareas;
}
unset($ciclo);

$notifNoLeidas = contarNotificacionesNoLeidasAula($idProfesor, 'profesor');

$tituloDelPagina = "AULAPRO | AULA DIGITAL";
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Gestión de materiales y tareas por módulo</p>
  </div>
  <?php if ($notifNoLeidas > 0): ?>
  <div style="position:relative;">
    <button id="aulaBell" class="boton-secundario" style="position:relative;">
      <i class="fas fa-bell"></i>
      <span style="position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:0.6rem;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center;"><?= $notifNoLeidas ?></span>
    </button>
    <div id="aulaNotifDropdown" class="aula-notif-dropdown">
      <div class="aula-notif-dropdown-header">
        <h4>Notificaciones</h4>
        <span style="font-size:0.75rem;color:#94a3b8;"><?= $notifNoLeidas ?> nuevas</span>
      </div>
      <?php foreach (listarNotificacionesAula($idProfesor, 'profesor', 10) as $n): ?>
      <div class="aula-notif-item <?= !$n['leida'] ? 'no-leida' : '' ?>">
        <div class="aula-notif-item-titulo"><?= htmlspecialchars($n['titulo']) ?></div>
        <div class="aula-notif-item-fecha"><?= date('d/m/Y H:i', strtotime($n['fechaCreacion'])) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if ($exito): ?><div class="mensaje-exito"><?= htmlspecialchars($exito) ?></div><?php endif; ?>
<?php if ($errores): ?><div class="mensaje-error"><?= htmlspecialchars($errores) ?></div><?php endif; ?>

<?php if (empty($ciclos)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;">
  <i class="fas fa-chalkboard" style="font-size:3rem;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
  <p class="texto-suave">No tienes ciclos asignados todavía.</p>
</div>
<?php else: ?>
<div class="aula-ciclos-grid" style="margin-top:20px;">
  <?php foreach ($ciclos as $ciclo):
    $nivel = strtolower($ciclo['nombreNivel'] ?? '');
    $claseGradiente = 'grado-default';
    if (strpos($nivel, 'medio') !== false) $claseGradiente = 'grado-medio';
    elseif (strpos($nivel, 'superior') !== false) $claseGradiente = 'grado-superior';
  ?>
  <a href="modulos.php?idCiclo=<?= $ciclo['idCiclo'] ?>" class="aula-ciclo-card <?= $claseGradiente ?>">
    <div class="aula-ciclo-nivel"><?= htmlspecialchars($ciclo['nombreNivel'] ?? 'Ciclo') ?></div>
    <div class="aula-ciclo-nombre"><?= htmlspecialchars($ciclo['nombreCiclo']) ?></div>
    <div class="aula-ciclo-stats">
      <span class="aula-ciclo-stat"><i class="fas fa-cubes"></i> <?= $ciclo['totalModulos'] ?> módulos</span>
      <span class="aula-ciclo-stat"><i class="fas fa-file"></i> <?= $ciclo['totalArchivos'] ?> archivos</span>
      <span class="aula-ciclo-stat"><i class="fas fa-tasks"></i> <?= $ciclo['totalTareas'] ?> tareas</span>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
