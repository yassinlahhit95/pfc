<?php
require_once __DIR__ . "/../../../include/Security.php";

if (empty($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

$id = (int)($_GET['id'] ?? 0);
require_once __DIR__ . '/../../../modelos/reclamaciones.php';
$registro = obtenerMensajePorId($id);

// IDOR: sólo puede borrar mensajes de este profesor
if (!$registro || (int)$registro['idProfesor'] !== (int)$_SESSION['idProfesor']) {
    $_SESSION['errores'] = "Mensaje no encontrado o acceso denegado.";
    header("Location: lista.php");
    exit;
}

$tituloDelPagina = "AULAPRO | Confirmar Eliminación";
$seccionActual   = 'reclamaciones';
include __DIR__ . '/../comunes/nav.php';
?>
<link rel="stylesheet" href="../../../public/css/mensajes.css">

<div style="margin-bottom:var(--gap);">
    <a href="lista.php" class="ibtn ibtn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
</div>

<div class="confirm-card">
    <h2><i class="fas fa-trash-alt" style="color:#ef4444;margin-right:8px;"></i> Eliminar Mensaje</h2>
    <p style="color:var(--dim);font-size:14px;margin-bottom:4px;">¿Estás seguro de que quieres eliminar este mensaje?</p>
    <div class="confirm-preview">
        <i class="fas fa-envelope" style="color:var(--mut);margin-right:8px;"></i>
        <?= Security::escapeHtml($registro['asunto']) ?>
    </div>
    <p class="confirm-sub">Esta acción es permanente y no se puede deshacer.</p>
    <div class="confirm-actions">
        <form method="POST" action="../../../controladores/profesores/mensajes/borrar.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idReclamacion" value="<?= $id ?>">
            <button type="submit" class="ibtn ibtn-danger">
                <i class="fas fa-trash"></i> Sí, eliminar
            </button>
        </form>
        <a href="lista.php" class="ibtn ibtn-secondary">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
