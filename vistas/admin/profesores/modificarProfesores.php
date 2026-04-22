<?php
session_start();
require_once "../../../modelos/profesores.php";


$idDelProfesor = 0;
if (isset($_GET['idProfesor'])) {
    $idDelProfesor = $_GET['idProfesor'];
}

$datosProfesorBD = obtenerProfesorPorId($idDelProfesor);

if (!$datosProfesorBD) {
    header("Location: verProfesores.php");
    exit;
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_profesor'])) {
    $datos = $_SESSION['datos_profesor'];
}
unset($_SESSION['errores'], $_SESSION['datos_profesor']);


$nombre = $datosProfesorBD['nombreProfesor'];
if (isset($datos['nombreProfesor'])) {
    $nombre = $datos['nombreProfesor'];
}

$email = $datosProfesorBD['emailProfesor'];
if (isset($datos['emailProfesor'])) {
    $email = $datos['emailProfesor'];
}

$dni = $datosProfesorBD['dniProfesor'];
if (isset($datos['dniProfesor'])) {
    $dni = $datos['dniProfesor'];
}

$telefono = $datosProfesorBD['telefonoProfesor'];
if (isset($datos['telefonoProfesor'])) {
    $telefono = $datos['telefonoProfesor'];
}

$especialidad = $datosProfesorBD['especialidad'];
if (isset($datos['especialidad'])) {
    $especialidad = $datos['especialidad'];
}

$direccion = $datosProfesorBD['direccionProfesor'];
if (isset($datos['direccionProfesor'])) {
    $direccion = $datos['direccionProfesor'];
}

$titulo_pagina = "Modificar Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Profesor: <?php echo $nombre; ?></h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $idDelProfesor; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo $nombre; ?>">
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailProfesor" value="<?php echo $email; ?>">
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php echo $dni; ?>">
            </div>

            <div class="campo-formulario">
                <label>Teléfono</label>
                <input type="text" name="telefonoProfesor" value="<?php echo $telefono; ?>">
            </div>

            <div class="campo-formulario">
                <label>Especialidad</label>
                <input type="text" name="especialidad" value="<?php echo $especialidad; ?>">
            </div>

            <div class="campo-formulario">
                <label>Dirección</label>
                <input type="text" name="direccionProfesor" value="<?php echo $direccion; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
