<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$datos = $_SESSION['datos_estudiante'] ?? [];

$titulo_pagina = "AULAPRO | NUEVO ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if (!empty($exito)) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if (is_string($errores) && $errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/estudiantes/insertar.php" method="POST">
        <div class="formulario">
            <div class="campo">
                <label for="nombreEstudiante">Nombre Completo</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= $datos['nombreEstudiante'] ?? '' ?>">
                <?php if (isset($errores['nombreEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="emailEstudiante">Email</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= $datos['emailEstudiante'] ?? '' ?>">
                <?php if (isset($errores['emailEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="dniEstudiante">DNI</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= $datos['dniEstudiante'] ?? '' ?>">
                <?php if (isset($errores['dniEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['dniEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="telefonoEstudiante">Teléfono</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= $datos['telefonoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['telefonoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= $datos['fechaNacimientoEstudiante'] ?? '' ?>">
                <?php if (isset($errores['fechaNacimientoEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= $datos['direccionEstudiante'] ?? '' ?>">
                <?php if (isset($errores['direccionEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= $datos['ciudadEstudiante'] ?? '' ?>">
                <?php if (isset($errores['ciudadEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= $datos['codigoPostalEstudiante'] ?? '' ?>">
                <?php if (isset($errores['codigoPostalEstudiante'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalEstudiante'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="curso">Nivel</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="">-- Selecciona un nivel --</option>
                    <option value="Grado Medio" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
                <?php if (isset($errores['curso'])) { ?>
                    <strong class="error-campo"><?= $errores['curso'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEstudiante" class="boton-primario" value="REGISTRAR ESTUDIANTE">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
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
    <?php if (!empty($datos['idCiclo'])) { ?>
    document.getElementById('idCiclo').value = '<?= $datos['idCiclo'] ?>';
    <?php } ?>
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
