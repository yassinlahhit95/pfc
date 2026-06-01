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
$registrosAsistencia = [];

foreach ($listaModulos as $modulo) {
    $sesiones = listarSesionesPorModulo($modulo['idModulo']);
    foreach ($sesiones as $sesion) {
        $con = obtenerConexion();
        $sql = "SELECT aa.*, p.nombreProfesor
                FROM aula_asistencias aa
                JOIN profesores p ON aa.idProfesor = p.idProfesor
                WHERE aa.idSesion = ? AND aa.idEstudiante = ?";
        $stmt = mysqli_prepare($con, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $sesion['idSesion'], $idEstudiante);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        while ($asistencia = mysqli_fetch_assoc($res)) {
            $asistencia['nombreModulo'] = $modulo['nombreModulo'];
            $asistencia['tituloSesion'] = $sesion['titulo'];
            $asistencia['fechaSesion'] = $sesion['fechaSesion'];
            $asistencia['horaSesion'] = $sesion['horaSesion'];
            $registrosAsistencia[] = $asistencia;
        }
        mysqli_close($con);
    }
}

usort($registrosAsistencia, function($a, $b) {
    return strtotime($b['fechaSesion'] . ' ' . $b['horaSesion']) - strtotime($a['fechaSesion'] . ' ' . $a['horaSesion']);
});

$totalSesionesAsistidas = count($registrosAsistencia);
$duracionTotal = array_sum(array_column($registrosAsistencia, 'duracion'));

$tituloDelPagina = 'AULAPRO | MI ASISTENCIA';
$seccionActual = 'aula_asistencia';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MI ASISTENCIA A SESIONES VIVAS</h1>
    <p class="texto-suave">Registro de tu participación en clases en vivo</p>
</div>

<div class="cuadricula-estadisticas">
    <div class="tarjeta-estadistica tarjeta-estadistica-azul">
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml($totalSesionesAsistidas ) ?></h3>
            <p>Sesiones Asistidas</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-verde">
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml(round($duracionTotal / 60)) ?></h3>
            <p>Minutos Totales</p>
        </div>
    </div>
    <div class="tarjeta-estadistica tarjeta-estadistica-morada">
        <div class="info-estadistica">
            <h3><?= Security::escapeHtml($totalSesionesAsistidas > 0 ? round($duracionTotal / $totalSesionesAsistidas / 60) : 0) ?></h3>
            <p>Promedio (min)</p>
        </div>
    </div>
</div>

<br>

<?php if (empty($registrosAsistencia)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>Aún no tienes registro de asistencia en sesiones vivas.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>MÓDULO</th>
                    <th>SESIÓN</th>
                    <th>PROFESOR</th>
                    <th>FECHA</th>
                    <th>HORA ENTRADA</th>
                    <th>HORA SALIDA</th>
                    <th>DURACIÓN</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registrosAsistencia as $asist) { ?>
                <tr>
                    <td><strong><?= Security::escapeHtml(htmlspecialchars($asist['nombreModulo'])) ?></strong></td>
                    <td><?= Security::escapeHtml(htmlspecialchars($asist['tituloSesion'])) ?></td>
                    <td><?= Security::escapeHtml(htmlspecialchars($asist['nombreProfesor'])) ?></td>
                    <td><?= Security::escapeHtml(date('d/m/Y', strtotime($asist['fechaSesion']))) ?></td>
                    <td><?= Security::escapeHtml($asist['horaUnion'] ? date('H:i', strtotime($asist['horaUnion'])) : '-') ?></td>
                    <td><?= Security::escapeHtml($asist['horaSalida'] ? date('H:i', strtotime($asist['horaSalida'])) : '-') ?></td>
                    <td>
                        <strong><?= Security::escapeHtml(round($asist['duracion'] / 60)) ?></strong> min
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3>Explicación de Datos</h3>
        <ul>
            <li><strong>Sesiones Asistidas:</strong> Número total de sesiones vivas a las que asististe</li>
            <li><strong>Minutos Totales:</strong> Suma total de minutos que participaste en sesiones</li>
            <li><strong>Promedio:</strong> Tiempo promedio de participación por sesión</li>
            <li><strong>Duración:</strong> Tiempo que estuviste conectado en cada sesión (en segundos)</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


