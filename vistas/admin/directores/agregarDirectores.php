<?php
session_start();

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_director'] ?? [];

$titulo_pagina = "AULAPRO | REGISTRAR DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO DIRECTOR DE CICLO</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<?php if ($errores) { ?>
    <div class="mensaje-error"><?= $errores ?></div>
<?php } ?>

<div class="panel">
    <form action="../../../controladores/admin/directores/insertar.php" method="POST">
        <div class="formulario">
            <div class="campo">
                <label for="nombreDirector">Nombre Completo</label>
                <input type="text" id="nombreDirector" name="nombreDirector" value="<?= $datos['nombreDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="emailDirector">Email</label>
                <input type="text" id="emailDirector" name="emailDirector" value="<?= $datos['emailDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="dniDirector">DNI</label>
                <input type="text" id="dniDirector" name="dniDirector" value="<?= $datos['dniDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="telefonoDirector">Teléfono</label>
                <input type="text" id="telefonoDirector" name="telefonoDirector" value="<?= $datos['telefonoDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="fechaNacimientoDirector">Fecha de Nacimiento</label>
                <input type="date" id="fechaNacimientoDirector" name="fechaNacimientoDirector" value="<?= $datos['fechaNacimientoDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="ciudadDirector">Ciudad</label>
                <input type="text" id="ciudadDirector" name="ciudadDirector" value="<?= $datos['ciudadDirector'] ?? '' ?>">
                
            </div>

            <div class="campo">
                <label for="codigoPostalDirector">Código Postal</label>
                <input type="text" id="codigoPostalDirector" name="codigoPostalDirector" value="<?= $datos['codigoPostalDirector'] ?? '' ?>">
                
            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionDirector">Dirección Completa</label>
                <input type="text" id="direccionDirector" name="direccionDirector" value="<?= $datos['direccionDirector'] ?? '' ?>">
                
            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesDirector">Observaciones / Notas Internas</label>
                <textarea id="observacionesDirector" name="observacionesDirector" rows="3"><?= $datos['observacionesDirector'] ?? '' ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarDirector" class="boton-primario" value="REGISTRAR DIRECTOR">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
