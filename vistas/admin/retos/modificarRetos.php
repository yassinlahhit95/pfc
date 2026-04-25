<?php
session_start();
$titulo_pagina = "Modificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";

require_once "../../../modelos/retos.php";
require_once "../../../modelos/modulos.php";

$id_reto = $_GET['idReto'];
$reto = obtenerRetoPorId($id_reto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulos_del_reto = obtenerModulosDeReto($id_reto);
$ids_modulos_viculados = [];
foreach ($modulos_del_reto as $m) {
    $ids_modulos_viculados[] = $m['idModulo'];
}

if (isset($_SESSION['datos_reto'])) {
    $reto = $_SESSION['datos_reto'];
    if (isset($reto['modulosReto'])) {
        $ids_modulos_viculados = $reto['modulosReto'];
    } else {
        $ids_modulos_viculados = [];
    }
}

$todos_los_modulos = listarModulos();

$error = $_SESSION['error'] ?? "";
$exito = $_SESSION['exito'] ?? "";
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_reto']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Reto</h1>
    <a href="verRetos.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>
<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form method="POST" action="/pfc/controladores/admin/retos/actualizar.php">
        <input type="hidden" name="idReto" value="<?php echo $id_reto; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?php echo $reto['nombreReto']; ?>">
                <?php if (isset($lista_de_errores['nombreReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales Estimadas *</label>
                <input type="text" name="horasReto" value="<?php echo $reto['horasReto']; ?>">
                <?php if (isset($lista_de_errores['horasReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['horasReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <input type="date" name="fechaInicioReto" value="<?php echo $reto['fechaInicioReto']; ?>">
                <?php if (isset($lista_de_errores['fechaInicioReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaInicioReto']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Fin *</label>
                <input type="date" name="fechaFinReto" value="<?php echo $reto['fechaFinReto']; ?>">
                <?php if (isset($lista_de_errores['fechaFinReto'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaFinReto']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <label><strong>Vincular Módulos *</strong></label>
            <div class="tarjeta-gris-suave scroll-vertical mt-5">
                <?php foreach ($todos_los_modulos as $modulo) { ?>
                    <div class="item-seleccionable">
                        <input type="checkbox" name="modulosReto[]" value="<?php echo $modulo['idModulo']; ?>" 
                            <?php if(in_array($modulo['idModulo'], $ids_modulos_viculados)) echo "checked"; ?>>
                        <span><?php echo $modulo['nombreModulo']; ?> (<?php echo $modulo['nombreCiclo']; ?>)</span>
                    </div>
                <?php } ?>
            </div>
            <?php if (isset($lista_de_errores['modulosReto'])) { ?>
                <p class="error-campo"><?php echo $lista_de_errores['modulosReto']; ?></p>
            <?php } ?>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReto" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
