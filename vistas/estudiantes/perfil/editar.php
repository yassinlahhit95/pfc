<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_perfil'] ?? [];

require_once "../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$est = obtenerEstudiantePorId($id);

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
    <div class="mensaje-exito"><?php echo $exito; ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/estudiantes/perfil/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3>DATOS PERSONALES Y CONTACTO</h3></div>

        <div class="campo">
            <label for="nombreEstudiante">Nombre Completo</label>
            <input type="text" id="nombreEstudiante" name="nombreEstudiante" value="<?= $datos['nombreEstudiante'] ?? $est['nombreEstudiante'] ?>" class="<?= isset($errores['nombreEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['nombreEstudiante'])) { ?>
                <strong class="error-campo"><?= $errores['nombreEstudiante'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="emailEstudiante">Correo Electronico</label>
            <input type="text" id="emailEstudiante" name="emailEstudiante" value="<?= $datos['emailEstudiante'] ?? $est['emailEstudiante'] ?>" class="<?= isset($errores['emailEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['emailEstudiante'])) { ?>
                <strong class="error-campo"><?= $errores['emailEstudiante'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="telefonoEstudiante">Numero de Telefono</label>
            <input type="tel" id="telefonoEstudiante" name="telefonoEstudiante" value="<?= $datos['telefonoEstudiante'] ?? $est['telefonoEstudiante'] ?>" class="<?= isset($errores['telefonoEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errores['telefonoEstudiante'])) { ?>
                <strong class="error-campo"><?= $errores['telefonoEstudiante'] ?></strong>
            <?php } ?>
        </div>

        <div class="titulo-tarjeta" style="margin-top: 30px;"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
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
