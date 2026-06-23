<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/FeatureGuard.php";
FeatureGuard::requirePage('feature_mensajes');

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$idEstudiante = (int)$_SESSION['idEstudiante'];
$todos        = listarMensajesDeEstudiante($idEstudiante);
$totalSinLeer = contarMensajesNoLeidosEstudiante($idEstudiante);

$folder  = $_GET['folder'] ?? 'todo';
$allowed = ['todo', 'nuevos', 'recibidos', 'enviados'];
if (!in_array($folder, $allowed)) $folder = 'todo';

$lista = array_filter($todos, function($m) use ($folder) {
    switch ($folder) {
        case 'nuevos':   return !$m['leido'] && $m['emisor_rol'] !== 'estudiante';
        case 'recibidos': return $m['emisor_rol'] !== 'estudiante';
        case 'enviados': return $m['emisor_rol'] === 'estudiante';
        default:         return true;
    }
});
$lista = array_values($lista);

$cTodo     = count($todos);
$cNuevos   = (int)$totalSinLeer;
$cRecibidos= count(array_filter($todos, fn($m) => $m['emisor_rol'] !== 'estudiante'));
$cEnviados = count(array_filter($todos, fn($m) => $m['emisor_rol'] === 'estudiante'));

$tituloDelPagina = "AULAPRO | Mis Mensajes";
$seccionActual   = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">
<script>window.MENSAJES_POLL_URL='../../../controladores/ajax/mensajes_polling.php';</script>

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

<section class="hero" style="margin-bottom:var(--gap);">
    <div class="hero-text">
        <p class="eyebrow">Mensajería</p>
        <h1>Mis Mensajes<?php if ($totalSinLeer > 0): ?> <span>(<?= $totalSinLeer ?> nuevos)</span><?php endif; ?></h1>
        <p class="sub">Consultas enviadas a <b>profesores</b> y a la <b>dirección</b></p>
    </div>
    <div class="hero-stats">
        <div class="stat"><span class="stat-k">Total</span><span class="stat-v"><?= $cTodo ?></span></div>
        <div class="stat"><span class="stat-k">Sin leer</span><span class="stat-v" style="color:<?= $totalSinLeer > 0 ? 'var(--accent)' : 'inherit' ?>"><?= $totalSinLeer ?></span></div>
    </div>
</section>

<div class="inbox-wrap" data-mensajes-page="1">
    <!-- Folders -->
    <div class="inbox-folders">
        <a href="agregar.php" class="inbox-compose-btn"><i class="fas fa-pen"></i> Nuevo Mensaje</a>

        <a href="?folder=todo" class="inbox-folder <?= $folder === 'todo' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-inbox"></i></span>
            <span class="inbox-folder-label">Todos</span>
            <?php if ($cTodo): ?><span class="inbox-folder-count"><?= $cTodo ?></span><?php endif; ?>
        </a>
        <a href="?folder=nuevos" class="inbox-folder <?= $folder === 'nuevos' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-circle-dot"></i></span>
            <span class="inbox-folder-label">No leídos</span>
            <?php if ($cNuevos): ?><span class="inbox-folder-count"><?= $cNuevos ?></span><?php endif; ?>
        </a>
        <a href="?folder=recibidos" class="inbox-folder <?= $folder === 'recibidos' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-inbox"></i></span>
            <span class="inbox-folder-label">Recibidos</span>
            <?php if ($cRecibidos): ?><span class="inbox-folder-count"><?= $cRecibidos ?></span><?php endif; ?>
        </a>
        <a href="?folder=enviados" class="inbox-folder <?= $folder === 'enviados' ? 'active' : '' ?>">
            <span class="inbox-folder-ico"><i class="fas fa-paper-plane"></i></span>
            <span class="inbox-folder-label">Enviados</span>
            <?php if ($cEnviados): ?><span class="inbox-folder-count"><?= $cEnviados ?></span><?php endif; ?>
        </a>
    </div>

    <!-- List -->
    <div class="inbox-main">
        <div class="inbox-toolbar">
            <span class="inbox-toolbar-title"><?= ['todo'=>'Todos','nuevos'=>'No leídos','recibidos'=>'Recibidos','enviados'=>'Enviados'][$folder] ?? 'Mensajes' ?></span>
            <span class="inbox-toolbar-count"><?= count($lista) ?> mensaje(s)</span>
        </div>

        <?php if (empty($lista)): ?>
        <div class="inbox-empty">
            <div class="inbox-empty-ico"><i class="fas fa-inbox"></i></div>
            <p>No hay mensajes en esta carpeta.</p>
        </div>
        <?php else: ?>
        <?php foreach ($lista as $msg):
            $esRecibido = $msg['emisor_rol'] !== 'estudiante';
            $esNuevo    = !$msg['leido'] && $esRecibido;
            $rowClass   = $esNuevo ? 'inbox-unread' : (!$esRecibido ? 'inbox-sent' : '');

            if ($msg['emisor_rol'] === 'estudiante') {
                // Enviado por el alumno
                $senderName = 'Tú';
                $avaClass   = 'inbox-ava-yo';
                $avaInit    = 'YO';
                $toStr = !empty($msg['nombreProfesor'])
                    ? '→ <span class="rtag rtag-profe">Profe</span> '.Security::escapeHtml($msg['nombreProfesor'])
                    : '→ <span class="rtag rtag-admin">Admin</span> Dirección';
            } elseif ($msg['emisor_rol'] === 'profesor') {
                $senderName = Security::escapeHtml($msg['nombreProfesor'] ?? 'Profesor');
                $avaClass   = 'inbox-ava-profe';
                $avaInit    = mb_strtoupper(mb_substr($msg['nombreProfesor'] ?? 'P', 0, 2));
                $toStr      = '';
            } else {
                $senderName = 'Administración';
                $avaClass   = 'inbox-ava-admin';
                $avaInit    = 'AD';
                $toStr      = '';
            }

            $ts      = strtotime($msg['fecha']);
            $timeStr = (date('Y-m-d', $ts) === date('Y-m-d')) ? date('H:i', $ts) : date('d/m/Y', $ts);
        ?>
        <a href="detalles.php?id=<?= (int)$msg['idReclamacion'] ?>" class="inbox-row <?= $rowClass ?>">
            <div class="inbox-ava <?= $avaClass ?>"><?= Security::escapeHtml($avaInit) ?></div>
            <div class="inbox-row-content">
                <div class="inbox-row-top">
                    <span class="inbox-row-sender">
                        <?= $senderName ?>
                        <?php if ($toStr): ?><span style="font-weight:500;color:var(--mut);font-size:12px;"> <?= $toStr ?></span><?php endif; ?>
                    </span>
                    <span class="inbox-row-time"><?= $timeStr ?></span>
                </div>
                <div class="inbox-row-subject"><?= Security::escapeHtml($msg['asunto']) ?></div>
                <div class="inbox-row-preview"><?= Security::escapeHtml(mb_substr($msg['descripcion'], 0, 80)) ?>…</div>
            </div>
            <?php if ($esNuevo): ?><div class="inbox-new-dot"></div><?php endif; ?>
        </a>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script src="../../../public/js/mensajes.js?v=<?= @filemtime(__DIR__.'/../../../public/js/mensajes.js') ?>"></script>
