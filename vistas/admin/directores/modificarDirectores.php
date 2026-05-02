<?php
session_start();
$titulo_pagina = "Modificar Director - Admin";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";

require_once __DIR__ . "/../../../modelos/directores.php";

$id_director = $_GET['idDirector'] ?? '';
$director = obtenerDirectorPorId($id_director);

if (!$director) {
    header("Location: verDirectores.php");
    exit;
}

$director = ($_SESSION['datos_director'] ?? 0);

$error = $_SESSION['error'] ?? '';
$lista_de_errores = $_SESSION['errores'] ?? [];

unset($_SESSION['error'], $_SESSION['errores'], $_SESSION['datos_director']);
?>

<div class="encabezado-pagina">
    <h1>Modificar Director</h1>
    <a href="verDirectores.php" class="boton-secundario">← Volver</a>
</div>

<?php if ($error) { ?>
    <div class="mensaje-error"><?= $error ?></div>
<?php } ?>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/directores/actualizar.php" method="POST">
        <input type="hidden" name="idDirector" value="<?= $id_director ?>">
        
        <div class="formulario-cuadricula">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?= $director['nombreDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['nombreDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['nombreDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailDirector" value="<?= $director['emailDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['emailDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['emailDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?= $director['dniDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['dniDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['dniDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" value="<?= $director['telefonoDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['telefonoDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['telefonoDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoDirector" value="<?= $director['fechaNacimientoDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['fechaNacimientoDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadDirector" value="<?= $director['ciudadDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['ciudadDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['ciudadDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalDirector" value="<?= $director['codigoPostalDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['codigoPostalDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['codigoPostalDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección Completa *</label>
                <input type="text" name="direccionDirector" value="<?= $director['direccionDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['direccionDirector'])) { ?>
                    <p class="error-campo"><?= $lista_de_errores['direccionDirector'] ?></p>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones / Notas Internas</label>
                <textarea name="observacionesDirector" rows="3"><?= $director['observacionesDirector'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="margen-arriba disposicion-flexible separacion-media">
            <button type="submit" name="actualizarDirector" class="boton-primario">
                <i class="fas fa-save"></i> Guardar Cambios
            </button>
            <button type="reset" class="boton-secundario">
                <i class="fas fa-eraser"></i> Limpiar
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


