<?php
session_start();
$titulo_pagina = "Agregar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../modelos/modulos.php";

$listaModulos = listarModulos();

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_reto'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_reto']);
?>

<div class="encabezado-pagina">
    <div>
        <h1>Agregar Reto</h1>
        <p class="subtitulo-encabezado">Diseñar un nuevo reto educativo para el alumnado</p>
    </div>
    <div class="acciones-pagina">
        <a href="vistas/retos/verRetos.php" class="boton-secundario">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>

<div class="tarjeta-blanca">
    <div class="titulo-tarjeta">
        <h3><i class="fas fa-tasks color-primary mr-10"></i> Configuración del Reto</h3>
    </div>
    <form action="controladores/retos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label for="nombreReto">Nombre del Reto *</label>
                <input type="text" id="nombreReto" name="nombreReto" 
                       placeholder="Ej: Desarrollo de una API REST"
                       value="<?php if (isset($datos['nombreReto'])) { echo $datos['nombreReto']; } else { echo ''; } ?>"
                       class="<?php if (isset($errores['nombreReto'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['nombreReto'])) { ?>
                    <span class="error-campo"><?php echo $errores['nombreReto']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaInicio">Fecha de Inicio *</label>
                <input type="date" id="fechaInicio" name="fechaInicio" 
                       value="<?php if (isset($datos['fechaInicio'])) { echo $datos['fechaInicio']; } else { echo date('Y-m-d'); } ?>"
                       class="<?php if (isset($errores['fechaInicio'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['fechaInicio'])) { ?>
                    <span class="error-campo"><?php echo $errores['fechaInicio']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaFin">Fecha de Finalización *</label>
                <input type="date" id="fechaFin" name="fechaFin" 
                       value="<?php if (isset($datos['fechaFin'])) { echo $datos['fechaFin']; } else { echo date('Y-m-d'); } ?>"
                       class="<?php if (isset($errores['fechaFin'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['fechaFin'])) { ?>
                    <span class="error-campo"><?php echo $errores['fechaFin']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="horasReto">Carga Horaria (Horas) *</label>
                <input type="text" id="horasReto" name="horasReto" 
                       placeholder="Ej: 20"
                       value="<?php if (isset($datos['horasReto'])) { echo $datos['horasReto']; } else { echo '10'; } ?>"
                       class="<?php if (isset($errores['horasReto'])) { echo 'input-error'; } else { echo ''; } ?>">
                <?php if (isset($errores['horasReto'])) { ?>
                    <span class="error-campo"><?php echo $errores['horasReto']; ?></span>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Módulos Asociados * <span class="texto-atenuado">(Selecciona al menos uno)</span></label>
                <div class="tarjeta-gris-suave scroll-vertical">
                    <?php if (empty($listaModulos)) { ?>
                        <p class="sin-datos">No hay módulos disponibles. Crea uno primero.</p>
                    <?php } else { ?>
                        <div class="formulario-cuadricula">
                            <?php foreach ($listaModulos as $modulo) { ?>
                                <label class="item-seleccionable tarjeta-blanca sin-margen p-0">
                                    <div class="disposicion-flexible alinear-centro separacion-pequena p-10">
                                        <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>" 
                                            <?php if ((isset($datos['modulos']) && in_array($modulo['idModulo'], $datos['modulos']))) { echo 'checked'; } else { echo ''; } ?>>
                                        <span class="texto-pequeno">
                                            <strong><?php echo $modulo['nombreModulo']; ?></strong><br>
                                            <small class="texto-atenuado"><?php echo $modulo['nombreCiclo']; ?></small>
                                        </span>
                                    </div>
                                </label>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
                <?php if (isset($errores['modulos'])) { ?>
                    <span class="error-campo"><?php echo $errores['modulos']; ?></span>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto Educativo
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
