<?php
session_start();
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

$titulo_pagina = "AULAPRO | MODIFICAR ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR ESTUDIANTE: <?= $estudiante['nombreEstudiante'] ?></h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/estudiantes/actualizar.php" method="POST">
        <input type="hidden" name="idEstudiante" value="<?= $id_del_estudiante ?>">

        <div class="formulario">
            <div class="campo">
                <label for="nombreEstudiante">Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $estudiante['nombreEstudiante'] ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="emailEstudiante">Email *</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $estudiante['emailEstudiante'] ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="curso">Nivel *</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="Grado Medio" <?php if ($estudiante['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if ($estudiante['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="dniEstudiante">DNI *</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $estudiante['dniEstudiante'] ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="telefonoEstudiante">Teléfono *</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $estudiante['telefonoEstudiante'] ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $estudiante['fechaNacimientoEstudiante'] ?>">
            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $estudiante['direccionEstudiante'] ?>">
            </div>

            <div class="campo">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $estudiante['ciudadEstudiante'] ?>">
            </div>

            <div class="campo">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $estudiante['codigoPostalEstudiante'] ?>">
            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesEstudiante">Observaciones</label>
                <textarea name="observacionesEstudiante" id="observacionesEstudiante"><?= $estudiante['observacionesEstudiante'] ?></textarea>
            </div>

            <input type="hidden" name="fechaAltaEstudiante" value="<?= $estudiante['fechaAltaEstudiante'] ?>">
        </div>

        <div class="acciones">
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
var todosCiclos = [<?php foreach ($todos_los_ciclos as $c) { echo '{idCiclo:' . $c['idCiclo'] . ',idNivel:' . $c['idNivel'] . ',nombreCiclo:"' . addslashes($c['nombreCiclo']) . '"},'; } ?>];

function filtrarCiclos() {
    var nivel = document.getElementById('curso').value;
    var cicloSelect = document.getElementById('idCiclo');
    var nivelId = nivel === 'Grado Medio' ? 1 : (nivel === 'Grado Superior' ? 2 : 0);

    cicloSelect.innerHTML = '<option value="">' + (nivel ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --') + '</option>';

    if (nivelId > 0) {
        todosCiclos.forEach(function(ciclo) {
            if (parseInt(ciclo.idNivel) === nivelId) {
                var opt = document.createElement('option');
                opt.value = ciclo.idCiclo;
                opt.textContent = ciclo.nombreCiclo;
                cicloSelect.appendChild(opt);
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    filtrarCiclos();
    document.getElementById('idCiclo').value = '<?= $estudiante['idCiclo'] ?>';
});
</script>

<?php include '../comunes/footer.php'; ?>
