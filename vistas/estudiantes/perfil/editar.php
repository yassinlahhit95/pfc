<?php
require_once __DIR__ . "/../../../include/EstudianteGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);

$datos = $_SESSION['datos_perfil'] ?? [];
unset($_SESSION['datos_perfil']);

require_once __DIR__ . "/../../../modelos/estudiantes.php";

$idEstudiante = $_SESSION['idEstudiante'];
$estudianteActual = obtenerEstudiantePorId($idEstudiante);

$tituloDelPagina = "AULAPRO | EDITAR PERFIL";
$seccionActual = 'perfil';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <h1>EDITAR MI PERFIL</h1>
    <a href="ver.php" class="boton-secundario"><i class="fas fa-arrow-left"></i> VOLVER</a>
</div>

<div class="panel">
    <form action="../../../controladores/estudiantes/perfil/actualizar.php" method="POST" class="formulario">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idEstudiante" value="<?= Security::escapeHtml($idEstudiante) ?>">

        <!-- ── Datos personales ── -->
        <div class="titulo-tarjeta"><h3><i class="fas fa-user"></i> DATOS PERSONALES Y CONTACTO</h3></div>

        <div class="campo<?= fieldClass($errores, 'nombreEstudiante') ?>">
            <label for="nombreEstudiante">Nombre Completo</label>
            <input type="text" name="nombreEstudiante" id="nombreEstudiante"
                   value="<?= Security::escapeHtml($datos['nombreEstudiante'] ?? $estudianteActual['nombreEstudiante'] ?? '') ?>">
            <?= fieldError($errores, 'nombreEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'emailEstudiante') ?>">
            <label for="emailEstudiante">Correo Electrónico</label>
            <input type="email" name="emailEstudiante" id="emailEstudiante"
                   value="<?= Security::escapeHtml($datos['emailEstudiante'] ?? $estudianteActual['emailEstudiante'] ?? '') ?>">
            <?= fieldError($errores, 'emailEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'telefonoEstudiante') ?>">
            <label for="telefonoEstudiante">Número de Teléfono</label>
            <input type="text" name="telefonoEstudiante" id="telefonoEstudiante"
                   value="<?= Security::escapeHtml($datos['telefonoEstudiante'] ?? $estudianteActual['telefonoEstudiante'] ?? '') ?>">
            <?= fieldError($errores, 'telefonoEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'dniEstudiante') ?>">
            <label for="dniEstudiante">DNI / Identificación</label>
            <input type="text" name="dniEstudiante" id="dniEstudiante"
                   value="<?= Security::escapeHtml($datos['dniEstudiante'] ?? $estudianteActual['dniEstudiante'] ?? '') ?>"
                   placeholder="12345678A">
            <?= fieldError($errores, 'dniEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'fechaNacimientoEstudiante') ?>">
            <label for="fechaNacimientoEstudiante">Fecha de Nacimiento</label>
            <input type="date" name="fechaNacimientoEstudiante" id="fechaNacimientoEstudiante"
                   value="<?= Security::escapeHtml($datos['fechaNacimientoEstudiante'] ?? $estudianteActual['fechaNacimientoEstudiante'] ?? '') ?>">
            <?= fieldError($errores, 'fechaNacimientoEstudiante') ?>
        </div>

        <!-- ── Dirección ── -->
        <div class="titulo-tarjeta" style="margin-top:24px;"><h3><i class="fas fa-map-marker-alt"></i> DIRECCIÓN</h3></div>

        <div class="campo ancho-total<?= fieldClass($errores, 'direccionEstudiante') ?>">
            <label for="direccionEstudiante">Dirección</label>
            <input type="text" name="direccionEstudiante" id="direccionEstudiante"
                   value="<?= Security::escapeHtml($datos['direccionEstudiante'] ?? $estudianteActual['direccionEstudiante'] ?? '') ?>"
                   placeholder="Calle, número, piso...">
            <?= fieldError($errores, 'direccionEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'ciudadEstudiante') ?>">
            <label for="ciudadEstudiante">Ciudad</label>
            <input type="text" name="ciudadEstudiante" id="ciudadEstudiante"
                   value="<?= Security::escapeHtml($datos['ciudadEstudiante'] ?? $estudianteActual['ciudadEstudiante'] ?? '') ?>">
            <?= fieldError($errores, 'ciudadEstudiante') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'codigoPostalEstudiante') ?>">
            <label for="codigoPostalEstudiante">Código Postal</label>
            <input type="text" name="codigoPostalEstudiante" id="codigoPostalEstudiante"
                   value="<?= Security::escapeHtml($datos['codigoPostalEstudiante'] ?? $estudianteActual['codigoPostalEstudiante'] ?? '') ?>"
                   maxlength="10" placeholder="28001">
            <?= fieldError($errores, 'codigoPostalEstudiante') ?>
        </div>

        <!-- ── Seguridad ── -->
        <div class="titulo-tarjeta" style="margin-top:24px;"><h3><i class="fas fa-lock"></i> SEGURIDAD Y CONTRASEÑA</h3></div>
        <p class="texto-suave" style="margin-bottom:15px;grid-column:1/-1;">Rellene estos campos solo si desea cambiar su contraseña de acceso.</p>

        <div class="campo<?= fieldClass($errores, 'current_password') ?>">
            <label for="current_password">Contraseña Actual</label>
            <input type="password" name="current_password" id="current_password" autocomplete="new-password">
            <?= fieldError($errores, 'current_password') ?>
        </div>

        <div class="campo<?= fieldClass($errores, 'new_password') ?>">
            <label for="new_password">Nueva Contraseña</label>
            <input type="password" name="new_password" id="new_password" autocomplete="new-password">
            <?= fieldError($errores, 'new_password') ?>
        </div>

        <div class="acciones" style="grid-column:1/-1;">
            <input type="submit" name="actualizarPerfil" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<?php include '../comunes/footer.php'; ?>
