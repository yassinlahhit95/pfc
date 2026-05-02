<?php
session_start();
$titulo_pagina = "Modificar Aula - Admin";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/aulas.php";

$id_aula = $_GET['idAula'] ?? '';
$la_aula = obtenerAulaPorId($id_aula);

if (!$la_aula) {
    header("Location: verAulas.php");
    exit;
}

$la_aula = ($_SESSION['datos_aulas'] ?? 0);

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Aula</h1>
    <a href="verAulas.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/aulas/actualizar.php">
        <input type="hidden" name="idAula" value="<?= $id_aula ?>">
        <div class="campo-formulario">
            <label>Nombre del Aula</label>
            <input type="text" name="nombreAula" value="<?= $la_aula['nombreAula'] ?? '' ?>">
            <?php if (isset($lista_de_errores['nombreAula'])) { ?>
                <span class="error-campo"><?= $lista_de_errores['nombreAula'] ?></span>
            <?php } ?>
        </div>
        <div class="margen-arriba disposicion-flexible separacion-media">
            <button type="submit" name="actualizarAula" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <button type="reset" class="boton-secundario">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


