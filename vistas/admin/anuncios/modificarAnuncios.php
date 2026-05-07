<?php
session_start();
$titulo_pagina = "Modificar Anuncio - Admin";
$seccion = 'anuncios';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/anuncios.php";

$id_anuncio = $_GET['idAnuncio'];
$anuncio = obtenerAnuncioPorId($id_anuncio);

if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

$anuncio = ($_SESSION['datos_anuncio'] ?? 0);

$error = $_SESSION['error'] ?? "";

$lista_de_errores = [];
$lista_de_errores = ($_SESSION['errores'] ?? 0);

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Anuncio</h1>
    <a href="gestionAnuncios.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="idAnuncio" value="<?= $id_anuncio ?>">
        
        <div class="campo-formulario">
            <label for="tituloAnuncio">Título del Anuncio *</label>
            <input type="text" id="tituloAnuncio" name="tituloAnuncio" value="<?= $anuncio['tituloAnuncio'] ?>">
            <?php if (isset($lista_de_errores['tituloAnuncio'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['tituloAnuncio'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario margen-arriba">
            <label for="contenidoAnuncio">Contenido del Anuncio *</label>
            <textarea id="contenidoAnuncio" name="contenidoAnuncio" rows="6"><?= $anuncio['contenidoAnuncio'] ?></textarea>
            <?php if (isset($lista_de_errores['contenidoAnuncio'])) { ?>
                <strong class="error-campo"><?= $lista_de_errores['contenidoAnuncio'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
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




