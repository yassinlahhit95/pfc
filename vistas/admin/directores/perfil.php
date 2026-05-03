<?php
session_start();

// Control de acceso para administradores
if (empty($_SESSION['idAdmin'])) {
    header("Location: ../../../index.php");
    exit;
}

$titulo_pagina = "MI PERFIL - ADMINISTRACI�N";
$seccion = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$idLogueado = $_SESSION['idAdmin'];
$datosAdmin = obtenerDirectorPorId($idLogueado);

$error = $_SESSION['error'] ?? '';
$exito = $_SESSION['exito'] ?? '';

unset($_SESSION['error'], $_SESSION['exito']);
?>

<div class="encabezado-pagina">
    <h1>MI PERFIL Y SEGURIDAD</h1>
</div>

<?php if ($exito) { ?>
    <div class="mensaje-exito"><?= $exito ?></div>
<?php } ?>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>
<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/directores/actualizar_perfil.php" method="POST">
        <input type="hidden" name="idDirector" value="<?= $idLogueado ?>">
        
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre Completo</label>
                <input type="text" name="nombreDirector" value="<?= $datosAdmin['nombreDirector'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>Correo Electr�nico</label>
                <input type="text" name="emailDirector" value="<?= $datosAdmin['emailDirector'] ?? '' ?>">
            </div>

            <div class="campo-formulario">
                <label>N�mero de Tel�fono</label>
                <input type="text" name="telefonoDirector" value="<?= $datosAdmin['telefonoDirector'] ?? '' ?>">
            </div>
        </div>

        <h3 class="margen-arriba mt-20"><i class="fas fa-lock"></i> CAMBIAR CONTRASE�A (OPCIONAL)</h3>
        <p class="texto-atenuado texto-pequeno">Solo rellene si desea actualizar su clave de acceso.</p>

        <div class="formulario-cuadricula mt-10">
            <div class="campo-formulario">
                <label>CONTRASE�A ACTUAL:</label>
                <input type="password" name="current_password" placeholder="Validar cambios">
            </div>

            <div class="campo-formulario">
                <label>NUEVA CONTRASE�A:</label>
                <input type="password" name="new_password" placeholder="M�nimo 6 caracteres">
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarPerfilBtn" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR MIS DATOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


