<?php
require_once __DIR__ . "/../../../include/Security.php";
if (empty($_SESSION['idAdmin'])) { header("Location: ../../login.php"); exit; }

require_once __DIR__ . "/../../../include/Security.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$idAula = (int)($_GET['id'] ?? 0);
$aula = obtenerAulaPorId($idAula);
if (!$aula) {
    $_SESSION['errores'] = "Aula no encontrada.";
    header("Location: gestionAulas.php");
    exit;
}

$titulo_pagina = "AULAPRO | CONFIRMAR";
$seccion = 'aulas';
include __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CONFIRMAR ELIMINACIÓN</h1>
</div>

<div class="panel" style="max-width:520px;">
    <p>¿Seguro que quieres eliminar el <b>Aula <?= Security::escapeHtml($aula['codigoAula']) ?></b><?= $aula['nombreAula'] ? ' (' . Security::escapeHtml($aula['nombreAula']) . ')' : '' ?>?</p>
    <p class="texto-suave">Las asignaciones del horario que la usen quedarán sin aula (no se borran).</p>
    <div class="acciones" style="margin-top:20px;">
        <form method="POST" action="../../../controladores/admin/aulas/borrar.php">
            <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idAula" value="<?= Security::escapeHtml($aula['idAula']) ?>">
            <button type="submit" class="boton-primario" style="background:#f87171;border-color:#f87171;min-width:160px;">Sí, eliminar</button>
        </form>
        <a href="gestionAulas.php" class="boton-secundario" style="min-width:160px;">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . "/../comunes/footer.php"; ?>
