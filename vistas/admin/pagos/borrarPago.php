<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
$id = (int)($_GET['id'] ?? 0);
require_once __DIR__ . '/../../../modelos/pagos.php';
require_once __DIR__ . '/../../../modelos/estudiantes.php';
$pago = obtenerPagoPorId($id);
$estudiante = obtenerEstudiantePorId($pago['idEstudiante'] ?? 0);
$titulo_pagina = 'AULAPRO | CONFIRMAR';
$seccion = '';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIRMAR ELIMINACIÓN</h1>
</div>

<div class="panel" style="max-width:500px;">
    <p>Quieres eliminar el pago de "<?= Security::escapeHtml($estudiante['nombreEstudiante'] ?? '') ?>"!</p>
    <div class="acciones" style="margin-top:20px;">
        <form method="POST" action="../../../controladores/admin/pagos/borrar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idPago" value="<?= (int)$id ?>">
            <button type="submit" class="boton-primario" style="background:#f87171;border-color:#f87171;min-width:160px;">Sí, eliminar</button>
        </form>
        <a href="verPagosGeneral.php" class="boton-secundario" style="min-width:160px;">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
