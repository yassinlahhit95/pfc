<?php
session_start();

require_once "../../../modelos/ciclos.php";
require_once "../../../modelos/niveles.php";
require_once "../../../modelos/profesores.php";
require_once "../../../modelos/aulas.php";

// Usamos el nombre descriptivo de la variable y del parametro GET
$idDelCiclo = 0;
if (isset($_GET['idCiclo'])) {
    $idDelCiclo = $_GET['idCiclo'];
}

if (!$idDelCiclo) {
    header("Location: verCiclos.php");
    exit;
}

$datosCicloBD = obtenerCicloUnico($idDelCiclo);

if (!$datosCicloBD) {
    header("Location: verCiclos.php");
    exit;
}

$profesoresDelCicloActual = obtenerProfesoresDeUnCiclo($idDelCiclo);
$idsProfesoresSeleccionados = array_column($profesoresDelCicloActual, 'idProfesor');

$aulasDelCicloActual = obtenerAulasDeUnCiclo($idDelCiclo);
$idsAulasSeleccionadas = array_column($aulasDelCicloActual, 'idAula');

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}
unset($_SESSION['errores']);

$titulo_pagina = "Modificar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Ciclo: <?php echo $datosCicloBD['nombreCiclo']; ?></h1>
    </div>
    <a href="/pfc/vistas/admin/ciclos/verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/ciclos/actualizar.php" method="POST">
        <input type="hidden" name="idCiclo" value="<?php echo $idDelCiclo; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" value="<?php echo $datosCicloBD['nombreCiclo']; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select name="idNivel" required>
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?php echo $nivel['idNivel']; ?>" <?php if ($datosCicloBD['idNivel'] == $nivel['idNivel']) { echo 'selected'; } ?>>
                            <?php echo $nivel['nombreNivel']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción del Ciclo *</label>
                <textarea name="descripcionCiclo" rows="3" required><?php echo $datosCicloBD['descripcionCiclo']; ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
            <div>
                <h4 class="margen-abajo">Vincular Tutores/Profesores</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="profesores[]" value="<?php echo $prof['idProfesor']; ?>"
                                <?php if (in_array($prof['idProfesor'], $idsProfesoresSeleccionados)) { echo 'checked'; } ?>>
                            <span><?php echo $prof['nombreProfesor']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 class="margen-abajo">Vincular Aulas Habituales</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaAulas as $aula) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="aulas[]" value="<?php echo $aula['idAula']; ?>"
                                <?php if (in_array($aula['idAula'], $idsAulasSeleccionadas)) { echo 'checked'; } ?>>
                            <span><?php echo $aula['nombreAula']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>