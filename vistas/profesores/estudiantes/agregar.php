<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$idProfesor   = $_SESSION['idProfesor'] ?? '';
$esTutor      = !empty($_SESSION['esTutor']);
$idCicloTutor = (int)($_SESSION['idCicloTutor'] ?? 0);

require_once __DIR__ . "/../../../modelos/ciclos.php";

if ($esTutor && $idCicloTutor) {
    $cicloTutor = obtenerCicloPorId($idCicloTutor);
    $misCiclos = $cicloTutor ? [$cicloTutor] : [];
} else {
    $misCiclos = listarCiclosDeProfesor($idProfesor);
}

$datos = $_SESSION['datos_estudiante'] ?? [];

$tituloDelPagina = "AULAPRO | NUEVO ESTUDIANTE";
$seccionActual = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="lista.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/profesores/estudiantes/insertar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo">
                <label for="nombreEstudiante">Nombre Completo</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= Security::escapeHtml($datos['nombreEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="emailEstudiante">Email</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= Security::escapeHtml($datos['emailEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="dniEstudiante">DNI</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= Security::escapeHtml($datos['dniEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="telefonoEstudiante">Teléfono</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= Security::escapeHtml($datos['telefonoEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= Security::escapeHtml($datos['fechaNacimientoEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= Security::escapeHtml($datos['direccionEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= Security::escapeHtml($datos['ciudadEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= Security::escapeHtml($datos['codigoPostalEstudiante'] ?? '') ?>">

            </div>

            <div class="campo">
                <label for="curso">Nivel</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="Grado Medio" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
            </div>

            <div class="campo">
                <label for="idCiclo">Ciclo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>

            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEstudiante" class="boton-primario" value="REGISTRAR ESTUDIANTE">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
var todosCiclos = <?= json_encode($misCiclos) ?>;

function filtrarCiclos() {
    var nivel = $('#curso').val();
    var placeholder = nivel ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --';
    var $select = $('#idCiclo').empty().append($('<option>').val('').text(placeholder));

    if (nivel) {
        $.each(todosCiclos, function(i, ciclo) {
            if (ciclo.nombreNivel === nivel) {
                $select.append($('<option>').val(ciclo.idCiclo).text(ciclo.nombreCiclo));
            }
        });
    }
}

$(function() {
    filtrarCiclos();
    <?php if (!empty($datos['idCiclo'])) { ?>
    $('#idCiclo').val('<?= Security::escapeHtml($datos['idCiclo'] ) ?>');
    <?php } ?>
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
