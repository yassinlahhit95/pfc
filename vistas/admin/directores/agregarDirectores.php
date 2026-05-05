<?php
session_start();
$titulo_pagina = "Registrar Director - Admin";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";

$lista_de_errores = $_SESSION['errores'] ?? [];
$datos = $_SESSION['datos_director'] ?? [];

unset($_SESSION['errores'], $_SESSION['datos_director']);
?>

<div class="encabezado-pagina">
    <h1>Nuevo Director de Ciclo</h1>
    <a href="verDirectores.php" class="boton-secundario">← VOLVER</a>
</div>

<div class="tarjeta-blanca">
    <form action="../../../controladores/admin/directores/insertar.php" method="POST">
        <div class="form-estandar">
            <div class="campo-formulario">
                <label>Nombre Completo *</label>
                <input type="text" name="nombreDirector" value="<?= $datos['nombreDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['nombreDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['nombreDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Email *</label>
                <input type="text" name="emailDirector" value="<?= $datos['emailDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['emailDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['emailDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>DNI *</label>
                <input type="text" name="dniDirector" value="<?= $datos['dniDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['dniDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['dniDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Teléfono *</label>
                <input type="text" name="telefonoDirector" value="<?= $datos['telefonoDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['telefonoDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['telefonoDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Fecha de Nacimiento *</label>
                <input type="date" name="fechaNacimientoDirector" value="<?= $datos['fechaNacimientoDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['fechaNacimientoDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['fechaNacimientoDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Ciudad *</label>
                <input type="text" name="ciudadDirector" value="<?= $datos['ciudadDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['ciudadDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['ciudadDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario">
                <label>Código Postal *</label>
                <input type="text" name="codigoPostalDirector" value="<?= $datos['codigoPostalDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['codigoPostalDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['codigoPostalDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Dirección Completa *</label>
                <input type="text" name="direccionDirector" value="<?= $datos['direccionDirector'] ?? '' ?>">
                <?php if (isset($lista_de_errores['direccionDirector'])) { ?>
                    <strong class="error-campo"><?= $lista_de_errores['direccionDirector'] ?></strong>
                <?php } ?>
            </div>

            <div class="campo-formulario campo-ancho-total">
                <label>Observaciones / Notas Internas</label>
                <textarea name="observacionesDirector" rows="3"><?= $datos['observacionesDirector'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="form-acciones">
            <button type="submit" name="guardarDirector" class="boton-primario">
                <i class="fas fa-save"></i> REGISTRAR DIRECTOR
            </button>
            <button type="button" class="boton-secundario" onclick="window.location.href = window.location.pathname + window.location.search;">
                <i class="fas fa-eraser"></i> LIMPIAR
            </button>
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>


