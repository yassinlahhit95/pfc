<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$datos = $_SESSION['datos_modulo'] ?? [];

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($todos_los_ciclos as $cicloItem) {
        if ($cicloItem['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $cicloItem['idNivel'];
            break;
        }
    }
}

$titulo_pagina = "AULAPRO | REGISTRAR MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>REGISTRAR NUEVO MÓDULO</h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">

        <div class="campo">
            <label for="nombreModulo">Nombre del Módulo</label>
            <input type="text" name="nombreModulo" id="nombreModulo" value="<?= Security::escapeHtml($datos['nombreModulo'] ?? '') ?>">
            
        </div>

        <div class="campo">
            <label for="nivel">Nivel</label>
            <select id="nivel" onchange="filtrarCiclos()">
                <option value="">-- Selecciona un nivel --</option>
                <option value="1" <?php if ($nivelActual == 1) { echo 'selected'; } ?>>Grado Medio</option>
                <option value="2" <?php if ($nivelActual == 2) { echo 'selected'; } ?>>Grado Superior</option>
            </select>
        </div>

        <div class="campo">
            <label for="idCiclo">Ciclo Formativo Asociado</label>
            <select name="idCiclo" id="idCiclo">
                <option value="">-- Selecciona primero un nivel --</option>
            </select>
            
        </div>

        <div class="campo">
            <label for="horasMaximas">Horas Máximas</label>
            <input type="number" name="horasMaximas" id="horasMaximas" min="1" value="<?= Security::escapeHtml($datos['horasMaximas'] ?? '') ?>">
        </div>

        <div class="campo">
            <label for="cursoAnio">Año del ciclo</label>
            <select name="cursoAnio" id="cursoAnio">
                <option value="">-- Sin especificar --</option>
                <option value="1º" <?= (($datos['cursoAnio'] ?? '') == '1º') ? 'selected' : '' ?>>1º año</option>
                <option value="2º" <?= (($datos['cursoAnio'] ?? '') == '2º') ? 'selected' : '' ?>>2º año</option>
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
var todosCiclos = <?= json_encode($todos_los_ciclos) ?>;

function filtrarCiclos() {
    var nivelId = parseInt($('#nivel').val());
    var placeholder = nivelId > 0 ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --';
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
    if ($('#nivel').val() !== '') {
        filtrarCiclos();
        <?php if (!empty($datos['idCiclo'])) { ?>
        $('#idCiclo').val('<?= Security::escapeHtml($datos['idCiclo']) ?>');
        <?php } ?>
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
