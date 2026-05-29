<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Security.php";

$idProfesor = $_SESSION['idProfesor'];
$idEntrega = $_GET['id'] ?? null;

if (!$idEntrega) {
    header("Location: tareas.php");
    exit;
}

$con = obtenerConexion();
$sql = "SELECT e.*, t.titulo, e.idTarea, e.idEstudiante, est.nombreEstudiante
        FROM aula_entregas e
        JOIN aula_tareas t ON e.idTarea = t.idTarea
        JOIN estudiantes est ON e.idEstudiante = est.idEstudiante
        WHERE e.idEntrega = ? AND t.idProfesor = ?";
$stmt = mysqli_prepare($con, $sql);
mysqli_stmt_bind_param($stmt, "ii", $idEntrega, $idProfesor);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$entrega = mysqli_fetch_assoc($res);
mysqli_close($con);

if (!$entrega) {
    header("Location: tareas.php");
    exit;
}

$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | CALIFICAR ENTREGA';
$seccionActual = 'aula_entregas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CALIFICAR ENTREGA</h1>
    <p class="texto-suave"><?= htmlspecialchars($entrega['nombreEstudiante']) ?> - <?= htmlspecialchars($entrega['titulo']) ?></p>
</div>

<div class="cuadricula-secundaria">
    <div class="caja direccion-columna espacio-grande relleno">
        <div class="panel">
            <div class="titulo-tarjeta">
                <h3>ENTREGA DEL ESTUDIANTE</h3>
            </div>

            <div style="margin: 20px 0; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <strong><i class="fas fa-file"></i> Archivo Entregado:</strong><br>
                <a href="../../../public/uploads/aula/entregas/<?= htmlspecialchars($entrega['archivoEntrega']) ?>"
                   class="boton-secundario" download style="margin-top: 10px;">
                    <i class="fas fa-download"></i> Descargar Entrega
                </a>
            </div>

            <div style="margin: 20px 0; padding: 15px; background: #f0f4f8; border-left: 4px solid #007bff; border-radius: 5px;">
                <strong><i class="fas fa-clock"></i> Fecha de Entrega:</strong><br>
                <?= date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) ?>
            </div>

            <?php if ($entrega['respuesta']) { ?>
            <div style="margin: 20px 0; padding: 15px; background: #fffbea; border-left: 4px solid #ff9800; border-radius: 5px;">
                <strong><i class="fas fa-comment"></i> Comentario del Estudiante:</strong><br>
                <?= htmlspecialchars($entrega['respuesta']) ?>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="caja direccion-columna espacio-grande relleno">
        <div class="panel">
            <div class="titulo-tarjeta">
                <h3>CALIFICAR</h3>
            </div>

            <form method="POST" action="../../../controladores/profesores/aula/calificar_entrega.php" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($idEntrega) ?>">

                <div class="grupo-formulario">
                    <label for="nota">CALIFICACIÓN (0-10) *</label>
                    <input type="number" id="nota" name="nota" min="0" max="10" step="0.5"
                           value="<?= $entrega['nota'] ?? '' ?>" required
                           placeholder="Ej: 8.5">
                </div>

                <div class="grupo-formulario">
                    <label for="comentario">RETROALIMENTACIÓN PARA EL ESTUDIANTE *</label>
                    <textarea id="comentario" name="comentario" rows="6" required
                              placeholder="Proporciona comentarios constructivos sobre la entrega..."><?= htmlspecialchars($entrega['comentarioCalificacion'] ?? '') ?></textarea>
                </div>

                <div class="grupo-formulario">
                    <label for="archivo_correccion">ARCHIVO DE CORRECCIÓN (Opcional)</label>
                    <input type="file" id="archivo_correccion" name="archivo_correccion"
                           accept=".pdf,.doc,.docx,.txt">
                    <span class="texto-pequeño texto-suave">Documento con correcciones detalladas (máx: 10MB)</span>
                </div>

                <div class="grupo-botones">
                    <a href="entregas.php?id=<?= $entrega['idTarea'] ?>" class="boton-secundario">ATRÁS</a>
                    <button type="submit" class="boton-primario">
                        <i class="fas fa-check"></i> GUARDAR CALIFICACIÓN
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
