<?php
session_start();
require_once "../../../modelos/directores.php";

$id = 0;
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$datosDirectorBD = obtenerDirectorPorId($id);

if (!$datosDirectorBD) {
    header("Location: verDirectores.php");
    exit;
}

// Datos y errores
$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_director'])) {
    $datos = $_SESSION['datos_director'];
}
unset($_SESSION['errores'], $_SESSION['datos_director']);

// Variables simples
$nombre = $datosDirectorBD['nombreDirector'];
if (isset($datos['nombreDirector'])) {
    $nombre = $datos['nombreDirector'];
}

$email = $datosDirectorBD['emailDirector'];
if (isset($datos['emailDirector'])) {
    $email = $datos['emailDirector'];
}

$dni = $datosDirectorBD['dniDirector'];
if (isset($datos['dniDirector'])) {
    $dni = $datos['dniDirector'];
}

$fechaAlta = $datosDirectorBD['fechaAltaDirector'];
if (isset($datos['fechaAltaDirector'])) {
    $fechaAlta = $datos['fechaAltaDirector'];
}

$titulo_pagina = "Modificar Director";
$seccion = 'directores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Director: <?php echo $nombre; ?></h1>
    <a href="/pfc/vistas/admin/directores/verDirectores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/actualizar.php" method="POST">
        <input type="hidden" name="idDirector" value="<?php echo $id; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailDirector" value="<?php echo $email; ?>">
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?php echo $dni; ?>">
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaDirector" value="<?php echo $fechaAlta; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarDirector" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>