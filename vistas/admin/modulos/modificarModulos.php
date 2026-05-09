<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_modulo = $_GET['idModulo'] ?? 0;
$modulo = obtenerModuloPorId(intval($id_del_modulo));

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

if (!empty($datos)) {
    $modulo = array_merge($modulo, $datos);
}

$titulo_pagina = "AULAPRO | MODIFICAR MóDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Módulo: <?= $modulo['nombreModulo'] ?></h1>
    <a href="verModulos.php" class="boton-secundario">â† Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?= $id_del_modulo ?>">

        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreModulo">Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" id="nombreModulo" value="<?= $modulo['nombreModulo'] ?>">
                <?php if (isset($errores['nombreModulo'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreModulo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select name="idCiclo" id="idCiclo">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" <?= ($modulo['idCiclo'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="horasMaximas">Horas Totales *</label>
                <input type="text" name="horasMaximas" id="horasMaximas" value="<?= $modulo['horasMaximas'] ?>">
                <?php if (isset($errores['horasMaximas'])) { ?>
                    <strong class="error-campo"><?= $errores['horasMaximas'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




