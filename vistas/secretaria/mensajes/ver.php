<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/reclamaciones.php";

$mensaje = obtenerMensajePorId((int)($_GET['id'] ?? 0));
if (!$mensaje) {
    header("Location: lista.php");
    exit;
}

$titulo_pagina = "AULAPRO | VER MENSAJE";
$seccion = 'reclamaciones';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MENSAJE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <h2 style="margin-bottom:1rem;"><?= Security::escapeHtml($mensaje['asunto'] ?? '(sin asunto)') ?></h2>

    <div class="fila-datos">
        <div class="dato">
            <span class="dato-label">De</span>
            <span class="dato-valor">
                <?php
                $rol = $mensaje['emisor_rol'] ?? 'estudiante';
                if ($rol === 'estudiante') {
                    echo Security::escapeHtml($mensaje['nombreEstudiante'] ?? '—');
                    echo ' <span class="texto-estado azul">Estudiante</span>';
                } else {
                    echo Security::escapeHtml($mensaje['nombreProfesor'] ?? '—');
                    echo ' <span class="texto-estado verde">Profesor</span>';
                }
                ?>
            </span>
        </div>
        <div class="dato">
            <span class="dato-label">Fecha</span>
            <span class="dato-valor"><?= Security::escapeHtml(date('d/m/Y H:i', strtotime($mensaje['fecha']))) ?></span>
        </div>
        <div class="dato">
            <span class="dato-label">Estado</span>
            <span class="dato-valor">
                <?php if ($mensaje['leido']): ?>
                <span class="texto-estado gris">Leído</span>
                <?php else: ?>
                <span class="texto-estado azul">Pendiente</span>
                <?php endif; ?>
            </span>
        </div>
    </div>

    <hr style="margin:1.5rem 0; border:none; border-top:1px solid var(--border);">

    <div style="white-space:pre-wrap; line-height:1.6;"><?= Security::escapeHtml($mensaje['descripcion'] ?? '') ?></div>

    <?php if (!$mensaje['leido']): ?>
    <hr style="margin:1.5rem 0; border:none; border-top:1px solid var(--border);">
    <form method="POST" action="../../../controladores/secretaria/mensajes/marcar_visto.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idReclamacion" value="<?= (int)$mensaje['idReclamacion'] ?>">
        <button type="submit" class="boton-primario">
            <i class="fas fa-check"></i> Marcar como leído
        </button>
    </form>
    <?php endif; ?>
</div>

<?php include '../comunes/footer.php'; ?>
