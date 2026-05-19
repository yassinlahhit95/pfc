<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_perfil'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_perfil']);

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

<?php if ($error) { ?>
    <div class="mensaje-error"><?php echo $error; ?></div>
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
            <input type="text" id="nombreEstudiante" name="nombreEstudiante" value="<?= $datos['nombreEstudiante'] ?? $est['nombreEstudiante'] ?>" class="<?= isset($errs['nombreEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreEstudiante'])) { ?>
                <strong class="error-campo"><?= $errs['nombreEstudiante'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="emailEstudiante">Correo Electrónico</label>
            <input type="text" id="emailEstudiante" name="emailEstudiante" value="<?= $datos['emailEstudiante'] ?? $est['emailEstudiante'] ?>" class="<?= isset($errs['emailEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['emailEstudiante'])) { ?>
                <strong class="error-campo"><?= $errs['emailEstudiante'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="telefonoEstudiante">Número de Teléfono</label>
            <input type="tel" id="telefonoEstudiante" name="telefonoEstudiante" value="<?= $datos['telefonoEstudiante'] ?? $est['telefonoEstudiante'] ?>" class="<?= isset($errs['telefonoEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['telefonoEstudiante'])) { ?>
                <strong class="error-campo"><?= $errs['telefonoEstudiante'] ?></b>
            <?php } ?>
        </div>

        <div class="titulo-tarjeta" style="margin-top: 30px;"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="atenuado" style="margin-bottom: 15px;">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" id="current_password" name="current_password" placeholder="Escriba su contraseña actual para validar" class="<?= isset($errs['current_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['current_password'])) { ?>
                <strong class="error-campo"><?= $errs['current_password'] ?></b>
            <?php } ?>
        </div>

        <div class="campo">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres" class="<?= isset($errs['new_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['new_password'])) { ?>
                <strong class="error-campo"><?= $errs['new_password'] ?></b>
            <?php } ?>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarPerfil" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
