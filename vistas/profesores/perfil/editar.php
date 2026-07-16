<?php
require_once __DIR__ . "/../../../include/ProfesorGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_perfil'] ?? [];
unset($_SESSION['datos_perfil']);

require_once __DIR__ . "/../../../modelos/profesores.php";

$idProfesor = $_SESSION['idProfesor'];
$profesorActual = obtenerProfesorPorId($idProfesor);

$tituloDelPagina = "AULAPRO | EDITAR PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR MI PERFIL</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>


<div class="panel">
    <form action="../../../controladores/profesores/perfil/actualizar.php" method="POST" class="formulario">
    <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idProfesor" value="<?= Security::escapeHtml($idProfesor ) ?>">

        <div class="titulo-tarjeta"><h3><i class="fas fa-user-circle"></i> DATOS DE CONTACTO</h3></div>

        <div class="campo<?= fieldClass($errores, 'nombreProfesor') ?>">
            <label for="nombreProfesor">Nombre Completo</label>
            <input type="text" name="nombreProfesor" id="nombreProfesor" value="<?= Security::escapeHtml($datos['nombreProfesor'] ?? $profesorActual['nombreProfesor']) ?>">
            <?= fieldError($errores, 'nombreProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'emailProfesor') ?>">
            <label for="emailProfesor">Correo Corporativo</label>
            <input type="email" name="emailProfesor" id="emailProfesor" value="<?= Security::escapeHtml($datos['emailProfesor'] ?? $profesorActual['emailProfesor']) ?>">
            <?= fieldError($errores, 'emailProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'telefonoProfesor') ?>">
            <label for="telefonoProfesor">Número de Teléfono</label>
            <input type="text" name="telefonoProfesor" id="telefonoProfesor" value="<?= Security::escapeHtml($datos['telefonoProfesor'] ?? $profesorActual['telefonoProfesor']) ?>">
            <?= fieldError($errores, 'telefonoProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'dniProfesor') ?>">
            <label for="dniProfesor">DNI / Identificación</label>
            <input type="text" name="dniProfesor" id="dniProfesor" value="<?= Security::escapeHtml($datos['dniProfesor'] ?? $profesorActual['dniProfesor'] ?? '') ?>">
            <?= fieldError($errores, 'dniProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaNacimientoProfesor') ?>">
            <label for="fechaNacimientoProfesor">Fecha de Nacimiento</label>
            <input type="date" name="fechaNacimientoProfesor" id="fechaNacimientoProfesor" value="<?= Security::escapeHtml($datos['fechaNacimientoProfesor'] ?? $profesorActual['fechaNacimientoProfesor'] ?? '') ?>">
            <?= fieldError($errores, 'fechaNacimientoProfesor') ?>
        </div>

        <div class="campo ancho-total<?= fieldClass($errores, 'direccionProfesor') ?>">
            <label for="direccionProfesor">Dirección</label>
            <input type="text" name="direccionProfesor" id="direccionProfesor" value="<?= Security::escapeHtml($datos['direccionProfesor'] ?? $profesorActual['direccionProfesor'] ?? '') ?>">
            <?= fieldError($errores, 'direccionProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'ciudadProfesor') ?>">
            <label for="ciudadProfesor">Ciudad</label>
            <input type="text" name="ciudadProfesor" id="ciudadProfesor" value="<?= Security::escapeHtml($datos['ciudadProfesor'] ?? $profesorActual['ciudadProfesor'] ?? '') ?>">
            <?= fieldError($errores, 'ciudadProfesor') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'codigoPostalProfesor') ?>">
            <label for="codigoPostalProfesor">Código Postal</label>
            <input type="text" name="codigoPostalProfesor" id="codigoPostalProfesor" value="<?= Security::escapeHtml($datos['codigoPostalProfesor'] ?? $profesorActual['codigoPostalProfesor'] ?? '') ?>">
            <?= fieldError($errores, 'codigoPostalProfesor') ?>
        </div>

        <div class="campo ancho-total">
            <label for="observacionesProfesor">Observaciones</label>
            <textarea name="observacionesProfesor" id="observacionesProfesor" rows="3"><?= Security::escapeHtml($datos['observacionesProfesor'] ?? $profesorActual['observacionesProfesor'] ?? '') ?></textarea>
        </div>

        <div class="titulo-tarjeta" style="margin-top:10px;"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-suave">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo<?= fieldClass($errores, 'current_password') ?>">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" name="current_password" id="current_password" placeholder="••••••••" autocomplete="new-password">
            <?= fieldError($errores, 'current_password') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'new_password') ?>">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" name="new_password" id="new_password" placeholder="••••••••" autocomplete="new-password">
            <?= fieldError($errores, 'new_password') ?>
        </div>

        <div class="acciones">
            <input type="submit" name="actualizarPerfil" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
