<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$page = (int)($_GET['p'] ?? 1);
$itemsPerPage = 50;
$offset = ($page - 1) * $itemsPerPage;

$con = obtenerConexion();

$sqlTotal = "SELECT COUNT(*) as total FROM aula_asistencias";
$resTotal = mysqli_query($con, $sqlTotal);
$rowTotal = mysqli_fetch_assoc($resTotal);
$totalAsistencias = $rowTotal['total'];
$totalPages = ceil($totalAsistencias / $itemsPerPage);

$sql = "SELECT aa.*, s.titulo, s.fechaSesion, s.horaSesion,
               m.nombreModulo, e.nombreEstudiante, p.nombreProfesor
        FROM aula_asistencias aa
        JOIN aula_sesiones s ON aa.idSesion = s.idSesion
        JOIN modulos m ON s.idModulo = m.idModulo
        JOIN estudiantes e ON aa.idEstudiante = e.idEstudiante
        JOIN profesores p ON s.idProfesor = p.idProfesor
        ORDER BY s.fechaSesion DESC, s.horaSesion DESC
        LIMIT ? OFFSET ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $itemsPerPage, $offset);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$asistencias = [];
while ($row = mysqli_fetch_assoc($res)) {
    $asistencias[] = $row;
}
mysqli_close($con);

$titulo_pagina = 'AULAPRO | REGISTRO DE ASISTENCIAS';
$seccion = 'aula_asistencia';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRO DE ASISTENCIAS</h1>
    <p class="texto-suave">Monitoreo de asistencias en sesiones vivas (<?= $totalAsistencias ?> registros)</p>
</div>

<?php if (empty($asistencias)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay registros de asistencia en el sistema.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>ESTUDIANTE</th>
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
                <?php foreach ($asistencias as $asist) { ?>
                <tr>
                    <td><strong><?= htmlspecialchars($asist['nombreEstudiante']) ?></strong></td>
                    <td><?= htmlspecialchars($asist['nombreModulo']) ?></td>
                    <td><?= htmlspecialchars($asist['titulo']) ?></td>
                    <td><?= htmlspecialchars($asist['nombreProfesor']) ?></td>
                    <td><?= date('d/m/Y', strtotime($asist['fechaSesion'])) ?></td>
                    <td><?= $asist['horaUnion'] ? date('H:i', strtotime($asist['horaUnion'])) : '-' ?></td>
                    <td><?= $asist['horaSalida'] ? date('H:i', strtotime($asist['horaSalida'])) : '-' ?></td>
                    <td>
                        <?php
                        $duracion = $asist['duracion'] ?? 0;
                        $minutos = floor($duracion / 60);
                        $segundos = $duracion % 60;
                        ?>
                        <strong><?= $minutos ?>m <?= $segundos ?>s</strong>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1) { ?>
    <div class="paginacion">
        <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
            <a href="?p=<?= $i ?>" class="boton-paginacion <?= ($i == $page) ? 'activo' : '' ?>">
                <?= $i ?>
            </a>
        <?php } ?>
    </div>
    <?php } ?>

    <div class="info-sistema">
        <h3>Estadísticas</h3>
        <ul>
            <li><strong>Total de Registros:</strong> <?= $totalAsistencias ?></li>
            <li><strong>Mostrando:</strong> <?= $offset + 1 ?> a <?= min($offset + $itemsPerPage, $totalAsistencias) ?> de <?= $totalAsistencias ?></li>
            <li><strong>Duración:</strong> Tiempo en segundos que el estudiante estuvo conectado</li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
