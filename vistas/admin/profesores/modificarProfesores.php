<?php
session_start();
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_profesor = $_GET['idProfesor'] ?? '';
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$modulos_por_ciclo = [];
foreach ($todosLosModulos as $m) {
    $idC = $m['idCiclo'];
    if (!isset($modulos_por_ciclo[$idC])) {
        $modulos_por_ciclo[$idC] = ['nombre' => $m['nombreCiclo'], 'modulos' => []];
    }
    $modulos_por_ciclo[$idC]['modulos'][] = $m;
}

if (isset($_SESSION['datos_profesor'])) {
    $profesor = array_merge($profesor, $_SESSION['datos_profesor']);
    $ciclosElegidos = (isset($_SESSION['datos_profesor']['ciclos']) && is_array($_SESSION['datos_profesor']['ciclos'])) ? $_SESSION['datos_profesor']['ciclos'] : [];
    $modulosElegidos = (isset($_SESSION['datos_profesor']['modulos']) && is_array($_SESSION['datos_profesor']['modulos'])) ? $_SESSION['datos_profesor']['modulos'] : [];
} else {
    $ciclosElegidos = [];
    $ciclosBD = listarCiclosTutorizadosProfesor($id_profesor);
    foreach ($ciclosBD as $cbd) { $ciclosElegidos[] = $cbd['idCiclo']; }
    $modulosElegidos = listarIdsModulosDeProfesor($id_profesor);
}

$mapaCiclosElegidos = [];
foreach ($ciclosElegidos as $idC) { $mapaCiclosElegidos[$idC] = true; }
$mapaModulosElegidos = [];
foreach ($modulosElegidos as $idM) { $mapaModulosElegidos[$idM] = true; }

$errores = $_SESSION['errores'] ?? [];
$error = $_SESSION['error'] ?? '';

unset($_SESSION['errores'], $_SESSION['datos_profesor'], $_SESSION['error']);

$titulo_pagina = "AULAPRO | MODIFICAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR PROFESOR: <?= $profesor['nombreProfesor'] ?></h1>
    <a href="verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?= $id_profesor ?>">
        
        <div class="formulario">
            <div class="campo">
                <label for="nombreProfesor">Nombre Completo *</label>
                <input type="text" name="nombreProfesor" id="nombreProfesor" value="<?= $profesor['nombreProfesor'] ?>">
                <?php if (isset($errores['nombreProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="emailProfesor">Email *</label>
                <input type="email" name="emailProfesor" id="emailProfesor" value="<?= $profesor['emailProfesor'] ?>">
                <?php if (isset($errores['emailProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['emailProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="dniProfesor">DNI *</label>
                <input type="text" name="dniProfesor" id="dniProfesor" value="<?= $profesor['dniProfesor'] ?>">
                <?php if (isset($errores['dniProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['dniProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="telefonoProfesor">Teléfono *</label>
                <input type="text" name="telefonoProfesor" id="telefonoProfesor" value="<?= $profesor['telefonoProfesor'] ?>">
                <?php if (isset($errores['telefonoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionProfesor">Dirección *</label>
                <input type="text" name="direccionProfesor" id="direccionProfesor" value="<?= $profesor['direccionProfesor'] ?>">
                <?php if (isset($errores['direccionProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="ciudadProfesor">Ciudad *</label>
                <input type="text" name="ciudadProfesor" id="ciudadProfesor" value="<?= $profesor['ciudadProfesor'] ?>">
                <?php if (isset($errores['ciudadProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="codigoPostalProfesor">Código Postal *</label>
                <input type="text" name="codigoPostalProfesor" id="codigoPostalProfesor" value="<?= $profesor['codigoPostalProfesor'] ?>">
                <?php if (isset($errores['codigoPostalProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoProfesor" id="fechaNacimientoProfesor" value="<?= $profesor['fechaNacimientoProfesor'] ?>">
                <?php if (isset($errores['fechaNacimientoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoProfesor'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaAltaProfesor">Fecha de Alta (en centro)</label>
                <input type="date" name="fechaAltaProfesor" id="fechaAltaProfesor" value="<?= $profesor['fechaAltaProfesor'] ?>">
            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesProfesor">Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea name="observacionesProfesor" id="observacionesProfesor" rows="3"><?= $profesor['observacionesProfesor'] ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 class="margen-abajo"><i class="fas fa-layer-group"></i> 1. Seleccionar Ciclos</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="ciclos[]" value="<?= $ciclo['idCiclo'] ?>" class="check-ciclo"
                                <?php if (isset($mapaCiclosElegidos[$ciclo['idCiclo']])) { echo 'checked'; } ?>>
                            <span><?= $ciclo['nombreCiclo'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="margen-abajo"><i class="fas fa-book"></i> 2. Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="atenuado" style="text-align: center; padding: 20px;">
                        Seleccione primero uno o varios ciclos para ver sus módulos disponibles.
                    </p>
                    <?php foreach ($modulos_por_ciclo as $idCiclo => $grupo) { ?>
                        <div class="grupo-modulos oculto" style="margin-bottom: 15px;" id="grupo-ciclo-<?= $idCiclo ?>">
                            <p class="texto-negrita color-primario" style="margin-bottom: 10px;">
                                <?= $grupo['nombre'] ?>
                            </p>
                            <?php foreach ($grupo['modulos'] as $mod) { ?>
                                <label class="check-item" style="padding-left: 10px;">
                                    <input type="checkbox" name="modulos[]" value="<?= $mod['idModulo'] ?>"
                                        <?php if (isset($mapaModulosElegidos[$mod['idModulo']])) { echo 'checked'; } ?>>
                                    <span><?= $mod['nombreModulo'] ?></span>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top: 25px;">
            <button type="submit" name="actualizarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<script src="../../../public/js/profesores-form.js"></script>

<?php include '../comunes/footer.php'; ?>

