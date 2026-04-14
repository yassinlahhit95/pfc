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
$datos = $_SESSION['datos_director'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_director']);

$titulo_pagina = "Modificar Director";
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
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" 
                       value="<?php echo htmlspecialchars($datos['nombreDirector'] ?? $datosDirectorBD['nombreDirector']); ?>">
                <?php if (isset($errores['nombreDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['nombreDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailDirector" 
                       value="<?php echo htmlspecialchars($datos['emailDirector'] ?? $datosDirectorBD['emailDirector']); ?>">
                <?php if (isset($errores['emailDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['emailDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" 
                       value="<?php echo htmlspecialchars($datos['dniDirector'] ?? $datosDirectorBD['dniDirector']); ?>">
                <?php if (isset($errores['dniDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['dniDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" 
                       value="<?php echo htmlspecialchars($datos['telefonoDirector'] ?? $datosDirectorBD['telefonoDirector']); ?>">
                <?php if (isset($errores['telefonoDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['telefonoDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionDirector" 
                       value="<?php echo htmlspecialchars($datos['direccionDirector'] ?? $datosDirectorBD['direccionDirector']); ?>">
                <?php if (isset($errores['direccionDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['direccionDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadDirector" 
                       value="<?php echo htmlspecialchars($datos['ciudadDirector'] ?? $datosDirectorBD['ciudadDirector']); ?>">
                <?php if (isset($errores['ciudadDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['ciudadDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalDirector" 
                       value="<?php echo htmlspecialchars($datos['codigoPostalDirector'] ?? $datosDirectorBD['codigoPostalDirector']); ?>">
                <?php if (isset($errores['codigoPostalDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['codigoPostalDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaDirector" 
                       value="<?php echo htmlspecialchars($datos['fechaAltaDirector'] ?? $datosDirectorBD['fechaAltaDirector']); ?>">
                <?php if (isset($errores['fechaAltaDirector'])): ?>
                    <p style="color: red; font-size: 13px; margin-top: 5px;"><?php echo $errores['fechaAltaDirector']; ?></p>
                <?php endif; ?>
            </div>

            <div class="campo-formulario">
                <label>Estado *</label>
                <select name="idEstado">
                    <?php 
                    $estadoActual = $datos['idEstado'] ?? $datosDirectorBD['idEstado'];
                    ?>
                    <option value="1" <?php echo $estadoActual == 1 ? 'selected' : ''; ?>>Activo</option>
                    <option value="2" <?php echo $estadoActual == 2 ? 'selected' : ''; ?>>Inactivo</option>
                </select>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarDirector" class="boton-azul">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
