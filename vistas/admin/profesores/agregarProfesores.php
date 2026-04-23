<?php
session_start();
$titulo_pagina = "Agregar Profesor - Super Admin";
$seccion = 'profesores';
include_once "../comunes/nav.php";

$lista_de_errores = [];
if (isset($_SESSION['errores'])) {
    $lista_de_errores = $_SESSION['errores'];
}

$datos = [];
if (isset($_SESSION['datos_profesor'])) {
    $datos = $_SESSION['datos_profesor'];
}

unset($_SESSION['errores'], $_SESSION['datos_profesor']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Profesor</h1>
    <a href="/pfc/vistas/admin/profesores/verProfesores.php" class="boton-secundario">← Volver</a>
</div>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/profesores/insertar.php" method="POST">
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreProfesor" value="<?php if(isset($datos['nombreProfesor'])) { echo $datos['nombreProfesor']; } ?>">
                <?php if (isset($lista_de_errores['nombreProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailProfesor" value="<?php if(isset($datos['emailProfesor'])) { echo $datos['emailProfesor']; } ?>">
                <?php if (isset($lista_de_errores['emailProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['emailProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniProfesor" value="<?php if(isset($datos['dniProfesor'])) { echo $datos['dniProfesor']; } ?>">
                <?php if (isset($lista_de_errores['dniProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['dniProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoProfesor" value="<?php if(isset($datos['telefonoProfesor'])) { echo $datos['telefonoProfesor']; } ?>">
                <?php if (isset($lista_de_errores['telefonoProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['telefonoProfesor']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Dirección *</label>
                <input type="text" name="direccionProfesor" value="<?php if(isset($datos['direccionProfesor'])) { echo $datos['direccionProfesor']; } ?>">
                <?php if (isset($lista_de_errores['direccionProfesor'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['direccionProfesor']; ?></p>
                <?php } ?>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="guardarProfesor" class="boton-primario">
                <i class="fas fa-save"></i> Registrar Profesor
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
