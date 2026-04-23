<?php
session_start();
$titulo_pagina = "Nuevo Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/modulos.php";

$todos_los_modulos = listarModulos();

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

$datos = [];
if (isset($_SESSION['datos_reto'])) { $datos = $_SESSION['datos_reto']; }

unset($_SESSION['errores'], $_SESSION['datos_reto']);
?>

<div class="encabezado-pagina">
    <h1>Crear Nuevo Reto</h1>
    <a href="/pfc/vistas/admin/retos/verRetos.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/retos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?php if(isset($datos['nombreReto'])) echo $datos['nombreReto']; ?>">
                <?php if (isset($lista_de_errores['nombreReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales Estimadas *</label>
                <input type="text" name="horasReto" value="<?php if(isset($datos['horasReto'])) echo $datos['horasReto']; ?>">
                <?php if (isset($lista_de_errores['horasReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['horasReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <input type="date" name="fechaInicioReto" min="<?php echo date('Y-m-d'); ?>" value="<?php if(isset($datos['fechaInicioReto'])) echo $datos['fechaInicioReto']; ?>">
                <?php if (isset($lista_de_errores['fechaInicioReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaInicioReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Fin *</label>
                <input type="date" name="fechaFinReto" min="<?php echo date('Y-m-d'); ?>" value="<?php if(isset($datos['fechaFinReto'])) echo $datos['fechaFinReto']; ?>">
                <?php if (isset($lista_de_errores['fechaFinReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaFinReto']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <label><strong>Vincular Módulos (Obligatorio seleccionar al menos uno) *</strong></label>
            <div class="tarjeta-gris-suave scroll-vertical mt-5">
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <div class="item-seleccionable">
                        <input type="checkbox" name="modulosReto[]" value="<?php echo $modulo['idModulo']; ?>" 
                            <?php if(isset($datos['modulosReto']) && in_array($modulo['idModulo'], $datos['modulosReto'])) echo "checked"; ?>>
                        <span><?php echo $modulo['nombreModulo']; ?> (<?php echo $modulo['nombreCiclo']; ?>)</span>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($lista_de_errores['modulosReto'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['modulosReto']; ?></p>
            <?php } ?>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarReto" class="boton-primario">
                <i class="fas fa-save"></i> Crear Reto
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
