<?php
session_start();
require_once "../../modelos/directores.php";

$id = $_GET['id'] ?? 0;
$datosDirectorBD = obtenerDirectorPorId($id);

if (!$datosDirectorBD) {
    header("Location: verDirectores.php");
    exit;
}

// Datos y errores
$errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_director'] ?? [];
unset($_SESSION['errores'], $_SESSION['datos_director']);

// Variables simples (Estudiante way)
$nombre = $datos['nombreDirector'] ?? $datosDirectorBD['nombreDirector'];
$email = $datos['emailDirector'] ?? $datosDirectorBD['emailDirector'];
$dni = $datos['dniDirector'] ?? $datosDirectorBD['dniDirector'];
$fechaAlta = $datos['fechaAltaDirector'] ?? $datosDirectorBD['fechaAltaDirector'];

$titulo_pagina = "Modificar Director";
$seccion = 'directores';
include_once "../comunes/nav.php";
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Director: <?php echo $nombre; ?></h1>
    <a href="vistas/directores/verDirectores.php" class="boton-secundario">Cancelar</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/directores/actualizar.php" method="POST">
        <input type="hidden" name="idDirector" value="<?php echo $id; ?>">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?php echo $nombre; ?>">
                <?php if (isset($errores['nombreDirector'])) { ?>
                    <p class="error-campo"><?php echo $errores['nombreDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="email" name="emailDirector" value="<?php echo $email; ?>">
                <?php if (isset($errores['emailDirector'])) { ?>
                    <p class="error-campo"><?php echo $errores['emailDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?php echo $dni; ?>">
                <?php if (isset($errores['dniDirector'])) { ?>
                    <p class="error-campo"><?php echo $errores['dniDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha Alta *</label>
                <input type="date" name="fechaAltaDirector" value="<?php echo $fechaAlta; ?>">
                <?php if (isset($errores['fechaAltaDirector'])) { ?>
                    <p class="error-campo"><?php echo $errores['fechaAltaDirector']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarDirector" class="boton-primario">Guardar Cambios</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
