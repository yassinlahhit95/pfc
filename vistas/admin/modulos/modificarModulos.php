<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/academico_config.php";

$id_del_modulo = (int)($_GET['idModulo'] ?? 0);
$modulo = obtenerModuloPorId($id_del_modulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$todos_los_ciclos = listarTodosLosCiclos();
$todos_los_cursos = listarTodosLosCursosAcademicos();

$datos = $_SESSION['datos_modulo'] ?? [];

if (!empty($datos)) {
    $modulo = $datos + $modulo;
}

$nivelActual = '';
foreach ($todos_los_ciclos as $cicloItem) {
    if ($cicloItem['idCiclo'] == $modulo['idCiclo']) {
        $nivelActual = $cicloItem['nombreNivel'];
        break;
    }
}

$titulo_pagina = "AULAPRO | MODIFICAR MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR MÓDULO: <?= Security::escapeHtml($modulo['nombreModulo']) ?></h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/modulos/actualizar.php" method="POST">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idModulo" value="<?= $id_del_modulo ?>">

        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreModulo') ?>">
                <label for="nombreModulo">Nombre del Módulo</label>
                <input type="text" name="nombreModulo" id="nombreModulo" value="<?= Security::escapeHtml($modulo['nombreModulo']) ?>">
                <?= fieldError($errores, 'nombreModulo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'codigoModulo') ?>">
                <label for="codigoModulo">Código del Módulo <span class="texto-suave" style="font-weight:400;">(oficial, ej: 0483)</span></label>
                <input type="text" name="codigoModulo" id="codigoModulo" maxlength="20" value="<?= Security::escapeHtml($modulo['codigoModulo'] ?? '') ?>">
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
                <label for="idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>
                <?= fieldError($errores, 'idCiclo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'horasMaximas') ?>">
                <label for="horasMaximas">Horas Totales</label>
                <input type="number" name="horasMaximas" id="horasMaximas" min="1" value="<?= Security::escapeHtml($modulo['horasMaximas']) ?>">
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
                    <option value="Específico" <?= (($modulo['tipoModulo'] ?? '') == 'Específico') ? 'selected' : '' ?>>Específico del Ciclo</option>
                    <option value="Transversal" <?= (($modulo['tipoModulo'] ?? '') == 'Transversal') ? 'selected' : '' ?>>Módulo Transversal (RD 659/2023)</option>
                    <option value="Proyecto" <?= (($modulo['tipoModulo'] ?? '') == 'Proyecto') ? 'selected' : '' ?>>Proyecto Intermodular</option>
                    <option value="Empresa" <?= (($modulo['tipoModulo'] ?? '') == 'Empresa') ? 'selected' : '' ?>>Estancia en Empresa</option>
                </select>
            </div>

            <div class="campo">
                <label for="creditosECTS">Créditos ECTS <span class="texto-suave" style="font-weight:400;">(RD 659/2023)</span></label>
                <input type="number" name="creditosECTS" id="creditosECTS" min="1" max="30" value="<?= Security::escapeHtml($modulo['creditosECTS'] ?? '') ?>" placeholder="Opcional">
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarModulo" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script>
var todosCiclos = <?= json_encode($todos_los_ciclos) ?>;
var todosCursos = <?= json_encode($todos_los_cursos) ?>;
var cursoAnioActual = <?= json_encode($modulo['cursoAnio'] ?? '') ?>;

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
        $('#idCiclo').val('<?= Security::escapeHtml($modulo['idCiclo']) ?>');
        poblarCursos();
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
