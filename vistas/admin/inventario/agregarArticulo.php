<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . '/../../../include/FeatureGuard.php';
FeatureGuard::requirePage('feature_inventario');

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_inventario'] ?? [];

$titulo_pagina = "AULAPRO | AÑADIR NUEVO ARTÍCULO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="contenedor-formulario-pequeno">
    <div class="cabecera">
        <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
        <h1>NUEVO ARTÍCULO</h1>
    </div>


    <div class="panel">
        <form method="POST" action="../../../controladores/admin/inventario/insertar.php">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
            <div class="formulario">
                <div class="campo">
                    <label for="nombreArticulo">NOMBRE DEL ARTÍCULO</label>
                    <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= Security::escapeHtml($datos['nombreArticulo'] ?? '') ?>" placeholder="Ej: Portátil HP ProBook">
                    
                </div>

                <div class="campo">
                    <label for="numeroSerie">NÚMERO DE SERIE</label>
                    <input type="text" name="numeroSerie" id="numeroSerie" value="<?= Security::escapeHtml($datos['numeroSerie'] ?? '') ?>" placeholder="Ej: SN-12345678">
                    
                </div>
            </div>

            <div class="acciones">
                <input type="submit" name="guardarArticulo" class="boton-primario" value="GUARDAR ARTÍCULO">
                <input type="reset" class="boton-secundario" value="LIMPIAR">
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
