<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_perfil'] ?? [];

require_once "../../../modelos/profesores.php";

$id = $_SESSION['idProfesor'];
$prof = obtenerProfesorPorId($id);

$nom = $datos['nombreProfesor'] ?? $prof['nombreProfesor'];
$eml = $datos['emailProfesor'] ?? $prof['emailProfesor'];
$tel = $datos['telefonoProfesor'] ?? $prof['telefonoProfesor'];

$tituloDelPagina = "AULAPRO | EDITAR PERFIL";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR MI PERFIL</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/profesores/perfil/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="idProfesor" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3><i class="fas fa-user-circle"></i> DATOS DE CONTACTO</h3></div>

        <div class="campo">
            <label for="nombreProfesor">Nombre Completo</label>
            
        </div>

        <div class="campo">
            <label for="emailProfesor">Correo Corporativo</label>
            
        </div>

        <div class="campo">
            <label for="telefonoProfesor">Numero de Telefono</label>
            
        </div>

        <div class="titulo-tarjeta" style="margin-top: 30px;"><h3>SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-suave" style="margin-bottom: 15px;">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo">
            <label for="current_password">Contraseña Actual</label>
            
        </div>

        <div class="campo">
            <label for="new_password">Nueva Contraseña</label>
            
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPerfil" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
