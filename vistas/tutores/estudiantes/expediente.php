<?php
require_once __DIR__ . '/../../../include/TutorGuard.php';
$titulo_pagina = 'AulaPro Familias — Expediente Académico';
$seccion       = 'inicio';
include __DIR__ . '/../comunes/nav.php';

require_once __DIR__ . "/../../../modelos/calificaciones.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = (int)($_GET['id'] ?? 0);

if (empty($idEstudiante)) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

// Verificar que el estudiante pertenezca al tutor
$hijos = listarEstudiantesPorTutor($_SESSION['idTutor']);
$esHijo = false;
$estudiante = null;
foreach ($hijos as $h) {
    if ($h['idEstudiante'] == $idEstudiante) {
        $esHijo = true;
        $estudiante = $h;
        break;
    }
}

if (!$esHijo) {
    header('Location: ../inicio/dashboard.php');
    exit;
}

$resultados = obtenerResultadosFinalesEstudiante($idEstudiante);
?>

<div class="hero">
  <div class="hero-text">
    <div class="eyebrow">Expediente de</div>
    <h1><?= Security::escapeHtml($estudiante['nombreEstudiante']) ?></h1>
    <p class="sub"><?= Security::escapeHtml($estudiante['nombreCiclo']) ?> — <?= Security::escapeHtml($estudiante['curso']) ?></p>
  </div>
</div>

<div class="grid">
    <div class="tile card-tint" style="--tint: #4f46e5;">
        <div class="tile-ico"><i class="fas fa-graduation-cap"></i></div>
        <div class="tile-body">
            <div class="tile-label">Media Global</div>
            <div class="tile-desc"><?= Security::escapeHtml($resultados['promedio_global']) ?></div>
        </div>
    </div>
    
    <div class="tile card-tint" style="--tint: <?= ($resultados['estado_global'] === 'APROBADO' ? '#10b981' : '#f59e0b') ?>;">
        <div class="tile-ico"><i class="fas fa-info-circle"></i></div>
        <div class="tile-body">
            <div class="tile-label">Estado Actual</div>
            <div class="tile-desc"><?= Security::escapeHtml($resultados['estado_global']) ?></div>
        </div>
    </div>
</div>

<div class="dash-panel mt-4">
    <div class="dash-panel-head">
        <h3>Calificaciones por Módulo</h3>
    </div>
    <div class="dash-panel-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="px-4">Módulo</th>
                        <th class="text-center">Media Retos</th>
                        <th class="text-center">Media Exámenes</th>
                        <th class="text-center">Nota Final</th>
                        <th class="text-end px-4">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resultados['detalles_modulos'] as $det): ?>
                        <tr>
                            <td class="px-4"><strong><?= Security::escapeHtml($det['nombreModulo']) ?></strong></td>
                            <td class="text-center"><?= Security::escapeHtml($det['media_retos']) ?></td>
                            <td class="text-center"><?= Security::escapeHtml($det['media_notas']) ?></td>
                            <td class="text-center"><span class="badge bg-light text-dark border"><?= Security::escapeHtml($det['nota_final']) ?></span></td>
                            <td class="text-end px-4">
                                <?php
                                $badgeClass = match($det['estado']) {
                                    'Aprobado' => 'bg-success-soft text-success-dark',
                                    'Suspenso' => 'bg-danger-soft text-danger-dark',
                                    default => 'bg-light text-muted'
                                };
                                ?>
                                <span class="badge-custom <?= $badgeClass ?>"><?= Security::escapeHtml($det['estado']) ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-success-soft { background-color: #ecfdf5; color: #065f46; }
    .bg-danger-soft { background-color: #fef2f2; color: #991b1b; }
    .badge-custom { padding: 0.35em 0.85em; font-size: 0.75rem; font-weight: 600; border-radius: 50rem; display: inline-flex; align-items: center; }
</style>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
