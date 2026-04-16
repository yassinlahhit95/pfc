<?php
session_start();
require_once "../../modelos/profesores.php";

$id = $_GET['id'] ?? 0;
$modeloProfesor = new profesor();
$datosProfesorBD = $modeloProfesor->obtenerProfesorPorIdModelo($id);

if (!$datosProfesorBD) {
    header("Location: verProfesores.php");
    exit;
}

// Lógica de errores y persistencia
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_profesor'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_profesor']);

$titulo_pagina = "Modificar Profesor";
$seccion = 'profesores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Profesor: <?php echo htmlspecialchars($datosProfesorBD['nombreProfesor']); ?></h1>
    <a href="vistas/profesores/verProfesores.php" class="boton-secundario">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../controladores/profesores/actualizar.php" method="POST">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idProfesor" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" 
                       value="<?php echo htmlspecialchars($datos['nombreProfesor'] ?? $datosProfesorBD['nombreProfesor']); ?>">
                <?php if (isset($errores['nombreProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailProfesor" 
                       value="<?php echo htmlspecialchars($datos['emailProfesor'] ?? $datosProfesorBD['emailProfesor']); ?>">
                <?php if (isset($errores['emailProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['emailProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" 
                       value="<?php echo htmlspecialchars($datos['dniProfesor'] ?? $datosProfesorBD['dniProfesor']); ?>">
                <?php if (isset($errores['dniProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['dniProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" 
                       value="<?php echo htmlspecialchars($datos['telefonoProfesor'] ?? $datosProfesorBD['telefonoProfesor']); ?>">
                <?php if (isset($errores['telefonoProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['telefonoProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Especialidad *</label>
                <input type="text" name="especialidad" 
                       value="<?php echo htmlspecialchars($datos['especialidad'] ?? $datosProfesorBD['especialidad']); ?>">
                <?php if (isset($errores['especialidad'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['especialidad']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" 
                       value="<?php echo htmlspecialchars($datos['direccionProfesor'] ?? $datosProfesorBD['direccionProfesor']); ?>">
                <?php if (isset($errores['direccionProfesor'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['direccionProfesor']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="idEstado">
                    <?php 
                    $estadoActual = $datos['idEstado'] ?? $datosProfesorBD['idEstado'];
                    ?>
                    <option value="1" <?php echo $estadoActual == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo $estadoActual == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
