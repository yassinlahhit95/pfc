<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AULAPRO | AÑADIR NUEVO ARTÍCULO";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="contenedor-formulario-pequeno">
    <div class="encabezado-pagina">
        <a href="verInventario.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
        <h1>NUEVO ARTÍCULO</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="tarjeta-blanca">
        <form method="POST" action="../../../controladores/admin/inventario/insertar.php">
            <div class="form-estandar">
                <div class="campo-formulario">
                    <label for="nombreArticulo">NOMBRE DEL ARTÍCULO *</label>
                    <input type="text" name="nombreArticulo" id="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? '' ?>" placeholder="Ej: Portátil HP ProBook">
                    <?php if (isset($errores['nombreArticulo'])) { ?>
                        <strong class="error-campo"><?= $errores['nombreArticulo'] ?></strong>
                    <?php } ?>
                </div>

                <div class="campo-formulario">
                    <label for="numeroSerie">NÚMERO DE SERIE *</label>
                    <input type="text" name="numeroSerie" id="numeroSerie" value="<?= $datos['numeroSerie'] ?? '' ?>" placeholder="Ej: SN-12345678">
                    <?php if (isset($errores['numeroSerie'])) { ?>
                        <strong class="error-campo"><?= $errores['numeroSerie'] ?></strong>
                    <?php } ?>
                </div>
            </div>

            <div class="form-acciones">
                <button type="submit" name="guardarArticulo" class="boton-primario">
                    <i class="fas fa-save"></i> GUARDAR ARTÍCULO
                </button>
                <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
                <button type="button" class="boton-secundario" onclick="window.location.href = 'verInventario.php';">
                    <i class="fas fa-times"></i> CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>


