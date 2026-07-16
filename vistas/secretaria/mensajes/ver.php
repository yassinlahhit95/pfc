<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idReclamacion = (int)($_GET['id'] ?? 0);
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: lista.php");
    exit;
}

$esParaAdmin = ($mensaje['emisor_rol'] === 'estudiante' && $mensaje['idProfesor'] === null)
            || ($mensaje['emisor_rol'] === 'profesor'   && $mensaje['idEstudiante'] === null);
if (!$mensaje['leido'] && $esParaAdmin) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$hilo = obtenerHiloCompleto($idReclamacion);

if ($mensaje['emisor_rol'] === 'admin') {
    $fromInit  = 'SC'; $fromAva = 'msg-ava-lg inbox-ava-admin';
    $fromRtag  = 'rtag-admin'; $fromRlabel = 'Secretaría'; $fromName = '';
    if (!empty($mensaje['idEstudiante']))     { $toName = Security::escapeHtml($mensaje['nombreEstudiante'] ?? '—'); $toRtag = 'rtag-alumno'; $toRlabel = 'Alumno'; }
    elseif (!empty($mensaje['idProfesor']))   { $toName = Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');   $toRtag = 'rtag-profe';  $toRlabel = 'Profe'; }
    else                                  { $toName = 'General'; $toRtag = ''; $toRlabel = ''; }
} elseif ($mensaje['emisor_rol'] === 'profesor') {
    $fromName  = Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');
    $fromInit  = Security::escapeHtml(mb_strtoupper(mb_substr($mensaje['nombreProfesor'] ?? 'P', 0, 2)));
    $fromAva   = 'msg-ava-lg inbox-ava-profe'; $fromRtag = 'rtag-profe'; $fromRlabel = 'Profe';
    $toName = ''; $toRtag = 'rtag-admin'; $toRlabel = 'Secretaría';
} else {
    $fromName  = Security::escapeHtml($mensaje['nombreEstudiante'] ?? '—');
    $fromInit  = Security::escapeHtml(mb_strtoupper(mb_substr($mensaje['nombreEstudiante'] ?? 'A', 0, 2)));
    $fromAva   = 'msg-ava-lg inbox-ava-alumno'; $fromRtag = 'rtag-alumno'; $fromRlabel = 'Alumno';
    $toName = ''; $toRtag = 'rtag-admin'; $toRlabel = 'Secretaría';
}

$titulo_pagina = "AULAPRO | MENSAJE";
$seccion = 'mensajes';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/features/mensajes.css">

<div class="msg-page">

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--gap);flex-wrap:wrap;">
        <a href="lista.php" class="ibtn ibtn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al buzón
        </a>
        <a href="#" class="ibtn ibtn-danger" style="margin-left:auto;"
           data-modal-borrar
           data-id="<?= $idReclamacion ?>"
           data-tipo="Mensaje"
           data-nombre="<?= Security::escapeHtml($mensaje['asunto'] ?? '—') ?>"
           data-url="/controladores/secretaria/mensajes/borrar.php"
           data-campo="idReclamacion"
           data-redirect="/vistas/secretaria/mensajes/lista.php">
            <i class="fas fa-trash"></i> Eliminar
        </a>
    </div>

    <?php if ($exito): ?>
    <div class="inbox-banner" style="margin-bottom:var(--gap);">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
        <button class="inbox-banner-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>
    <?php if ($errores): ?>
    <div class="inbox-banner" style="margin-bottom:var(--gap);background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:var(--rojo);">
        <i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml($errores) ?>
        <button class="inbox-banner-close" style="color:var(--rojo);" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <div class="msg-card" style="margin-bottom:var(--gap);">
        <div class="msg-card-head">
            <div class="<?= $fromAva ?>"><?= $fromInit ?></div>
            <div class="msg-head-meta">
                <div class="msg-head-subject"><?= Security::escapeHtml(strtoupper($mensaje['asunto'] ?? '')) ?></div>
                <div class="msg-meta-row">
                    <div class="msg-meta-item">
                        <span class="msg-meta-label">De:</span>
                        <span class="rtag <?= $fromRtag ?>"><?= $fromRlabel ?></span>
                        <?= $fromName ?>
                    </div>
                    <div class="msg-meta-item">
                        <span class="msg-meta-label">Para:</span>
                        <?php if ($toRtag): ?><span class="rtag <?= $toRtag ?>"><?= $toRlabel ?></span><?php endif; ?>
                        <?= $toName ?>
                    </div>
                    <div class="msg-meta-item">
                        <span class="msg-meta-label">Fecha:</span>
                        <?= Security::escapeHtml(date('d/m/Y H:i', strtotime($mensaje['fecha']))) ?>
                    </div>
                    <div class="msg-meta-item">
                        <?php if ($mensaje['leido']): ?>
                            <span class="schip schip-read"><i class="fas fa-check-double"></i> Leído</span>
                        <?php else: ?>
                            <span class="schip schip-unread"><i class="fas fa-circle"></i> Nuevo</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="msg-thread-wrap">
        <div class="msg-thread-title">
            <i class="fas fa-comments"></i>
            Conversación (<?= count($hilo) ?> mensaje<?= count($hilo) !== 1 ? 's' : '' ?>)
        </div>
        <div class="msg-thread-body" id="thread-body">
            <?php foreach ($hilo as $mensajeHilo):
                $isMine = ($mensajeHilo['emisor_rol'] === 'admin');
                if ($mensajeHilo['emisor_rol'] === 'admin') {
                    $avaClass = 'ava-admin'; $avaInit = 'SC'; $senderLabel = 'Secretaría';
                } elseif ($mensajeHilo['emisor_rol'] === 'profesor') {
                    $avaClass    = 'ava-profe';
                    $avaInit     = Security::escapeHtml(mb_strtoupper(mb_substr($mensajeHilo['nombreProfesor'] ?? 'P', 0, 2)));
                    $senderLabel = Security::escapeHtml($mensajeHilo['nombreProfesor'] ?? 'Profesor');
                } else {
                    $avaClass    = 'ava-alumno';
                    $avaInit     = Security::escapeHtml(mb_strtoupper(mb_substr($mensajeHilo['nombreEstudiante'] ?? 'A', 0, 2)));
                    $senderLabel = Security::escapeHtml($mensajeHilo['nombreEstudiante'] ?? 'Alumno');
                }
                $timeStr   = date('d/m/Y H:i', strtotime($mensajeHilo['fecha']));
                $contenido = $mensajeHilo['descripcion'] ?? '';
            ?>
            <div class="msg-thread-row <?= $isMine ? 'mine' : '' ?>">
                <div class="msg-thread-ava <?= $avaClass ?>"><?= $avaInit ?></div>
                <div class="msg-thread-bubble-wrap">
                    <?php if (!$isMine): ?>
                    <div class="msg-thread-sender-name"><?= $senderLabel ?></div>
                    <?php endif; ?>
                    <div class="msg-thread-bubble"><?= Security::escapeHtml($contenido) ?></div>
                    <div class="msg-thread-foot">
                        <span class="msg-thread-time"><?= $timeStr ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <form method="POST" action="../../../controladores/secretaria/mensajes/actualizar.php" class="msg-thread-reply">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
            <textarea name="respuesta" placeholder="Escribe tu respuesta…" maxlength="1000" required></textarea>
            <button type="submit" name="guardarCambios" class="msg-thread-send" title="Enviar respuesta">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script src="../../../public/js/features/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/features/mensajes.js') ?>"></script>
<script>
(function () {
    var body = document.getElementById('thread-body');
    if (body) body.scrollTop = body.scrollHeight;
})();
</script>
