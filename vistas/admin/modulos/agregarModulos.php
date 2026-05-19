<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";

$todos_los_ciclos = listarTodosLosCiclos();

$error = $_SESSION['error'] ?? "";
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_modulo']);

$nivelActual = '';
if (!empty($datos['idCiclo'])) {
    foreach ($todos_los_ciclos as $c) {
        if ($c['idCiclo'] == $datos['idCiclo']) {
            $nivelActual = $c['idNivel'];
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

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/modulos/insertar.php" method="POST" class="formulario">

        <div class="campo">
            <label for="nombreModulo">Nombre del Módulo</label>
            <input type="text" name="nombreModulo" id="nombreModulo" value="<?= $datos['nombreModulo'] ?? '' ?>">
            <?php if (isset($errores['nombreModulo'])) { ?>
                <strong class="error-campo"><?= $errores['nombreModulo'] ?></strong>
            <?php } ?>
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
            <?php if (isset($errores['idCiclo'])) { ?>
                <strong class="error-campo"><?= $errores['idCiclo'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="horasMaximas">Horas Máximas</label>
            <input type="text" name="horasMaximas" id="horasMaximas" value="<?= $datos['horasMaximas'] ?? '' ?>">
            <?php if (isset($errores['horasMaximas'])) { ?>
                <strong class="error-campo"><?= $errores['horasMaximas'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarModulo" class="boton-primario" value="REGISTRAR MÓDULO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
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
        <?php if (!empty($datos['idCiclo'])) { ?>
        document.getElementById('idCiclo').value = '<?= $datos['idCiclo'] ?>';
        <?php } ?>
    }
});
</script>

<?php include '../comunes/footer.php'; ?>
