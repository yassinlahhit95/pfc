<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idProfesor = $_SESSION['idProfesor'];
$idTarea = $_GET['id'] ?? null;

if (!$idTarea) {
    header("Location: tareas.php");
    exit;
}

$tarea = obtenerTareaPorIdAula($idTarea);

if (!$tarea || $tarea['idProfesor'] != $idProfesor) {
    header("Location: tareas.php");
    exit;
}

$entregas = listarEntregasPorTareaAula($idTarea);

$tituloDelPagina = 'AULAPRO | ENTREGAS DE TAREA';
$seccionActual = 'aula_entregas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>ENTREGAS - <?= htmlspecialchars($tarea['titulo']) ?></h1>
    <p class="texto-suave">Revisa y califica las entregas de tus estudiantes</p>
</div>

<div style="margin-bottom: 20px; text-align: right;">
    <a href="tareas.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>

<?php if (empty($entregas)) { ?>
    <div class="alerta-info">
        <i class="fas fa-info-circle"></i>
        <p>Aún no hay entregas para esta tarea.</p>
    </div>
<?php } else { ?>
    <div class="tabla-responsiva">
        <table class="tabla-contenido">
            <thead>
                <tr>
                    <th>ESTUDIANTE</th>
                    <th>FECHA ENTREGA</th>
                    <th>ARCHIVO</th>
                    <th>CALIFICACIÓN</th>
                    <th>ESTADO</th>
                    <th>ACCIONES</th>
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
                        <a href="calificar.php?id=<?= $entrega['idEntrega'] ?>" class="boton-primario btn-pequeno">
                            <i class="fas fa-edit"></i> CALIFICAR
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php } ?>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
