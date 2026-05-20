<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";

$todos_los_modulos = listarModulos();

$datos = $_SESSION['datos_reto'] ?? [];

$titulo_pagina = "AULAPRO | NUEVO RETO";
$seccion = 'retos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>CREAR NUEVO RETO</h1>
    <a href="verRetos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>
<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/retos/insertar.php" method="POST" class="formulario">
        <div class="campo">
                <label for="nombreReto">Nombre del Reto</label>
                <input type="text" name="nombreReto" id="nombreReto" value="<?= $datos['nombreReto'] ?? '' ?>">
                <?php if (isset($errores['nombreReto'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="horasReto">Horas Totales Estimadas</label>
                <input type="number" name="horasReto" id="horasReto" value="<?= $datos['horasReto'] ?? '' ?>">
                <?php if (isset($errores['horasReto'])) { ?>
                    <strong class="error-campo"><?= $errores['horasReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaInicioReto">Fecha de Inicio</label>
                <input type="date" name="fechaInicioReto" id="fechaInicioReto" min="<?= date('Y-m-d') ?>" value="<?= $datos['fechaInicioReto'] ?? '' ?>">
                <?php if (isset($errores['fechaInicioReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaInicioReto'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaFinReto">Fecha de Fin</label>
                <input type="date" name="fechaFinReto" id="fechaFinReto" min="<?= date('Y-m-d') ?>" value="<?= $datos['fechaFinReto'] ?? '' ?>">
                <?php if (isset($errores['fechaFinReto'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaFinReto'] ?></strong>
                <?php } ?>
            </div>

        <div class="campo">
            <label for="modulosReto">Módulo Asociado</label>
            <select name="modulosReto" id="modulosReto">
                <option value="">-- Selecciona un módulo --</option>
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <option value="<?= $modulo['idModulo'] ?>" <?= ($datos['modulosReto'] ?? '') == $modulo['idModulo'] ? 'selected' : '' ?>>
                        <?= $modulo['nombreModulo'] ?> (<?= $modulo['nombreCiclo'] ?>)
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['modulosReto'])) { ?>
                <strong class="error-campo"><?= $errores['modulosReto'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarReto" class="boton-primario" value="CREAR RETO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
