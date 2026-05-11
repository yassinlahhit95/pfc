<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../login.php");
    exit;
}

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_perfil'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_perfil']);

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

<div class="encabezado-pagina">
    <h1>EDITAR MI PERFIL</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/perfil/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3><i class="fas fa-user-circle"></i> DATOS DE CONTACTO</h3></div>

        <div class="campo-formulario">
            <label for="nombreProfesor">Nombre Completo</label>
            <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= $nom ?>" class="<?= isset($errs['nombreProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreProfesor'])) { ?>
                <strong class="error-campo"><?= $errs['nombreProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="emailProfesor">Correo Corporativo</label>
            <input type="text" id="emailProfesor" name="emailProfesor" value="<?= $eml ?>" class="<?= isset($errs['emailProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['emailProfesor'])) { ?>
                <strong class="error-campo"><?= $errs['emailProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="telefonoProfesor">Número de Teléfono</label>
            <input type="tel" id="telefonoProfesor" name="telefonoProfesor" value="<?= $tel ?>" class="<?= isset($errs['telefonoProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['telefonoProfesor'])) { ?>
                <strong class="error-campo"><?= $errs['telefonoProfesor'] ?></strong>
            <?php } ?>
        </div>

        <div class="titulo-tarjeta mt-30"><h3>SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-atenuado mb-15">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo-formulario">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" id="current_password" name="current_password" placeholder="Escriba su contraseña actual para validar" class="<?= isset($errs['current_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['current_password'])) { ?>
                <strong class="error-campo"><?= $errs['current_password'] ?></strong>
            <?php } ?>
        </div>

        <div class="campo-formulario">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres" class="<?= isset($errs['new_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['new_password'])) { ?>
                <strong class="error-campo"><?= $errs['new_password'] ?></strong>
            <?php } ?>
        </div>

        <div class="form-acciones">
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
