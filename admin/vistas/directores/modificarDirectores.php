<?php
session_start();
require_once "../../modelos/conexion.php";
require_once "../../modelos/directores.php";

$id = $_GET['id'] ?? 0;
$objetoConexion = new Conexion();
$conexionBD = $objetoConexion->conectar();

$modeloDirector = new director($conexionBD);
$datosDirectorBD = $modeloDirector->obtenerDirectorPorIdModelo($id);

if (!$datosDirectorBD) {
    header("Location: verDirectores.php");
    exit;
}

// Lógica de errores y persistencia
$errores = $_SESSION['errores'] ?? [];
$datosViejos = $_SESSION['datos_viejos'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_viejos']);

$titulo_pagina = "Modificar Director - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Director: <?php echo htmlspecialchars($datosDirectorBD['nombreDirector']); ?></h1>
    <a href="vistas/directores/verDirectores.php" class="boton-gris">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/directoresControlador.php" method="POST">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="idDirector" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreDirector" 
                       class="<?php echo isset($errores['nombreDirector']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['nombreDirector'] ?? $datosDirectorBD['nombreDirector']); ?>">
                <?php if (isset($errores['nombreDirector'])) echo "<p class='error-campo'>{$errores['nombreDirector']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Email</label>
                <input type="text" name="emailDirector" 
                       class="<?php echo isset($errores['emailDirector']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['emailDirector'] ?? $datosDirectorBD['emailDirector']); ?>">
                <?php if (isset($errores['emailDirector'])) echo "<p class='error-campo'>{$errores['emailDirector']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono</label>
                <input type="text" name="telefonoDirector" 
                       value="<?php echo htmlspecialchars($datosViejos['telefonoDirector'] ?? $datosDirectorBD['telefonoDirector']); ?>">
            </div>

            <div class="campo-formulario">
                <label>DNI</label>
                <input type="text" name="dniDirector" 
                       class="<?php echo isset($errores['dniDirector']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['dniDirector'] ?? $datosDirectorBD['dniDirector']); ?>">
                <?php if (isset($errores['dniDirector'])) echo "<p class='error-campo'>{$errores['dniDirector']}</p>"; ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad</label>
                <input type="text" name="ciudadDirector" 
                       value="<?php echo htmlspecialchars($datosViejos['ciudadDirector'] ?? $datosDirectorBD['ciudadDirector']); ?>">
            </div>

            <div class="campo-formulario">
                <label>Código Postal</label>
                <input type="text" name="codigoPostalDirector" 
                       class="<?php echo isset($errores['codigoPostalDirector']) ? 'input-error' : ''; ?>"
                       value="<?php echo htmlspecialchars($datosViejos['codigoPostalDirector'] ?? $datosDirectorBD['codigoPostalDirector']); ?>">
                <?php if (isset($errores['codigoPostalDirector'])) echo "<p class='error-campo'>{$errores['codigoPostalDirector']}</p>"; ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección</label>
                <input type="text" name="direccionDirector" 
                       value="<?php echo htmlspecialchars($datosViejos['direccionDirector'] ?? $datosDirectorBD['direccionDirector']); ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
