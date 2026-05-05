<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "AÑADIR NUEVO ARTÍCULO - ADMIN";
$seccion = 'inventario';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_inventario'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_inventario']);
?>

<div class="contenedor-formulario-pequeno">
    <div class="encabezado-pagina">
        <a href="verInventario.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> VOLVER
        </a>
        <h1>NUEVO ARTÍCULO</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="tarjeta-blanca">
        <form method="POST" action="../../../controladores/admin/inventario/insertar.php">
            <div class="campo-formulario">
                <label>NOMBRE DEL ARTÍCULO *</label>
                <input type="text" name="nombreArticulo" value="<?= $datos['nombreArticulo'] ?? '' ?>" placeholder="Ej: Portátil HP ProBook">
                <?php if (isset($lista_de_errores['nombreArticulo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreArticulo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>NÚMERO DE SERIE *</label>
                <input type="text" name="numeroSerie" value="<?= $datos['numeroSerie'] ?? '' ?>" placeholder="Ej: SN-12345678">
                <?php if (isset($lista_de_errores['numeroSerie'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['numeroSerie'] ?></strong>
                <?php } ?>
            </div>

            <div class="botones-formulario mt-20">
                <button type="submit" name="guardarArticulo" class="boton-primario">
                    <i class="fas fa-save"></i> GUARDAR ARTÍCULO
                </button>
                <button type="button" class="boton-secundario" onclick="window.location.href = 'verInventario.php';">
                    <i class="fas fa-times"></i> CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>
