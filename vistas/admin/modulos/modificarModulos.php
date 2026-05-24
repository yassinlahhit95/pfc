<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_modulo = $_GET['idModulo'] ?? 0;
$modulo = obtenerModuloPorId($id_del_modulo);

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$todos_los_ciclos = listarTodosLosCiclos();

$datos = $_SESSION['datos_modulo'] ?? [];

if (!empty($datos)) {
    $modulo = $datos + $modulo;
}

$nivelActual = '';
foreach ($todos_los_ciclos as $cicloItem) {
    if ($cicloItem['idCiclo'] == $modulo['idCiclo']) {
        $nivelActual = $cicloItem['idNivel'];
        break;
    }
}

$titulo_pagina = "AULAPRO | MODIFICAR MÓDULO";
$seccion = 'modulos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR MÓDULO: <?= $modulo['nombreModulo'] ?></h1>
    <a href="verModulos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?= $id_del_modulo ?>">

        <div class="formulario">
            <div class="campo">
                <label for="nombreModulo">Nombre del Módulo</label>
                <input type="text" name="nombreModulo" id="nombreModulo" value="<?= $modulo['nombreModulo'] ?>">
                
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
                <label for="idCiclo">Ciclo Formativo</label>
                <select name="idCiclo" id="idCiclo">
                    <option value="">-- Selecciona primero un nivel --</option>
                </select>
                
            </div>

            <div class="campo">
                <label for="horasMaximas">Horas Totales</label>
                <input type="text" name="horasMaximas" id="horasMaximas" value="<?= $modulo['horasMaximas'] ?>">
                
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
        $('#idCiclo').val('<?= $modulo['idCiclo'] ?>');
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
