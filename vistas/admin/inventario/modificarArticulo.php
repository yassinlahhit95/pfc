<?php
session_start();
require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = $_GET['idArticulo'] ?? 0;
$articulo = obtenerArticuloPorId(intval($idArticulo));

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_inventario']);

$titulo_pagina = "AULAPRO | MODIFICAR ARTÍCULO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR ARTÍCULO: <?= $articulo['nombreArticulo'] ?></h1>
    <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/inventario/actualizar.php" method="POST">
        <input type="hidden" name="idArticulo" value="<?= $articulo['idArticulo'] ?>">

        <div class="formulario">
            <div class="campo">
                <label for="nombreArticulo">Nombre del Artículo *</label>
                <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? $articulo['nombreArticulo'] ?>">
                <?php if (isset($errores['nombreArticulo'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreArticulo'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="numeroSerie">Número de Serie *</label>
                <input type="text" name="numeroSerie" id="numeroSerie" value="<?= $datos['numeroSerie'] ?? $articulo['numeroSerie'] ?>">
                <?php if (isset($errores['numeroSerie'])) { ?>
                    <strong class="error-campo"><?= $errores['numeroSerie'] ?></b>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarArticulo" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
