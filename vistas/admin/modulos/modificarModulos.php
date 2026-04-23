<?php
session_start();
require_once "../../../modelos/conectar.php";
require_once "../../../modelos/modulos.php";
require_once "../../../modelos/ciclos.php";

$id_del_modulo = $_GET['idModulo'];
$modulo = obtenerModuloPorId($id_del_modulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

if (isset($_SESSION['datos_modulo'])) {
    $modulo = $_SESSION['datos_modulo'];
}

$todos_los_ciclos = listarTodosLosCiclos();

$mensaje_error = "";
if (isset($_SESSION['error'])) { $mensaje_error = $_SESSION['error']; }

$lista_de_errores = array();
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

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

<?php if ($mensaje_error != "") { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $id_del_modulo; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" value="<?php echo $modulo['nombreModulo']; ?>">
                <?php if (isset($lista_de_errores['nombreModulo'])) { ?>
                    <span class="error-campo"><?php echo $lista_de_errores['nombreModulo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { echo "selected"; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idCiclo'])) { ?>
                    <span class="error-campo"><?php echo $lista_de_errores['idCiclo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasMaximas" value="<?php echo $modulo['horasMaximas']; ?>">
                <?php if (isset($lista_de_errores['horasMaximas'])) { ?>
                    <span class="error-campo"><?php echo $lista_de_errores['horasMaximas']; ?></span>
                <?php } ?>
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
