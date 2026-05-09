<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/aulas.php";

$id = $_GET['idAula'] ?? '';
$aula = obtenerAulaPorId($id);

if (!$aula) {
    header("Location: verAulas.php");
    exit;
}

// Si hay datos de un intento previo fallido, los usamos
$datosForm = $_SESSION['datos_aulas'] ?? [];
if (!empty($datosForm)) {
    foreach ($datosForm as $k => $v) {
        $aula[$k] = $v;
    }
}

$error = $_SESSION['error'] ?? '';
$errs = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);

$titulo_pagina = "AULAPRO | MODIFICAR AULA";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR AULA</h1>
    <a href="verAulas.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/aulas/actualizar.php" class="form-estandar">
        <input type="hidden" name="idAula" value="<?= $id ?>">
        
        <div class="campo-formulario">
            <label for="nombreAula">Nombre del Aula</label>
            <input type="text" id="nombreAula" name="nombreAula" value="<?= $aula['nombreAula'] ?>" class="<?= isset($errs['nombreAula']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreAula'])) { ?>
                <strong class="error-campo"><?= $errs['nombreAula'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarAula" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


