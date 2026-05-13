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

$nivelActual = '';
foreach ($todos_los_ciclos as $c) {
    if ($c['idCiclo'] == $modulo['idCiclo']) {
        $nivelActual = $c['idNivel'];
        break;
    }
}

$titulo_pagina = "AULAPRO | MODIFICAR MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR MÓDULO: <?= $modulo['nombreModulo'] ?></h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
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
                <label>Grado Formativo *</label>
                <select id="filtroNivel" onchange="alCambiarNivel()">
                    <option value="">-- Selecciona un grado --</option>
                    <option value="1" <?php if ($nivelActual == 1) { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="2" <?php if ($nivelActual == 2) { echo 'selected'; } ?>>Grado Superior</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select name="idCiclo" id="idCiclo" <?php if (!$nivelActual) { echo 'disabled'; } ?>>
                    <option value="">-- Selecciona un ciclo --</option>
                    <?php foreach ($todos_los_ciclos as $ciclo) { ?>
                        <option value="<?= $ciclo['idCiclo'] ?>" data-nivel="<?= $ciclo['idNivel'] ?>"
                            <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } ?>
                            <?php if ($nivelActual !== '' && $ciclo['idNivel'] != $nivelActual) { echo 'style="display: none"'; } ?>>
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
                    <option value="1" <?php if ($modulo['curso'] == 1) { echo 'selected'; } ?>>1º Curso</option>
                    <option value="2" <?php if ($modulo['curso'] == 2) { echo 'selected'; } ?>>2º Curso</option>
                </select>
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

<script>
function alCambiarNivel() {
    var idNivel = document.getElementById('filtroNivel').value;
    var selectCiclo = document.getElementById('idCiclo');

    if (idNivel === '') {
        selectCiclo.value = '';
        selectCiclo.disabled = true;
        selectCiclo.options[0].textContent = '-- Primero selecciona un nivel --';
        var opciones = selectCiclo.querySelectorAll('option');
        opciones.forEach(function(opcion) { opcion.style.display = ''; });
        return;
    }

    var opciones = selectCiclo.querySelectorAll('option');
    opciones.forEach(function(opcion) {
        if (opcion.value === '') {
            opcion.style.display = '';
            return;
        }
        if (opcion.getAttribute('data-nivel') === idNivel) {
            opcion.style.display = '';
        } else {
            opcion.style.display = 'none';
        }
    });

    selectCiclo.value = '';
    selectCiclo.options[0].textContent = '-- Selecciona un ciclo --';
    selectCiclo.disabled = false;
}
</script>

<?php include '../comunes/footer.php'; ?>
