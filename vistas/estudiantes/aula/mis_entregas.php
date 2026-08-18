<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = listarModulosPorCiclo($idCiclo);
$todasLasEntregas = [];

foreach ($listaModulos as $modulo) {
    // listarTareasPorModuloAula ya devuelve solo tareas con publicado = 1
    $tareas = listarTareasPorModuloAula($modulo['idModulo']);
    foreach ($tareas as $tarea) {
        $entrega = obtenerEntregaAula($tarea['idTarea'], $idEstudiante);
        if ($entrega) {
            $entrega['nombreModulo'] = $modulo['nombreModulo'];
            $entrega['nombreTarea'] = $tarea['titulo'];
            $todasLasEntregas[] = $entrega;
        }
    }
}

usort($todasLasEntregas, function($a, $b) {
    return strtotime($b['fechaEntrega']) - strtotime($a['fechaEntrega']);
});

$totalEntregas = count($todasLasEntregas);
$totalCalificadas = count(array_filter($todasLasEntregas, function($entrega) { return $entrega['nota'] !== null; }));
$promedio = 0;
if ($totalCalificadas > 0) {
    $sumNotas = array_sum(array_column($todasLasEntregas, 'nota'));
    $promedio = round($sumNotas / $totalCalificadas, 1);
}

$exito   = $_SESSION['exito'] ?? null;   unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? null; unset($_SESSION['errores']);

$titulo_pagina = 'Mis Entregas';
$seccionActual = 'aula_entregas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Mis Entregas</h1>
    <p class="subtitulo-encabezado">Historial y calificaciones de tus entregas</p>
</div>

<?php if ($exito): ?>
<div class="alerta alerta-exito" style="margin-bottom:var(--gap);"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta alerta-error" style="margin-bottom:var(--gap);"><i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml(is_array($errores) ? implode(', ', $errores) : $errores) ?></div>
<?php endif; ?>

<div class="cuadricula-estadisticas">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul">
        <span class="tarjeta-estadistica-icono"><i class="fas fa-file-arrow-up"></i></span>
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml($totalEntregas) ?></h3>
            <p>Entregas totales</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde">
        <span class="tarjeta-estadistica-icono"><i class="fas fa-circle-check"></i></span>
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml($totalCalificadas) ?></h3>
            <p>Calificadas</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-morada">
        <span class="tarjeta-estadistica-icono"><i class="fas fa-chart-line"></i></span>
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml($promedio) ?></h3>
            <p>Promedio</p>
        </div>
    </div>
</div>

<?php if (empty($todasLasEntregas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>Aún no tienes entregas registradas.</p>
    </div>
<?php } else { ?>
    <div class="contenedor-tabla">
        <table class="tabla-datos" id="tablaEntregas">
            <thead>
                <tr>
                    <th>TAREA</th>
                    <th>MÓDULO</th>
                    <th>FECHA ENTREGA</th>
                    <th>CALIFICACIÓN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todasLasEntregas as $entrega) { ?>
                <tr>
                    <td><strong><?= Security::escapeHtml(substr($entrega['nombreTarea'], 0, 40)) ?></strong></td>
                    <td><?= Security::escapeHtml($entrega['nombreModulo']) ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($entrega['fechaEntrega']))) ?></td>
                    <td>
                        <?php if ($entrega['nota'] !== null) { ?>
                            <strong style="font-size: 16px; color: var(--verde);"><?= Security::escapeHtml($entrega['nota']) ?>/10</strong>
                        <?php } else { ?>
                            <span style="color: var(--naranja);">Pendiente</span>
                        <?php } ?>
                    </td>
                    <td>
                        <?php
                        if ($entrega['nota'] !== null) {
                            if ($entrega['nota'] >= 7) {
                                echo '<span class="badge badge-verde">APROBADA</span>';
                            } else {
                                echo '<span class="badge badge-rojo">REPROBADA</span>';
                            }
                        } else {
                            echo '<span class="badge badge-gris">SIN CALIFICAR</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <a href="tarea.php?id=<?= Security::escapeHtml($entrega['idTarea']) ?>" class="boton-secundario btn-pequeno">
                            <i class="fas fa-eye"></i> VER
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3><i class="fas fa-circle-info" style="color:var(--accent);margin-right:6px;"></i> Información sobre calificaciones</h3>
        <ul>
            <li><strong>Sin calificar:</strong> el profesor aún está revisando tu entrega.</li>
            <li><strong>Aprobada:</strong> calificación ≥ 7.0.</li>
            <li><strong>Reprobada:</strong> calificación &lt; 7.0.</li>
            <li>Puedes descargar la retroalimentación del profesor haciendo clic en «Ver».</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

