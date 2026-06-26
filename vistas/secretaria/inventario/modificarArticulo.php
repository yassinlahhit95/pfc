<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
require_once __DIR__ . "/../../../include/form_helpers.php";
FeatureGuard::requirePage('feature_inventario');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/inventario.php";

$idArticulo = (int)($_GET['idArticulo'] ?? 0);
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
    <h1>MODIFICAR ARTÍCULO: <?= Security::escapeHtml($articulo['nombreArticulo']) ?></h1>
    <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/inventario/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idArticulo" value="<?= Security::escapeHtml($articulo['idArticulo']) ?>">

        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreArticulo') ?>">
                <label for="nombreArticulo">Nombre del Artículo</label>
                <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= Security::escapeHtml($datos['nombreArticulo'] ?? $articulo['nombreArticulo']) ?>">
                <?= fieldError($errores, 'nombreArticulo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'numeroSerie') ?>">
                <label for="numeroSerie">Número de Serie</label>
                <input type="text" name="numeroSerie" id="numeroSerie" value="<?= Security::escapeHtml($datos['numeroSerie'] ?? $articulo['numeroSerie']) ?>">
                <?= fieldError($errores, 'numeroSerie') ?>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarArticulo" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
