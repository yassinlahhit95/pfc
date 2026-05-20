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

<?php if (is_string($errores) && $errores) { ?>
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
            <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= $nom ?>" class="<?= isset($errores['nombreProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['nombreProfesor'])) { ?>
                <strong class="error-campo"><?= $errores['nombreProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="emailProfesor">Correo Corporativo</label>
            <input type="text" id="emailProfesor" name="emailProfesor" value="<?= $eml ?>" class="<?= isset($errores['emailProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['emailProfesor'])) { ?>
                <strong class="error-campo"><?= $errores['emailProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="telefonoProfesor">Numero de Telefono</label>
            <input type="tel" id="telefonoProfesor" name="telefonoProfesor" value="<?= $tel ?>" class="<?= isset($errores['telefonoProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['telefonoProfesor'])) { ?>
                <strong class="error-campo"><?= $errores['telefonoProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="titulo-tarjeta" style="margin-top: 30px;"><h3>SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-suave" style="margin-bottom: 15px;">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" id="current_password" name="current_password" placeholder="Escriba su contraseña actual para validar" class="<?= isset($errores['current_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['current_password'])) { ?>
                <strong class="error-campo"><?= $errores['current_password'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres" class="<?= isset($errores['new_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['new_password'])) { ?>
                <strong class="error-campo"><?= $errores['new_password'] ?></strong>
            <?php } ?>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPerfil" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
