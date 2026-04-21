<?php
session_start();
$titulo_pagina = "Nuevo Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";

$datos = [];
if (isset($_SESSION['datos_profesor'])) {
    $datos = $_SESSION['datos_profesor'];
}

$errores = [];
if (isset($_SESSION['errores'])) {
    $errores = $_SESSION['errores'];
}
unset($_SESSION['datos_profesor'], $_SESSION['errores']);

// Variables simples
$nombre = '';
if (isset($datos['nombreProfesor'])) {
    $nombre = $datos['nombreProfesor'];
}

$email = '';
if (isset($datos['emailProfesor'])) {
    $email = $datos['emailProfesor'];
}

$dni = '';
if (isset($datos['dniProfesor'])) {
    $dni = $datos['dniProfesor'];
}

$telefono = '';
if (isset($datos['telefonoProfesor'])) {
    $telefono = $datos['telefonoProfesor'];
}

$especialidad = '';
if (isset($datos['especialidad'])) {
    $especialidad = $datos['especialidad'];
}

$direccion = '';
if (isset($datos['direccionProfesor'])) {
    $direccion = $datos['direccionProfesor'];
}
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Profesor</h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/profesores/insertar.php" method="POST">
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
            <button type="submit" name="guardarProfesor" class="boton-primario">Registrar Profesor</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>