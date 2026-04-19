<?php
session_start();
$titulo_pagina = "Modificar Ciclo - Super Admin";
$seccion = 'ciclos';
include_once "../comunes/nav.php";

require_once "../../modelos/conectar.php";
require_once "../../modelos/ciclos.php";
require_once "../../modelos/niveles.php";
require_once "../../modelos/profesores.php";
require_once "../../modelos/aulas.php";

// Usamos el nombre descriptivo de la variable y del parametro GET
$idDelCiclo = $_GET['idCiclo'] ?? null;

if (!$idDelCiclo) {
    header("Location: verCiclos.php");
    exit;
}

$cicloActual = obtenerCicloUnico($idDelCiclo);

if (!$cicloActual) {
    header("Location: verCiclos.php");
    exit;
}


$profesoresSeleccionados = array_column(obtenerProfesoresDeUnCiclo($idDelCiclo), 'idProfesor');
$aulasSeleccionadas = array_column(obtenerAulasDeUnCiclo($idDelCiclo), 'idAula');

$listaNiveles = listarNiveles();
$listaProfesores = listarProfesores();
$listaAulas = listarAulas();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Ciclo</h1>
        <p class="subtitulo-encabezado">Actualizando la información de: <strong><?php echo $cicloActual['nombreCiclo']; ?></strong></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/ciclos/verCiclos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-edit color-primary mr-10"></i> Edición de Recursos del Ciclo</h3>
    </div>
    <form action="controladores/ciclos/actualizar.php" method="POST">
        <input type="hidden" name="idCiclo" value="<?php echo $cicloActual['idCiclo']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label for="nombreCiclo">Nombre del Ciclo *</label>
                <input type="text" id="nombreCiclo" name="nombreCiclo" 
                       placeholder="Ej: Desarrollo de Aplicaciones Web"
                       value="<?php echo $cicloActual['nombreCiclo']; ?>"
                       class="<?php if (isset($errores['nombreCiclo'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['nombreCiclo'])) { ?>
                    <span class="error-campo"><?php echo $errores['nombreCiclo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idNivel">Nivel Educativo *</label>
                <select id="idNivel" name="idNivel" class="<?php if (isset($errores['idNivel'])) { echo 'input-error'; } else { echo ''; } ?>">
                    <option value="">-- Seleccionar Nivel --</option>
                    <?php foreach($listaNiveles as $nivel) { 
                        $selected = ($cicloActual['idNivel'] == $nivel['idNivel']) ? 'selected' : '';
                        echo "<option value='{$nivel['idNivel']}' {$selected}>" . $nivel['nombreNivel'] . "</option>";
                    } ?>
                </select>
                <?php if (isset($errores['idNivel'])) { ?>
                    <span class="error-campo"><?php echo $errores['idNivel']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idEstado">Estado del Ciclo *</label>
                <select id="idEstado" name="idEstado">
                    <option value="1" <?php if ($cicloActual['idEstado'] == 1) { echo 'selected'; } else { echo ''; } ?>>Activo</option>
                    <option value="2" <?php if ($cicloActual['idEstado'] == 2) { echo 'selected'; } else { echo ''; } ?>>Inactivo</option>
                </select>
                <?php if (isset($errores['idEstado'])) { ?>
                    <span class="error-campo"><?php echo $errores['idEstado']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Profesores Tutores * <span class="texto-atenuado">(Selecciona uno o más)</span></label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <?php foreach($listaProfesores as $profesor) { 
                        $checked = in_array($profesor['idProfesor'], $profesoresSeleccionados) ? 'checked' : '';
                    ?>
                        <label class="item-seleccionable">
                            <input type="checkbox" name="profesores[]" value="<?php echo $profesor['idProfesor']; ?>" id="profe_<?php echo $profesor['idProfesor']; ?>" <?php echo $checked; ?>>
                            <span class="texto-pequeno"><?php echo $profesor['nombreProfesores']; ?></span>
                        </label>
                    <?php } ?>
                </div>
                <?php if (isset($errores['profesores'])) { ?>
                    <span class="error-campo"><?php echo $errores['profesores']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Aulas Asignadas * <span class="texto-atenuado">(Selecciona una o más)</span></label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <?php foreach($listaAulas as $aula) { 
                        $checked = in_array($aula['idAula'], $aulasSeleccionadas) ? 'checked' : '';
                    ?>
                        <label class="item-seleccionable">
                            <input type="checkbox" name="aulas[]" value="<?php echo $aula['idAula']; ?>" id="aula_<?php echo $aula['idAula']; ?>" <?php echo $checked; ?>>
                            <span class="texto-pequeno"><?php echo $aula['nombreAula']; ?></span>
                        </label>
                    <?php } ?>
                </div>
                <?php if (isset($errores['aulas'])) { ?>
                    <span class="error-campo"><?php echo $errores['aulas']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="descripcionCiclo">Descripción del Ciclo *</label>
                <textarea id="descripcionCiclo" name="descripcionCiclo" rows="4" 
                          placeholder="Escribe una breve descripción del ciclo..."
                          class="<?php if (isset($errores['descripcionCiclo'])) { echo 'input-error'; } else { echo ''; } ?>"><?php echo $cicloActual['descripcionCiclo']; ?></textarea>
                <?php if (isset($errores['descripcionCiclo'])) { ?>
                    <span class="error-campo"><?php echo $errores['descripcionCiclo']; ?></span>
                <?php } ?>
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
