<?php
session_start();
$titulo_pagina = "Nuevo Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_profesor'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['datos_profesor'], $_SESSION['errores']);

// Variables simples (Estudiante way)
$nombre = $datos['nombreProfesor'] ?? '';
$email = $datos['emailProfesor'] ?? '';
$dni = $datos['dniProfesor'] ?? '';
$telefono = $datos['telefonoProfesor'] ?? '';
$especialidad = $datos['especialidad'] ?? '';
$direccion = $datos['direccionProfesor'] ?? '';
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Profesor</h1>
    <a href="vistas/profesores/verProfesores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/profesores/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo $nombre; ?>">
                <?php if (isset($errores['nombreProfesor'])) { ?>
                    <p class="error-campo"><?php echo $errores['nombreProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailProfesor" value="<?php echo $email; ?>">
                <?php if (isset($errores['emailProfesor'])) { ?>
                    <p class="error-campo"><?php echo $errores['emailProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php echo $dni; ?>">
                <?php if (isset($errores['dniProfesor'])) { ?>
                    <p class="error-campo"><?php echo $errores['dniProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" value="<?php echo $telefono; ?>">
                <?php if (isset($errores['telefonoProfesor'])) { ?>
                    <p class="error-campo"><?php echo $errores['telefonoProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Especialidad *</label>
                <input type="text" name="especialidad" value="<?php echo $especialidad; ?>">
                <?php if (isset($errores['especialidad'])) { ?>
                    <p class="error-campo"><?php echo $errores['especialidad']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" value="<?php echo $direccion; ?>">
                <?php if (isset($errores['direccionProfesor'])) { ?>
                    <p class="error-campo"><?php echo $errores['direccionProfesor']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-primario">Guardar Profesor</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
