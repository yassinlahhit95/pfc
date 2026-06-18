<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_anuncio'] ?? [];

$titulo_pagina = "AULAPRO | PUBLICAR NUEVO ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="contenedor-formulario-mediano">
    <div class="cabecera">
        <h1>NUEVO ANUNCIO</h1>
        <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
    </div>

<?php if (!empty($errores) && !is_array($errores)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.Toast) Toast.show(<?= json_encode($errores) ?>, 'error');
});
</script>
<?php endif; ?>

    <div class="panel">
        <form method="POST" action="../../../controladores/admin/anuncios/insertar.php" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="campo">
                <label for="tituloAnuncio">TÍTULO DEL ANUNCIO</label>
                <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= Security::escapeHtml($datos['tituloAnuncio'] ?? '') ?>" placeholder="Ej: Mantenimiento de la plataforma" class="<?= (is_array($errores) && isset($errores['tituloAnuncio'])) ? 'border-error' : '' ?>">
                <?php if (is_array($errores) && isset($errores['tituloAnuncio'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['tituloAnuncio']) ?></span><?php endif; ?>
            </div>

            <div class="campo">
                <label for="dirigidoA">DIRIGIDO A </label>
                <select id="dirigidoA" name="dirigidoA">
                    <option value="todos" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'todos') ? 'selected' : '' ?>>Todos los usuarios</option>
                    <option value="estudiantes" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'estudiantes') ? 'selected' : '' ?>>Solo Estudiantes</option>
                    <option value="profesores" <?= (isset($datos['dirigidoA']) && $datos['dirigidoA'] == 'profesores') ? 'selected' : '' ?>>Solo Profesores</option>
                </select>
            </div>

            <div class="campo">
                <label for="contenidoAnuncio">CONTENIDO DEL ANUNCIO</label>
                <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6" placeholder="Escriba aquí el mensaje..." class="<?= (is_array($errores) && isset($errores['contenidoAnuncio'])) ? 'border-error' : '' ?>"><?= Security::escapeHtml($datos['contenidoAnuncio'] ?? '') ?></textarea>
                <?php if (is_array($errores) && isset($errores['contenidoAnuncio'])): ?><span class="error-campo"><?= Security::escapeHtml($errores['contenidoAnuncio']) ?></span><?php endif; ?>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarAnuncio" class="boton-primario" value="PUBLICAR ANUNCIO">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </form>
    </div>
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
