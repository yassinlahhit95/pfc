<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = $_GET['idArticulo'] ?? 0;
$articulo = obtenerArticuloPorId(intval($idArticulo));

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$titulo_pagina = "Modificar Artículo - Admin";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Artículo: <?= $articulo['nombreArticulo'] ?></h1>
    <a href="verInventario.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/inventario/actualizar.php" method="POST">
        <input type="hidden" name="idArticulo" value="<?= $articulo['idArticulo'] ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" value="<?= $articulo['nombreArticulo'] ?>">
            </div>

            <div class="campo-formulario">
                <label>Número de Serie *</label>
                <input type="text" name="numeroSerie" value="<?= $articulo['numeroSerie'] ?>">
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible separacion-media">
            <button type="submit" name="actualizarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <button type="reset" class="boton-secundario">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


