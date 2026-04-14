<?php
session_start();
require_once "../../modelos/conexion.php";
require_once "../../modelos/profesores.php";

$id = $_GET['id'] ?? 0;
$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloProfesor = new profesor($conexionBD);
$datosProfesorBD = $modeloProfesor->obtenerProfesorPorIdModelo($id);

if (!$datosProfesorBD) {
    header("Location: verProfesores.php");
    exit;
}

// Lógica de errores y persistencia
$errores = $_SESSION['errores'] ?? [];
$datosViejos = $_SESSION['datos_viejos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_viejos']);

$titulo_pagina = "Modificar Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Profesor: <?php echo htmlspecialchars($datosProfesorBD['nombreProfesor']); ?></h1>
    <a href="vistas/profesores/verProfesores.php" class="boton-gris">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/profesoresControlador.php" method="POST">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idProfesor" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreProfesor" 
                       class="<?php echo isset($errores['nombreProfesor']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['nombreProfesor'] ?? $datosProfesorBD['nombreProfesor']); ?>">
                <?php if (isset($errores['nombreProfesor'])) echo "<p class='error-campo'>{$errores['nombreProfesor']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Correo Electrónico</label>
                <input type="text" name="emailProfesor" 
                       class="<?php echo isset($errores['emailProfesor']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['emailProfesor'] ?? $datosProfesorBD['emailProfesor']); ?>">
                <?php if (isset($errores['emailProfesor'])) echo "<p class='error-campo'>{$errores['emailProfesor']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono</label>
                <input type="text" name="telefonoProfesor" 
                       class="<?php echo isset($errores['telefonoProfesor']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['telefonoProfesor'] ?? $datosProfesorBD['telefonoProfesor']); ?>">
                <?php if (isset($errores['telefonoProfesor'])) echo "<p class='error-campo'>{$errores['telefonoProfesor']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI</label>
                <input type="text" name="dniProfesor" 
                       class="<?php echo isset($errores['dniProfesor']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['dniProfesor'] ?? $datosProfesorBD['dniProfesor']); ?>">
                <?php if (isset($errores['dniProfesor'])) echo "<p class='error-campo'>{$errores['dniProfesor']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Estado</label>
                <select name="idEstado">
                    <?php 
                    $estadoActual = $datosViejos['idEstado'] ?? $datosProfesorBD['idEstado'];
                    ?>
                    <option value="1" <?php echo $estadoActual == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo $estadoActual == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionProfesor" 
                       value="<?php echo htmlspecialchars($datosViejos['direccionProfesor'] ?? $datosProfesorBD['direccionProfesor']); ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
