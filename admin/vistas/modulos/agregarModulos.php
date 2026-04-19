<?php
session_start();
$titulo_pagina = "Agregar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";

require_once "../../modelos/ciclos.php";

$listaCiclos = listarTodosLosCiclos();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_modulo'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_modulo']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Módulo</h1>
        <p class="subtitulo-encabezado">Crear un nuevo módulo profesional para la oferta formativa</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/modulos/verModulos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3>Datos del Módulo</h3>
    </div>
    <form action="controladores/modulos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="nombreModulo">Nombre del Módulo *</label>
                <input type="text" id="nombreModulo" name="nombreModulo" 
                       placeholder="Ej: Programación"
                       value="<?php if (isset($datos['nombreModulo'])) { echo $datos['nombreModulo']; } else { echo ''; } ?>"
                       class="<?php if (isset($errores['nombreModulo'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['nombreModulo'])) { ?>
                    <span class="error-campo"><?php echo $errores['nombreModulo']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="idCiclo">Ciclo Formativo *</label>
                <select id="idCiclo" name="idCiclo" class="<?php if (isset($errores['idCiclo'])) { echo 'input-error'; } else { echo ''; } ?>">
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" 
                            <?php if ((isset($datos['idCiclo']) && $datos['idCiclo'] == $ciclo['idCiclo'])) { echo 'selected'; } else { echo ''; } ?>>
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
                       value="<?php if (isset($datos['horasMaximas'])) { echo $datos['horasMaximas']; } else { echo '100'; } ?>"
                       class="<?php if (isset($errores['horasMaximas'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['horasMaximas'])) { ?>
                    <span class="error-campo"><?php echo $errores['horasMaximas']; ?></span>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarModulo" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Módulo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

