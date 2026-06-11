<?php
require_once __DIR__ . "/../../../include/Security.php";
$idEstudiante = $_SESSION['idEstudiante'] ?? '';
if (!$idEstudiante) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../modelos/ejercicios.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$estudiante  = obtenerEstudiantePorId($idEstudiante);
$idCiclo     = $estudiante['idCiclo'] ?? 0;
$idCarpeta   = intval($_GET['idCarpeta'] ?? 0);

$carpetas   = listarCarpetasPorCiclo($idCiclo);
$ejercicios = listarEjerciciosPorCiclo($idCiclo, $idCarpeta);

$tituloDelPagina = "AULAPRO | MIS EJERCICIOS";
$seccionActual   = 'ejercicios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
  <div>
    <h1>EJERCICIOS</h1>
    <p class="texto-suave" style="margin-top:4px;font-size:0.85rem;">Tareas y ejercicios de tu ciclo</p>
  </div>
</div>

<div class="ejercicios-layout" style="margin-top:20px;">

  <!-- CARPETAS -->
  <div class="ejercicios-carpetas-panel">
    <div class="ejercicios-carpetas-header">
      <h3>Carpetas</h3>
    </div>

    <a href="lista.php" class="carpeta-item <?= Security::escapeHtml($idCarpeta === 0 ? 'activa' : '') ?>">
      <span class="carpeta-punto" style="background:#94a3b8;"></span>
      <span>Todos</span>
      <span class="carpeta-count"><?= Security::escapeHtml(count($ejercicios)) ?></span>
    </a>

    <?php foreach ($carpetas as $c): ?>
    <a href="lista.php?idCarpeta=<?= Security::escapeHtml($c['idCarpeta'] ) ?>" class="carpeta-item <?= Security::escapeHtml($idCarpeta == $c['idCarpeta'] ? 'activa' : '') ?>">
      <span class="carpeta-punto" style="background:<?= Security::escapeHtml($c['color']) ?>;"></span>
      <span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= Security::escapeHtml($c['nombre']) ?></span>
      <span class="carpeta-count"><?= Security::escapeHtml($c['totalEjercicios'] ) ?></span>
    </a>
    <?php endforeach; ?>

    <?php if (empty($carpetas)): ?>
    <p class="texto-suave" style="padding:16px 18px;font-size:0.8rem;">Sin carpetas</p>
    <?php endif; ?>
  </div>

  <!-- EJERCICIOS -->
  <div>
    <?php if (empty($ejercicios)): ?>
    <div class="panel" style="text-align:center;padding:60px 20px;">
      <i class="fas fa-book-open" style="font-size:3rem;color:#e2e8f0;margin-bottom:16px;display:block;"></i>
      <p class="texto-suave">No hay ejercicios disponibles aún.</p>
    </div>
    <?php else: ?>
    <div class="ejercicios-grid">
      <?php foreach ($ejercicios as $ej):
        $entrega = obtenerEntregaPorEstudiante($ej['idEjercicio'], $idEstudiante);
        $hoy = new DateTime();
        $limite = $ej['fechaLimite'] ? new DateTime($ej['fechaLimite']) : null;

        if ($entrega) {
          if ($entrega['estado'] === 'calificado') {
            $badgeClass = 'badge-calificado';
            $badgeText  = '★ ' . $entrega['nota'] . ' / 10';
          } else {
            $badgeClass = 'badge-entregado';
            $badgeText  = 'Entregado';
          }
        } elseif ($limite && $limite < $hoy) {
          $badgeClass = 'badge-tarde';
          $badgeText  = 'Tarde';
        } else {
          $badgeClass = 'badge-pendiente';
          $badgeText  = 'Pendiente';
        }

        $claseUrgencia = '';
        $textoFecha = 'Sin límite';
        if ($limite) {
          $textoFecha = $limite->format('d/m/Y H:i');
          if (!$entrega) {
            $diff = $hoy->diff($limite);
            if ($limite < $hoy) $claseUrgencia = 'urgente';
            elseif ($diff->days <= 3) $claseUrgencia = 'pronto';
          }
        }
      ?>
      <a href="ver.php?id=<?= Security::escapeHtml($ej['idEjercicio'] ) ?>" class="ejercicio-card">
        <?php if ($ej['nombreCarpeta']): ?>
        <span class="ejercicio-card-carpeta" style="background:<?= Security::escapeHtml($ej['colorCarpeta']) ?>22;color:<?= Security::escapeHtml($ej['colorCarpeta']) ?>;">
          <i class="fas fa-folder" style="font-size:0.65rem;"></i>
          <?= Security::escapeHtml($ej['nombreCarpeta']) ?>
        </span>
        <?php endif; ?>

        <p class="ejercicio-card-titulo"><?= Security::escapeHtml($ej['titulo']) ?></p>

        <?php if ($ej['descripcion']): ?>
        <p class="ejercicio-card-desc"><?= Security::escapeHtml($ej['descripcion']) ?></p>
        <?php endif; ?>

        <div class="ejercicio-card-footer">
          <span class="ejercicio-fecha <?= Security::escapeHtml($claseUrgencia ) ?>">
            <i class="fas fa-clock"></i> <?= Security::escapeHtml($textoFecha ) ?>
          </span>
          <span class="badge-estado <?= Security::escapeHtml($badgeClass ) ?>"><?= Security::escapeHtml($badgeText ) ?></span>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


