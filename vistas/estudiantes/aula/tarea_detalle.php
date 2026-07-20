<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";

$idEstudiante = $_SESSION['idEstudiante'];
$idTarea = (int)($_GET['id'] ?? 0);

if (!$idTarea) {
    header("Location: tareas.php");
    exit;
}

$tarea           = obtenerTareaPorIdAula($idTarea);
$datosEstudiante = obtenerEstudiantePorId($idEstudiante);

// Reject if task doesn't exist, isn't published, or belongs to a different ciclo (IDOR guard)
if (!$tarea || $tarea['publicado'] == 0
    || (int)$datosEstudiante['idCiclo'] !== (int)$tarea['idCiclo']) {
    header("Location: tareas.php");
    exit;
}

$entrega = obtenerEntregaAula($idTarea, $idEstudiante);
$csrfToken = Security::generateCSRFToken();

$exito   = $_SESSION['exito'] ?? null;   unset($_SESSION['exito']);
$errores = $_SESSION['errores'] ?? null; unset($_SESSION['errores']);

$tituloDelPagina = 'AULAPRO | ' . Security::escapeHtml($tarea['titulo']);
$seccionActual = 'aula_sesiones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1><?= Security::escapeHtml($tarea['titulo']) ?></h1>
    <p class="subtitulo-encabezado"><?= Security::escapeHtml($tarea['nombreModulo']) ?> - Prof. <?= Security::escapeHtml($tarea['nombreProfesor']) ?></p>
</div>

<?php if ($exito): ?>
<div class="alerta alerta-exito" style="margin-bottom:var(--gap);"><i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?></div>
<?php endif; ?>
<?php if ($errores): ?>
<div class="alerta alerta-error" style="margin-bottom:var(--gap);"><i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml(is_array($errores) ? implode(', ', $errores) : $errores) ?></div>
<?php endif; ?>

<div class="cuadricula-secundaria">
    <div class="caja direccion-columna espacio-grande relleno">
        <div class="panel">
            <div class="titulo-tarjeta">
                <h3>DESCRIPCIÓN DE LA TAREA</h3>
                <span class="texto-suave"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tarea['fechaCreacion']))) ?></span>
            </div>

            <div style="margin: 20px 0; line-height: 1.6;">
                <?= Security::escapeHtml($tarea['descripcion']) ?>
            </div>

            <?php if ($tarea['archivoAdjunto']) { ?>
            <div style="margin-top: 20px; padding: 15px; background:var(--surface-2); border-radius: 5px;">
                <strong><i class="fas fa-paperclip"></i> Archivo Adjunto:</strong><br>
                <a href="../../../public/uploads/aula/tareas/<?= Security::escapeHtml($tarea['archivoAdjunto']) ?>"
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
                <div style="margin: 15px 0; padding: 15px; background: var(--verde-suave); border-left: 4px solid var(--verde); border-radius: 3px;">
                    <strong>✓ Fecha de Entrega:</strong> <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($entrega['fechaEntrega']))) ?><br>
                    <?php if ($entrega['nota'] !== null) { ?>
                        <strong>✓ Calificación:</strong> <span style="font-size: 18px; color: var(--verde);"><?= Security::escapeHtml($entrega['nota']) ?>/10</span>
                    <?php } else { ?>
                        <strong>⏳ Calificación:</strong> <span style="color: var(--naranja);">Pendiente</span>
                    <?php } ?>
                </div>

                <strong><i class="fas fa-file"></i> Tu Entrega:</strong><br>
                <a href="../../../public/uploads/aula/entregas/<?= Security::escapeHtml($entrega['archivoEntrega']) ?>"
                   class="boton-secundario" download style="margin: 10px 0;">
                    <i class="fas fa-download"></i> Descargar Mi Entrega
                </a>

                <?php if ($entrega['archivoCorreccion']) { ?>
                <div style="margin-top: 20px; padding: 15px; background: var(--naranja-suave); border-left: 4px solid var(--naranja); border-radius: 3px;">
                    <strong><i class="fas fa-comment"></i> Retroalimentación del Profesor:</strong><br>
                    <?= Security::escapeHtml($entrega['comentarioCalificacion']) ?><br><br>
                    <a href="../../../public/uploads/aula/correcciones/<?= Security::escapeHtml($entrega['archivoCorreccion']) ?>"
                       class="boton-secundario btn-pequeno" download>
                        <i class="fas fa-file-pdf"></i> Ver Corrección
                    </a>
                </div>
                <?php } ?>

            <?php } else { ?>
                <form method="POST" action="../../../controladores/estudiantes/aula/enviar_entrega.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                    <input type="hidden" name="idTarea" value="<?= Security::escapeHtml($idTarea) ?>">

                    <div class="grupo-formulario">
                        <label for="archivo">SUBIR ARCHIVO DE ENTREGA *</label>
                        <input type="file" id="archivo" name="archivo" accept=".pdf,.doc,.docx,.zip,.rar,.txt">
                        <span class="texto-pequeno texto-suave">Formatos: PDF, DOC, DOCX, ZIP, RAR, TXT (Máx: 10MB)</span>
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

