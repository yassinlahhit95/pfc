<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_mensajes');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idReclamacion = (int)($_GET['id'] ?? 0);
$mensaje = obtenerMensajePorId($idReclamacion);

if (!$mensaje) {
    header("Location: lista.php");
    exit;
}

// Auto-marcar como leído si llegó a la dirección
$esParaAdmin = ($mensaje['emisor_rol'] === 'estudiante' && $mensaje['idProfesor'] === null)
            || ($mensaje['emisor_rol'] === 'profesor'   && $mensaje['idEstudiante'] === null);

if (!$mensaje['leido'] && $esParaAdmin) {
    marcarMensajeComoLeido($idReclamacion);
    $mensaje['leido'] = 1;
}

$hilo = obtenerHiloCompleto($idReclamacion);

// Construye los metadatos de la cabecera
if ($mensaje['emisor_rol'] === 'admin') {
    $fromName = '';
    $fromAva  = 'msg-ava-lg inbox-ava-admin';
    $fromInit = 'AD';
    $fromRtag = 'rtag-admin'; $fromRlabel = 'Dirección';
    if (!empty($mensaje['idEstudiante'])) {
        $toName = Security::escapeHtml($mensaje['nombreEstudiante'] ?? '—');
        $toRtag = 'rtag-alumno'; $toRlabel = 'Alumno';
    } elseif (!empty($mensaje['idProfesor'])) {
        $toName = Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');
        $toRtag = 'rtag-profe'; $toRlabel = 'Profe';
    } else {
        $toName = 'General (todos)'; $toRtag = ''; $toRlabel = '';
    }
} elseif ($mensaje['emisor_rol'] === 'profesor') {
    $fromName = Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');
    $fromInit = Security::escapeHtml(mb_strtoupper(mb_substr($mensaje['nombreProfesor'] ?? 'P', 0, 2)));
    $fromAva  = 'msg-ava-lg inbox-ava-profe';
    $fromRtag = 'rtag-profe'; $fromRlabel = 'Profe';
    $toName   = ''; $toRtag = 'rtag-admin'; $toRlabel = 'Dirección';
} else {
    $fromName = Security::escapeHtml($mensaje['nombreEstudiante'] ?? '—');
    $fromInit = Security::escapeHtml(mb_strtoupper(mb_substr($mensaje['nombreEstudiante'] ?? 'A', 0, 2)));
    $fromAva  = 'msg-ava-lg inbox-ava-alumno';
    $fromRtag = 'rtag-alumno'; $fromRlabel = 'Alumno';
    $toName   = ''; $toRtag = 'rtag-admin'; $toRlabel = 'Dirección';
}

$titulo_pagina = "AULAPRO | Mensaje";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/features/mensajes.css">

<div class="msg-page">

    <div style="display:flex;align-items:center;gap:10px;margin-bottom:var(--gap);flex-wrap:wrap;">
        <a href="lista.php" class="ibtn ibtn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al buzón
        </a>
        <?php if (($mensaje['emisor_rol'] !== 'admin') && FeatureGuard::check('feature_chat')): ?>
        <?php
            if ($mensaje['emisor_rol'] === 'profesor') {
                $chatTargetRol  = 'profesor';
                $chatTargetId   = (int)($mensaje['idProfesor'] ?? 0);
                $chatTargetName = $mensaje['nombreProfesor'] ?? 'Profesor';
            } else {
                $chatTargetRol  = 'estudiante';
                $chatTargetId   = (int)($mensaje['idEstudiante'] ?? 0);
                $chatTargetName = $mensaje['nombreEstudiante'] ?? 'Alumno';
            }
        ?>
        <button type="button" class="ibtn ibtn-primary"
                onclick="if(window.ChatWidget)ChatWidget.startWith(<?= Security::escapeHtml(json_encode($chatTargetRol)) ?>,<?= $chatTargetId ?>,<?= Security::escapeHtml(json_encode($chatTargetName)) ?>)">
            <i class="fas fa-comments"></i> Chat en vivo
        </button>
        <?php endif; ?>
        <a href="#" class="ibtn ibtn-danger" style="margin-left:auto;"
           data-modal-borrar
           data-id="<?= $idReclamacion ?>"
           data-tipo="Mensaje"
           data-nombre="<?= Security::escapeHtml($mensaje['asunto'] ?? '—') ?>"
           data-url="/controladores/admin/mensajes/borrar.php"
           data-campo="idReclamacion"
           data-redirect="/vistas/admin/mensajes/lista.php">
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

    <!-- Hilo de conversación -->
    <div class="msg-thread-wrap">
        <div class="msg-thread-title">
            <i class="fas fa-comments"></i>
            Conversación (<?= count($hilo) ?> mensaje<?= count($hilo) !== 1 ? 's' : '' ?>)
        </div>
        <div class="msg-thread-body" id="thread-body">
            <?php foreach ($hilo as $mensajeHilo):
                $isMine = ($mensajeHilo['emisor_rol'] === 'admin');
                if ($mensajeHilo['emisor_rol'] === 'admin') {
                    $avaClass = 'ava-admin'; $avaInit = 'AD'; $senderLabel = 'Dirección';
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
                $esEditado = !empty($mensajeHilo['editado']);
            ?>
            <div class="msg-thread-row <?= $isMine ? 'mine' : '' ?>" data-msg-id="<?= (int)$mensajeHilo['idReclamacion'] ?>">
                <div class="msg-thread-ava <?= $avaClass ?>"><?= $avaInit ?></div>
                <div class="msg-thread-bubble-wrap">
                    <?php if (!$isMine): ?>
                    <div class="msg-thread-sender-name"><?= $senderLabel ?></div>
                    <?php endif; ?>
                    <div class="msg-thread-bubble" data-original="<?= Security::escapeHtml($contenido) ?>"><?= Security::escapeHtml($contenido) ?></div>
                    <div class="msg-thread-foot">
                        <span class="msg-thread-time"><?= $timeStr ?></span>
                        <?php if ($esEditado): ?><span class="msg-editado-chip">Editado</span><?php endif; ?>
                        <?php if ($isMine): ?>
                        <button class="msg-edit-btn" data-msg-id="<?= (int)$mensajeHilo['idReclamacion'] ?>" title="Editar mensaje">
                            <i class="fas fa-pencil-alt"></i>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Formulario de respuesta -->
        <form method="POST" action="../../../controladores/admin/mensajes/actualizar.php" class="msg-thread-reply">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idReclamacion" value="<?= $idReclamacion ?>">
            <textarea name="respuesta" placeholder="Escribe tu respuesta…" maxlength="1000" required></textarea>
            <button type="submit" name="guardarCambios" class="msg-thread-send" title="Enviar respuesta">
                <i class="fas fa-paper-plane"></i>
            </button>
        </form>
    </div>

</div>

<?php include '../comunes/footer.php'; ?>
<script src="../../../public/js/features/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/features/mensajes.js') ?>"></script>
<script>
window.MSG_EDIT_URL = '/controladores/admin/mensajes/editar.php';
(function () {
    var body = document.getElementById('thread-body');
    if (body) body.scrollTop = body.scrollHeight;
    // Focus reply textarea on desktop only (mobile would trigger keyboard)
    if (window.innerWidth >= 720) {
        var ta = document.querySelector('.msg-thread-reply textarea');
        if (ta) ta.focus();
    }
})();

// AJAX reply form
(function () {
    var form = document.querySelector('.msg-thread-reply');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        var ta  = form.querySelector('textarea[name="respuesta"]');
        var text = (ta ? ta.value : '').trim();
        if (!text) return;

        var btn = form.querySelector('[name="guardarCambios"]');
        if (btn) { btn.disabled = true; }

        var fd = new FormData(form);
        fd.append('guardarCambios', '1');

        fetch(form.action, {
            method:  'POST',
            body:    fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(function (r) { return r.json(); })
        .then(function (res) {
            if (res && res.ok) {
                // Play sound
                if (typeof playMsgSound === 'function') playMsgSound();

                // Append message to thread optimistically
                var threadBody = document.getElementById('thread-body');
                if (threadBody) {
                    var now = new Date();
                    var pad = function(n){ return String(n).padStart(2,'0'); };
                    var timeStr = pad(now.getDate())+'/'+pad(now.getMonth()+1)+'/'+now.getFullYear()+' '+pad(now.getHours())+':'+pad(now.getMinutes());
                    var div = document.createElement('div');
                    div.className = 'msg-thread-row mine';
                    div.innerHTML =
                        '<div class="msg-thread-ava ava-admin">AD</div>' +
                        '<div class="msg-thread-bubble-wrap">' +
                            '<div class="msg-thread-bubble" data-original="'+escHtml(text)+'">'+escHtml(text)+'</div>' +
                            '<div class="msg-thread-foot">' +
                                '<span class="msg-thread-time">'+escHtml(timeStr)+'</span>' +
                                '<button class="msg-edit-btn" data-msg-id="" title="Editar mensaje"><i class="fas fa-pencil-alt"></i></button>' +
                            '</div>' +
                        '</div>';
                    threadBody.appendChild(div);
                    threadBody.scrollTop = threadBody.scrollHeight;
                }

                // Clear textarea
                if (ta) ta.value = '';

                if (window.Toast) Toast.show('Respuesta enviada', 'success');
            } else {
                if (window.Toast) Toast.show((res && res.msg) || 'Error al enviar', 'error');
            }
        })
        .catch(function () {
            if (window.Toast) Toast.show('Error de conexión', 'error');
        })
        .finally(function () {
            if (btn) btn.disabled = false;
        });
    });

    function escHtml(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }
})();
</script>
