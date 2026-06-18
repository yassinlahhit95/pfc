<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);
$idCiclo = $estudianteActual['idCiclo'] ?? 0;

$listaModulos = listarModulosPorCiclo($idCiclo);
$todasLasTareas = [];

foreach ($listaModulos as $modulo) {
    $tareas = listarTareasPorModuloAula($modulo['idModulo']);
    foreach ($tareas as $tarea) {
        $tarea['nombreModulo'] = $modulo['nombreModulo'];
        $todasLasTareas[] = $tarea;
    }
}

usort($todasLasTareas, function($a, $b) {
    return strtotime($b['fechaCreacion']) - strtotime($a['fechaCreacion']);
});

$tituloDelPagina = 'AULAPRO | TAREAS';
$seccionActual = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>TAREAS</h1>
    <p class="texto-suave">Tareas pendientes y completadas de tus módulos</p>
</div>

<?php
$tareasPendientes = array_filter($todasLasTareas, function($t) { return $t['publicada'] == 1; });
$tareasNoPublicadas = array_filter($todasLasTareas, function($t) { return $t['publicada'] == 0; });
?>

<?php if (empty($tareasPendientes) && empty($tareasNoPublicadas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay tareas disponibles en tus módulos.</p>
    </div>
<?php } else { ?>

    <?php if (!empty($tareasPendientes)) { ?>
    <div style="margin-bottom: 40px;">
        <h2 style="margin-bottom: 20px; color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px;">
            <i class="fas fa-tasks"></i> TAREAS DISPONIBLES
        </h2>

        <div class="tabla-responsiva">
            <table class="tabla-contenido">
                <thead>
                    <tr>
                        <th>TAREA</th>
                        <th>MÓDULO</th>
                        <th>PROFESOR</th>
                        <th>DESCRIPCIÓN</th>
                        <th>ESTADO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareasPendientes as $tarea) {
                        $con = obtenerConexion();
                        $sql = "SELECT COUNT(*) as total FROM aula_entregas WHERE idTarea = ? AND idEstudiante = ?";
                        $stmt = mysqli_prepare($con, $sql);
                        mysqli_stmt_bind_param($stmt, "ii", $tarea['idTarea'], $idEstudiante);
                        mysqli_stmt_execute($stmt);
                        $res = mysqli_stmt_get_result($stmt);
                        $result = mysqli_fetch_assoc($res);
                        $tieneEntrega = $result['total'] > 0;
                        mysqli_close($con);

                        $estado = $tieneEntrega ? '<span class="badge badge-verde">ENTREGADA</span>' : '<span class="badge badge-azul">PENDIENTE</span>';
                    ?>
                    <tr>
                        <td><strong><?= Security::escapeHtml(substr($tarea['titulo'], 0, 40)) ?></strong></td>
                        <td><?= Security::escapeHtml($tarea['nombreModulo']) ?></td>
                        <td><?= Security::escapeHtml($tarea['nombreProfesor']) ?></td>
                        <td><span class="texto-pequeño"><?= Security::escapeHtml(substr(strip_tags($tarea['descripcion']), 0, 60)) ?>...</span></td>
                        <td><?= Security::escapeHtml($estado ) ?></td>
                        <td>
                            <a href="tarea_detalle.php?id=<?= Security::escapeHtml($tarea['idTarea'] ) ?>" class="boton-primario btn-pequeno">
                                <i class="fas fa-arrow-right"></i> VER
                            </a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php } ?>

    <?php if (!empty($tareasNoPublicadas)) { ?>
    <div style="margin-top: 40px;">
        <h2 style="margin-bottom: 20px; color: #666; border-bottom: 2px solid #ddd; padding-bottom: 10px;">
            <i class="fas fa-lock"></i> TAREAS NO PUBLICADAS
        </h2>
        <div class="alerta-info">
            <p>Hay <?= Security::escapeHtml(count($tareasNoPublicadas)) ?> tarea(s) que aún no están disponibles para estudiantes.</p>
        </div>
    </div>
    <?php } ?>

    <div class="info-sistema">
        <h3>Información sobre Tareas</h3>
        <ul>
            <li><strong>Tareas Disponibles:</strong> Tareas publicadas por el profesor</li>
            <li><strong>Pendiente:</strong> Aún no has entregado la tarea</li>
            <li><strong>Entregada:</strong> Ya has enviado tu solución</li>
            <li><strong>Calificada:</strong> El profesor ha evaluado tu entrega</li>
            <li>Puedes ver el estado de cada entrega en <strong>MIS ENTREGAS</strong></li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


