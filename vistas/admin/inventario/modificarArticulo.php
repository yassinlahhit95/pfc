<?php
session_start();

if (!isset($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = $_GET['idArticulo'] ?? 0;
$articulo = obtenerArticuloPorId(intval($idArticulo));

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$titulo_pagina = "AULAPRO | MODIFICAR ARTÍCULO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR ARTÍCULO: <?= $articulo['nombreArticulo'] ?></h1>
    <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/inventario/actualizar.php" method="POST">
        <input type="hidden" name="idArticulo" value="<?= $articulo['idArticulo'] ?>">

        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreArticulo">Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= $articulo['nombreArticulo'] ?>">
            </div>

            <div class="campo-formulario">
                <label for="numeroSerie">Número de Serie *</label>
                <input type="text" name="numeroSerie" id="numeroSerie" value="<?= $articulo['numeroSerie'] ?>">
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




