<?php
session_start();

// Control de acceso para administradores
if (isset($_SESSION['idAdmin']) == false) {
    header("Location: /pfc/index.php");
    exit;
}

$titulo_pagina = "MI PERFIL - ADMINISTRACIÓN";
$seccion = 'perfil';
include_once "../comunes/nav.php";

require_once "../../../modelos/directores.php";

$idLogueado = $_SESSION['idAdmin'];
$datosAdmin = obtenerDirectorPorId($idLogueado);

// Captura de mensajes de sesión
$mensajeError = "";
if (isset($_SESSION['error'])) { $mensajeError = $_SESSION['error']; }

$mensajeExito = "";
if (isset($_SESSION['exito'])) { $mensajeExito = $_SESSION['exito']; }

// Limpiar sesión
unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>MI PERFIL Y SEGURIDAD</h1>
</div>

<?php if ($mensajeExito != "") { ?>
    <div class="mensaje-exito"><?php echo $mensajeExito; ?></div>
<?php } ?>

<?php if ($mensajeError != "") { ?>
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
                <label>Correo Electrónico</label>
                <input type="text" name="emailDirector" value="<?php echo $datosAdmin['emailDirector']; ?>">
            </div>

            <div class="campo-formulario">
                <label>Número de Teléfono</label>
                <input type="text" name="telefonoDirector" value="<?php echo $datosAdmin['telefonoDirector']; ?>">
            </div>
        </div>

        <h3 class="margen-arriba mt-20"><i class="fas fa-lock"></i> CAMBIAR CONTRASEÑA (OPCIONAL)</h3>
        <p class="texto-atenuado texto-pequeno">Solo rellene si desea actualizar su clave de acceso.</p>

        <div class="formulario-cuadricula mt-10">
            <div class="campo-formulario">
                <label>CONTRASEÑA ACTUAL:</label>
                <input type="password" name="current_password" placeholder="Validar cambios">
            </div>

            <div class="campo-formulario">
                <label>NUEVA CONTRASEÑA:</label>
                <input type="password" name="new_password" placeholder="Mínimo 6 caracteres">
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

