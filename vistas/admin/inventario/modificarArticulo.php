<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: /pfc/index.php");
    exit;
}

require_once "../../../modelos/inventario.php";

$idArticulo = isset($_GET['idArticulo']) ? intval($_GET['idArticulo']) : 0;
$articulo = obtenerArticuloPorId($idArticulo);

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$titulo_pagina = "Modificar Artículo - Super Admin";
$seccion = 'inventario';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Artículo: <?php echo $articulo['nombreArticulo']; ?></h1>
    <a href="verInventario.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/inventario/actualizar.php" method="POST">
        <input type="hidden" name="idArticulo" value="<?php echo $articulo['idArticulo']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" value="<?php echo $articulo['nombreArticulo']; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Número de Serie *</label>
                <input type="text" name="numeroSerie" value="<?php echo $articulo['numeroSerie']; ?>" required>
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

