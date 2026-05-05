<?php
session_start();

if (!isset($_SESSION['idProfesor'])) {
    header("Location: ../../../index.php");
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

$tituloPagina = "Editar Mi Perfil - Portal Profesores";
$seccionActual = 'perfil';
include_once "../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <h1>Editar Mi Perfil</h1>
    <a href="ver.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error): ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php endif; ?>
<?php if ($exito): ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php endif; ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/profesores/perfil/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idProfesor" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3>Datos de Contacto</h3></div>

        <div class="campo-formulario">
            <label>Nombre Completo</label>
            <input type="text" name="nombreProfesor" value="<?= $nom ?>" class="<?= isset($errs['nombreProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreProfesor'])): ?>
                <strong class="error-campo"><?= $errs['nombreProfesor'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Correo Corporativo</label>
            <input type="text" name="emailProfesor" value="<?= $eml ?>" class="<?= isset($errs['emailProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['emailProfesor'])): ?>
                <strong class="error-campo"><?= $errs['emailProfesor'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Número de Teléfono</label>
            <input type="tel" name="telefonoProfesor" value="<?= $tel ?>" class="<?= isset($errs['telefonoProfesor']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['telefonoProfesor'])): ?>
                <strong class="error-campo"><?= $errs['telefonoProfesor'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="titulo-tarjeta mt-30"><h3>Seguridad y Contraseña</h3></div>
        <p class="texto-atenuado mb-15">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo-formulario">
            <label>Contraseña Actual</label>
            <input type="password" name="current_password" placeholder="Escriba su contraseña actual para validar" class="<?= isset($errs['current_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['current_password'])): ?>
                <strong class="error-campo"><?= $errs['current_password'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Nueva Contraseña</label>
            <input type="password" name="new_password" placeholder="Mínimo 6 caracteres" class="<?= isset($errs['new_password']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['new_password'])): ?>
                <strong class="error-campo"><?= $errs['new_password'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPerfil" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario px-25" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
