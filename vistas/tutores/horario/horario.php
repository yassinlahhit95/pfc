<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . '/../../../include/AssetMin.php';
FeatureGuard::requirePage('feature_horario');
require_once __DIR__ . "/../../../modelos/tutores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/horarios.php";

$idEstudiante = (int)($_GET['id'] ?? 0);

$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$estudiante = null;
foreach ($hijos as $hijo) {
    if ((int)$hijo['idEstudiante'] === $idEstudiante) {
        $estudiante = $hijo;
        break;
    }
}

if (!$estudiante) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

$idCicloHorario = (int)($estudiante['idCiclo'] ?? 0);
$ciclo          = $idCicloHorario ? obtenerCicloPorId($idCicloHorario) : null;
$horarioCeldas  = $idCicloHorario ? listarHorarioPorCiclo($idCicloHorario) : [];
$puedeEditar    = false;

$titulo_pagina = 'Aulapro Familias — Horario de ' . $Estudiante['Nombreestudiante'];
$Seccion       = 'Hijo';
include __DIR__ . '/../comunes/nav.php';
?>

<link rel="stylesheet" href="<?= AssetMin::url(__DIR__, '../../../public/css/features/horario-admin.css') ?>">

<div class="cabecera">
  <div>
    <h1>Horario &mdash; <?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    <?php if ($ciclo): ?><p class="subtitulo-encabezado"><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></p><?php endif; ?>
  </div>
  <div style="display:flex;gap:10px;flex-wrap:wrap">
    <?php if ($idCicloHorario): ?>
    <form method="POST" action="../../../controladores/tutores/informes/generarHorario.php" target="_blank">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= (int)$estudiante['idEstudiante'] ?>">
        <button type="submit" class="boton-secundario"><i class="fas fa-print"></i> Imprimir PDF</button>
    </form>
    <?php endif; ?>
    <a href="../estudiantes/expediente.php?id=<?= (int)$estudiante['idEstudiante'] ?>" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
  </div>
</div>

<?php if (!$idCicloHorario): ?>
    <div class="panel">
      <div class="panel-vacio">
        <div class="panel-vacio-icono"><i class="fas fa-calendar-xmark"></i></div>
        <div class="panel-vacio-titulo">Sin ciclo asignado</div>
        <div class="panel-vacio-desc">Este estudiante no tiene un ciclo formativo asignado.</div>
      </div>
    </div>
<?php else: ?>
<div class="horario-contenido horario-solo-lectura">
    <?php include __DIR__ . "/../../../include/horario-tabla.php"; ?>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
