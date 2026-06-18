<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/anuncios.php";

$id_anuncio = (int)($_GET['idAnuncio'] ?? 0);
$anuncio = obtenerAnuncioPorId($id_anuncio);

if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

if (isset($_SESSION['datos_anuncio'])) {
    $anuncio = $_SESSION['datos_anuncio'] + $anuncio;
}

$titulo_pagina = "AULAPRO | MODIFICAR ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($errores) || !empty($exito)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($errores) && !is_array($errores)): ?>if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');<?php endif; ?>
    <?php if (!empty($exito)): ?>if (window.Toast) Toast.show(<?= json_encode($exito) ?>, 'success');<?php endif; ?>
});
</script>
<?php endif; ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idAnuncio" value="<?= $id_anuncio ?>">

        <div class="campo">
            <label for="tituloAnuncio">Título del Anuncio</label>
            <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= Security::escapeHtml($anuncio['tituloAnuncio']) ?>" class="<?= (is_array($errores) && isset($errores['tituloAnuncio'])) ? 'border-error' : '' ?>">
            <?php if (is_array($errores) && isset($errores['tituloAnuncio'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['tituloAnuncio']) ?></span><?php endif; ?>
        </div>

        <div class="campo margen-arriba">
            <label for="contenidoAnuncio">Contenido del Anuncio</label>
            <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6" class="<?= (is_array($errores) && isset($errores['contenidoAnuncio'])) ? 'border-error' : '' ?>"><?= Security::escapeHtml($anuncio['contenidoAnuncio']) ?></textarea>
            <?php if (is_array($errores) && isset($errores['contenidoAnuncio'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['contenidoAnuncio']) ?></span><?php endif; ?>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarAnuncio" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
<?php if (is_array($errores) && !empty($errores)): ?>
<script>
(function(){
    var first = document.querySelector('.border-error');
    if (first) { first.focus(); first.scrollIntoView({behavior:'smooth', block:'center'}); }
})();
</script>
<?php endif; ?>

