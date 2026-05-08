<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

$titulo_pagina = "AÑADIR NUEVA AULA - ADMIN";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_aulas'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="contenedor-formulario-pequeno">
    <div class="encabezado-pagina">
        <a href="verAulas.php" class="boton-secundario">← Volver</a>
        <h1>NUEVA AULA</h1>
    </div>

    <?php if ($error) { ?>
        <div class="mensaje-error"><?= $error ?></div>
    <?php } ?>

    <div class="tarjeta-blanca">
        <form method="POST" action="../../../controladores/admin/aulas/insertar.php">
            <div class="campo-formulario">
                <label for="nombreAula">NOMBRE DEL AULA *</label>
                <input type="text" id="nombreAula" name="nombreAula" value="<?= $datos['nombreAula'] ?? '' ?>" placeholder="Ej: Aula 101, Taller de Informática...">
                <?php if (isset($errores['nombreAula'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreAula'] ?></strong>
                <?php } ?>
            </div>

            <div class="form-acciones">
                <button type="submit" name="guardarAula" class="boton-primario">
                    <i class="fas fa-save"></i> GUARDAR AULA
                </button>
                <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                    <i class="fas fa-eraser"></i> LIMPIAR
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../comunes/footer.php'; ?>

