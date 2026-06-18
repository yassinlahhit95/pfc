<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

require_once __DIR__ . "/../../../modelos/tfg.php";

$idEstudiante = (int)($_GET['id'] ?? 0);
$datosTFG = obtenerTFGporEstudiante($idEstudiante);

$tituloPagina = "Editar TFG - Portal Profesores";
$seccionActual = 'tfg';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR DATOS TFG</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <form action="../../../controladores/profesores/pfc/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">
        <div class="form-cols">
            <div class="campo">
                <label for="nombreEstudiante">Estudiante</label>
                <input type="text" id="nombreEstudiante" value="<?= Security::escapeHtml($datosTFG['nombreEstudiante'] ?? '') ?>" disabled>
            </div>

            <div class="campo">
                <label for="tituloTFG">Título del TFG</label>
                <input type="text" id="tituloTFG" name="tituloTFG" value="<?= Security::escapeHtml($datosTFG['tituloTFG'] ?? '') ?>">
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarTFG" class="boton-primario" value="ACTUALIZAR TFG">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>



