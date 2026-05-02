<?php
session_start();

// Control de acceso para administradores
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "MI PERFIL - ADMINISTRACIÃ“N";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$idLogueado = $_SESSION['idAdmin'];
$datosAdmin = obtenerDirectorPorId($idLogueado);

// Captura de mensajes de sesiÃ³n
$mensajeError = "";
if (isset($_SESSION['error'])) { $mensajeError = $_SESSION['error']; }

$mensajeExito = "";
if (isset($_SESSION['exito'])) { $mensajeExito = $_SESSION['exito']; }

// Limpiar sesiÃ³n
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>MI PERFIL Y SEGURIDAD</h1>
</div>

<?php if (!empty($mensajeExito)) { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>

<?php if (!empty($mensajeError)) { ?>
    <div class="mensaje-error"><?php echo $mensajeError; ?></div>
<?php } ?>
<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/actualizar_perfil.php" method="POST">
        <input type="hidden" name="idDirector" value="<?php echo $idLogueado; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreDirector" value="<?php echo $datosAdmin['nombreDirector']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Correo ElectrÃ³nico</label>
                <input type="text" name="emailDirector" value="<?php echo $datosAdmin['emailDirector']; ?>">
            </div>

            <div class="campo-formulario">
                <label>NÃºmero de TelÃ©fono</label>
                <input type="text" name="telefonoDirector" value="<?php echo $datosAdmin['telefonoDirector']; ?>">
            </div>
        </div>

        <h3 class="margen-arriba mt-20"><i class="fas fa-lock"></i> CAMBIAR CONTRASEÃ‘A (OPCIONAL)</h3>
        <p class="texto-atenuado texto-pequeno">Solo rellene si desea actualizar su clave de acceso.</p>

        <div class="formulario-cuadricula mt-10">
            <div class="campo-formulario">
                <label>CONTRASEÃ‘A ACTUAL:</label>
                <input type="password" name="current_password" placeholder="Validar cambios">
            </div>

            <div class="campo-formulario">
                <label>NUEVA CONTRASEÃ‘A:</label>
                <input type="password" name="new_password" placeholder="MÃ­nimo 6 caracteres">
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarPerfilBtn" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR MIS DATOS
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

