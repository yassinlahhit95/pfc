<?php
session_start();
$titulo_pagina = "Modificar Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

require_once "../../../modelos/profesores.php";

$id_profesor = $_GET['idProfesor'];
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

if (isset($_SESSION['datos_profesor'])) {
    $profesor = $_SESSION['datos_profesor'];
}

$lista_de_errores = [];
if (isset($_SESSION['errores'])) {
    $lista_de_errores = $_SESSION['errores'];
}

unset($_SESSION['errores'], $_SESSION['datos_profesor']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Profesor: <?php echo $profesor['nombreProfesor']; ?></h1>
    <a href="verProfesores.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="idProfesor" value="<?php echo $id_profesor; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php echo $profesor['nombreProfesor']; ?>">
                <?php if (isset($lista_de_errores['nombreProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailProfesor" value="<?php echo $profesor['emailProfesor']; ?>">
                <?php if (isset($lista_de_errores['emailProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['emailProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php echo $profesor['dniProfesor']; ?>">
                <?php if (isset($lista_de_errores['dniProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['dniProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" value="<?php echo $profesor['telefonoProfesor']; ?>">
                <?php if (isset($lista_de_errores['telefonoProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['telefonoProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" value="<?php echo $profesor['direccionProfesor']; ?>">
                <?php if (isset($lista_de_errores['direccionProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['direccionProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadProfesor" value="<?php echo $profesor['ciudadProfesor']; ?>">
                <?php if (isset($lista_de_errores['ciudadProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['ciudadProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalProfesor" value="<?php echo $profesor['codigoPostalProfesor']; ?>">
                <?php if (isset($lista_de_errores['codigoPostalProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['codigoPostalProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoProfesor" value="<?php echo $profesor['fechaNacimientoProfesor']; ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaNacimientoProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Alta (en centro)</label>
                <input type="date" name="fechaAltaProfesor" value="<?php echo $profesor['fechaAltaProfesor']; ?>">
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea name="observacionesProfesor" rows="3"><?php echo $profesor['observacionesProfesor']; ?></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

