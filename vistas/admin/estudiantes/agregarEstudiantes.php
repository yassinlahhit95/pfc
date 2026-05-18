<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$titulo_pagina = "AULAPRO | NUEVO ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";

$todos_los_ciclos = listarTodosLosCiclos();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_estudiante'] ?? [];

if (!is_array($errores)) {
    $errores = [];
}
if (!is_array($datos)) {
    $datos = [];
}

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";

unset($_SESSION['errores'], $_SESSION['datos_estudiante'], $_SESSION['error'], $_SESSION['exito']);

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($todos_los_ciclos as $c) {
        if ($c['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $c['idNivel'];
            break;
        }
    }
}
?>

<div class="encabezado-pagina">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/estudiantes/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreEstudiante">Nombre Completo *</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?php if(isset($datos['nombreEstudiante'])) { echo $datos['nombreEstudiante']; } ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailEstudiante">Email *</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?php if(isset($datos['emailEstudiante'])) { echo $datos['emailEstudiante']; } ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniEstudiante">DNI *</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?php if(isset($datos['dniEstudiante'])) { echo $datos['dniEstudiante']; } ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoEstudiante">Teléfono *</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?php if(isset($datos['telefonoEstudiante'])) { echo $datos['telefonoEstudiante']; } ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento *</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?php if(isset($datos['fechaNacimientoEstudiante'])) { echo $datos['fechaNacimientoEstudiante']; } ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="direccionEstudiante">Dirección *</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?php if(isset($datos['direccionEstudiante'])) { echo $datos['direccionEstudiante']; } ?>">
                <?php if (isset($errores['direccionEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ciudadEstudiante">Ciudad *</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?php if(isset($datos['ciudadEstudiante'])) { echo $datos['ciudadEstudiante']; } ?>">
                <?php if (isset($errores['ciudadEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalEstudiante">Código Postal *</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?php if(isset($datos['codigoPostalEstudiante'])) { echo $datos['codigoPostalEstudiante']; } ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="curso">Grado *</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="">-- Selecciona un grado --</option>
                    <option value="Grado Medio" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
                <?php if (isset($errores['curso'])) { ?>
                    <strong class="error-campo"><?= $errores['curso'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un grado --</option>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarEstudiante" class="boton-primario">REGISTRAR ESTUDIANTE</button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>


<script>
var todosCiclos = <?= json_encode($todos_los_ciclos) ?>;

function filtrarCiclos() {
    var grado = document.getElementById('curso').value;
    var cicloSelect = document.getElementById('idCiclo');
    var nivelId = grado === 'Grado Medio' ? 1 : (grado === 'Grado Superior' ? 2 : 0);

    cicloSelect.innerHTML = '<option value="">' + (grado ? '-- Selecciona un ciclo --' : '-- Selecciona primero un grado --') + '</option>';

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
    <?php if (!empty($datos['idCiclo'])) { ?>
    document.getElementById('idCiclo').value = '<?= $datos['idCiclo'] ?>';
    <?php } ?>
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
