<?php
session_start();
$titulo_pagina = "Nuevo Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_profesor'] ?? [];
$error_nombre = $_SESSION['error_nombre'] ?? "";
$error_email = $_SESSION['error_email'] ?? "";
$error_dni = $_SESSION['error_dni'] ?? "";

unset($_SESSION['datos_profesor'], $_SESSION['error_nombre'], $_SESSION['error_email'], $_SESSION['error_dni']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Profesor</h1>
    <a href="vistas/profesores/verProfesores.php" class="boton-gris">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/profesoresControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreProfesor" value="<?php echo $datos['nombreProfesor'] ?? ''; ?>">
                <?php if ($error_nombre != "") { echo "<p class='error-campo'>$error_nombre</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Email</label>
                <input type="text" name="emailProfesor" value="<?php echo $datos['emailProfesor'] ?? ''; ?>">
                <?php if ($error_email != "") { echo "<p class='error-campo'>$error_email</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI</label>
                <input type="text" name="dniProfesor" value="<?php echo $datos['dniProfesor'] ?? ''; ?>">
                <?php if ($error_dni != "") { echo "<p class='error-campo'>$error_dni</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono</label>
                <input type="text" name="telefonoProfesor" value="<?php echo $datos['telefonoProfesor'] ?? ''; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Profesor</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
