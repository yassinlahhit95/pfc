<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/FPSystem.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$idCiclo = (int)($_GET['idCiclo'] ?? 0);
$ciclo = obtenerCicloPorId($idCiclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();

$profesoresMarcados = listarProfesoresDeUnCiclo($idCiclo);
$datosSesion = $_SESSION['datos_ciclos'] ?? null;
unset($_SESSION['datos_ciclos']);
if ($datosSesion) {
    $ciclo = $datosSesion + $ciclo;
    $profesoresMarcados = $datosSesion['profesores'] ?? [];
}

$titulo_pagina = "Modificar Ciclo";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR CICLO: <?= Security::escapeHtml($ciclo['nombreCiclo']) ?></h1>
    </div>
    <a href="verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>


<div class="panel">
    <form method="POST" action="../../../controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idCiclo" value="<?= Security::escapeHtml($idCiclo) ?>">
        
        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreCiclo') ?>">
                    <label for="nombreCiclo">Nombre del Ciclo</label>
                    <input type="text" id="nombreCiclo" name="nombreCiclo" value="<?= Security::escapeHtml($ciclo['nombreCiclo'] ?? '') ?>">
                    <?= fieldError($errores, 'nombreCiclo') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'abreviaturaCiclo') ?>">
                    <label for="abreviaturaCiclo">Abreviatura</label>
                    <input type="text" id="abreviaturaCiclo" name="abreviaturaCiclo" maxlength="10" value="<?= Security::escapeHtml($ciclo['abreviaturaCiclo'] ?? '') ?>">
                    <?= fieldError($errores, 'abreviaturaCiclo') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo">
                    <label for="tipoFormacion">Tipo de Formación Profesional</label>
                    <div style="padding: 10px; background: var(--surface-2); border-radius: 6px; border: 1px solid var(--border);">
                        <strong><?= Security::escapeHtml(FPSystem::getLabel($ciclo['tipoFormacion'] ?? 'medio')) ?></strong>
                        <small style="display: block; color: var(--text-2); margin-top: 4px;">No se puede cambiar después de la creación</small>
                    </div>
                </div>

                <div class="campo">
                    <label for="idNivel">Nivel Formativo</label>
                    <select id="idNivel" name="idNivel">
                        <?php foreach ($listaNiveles as $nivel) { ?>
                            <option value="<?= Security::escapeHtml($nivel['idNivel']) ?>" <?php if (($ciclo['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                                <?= Security::escapeHtml($nivel['nombreNivel']) ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="campo<?= fieldClass($errores, 'precioCiclo') ?>">
                    <label for="precioCiclo">Precio Total del Ciclo (€)</label>
                    <input type="number" id="precioCiclo" name="precioCiclo" step="0.01" value="<?= Security::escapeHtml($ciclo['precioCiclo'] ?? '') ?>">
                    <?= fieldError($errores, 'precioCiclo') ?>
                </div>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;">Asignar Tutores/Profesores</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="profesores[]" value="<?= Security::escapeHtml($prof['idProfesor']) ?>"
                                <?php if (in_array($prof['idProfesor'], $profesoresMarcados)) { ?>checked<?php } ?>>
                            <span><?= Security::escapeHtml($prof['nombreProfesor']) ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarCiclo" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
