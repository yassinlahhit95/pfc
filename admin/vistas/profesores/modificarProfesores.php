<?php
session_start();
require_once "../../modelos/profesores.php";

// Usamos idProfesor para ser claros
$idDelProfesor = $_GET['idProfesor'] ?? 0;
$datosProfesorBD = obtenerProfesorPorId($idDelProfesor);

if (!$datosProfesorBD) {
    header("Location: verProfesores.php");
    exit;
}

$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_profesor'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_profesor']);

// Variables simples (Estudiante way)
$nombre = $datos['nombreProfesor'] ?? $datosProfesorBD['nombreProfesor'];
$email = $datos['emailProfesor'] ?? $datosProfesorBD['emailProfesor'];
$dni = $datos['dniProfesor'] ?? $datosProfesorBD['dniProfesor'];
$telefono = $datos['telefonoProfesor'] ?? $datosProfesorBD['telefonoProfesor'];
$especialidad = $datos['especialidad'] ?? $datosProfesorBD['especialidad'];
$direccion = $datos['direccionProfesor'] ?? $datosProfesorBD['direccionProfesor'];

$titulo_pagina = "Modificar Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Profesor: <?php echo $nombre; ?></h1>
    <a href="vistas/profesores/verProfesores.php" class="boton-secundario">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $idDelProfesor; ?>">

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
            <button type="submit" name="guardarProfesor" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
