<?php
session_start();
$titulo_pagina = "Modificar Director";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../../modelos/directores.php";

$id = '';
if (isset($_GET['id'])) {
    $id = $_GET['id'];
}

$director = obtenerDirectorPorId($id);

if (!$director) {
    header("Location: /pfc/vistas/admin/directores/verDirectores.php");
    exit;
}

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Modificar Director: <?php echo $director['nombreDirector']; ?></h1>
    <a href="/pfc/vistas/admin/directores/verDirectores.php" class="boton-secundario">Volver</a>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/actualizar.php" method="POST" style="max-width: 600px; margin: 0 auto;">
        <input type="hidden" name="idDirector" value="<?php echo $director['idDirector']; ?>">
        
        <div class="campo-formulario">
            <label>Nombre Completo *</label>
            <input type="text" name="nombreDirector" value="<?php echo $director['nombreDirector']; ?>">
        </div>

        <div class="campo-formulario">
            <label>Email *</label>
            <input type="email" name="emailDirector" value="<?php echo $director['emailDirector']; ?>">
        </div>

        <div class="campo-formulario">
            <label>DNI *</label>
            <input type="text" name="dniDirector" value="<?php echo $director['dniDirector']; ?>">
        </div>

        <div class="campo-formulario">
            <label>Teléfono</label>
            <input type="text" name="telefonoDirector" value="<?php echo $director['telefonoDirector']; ?>">
        </div>

        <div class="campo-formulario">
            <label>Fecha de Alta</label>
            <input type="date" name="fechaAltaDirector" value="<?php echo $director['fechaAltaDirector']; ?>">
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarDirector" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>