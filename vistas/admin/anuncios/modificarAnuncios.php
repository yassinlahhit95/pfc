<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/anuncios.php";

$id_anuncio = $_GET['idAnuncio'] ?? '';
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

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores) ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idAnuncio" value="<?= $id_anuncio ?>">
        
        <div class="campo">
            <label for="tituloAnuncio">Título del Anuncio </label>
            <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= $anuncio['tituloAnuncio'] ?>">
            
        </div>

        <div class="campo margen-arriba">
            <label for="contenidoAnuncio">Contenido del Anuncio </label>
            <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6"><?= $anuncio['contenidoAnuncio'] ?></textarea>
            
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarAnuncio" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

