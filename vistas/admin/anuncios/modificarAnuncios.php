<?php
session_start();
$titulo_pagina = "Modificar Anuncio - Super Admin";
$seccion = 'anuncios';
include_once "../comunes/nav.php";

require_once "../../../modelos/anuncios.php";

$id_anuncio = $_GET['idAnuncio'];
$anuncio = obtenerAnuncioPorId($id_anuncio);

if (!$anuncio) {
    header("Location: gestionAnuncios.php");
    exit;
}

if (isset($_SESSION['datos_anuncio'])) {
    $anuncio = $_SESSION['datos_anuncio'];
}

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_anuncio']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Anuncio</h1>
    <a href="gestionAnuncios.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/anuncios/actualizar.php">
        <input type="hidden" name="idAnuncio" value="<?php echo $id_anuncio; ?>">
        
        <div class="campo-formulario">
            <label>Título del Anuncio *</label>
            <input type="text" name="tituloAnuncio" value="<?php echo $anuncio['tituloAnuncio']; ?>">
            <?php if (isset($lista_de_errores['tituloAnuncio'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['tituloAnuncio']; ?></p>
            <?php } ?>
        </div>

        <div class="campo-formulario margen-arriba">
            <label>Contenido del Anuncio *</label>
            <textarea name="contenidoAnuncio" rows="6"><?php echo $anuncio['contenidoAnuncio']; ?></textarea>
            <?php if (isset($lista_de_errores['contenidoAnuncio'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['contenidoAnuncio']; ?></p>
            <?php } ?>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarAnuncio" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
