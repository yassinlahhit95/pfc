<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$id_ciclo = $_GET['idCiclo'] ?? '';
$ciclo = obtenerCicloPorId($id_ciclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$profesoresAsignadosRaw = obtenerProfesoresDeUnCiclo($id_ciclo);
if (!is_array($profesoresAsignadosRaw)) {
    $profesoresAsignadosRaw = [];
}
$profesoresAsignados = [];
foreach ($profesoresAsignadosRaw as $p) {
    if (is_array($p) && isset($p['idProfesor'])) {
        $profesoresAsignados[] = $p['idProfesor'];
    }
}

$aulasAsignadasRaw = obtenerAulasDeUnCiclo($id_ciclo);
if (!is_array($aulasAsignadasRaw)) {
    $aulasAsignadasRaw = [];
}
$aulasAsignadas = [];
foreach ($aulasAsignadasRaw as $a) {
    if (is_array($a) && isset($a['idAula'])) {
        $aulasAsignadas[] = $a['idAula'];
    }
}

if (isset($_SESSION['datos_ciclos'])) {
    foreach ($_SESSION['datos_ciclos'] as $key => $value) {
        $ciclo[$key] = $value;
    }
}

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_ciclos']);

$titulo_pagina = "Modificar Ciclo - Admin";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Ciclo: <?= $ciclo['nombreCiclo'] ?></h1>
    <a href="verCiclos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="../../../controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="idCiclo" value="<?= $id_ciclo ?>">
        
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" value="<?= $ciclo['nombreCiclo'] ?? '' ?>">
                <?php if (isset($lista_de_errores['nombreCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Abreviatura *</label>
                <input type="text" name="abreviaturaCiclo" maxlength="10" value="<?= $ciclo['abreviaturaCiclo'] ?? '' ?>">
                <?php if (isset($lista_de_errores['abreviaturaCiclo'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['abreviaturaCiclo'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select name="idNivel">
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?= $nivel['idNivel'] ?>" <?php if (($ciclo['idNivel'] ?? '') == $nivel['idNivel']) { ?>selected<?php } ?>>
                            <?= $nivel['nombreNivel'] ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idNivel'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['idNivel'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Precio Total del Ciclo (€) *</label>
                <input type="number" name="precioCiclo" step="0.01" value="<?= $ciclo['precioCiclo'] ?? '' ?>">
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
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

            <div>
                <h4 class="margen-abajo">Asignar Aulas Habituales</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaAulas as $aula) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="aulas[]" value="<?= $aula['idAula'] ?>"
                                <?php if (in_array($aula['idAula'], $aulasAsignadas)) { ?>checked<?php } ?>>
                            <span><?= $aula['nombreAula'] ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


