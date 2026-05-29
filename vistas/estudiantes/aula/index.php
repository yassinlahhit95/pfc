<?php
session_start();
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$estudiante = obtenerEstudiantePorId($idEstudiante);
$idCiclo    = $estudiante['idCiclo'] ?? 0;
$modulos    = listarModulosPorCiclo($idCiclo);

$notifNoLeidas = contarNotificacionesNoLeidasAula($idEstudiante, 'estudiante');

$tituloDelPagina = "AULAPRO | AULA DIGITAL";
$seccionActual   = 'aula';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1>AULA DIGITAL</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;"><?= htmlspecialchars($estudiante['nombreCiclo'] ?? '') ?> · <?= count($modulos) ?> módulos</p>
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
      <?php foreach (listarNotificacionesAula($idEstudiante, 'estudiante', 10) as $n): ?>
      <div class="aula-notif-item <?= !$n['leida'] ? 'no-leida' : '' ?>">
        <div class="aula-notif-item-titulo"><?= htmlspecialchars($n['titulo']) ?></div>
        <div class="aula-notif-item-fecha"><?= date('d/m/Y H:i', strtotime($n['fechaCreacion'])) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (empty($modulos)): ?>
<div class="panel" style="text-align:center;padding:60px 20px;margin-top:20px;">
  <i class="fas fa-chalkboard" style="font-size:3rem;color:#e2e8f0;display:block;margin-bottom:16px;"></i>
  <p class="texto-suave">No hay módulos disponibles aún.</p>
</div>
<?php else: ?>
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-top:20px;">
  <?php foreach ($modulos as $modulo):
    $nArchivos = contarArchivosPorModuloAula($modulo['idModulo']);
    $nTareas   = count(listarTareasPorModuloAula($modulo['idModulo']));
    // Contar tareas pendientes del estudiante
    $tareasList = listarTareasPorModuloAula($modulo['idModulo']);
    $pendientes = 0;
    foreach ($tareasList as $t) {
      if (!obtenerEntregaAula($t['idTarea'], $idEstudiante)) $pendientes++;
    }
  ?>
  <a href="modulo.php?id=<?= $modulo['idModulo'] ?>" class="ejercicio-card">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
      <div style="width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,#0ea5e9,#0ea5e9);display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;flex-shrink:0;">
        <i class="fas fa-book"></i>
      </div>
      <p class="ejercicio-card-titulo" style="margin:0;flex:1;"><?= htmlspecialchars($modulo['nombreModulo']) ?></p>
    </div>
    <div class="ejercicio-card-footer">
      <span class="ejercicio-fecha"><i class="fas fa-file"></i> <?= $nArchivos ?> archivos</span>
      <span class="ejercicio-fecha"><i class="fas fa-tasks"></i> <?= $nTareas ?> tareas</span>
      <?php if ($pendientes > 0): ?>
      <span class="badge-estado badge-pendiente"><i class="fas fa-exclamation"></i> <?= $pendientes ?> pendiente<?= $pendientes>1?'s':'' ?></span>
      <?php endif; ?>
    </div>
  </a>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
