<?php
require_once __DIR__ . "/../../../include/Security.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_perfil'] ?? [];

require_once "../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

$tituloDelPagina = "AULAPRO | EDITAR PERFIL";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR MI PERFIL</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= Security::escapeHtml($errores ) ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= Security::escapeHtml($exito ) ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/estudiantes/perfil/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante ) ?>">

        <div class="titulo-tarjeta"><h3>DATOS PERSONALES Y CONTACTO</h3></div>

        <div class="campo">
            <label for="nombreEstudiante">Nombre Completo</label>
            <input type="text" name="nombreEstudiante" id="nombreEstudiante" value="<?= Security::escapeHtml($datos['nombreEstudiante'] ?? $estudianteActual['nombreEstudiante']) ?>">
        </div>

        <div class="campo">
            <label for="emailEstudiante">Correo Electronico</label>
            <input type="email" name="emailEstudiante" id="emailEstudiante" value="<?= Security::escapeHtml($datos['emailEstudiante'] ?? $estudianteActual['emailEstudiante']) ?>">
        </div>

        <div class="campo">
            <label for="telefonoEstudiante">Numero de Telefono</label>
            <input type="text" name="telefonoEstudiante" id="telefonoEstudiante" value="<?= Security::escapeHtml($datos['telefonoEstudiante'] ?? $estudianteActual['telefonoEstudiante']) ?>">
        </div>

        <div class="titulo-tarjeta" style="margin-top: 30px;"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-suave" style="margin-bottom: 15px;">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" name="current_password" id="current_password">
        </div>

        <div class="campo">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" name="new_password" id="new_password">
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPerfil" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


