<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/directores.php";

$id_director = (int)($_GET['idDirector'] ?? 0);
$director = obtenerDirectorPorId($id_director);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

if (isset($_SESSION['datos_director'])) {
    $director = $_SESSION['datos_director'] + $director;
}

$titulo_pagina = "AULAPRO | MODIFICAR DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR DIRECTOR</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
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
    <form action="../../../controladores/admin/directores/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idDirector" value="<?= (int)$id_director ?>">

        <div class="formulario">
            <div class="campo">
                <label for="nombreDirector">Nombre Completo</label>
                <input type="text" id="nombreDirector" name="nombreDirector" value="<?= Security::escapeHtml($director['nombreDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="emailDirector">Email</label>
                <input type="text" id="emailDirector" name="emailDirector" value="<?= Security::escapeHtml($director['emailDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="dniDirector">DNI</label>
                <input type="text" id="dniDirector" name="dniDirector" value="<?= Security::escapeHtml($director['dniDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="telefonoDirector">Teléfono</label>
                <input type="text" id="telefonoDirector" name="telefonoDirector" value="<?= Security::escapeHtml($director['telefonoDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="fechaNacimientoDirector">Fecha de Nacimiento</label>
                <input type="date" id="fechaNacimientoDirector" name="fechaNacimientoDirector" value="<?= Security::escapeHtml($director['fechaNacimientoDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="ciudadDirector">Ciudad</label>
                <input type="text" id="ciudadDirector" name="ciudadDirector" value="<?= Security::escapeHtml($director['ciudadDirector'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="codigoPostalDirector">Código Postal</label>
                <input type="text" id="codigoPostalDirector" name="codigoPostalDirector" value="<?= Security::escapeHtml($director['codigoPostalDirector'] ?? '') ?>">

            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionDirector">Dirección Completa</label>
                <input type="text" id="direccionDirector" name="direccionDirector" value="<?= Security::escapeHtml($director['direccionDirector'] ?? '') ?>">

            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesDirector">Observaciones / Notas Internas</label>
                <textarea id="observacionesDirector" name="observacionesDirector" rows="3"><?= Security::escapeHtml($director['observacionesDirector'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarDirector" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
