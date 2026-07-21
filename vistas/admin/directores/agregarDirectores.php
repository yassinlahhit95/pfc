<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_director'] ?? [];
unset($_SESSION['datos_director']);

$titulo_pagina = "AULAPRO | REGISTRAR DIRECTOR";
$seccion = 'directores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>NUEVO DIRECTOR DE CICLO</h1>
    <a href="verDirectores.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/directores/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreDirector') ?>">
                    <label for="nombreDirector">Nombre Completo</label>
                    <input type="text" id="nombreDirector" name="nombreDirector" value="<?= Security::escapeHtml($datos['nombreDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'nombreDirector') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'emailDirector') ?>">
                    <label for="emailDirector">Email</label>
                    <input type="text" id="emailDirector" name="emailDirector" value="<?= Security::escapeHtml($datos['emailDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'emailDirector') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'dniDirector') ?>">
                    <label for="dniDirector">DNI</label>
                    <input type="text" id="dniDirector" name="dniDirector" value="<?= Security::escapeHtml($datos['dniDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'dniDirector') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'telefonoDirector') ?>">
                    <label for="telefonoDirector">Teléfono</label>
                    <input type="text" id="telefonoDirector" name="telefonoDirector" value="<?= Security::escapeHtml($datos['telefonoDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'telefonoDirector') ?>
                </div>

                <div class="campo">
                    <label for="fechaNacimientoDirector">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimientoDirector" name="fechaNacimientoDirector" value="<?= Security::escapeHtml($datos['fechaNacimientoDirector'] ?? '') ?>">
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'ciudadDirector') ?>">
                    <label for="ciudadDirector">Ciudad</label>
                    <input type="text" id="ciudadDirector" name="ciudadDirector" value="<?= Security::escapeHtml($datos['ciudadDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'ciudadDirector') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'codigoPostalDirector') ?>">
                    <label for="codigoPostalDirector">Código Postal</label>
                    <input type="text" id="codigoPostalDirector" name="codigoPostalDirector" value="<?= Security::escapeHtml($datos['codigoPostalDirector'] ?? '') ?>">
                    <?= fieldError($errores, 'codigoPostalDirector') ?>
                </div>
            </div>

            <div class="campo campo-ancho-total">
                <label for="direccionDirector">Dirección Completa</label>
                <input type="text" id="direccionDirector" name="direccionDirector" value="<?= Security::escapeHtml($datos['direccionDirector'] ?? '') ?>">
            </div>

            <div class="campo campo-ancho-total">
                <label for="observacionesDirector">Observaciones / Notas Internas</label>
                <textarea id="observacionesDirector" name="observacionesDirector" rows="3"><?= Security::escapeHtml($datos['observacionesDirector'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="acciones">
            <input type="submit" name="guardarDirector" class="boton-primario" value="REGISTRAR DIRECTOR">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
