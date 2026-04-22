<?php
session_start();
require_once "../../../modelos/conectar.php";
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/ciclos.php";


$idDelModulo = 0;
if (isset($_GET['idModulo'])) {
    $idDelModulo = $_GET['idModulo'];
}

if (!$idDelModulo) {
    header("Location: verModulos.php");
    exit;
}

$modulo = obtenerModuloPorId($idDelModulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$listaDeCiclos = listarTodosLosCiclos();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}
unset($_SESSION['errores']);

$titulo_pagina = "Modificar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Módulo: <?php echo $modulo['nombreModulo']; ?></h1>
    </div>
    <a href="/pfc/vistas/admin/modulos/verModulos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $idDelModulo; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" value="<?php echo $modulo['nombreModulo']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($listaDeCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasMaximas" value="<?php echo $modulo['horasMaximas']; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
