<?php
session_start();
require_once __DIR__ . "/../../../modelos/conectar.php";
require_once __DIR__ . "/../../../modelos/estudiantes.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_estudiante = $_GET['idEstudiante'];
$estudiante = obtenerEstudiantePorId($id_del_estudiante);

if (!$estudiante) {
    header("Location: verEstudiantes.php");
    exit;
}

$datosGuardados = $_SESSION['datos_estudiante'] ?? [];
if (!is_array($datosGuardados)) {
    $datosGuardados = [];
}

if (!empty($datosGuardados)) {
    foreach ($datosGuardados as $key => $value) {
        $estudiante[$key] = $value;
    }
}

$todos_los_ciclos = listarTodosLosCiclos();

$errores = $_SESSION['errores'] ?? [];
if (!is_array($errores)) {
    $errores = [];
}

unset($_SESSION['datos_estudiante'], $_SESSION['errores']);

$nivelActual = '';
foreach ($todos_los_ciclos as $c) {
    if ($c['idCiclo'] == $estudiante['idCiclo']) {
        $nivelActual = $c['idNivel'];
        break;
    }
}

$titulo_pagina = "AULAPRO | MODIFICAR ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR ESTUDIANTE: <?= $estudiante['nombreEstudiante'] ?></h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id_del_estudiante ?>">

        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreEstudiante">Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $estudiante['nombreEstudiante'] ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailEstudiante">Email *</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $estudiante['emailEstudiante'] ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select id="filtroNivel" onchange="alCambiarNivel()">
                    <option value="">-- Selecciona un nivel --</option>
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
                            <?php if ($ciclo['idCiclo'] == $estudiante['idCiclo']) { echo 'selected'; } ?>
                            <?php if ($nivelActual !== '' && $ciclo['idNivel'] != $nivelActual) { echo 'style="display:none"'; } ?>>
                            <?= $ciclo['nombreCiclo'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="curso">Curso *</label>
                <select name="curso" id="curso">
                    <option value="1" <?php if ($estudiante['curso'] == 1) { echo 'selected'; } ?>>1º Curso</option>
                    <option value="2" <?php if ($estudiante['curso'] == 2) { echo 'selected'; } ?>>2º Curso</option>
                </select>
            </div>

            <div class="campo-formulario">
                <label for="dniEstudiante">DNI *</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $estudiante['dniEstudiante'] ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoEstudiante">Teléfono *</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $estudiante['telefonoEstudiante'] ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $estudiante['fechaNacimientoEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $estudiante['direccionEstudiante'] ?>">
            </div>

            <div class="campo-formulario">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $estudiante['ciudadEstudiante'] ?>">
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $estudiante['codigoPostalEstudiante'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="observacionesEstudiante">Observaciones</label>
                <textarea name="observacionesEstudiante" id="observacionesEstudiante"><?= $estudiante['observacionesEstudiante'] ?></textarea>
            </div>

            <input type="hidden" name="fechaAltaEstudiante" value="<?= $estudiante['fechaAltaEstudiante'] ?>">
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarEstudiante" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
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
