<?php
session_start();
require_once "../../modelos/ciclos.php";

$listaCiclos = listarTodosLosCiclos();

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_modulo'])) {
    $datos = $_SESSION['datos_modulo'];
}
unset($_SESSION['errores'], $_SESSION['datos_modulo']);

// Variables simples
$nombre = '';
if (isset($datos['nombreModulo'])) {
    $nombre = $datos['nombreModulo'];
}

$idCicloElegido = '';
if (isset($datos['idCiclo'])) {
    $idCicloElegido = $datos['idCiclo'];
}

$horas = 0;
if (isset($datos['horasMaximas'])) {
    $horas = $datos['horasMaximas'];
}

$titulo_pagina = "Agregar Módulo - Super Admin";
$seccion = 'modulos';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Nuevo Módulo Profesional</h1>
    <a href="vistas/modulos/verModulos.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/modulos/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre del Módulo *</label>
                <input type="text" name="nombreModulo" value="<?php echo $nombre; ?>" required>
            </div>

            <div class="campo-formulario">
                <label>Ciclo Formativo *</label>
                <select name="idCiclo" required>
                    <option value="">-- Seleccionar Ciclo --</option>
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <option value="<?php echo $ciclo['idCiclo']; ?>" <?php if ($idCicloElegido == $ciclo['idCiclo']) { echo 'selected'; } ?>>
                            <?php echo $ciclo['nombreCiclo']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="campo-formulario">
                <label>Horas Totales *</label>
                <input type="text" name="horasMaximas" value="<?php echo $horas; ?>" required>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarModulo" class="boton-primario">Registrar Módulo</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>