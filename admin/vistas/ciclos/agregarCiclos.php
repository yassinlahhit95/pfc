<?php
session_start();
require_once "../../../modelos/niveles.php";
require_once "../../../modelos/profesores.php";
require_once "../../../modelos/aulas.php";

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_ciclo'])) {
    $datos = $_SESSION['datos_ciclo'];
}
unset($_SESSION['errores'], $_SESSION['datos_ciclo']);

// Variables simples
$nombre = '';
if (isset($datos['nombreCiclo'])) {
    $nombre = $datos['nombreCiclo'];
}

$idNivelElegido = '';
if (isset($datos['idNivel'])) {
    $idNivelElegido = $datos['idNivel'];
}

$profesoresElegidos = [];
if (isset($datos['profesores'])) {
    $profesoresElegidos = $datos['profesores'];
}

$aulasElegidas = [];
if (isset($datos['aulas'])) {
    $aulasElegidas = $datos['aulas'];
}

$descripcion = '';
if (isset($datos['descripcionCiclo'])) {
    $descripcion = $datos['descripcionCiclo'];
}

$titulo_pagina = "Agregar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Ciclo</h1>
        <p class="subtitulo-encabezado">Defina un nuevo programa formativo y asigne recursos</p>
    </div>
    <a href="vistas/ciclos/verCiclos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/ciclos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Ciclo *</label>
                <input type="text" name="nombreCiclo" placeholder="Ej: Desarrollo de Aplicaciones Web" value="<?php echo $nombre; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Nivel Formativo *</label>
                <select name="idNivel" required>
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach ($listaNiveles as $nivel) { ?>
                        <option value="<?php echo $nivel['idNivel']; ?>" <?php if ($idNivelElegido == $nivel['idNivel']) { echo 'selected'; } ?>>
                            <?php echo $nivel['nombreNivel']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Descripción del Ciclo *</label>
                <textarea name="descripcionCiclo" rows="3" placeholder="Resumen del programa formativo..."><?php echo $descripcion; ?></textarea>
            </div>
        </div>

        <div class="cuadricula-secundaria mt-25">
            <div>
                <h4 class="margen-abajo">Asignar Tutores/Profesores</h4>
                <div class="lista-checkboxes">
                    <?php foreach ($listaProfesores as $prof) { ?>
                        <label class="item-checkbox">
                            <input type="checkbox" name="profesores[]" value="<?php echo $prof['idProfesor']; ?>"
                                <?php if (in_array($prof['idProfesor'], $profesoresElegidos)) { echo 'checked'; } ?>>
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
                                <?php if (in_array($aula['idAula'], $aulasElegidas)) { echo 'checked'; } ?>>
                            <span><?php echo $aula['nombreAula']; ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarCiclo" class="boton-primario">
                <i class="fas fa-save"></i> Crear Ciclo Formativo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>