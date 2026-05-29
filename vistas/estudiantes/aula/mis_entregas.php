<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = listarModulosPorCiclo($idCiclo);
$todasLasEntregas = [];

foreach ($listaModulos as $modulo) {
    $tareas = listarTareasPorModuloAula($modulo['idModulo']);
    foreach ($tareas as $tarea) {
        if ($tarea['publicada']) {
            $entrega = obtenerEntregaAula($tarea['idTarea'], $idEstudiante);
            if ($entrega) {
                $entrega['nombreModulo'] = $modulo['nombreModulo'];
                $entrega['nombreTarea'] = $tarea['titulo'];
                $todasLasEntregas[] = $entrega;
            }
        }
    }
}

usort($todasLasEntregas, function($a, $b) {
    return strtotime($b['fechaEntrega']) - strtotime($a['fechaEntrega']);
});

$totalEntregas = count($todasLasEntregas);
$totalCalificadas = count(array_filter($todasLasEntregas, function($e) { return $e['nota'] !== null; }));
$promedio = 0;
if ($totalCalificadas > 0) {
    $sumNotas = array_sum(array_column($todasLasEntregas, 'nota'));
    $promedio = round($sumNotas / $totalCalificadas, 1);
}

$tituloDelPagina = 'AULAPRO | MIS ENTREGAS';
$seccionActual = 'aula_entregas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS ENTREGAS</h1>
    <p class="texto-suave">Historial y calificaciones de tus entregas</p>
</div>

<div class="cuadricula-estadisticas">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul">
        <div class="info-estadistica">
            <h3><?= $totalEntregas ?></h3>
            <p>Entregas Totales</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde">
        <div class="info-estadistica">
            <h3><?= $totalCalificadas ?></h3>
            <p>Calificadas</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-morada">
        <div class="info-estadistica">
            <h3><?= $promedio ?></h3>
            <p>Promedio</p>
        </div>
    </div>
</div>

<br>

<?php if (empty($todasLasEntregas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>Aún no tienes entregas registradas.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
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
                    <td><strong><?= htmlspecialchars(substr($entrega['nombreTarea'], 0, 40)) ?></strong></td>
                    <td><?= htmlspecialchars($entrega['nombreModulo']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) ?></td>
                    <td>
                        <?php if ($entrega['nota'] !== null) { ?>
                            <strong style="font-size: 16px; color: #4caf50;"><?= $entrega['nota'] ?>/10</strong>
                        <?php } else { ?>
                            <span style="color: #ff9800;">Pendiente</span>
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
                        <a href="tarea_detalle.php?id=<?= $entrega['idTarea'] ?>" class="boton-secundario btn-pequeno">
                            <i class="fas fa-eye"></i> VER
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3>Información sobre Calificaciones</h3>
        <ul>
            <li><strong>Sin Calificar:</strong> El profesor aún está revisando tu entrega</li>
            <li><strong>Aprobada:</strong> Calificación >= 7.0</li>
            <li><strong>Reprobada:</strong> Calificación < 7.0</li>
            <li>Puedes descargar la retroalimentación del profesor haciendo click en VER</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
