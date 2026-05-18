<?php
session_start();
require_once __DIR__ . "/../../../modelos/anuncios.php";

$id_anuncio = $_GET['idAnuncio'] ?? '';
$anuncio = obtenerAnuncioPorId($id_anuncio);

if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

if (isset($_SESSION['datos_anuncio'])) {
    $anuncio = array_merge($anuncio, $_SESSION['datos_anuncio']);
}

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);

$titulo_pagina = "AULAPRO | MODIFICAR ANUNCIO";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR ANUNCIO</h1>
    <a href="gestionAnuncios.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="idAnuncio" value="<?= $id_anuncio ?>">
        
        <div class="campo">
            <label for="tituloAnuncio">Título del Anuncio *</label>
            <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= $anuncio['tituloAnuncio'] ?>">
            <?php if (isset($errores['tituloAnuncio'])) { ?>
                <strong class="error-campo"><?= $errores['tituloAnuncio'] ?></b>
            <?php } ?>
        </div>

        <div class="campo margen-arriba">
            <label for="contenidoAnuncio">Contenido del Anuncio *</label>
            <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6"><?= $anuncio['contenidoAnuncio'] ?></textarea>
            <?php if (isset($errores['contenidoAnuncio'])) { ?>
                <strong class="error-campo"><?= $errores['contenidoAnuncio'] ?></b>
            <?php } ?>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarAnuncio" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

