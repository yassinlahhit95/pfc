<?php
require_once __DIR__ . "/../../../include/AdminGuard.php";
require_once __DIR__ . "/../../../include/form_helpers.php";

$exito = $_SESSION['exito'] ?? '';
$errores = $_SESSION['errores'] ?? null;
unset($_SESSION['exito'], $_SESSION['errores']);
require_once __DIR__ . "/../../../modelos/profesores.php";
require_once __DIR__ . "/../../../modelos/ciclos.php";
require_once __DIR__ . "/../../../modelos/modulos.php";

$id_profesor = (int)($_GET['idProfesor'] ?? 0);
$profesor = obtenerProfesorPorId($id_profesor);

if (!$profesor) {
    header("Location: verProfesores.php");
    exit;
}

$listaCiclos = listarTodosLosCiclos();
$todosLosModulos = listarModulos();

$datos_sesion = $_SESSION['datos_profesor'] ?? null;
if ($datos_sesion) {
    $profesor = $datos_sesion + $profesor;
    $ciclos_marcados = $datos_sesion['ciclos'] ?? [];
    $modulos_marcados = $datos_sesion['modulos'] ?? [];
} else {
    $ciclos_marcados = [];
    $ciclosBD = listarCiclosTutorizadosProfesor($id_profesor);
    foreach ($ciclosBD as $cicloItem) {
        $ciclos_marcados[] = $cicloItem['idCiclo'];
    }
    $modulos_marcados = listarIdsModulosDeProfesor($id_profesor);
}

$titulo_pagina = "AULAPRO | MODIFICAR PROFESOR";
$seccion = 'profesores';
include_once __DIR__ . "/../comunes/nav.php";
?>

<div class="cabecera">
    <div>
        <h1>MODIFICAR PROFESOR: <?= Security::escapeHtml($profesor['nombreProfesor']) ?></h1>
    </div>
    <a href="verProfesores.php" class="boton-secundario">
        <i class="fas fa-arrow-left"></i> VOLVER
    </a>
</div>


<div class="panel">
    <form action="../../../controladores/admin/profesores/actualizar.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
        <input type="hidden" name="idProfesor" value="<?= Security::escapeHtml($id_profesor) ?>">
        
        <div class="formulario">
            <div class="campo<?= fieldClass($errores, 'nombreProfesor') ?>">
                <label for="nombreProfesor">Nombre Completo</label>
                <input type="text" id="nombreProfesor" name="nombreProfesor" value="<?= Security::escapeHtml($profesor['nombreProfesor']) ?>">
                <?= fieldError($errores, 'nombreProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'emailProfesor') ?>">
                <label for="emailProfesor">Email</label>
                <input type="email" id="emailProfesor" name="emailProfesor" value="<?= Security::escapeHtml($profesor['emailProfesor']) ?>">
                <?= fieldError($errores, 'emailProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'dniProfesor') ?>">
                <label for="dniProfesor">DNI</label>
                <input type="text" id="dniProfesor" name="dniProfesor" value="<?= Security::escapeHtml($profesor['dniProfesor']) ?>">
                <?= fieldError($errores, 'dniProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'telefonoProfesor') ?>">
                <label for="telefonoProfesor">Teléfono</label>
                <input type="text" id="telefonoProfesor" name="telefonoProfesor" value="<?= Security::escapeHtml($profesor['telefonoProfesor']) ?>">
                <?= fieldError($errores, 'telefonoProfesor') ?>
            </div>

            <div class="campo ancho-total<?= fieldClass($errores, 'direccionProfesor') ?>">
                <label for="direccionProfesor">Dirección</label>
                <input type="text" id="direccionProfesor" name="direccionProfesor" value="<?= Security::escapeHtml($profesor['direccionProfesor']) ?>">
                <?= fieldError($errores, 'direccionProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'ciudadProfesor') ?>">
                <label for="ciudadProfesor">Ciudad</label>
                <input type="text" id="ciudadProfesor" name="ciudadProfesor" value="<?= Security::escapeHtml($profesor['ciudadProfesor']) ?>">
                <?= fieldError($errores, 'ciudadProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'codigoPostalProfesor') ?>">
                <label for="codigoPostalProfesor">Código Postal</label>
                <input type="text" id="codigoPostalProfesor" name="codigoPostalProfesor" value="<?= Security::escapeHtml($profesor['codigoPostalProfesor']) ?>">
                <?= fieldError($errores, 'codigoPostalProfesor') ?>
            </div>

            <div class="campo<?= fieldClass($errores, 'fechaNacimientoProfesor') ?>">
                <label for="fechaNacimientoProfesor">Fecha de Nacimiento</label>
                <input type="date" id="fechaNacimientoProfesor" name="fechaNacimientoProfesor" value="<?= Security::escapeHtml($profesor['fechaNacimientoProfesor']) ?>">
                <?= fieldError($errores, 'fechaNacimientoProfesor') ?>
            </div>

            <div class="campo">
                <label for="fechaAltaProfesor">Fecha de Alta (en centro)</label>
                <input type="date" id="fechaAltaProfesor" name="fechaAltaProfesor" value="<?= Security::escapeHtml($profesor['fechaAltaProfesor']) ?>">
            </div>

            <div class="campo ancho-total">
                <label for="observacionesProfesor">Observaciones / Curriculum Vitae (Resumen)</label>
                <textarea id="observacionesProfesor" name="observacionesProfesor" rows="3"><?= Security::escapeHtml($profesor['observacionesProfesor']) ?></textarea>
            </div>
        </div>

        <!-- Tutor de Ciclo (profesor con rol especial, NO familia/padres) -->
        <div class="panel" style="margin-top:25px;padding:20px;border:1px solid var(--border);background:var(--surface-2);">
            <h4 style="margin:0 0 6px;"><i class="fas fa-user-shield" style="color:var(--naranja);"></i> Tutor de Ciclo</h4>
            <p style="font-size:.82rem;color:var(--dim);margin-bottom:16px;">
                El <strong>Tutor de Ciclo</strong> es un profesor con acceso especial sobre un ciclo: puede gestionar notas, horarios y cambiar contraseñas de sus estudiantes.
                <em>Este rol no tiene relación con los tutores familiares (padres/madres).</em>
            </p>
            <div class="formulario" style="grid-template-columns:1fr 1fr;gap:15px;align-items:start;">
                <div class="campo">
                    <label style="font-size:.82rem;color:var(--dim);margin-bottom:6px;display:block;">Activar rol de Tutor de Ciclo</label>
                    <div class="toggle-wrap">
                        <label class="toggle-switch">
                            <input type="checkbox" id="esTutorCheck" name="esTutor" value="1"
                                   <?= !empty($profesor['esTutor']) ? 'checked' : '' ?>
                                   onchange="toggleCicloTutor(this)">
                            <span class="toggle-track"></span>
                        </label>
                        <span class="toggle-label">
                            Este profesor es Tutor de Ciclo
                            <small>Puede cambiar contraseñas de sus alumnos</small>
                        </span>
                    </div>
                </div>
                <div class="campo" id="campo-ciclo-tutor" style="<?= empty($profesor['esTutor']) ? 'opacity:.4;pointer-events:none;' : '' ?>">
                    <label for="idCicloTutor">Ciclo asignado</label>
                    <select id="idCicloTutor" name="idCicloTutor">
                        <option value="">-- Seleccionar ciclo --</option>
                        <?php foreach ($listaCiclos as $ciclo): ?>
                            <option value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" <?= ($profesor['idCicloTutor'] == $ciclo['idCiclo']) ? 'selected' : '' ?>>
                                <?= Security::escapeHtml($ciclo['nombreCiclo']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="cuadricula-secundaria" style="margin-top: 25px;">
            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-layer-group"></i> 1. Seleccionar Ciclos</h4>
                <div class="checks scroll-v200">
                    <?php foreach ($listaCiclos as $ciclo) { ?>
                        <label class="check-item">
                            <input type="checkbox" name="ciclos[]" value="<?= Security::escapeHtml($ciclo['idCiclo']) ?>" class="check-ciclo"
                                <?php if (in_array($ciclo['idCiclo'], $ciclos_marcados)) { echo 'checked'; } ?>>
                            <span><?= Security::escapeHtml($ciclo['nombreCiclo']) ?></span>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div>
                <h4 style="margin-bottom: 15px;"><i class="fas fa-book"></i> 2. Seleccionar Módulos</h4>
                <div id="contenedor-modulos-dinamico" class="checks scroll-v400 bg-gris-suave">
                    <p id="msg-seleccionar-ciclo" class="texto-suave" style="text-align: center; padding: 20px;">
                        Seleccione primero uno o varios ciclos para ver sus módulos disponibles.
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
                                            <?php if (in_array($modulo['idModulo'], $modulos_marcados)) { echo 'checked'; } ?>>
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
            <input type="submit" name="actualizarProfesor" class="boton-primario" value="GUARDAR CAMBIOS">
            <input type="reset" class="boton-secundario" value="LIMPIAR">
        </div>
    </form>
</div>

<div class="panel" style="margin-top:20px;">
    <h3 style="margin:0 0 16px;"><i class="fas fa-key" style="color:var(--accent);"></i> Cambiar contraseña</h3>
    <div class="formulario" style="grid-template-columns:1fr 1fr;gap:15px;align-items:end;">
        <div class="campo">
            <label for="nueva-pass-prof">Nueva contraseña <small style="color:var(--dim);">(mín. 8 caracteres)</small></label>
            <input type="password" id="nueva-pass-prof" minlength="8" placeholder="Nueva contraseña" autocomplete="new-password">
        </div>
        <div class="campo">
            <label for="nueva-pass-prof-confirm">Confirmar contraseña</label>
            <input type="password" id="nueva-pass-prof-confirm" minlength="8" placeholder="Repetir contraseña" autocomplete="new-password">
        </div>
    </div>
    <div class="acciones" style="margin-top:16px;">
        <button type="button" class="boton-primario" onclick="cambiarPassProf()">
            <i class="fas fa-lock"></i> Guardar contraseña
        </button>
    </div>
</div>

<script src="../../../public/js/features/profesores-form.js"></script>
<script>
function cambiarPassProf() {
    var pass    = document.getElementById('nueva-pass-prof').value.trim();
    var confirm = document.getElementById('nueva-pass-prof-confirm').value.trim();
    if (pass.length < 8) {
        if (window.Toast) Toast.show('La contraseña debe tener al menos 8 caracteres.', 'error');
        return;
    }
    if (pass !== confirm) {
        if (window.Toast) Toast.show('Las contraseñas no coinciden.', 'error');
        return;
    }
    var token = document.querySelector('[name=csrf_token]').value;
    fetch('../../../controladores/admin/usuarios/cambiarPassword.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: 'tipo=profesor&id=<?= $id_profesor ?>&nuevaPassword=' + encodeURIComponent(pass) + '&csrf_token=' + encodeURIComponent(token)
    })
    .then(function(r) { return r.json(); })
    .then(function(data) {
        if (window.Toast) Toast.show(data.msg, data.ok ? 'success' : 'error');
        if (data.ok) {
            document.getElementById('nueva-pass-prof').value = '';
            document.getElementById('nueva-pass-prof-confirm').value = '';
            setTimeout(function() { location.reload(); }, 1800);
        }
    })
    .catch(function() { if (window.Toast) Toast.show('Error de conexión.', 'error'); });
}

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
