<?php
session_start();
if (empty($_SESSION['idProfesor'])) { header("Location: ../../login.php"); exit; }
$id = $_GET['id'] ?? '';
require_once __DIR__ . '/../../../modelos/tfg.php';
$registro = obtenerTFGporEstudiante($id);
$tituloDelPagina = 'AULAPRO | CONFIRMAR';
$seccionActual = '';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>CONFIRMAR ELIMINACIÓN</h1>
</div>

<div class="panel" style="max-width:500px;">
    <p>Quieres eliminar el TFG de "<?= Security::escapeHtml($registro['nombreEstudiante'] ) ?>"!</p>
    <div class="acciones" style="margin-top:20px;">
        <form method="POST" action="../../../controladores/profesores/pfc/borrar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($id ) ?>">
            <button type="submit" class="boton-primario" style="background:#f87171;border-color:#f87171;min-width:160px;">Sí, eliminar</button>
        </form>
        <a href="lista.php" class="boton-secundario" style="min-width:160px;">Cancelar</a>
    </div>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>


