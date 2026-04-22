<?php
session_start();
$titulo_pagina = "Nuevo Director";
$seccion = 'directores';
include_once "../comunes/nav.php";

$error = '';
if (isset($_SESSION['error'])) {
    $error = $_SESSION['error'];
}
unset($_SESSION['error']);
?>

<div class="disposicion-flexible espacio-entre-elementos alinear-centro margen-abajo">
    <h1>Nuevo Director</h1>
    <a href="/pfc/vistas/admin/directores/verDirectores.php" class="boton-secundario">Volver</a>
</div>

<?php if (!empty($error)) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/insertar.php" method="POST" style="max-width: 600px; margin: 0 auto;">
        
        <div class="campo-formulario">
            <label>Nombre Completo *</label>
            <input type="text" name="nombreDirector" placeholder="Introduce el nombre">
        </div>

        <div class="campo-formulario">
            <label>Email *</label>
            <input type="email" name="emailDirector" placeholder="correo@ejemplo.com">
        </div>

        <div class="campo-formulario">
            <label>DNI *</label>
            <input type="text" name="dniDirector" placeholder="12345678X">
        </div>

        <div class="campo-formulario">
            <label>Teléfono</label>
            <input type="text" name="telefonoDirector" placeholder="600000000">
        </div>

        <div class="campo-formulario">
            <label>Fecha de Alta</label>
            <input type="date" name="fechaAltaDirector">
        </div>

        <div class="margen-arriba pt-20">
            <button type="submit" name="guardarDirector" class="boton-primario ancho-total">
                <i class="fas fa-save"></i> Registrar Director
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>