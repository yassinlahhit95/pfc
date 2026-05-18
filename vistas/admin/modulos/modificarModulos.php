<?php
session_start();
require_once __DIR__ . "/../../../modelos/modulos.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";

$id_del_modulo = $_GET['idModulo'] ?? 0;
$modulo = obtenerModuloPorId(intval($id_del_modulo));

if (!$modulo) {
    header("Location: verModulos.php");
    exit;
}

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

if (!empty($datos)) {
    $modulo = array_merge($modulo, $datos);
}

$nivelActual = '';
foreach ($todos_los_ciclos as $c) {
    if ($c['idCiclo'] == $modulo['idCiclo']) {
        $nivelActual = $c['idNivel'];
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

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?= $id_del_modulo ?>">

        <div class="formulario">
            <div class="campo">
                <label for="nombreModulo">Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" id="nombreModulo" value="<?= $modulo['nombreModulo'] ?>">
                <?php if (isset($errores['nombreModulo'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreModulo'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="nivel">Nivel *</label>
                <select id="nivel" onchange="filtrarCiclos()">
                    <option value="">-- Selecciona un nivel --</option>
                    <option value="1" <?php if ($nivelActual == 1) { echo 'selected'; } ?>>Grado Medio</option>
                    <option value="2" <?php if ($nivelActual == 2) { echo 'selected'; } ?>>Grado Superior</option>
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
                <label for="horasMaximas">Horas Totales *</label>
                <input type="text" name="horasMaximas" id="horasMaximas" value="<?= $modulo['horasMaximas'] ?>">
                <?php if (isset($errores['horasMaximas'])) { ?>
                    <strong class="error-campo"><?= $errores['horasMaximas'] ?></b>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<script>
var todosCiclos = [<?php foreach ($todos_los_ciclos as $c) { echo '{idCiclo:' . $c['idCiclo'] . ',idNivel:' . $c['idNivel'] . ',nombreCiclo:"' . addslashes($c['nombreCiclo']) . '"},'; } ?>];

function filtrarCiclos() {
    var nivelId = parseInt(document.getElementById('nivel').value);
    var cicloSelect = document.getElementById('idCiclo');

    cicloSelect.innerHTML = '<option value="">' + (nivelId > 0 ? '-- Selecciona un ciclo --' : '-- Selecciona primero un nivel --') + '</option>';

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
    if (document.getElementById('nivel').value !== '') {
        filtrarCiclos();
        document.getElementById('idCiclo').value = '<?= $modulo['idCiclo'] ?>';
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
