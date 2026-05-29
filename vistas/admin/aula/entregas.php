<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idTarea = $_GET['id'] ?? null;

if (!$idTarea) {
    header("Location: tareas.php");
    exit;
}

$con = obtenerConexion();

// Get tarea info
$sqlTarea = "SELECT t.*, m.nombreModulo, p.nombreProfesor FROM aula_tareas t
             JOIN modulos m ON t.idModulo = m.idModulo
             JOIN profesores p ON t.idProfesor = p.idProfesor
             WHERE t.idTarea = ?";
$stmtTarea = mysqli_prepare($con, $sqlTarea);
mysqli_stmt_bind_param($stmtTarea, "i", $idTarea);
mysqli_stmt_execute($stmtTarea);
$resTarea = mysqli_stmt_get_result($stmtTarea);
$tarea = mysqli_fetch_assoc($resTarea);

if (!$tarea) {
    mysqli_close($con);
    header("Location: tareas.php");
    exit;
}

// Get entregas
$sqlEntregas = "SELECT e.*, est.nombreEstudiante FROM aula_entregas e
                JOIN estudiantes est ON e.idEstudiante = est.idEstudiante
                WHERE e.idTarea = ?
                ORDER BY e.fechaEntrega DESC";
$stmtEntregas = mysqli_prepare($con, $sqlEntregas);
mysqli_stmt_bind_param($stmtEntregas, "i", $idTarea);
mysqli_stmt_execute($stmtEntregas);
$resEntregas = mysqli_stmt_get_result($stmtEntregas);
$entregas = [];
while ($row = mysqli_fetch_assoc($resEntregas)) {
    $entregas[] = $row;
}
mysqli_close($con);

$titulo_pagina = 'AULAPRO | ENTREGAS DE TAREA';
$seccion = 'aula_entregas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ENTREGAS - <?= htmlspecialchars($tarea['titulo']) ?></h1>
    <p class="texto-suave"><?= htmlspecialchars($tarea['nombreModulo']) ?> - Prof. <?= htmlspecialchars($tarea['nombreProfesor']) ?></p>
</div>

<div style="margin-bottom: 20px; text-align: right;">
    <a href="tareas.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if (empty($entregas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay entregas para esta tarea.</p>
    </div>
<?php } else { ?>
    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
        <strong>Estadísticas:</strong><br>
        Total Entregas: <?= count($entregas) ?> |
        Calificadas: <?= count(array_filter($entregas, function($e) { return $e['nota'] !== null; })) ?> |
        Pendientes: <?= count(array_filter($entregas, function($e) { return $e['nota'] === null; })) ?>
    </div>

    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>ESTUDIANTE</th>
                    <th>FECHA ENTREGA</th>
                    <th>ARCHIVO</th>
                    <th>CALIFICACIÓN</th>
                    <th>ESTADO</th>
                    <th>COMENTARIO</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($entregas as $entrega) { ?>
                <tr>
                    <td><strong><?= htmlspecialchars($entrega['nombreEstudiante']) ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) ?></td>
                    <td>
                        <a href="../../../public/uploads/aula/entregas/<?= htmlspecialchars($entrega['archivoEntrega']) ?>"
                           class="boton-secundario btn-pequeno" download title="Descargar">
                            <i class="fas fa-download"></i>
                        </a>
                    </td>
                    <td>
                        <?php if ($entrega['nota'] !== null) { ?>
                            <strong style="font-size: 16px; color: #4caf50;"><?= $entrega['nota'] ?>/10</strong>
                        <?php } else { ?>
                            <span style="color: #999;">-</span>
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
                        <?php if ($entrega['comentarioCalificacion']) { ?>
                            <span title="<?= htmlspecialchars($entrega['comentarioCalificacion']) ?>" style="cursor: help;">
                                <i class="fas fa-comment"></i> Ver
                            </span>
                        <?php } else { ?>
                            -
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
