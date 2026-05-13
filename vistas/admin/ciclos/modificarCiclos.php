<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";

$id_ciclo = $_GET['idCiclo'] ?? '';
$ciclo = obtenerCicloPorId($id_ciclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();

$profesoresAsignados = obtenerProfesoresDeUnCiclo($id_ciclo);
if (!is_array($profesoresAsignados)) {
    $profesoresAsignados = [];
}

if (isset($_SESSION['datos_ciclos'])) {
    foreach ($_SESSION['datos_ciclos'] as $key => $value) {
        $ciclo[$key] = $value;
    }
    if (isset($_SESSION['datos_ciclos']['profesores'])) {
        $profesoresAsignados = $_SESSION['datos_ciclos']['profesores'];
    }
}

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_ciclos']);

$titulo_pagina = "AULAPRO | MODIFICAR CICLO";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR CICLO: <?= $ciclo['nombreCiclo'] ?></h1>
    <a href="verCiclos.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="idCiclo" value="<?= $id_ciclo ?>">
        
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreCiclo">Nombre del Ciclo *</label>
                <input type="text" id="nombreCiclo" name="nombreCiclo" value="<?= $ciclo['nombreCiclo'] ?? '' ?>">
                <?php if (isset($errores['nombreCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="abreviaturaCiclo">Abreviatura *</label>
                <input type="text" id="abreviaturaCiclo" name="abreviaturaCiclo" maxlength="10" value="<?= $ciclo['abreviaturaCiclo'] ?? '' ?>">
                <?php if (isset($errores['abreviaturaCiclo'])) { ?>
                    <strong class="error-campo"><?= $errores['abreviaturaCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idNivel">Nivel Formativo *</label>
                <select id="idNivel" name="idNivel">
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= $nivel['idNivel'] ?>" <?php if (($ciclo['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                            <?= $nivel['nombreNivel'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idNivel'])) { ?>
                    <strong class="error-campo"><?= $errores['idNivel'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="precioCiclo">Precio Total del Ciclo (€) *</label>
                <input type="number" id="precioCiclo" name="precioCiclo" step="0.01" value="<?= $ciclo['precioCiclo'] ?? '' ?>">
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 class="margen-abajo">Asignar Tutores/Profesores</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="profesores[]" value="<?= $prof['idProfesor'] ?>"
                                <?php if (in_array($prof['idProfesor'], $profesoresAsignados)) { ?>checked<?php } ?>>
                            <span><?= $prof['nombreProfesor'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>




