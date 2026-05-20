<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/retos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_reto = $_GET['idReto'] ?? '';
$reto = obtenerRetoPorId($id_reto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulos_del_reto = listarModulosDeReto($id_reto);
$idModuloActual = !empty($modulos_del_reto) ? $modulos_del_reto[0]['idModulo'] : '';

if (isset($_SESSION['datos_reto'])) {
    $reto = $_SESSION['datos_reto'];
    $idModuloActual = $reto['modulosReto'] ?? '';
}

$todos_los_modulos = listarModulos();

$titulo_pagina = "AULAPRO | MODIFICAR RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form method="POST" action="../../../controladores/admin/retos/actualizar.php" class="formulario">
        <input type="hidden" name="idReto" value="<?= $id_reto ?>">

        <div class="campo">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= $reto['nombreReto'] ?>">
                <?php if (isset($errores['nombreReto'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= $reto['horasReto'] ?>">
                <?php if (isset($errores['horasReto'])) { ?>
                    <strong class="error-campo"><?= $errores['horasReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaInicioReto">Fecha de Inicio</label>
                <input type="date" name="fechaInicioReto" id="fechaInicioReto" value="<?= $reto['fechaInicio'] ?>">
                <?php if (isset($errores['fechaInicioReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaInicioReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaFinReto">Fecha de Fin</label>
                <input type="date" name="fechaFinReto" id="fechaFinReto" value="<?= $reto['fechaFin'] ?>">
                <?php if (isset($errores['fechaFinReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaFinReto'] ?></strong>
                <?php } ?>
            </div>

        <div class="campo">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= $idModuloActual == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= $modulo['nombreModulo'] ?> (<?= $modulo['nombreCiclo'] ?>)
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['modulosReto'])) { ?>
                <strong class="error-campo"><?= $errores['modulosReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarReto" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
