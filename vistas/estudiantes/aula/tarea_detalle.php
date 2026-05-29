<?php
session_start();

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/Security.php";

$idEstudiante = $_SESSION['idEstudiante'];
$idTarea = $_GET['id'] ?? null;

if (!$idTarea) {
    header("Location: tareas.php");
    exit;
}

$tarea = obtenerTareaPorIdAula($idTarea);

if (!$tarea || $tarea['publicada'] == 0) {
    header("Location: tareas.php");
    exit;
}

$entrega = obtenerEntregaAula($idTarea, $idEstudiante);
$csrfToken = Security::generateCSRFToken();

$tituloDelPagina = 'AULAPRO | ' . htmlspecialchars($tarea['titulo']);
$seccionActual = 'aula_tareas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1><?= htmlspecialchars($tarea['titulo']) ?></h1>
    <p class="texto-suave"><?= htmlspecialchars($tarea['nombreModulo']) ?> - Prof. <?= htmlspecialchars($tarea['nombreProfesor']) ?></p>
</div>

<?php
$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php }
if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="cuadricula-secundaria">
    <div class="caja direccion-columna espacio-grande relleno">
        <div class="panel">
            <div class="titulo-tarjeta">
                <h3>DESCRIPCIÓN DE LA TAREA</h3>
                <span class="texto-suave"><?= date('d/m/Y H:i', strtotime($tarea['fechaCreacion'])) ?></span>
            </div>

            <div style="margin: 20px 0; line-height: 1.6;">
                <?= $tarea['descripcion'] ?>
            </div>

            <?php if ($tarea['archivoAdjunto']) { ?>
            <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px;">
                <strong><i class="fas fa-paperclip"></i> Archivo Adjunto:</strong><br>
                <a href="../../../public/uploads/aula/tareas/<?= htmlspecialchars($tarea['archivoAdjunto']) ?>"
                   class="boton-secundario" download>
                    <i class="fas fa-download"></i> Descargar
                </a>
            </div>
            <?php } ?>
        </div>
    </div>

    <div class="caja direccion-columna espacio-grande relleno">
        <div class="panel">
            <div class="titulo-tarjeta">
                <h3>
                    <?php if ($entrega) { ?>
                        <span class="badge badge-verde">ENTREGADO</span>
                    <?php } else { ?>
                        <span class="badge badge-azul">PENDIENTE DE ENTREGA</span>
                    <?php } ?>
                </h3>
            </div>

            <?php if ($entrega) { ?>
                <div style="margin: 15px 0; padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 3px;">
                    <strong>✓ Fecha de Entrega:</strong> <?= date('d/m/Y H:i', strtotime($entrega['fechaEntrega'])) ?><br>
                    <?php if ($entrega['nota'] !== null) { ?>
                        <strong>✓ Calificación:</strong> <span style="font-size: 18px; color: #4caf50;"><?= $entrega['nota'] ?>/10</span>
                    <?php } else { ?>
                        <strong>⏳ Calificación:</strong> <span style="color: #ff9800;">Pendiente</span>
                    <?php } ?>
                </div>

                <strong><i class="fas fa-file"></i> Tu Entrega:</strong><br>
                <a href="../../../public/uploads/aula/entregas/<?= htmlspecialchars($entrega['archivoEntrega']) ?>"
                   class="boton-secundario" download style="margin: 10px 0;">
                    <i class="fas fa-download"></i> Descargar Mi Entrega
                </a>

                <?php if ($entrega['archivoCorreccion']) { ?>
                <div style="margin-top: 20px; padding: 15px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 3px;">
                    <strong><i class="fas fa-comment"></i> Retroalimentación del Profesor:</strong><br>
                    <?= htmlspecialchars($entrega['comentarioCalificacion']) ?><br><br>
                    <a href="../../../public/uploads/aula/correcciones/<?= htmlspecialchars($entrega['archivoCorreccion']) ?>"
                       class="boton-secundario btn-pequeno" download>
                        <i class="fas fa-file-pdf"></i> Ver Corrección
                    </a>
                </div>
                <?php } ?>

            <?php } else { ?>
                <form method="POST" action="../../../controladores/estudiantes/aula/enviar_entrega.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
                    <input type="hidden" name="idTarea" value="<?= htmlspecialchars($idTarea) ?>">

                    <div class="grupo-formulario">
                        <label for="archivo">SUBIR ARCHIVO DE ENTREGA *</label>
                        <input type="file" id="archivo" name="archivo" required accept=".pdf,.doc,.docx,.zip,.rar,.txt">
                        <span class="texto-pequeño texto-suave">Formatos: PDF, DOC, DOCX, ZIP, RAR, TXT (Máx: 10MB)</span>
                    </div>

                    <div class="grupo-formulario">
                        <label for="respuesta">COMENTARIO (Opcional)</label>
                        <textarea id="respuesta" name="respuesta" rows="4" placeholder="Escribe un comentario sobre tu entrega..."></textarea>
                    </div>

                    <div class="grupo-botones">
                        <a href="tareas.php" class="boton-secundario">ATRÁS</a>
                        <button type="submit" class="boton-primario">
                            <i class="fas fa-check"></i> ENTREGAR TAREA
                        </button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
