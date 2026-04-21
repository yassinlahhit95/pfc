<?php
session_start();

require_once "../../../modelos/retos.php";
require_once "../../../modelos/modulos.php";

// Usamos el nombre descriptivo de la variable y del parametro GET
$idDelReto = 0;
if (isset($_GET['idReto'])) {
    $idDelReto = $_GET['idReto'];
}

if (!$idDelReto) {
    header("Location: verRetos.php");
    exit;
}

$reto = obtenerRetoPorId($idDelReto);

if (!$reto) {
    header("Location: verRetos.php");
    exit;
}

$modulosDelRetoActual = obtenerModulosDeReto($idDelReto);
$idsModulosSeleccionados = array_column($modulosDelRetoActual, 'idModulo');

$listaDeModulos = listarModulos();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}
unset($_SESSION['errores']);

$titulo_pagina = "Modificar Reto - Super Admin";
$seccion = 'retos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Modificar Reto: <?php echo $reto['nombreReto']; ?></h1>
        <p class="subtitulo-encabezado">Actualice la información y los módulos vinculados</p>
    </div>
    <a href="/pfc/vistas/admin/retos/verRetos.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> Volver a la lista
    </a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/retos/actualizar.php" method="POST">
        <input type="hidden" name="idReto" value="<?php echo $idDelReto; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario campo-ancho-total">
                <label>Nombre del Reto *</label>
                <input type="text" name="nombreReto" value="<?php echo $reto['nombreReto']; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Horas del Reto *</label>
                <input type="text" name="horasReto" value="<?php echo $reto['horasReto']; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Inicio *</label>
                <input type="date" name="fechaInicio" value="<?php echo $reto['fechaInicio']; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Finalización *</label>
                <input type="date" name="fechaFin" value="<?php echo $reto['fechaFin']; ?>" required>
            </div>
        </div>

        <div class="mt-25">
            <h4 class="margen-abajo">Vincular Módulos (Subproyectos)</h4>
            <div class="lista-checkboxes">
                <?php foreach ($listaDeModulos as $modulo) { ?>
                    <label class="item-checkbox">
                        <input type="checkbox" name="modulos[]" value="<?php echo $modulo['idModulo']; ?>"
                            <?php if (in_array($modulo['idModulo'], $idsModulosSeleccionados)) { echo 'checked'; } ?>>
                        <span><?php echo $modulo['nombreModulo']; ?> (<?php echo $modulo['nombreCiclo']; ?>)</span>
                    </label>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarReto" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>