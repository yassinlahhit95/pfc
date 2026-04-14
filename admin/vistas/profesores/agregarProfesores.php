<?php
session_start();
$titulo_pagina = "Nuevo Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_profesor'] ?? [];
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['datos_profesor'], $_SESSION['errores']);
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
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo htmlspecialchars($datos['nombreProfesor'] ?? ''); ?>">
                <?php if (isset($errores['nombreProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailProfesor" value="<?php echo htmlspecialchars($datos['emailProfesor'] ?? ''); ?>">
                <?php if (isset($errores['emailProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['emailProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php echo htmlspecialchars($datos['dniProfesor'] ?? ''); ?>">
                <?php if (isset($errores['dniProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['dniProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" value="<?php echo htmlspecialchars($datos['telefonoProfesor'] ?? ''); ?>">
                <?php if (isset($errores['telefonoProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['telefonoProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Especialidad *</label>
                <input type="text" name="especialidad" value="<?php echo htmlspecialchars($datos['especialidad'] ?? ''); ?>">
                <?php if (isset($errores['especialidad'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['especialidad']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" value="<?php echo htmlspecialchars($datos['direccionProfesor'] ?? ''); ?>">
                <?php if (isset($errores['direccionProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['direccionProfesor']; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-azul">Guardar Profesor</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
