<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "AÑADIR NUEVA AULA - ADMIN";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_aulas'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="contenedor-formulario-pequeno">
    <div class="encabezado-pagina">
        <a href="verAulas.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> VOLVER
        </a>
        <h1>NUEVA AULA</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="tarjeta-blanca">
        <form method="POST" action="../../../controladores/admin/aulas/insertar.php">
            <div class="campo-formulario">
                <label>NOMBRE DEL AULA *</label>
                <input type="text" name="nombreAula" value="<?= $datos['nombreAula'] ?? '' ?>" placeholder="Ej: Aula 101, Taller de Informática...">
                <?php if (isset($lista_de_errores['nombreAula'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreAula'] ?></strong>
                <?php } ?>
            </div>

            <div class="botones-formulario mt-20">
                <button type="submit" name="guardarAula" class="boton-primario">
                    <i class="fas fa-save"></i> GUARDAR AULA
                </button>
                <button type="button" class="boton-secundario" onclick="window.location.href = 'verAulas.php';">
                    <i class="fas fa-times"></i> CANCELAR
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

