<?php
session_start();
$titulo_pagina = "Nuevo Director";
$seccion = 'directores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_director'] ?? [];
$errores = $_SESSION['errores'] ?? [];
unset($_SESSION['datos_director'], $_SESSION['errores']);

// Variables simples (Estudiante way)
$nombre = $datos['nombreDirector'] ?? '';
$email = $datos['emailDirector'] ?? '';
$dni = $datos['dniDirector'] ?? '';
$fechaAlta = $datos['fechaAltaDirector'] ?? date('Y-m-d');
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Director</h1>
    <a href="vistas/directores/verDirectores.php" class="boton-secundario">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controladores/directores/insertar.php" method="POST">
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
            <button type="submit" name="guardarDirector" class="boton-primario">Guardar Director</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
