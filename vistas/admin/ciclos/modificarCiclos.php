<?php
session_start();
$titulo_pagina = "Modificar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../../modelos/ciclos.php";

$id_ciclo = $_GET['idCiclo'];
$ciclo = obtenerCicloPorId($id_ciclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

if (isset($_SESSION['datos_ciclos'])) {
    $ciclo = $_SESSION['datos_ciclos'];
}

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_ciclos']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Ciclo</h1>
    <a href="verCiclos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" value="<?php echo $ciclo['nombreCiclo']; ?>">
                <?php if (isset($lista_de_errores['nombreCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreCiclo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Grado *</label>
                <select name="gradoCiclo">
                    <option value="Medio" <?php if($ciclo['gradoCiclo'] == 'Medio') { echo "selected"; } ?>>Grado Medio</option>
                    <option value="Superior" <?php if($ciclo['gradoCiclo'] == 'Superior') { echo "selected"; } ?>>Grado Superior</option>
                </select>
                <?php if (isset($lista_de_errores['gradoCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['gradoCiclo']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
