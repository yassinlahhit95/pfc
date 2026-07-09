<?php
require_once __DIR__ . "/../../../include/SecretariaGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito   = $_SESSION['exito']   ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_estudiante'] ?? [];
unset($_SESSION['datos_estudiante']);

require_once __DIR__ . "/../../../modelos/ciclos.php";
$ciclos = listarTodosLosCiclos();

$titulo_pagina = 'AULAPRO | NUEVO ESTUDIANTE';
$seccion = 'estudiantes';
include __DIR__ . '/../comunes/nav.php';
?>

<div class="cabecera">
    <h1>NUEVO ESTUDIANTE</h1>
    <a href="verEstudiantes.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> Volver</a>
</div>

<div class="panel">
    <form action="../../../controladores/secretaria/estudiantes/insertar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'nombre') ?>">
            <label for="nombre">Nombre completo</label>
            <input type="text" name="nombre" id="nombre" value="<?= Security::escapeHtml($datos['nombre'] ?? '') ?>">
            <?= fieldError($errores, 'nombre') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'email') ?>">
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= Security::escapeHtml($datos['email'] ?? '') ?>">
            <?= fieldError($errores, 'email') ?>
        </div>

        <!-- Nivel / Curso -->
        <div class="campo<?= fieldClass($errores, 'curso') ?>">
            <label for="curso">Nivel</label>
            <select name="curso" id="curso" onchange="filtrarCiclos()">
                <option value="">-- Selecciona un nivel --</option>
                <option value="Grado Medio" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                <option value="Grado Superior" <?php if (isset($datos['curso']) && $datos['curso'] == 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
            </select>
            <?= fieldError($errores, 'curso') ?>
        </div>

        <!-- Año de estudio -->
        <div class="campo">
            <label for="anioEstudio">Año de estudio</label>
            <select name="anioEstudio" id="anioEstudio">
                <option value="">-- 1º o 2º año --</option>
                <option value="1º" <?= (($datos['anioEstudio'] ?? '') == '1º') ? 'selected' : '' ?>>1º año</option>
                <option value="2º" <?= (($datos['anioEstudio'] ?? '') == '2º') ? 'selected' : '' ?>>2º año</option>
            </select>
        </div>

        <!-- Ciclo formativo -->
        <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
            <label for="idCiclo">Ciclo formativo</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">-- Selecciona primero un nivel --</option>
            </select>
            <?= fieldError($errores, 'idCiclo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'dni') ?>">
            <label for="dni">DNI</label>
            <input type="text" name="dni" id="dni" value="<?= Security::escapeHtml($datos['dni'] ?? '') ?>">
            <?= fieldError($errores, 'dni') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'telefono') ?>">
            <label for="telefono">Teléfono</label>
            <input type="text" name="telefono" id="telefono" value="<?= Security::escapeHtml($datos['telefono'] ?? '') ?>">
            <?= fieldError($errores, 'telefono') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaNacimiento') ?>">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" name="fechaNacimiento" id="fechaNacimiento" value="<?= Security::escapeHtml($datos['fechaNacimiento'] ?? '') ?>">
            <?= fieldError($errores, 'fechaNacimiento') ?>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'direccion') ?>">
            <label for="direccion">Dirección</label>
            <input type="text" name="direccion" id="direccion" value="<?= Security::escapeHtml($datos['direccion'] ?? '') ?>">
            <?= fieldError($errores, 'direccion') ?>
        </div>

        <div class="campo ancho-total" style="display:flex;gap:10px;justify-content:flex-end;">
            <a href="verEstudiantes.php" class="boton-secundario">Cancelar</a>
            <button type="submit" class="boton-primario"><i class="fas fa-save"></i> Guardar</button>
        </div>
    </form>
</div>

<?php include __DIR__ . '/../comunes/footer.php'; ?>
<script>
var todosCiclos = <?= json_encode($ciclos) ?>;

function filtrarCiclos() {
    var nivel = $('#curso').val();
    var nivelId = nivel === 'Grado Medio' ? 1 : (nivel === 'Grado Superior' ? 2 : 0);
    var placeholder = nivel ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --';
    var $select = $('#idCiclo').empty().append($('<option>').val('').text(placeholder));

    if (nivelId > 0) {
        $.each(todosCiclos, function(i, ciclo) {
            if (parseInt(ciclo.idNivel) === nivelId) {
                $select.append($('<option>').val(ciclo.idCiclo).text(ciclo.nombreCiclo));
            }
        });
    }
}

$(function() {
    filtrarCiclos();
    <?php if (!empty($datos['idCiclo'])) { ?>
    $('#idCiclo').val('<?= Security::escapeHtml($datos['idCiclo']) ?>');
    <?php } ?>
});
</script>
