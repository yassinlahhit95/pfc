<?php
session_start();
$titulo_pagina = "Modificar Profesor - Admin";
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

$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['errores'], $_SESSION['datos_profesor']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Profesor: <?= $profesor['nombreProfesor'] ?></h1>
    <a href="verProfesores.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?= $id_profesor ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label for="nombreProfesor">Nombre Completo *</label>
                <input type="text" name="nombreProfesor" id="nombreProfesor" value="<?= $profesor['nombreProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['nombreProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="emailProfesor">Email *</label>
                <input type="email" name="emailProfesor" id="emailProfesor" value="<?= $profesor['emailProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['emailProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['emailProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="dniProfesor">DNI *</label>
                <input type="text" name="dniProfesor" id="dniProfesor" value="<?= $profesor['dniProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['dniProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['dniProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="telefonoProfesor">Teléfono *</label>
                <input type="text" name="telefonoProfesor" id="telefonoProfesor" value="<?= $profesor['telefonoProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['telefonoProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['telefonoProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="direccionProfesor">Dirección *</label>
                <input type="text" name="direccionProfesor" id="direccionProfesor" value="<?= $profesor['direccionProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['direccionProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['direccionProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="ciudadProfesor">Ciudad *</label>
                <input type="text" name="ciudadProfesor" id="ciudadProfesor" value="<?= $profesor['ciudadProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['ciudadProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['ciudadProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="codigoPostalProfesor">Código Postal *</label>
                <input type="text" name="codigoPostalProfesor" id="codigoPostalProfesor" value="<?= $profesor['codigoPostalProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['codigoPostalProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['codigoPostalProfesor'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoProfesor" id="fechaNacimientoProfesor" value="<?= $profesor['fechaNacimientoProfesor'] ?>" required>
                <?php if (isset($lista_de_errores['fechaNacimientoProfesor'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaNacimientoProfesor'] ?></strong>
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




