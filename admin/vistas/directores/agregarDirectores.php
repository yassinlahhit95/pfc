<?php
session_start();
$titulo_pagina = "Nuevo Director";
$seccion = 'directores';
include_once "../comunes/nav.php";

$datos = $_SESSION['datos_director'] ?? [];
$error_nombre = $_SESSION['error_nombre'] ?? "";
$error_email = $_SESSION['error_email'] ?? "";

unset($_SESSION['datos_director'], $_SESSION['error_nombre'], $_SESSION['error_email']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Director</h1>
    <a href="vistas/directores/verDirectores.php" class="boton-gris">Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="controlador/directoresControlador.php" method="POST">
        <input type="hidden" name="accion" value="insertar">

        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre</label>
                <input type="text" name="nombreDirector" value="<?php echo $datos['nombreDirector'] ?? ''; ?>">
                <?php if ($error_nombre != "") { echo "<p class='error-campo'>$error_nombre</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Email</label>
                <input type="text" name="emailDirector" value="<?php echo $datos['emailDirector'] ?? ''; ?>">
                <?php if ($error_email != "") { echo "<p class='error-campo'>$error_email</p>"; } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad</label>
                <input type="text" name="ciudadDirector" value="<?php echo $datos['ciudadDirector'] ?? ''; ?>">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" class="boton-azul">Guardar Director</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
