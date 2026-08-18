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

$datos = $_SESSION['datos_modulo'] ?? [];
unset($_SESSION['datos_modulo']);

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($todosLosCiclos as $cicloItem) {
        if ($cicloItem['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $cicloItem['nombreNivel'];
            break;
        }
    }
}

$titulo_pagina = "Registrar Módulo";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>Registrar Nuevo Módulo</h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo<?= fieldClass($errores, 'nombreModulo') ?>">
            <label for="nombreModulo">Nombre del Módulo</label>
            <input type="text" name="nombreModulo" id="nombreModulo" value="<?= Security::escapeHtml($datos['nombreModulo'] ?? '') ?>">
            <?= fieldError($errores, 'nombreModulo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'codigoModulo') ?>">
            <label for="codigoModulo">Código del Módulo <span class="texto-suave" style="font-weight:400;">(oficial, ej: 0483)</span></label>
            <input type="text" name="codigoModulo" id="codigoModulo" maxlength="20" value="<?= Security::escapeHtml($datos['codigoModulo'] ?? '') ?>">
            <?= fieldError($errores, 'codigoModulo') ?>
        </div>

        <div class="campo">
            <label for="nivel">Nivel</label>
            <select id="nivel" onchange="filtrarCiclos()">
                <option value="">-- Selecciona un nivel --</option>
                <option value="Grado Medio" <?php if ($nivelActual === 'Grado Medio') { echo 'selected'; } ?>>Grado Medio</option>
                <option value="Grado Superior" <?php if ($nivelActual === 'Grado Superior') { echo 'selected'; } ?>>Grado Superior</option>
            </select>
        </div>

        <div class="campo<?= fieldClass($errores, 'idCiclo') ?>">
            <label for="idCiclo">Ciclo Formativo Asociado</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">-- Selecciona primero un nivel --</option>
            </select>
            <?= fieldError($errores, 'idCiclo') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'horasMaximas') ?>">
            <label for="horasMaximas">Horas Máximas</label>
            <input type="number" name="horasMaximas" id="horasMaximas" min="1" value="<?= Security::escapeHtml($datos['horasMaximas'] ?? '') ?>">
            <?= fieldError($errores, 'horasMaximas') ?>
        </div>

        <div class="campo">
            <label for="cursoAnio">Año del ciclo</label>
            <select name="cursoAnio" id="cursoAnio">
                <option value="">-- Selecciona primero un ciclo --</option>
            </select>
        </div>

        <div class="campo">
            <label for="tipoModulo">Tipo de Módulo</label>
            <select name="tipoModulo" id="tipoModulo">
                <option value="Específico" <?= (($datos['tipoModulo'] ?? '') == 'Específico') ? 'selected' : '' ?>>Específico del Ciclo</option>
                <option value="Transversal" <?= (($datos['tipoModulo'] ?? '') == 'Transversal') ? 'selected' : '' ?>>Módulo Transversal (RD 659/2023)</option>
                <option value="Proyecto" <?= (($datos['tipoModulo'] ?? '') == 'Proyecto') ? 'selected' : '' ?>>Proyecto Intermodular</option>
                <option value="Empresa" <?= (($datos['tipoModulo'] ?? '') == 'Empresa') ? 'selected' : '' ?>>Estancia en Empresa</option>
            </select>
        </div>

        <div class="campo">
            <label for="creditosECTS">Créditos ECTS <span class="texto-suave" style="font-weight:400;">(RD 659/2023)</span></label>
            <input type="number" name="creditosECTS" id="creditosECTS" min="1" max="30" value="<?= Security::escapeHtml($datos['creditosECTS'] ?? '') ?>" placeholder="Opcional">
        </div>

        <div class="acciones">
            <input type="submit" name="guardarModulo" class="boton-primario" value="REGISTRAR MÓDULO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
var todosCiclos = <?= Security::jsonEncodeSafe($todosLosCiclos) ?>;
var todosCursos = <?= Security::jsonEncodeSafe($todosLosCursos) ?>;
var cursoAnioActual = <?= Security::jsonEncodeSafe($datos['cursoAnio'] ?? '') ?>;

function filtrarCiclos() {
    var nivelNombre = $('#nivel').val();
    var placeholder = nivelNombre ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --';
    var $select = $('#idCiclo').empty().append($('<option>').val('').text(placeholder));

    if (nivelNombre) {
        $.each(todosCiclos, function(i, ciclo) {
            if (ciclo.nombreNivel === nivelNombre) {
                $select.append($('<option>').val(ciclo.idCiclo).text(ciclo.nombreCiclo));
            }
        });
    }
    poblarCursos();
}

function poblarCursos() {
    var idCiclo = $('#idCiclo').val();
    var $select = $('#cursoAnio').empty();
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
    if (cursoAnioActual) $select.val(cursoAnioActual);
}

$('#idCiclo').on('change', poblarCursos);

$(function() {
    if ($('#nivel').val() !== '') {
        filtrarCiclos();
        <?php if (!empty($datos['idCiclo'])) { ?>
        $('#idCiclo').val('<?= Security::escapeHtml($datos['idCiclo']) ?>');
        poblarCursos();
        <?php } ?>
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
