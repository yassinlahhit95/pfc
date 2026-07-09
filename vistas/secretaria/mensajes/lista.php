<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$todosMensajes = listarTodosLosMensajes();
$totalSinLeer  = contarMensajesNoLeidosAdmin();
$totalMensajes = count($todosMensajes);

$folder  = $_GET['folder'] ?? 'todo';
$allowed = ['todo', 'nuevos', 'alumnos', 'profesores', 'enviados'];
if (!in_array($folder, $allowed)) $folder = 'todo';

$listaDeMensajes = array_values(array_filter($todosMensajes, function ($m) use ($folder) {
    switch ($folder) {
        case 'nuevos':     return !$m['leido'] && $m['emisor_rol'] !== 'admin';
        case 'alumnos':    return $m['emisor_rol'] === 'estudiante';
        case 'profesores': return $m['emisor_rol'] === 'profesor';
        case 'enviados':   return $m['emisor_rol'] === 'admin';
        default:           return true;
    }
}));

$cNuevos   = (int)$totalSinLeer;
$cAlumnos  = count(array_filter($todosMensajes, fn($m) => $m['emisor_rol'] === 'estudiante'));
$cProfes   = count(array_filter($todosMensajes, fn($m) => $m['emisor_rol'] === 'profesor'));
$cEnviados = count(array_filter($todosMensajes, fn($m) => $m['emisor_rol'] === 'admin'));

$titulo_pagina = "AULAPRO | MENSAJERÍA";
$seccion = 'mensajes';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">
<script>window.MENSAJES_POLL_URL='../../../controladores/ajax/mensajes_polling.php';</script>

<?php if ($exito): ?>
    <div class="inbox-banner" id="msg-banner-exito">
        <i class="fas fa-check-circle"></i> <?= Security::escapeHtml($exito) ?>
        <button class="inbox-banner-close" onclick="this.parentElement.remove()">×</button>
    </div>
<?php endif; ?>
<?php if ($errores): ?>
    <div class="inbox-banner" style="background:rgba(239,68,68,.08);border-color:rgba(239,68,68,.25);color:var(--rojo);">
        <i class="fas fa-exclamation-triangle"></i> <?= Security::escapeHtml(is_string($errores) ? $errores : implode(' ', (array)$errores)) ?>
        <button class="inbox-banner-close" style="color:var(--rojo);" onclick="this.parentElement.remove()">×</button>
    </div>
<?php endif; ?>

<section class="hero" style="margin-bottom:var(--gap);">
    <div class="hero-text">
        <p class="eyebrow">Mensajería</p>
        <h1>Buzón<?php if ($totalSinLeer > 0): ?> <span>(<?= $totalSinLeer ?> nuevos)</span><?php endif; ?></h1>
        <p class="sub">Comunicaciones de <b>estudiantes</b> y <b>profesores</b> con la secretaría</p>
    </div>
</section>

<div class="inbox-wrap" data-mensajes-page="1">

    <div class="inbox-folders">
        <a href="?folder=todo" class="inbox-folder <?= $folder === 'todo' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-inbox"></i></span>
            <span class="inbox-folder-label">Todos</span>
            <?php if ($totalMensajes > 0): ?><span class="inbox-folder-count"><?= $totalMensajes ?></span><?php endif; ?>
        </a>
        <a href="?folder=nuevos" class="inbox-folder <?= $folder === 'nuevos' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-circle-dot"></i></span>
            <span class="inbox-folder-label">No leídos</span>
            <?php if ($cNuevos > 0): ?><span class="inbox-folder-count"><?= $cNuevos ?></span><?php endif; ?>
        </a>
        <a href="?folder=alumnos" class="inbox-folder <?= $folder === 'alumnos' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-user-graduate"></i></span>
            <span class="inbox-folder-label">Alumnos</span>
            <?php if ($cAlumnos > 0): ?><span class="inbox-folder-count"><?= $cAlumnos ?></span><?php endif; ?>
        </a>
        <a href="?folder=profesores" class="inbox-folder <?= $folder === 'profesores' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-chalkboard-teacher"></i></span>
            <span class="inbox-folder-label">Profesores</span>
            <?php if ($cProfes > 0): ?><span class="inbox-folder-count"><?= $cProfes ?></span><?php endif; ?>
        </a>
        <a href="?folder=enviados" class="inbox-folder <?= $folder === 'enviados' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-paper-plane"></i></span>
            <span class="inbox-folder-label">Enviados</span>
            <?php if ($cEnviados > 0): ?><span class="inbox-folder-count"><?= $cEnviados ?></span><?php endif; ?>
        </a>
    </div>

    <div class="inbox-main">
        <div class="inbox-toolbar">
            <span class="inbox-toolbar-title">
                <?php $labels = ['todo'=>'Todos los mensajes','nuevos'=>'No leídos','alumnos'=>'De alumnos','profesores'=>'De profesores','enviados'=>'Enviados'];
                echo $labels[$folder] ?? 'Mensajes'; ?>
            </span>
            <span class="inbox-toolbar-count"><?= count($listaDeMensajes) ?> mensaje(s)</span>
        </div>

        <?php if (empty($listaDeMensajes)): ?>
            <div class="inbox-empty">
                <div class="inbox-empty-ico"><i class="fas fa-inbox"></i></div>
                <p>No hay mensajes en esta carpeta.</p>
            </div>
        <?php else: ?>
            <?php foreach ($listaDeMensajes as $msg):
                $esNuevo   = !$msg['leido'] && $msg['emisor_rol'] !== 'admin';
                $rowClass  = $esNuevo ? 'inbox-unread' : ($msg['emisor_rol'] === 'admin' ? 'inbox-sent' : '');
                $ts        = strtotime($msg['fecha']);
                $timeStr   = (date('Y-m-d', $ts) === date('Y-m-d')) ? date('H:i', $ts) : date('d/m/Y', $ts);

                if ($msg['emisor_rol'] === 'admin') {
                    $senderName = 'Secretaría'; $avaClass = 'inbox-ava-admin'; $avaInit = 'SC';
                    $rtagClass = 'rtag-admin'; $rtagLabel = 'Secretaría';
                    $receiver = '';
                    if (!empty($msg['idEstudiante']))   $receiver = '→ ' . Security::escapeHtml($msg['nombreEstudiante'] ?? '');
                    elseif (!empty($msg['idProfesor'])) $receiver = '→ ' . Security::escapeHtml($msg['nombreProfesor'] ?? '');
                    else                                $receiver = '→ General';
                } elseif ($msg['emisor_rol'] === 'estudiante') {
                    $senderName = Security::escapeHtml($msg['nombreEstudiante'] ?? 'Alumno');
                    $avaClass   = 'inbox-ava-alumno';
                    $avaInit    = Security::escapeHtml(mb_strtoupper(mb_substr($msg['nombreEstudiante'] ?? 'A', 0, 2)));
                    $rtagClass  = 'rtag-alumno'; $rtagLabel = 'Alumno'; $receiver = '→ Secretaría';
                } else {
                    $senderName = Security::escapeHtml($msg['nombreProfesor'] ?? 'Profesor');
                    $avaClass   = 'inbox-ava-profe';
                    $avaInit    = Security::escapeHtml(mb_strtoupper(mb_substr($msg['nombreProfesor'] ?? 'P', 0, 2)));
                    $rtagClass  = 'rtag-profe'; $rtagLabel = 'Profe'; $receiver = '→ Secretaría';
                }
            ?>
            <div class="inbox-row-outer">
                <a href="ver.php?id=<?= (int)$msg['idReclamacion'] ?>" class="inbox-row <?= $rowClass ?>">
                    <div class="inbox-ava <?= $avaClass ?>"><?= $avaInit ?></div>
                    <div class="inbox-row-content">
                        <div class="inbox-row-top">
                            <span class="inbox-row-sender">
                                <span class="rtag <?= $rtagClass ?>"><?= $rtagLabel ?></span>
                                <?= $senderName ?>
                                <span style="font-weight:500;color:var(--mut);font-size:12px;"> <?= $receiver ?></span>
                            </span>
                            <span class="inbox-row-time"><?= $timeStr ?></span>
                        </div>
                        <div class="inbox-row-subject"><?= Security::escapeHtml($msg['asunto'] ?? '(sin asunto)') ?></div>
                        <div class="inbox-row-preview"><?= Security::escapeHtml(mb_substr($msg['descripcion'] ?? '', 0, 80)) ?>…</div>
                    </div>
                    <?php if ($esNuevo): ?><div class="inbox-new-dot"></div><?php endif; ?>
                </a>
                <?php if ($esNuevo): ?>
                <div class="inbox-visto-form">
                    <form method="POST" action="../../../controladores/secretaria/mensajes/marcar_visto.php">
                        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                        <input type="hidden" name="idReclamacion" value="<?= (int)$msg['idReclamacion'] ?>">
                        <button type="submit" name="marcarVisto" class="inbox-visto-btn">
                            <i class="fas fa-check"></i> Visto
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script src="../../../public/js/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/mensajes.js') ?>"></script>
