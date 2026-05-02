<?php
session_start();
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/niveles.php";
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/aulas.php";

$id_ciclo = $_GET['idCiclo'];
$ciclo = obtenerCicloPorId($id_ciclo);

if (!$ciclo) {
    header("Location: verCiclos.php");
    exit;
}

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

// Get current assignments
$profesoresAsignadosRaw = obtenerProfesoresDeUnCiclo($id_ciclo);
$profesoresAsignados = [];
foreach ($profesoresAsignadosRaw as $p) { $profesoresAsignados[] = $p['idProfesor']; }

$aulasAsignadasRaw = obtenerAulasDeUnCiclo($id_ciclo);
$aulasAsignadas = [];
foreach ($aulasAsignadasRaw as $a) { $aulasAsignadas[] = $a['idAula']; }

if (isset($_SESSION['datos_ciclos'])) {
    // Basic array merge without using array_merge to keep it simple
    foreach ($_SESSION['datos_ciclos'] as $key => $value) {
        $ciclo[$key] = $value;
    }
}

$error = "";
if (isset($_SESSION['error'])) { $error = $_SESSION['error']; }

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_ciclos']);

$titulo_pagina = "Modificar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Modificar Ciclo: <?php echo $ciclo['nombreCiclo']; ?></h1>
    <a href="verCiclos.php" class="boton-secundario">← Volver</a>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/ciclos/actualizar.php">
        <input type="hidden" name="idCiclo" value="<?php echo $id_ciclo; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" value="<?php echo $ciclo['nombreCiclo']; ?>">
                <?php if (isset($lista_de_errores['nombreCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreCiclo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Abreviatura *</label>
                <input type="text" name="abreviaturaCiclo" maxlength="10" value="<?php echo $ciclo['abreviaturaCiclo']; ?>">
                <?php if (isset($lista_de_errores['abreviaturaCiclo'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['abreviaturaCiclo']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select name="idNivel">
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?php echo $nivel['idNivel']; ?>" <?php if ($ciclo['idNivel'] == $nivel['idNivel']) { echo 'selected'; } ?>>
                            <?php echo $nivel['nombreNivel']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($lista_de_errores['idNivel'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['idNivel']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Precio Total del Ciclo (€) *</label>
                <input type="number" name="precioCiclo" step="0.01" value="<?php echo $ciclo['precioCiclo']; ?>">
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
            <div>
                <h4 class="margen-abajo">Asignar Tutores/Profesores</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="profesores[]" value="<?php echo $prof['idProfesor']; ?>"
                                <?php if (in_array($prof['idProfesor'], $profesoresAsignados)) { echo 'checked'; } ?>>
                            <span><?php echo $prof['nombreProfesor']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="margen-abajo">Asignar Aulas Habituales</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaAulas as $aula) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="aulas[]" value="<?php echo $aula['idAula']; ?>"
                                <?php if (in_array($aula['idAula'], $aulasAsignadas)) { echo 'checked'; } ?>>
                            <span><?php echo $aula['nombreAula']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

