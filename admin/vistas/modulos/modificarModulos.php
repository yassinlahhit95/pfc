<?php
session_start();
$titulo_pagina = "Modificar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";
require_once "../../modelos/ciclos.php";

// Usamos el nombre descriptivo de la variable y del parametro GET
$idDelModulo = $_GET['idModulo'] ?? null;

if (!$idDelModulo) {
    header("Location: verModulos.php");
    exit;
}

$moduloActual = obtenerModuloPorId($idDelModulo);

if (!$moduloActual) {
    header("Location: verModulos.php");
    exit;
}

$listaDeCiclos = listarTodosLosCiclos();

$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['errores']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Módulo</h1>
        <p class="subtitulo-encabezado">Actualizando información de: <strong><?php echo $moduloActual['nombreModulo']; ?></strong></p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/verModulos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver al listado
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Datos del Módulo</h3>
    </div>
    <form action="controladores/modulos/actualizar.php" method="POST">
        <input type="hidden" name="idModulo" value="<?php echo $moduloActual['idModulo']; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="nombreModulo">Nombre del Módulo *</label>
                <input type="text" id="nombreModulo" name="nombreModulo" 
                       placeholder="Ej: Programación"
                       value="<?php echo $moduloActual['nombreModulo']; ?>"
                       class="<?php if (isset($errores['nombreModulo'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['nombreModulo'])) { ?>
                    <span class="error-campo"><?php echo $errores['nombreModulo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select id="idCiclo" name="idCiclo" class="<?php if (isset($errores['idCiclo'])) { echo 'input-error'; } else { echo ''; } ?>">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaDeCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" 
                            <?php if ($moduloActual['idCiclo'] == $ciclo['idCiclo']) { echo 'selected'; } else { echo ''; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
                <?php if (isset($errores['idCiclo'])) { ?>
                    <span class="error-campo"><?php echo $errores['idCiclo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="horasMaximas">Horas Máximas Anuales *</label>
                <input type="text" id="horasMaximas" name="horasMaximas" 
                       placeholder="Ej: 100"
                       value="<?php echo $moduloActual['horasMaximas']; ?>"
                       class="<?php if (isset($errores['horasMaximas'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['horasMaximas'])) { ?>
                    <span class="error-campo"><?php echo $errores['horasMaximas']; ?></span>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
