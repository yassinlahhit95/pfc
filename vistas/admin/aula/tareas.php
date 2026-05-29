<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$con = obtenerConexion();
$sql = "SELECT t.*, m.nombreModulo, p.nombreProfesor,
               (SELECT COUNT(*) FROM aula_entregas e WHERE e.idTarea = t.idTarea) as totalEntregas,
               (SELECT COUNT(*) FROM aula_entregas e WHERE e.idTarea = t.idTarea AND e.nota IS NOT NULL) as totalCalificadas
        FROM aula_tareas t
        JOIN modulos m ON t.idModulo = m.idModulo
        JOIN profesores p ON t.idProfesor = p.idProfesor
        ORDER BY t.fechaCreacion DESC";
$result = mysqli_query($con, $sql);
$tareas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tareas[] = $row;
}
mysqli_close($con);

$titulo_pagina = 'AULAPRO | TAREAS';
$seccion = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MONITOREO DE TAREAS</h1>
    <p class="texto-suave">Visualización de todas las tareas del sistema</p>
</div>

<?php if (empty($tareas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No hay tareas en el sistema.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>TAREA</th>
                    <th>MÓDULO</th>
                    <th>PROFESOR</th>
                    <th>ESTADO</th>
                    <th>ENTREGAS</th>
                    <th>CALIFICADAS</th>
                    <th>CREADA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $tarea) { ?>
                <tr>
                    <td><strong><?= htmlspecialchars(substr($tarea['titulo'], 0, 40)) ?></strong></td>
                    <td><?= htmlspecialchars($tarea['nombreModulo']) ?></td>
                    <td><?= htmlspecialchars($tarea['nombreProfesor']) ?></td>
                    <td>
                        <?php if ($tarea['publicada']) { ?>
                            <span class="badge badge-verde">PUBLICADA</span>
                        <?php } else { ?>
                            <span class="badge badge-gris">BORRADOR</span>
                        <?php } ?>
                    </td>
                    <td>
                        <strong><?= $tarea['totalEntregas'] ?></strong> entregas
                    </td>
                    <td>
                        <strong><?= $tarea['totalCalificadas'] ?></strong> / <?= $tarea['totalEntregas'] ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($tarea['fechaCreacion'])) ?></td>
                    <td>
                        <a href="entregas.php?id=<?= $tarea['idTarea'] ?>" class="boton-secundario btn-pequeno" title="Ver entregas">
                            <i class="fas fa-inbox"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="info-sistema">
        <h3>Estadísticas Generales</h3>
        <ul>
            <li><strong>Total de Tareas:</strong> <?= count($tareas) ?></li>
            <li><strong>Publicadas:</strong> <?= count(array_filter($tareas, function($t) { return $t['publicada']; })) ?></li>
            <li><strong>Borradores:</strong> <?= count(array_filter($tareas, function($t) { return !$t['publicada']; })) ?></li>
            <li><strong>Total de Entregas:</strong> <?= array_sum(array_column($tareas, 'totalEntregas')) ?></li>
        </ul>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
