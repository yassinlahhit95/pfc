<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();

$datos = $_SESSION['datos_ciclo'] ?? [];
unset($_SESSION['datos_ciclo']);

$profesoresElegidos = $datos['profesores'] ?? [];
$mapaProfesoresElegidos = [];
foreach ($profesoresElegidos as $idProfesor) { $mapaProfesoresElegidos[$idProfesor] = true; }

$titulo_pagina = "AULAPRO | AGREGAR CICLO";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>AGREGAR CICLO</h1>
        <p class="subtitulo-encabezado">Defina un nuevo programa formativo y asigne recursos</p>
    </div>
    <a href="verCiclos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/admin/ciclos/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreCiclo') ?>">
                <label for="nombreCiclo">Nombre del Ciclo </label>
                <input type="text" id="nombreCiclo" name="nombreCiclo" placeholder="Desarrollo de Aplicaciones Web" value="<?= Security::escapeHtml($datos['nombreCiclo'] ?? '') ?>">
                <?= fieldError($errores, 'nombreCiclo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'abreviaturaCiclo') ?>">
                <label for="abreviaturaCiclo">Abreviatura </label>
                <input type="text" id="abreviaturaCiclo" name="abreviaturaCiclo" placeholder="Ej: DAW, SMR, Bach..." maxlength="10" value="<?= Security::escapeHtml($datos['abreviaturaCiclo'] ?? '') ?>">
                <?= fieldError($errores, 'abreviaturaCiclo') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'idNivel') ?>">
                <label for="idNivel">Nivel Formativo </label>
                <select id="idNivel" name="idNivel">
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= Security::escapeHtml($nivel['idNivel']) ?>" <?php if (($datos['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                            <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                        </option>
                    <?php } ?>
                </select>
                <?= fieldError($errores, 'idNivel') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'precioCiclo') ?>">
                <label for="precioCiclo">Precio Total del Ciclo (€) </label>
                <input type="number" id="precioCiclo" name="precioCiclo" step="0.01" value="<?= Security::escapeHtml($datos['precioCiclo'] ?? '1000.00') ?>">
                <?= fieldError($errores, 'precioCiclo') ?>
            </div>
        </div>

        <div style="margin-top: 25px;">
            <h4 class="margen-abajo">Asignar Tutores/Profesores</h4>
            <div class="modulo-chips">
                <?php foreach ($listaProfesores as $prof) { ?>
                    <label class="modulo-chip">
                        <input type="checkbox" name="profesores[]" value="<?= Security::escapeHtml($prof['idProfesor']) ?>"
                            <?php if (isset($mapaProfesoresElegidos[$prof['idProfesor']])) { ?>checked<?php } ?>>
                        <span><?= Security::escapeHtml($prof['nombreProfesor']) ?></span>
                    </label>
                <?php } ?>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarCiclo" class="boton-primario" value="CREAR CICLO FORMATIVO">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
