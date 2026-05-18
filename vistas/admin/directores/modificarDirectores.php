<?php
session_start();
require_once __DIR__ . "/../../../modelos/directores.php";

$id_director = $_GET['idDirector'] ?? '';
$director = obtenerDirectorPorId($id_director);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

if (isset($_SESSION['datos_director'])) {
    $director = array_merge($director, $_SESSION['datos_director']);
}

$error = $_SESSION['error'] ?? '';
$errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_director']);

$titulo_pagina = "AULAPRO | MODIFICAR DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>MODIFICAR DIRECTOR</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/directores/actualizar.php" method="POST">
        <input type="hidden" name="idDirector" value="<?= $id_director ?>">
        
        <div class="formulario">
            <div class="campo">
                <label for="nombreDirector">Nombre Completo *</label>
                <input type="text" id="nombreDirector" name="nombreDirector" value="<?= $director['nombreDirector'] ?? '' ?>">
                <?php if (isset($errores['nombreDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="emailDirector">Email *</label>
                <input type="text" id="emailDirector" name="emailDirector" value="<?= $director['emailDirector'] ?? '' ?>">
                <?php if (isset($errores['emailDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['emailDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="dniDirector">DNI *</label>
                <input type="text" id="dniDirector" name="dniDirector" value="<?= $director['dniDirector'] ?? '' ?>">
                <?php if (isset($errores['dniDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['dniDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="telefonoDirector">Teléfono *</label>
                <input type="text" id="telefonoDirector" name="telefonoDirector" value="<?= $director['telefonoDirector'] ?? '' ?>">
                <?php if (isset($errores['telefonoDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="fechaNacimientoDirector">Fecha de Nacimiento *</label>
                <input type="date" id="fechaNacimientoDirector" name="fechaNacimientoDirector" value="<?= $director['fechaNacimientoDirector'] ?? '' ?>">
                <?php if (isset($errores['fechaNacimientoDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="ciudadDirector">Ciudad *</label>
                <input type="text" id="ciudadDirector" name="ciudadDirector" value="<?= $director['ciudadDirector'] ?? '' ?>">
                <?php if (isset($errores['ciudadDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo">
                <label for="codigoPostalDirector">Código Postal *</label>
                <input type="text" id="codigoPostalDirector" name="codigoPostalDirector" value="<?= $director['codigoPostalDirector'] ?? '' ?>">
                <?php if (isset($errores['codigoPostalDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionDirector">Dirección Completa *</label>
                <input type="text" id="direccionDirector" name="direccionDirector" value="<?= $director['direccionDirector'] ?? '' ?>">
                <?php if (isset($errores['direccionDirector'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionDirector'] ?></b>
                <?php } ?>
            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesDirector">Observaciones / Notas Internas</label>
                <textarea id="observacionesDirector" name="observacionesDirector" rows="3"><?= $director['observacionesDirector'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <button type="submit" name="actualizarDirector" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
