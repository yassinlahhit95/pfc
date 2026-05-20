<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = $_GET['idArticulo'] ?? 0;
$articulo = obtenerArticuloPorId($idArticulo);

if (!$articulo) {
    header("Location: verInventario.php");
    exit;
}

$datos = $_SESSION['datos_inventario'] ?? [];

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
                <label for="nombreArticulo">Nombre del Artículo</label>
                <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? $articulo['nombreArticulo'] ?>">
                
            </div>

            <div class="campo">
                <label for="numeroSerie">Número de Serie</label>
                <input type="text" name="numeroSerie" id="numeroSerie" value="<?= $datos['numeroSerie'] ?? $articulo['numeroSerie'] ?>">
                
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarArticulo" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
