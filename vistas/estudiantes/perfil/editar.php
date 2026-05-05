<?php
session_start();

$error = $_SESSION['error'] ?? null;
$exito = $_SESSION['exito'] ?? null;
$errs = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_perfil'] ?? [];
unset($_SESSION['error'], $_SESSION['exito'], $_SESSION['errores'], $_SESSION['datos_perfil']);

if (!isset($_SESSION['idEstudiante'])) {
    header("Location: ../../../index.php");
    exit;
}

require_once "../../../modelos/estudiantes.php";

$id = $_SESSION['idEstudiante'];
$est = obtenerEstudiantePorId($id);

$nom = $datos['nombreEstudiante'] ?? $est['nombreEstudiante'];
$eml = $datos['emailEstudiante'] ?? $est['emailEstudiante'];
$tel = $datos['telefonoEstudiante'] ?? $est['telefonoEstudiante'];

$tituloPagina = "Editar Mi Perfil - Portal Estudiantes";
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
    <form action="../../../controladores/estudiantes/perfil/actualizar.php" method="POST" class="form-estandar">
        <input type="hidden" name="idEstudiante" value="<?= $id ?>">

        <div class="titulo-tarjeta"><h3>Datos Personales</h3></div>
        
        <div class="campo-formulario">
            <label>Nombre Completo</label>
            <input type="text" name="nombreEstudiante" value="<?= $nom ?>" class="<?= isset($errs['nombreEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['nombreEstudiante'])): ?>
                <strong class="error-campo"><?= $errs['nombreEstudiante'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Correo Electrónico</label>
            <input type="text" name="emailEstudiante" value="<?= $eml ?>" class="<?= isset($errs['emailEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['emailEstudiante'])): ?>
                <strong class="error-campo"><?= $errs['emailEstudiante'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Número de Teléfono</label>
            <input type="tel" name="telefonoEstudiante" value="<?= $tel ?>" class="<?= isset($errs['telefonoEstudiante']) ? 'input-error' : '' ?>">
            <?php if (isset($errs['telefonoEstudiante'])): ?>
                <strong class="error-campo"><?= $errs['telefonoEstudiante'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="titulo-tarjeta mt-30"><h3>Seguridad y Contraseña</h3></div>
        <p class="texto-atenuado mb-15">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo-formulario">
            <label>Contraseña Actual</label>
            <input type="password" name="current_password" class="<?= isset($errs['current_password']) ? 'input-error' : '' ?>" placeholder="Escriba su contraseña para validar los cambios">
            <?php if (isset($errs['current_password'])): ?>
                <strong class="error-campo"><?= $errs['current_password'] ?></strong>
            <?php endif; ?>
        </div>

        <div class="campo-formulario">
            <label>Nueva Contraseña</label>
            <input type="password" name="new_password" class="<?= isset($errs['new_password']) ? 'input-error' : '' ?>" placeholder="Debe tener al menos 6 caracteres">
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

