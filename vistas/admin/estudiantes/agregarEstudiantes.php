<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

$todosLosCiclos = listarTodosLosCiclos();
$todosLosCursos = listarTodosLosCursosAcademicos();

$datos = $_SESSION['datos_estudiante'] ?? [];
unset($_SESSION['datos_estudiante']);

$titulo_pagina = "AULAPRO | NUEVO ESTUDIANTE";
$seccion = 'estudiantes';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="../../../vistas/admin/estudiantes/verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/estudiantes/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreEstudiante') ?>">
                <label for="nombreEstudiante">Nombre Completo</label>
                <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= Security::escapeHtml($datos['nombreEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'nombreEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'emailEstudiante') ?>">
                <label for="emailEstudiante">Email</label>
                <input type="text" name="emailEstudiante" id="emailEstudiante" value="<?= Security::escapeHtml($datos['emailEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'emailEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'dniEstudiante') ?>">
                <label for="dniEstudiante">DNI</label>
                <input type="text" name="dniEstudiante" id="dniEstudiante" value="<?= Security::escapeHtml($datos['dniEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'dniEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'telefonoEstudiante') ?>">
                <label for="telefonoEstudiante">Teléfono</label>
                <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= Security::escapeHtml($datos['telefonoEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'telefonoEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaNacimientoEstudiante') ?>">
                <label for="fechaNacimientoEstudiante">Fecha Nacimiento</label>
                <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante" value="<?= Security::escapeHtml($datos['fechaNacimientoEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'fechaNacimientoEstudiante') ?>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'direccionEstudiante') ?>">
                <label for="direccionEstudiante">Dirección</label>
                <input type="text" name="direccionEstudiante" id="direccionEstudiante" value="<?= Security::escapeHtml($datos['direccionEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'direccionEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'ciudadEstudiante') ?>">
                <label for="ciudadEstudiante">Ciudad</label>
                <input type="text" name="ciudadEstudiante" id="ciudadEstudiante" value="<?= Security::escapeHtml($datos['ciudadEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'ciudadEstudiante') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'codigoPostalEstudiante') ?>">
                <label for="codigoPostalEstudiante">Código Postal</label>
                <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante" value="<?= Security::escapeHtml($datos['codigoPostalEstudiante'] ?? '') ?>">
                <?= fieldError($errores, 'codigoPostalEstudiante') ?>
            </div>

            <div class="campo">
                <label for="fechaAltaEstudiante">Fecha de Alta</label>
                <input type="date" name="fechaAltaEstudiante" id="fechaAltaEstudiante" value="<?= Security::escapeHtml($datos['fechaAltaEstudiante'] ?? date('Y-m-d')) ?>">
            </div>

            <div class="campo<?= fieldClass($errores, 'curso') ?>">
                <label for="curso">Nivel</label>
                <select name="curso" id="curso" onchange="filtrarCiclos()">
                    <option value="">-- Selecciona un nivel --</option>
                    <option value="Grado Medio" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="Grado Superior" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
                </select>
                <?= fieldError($errores, 'curso') ?>
            </div>

            <div class="campo">
                <label for="anioEstudio">Año de estudio</label>
                <select name="anioEstudio" id="anioEstudio">
                    <option value="">-- Selecciona primero un ciclo --</option>
                </select>
            </div>

            <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
                <label for="idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>
                <?= fieldError($errores, 'idCiclo') ?>
            </div>

            <div class="campo ancho-total">
                <label for="observacionesEstudiante">Observaciones</label>
                <textarea name="observacionesEstudiante" id="observacionesEstudiante" rows="3"><?= Security::escapeHtml($datos['observacionesEstudiante'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarEstudiante" class="boton-primario" value="REGISTRAR ESTUDIANTE">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
var todosCiclos = <?= json_encode($todosLosCiclos) ?>;
var todosCursos = <?= json_encode($todosLosCursos) ?>;
var anioEstudioActual = <?= json_encode($datos['anioEstudio'] ?? '') ?>;

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
    poblarAnios();
}

function poblarAnios() {
    var idCiclo = $('#idCiclo').val();
    var $select = $('#anioEstudio').empty();
    if (!idCiclo) {
        $select.append($('<option>').val('').text('-- Selecciona primero un ciclo --'));
        return;
    }
    $select.append($('<option>').val('').text('-- Sin especificar --'));
    $.each(todosCursos, function(i, curso) {
        if (String(curso.idCiclo) === String(idCiclo)) {
            $select.append($('<option>').val(curso.nombre).text(curso.nombre + ' año'));
        }
    });
    if (anioEstudioActual) $select.val(anioEstudioActual);
}

$('#idCiclo').on('change', poblarAnios);

$(function() {
    filtrarCiclos();
    <?php if (!empty($datos['idCiclo'])) { ?>
    $('#idCiclo').val('<?= Security::escapeHtml($datos['idCiclo']) ?>');
    poblarAnios();
    <?php } ?>
});
</script>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
