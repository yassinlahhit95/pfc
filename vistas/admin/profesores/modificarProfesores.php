<?php
session_start();
$titulo_pagina = "AULAPRO | MODIFICAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/profesores.php";

$id_profesor = $_GET['idProfesor'] ?? '';
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

if (isset($_SESSION['datos_profesor'])) {
    $profesor = array_merge($profesor, $_SESSION['datos_profesor']);
}

$errores = $_SESSION['errores'] ?? [];
$error = $_SESSION['error'] ?? '';

unset($_SESSION['errores'], $_SESSION['datos_profesor'], $_SESSION['error']);
?>

<div class="encabezado-pagina">
    <h1>MODIFICAR PROFESOR: <?= $profesor['nombreProfesor'] ?></h1>
    <a href="verProfesores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?= $id_profesor ?>">
        
        <div class="form-estandar">
            <div class="campo-formulario">
                <label for="nombreProfesor">Nombre Completo *</label>
                <input type="text" name="nombreProfesor" id="nombreProfesor" value="<?= $profesor['nombreProfesor'] ?>">
                <?php if (isset($errores['nombreProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['nombreProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailProfesor">Email *</label>
                <input type="email" name="emailProfesor" id="emailProfesor" value="<?= $profesor['emailProfesor'] ?>">
                <?php if (isset($errores['emailProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['emailProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniProfesor">DNI *</label>
                <input type="text" name="dniProfesor" id="dniProfesor" value="<?= $profesor['dniProfesor'] ?>">
                <?php if (isset($errores['dniProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['dniProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoProfesor">Teléfono *</label>
                <input type="text" name="telefonoProfesor" id="telefonoProfesor" value="<?= $profesor['telefonoProfesor'] ?>">
                <?php if (isset($errores['telefonoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['telefonoProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="direccionProfesor">Dirección *</label>
                <input type="text" name="direccionProfesor" id="direccionProfesor" value="<?= $profesor['direccionProfesor'] ?>">
                <?php if (isset($errores['direccionProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['direccionProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ciudadProfesor">Ciudad *</label>
                <input type="text" name="ciudadProfesor" id="ciudadProfesor" value="<?= $profesor['ciudadProfesor'] ?>">
                <?php if (isset($errores['ciudadProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['ciudadProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalProfesor">Código Postal *</label>
                <input type="text" name="codigoPostalProfesor" id="codigoPostalProfesor" value="<?= $profesor['codigoPostalProfesor'] ?>">
                <?php if (isset($errores['codigoPostalProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['codigoPostalProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoProfesor" id="fechaNacimientoProfesor" value="<?= $profesor['fechaNacimientoProfesor'] ?>">
                <?php if (isset($errores['fechaNacimientoProfesor'])) { ?>
                    <strong class="error-campo"><?= $errores['fechaNacimientoProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaAltaProfesor">Fecha de Alta (en centro)</label>
                <input type="date" name="fechaAltaProfesor" id="fechaAltaProfesor" value="<?= $profesor['fechaAltaProfesor'] ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label for="observacionesProfesor">Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea name="observacionesProfesor" id="observacionesProfesor" rows="3"><?= $profesor['observacionesProfesor'] ?></textarea>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="actualizarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> GUARDAR CAMBIOS
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;"><i class="fas fa-eraser"></i> LIMPIAR</button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>





