<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$sesiones = listarSesionesPorProfesor($idProfesor);

$registrosPorSesion = [];

foreach ($sesiones as $sesion) {
    $asistencias = listarAsistenciasPorSesion($sesion['idSesion']);
    if (!empty($asistencias)) {
        $registrosPorSesion[] = [
            'sesion' => $sesion,
            'asistencias' => $asistencias,
            'total' => count($asistencias)
        ];
    }
}

usort($registrosPorSesion, function($a, $b) {
    return strtotime($b['sesion']['fechaSesion'] . ' ' . $b['sesion']['horaSesion']) -
           strtotime($a['sesion']['fechaSesion'] . ' ' . $a['sesion']['horaSesion']);
});

$tituloDelPagina = 'AULAPRO | ASISTENCIAS';
$seccionActual = 'aula_asistencia';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRO DE ASISTENCIAS</h1>
    <p class="texto-suave">Visualiza la asistencia de tus estudiantes en sesiones vivas</p>
</div>

<?php if (empty($registrosPorSesion)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay registros de asistencia en tus sesiones vivas.</p>
    </div>
<?php } else { ?>
    <?php foreach ($registrosPorSesion as $grupo) {
        $sesion = $grupo['sesion'];
        $asistencias = $grupo['asistencias'];
    ?>
    <div class="panel" style="margin-bottom: 30px;">
        <div class="titulo-tarjeta">
            <h3><?= Security::escapeHtml(htmlspecialchars($sesion['titulo'])) ?></h3>
            <span class="texto-suave"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($sesion['fechaSesion'] . ' ' . $sesion['horaSesion']))) ?></span>
        </div>

        <div style="margin-bottom: 15px;">
            <strong>Estudiantes Asistentes: <?= Security::escapeHtml($grupo['total'] ) ?></strong>
        </div>

        <div class="tabla-responsiva">
            <table class="tabla-contenido">
                <thead>
                    <tr>
                        <th>ESTUDIANTE</th>
                        <th>HORA ENTRADA</th>
                        <th>HORA SALIDA</th>
                        <th>DURACIÓN</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($asistencias as $asist) { ?>
                    <tr>
                        <td><strong><?= Security::escapeHtml(htmlspecialchars($asist['nombreEstudiante'] ?? 'Estudiante')) ?></strong></td>
                        <td><?= Security::escapeHtml($asist['horaUnion'] ? date('H:i', strtotime($asist['horaUnion'])) : '-') ?></td>
                        <td><?= Security::escapeHtml($asist['horaSalida'] ? date('H:i', strtotime($asist['horaSalida'])) : '-') ?></td>
                        <td>
                            <?php
                            $duracion = $asist['duracion'] ?? 0;
                            $minutos = floor($duracion / 60);
                            $segundos = $duracion % 60;
                            ?>
                            <strong><?= Security::escapeHtml($minutos ) ?>m <?= Security::escapeHtml($segundos) ?>s</strong>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


