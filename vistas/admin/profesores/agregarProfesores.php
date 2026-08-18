<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$datos = $_SESSION['datos_profesor'] ?? [];
unset($_SESSION['datos_profesor']);

$ciclosMarcados = $datos['ciclos'] ?? [];
$modulosMarcados = $datos['modulos'] ?? [];

$titulo_pagina = "Agregar Profesor";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>Nuevo Profesor</h1>
    </div>
    <a href="../../../vistas/admin/profesores/verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/profesores/insertar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <div class="formulario">
            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'nombreProfesor') ?>">
                    <label for="nombreProfesor">Nombre Completo</label>
                    <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= Security::escapeHtml($datos['nombreProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'nombreProfesor') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'emailProfesor') ?>">
                    <label for="emailProfesor">Email</label>
                    <input type="text" id="emailProfesor" name="emailProfesor" value="<?= Security::escapeHtml($datos['emailProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'emailProfesor') ?>
                </div>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'dniProfesor') ?>">
                    <label for="dniProfesor">DNI</label>
                    <input type="text" id="dniProfesor" name="dniProfesor" value="<?= Security::escapeHtml($datos['dniProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'dniProfesor') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'telefonoProfesor') ?>">
                    <label for="telefonoProfesor">Teléfono</label>
                    <input type="text" id="telefonoProfesor" name="telefonoProfesor" value="<?= Security::escapeHtml($datos['telefonoProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'telefonoProfesor') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'fechaNacimientoProfesor') ?>">
                    <label for="fechaNacimientoProfesor">Fecha de Nacimiento</label>
                    <input type="date" id="fechaNacimientoProfesor" name="fechaNacimientoProfesor" value="<?= Security::escapeHtml($datos['fechaNacimientoProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'fechaNacimientoProfesor') ?>
                </div>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'direccionProfesor') ?>">
                <label for="direccionProfesor">Dirección</label>
                <input type="text" id="direccionProfesor" name="direccionProfesor" value="<?= Security::escapeHtml($datos['direccionProfesor'] ?? '') ?>">
                <?= fieldError($errores, 'direccionProfesor') ?>
            </div>

            <div class="form-fila">
                <div class="campo<?= fieldClass($errores, 'ciudadProfesor') ?>">
                    <label for="ciudadProfesor">Ciudad</label>
                    <input type="text" id="ciudadProfesor" name="ciudadProfesor" value="<?= Security::escapeHtml($datos['ciudadProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'ciudadProfesor') ?>
                </div>

                <div class="campo<?= fieldClass($errores, 'codigoPostalProfesor') ?>">
                    <label for="codigoPostalProfesor">Código Postal</label>
                    <input type="text" id="codigoPostalProfesor" name="codigoPostalProfesor" value="<?= Security::escapeHtml($datos['codigoPostalProfesor'] ?? '') ?>">
                    <?= fieldError($errores, 'codigoPostalProfesor') ?>
                </div>
            </div>

            <div class="campo ancho-total">
                <label for="observacionesProfesor">Observaciones</label>
                <textarea id="observacionesProfesor" name="observacionesProfesor" rows="3"><?= Security::escapeHtml($datos['observacionesProfesor'] ?? '') ?></textarea>
            </div>
        </div>

        <!-- Tutor Status -->
        <div class="panel" style="margin-top:25px;padding:20px;border:1px solid var(--border);background:var(--bg-2);">
            <h4 style="margin:0 0 15px;"><i class="fas fa-star" style="color:var(--naranja);"></i> Permiso de Tutor de Ciclo</h4>
            <p style="font-size:.85rem;color:var(--text-2);margin-bottom:15px;">
                Si activas esta opción, el profesor actuará como administrador del ciclo asignado con acceso completo a sus estudiantes, notas, módulos y horario.
            </p>
            <div class="formulario" style="grid-template-columns:1fr 1fr;gap:15px;">
                <div class="campo">
                    <label class="check-item" style="cursor:pointer;">
                        <input type="checkbox" id="esTutorCheck" name="esTutor" value="1" <?= !empty($datos['esTutor']) ? 'checked' : '' ?> onchange="toggleCicloTutor(this)">
                        <span><b>Este profesor es Tutor de Ciclo</b></span>
                    </label>
                </div>
                <div class="campo" id="campo-ciclo-tutor" style="<?= empty($datos['esTutor']) ? 'opacity:.4;pointer-events:none;' : '' ?>">
                    <label for="idCicloTutor">Ciclo asignado como Tutor</label>
                    <select id="idCicloTutor" name="idCicloTutor">
                        <option value="">-- Seleccionar ciclo --</option>
                        <?php foreach ($listaCiclos as $ciclo): ?>
                            <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= (($datos['idCicloTutor'] ?? '') == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                                <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;">Seleccionar Ciclos</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="ciclos[]" value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="check-ciclo"
                                <?php if (in_array($ciclo['idCiclo'], $ciclosMarcados)) { echo 'checked'; } ?>>
                            <span><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 style="margin-bottom: 15px;">Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-suave" style="text-align: center; padding: 20px;">
                        Seleccione primero un ciclo para ver sus módulos.
                    </p>

                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <div class="grupo-modulos oculto" style="margin-bottom: 15px;" id="grupo-ciclo-<?= Security::escapeHtml($ciclo['idCiclo']) ?>">
                            <p class="texto-negrita color-primario" style="margin-bottom: 5px;">
                                <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                            </p>
                            
                            <?php foreach ($todosLosModulos as $modulo) { ?>
                                <?php if ($modulo['idCiclo'] == $ciclo['idCiclo']) { ?>
                                    <label class="check-item" style="padding-left: 10px;">
                                        <input type="checkbox" name="modulos[]" value="<?= Security::escapeHtml($modulo['idModulo']) ?>"
                                            <?php if (in_array($modulo['idModulo'], $modulosMarcados)) { echo 'checked'; } ?>>
                                        <span><?= Security::escapeHtml($modulo['nombreModulo']) ?></span>
                                    </label>
                                <?php } ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="acciones" style="margin-top: 25px;">
            <input type="submit" name="guardarProfesor" class="boton-primario" value="REGISTRAR PROFESOR">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<script src="../../../public/js/features/profesores-form.js"></script>
<script>
function toggleCicloTutor(cb) {
    var campo = document.getElementById('campo-ciclo-tutor');
    if (cb.checked) {
        campo.style.opacity = '1';
        campo.style.pointerEvents = 'auto';
    } else {
        campo.style.opacity = '.4';
        campo.style.pointerEvents = 'none';
        document.getElementById('idCicloTutor').value = '';
    }
}
</script>

<?php include '../comunes/footer.php'; ?>
