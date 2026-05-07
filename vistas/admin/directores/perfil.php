<?php
session_start();

if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../login.php");
    exit;
}

require_once __DIR__ . "/../../../modelos/directores.php";

$idLogueado = $_SESSION['idAdmin'];
$datosAdmin = obtenerDirectorPorId($idLogueado);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';
unset($_SESSION['error'], $_SESSION['exito']);

$titulo_pagina = "Mi Perfil - Administración";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="encabezado-pagina">
    <div>
        <h1>Mi Perfil</h1>
        <p class="subtitulo">Información de tu cuenta de administrador</p>
    </div>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/directores/actualizar_perfil.php" method="POST" class="form-estandar">
        <input type="hidden" name="idDirector" value="<?= $idLogueado ?>">
        
        <div class="titulo-tarjeta"><h3><i class="fas fa-user-circle"></i> DATOS DE CONTACTO</h3></div>

        <div class="campo-formulario">
            <label for="nombreDirector">Nombre Completo</label>
            <input type="text" id="nombreDirector" name="nombreDirector" value="<?= $datosAdmin['nombreDirector'] ?? '' ?>">
        </div>

        <div class="campo-formulario">
            <label for="emailDirector">Correo Electrónico</label>
            <input type="text" id="emailDirector" name="emailDirector" value="<?= $datosAdmin['emailDirector'] ?? '' ?>">
        </div>

        <div class="campo-formulario">
            <label for="telefonoDirector">Número de Teléfono</label>
            <input type="text" id="telefonoDirector" name="telefonoDirector" value="<?= $datosAdmin['telefonoDirector'] ?? '' ?>">
        </div>

        <div class="titulo-tarjeta mt-30"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-atenuado mb-15">Solo rellene estos campos si desea actualizar su clave de acceso.</p>

        <div class="campo-formulario">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" id="current_password" name="current_password" placeholder="Validar cambios">
        </div>

        <div class="campo-formulario">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" id="new_password" name="new_password" placeholder="Mínimo 6 caracteres">
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPerfilBtn" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR MIS DATOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
