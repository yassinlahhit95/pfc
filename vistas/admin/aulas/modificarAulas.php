<?php
session_start();
$titulo_pagina = "Modificar Aula - Super Admin";
$seccion = 'aulas';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/aulas.php";

$id_aula = $_GET['idAula'];
$la_aula = obtenerAulaPorId($id_aula);

if (!$la_aula) {
    header("Location: verAulas.php");
    exit;
}

if (isset($_SESSION['datos_aulas'])) {
    $la_aula = $_SESSION['datos_aulas'];
}

$error = $_SESSION['error'] ?? "";

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_aulas']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Aula</h1>
    <a href="verAulas.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/aulas/actualizar.php">
        <input type="hidden" name="idAula" value="<?php echo $id_aula; ?>">
        <div class="campo-formulario">
            <label>Nombre del Aula</label>
            <input type="text" name="nombreAula" value="<?php echo $la_aula['nombreAula']; ?>">
            <?php if (isset($lista_de_errores['nombreAula'])) { ?>
                <span class="error-campo"><?php echo $lista_de_errores['nombreAula']; ?></span>
            <?php } ?>
        </div>
        <div class="margen-arriba">
            <button type="submit" name="actualizarAula" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

