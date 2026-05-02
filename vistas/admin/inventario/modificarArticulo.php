<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = isset($_GET['idArticulo']) ? intval($_GET['idArticulo']) : 0;
$articulo = obtenerArticuloPorId($idArticulo);

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$titulo_pagina = "Modificar ArtÃ­culo - Super Admin";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar ArtÃ­culo: <?php echo $articulo['nombreArticulo']; ?></h1>
    <a href="verInventario.php" class="boton-secundario">â† Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/inventario/actualizar.php" method="POST">
        <input type="hidden" name="idArticulo" value="<?php echo $articulo['idArticulo']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del ArtÃ­culo *</label>
                <input type="text" name="nombreArticulo" value="<?php echo $articulo['nombreArticulo']; ?>">
            </div>

            <div class="campo-formulario">
                <label>NÃºmero de Serie *</label>
                <input type="text" name="numeroSerie" value="<?php echo $articulo['numeroSerie']; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

