<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];

$con = obtenerConexion();
$sql = "SELECT t.*, m.nombreModulo, p.nombreProfesor,
               (SELECT COUNT(*) FROM aula_entregas e WHERE e.idTarea = t.idTarea) as totalEntregas
        FROM aula_tareas t
        JOIN modulos m ON t.idModulo = m.idModulo
        JOIN profesores p ON t.idProfesor = p.idProfesor
        WHERE t.idProfesor = ?
        ORDER BY t.fechaCreacion DESC";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "i", $idProfesor);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$tareas = [];
while ($row = mysqli_fetch_assoc($res)) {
    $tareas[] = $row;
}
mysqli_close($con);

$tituloDelPagina = 'AULAPRO | MIS TAREAS';
$seccionActual = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MIS TAREAS</h1>
    <p class="texto-suave">Gestiona las tareas de tus módulos</p>
</div>

<div style="margin-bottom: 20px; text-align: right;">
    <a href="crear_tarea.php" class="boton-primario">
        <i class="fas fa-plus-circle"></i> NUEVA TAREA
    </a>
</div>

<?php if (empty($tareas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>No tienes tareas creadas. <a href="crear_tarea.php">Crea una nueva</a></p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>TAREA</th>
                    <th>MÓDULO</th>
                    <th>ESTADO</th>
                    <th>ENTREGAS</th>
                    <th>CREADA</th>
                    <th>ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tareas as $tarea) { ?>
                <tr>
                    <td><strong><?= htmlspecialchars(substr($tarea['titulo'], 0, 40)) ?></strong></td>
                    <td><?= htmlspecialchars($tarea['nombreModulo']) ?></td>
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
                    <td><?= date('d/m/Y', strtotime($tarea['fechaCreacion'])) ?></td>
                    <td>
                        <a href="entregas.php?id=<?= $tarea['idTarea'] ?>" class="boton-secundario btn-pequeno" title="Ver entregas">
                            <i class="fas fa-inbox"></i>
                        </a>
                        <a href="editar_tarea.php?id=<?= $tarea['idTarea'] ?>" class="boton-secundario btn-pequeno" title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="../../../controladores/aula/borrar_tarea.php?id=<?= $tarea['idTarea'] ?>" class="boton-peligro btn-pequeno" onclick="return confirm('¿Eliminar esta tarea?')" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
