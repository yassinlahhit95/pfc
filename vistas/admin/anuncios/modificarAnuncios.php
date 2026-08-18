<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_anuncios');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/anuncios.php";

$idAnuncio = (int)($_GET['idAnuncio'] ?? 0);
$anuncio = obtenerAnuncioPorId($idAnuncio);

if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

if (isset($_SESSION['datos_anuncio'])) {
    $anuncio = $_SESSION['datos_anuncio'] + $anuncio;
}
unset($_SESSION['datos_anuncio']);

$titulo_pagina = "Modificar Anuncio";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Modificar Anuncio</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idAnuncio" value="<?= $idAnuncio ?>">

        <div class="formulario">
            <div class="campo ancho-total<?= fieldClass($errores, 'tituloAnuncio') ?>">
                <label for="tituloAnuncio">Título del Anuncio</label>
                <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= Security::escapeHtml($anuncio['tituloAnuncio']) ?>">
                <?= fieldError($errores, 'tituloAnuncio') ?>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'contenidoAnuncio') ?>">
                <label for="contenidoAnuncio">Contenido del Anuncio</label>
                <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6"><?= Security::escapeHtml($anuncio['contenidoAnuncio']) ?></textarea>
                <?= fieldError($errores, 'contenidoAnuncio') ?>
            </div>

            <div class="acciones">
                <input type="submit" name="actualizarAnuncio" class="boton-primario" value="GUARDAR CAMBIOS">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
