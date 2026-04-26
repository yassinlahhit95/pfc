<?php
session_start();
$titulo_pagina = "Modificar Director - Super Admin";
$seccion = 'directores';
include_once "../comunes/nav.php";

require_once "../../../modelos/directores.php";

$id_director = $_GET['idDirector'];
$director = obtenerDirectorPorId($id_director);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

if (isset($_SESSION['datos_director'])) {
    $director = $_SESSION['datos_director'];
}

$lista_de_errores = [];
if (isset($_SESSION['errores'])) { $lista_de_errores = $_SESSION['errores']; }

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_director']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Director</h1>
    <a href="verDirectores.php" class="boton-secundario">← Volver</a>
</div>

<?php if (isset($mensaje_error)) { ?>
    <div class="mensaje-error"><?php echo $mensaje_error; ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="/pfc/controladores/admin/directores/actualizar.php" method="POST">
        <input type="hidden" name="idDirector" value="<?php echo $id_director; ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?php echo $director['nombreDirector']; ?>">
                <?php if (isset($lista_de_errores['nombreDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['nombreDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailDirector" value="<?php echo $director['emailDirector']; ?>">
                <?php if (isset($lista_de_errores['emailDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['emailDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?php echo $director['dniDirector']; ?>">
                <?php if (isset($lista_de_errores['dniDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['dniDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" value="<?php echo $director['telefonoDirector']; ?>">
                <?php if (isset($lista_de_errores['telefonoDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['telefonoDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoDirector" value="<?php echo $director['fechaNacimientoDirector']; ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['fechaNacimientoDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadDirector" value="<?php echo $director['ciudadDirector']; ?>">
                <?php if (isset($lista_de_errores['ciudadDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['ciudadDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalDirector" value="<?php echo $director['codigoPostalDirector']; ?>">
                <?php if (isset($lista_de_errores['codigoPostalDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['codigoPostalDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección Completa *</label>
                <input type="text" name="direccionDirector" value="<?php echo $director['direccionDirector']; ?>">
                <?php if (isset($lista_de_errores['direccionDirector'])) { ?>
                    <p class="error-campo"><?php echo $lista_de_errores['direccionDirector']; ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones / Notas Internas</label>
                <textarea name="observacionesDirector" rows="3"><?php echo $director['observacionesDirector']; ?></textarea>
            </div>
        </div>

        <div class="margen-arriba">
            <button type="submit" name="actualizarDirector" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>

