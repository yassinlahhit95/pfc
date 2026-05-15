<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($todos_los_ciclos as $c) {
        if ($c['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $c['idNivel'];
            break;
        }
    }
}

$titulo_pagina = "AULAPRO | REGISTRAR MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>REGISTRAR NUEVO MÓDULO</h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="form-estandar">

        <div class="campo-formulario">
            <label for="nombreModulo">Nombre del Módulo *</label>
            <input type="text" name="nombreModulo" id="nombreModulo" value="<?= $datos['nombreModulo'] ?? '' ?>">
            <?php if (isset($errores['nombreModulo'])) { ?>
                <strong class="error-campo"><?= $errores['nombreModulo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="idCiclo">Ciclo Formativo Asociado *</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">-- Selecciona un ciclo --</option>
                <optgroup label="Grado Medio">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <?php if ($ciclo['idNivel'] == 1) { ?>
                            <option value="<?= $ciclo['idCiclo'] ?>" <?php if (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                                <?= $ciclo['nombreCiclo'] ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </optgroup>
                <optgroup label="Grado Superior">
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <?php if ($ciclo['idNivel'] == 2) { ?>
                            <option value="<?= $ciclo['idCiclo'] ?>" <?php if (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                                <?= $ciclo['nombreCiclo'] ?>
                            </option>
                        <?php } ?>
                    <?php } ?>
                </optgroup>
            </select>
            <?php if (isset($errores['idCiclo'])) { ?>
                <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="curso">Nivel Formativo *</label>
            <select name="curso" id="curso">
                <option value="1" <?php if (isset($datos['curso']) && $datos['curso'] == 1) { echo 'selected'; } ?>>1º Curso</option>
                <option value="2" <?php if (isset($datos['curso']) && $datos['curso'] == 2) { echo 'selected'; } ?>>2º Curso</option>
            </select>
        </div>

        <div class="campo-formulario">
            <label for="horasMaximas">Horas Máximas *</label>
            <input type="text" name="horasMaximas" id="horasMaximas" value="<?= $datos['horasMaximas'] ?? '' ?>">
            <?php if (isset($errores['horasMaximas'])) { ?>
                <strong class="error-campo"><?= $errores['horasMaximas'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR MÓDULO
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
