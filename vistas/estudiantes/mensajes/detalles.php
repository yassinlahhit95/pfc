<?php
require_once __DIR__ . "/../../../include/Security.php";

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../login.php");
    exit;
}

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = (int)($_GET['id'] ?? 0);
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje || (int)$mensaje['idEstudiante'] !== (int)$_SESSION['idEstudiante']) {
    $_SESSION['errores'] = "Mensaje no encontrado o acceso denegado.";
    header("Location: lista.php");
    exit;
}

if (!$mensaje['leido'] && $mensaje['emisor_rol'] !== 'estudiante') {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$hilo = obtenerHiloCompleto($idReclamacion);

if ($mensaje['emisor_rol'] === 'estudiante') {
    $fromName = 'Tú'; $fromInit = 'YO';
    $fromAva  = 'msg-ava-lg inbox-ava-yo';
    $fromRtag = 'rtag-yo'; $fromRlabel = 'Tú';
    if (!empty($mensaje['nombreProfesor'])) {
        $toName = Security::escapeHtml($mensaje['nombreProfesor']);
        $toRtag = 'rtag-profe'; $toRlabel = 'Profe';
    } else {
        $toName = 'Dirección'; $toRtag = 'rtag-admin'; $toRlabel = 'Admin';
    }
} elseif ($mensaje['emisor_rol'] === 'profesor') {
    $fromName = Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');
    $fromInit = mb_strtoupper(mb_substr($mensaje['nombreProfesor'] ?? 'P', 0, 2));
    $fromAva  = 'msg-ava-lg inbox-ava-profe';
    $fromRtag = 'rtag-profe'; $fromRlabel = 'Profe';
    $toName   = 'Tú'; $toRtag = 'rtag-yo'; $toRlabel = 'Tú';
} else {
    $fromName = 'Dirección'; $fromInit = 'AD';
    $fromAva  = 'msg-ava-lg inbox-ava-admin';
    $fromRtag = 'rtag-admin'; $fromRlabel = 'Admin';
    $toName   = 'Tú'; $toRtag = 'rtag-yo'; $toRlabel = 'Tú';
}

$tituloDelPagina = "AULAPRO | Detalle Mensaje";
$seccionActual   = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">

<div class="msg-page">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--gap);flex-wrap:wrap;">
        <a href="lista.php" class="ibtn ibtn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al buzón
        </a>
    </div>

    <?php if ($exito): ?>
    <div class="inbox-banner" style="margin-bottom:var(--gap);">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
        <button class="inbox-banner-close" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>
    <?php if ($errores): ?>
    <div class="inbox-banner" style="margin-bottom:var(--gap);background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:#dc2626;">
        <i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml($errores) ?>
        <button class="inbox-banner-close" style="color:#dc2626;" onclick="this.parentElement.remove()">×</button>
    </div>
    <?php endif; ?>

    <!-- Cabecera del mensaje -->
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
                        <span class="rtag <?= $toRtag ?>"><?= $toRlabel ?></span>
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

    <!-- Hilo de conversación -->
    <div class="msg-thread-wrap">
        <div class="msg-thread-title">
            <i class="fas fa-comments"></i>
            Conversación (<?= count($hilo) ?> mensaje<?= count($hilo) !== 1 ? 's' : '' ?>)
        </div>
        <div class="msg-thread-body" id="thread-body">
            <?php foreach ($hilo as $item):
                $isMine = ($item['emisor_rol'] === 'estudiante' && (int)$item['idEstudiante'] === (int)$_SESSION['idEstudiante']);
                if ($item['emisor_rol'] === 'admin') {
                    $avaClass = 'ava-admin'; $avaInit = 'AD'; $senderLabel = 'Dirección';
                } elseif ($item['emisor_rol'] === 'profesor') {
                    $avaClass    = 'ava-profe';
                    $avaInit     = mb_strtoupper(mb_substr($item['nombreProfesor'] ?? 'P', 0, 2));
                    $senderLabel = Security::escapeHtml($item['nombreProfesor'] ?? 'Profesor');
                } else {
                    $avaClass    = 'ava-yo';
                    $avaInit     = 'YO';
                    $senderLabel = 'Tú';
                }
                $timeStr   = date('d/m/Y H:i', strtotime($item['fecha']));
                $contenido = $item['descripcion'] ?? '';
            ?>
            <div class="msg-thread-row <?= $isMine ? 'mine' : '' ?>">
                <div class="msg-thread-ava <?= $avaClass ?>"><?= $isMine ? 'YO' : $avaInit ?></div>
                <div class="msg-thread-bubble-wrap">
                    <?php if (!$isMine): ?>
                    <div class="msg-thread-sender-name"><?= $senderLabel ?></div>
                    <?php endif; ?>
                    <div class="msg-thread-bubble"><?= Security::escapeHtml($contenido) ?></div>
                    <div class="msg-thread-time"><?= $timeStr ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulario de respuesta -->
        <form method="POST" action="../../../controladores/estudiantes/mensajes/responder.php" class="msg-thread-reply">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
            <textarea name="respuesta" placeholder="Escribe tu respuesta…" maxlength="1000" required></textarea>
            <button type="submit" name="enviarRespuesta" class="msg-thread-send" title="Enviar respuesta">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
<script src="../../../public/js/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/mensajes.js') ?>"></script>
<script>
(function () {
    var body = document.getElementById('thread-body');
    if (body) body.scrollTop = body.scrollHeight;
})();
</script>
