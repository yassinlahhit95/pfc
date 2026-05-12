<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

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
                <option value="">Seleccione un ciclo</option>
                <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                    <option value="<?= $ciclo['idCiclo'] ?>" <?= (isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                        <?= $ciclo['nombreCiclo'] ?>
                    </option>
                <?php } ?>
            </select>
            <?php if (isset($errores['idCiclo'])) { ?>
                <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="curso">Nivel Formativo *</label>
            <select name="curso" id="curso">
                <option value="1" <?= (isset($datos['curso']) && $datos['curso'] == 1) ? 'selected' : '' ?>>Grado Medio</option>
                <option value="2" <?= (isset($datos['curso']) && $datos['curso'] == 2) ? 'selected' : '' ?>>Grado Superior</option>
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




