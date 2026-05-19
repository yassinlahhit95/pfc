<?php
session_start();

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_inventario']);

$titulo_pagina = "AULAPRO | AÑADIR NUEVO ARTÍCULO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="contenedor-formulario-pequeno">
    <div class="cabecera">
        <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
        <h1>NUEVO ARTÍCULO</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="panel">
        <form method="POST" action="../../../controladores/admin/inventario/insertar.php">
            <div class="formulario">
                <div class="campo">
                    <label for="nombreArticulo">NOMBRE DEL ARTÍCULO</label>
                    <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? '' ?>" placeholder="Ej: Portátil HP ProBook">
                    <?php if (isset($errores['nombreArticulo'])) { ?>
                        <strong class="error-campo"><?= $errores['nombreArticulo'] ?></strong>
                    <?php } ?>
                </div>

                <div class="campo">
                    <label for="numeroSerie">NÚMERO DE SERIE</label>
                    <input type="text" name="numeroSerie" id="numeroSerie" value="<?= $datos['numeroSerie'] ?? '' ?>" placeholder="Ej: SN-12345678">
                    <?php if (isset($errores['numeroSerie'])) { ?>
                        <strong class="error-campo"><?= $errores['numeroSerie'] ?></strong>
                    <?php } ?>
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
