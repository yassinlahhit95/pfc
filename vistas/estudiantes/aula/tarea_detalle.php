<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/aula.php";
require_once __DIR__ . "/../../../include/R2Client.php";

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

<div class="grid-2col">
    <div class="panel">
        <div class="titulo-tarjeta">
            <h3><i class="fas fa-align-left" style="color:var(--accent);"></i> Descripción de la tarea</h3>
            <span class="texto-suave"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($tarea['fechaCreacion']))) ?></span>
        </div>

        <div style="margin: 20px 0; line-height: 1.6;">
            <?= Security::escapeHtml($tarea['descripcion']) ?>
        </div>

        <?php if ($tarea['archivoAdjunto']) {
            $adjuntoUrl = R2Client::documentoUrl(
                __DIR__ . '/../../../public/uploads/aula/tareas/' . $tarea['archivoAdjunto'],
                '../../../public/uploads/aula/tareas/' . $tarea['archivoAdjunto'],
                'aula/tareas/' . $tarea['archivoAdjunto']
            );
        ?>
        <div style="margin-top: 20px; padding: 15px; background:var(--surface-2); border: 1px solid var(--border); border-radius: 10px;">
            <strong><i class="fas fa-paperclip"></i> Archivo adjunto:</strong><br>
            <a href="<?= Security::escapeHtml($adjuntoUrl) ?>"
               class="boton-secundario" download>
                <i class="fas fa-download"></i> Descargar
            </a>
        </div>
        <?php } ?>
    </div>

    <div class="panel">
        <div class="titulo-tarjeta">
            <h3><i class="fas fa-file-circle-check" style="color:var(--accent);"></i> Tu entrega</h3>
            <?php if ($entrega) { ?>
                <span class="badge badge-exito">ENTREGADO</span>
            <?php } else { ?>
                <span class="badge badge-normal">PENDIENTE</span>
            <?php } ?>
        </div>

            <?php if ($entrega) { ?>
                <div style="margin: 15px 0; padding: 15px; background: var(--verde-suave); border-left: 4px solid var(--verde); border-radius: var(--radius-sm, 10px);">
                    <strong><i class="fas fa-check"></i> Fecha de entrega:</strong> <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($entrega['fechaEntrega']))) ?><br>
                    <?php if ($entrega['nota'] !== null) { ?>
                        <strong><i class="fas fa-check"></i> Calificación:</strong> <span style="font-size: 18px; font-weight:700; color: var(--verde);"><?= Security::escapeHtml($entrega['nota']) ?>/10</span>
                    <?php } else { ?>
                        <strong><i class="fas fa-hourglass-half"></i> Calificación:</strong> <span style="color: var(--naranja);">Pendiente</span>
                    <?php } ?>
                </div>

                <?php
                    $entregaUrl = R2Client::documentoUrl(
                        __DIR__ . '/../../../public/uploads/aula/entregas/' . $entrega['archivoEntrega'],
                        '../../../public/uploads/aula/entregas/' . $entrega['archivoEntrega'],
                        'aula/entregas/' . $entrega['archivoEntrega']
                    );
                ?>
                <strong><i class="fas fa-file"></i> Tu entrega:</strong><br>
                <a href="<?= Security::escapeHtml($entregaUrl) ?>"
                   class="boton-secundario" download style="margin: 10px 0;">
                    <i class="fas fa-download"></i> Descargar mi entrega
                </a>

                <?php if ($entrega['archivoCorreccion']) {
                    $correccionUrl = R2Client::documentoUrl(
                        __DIR__ . '/../../../public/uploads/aula/correcciones/' . $entrega['archivoCorreccion'],
                        '../../../public/uploads/aula/correcciones/' . $entrega['archivoCorreccion'],
                        'aula/correcciones/' . $entrega['archivoCorreccion']
                    );
                ?>
                <div style="margin-top: 20px; padding: 15px; background: var(--naranja-suave); border-left: 4px solid var(--naranja); border-radius: var(--radius-sm, 10px);">
                    <strong><i class="fas fa-comment"></i> Retroalimentación del profesor:</strong><br>
                    <?= Security::escapeHtml($entrega['comentarioCalificacion']) ?><br><br>
                    <a href="<?= Security::escapeHtml($correccionUrl) ?>"
                       class="boton-secundario btn-pequeno" download>
                        <i class="fas fa-file-pdf"></i> Ver corrección
                    </a>
                </div>
                <?php } ?>

            <?php } else { ?>
                <form method="POST" action="../../../controladores/estudiantes/aula/enviar_entrega.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf_token" value="<?= Security::escapeHtml($csrfToken) ?>">
                    <input type="hidden" name="idTarea" value="<?= Security::escapeHtml($idTarea) ?>">

                    <div class="grupo-formulario">
                        <label for="archivo">Subir archivo de entrega *</label>
                        <input type="file" id="archivo" name="archivo" accept=".pdf,.doc,.docx,.zip,.rar,.txt">
                        <span class="texto-pequeno texto-suave">Formatos: PDF, DOC, DOCX, ZIP, RAR, TXT (máx. 10 MB)</span>
                    </div>

                    <div class="grupo-formulario">
                        <label for="respuesta">Comentario (opcional)</label>
                        <textarea id="respuesta" name="respuesta" rows="4" placeholder="Escribe un comentario sobre tu entrega..."></textarea>
                    </div>

                    <div class="grupo-botones">
                        <a href="tareas.php" class="boton-secundario">Atrás</a>
                        <button type="submit" class="boton-primario">
                            <i class="fas fa-check"></i> Entregar tarea
                        </button>
                    </div>
                </form>
            <?php } ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>

